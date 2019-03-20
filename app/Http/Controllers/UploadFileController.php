<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Requests;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Auth;
use DB;
use SplFileInfo;
use App\Models\UserStorageAuth;
use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxFile;
use App\Providers\BoxAPI as BoxAPI;
use Krizalys\Onedrive\Client as OneDriveClient;


class UploadFileController extends Controller
{
	public function index(){
		return view('uploadfile');
	}
	public function showUploadFile(Request $request){
	
		$storage_id = $request->input('storage');
		$user_id = Auth::user()->id;
		$storage = UserStorageAuth::select('auth_token','type','id')->where([['user_id','=',$user_id],['id','=',$storage_id]])->get();
		if($storage==null){
			return Response::make("ERROR : No permission");
		}
		$uploadStorage=$storage[0]->type;
		$tmp_file = $request->file;
		$destinationPath = 'uploads';
		$fileName=$tmp_file->getClientOriginalName();
		$mimeType=$tmp_file->getMimeType();
		$tmp_file->move(base_path().'/uploads',$fileName);
		$file = fopen(base_path().'/uploads/'.$fileName, "r") or die("Unable to open file!");
		$file_info = new SplFileInfo(base_path().'/uploads/'.$fileName);

		$content = file_get_contents($file_info->getRealPath());

		$user_credentials =$storage[0]->auth_token;
		error_log("Upload type is ".$user_credentials);
		

		//Move Uploaded File

		/*
		$EncAlgo = $request->input('Encryption');
		if ($EncAlgo == 'AES-128')
		{
			$CipherType = "AES-128-CBC";
		}
		elseif($EncAlgo == 'AES-192')
		{
			$CipherType = "AES-192-CBC";
		}         
		elseif($EncAlgo == 'AES-256')
		{
			$CipherType = "AES-256-CBC";
		} 
		elseif($EncAlgo == 'IDEA')
		{
			$CipherType = "IDEA-CBC";      
		} 
		elseif($EncAlgo == 'Blowfish')
		{       
			$CipherType = "BF-CBC";
		} 
		else
		{
			$CipherType = "AES-128-CBC";
		}

		//$CipherType = ChooseCipherType($EncAlgo);
		//Display File Name
		echo 'File Name: '.$fileName;
		echo '<br>';

		//Display File Extension
		echo 'File Extension: '.$file_info->getExtension();
		echo '<br>';

		//Display File Real Path
		echo 'File Real Path: '.$file_info->getRealPath();
		echo '<br>';

		//Display File Size
		echo 'File Size: '.$file_info->getSize();
		echo '<br>';

		//Display File Mime Type
		echo 'File Mime Type: '.$mimeType;

		//Added by AceKrypt for encryption
		$key = pack('H*', "bcb04b7e103a0cd8b54763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3");
		$key_size =  strlen($key);
		echo "Key size: " . $key_size . "\n";
		$plaintext = $content;
		error_log('$CipherType: ' . $CipherType);
		$iv_size = openssl_cipher_iv_length($CipherType);
		//error_log('$IVsize: ' . $iv_size);
		$iv = openssl_random_pseudo_bytes($iv_size);
		//error_log('$IV: ' . $iv);
		$ciphertext = openssl_encrypt($plaintext, $CipherType, $key, 0, $iv);
		//error_log('$ciphertext: ' . $ciphertext);
		$ciphertext = $CipherType . $iv . $ciphertext;
		$ciphertext_base64 = base64_encode($ciphertext);
		//echo  $ciphertext_base64 . "\n";
		$content = $ciphertext;
		file_put_contents($file_info->getRealPath(),$content); //replace the original with encrypted content
		*/
		if($uploadStorage == 'google'){
			//upload file to google drive 

			$client = new Google_Client();
			$client->setAuthConfig(__DIR__.'/../../../client_secret.json');
			$client->addScope(Google_Service_Drive::DRIVE);
			$client->setAccessType("offline");
			if ($user_credentials) {
				if(json_decode($user_credentials)->access_token!=""){

					$access_token = json_decode($user_credentials,true);
					$client->setAccessToken($access_token);
					//Refresh the token if it's expired.
					if ($client->isAccessTokenExpired()) {
						$client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
						//store the refreshed authtoken
						//file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
						//store it in DB
						$access_token = $client->getAccessToken();
						$userauth=UserStorageAuth::find($currentuserid);
						$userauth->auth_token=json_encode($access_token);
						$userauth->save();
					}
					$drive_service = new Google_Service_Drive($client);

					$fileMetadata = new Google_Service_Drive_DriveFile(array(
								'name' => $fileName,
								'mimeType' => 'application/octet-stream'));

					$file = $drive_service->files->create($fileMetadata, array(
								//'data' => $currentuserid."_".$content, //prepend userid to the file content
								'data' => $content, //prepend userid to the file content
								'mimeType' => mime_content_type($file_info->getRealPath()),
								'uploadType' => 'multipart',
								'fields' => 'id'));

					error_log($file->id);
					return Response::make(json_encode($file->id));
				}else{
					// Request authorization from the user.
					$app_url = getenv("APP_URL");
					$redirect_uri = $app_url . '/drive/handle/oauth';
					$client->setRedirectUri($redirect_uri);
					//$client->authenticate();
					$authUrl = $client->createAuthUrl();
					//return Redirect::away($authUrl);
					return Response::make('failed');

				}
			} else {
				// Request authorization from the user.
				$app_url = getenv("APP_URL");
				print_r($app_url,true);
				$redirect_uri = $app_url . '/drive/handle/oauth';
				$client->setRedirectUri($redirect_uri);
				//$client->authenticate();
				$authUrl = $client->createAuthUrl();
				//echo "No Credentials";
				//echo filter_var($authUrl, FILTER_SANITIZE_URL);
				//return Redirect::away($authUrl);
					return Response::make('failed');
			}
			return Redirect::to('/user');
		}else if($uploadStorage == 'dropbox'){
			if ($user_credentials) {
				$dbx_auth_data=json_decode($user_credentials);
				$dbx_client_id=getenv("DROPBOX_CLIENT_ID");
				$dbx_client_secret=getenv("DROPBOX_CLIENT_SECRET");
				$callbackUrl = getenv("DROPBOX_CALLBACK_URL");
				$app = new DropboxApp($dbx_client_id, $dbx_client_secret,$dbx_auth_data->access_token);
				$dropbox = new Dropbox($app);
				$mode = DropboxFile::MODE_READ;
				$dropboxFile = DropboxFile::createByStream($file_info->getRealPath(), $content, DropboxFile::MODE_READ);
				//$dropboxFile = new DropboxFile($file->getRealPath());

				$upload_file = $dropbox->upload($dropboxFile,'/'.$fileName , ['autorename' => true]);
				//Uploaded File
				$upload_file->getName();
				return Redirect::to('/user/home');

			} else {
				$authHelper = $dropbox->getAuthHelper();
				//Fetch the Authorization/Login URL
				$authUrl = $authHelper->getAuthUrl($callbackUrl);

				//return Redirect::to(filter_var($authUrl, FILTER_SANITIZE_URL));
					echo "ERROR";
#return Redirect::away($authUrl);
			}

		}else if($uploadStorage == 'box'){
			if ($user_credentials) {
				$box_auth_data=json_decode($user_credentials);
				error_log("Going to upload box".print_r($box_auth_data,true));
				$box_client_id=getenv("BOX_CLIENT_ID");
				$box_client_secret=getenv("BOX_CLIENT_SECRET");
				$callbackUrl = getenv("BOX_CALLBACK_URL");
				$box = new BoxAPI($box_client_id, $box_client_secret,$box_auth_data,$callbackUrl);

				$upload_file = $box->put_file($file_info->getRealPath(),$fileName,0);
				error_log("Upload response ".print_r($upload_file,true));	
			}
			return Redirect::to('/user/home');
		}else if($uploadStorage == 'onedrive'){

			$od_client_id=getenv("ONEDRIVE_CLIENT_ID");
			$od_client_secret=getenv("ONEDRIVE_CLIENT_SECRET");



			if($user_credentials){
				$dbx_auth_data=json_decode($user_credentials);
				$onedrive = new OneDriveClient([
						'authstate' => $dbx_auth_data,
				]);
				//move this check to user login action
				if($onedrive->getAccessTokenStatus()!=1){
					error_log("Acees token expired , renewing with refresh token");
					$onedrive->setClientId($od_client_id);
					$onedrive->renewAccessToken($od_client_secret);
					//update the new acess token in DB so that it can be re-used
					$credentials_id = UserStorageAuth::where([['user_id', '=', $currentuserid],['type','=','onedrive']])->value('id');
					$userauth=UserStorageAuth::find($credentials_id);
					$userauth->auth_token=json_encode($onedrive->getState());
					$userauth->save();
				}
				$root = $onedrive->fetchRoot();
				$root->createFile($fileName,$content);
			}
		}

		//remove the temp file here

	}
	private function ChooseCipherType($EncAlgo)
	{
		return $CipherType;
	}

}
