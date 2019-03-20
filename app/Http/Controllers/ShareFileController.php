<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

use App\Http\Controllers\Controller;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Auth;
use DB;
use App\Models\User;
use App\Models\UserShares;
use App\Models\UserPreferences;
use App\Models\UserStorageAuth;
use Exception;
use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxFile;
use Krizalys\Onedrive\Client as OneDriveClient;
use App\Providers\BoxAPI as BoxAPI;
use App\Providers\PRE as PRE;
use App\Providers\rekey_Struct as rekey_Struct;
use App\Providers\publickey_Struct as publickey_Struct;
use App\Providers\ct1_Struct as ct1_Struct;
use App\Providers\ct2_Struct as ct2_Struct;
use League\OAuth2\Client\Token\AccessToken;
use Stevenmaguire\OAuth2\Client\Provider\Box;
use SplFileInfo;


class ShareFileController extends Controller
{
    //
   public function shareFile(Request $request){
      
	$fileId = $request->input('fileId');
	$fileName = $request->input('fileName');
	$email = $request->input('email');
	$reKey = $request->input('reKey');
	$recieveruserid = $request->input('userId');
        $storageId = $request->input('storage');
		error_log("Going to share for ".$storageId." mail ".$email);
      		//$recieveruserid = User::where('email','=',$email)->value('id');
		if($recieveruserid == ''){
			throw Exception("Shared User not found");
		}
   
     		$currentuserid = Auth::user()->id;

		$fileData= new \stdClass();
		$fileData->fileId = $fileId;
		$fileData->fileName = $fileName;
		$fileData->storageId = $storageId;


		
        	$usershare = new UserShares;
		$usershare->sharedBy=$currentuserid;
		$usershare->sharedTo=$recieveruserid;
		$usershare->reKey=$reKey;
		$usershare->fileData=json_encode($fileData);
		$usershare->save();
		
		//error_log(print_r($usershare,true));
		return Response::make('success');

	}

	public function updateShares(Request $request,$id,$status){

		$user_id = Auth::user()->id;
		$share = UserShares::find($id);
	
		if($share->status!='waiting'){
			return Response::make('failed');
		}
	
		if($status=='accept'){

		//copy the file from one user to other
			$shareData= new \stdClass();
			$shareData->sharedBy=$share->sharedBy;	
			$shareData->sharedTo=$share->sharedTo;	
			$shareData->fileData=json_decode($share->fileData);	
			$shareData->reKey=$share->reKey;	
			$this->copySharedFile($shareData);	
	
		$share->status='accepted';
		}
		if($status=='ignore'){
		$share->status='rejected';
		}

		$share->save();
		//return Response::make($share);
		return Response::make('success');

	}

	public function getShares(Request $request){

		$user_id = Auth::user()->id;
		$shares = UserShares::select('user_shares.id','fileData','first_name','last_name')
			  ->join('users', 'users.id', '=', 'user_shares.sharedBy')
			  ->where([['sharedTo', '=', $user_id],['status','=','waiting']])
			  ->limit(10)
			  ->get();
		return Response::make($shares);

	}

	public function copySharedFile($shareData){
	
		error_log(print_r($shareData,true));

        


	
		$content="";
        	$storageId = $shareData->fileData->storageId;
		$userStorageAuth = UserStorageAuth::find($storageId);
		$downloadStorage=$userStorageAuth->type;
		$user_credentials=$userStorageAuth->auth_token;

		$fileId = $shareData->fileData->fileId;
		$fileName = $shareData->fileData->fileName;

                $handle = fopen("/tmp/".$fileName, 'w') or die('Cannot create temp file:  '); //implicitly creates file
                $mimeType=mime_content_type("/tmp/".$fileName);
		
		$senderuserid = $shareData->sharedBy;
		$recieveruserid = $shareData->sharedTo;
		$reKey = ShareFileController::wrapRKwithGMP(json_decode($shareData->reKey));

		$senderUserPK  = UserPreferences::where('user_id', '=', $senderuserid)->value('publicKey');
		$recieverUserPK  = UserPreferences::where('user_id', '=', $recieveruserid)->value('publicKey');
		$recieverPrefStorage  = UserPreferences::where('user_id', '=', $recieveruserid)->value('preferred_storage');
		
		$senderUserPKObj  = ShareFileController::wrapPKwithGMP(json_decode($senderUserPK)); 
		$recieverUserPKObj  = ShareFileController::wrapPKwithGMP(json_decode($recieverUserPK));

		$controller = StorageFactory::getStorageHandler($downloadStorage);
		$content=$controller->downloadFile($userStorageAuth,$fileId,$fileName);


			//re-encrypt the content here
			$content_obj = json_decode($content);
			$cipherObj= ShareFileController::wrapCipherwithGMP($content_obj->cipher);
			error_log("cipher obj is ".print_r($cipherObj,true));		
			$payload= $content_obj->content;
			$PRE_obj = new PRE();
			$PRE_obj->Setup(1024);
			$PRE_obj->printCipher($cipherObj);
			$newCipher = $PRE_obj->ReEncrypt($reKey, $cipherObj,$senderUserPKObj, $recieverUserPKObj);
			error_log("New cipher is ".print_r($newCipher,true));		
	
			$tmp_content_obj = new \stdClass();
			$tmp_content_obj->cipher=ShareFileController::convertGMPToString($newCipher);
			$tmp_content_obj->content=$payload;
		       error_log("tmp content obj is".print_r($tmp_content_obj,true));	
			$newContent = json_encode($tmp_content_obj);		
		       error_log("new content obj is".print_r($newContent,true));	
	
			$filePath=base_path().'/uploads/'.$recieveruserid.'_'.$fileName;
			file_put_contents($filePath, $newContent);//write the plain file in local disk
			//now copy the file to the shared user space 

			$this->copyFileToUserDrive($recieverPrefStorage,$filePath,$fileName,$mimeType);

			return Response::make('success');
	
	}

	private static function wrapRKwithGMP($reKey){

		error_log(print_r($reKey,true));
		$rekeyobj = new rekey_Struct();

		$rekeyobj->RKi_j = gmp_init(str_replace('0x','',$reKey->RKi_j),16);
		$rekeyobj->V = gmp_init(str_replace('0x','',$reKey->V),16);
		$rekeyobj->W = gmp_init(str_replace('0x','',$reKey->W),16);
		error_log(print_r($rekeyobj,true));
		return $rekeyobj;
	}

	private static function convertGMPToString($cipher){

		error_log(print_r($cipher,true));
		$cipherObj = new ct2_Struct();
		$cipherObj->E1 = gmp_strval($cipher->E1,16);
		$cipherObj->F = gmp_strval($cipher->F,16);
		$cipherObj->V = gmp_strval($cipher->V,16);
		$cipherObj->W = gmp_strval($cipher->W,16);
		error_log(print_r($cipherObj,true));
		return $cipherObj;
	}

	private static function wrapCipherwithGMP($cipher){

		$cipherObj = new ct1_Struct();
		$cipherObj->D = gmp_init(str_replace('0x','',$cipher->D),16);
		$cipherObj->E = gmp_init(str_replace('0x','',$cipher->E),16);
		$cipherObj->F = gmp_init(str_replace('0x','',$cipher->F),16);
		$cipherObj->s = gmp_init(str_replace('0x','',$cipher->s),16);
		return $cipherObj;
	}

	private static function wrapPKwithGMP($pki){
		
		$pkObj=new publickey_Struct();
		$pkObj->pk_1 = gmp_init(str_replace('0x','',$pki->pk_1),16);
		$pkObj->pk_2= gmp_init(str_replace('0x','',$pki->pk_2),16);
		return $pkObj;
	}




    	public function copyFileToUserDrive($storage_id,$filePath,$fileName,$mimeType){
     		
	    $userid = Auth::user()->id;
			// get the reciver preferred storage Id
            $storage = UserStorageAuth::select('auth_token','type','id')->where([['user_id','=',$userid],['id','=',$storage_id]])->get();
            if($storage==null){
                    return Response::make("ERROR : No permission");
            }
            $uploadStorage=$storage[0]->type;
            $file_info = new SplFileInfo($filePath);

            $content = file_get_contents($file_info->getRealPath());

            $user_credentials =$storage[0]->auth_token;


        $controller = StorageFactory::getStorageHandler($uploadStorage);
        $fileId=$controller->uploadFile($content,$mimeType,$file_info,$storage[0],$userid);

	return $fileId;
  	}


}
