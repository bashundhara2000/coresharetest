<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Auth;
use DB;
use App\Models\UserStorageAuth;
use Krizalys\Onedrive\Client;

class OneDriveStorageController extends AbstractStorageController{

	public static function getClient($state="",$authState=null){
		
		$app_url = getenv("APP_URL");
		$od_client_id=getenv("ONEDRIVE_CLIENT_ID");
		$od_client_secret=getenv("ONEDRIVE_CLIENT_SECRET");
		$callbackUrl = getenv("ONEDRIVE_CALLBACK_URL");
		if($authState==null){
		$authState = (object) [
                'redirect_uri' => $callbackUrl,
                'token'        => null, ];
		}
		// Instantiates a OneDrive client bound to your OneDrive application.
		$onedrive = new Client([
					'client_id' => $od_client_id,
					'state' => $state,
					'authstate' => $authState,
		]);
		
		return $onedrive;
	}
	public static function getDriveService($user_credentials,$id){

		$od_client_id=getenv("ONEDRIVE_CLIENT_ID");
		$od_client_secret=getenv("ONEDRIVE_CLIENT_SECRET");
		$od_auth_data=json_decode($user_credentials);
		$onedrive=OneDriveStorageController::getClient("",$od_auth_data);
		//move this check to user login action
		if($onedrive->getAccessTokenStatus()!=1){
			error_log("Acees token expired , renewing with refresh token");
			$onedrive->setClientId($od_client_id);
			$onedrive->renewAccessToken($od_client_secret);
			//update the new acess token in DB so that it can be re-used
			$userauth=UserStorageAuth::find($id);
			$userauth->auth_token=json_encode($onedrive->getState());
			$userauth->save();
		}
		return $onedrive;
	}
	function getAuthorizationURL($state){
		
		$callbackUrl = getenv("ONEDRIVE_CALLBACK_URL");
		$onedrive=OneDriveStorageController::getClient($state);
		//$request->session()->put('onedrive.client.state', $onedrive->getState());
			// Gets a log in URL with sufficient privileges from the OneDrive API.
			$authUrl = $onedrive->getLogInUrl([
					'wl.signin',
					'wl.basic',
					'wl.contacts_skydrive',
					'wl.skydrive_update',
					'wl.offline_access',
			], $callbackUrl);
		return $authUrl;
	}
	function storeAuthCredentials($userid,$token,$id){

		$od_client_secret=getenv("ONEDRIVE_CLIENT_SECRET");
		$od_client_id=getenv("ONEDRIVE_CLIENT_ID");
		$onedrive=OneDriveStorageController::getClient();
		
			// Obtain the token using the code received by the OneDrive API.
			$onedrive->obtainAccessToken($od_client_secret, $token);
			$access_token=$onedrive->getState();

		//store it in DB
		//$access_token = $client->getAccessToken();
		//$refresh_token = $client->getRefreshToken();
		//error_log("Refresh token ".$refresh_token);
		//$userauth = UserStorageAuth::firstOrNew(array('user_id' => $userid , 'type'=>'google'));
		$userauth=UserStorageAuth::find($id);
		$userauth->auth_token=json_encode($access_token);
		//$userauth->refesh_token=json_encode($refresh_token);
		$userauth->save();
		//$user_credentials = UserStorageAuth::where('user_id', '=', $currentuserid)->first();

		//file_put_contents(__DIR__."/credentials.json", json_encode($access_token));
		
		return Redirect::to('/user/home');

		//return "Success";

	}
	function uploadFile($content,$mimeType,$file_info,$storage,$userid){

		$fileName = $file_info->getFileName();
		$user_credentials = $storage->auth_token;

		 if($user_credentials){
				$onedrive=OneDriveStorageController::getDriveService($user_credentials,$storage->id);
                                /*$od_auth_data=json_decode($user_credentials);
				$onedrive=OneDriveStorageController::getClient("",$od_auth_data);
                                //move this check to user login action
                                if($onedrive->getAccessTokenStatus()!=1){
                                        error_log("Acees token expired , renewing with refresh token");
					$od_client_id=getenv("ONEDRIVE_CLIENT_ID");
                                        $onedrive->setClientId($od_client_id);
					$od_client_secret=getenv("ONEDRIVE_CLIENT_SECRET");
                                        $onedrive->renewAccessToken($od_client_secret);
                                        //update the new acess token in DB so that it can be re-used
                                        $userauth=UserStorageAuth::find($storage->id);
                                        $userauth->auth_token=json_encode($onedrive->getState());
                                        $userauth->save();
                                }*/
                                $root = $onedrive->fetchRoot();
                                $root->createFile($fileName,$content);
                        }



	}
	function deleteFile($storage,$fileId,$filePath){
		//delete files	
		$user_credentials = $storage->auth_token;
		$onedrive=OneDriveStorageController::getDriveService($user_credentials,$storage->id);
		$onedrive->deleteDriveItem($fileId);
	}
	function downloadFile($storage,$fileId,$filePath){
		
		$content="";
		$user_credentials = $storage->auth_token;
		if($user_credentials){
				$onedrive=OneDriveStorageController::getDriveService($user_credentials,$storage->id);
                                /*$od_auth_data=json_decode($user_credentials);
				$onedrive=OneDriveStorageController::getClient("",$od_auth_data);
                                //move this check to user login action
                                if($onedrive->getAccessTokenStatus()!=1){
                                        error_log("Acees token expired , renewing with refresh token");
					$od_client_id=getenv("ONEDRIVE_CLIENT_ID");
                                        $onedrive->setClientId($od_client_id);
					$od_client_secret=getenv("ONEDRIVE_CLIENT_SECRET");
                                        $onedrive->renewAccessToken($od_client_secret);
                                        //update the new acess token in DB so that it can be re-used
                                        $userauth=UserStorageAuth::find($storage->id);
                                        $userauth->auth_token=json_encode($onedrive->getState());
                                        $userauth->save();
                                }*/
                                $content = $onedrive->fetchFileContent($fileId);
                        }
			return $content;
		}
	function listFiles($id,$limit=20,$page=0){
		
		$files_list=[];
		$user_credentials = UserStorageAuth::where([['id', '=', $id],['type','=','onedrive']])->value('auth_token');

		if($user_credentials){
			$onedrive=OneDriveStorageController::getDriveService($user_credentials,$id);
			/*$dbx_auth_data=json_decode($user_credentials);
			$onedrive=OneDriveStorageController::getClient("",$dbx_auth_data);
			error_log(print_r($onedrive,true));
			//move this check to user login action
			if($onedrive->getAccessTokenStatus()!=1){
				//update the new acess token in DB so that it can be re-used
				$od_client_secret=getenv("ONEDRIVE_CLIENT_SECRET");
				$onedrive->renewAccessToken($od_client_secret);
				$userauth=UserStorageAuth::find($id);
				$userauth->auth_token=json_encode($onedrive->getState());
				$userauth->save();
			}
			*/
			$root = $onedrive->fetchRoot();
			$files = $root->fetchDriveItems();
			
			foreach($files as $file){
				//error_log(print_r($file,true));
				$row = new \stdClass();
				$row->name=$file->getName();
				$row->mimeType= $file->isFolder()?"application/folder":"application/octet-stream";
				$row->id=$file->getId();
				array_push($files_list,$row);	
			}
		}

		error_log(print_r($files_list,true));
		return $files_list;
	}
}

?>
