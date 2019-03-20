<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests;
use Auth;
use DB;
use App\Models\UserStorageAuth;
use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxFile;

class DropBoxStorageController extends AbstractStorageController{

	public static function getClient($accessToken=""){

		$app_url = getenv("APP_URL");
		$dbx_client_id=getenv("DROPBOX_CLIENT_ID");
		$dbx_client_secret=getenv("DROPBOX_CLIENT_SECRET");

		if($accessToken==""){
		$app = new DropboxApp($dbx_client_id, $dbx_client_secret);
		}else{
		$app = new DropboxApp($dbx_client_id, $dbx_client_secret,$accessToken);
		}
		
		$dropbox = new Dropbox($app);

		return $dropbox;

	}

	function getAuthorizationURL($state){
		
		$dropbox=DropBoxStorageController::getClient();
		$authHelper = $dropbox->getAuthHelper();
		//Callback URL
		$callbackUrl = getenv("DROPBOX_CALLBACK_URL");
        	$authUrl = $authHelper->getOAuth2Client()->getAuthorizationUrl($callbackUrl, $state, []);
		//$authUrl = $authHelper->getAuthUrl($callbackUrl);
		return $authUrl;
	}
	function storeAuthCredentials($userid,$token,$id){

		$dropbox=DropBoxStorageController::getClient();
		$authHelper = $dropbox->getAuthHelper();
		//$access_token = $client->fetchAccessTokenWithAuthCode($token);
		$callbackUrl = getenv("DROPBOX_CALLBACK_URL");
		//Fetch the AccessToken
		$access_token = $authHelper->getAccessToken($token, null, $callbackUrl)->getData();
		error_log("Dropbox token is ".print_r($access_token,true));

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

		 $content="";
                $user_credentials=$storage->auth_token;

		$fileName=$file_info->getFileName();
		$content = file_get_contents($file_info->getRealPath());
		if ($user_credentials) {
			$credentials=json_decode($user_credentials);

			if($credentials->access_token!=""){
				$client=DropBoxStorageController::getClient($credentials->access_token);
				//Refresh the token if it's expired.
                                $mode = DropboxFile::MODE_READ;
                                $dropboxFile = DropboxFile::createByStream($file_info->getRealPath(), $content, DropboxFile::MODE_READ);
                                //$dropboxFile = new DropboxFile($file->getRealPath());

                                $upload_file = $client->upload($dropboxFile,'/'.$fileName , ['autorename' => true]);
                                //Uploaded File
                                return $upload_file->getName();
			}else{
				// Request authorization from the user.
				//throw exception
				error_log("Authorization problem ");
			}
			
		}
	}
	function deleteFile($storage,$fileId,$filePath){
		//delete files	
		$user_credentials = $storage->auth_token;
		if ($user_credentials) {
			$credentials=json_decode($user_credentials);

			if($credentials->access_token!=""){
				$client=DropBoxStorageController::getClient($credentials->access_token);
				 $file = $client->delete("/".$filePath);
				 return "success";
                        }

                }else{
                        //no credentials throw error
                        throw new Exception("No credentials");

                }
	}
	function downloadFile($storage,$fileId,$filePath){
		
		$content="";
		$user_credentials = $storage->auth_token;
		if ($user_credentials) {
			$credentials=json_decode($user_credentials);

			if($credentials->access_token!=""){
				$client=DropBoxStorageController::getClient($credentials->access_token);
				 $file = $client->download("/".$filePath);
                                        //Downloaded File Metadata
                                        //$metadata = $file->getMetadata();
                                        //File Contents
                                        $content = $file->getContents();
					return $content;
                        }

                }else{
                        //no credentials throw error
                        throw new Exception("No credentials");

                }

		}
	function listFiles($id,$limit=20,$page=0){
		
		$files_list=[];
		$user_credentials = UserStorageAuth::where([['id', '=', $id],['type','=','dropbox']])->value('auth_token');
		if ($user_credentials) {
			$credentials=json_decode($user_credentials);

			if($credentials->access_token!=""){
					$client=DropBoxStorageController::getClient($credentials->access_token);
					$listFolderContents = $client->listFolder("/");
					//Fetch Items
					$files = $listFolderContents->getItems();			
			}


			foreach($files as $file){
				$row = new \stdClass();
				$row->name=$file->getName();
				$row->mimeType=$file->getTag();
				$row->id=$file->getId();
				array_push($files_list,$row);	
			}
		}
		error_log(print_r($files_list,true));
		return $files_list;
	}
}

?>
