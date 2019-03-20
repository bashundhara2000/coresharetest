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



class DeleteController extends Controller

{

    //



   public function deleteFile(Request $request,$fileId){



                $client = new Google_Client();

                $client->setAuthConfig(__DIR__.'/../../../client_secret.json');

                $client->addScope(Google_Service_Drive::DRIVE);

                $client->setAccessType("offline");

     		$currentuserid = Auth::user()->id;

		$user_credentials = UserStorageAuth::where('user_id', '=', $currentuserid)->value('auth_token');

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

                        $driveService = new Google_Service_Drive($client);

                        //$driveResourceService = new Google_Service_Drive($client);

			//$fileId = '0BwwA4oUTeiV1UVNwOHItT0xfa2M';

			$filemeta = $driveService->files->get($fileId);

			//var_dump($filemeta,true);

			$response = $driveService->files->get($fileId, array(

						'alt' => 'media'));

			$content = $response->getBody()->getContents();

                        



                        //Removed by AceKrypt for decryption



                        /*$prefix=$currentuserid."_";



			if (substr($content, 0, strlen($prefix)) == $prefix) {

				$content = substr($content, strlen($prefix));

			} */



            //Added by AceKrypt for deletion



                        try {

                                $driveService->files->delete($fileId);

                            } catch (Exception $e) {

                                return "Error: " . $e->getMessage();

                            }
				return "OK";
                //return Redirect::to('/user');

			}	

		}

	}





}
