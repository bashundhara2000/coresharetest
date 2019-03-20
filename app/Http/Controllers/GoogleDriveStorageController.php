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

class GoogleDriveStorageController extends AbstractStorageController{

	public static function getClient($state=""){

		$client = new Google_Client();
		$secret_file = getenv("GOOGLE_SECRET_FILE");
		error_log($secret_file);
		$client->setAuthConfig(base_path().'/'.$secret_file);
		$client->addScope(Google_Service_Drive::DRIVE);
		$client->setAccessType("offline");
                $client->setApprovalPrompt("force");
		// Request authorization from the user.
		$app_url = getenv("APP_URL");
		$client->setState($state);
		$redirect_uri = $app_url . '/drive/handle/oauth';
		$client->setRedirectUri($redirect_uri);
		return $client;

	}
	public static function getDriveService($user_credentials,$id){

			$client=GoogleDriveStorageController::getClient();
                        $access_token = json_decode($user_credentials,true);
                        $client->setAccessToken($access_token);
                        //Refresh the token if it's expired.
                        if ($client->isAccessTokenExpired()) {
                                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                                //store the refreshed authtoken
                                //file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
                                //store it in DB
                                $access_token = $client->getAccessToken();
                                $userauth=UserStorageAuth::find($id);
                                $userauth->auth_token=json_encode($access_token);
                                $userauth->save();
                        }
			$drive_service = new Google_Service_Drive($client);

			return $drive_service;
	}

	function getAuthorizationURL($state){
		
		$client=GoogleDriveStorageController::getClient($state);
		$authUrl = $client->createAuthUrl();
		return $authUrl;
	}
	function storeAuthCredentials($userid,$token,$id){

		$client=GoogleDriveStorageController::getClient($id);
		$access_token = $client->fetchAccessTokenWithAuthCode($token);

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
	//function uploadFile($id,$tmp_file){
	function uploadFile($content,$mimeType,$file_info,$storage,$userid){

		$fileName=$file_info->getFileName();
		$user_credentials = $storage->auth_token;
		$id=$storage->id;
		if(json_decode($user_credentials)->access_token!=""){
			$drive_service=GoogleDriveStorageController::getDriveService($user_credentials,$id);

				/*$access_token = json_decode($user_credentials,true);
				$client->setAccessToken($access_token);
				//Refresh the token if it's expired.
				if ($client->isAccessTokenExpired()) {
					$client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
					//store the refreshed authtoken
					//file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
					//store it in DB
					$access_token = $client->getAccessToken();
					$userauth=UserStorageAuth::find($id);
					$userauth->auth_token=json_encode($access_token);
					$userauth->save();
				}
				$drive_service = new Google_Service_Drive($client);
				*/
				$fileMetadata = new Google_Service_Drive_DriveFile(array(
							'name' => $fileName,
							'mimeType' => 'application/octet-stream'));//need to change this

				$file = $drive_service->files->create($fileMetadata, array(
							//'data' => $currentuserid."_".$content, //prepend userid to the file content
							'data' => $content, //prepend userid to the file content
							'mimeType' => mime_content_type($file_info->getRealPath()),
							'uploadType' => 'multipart',
							'fields' => 'id'));

				return $file->id;
			}else{
				// Request authorization from the user.
				//throw exception
				error_log("Authorization problem ");
			}

	}
	
	function deleteFile($storage,$fileId,$filePath){
		//delete files	
		$user_credentials=$storage->auth_token;
		$id=$storage->id;
		$driveService=GoogleDriveStorageController::getDriveService($user_credentials,$id);
                $driveService->files->delete($fileId);
	}
	
	function downloadFile($storage,$fileId,$filePath){
		
		$content="";
		$user_credentials=$storage->auth_token;
		$id=$storage->id;
                if ($user_credentials) {
                        if(json_decode($user_credentials)->access_token!=""){
			$driveService=GoogleDriveStorageController::getDriveService($user_credentials,$id);

                        /*$access_token = json_decode($user_credentials,true);
                        $client->setAccessToken($access_token);
                        //Refresh the token if it's expired.
                        if ($client->isAccessTokenExpired()) {
                                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                                //store the refreshed authtoken
                                //file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
                                //store it in DB
                                $access_token = $client->getAccessToken();
                                $userauth=UserStorageAuth::find($storage->id);
                                $userauth->auth_token=json_encode($access_token);
                                $userauth->save();
                        }
                        $driveService = new Google_Service_Drive($client);
                        */
			//$driveResourceService = new Google_Service_Drive($client);
                        //$fileId = '0BwwA4oUTeiV1UVNwOHItT0xfa2M';
                        $filemeta = $driveService->files->get($fileId);
                        $mimeType=$filemeta->mimeType;
                        //var_dump($filemeta,true);
                        $response = $driveService->files->get($fileId, array(
                                                'alt' => 'media'));
                        $content = $response->getBody()->getContents();
                        }

                }else{
                        //no credentials throw error
                        throw new Exception("No credentials");

                }

		return $content;
		}
	function listFiles($id,$limit=20,$page=0){
	        	
		$files_list=[];
		$user_credentials = UserStorageAuth::where([['id', '=', $id],['type','=','google']])->value('auth_token');
		$credentials=json_decode($user_credentials);
		if ($user_credentials) {
			if($credentials->access_token!=""){
			$drive_service=GoogleDriveStorageController::getDriveService($user_credentials,$id);

					/*$client->setAccessToken($user_credentials);
					//Refresh the token if it's expired.
					if ($client->isAccessTokenExpired()) {
						//get new access token and store it in the db
						$newtoken=$client->fetchAccessTokenWithRefreshToken();
						if($newtoken['refresh_token'] ==''){

							throw new Exception( "Linking failed , Please link again");
						}
						//store the refreshed authtoken
						//$credentials->access_token=$newtoken->access_token;
                                                //$credential_id = UserStorageAuth::where([['id', '=', $id],['type','=','google']])->value('id');
						$userauth=UserStorageAuth::find($id);
						$userauth->auth_token=json_encode($newtoken);
						$userauth->save();
					}
					$drive_service = new Google_Service_Drive($client);
					*/
					// Print the names and IDs for up to 10 files.
				$search = "trashed != true";
					$optParams = array(
							'q' => $search,
							'pageSize' => 1000
							);
					$files_list = $drive_service->files->listFiles($optParams)->getFiles(); //http://stackoverflow.com/questions/37975479/call-to-undefined-method-google-service-drive-filelistgetitems

			}
		}
                //$file_list = serialize($files_list);
		//error_log(print_r($files_list,true));
		return $files_list; 
                //return view('panels.user.home')->with(array('files_list'=> $files_list));
	}
}

?>
