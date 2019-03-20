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
use App\Providers\BoxAPI as BoxAPI;
use League\OAuth2\Client\Token\AccessToken;
use Stevenmaguire\OAuth2\Client\Provider\Box;

class BoxStorageController extends AbstractStorageController{

	public static function getClient($state=""){

		$app_url = getenv("APP_URL");
		$bx_client_id=getenv("BOX_CLIENT_ID");
		$bx_client_secret=getenv("BOX_CLIENT_SECRET");
		$callbackUrl = getenv("BOX_CALLBACK_URL");
		
		$provider = new Box([
				'clientId'          => $bx_client_id,
				'clientSecret'      => $bx_client_secret,
				'redirectUri'       => $callbackUrl,
				'state'             => $state
		]);
		return $provider;

	}

	function getAuthorizationURL($state){
		
		$provider=BoxStorageController::getClient($state);
		$authUrl = $provider->getAuthorizationUrl(['state'=>$state]);
		return $authUrl;
	}
	function storeAuthCredentials($userid,$token,$id){

		$provider=BoxStorageController::getClient();
		$access_token = $provider->getAccessToken('authorization_code', [
					'code' => $token
			]);
			//Fetch the AccessToken

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

		$app_url = getenv("APP_URL");
		$box_client_id=getenv("BOX_CLIENT_ID");
		$box_client_secret=getenv("BOX_CLIENT_SECRET");
		$callbackUrl = getenv("BOX_CALLBACK_URL");
	        $upload_file = array();	
		$fileName=$file_info->getFileName();
		$user_credentials = $storage->auth_token;
		if ($user_credentials) {
			$box_auth_data=json_decode($user_credentials);
			$box = new BoxAPI($box_client_id, $box_client_secret,$box_auth_data,$callbackUrl);
			$upload_file = $box->put_file($file_info->getRealPath(),$fileName,0);
                        foreach($upload_file['entries'] as $result){
			error_log("Upload response !!!!!!!!!!!!!!!!!!!!".print_r($result,true));
			return $result['id'];
                        }
		}
	}
	function deleteFile($storage,$fileId,$filePath){
		//delete files	
		$box_credentials = $storage->auth_token;
		if ($box_credentials) {
                                $box_auth_data=json_decode($box_credentials);
                                error_log("Going to upload box".print_r($box_auth_data,true));
                                $box_client_id=getenv("BOX_CLIENT_ID");
                                $box_client_secret=getenv("BOX_CLIENT_SECRET");
                                $callbackUrl = getenv("BOX_CALLBACK_URL");
                                $box = new BoxAPI($box_client_id, $box_client_secret,$box_auth_data,$callbackUrl);

                                $content=$box->delete_file($fileId);
				return "success";
                }else{
                        //no credentials throw error
                        throw new Exception("No credentials");

                }
	}
	function downloadFile($storage,$fileId,$filePath){
		
		$content="";
		$box_credentials = $storage->auth_token;
		if ($box_credentials) {
                                $box_auth_data=json_decode($box_credentials);
                                error_log("Going to upload box".print_r($box_auth_data,true));
                                $box_client_id=getenv("BOX_CLIENT_ID");
                                $box_client_secret=getenv("BOX_CLIENT_SECRET");
                                $callbackUrl = getenv("BOX_CALLBACK_URL");
                                $box = new BoxAPI($box_client_id, $box_client_secret,$box_auth_data,$callbackUrl);

                                $content=$box->download_file($fileId);
                }else{
                        //no credentials throw error
                        throw new Exception("No credentials");

                }
				return $content;

		}
	function listFiles($id,$limit=20,$page=0){

		$app_url = getenv("APP_URL");
		$box_client_id=getenv("BOX_CLIENT_ID");
		$box_client_secret=getenv("BOX_CLIENT_SECRET");
		$callbackUrl = getenv("BOX_CALLBACK_URL");
		$files_list=[];
		$user_credentials = UserStorageAuth::where([['id', '=', $id],['type','=','box']])->value('auth_token');


		if($user_credentials){
			$dbx_auth_data=json_decode($user_credentials);

			$access_token= new AccessToken([
					'expires'=>$dbx_auth_data->expires,
					'access_token'=>$dbx_auth_data->access_token,
					'refresh_token'=>$dbx_auth_data->refresh_token,
					'token_type'=>$dbx_auth_data->token_type
			]);
			if($access_token->hasExpired()){
				$provider = new Box([
						'clientId'          => $box_client_id,
						'clientSecret'      => $box_client_secret,
						'redirectUri'       => $callbackUrl
				]);
				$newAccessToken = $provider->getAccessToken('refresh_token', [
						'refresh_token' => $access_token->getRefreshToken()
				]);
				//$dbx_auth_data->access_token=$newAccessToken;
				$userauth=UserStorageAuth::find($id);
				$userauth->auth_token=json_encode($newAccessToken);
				$userauth->save();
				$dbx_auth_data=json_decode(json_encode($newAccessToken)); //skipping DB
			}
			$box = new BoxAPI($box_client_id, $box_client_secret,$dbx_auth_data,$callbackUrl);
			// User details

			// Get folder details
			//echo $box->get_folder_details('');
			// Get folder items list
			$folders=$box->get_folder_items('0');
			error_log(print_r($folders,true));

			foreach($folders['entries'] as $folder){

					if($folder['type']=='file'){
					$row = new \stdClass();
					$row->name=$folder['name'];
					$row->id=$folder['id'];
					$row->mimeType="file";
					array_push($files_list,$row);	
					continue;
					}else{ //folder
					
					$row = new \stdClass();
					$row->name=$folder['name'];
					$row->id=$folder['id'];
					$row->mimeType="folder";
					array_push($files_list,$row);	
				
					$files=$box->get_folder_items($folder['id']);
					foreach($files['entries'] as $file){
					$row = new \stdClass();
					$row->name=$file['name'];
					$row->mimeType="file";
					$row->folder=$folder['name'];
					$row->id=$file['id'];
					array_push($files_list,$row);	
					}
			 	}
			}

			}

			return $files_list;
		}
	}

?>
