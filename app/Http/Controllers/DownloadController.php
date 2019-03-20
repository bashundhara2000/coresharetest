<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Auth;
use DB;
use App\Models\User;
use App\Models\UserStorageAuth;
use Exception;
use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxFile;
use App\Providers\BoxAPI;
use Krizalys\Onedrive\Client as OneDriveClient;


class DownloadController extends Controller
{
    //

   public function downloadFile(Request $request,$fileId,$filePath){
		$content="";
                $downloadStorage = $request->input('type');
     		$currentuserid = Auth::user()->id;
		$handle = fopen("/tmp/".$filePath, 'w') or die('Cannot create temp file:  '); //implicitly creates file
		$mimeType=mime_content_type("/tmp/".$filePath);
                if($downloadStorage == 'google'){
                $client = new Google_Client();
                $client->setAuthConfig(__DIR__.'/../../../client_secret.json');
                $client->addScope(Google_Service_Drive::DRIVE);
                $client->setAccessType("offline");
		$user_credentials = UserStorageAuth::where([['user_id', '=', $currentuserid],['type','=','google']])->value('auth_token');
                if ($user_credentials) {

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
                        $driveService = new Google_Service_Drive($client);
                        //$driveResourceService = new Google_Service_Drive($client);
			//$fileId = '0BwwA4oUTeiV1UVNwOHItT0xfa2M';
			$filemeta = $driveService->files->get($fileId);
			$mimeType=$filemeta->mimeType;
			//var_dump($filemeta,true);
			$response = $driveService->files->get($fileId, array(
						'alt' => 'media'));
			$content = $response->getBody()->getContents();

		}else{  
			//no credentials throw error
			throw new Exception("No credentials");

		}
		}else if($downloadStorage == 'dropbox'){
						
				$dbx_credentials = UserStorageAuth::where([['user_id', '=', $currentuserid],['type','=','dropbox']])->value('auth_token');
				if ($dbx_credentials) {
					$dbx_auth_data=json_decode($dbx_credentials);
					$dbx_client_id=getenv("DROPBOX_CLIENT_ID");
					$dbx_client_secret=getenv("DROPBOX_CLIENT_SECRET");
					$callbackUrl = getenv("DROPBOX_CALLBACK_URL");
					$app = new DropboxApp($dbx_client_id, $dbx_client_secret,$dbx_auth_data->access_token);
					$dropbox = new Dropbox($app);
					$file = $dropbox->download("/".$filePath);
					//Downloaded File Metadata
					//$metadata = $file->getMetadata();
					//File Contents
					$content = $file->getContents();

				}
	
		}else if($downloadStorage == 'box'){
			$box_credentials = UserStorageAuth::where([['user_id', '=', $currentuserid],['type','=','box']])->value('auth_token');
			if ($box_credentials) {
				$box_auth_data=json_decode($box_credentials);
				error_log("Going to upload box".print_r($box_auth_data,true));
				$box_client_id=getenv("BOX_CLIENT_ID");
				$box_client_secret=getenv("BOX_CLIENT_SECRET");
				$callbackUrl = getenv("BOX_CALLBACK_URL");
				$box = new BoxAPI($box_client_id, $box_client_secret,$box_auth_data,$callbackUrl);

				$content=$box->download_file($fileId);
			}


			

		}else if($downloadStorage == 'onedrive'){

			$od_client_id=getenv("ONEDRIVE_CLIENT_ID");
			$od_client_secret=getenv("ONEDRIVE_CLIENT_SECRET");


			$dbx_credentials = UserStorageAuth::where([['user_id', '=', $currentuserid],['type','=','onedrive']])->value('auth_token');

			if($dbx_credentials){
				$dbx_auth_data=json_decode($dbx_credentials);
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
				$content = $onedrive->fetchFileContent($fileId);
			}
		}


				//Removed by AceKrypt for decryption

                        /*$prefix=$currentuserid."_";

			if (substr($content, 0, strlen($prefix)) == $prefix) {
				$content = substr($content, strlen($prefix));
			} 

                        //Added by AceKrypt for decryption
                        //error_log("Hello: " . substr($content,0,strlen(MCRYPT_RIJNDAEL_128)));
                        if (substr($content,0,strlen("AES-128-CBC"))== "AES-128-CBC")
                        {
                                $EncAlgo = "AES-128-CBC";
                                $content = substr($content, strlen("AES-128-CBC"));
                                //error_log(substr($content,0,strlen(MCRYPT_RIJNDAEL_128)));
                                //error_log($EncAlgo);
                                //error_log(strlen(MCRYPT_RIJNDAEL_128));
                        }
                        elseif (substr($content,0,strlen("AES-192-CBC"))== "AES-192-CBC")
                        {
                                $EncAlgo = "AES-192-CBC";
                                $content = substr($content, strlen("AES-192-CBC"));
                                //error_log(substr($content,0,strlen(MCRYPT_RIJNDAEL_128)));
                                //error_log($EncAlgo);
                                //error_log(strlen(MCRYPT_RIJNDAEL_128));
                        }
                        elseif (substr($content,0,strlen("AES-256-CBC")) == "AES-256-CBC")
                        {
                                $EncAlgo = "AES-256-CBC";
                                $content = substr($content, strlen("AES-256-CBC"));
                                //error_log($EncAlgo);
                                //error_log(strlen(MCRYPT_RIJNDAEL_256));
                        }
                        elseif (substr($content,0,strlen("IDEA-CBC")) == "IDEA-CBC")
                        {
                                $EncAlgo = "IDEA-CBC";
                                $content = substr($content, strlen("IDEA-CBC"));
                                //error_log($EncAlgo);
                                //error_log(strlen(MCRYPT_3DES));
                        }
                        elseif (substr($content,0,strlen("BF-CBC")) == "BF-CBC")
                        {
                                $EncAlgo = "BF-CBC";
                                $content = substr($content, strlen("BF-CBC"));
                                //error_log($EncAlgo);
                                //error_log(strlen(MCRYPT_BLOWFISH));                             
                        }
                        else 
                        {
                                $EncAlgo = "NoEnc";
                        }
                if($EncAlgo != "NoEnc")
                { 
                        $iv_size = $iv_size = openssl_cipher_iv_length($EncAlgo);
                        $key = pack('H*', "bcb04b7e103a0cd8b54763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3");
                        $key_size =  strlen($key);
                        //echo "Key size: " . $key_size . "\n";

                        $iv_dec = substr($content, 0, $iv_size);
                        $content = substr($content, $iv_size);
                        
                        
                        $content = openssl_decrypt($content, $EncAlgo, $key, 0, $iv_dec);
			error_log($content);
                }
		*/
                //error_log('$EncAlgo=' . $EncAlgo);
                        /*try {
                                $driveService->files->delete($fileId);
                            } catch (Exception $e) {
                                return "Error: " . $e->getMessage();
                            }*/
			error_log("mime type is".print_r($mimeType,true));
			return response($content)->header('Content-Type',$mimeType) ;

			}	


}
