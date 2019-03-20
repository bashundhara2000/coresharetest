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

class DriveController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created google api credentials in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		//
		//
		$app_url = getenv("APP_URL");

		$client = new Google_Client();
		$client->setAuthConfigFile(__DIR__.'/../../../client_secret.json');
		$client->setApplicationName("Google Drive Integration Test");
  		//$client->setScopes(SCOPES);
		$client->setRedirectUri($app_url . '/drive/handle/oauth');
		$client->addScope(Google_Service_Drive::DRIVE); //::DRIVE_METADATA_READONLY
		$client->setAccessType("offline");
		$client->setApprovalPrompt("force");
		$id = $request->input('id');

		if (! isset($_GET['code'])) {
			$auth_url = $client->createAuthUrl();
			return Redirect::to(filter_var($auth_url, FILTER_SANITIZE_URL));
			//header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
		} else {
			//$client->authenticate($_GET['code']);
			$access_token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

			//store it in DB
			//$access_token = $client->getAccessToken();
			//$refresh_token = $client->getRefreshToken();
			//error_log("Refresh token ".$refresh_token);
			$currentuserid = Auth::user()->id;
			//$userauth = UserStorageAuth::firstOrNew(array('user_id' => $currentuserid , 'type'=>'google'));
			$userauth=UserStorageAuth::find($id);
			$userauth->auth_token=json_encode($access_token);
			$userauth->type='google';
			//$userauth->refesh_token=json_encode($refresh_token);
			$userauth->save();
			//$user_credentials = UserStorageAuth::where('user_id', '=', $currentuserid)->first();
			
			//file_put_contents(__DIR__."/credentials.json", json_encode($access_token));

			return Redirect::to('/user/home');
			//header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
		}  
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		//
	}


	public function link(Request $request){

		if (!file_exists(__DIR__."/../../../client_secret.json")) exit("Client secret file not found");
		$client = new Google_Client();
		$client->setAuthConfig(__DIR__.'/../../../client_secret.json');
		$client->addScope(Google_Service_Drive::DRIVE);
		$client->setAccessType("offline");
		$client->setApprovalPrompt("force");
		$currentuserid = Auth::user()->user_id;
		$user_credentials = UserStorageAuth::where([['user_id', '=', $currentuserid],['type','=','google']])->value('auth_token');
		echo $user_credentials;
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
                               // $credentials->access_token=$newtoken->access_token;
				$userauth=UserStorageAuth::find($currentuserid);
				$userauth->auth_token=json_encode($access_token);
				$userauth->save();
			}
			$drive_service = new Google_Service_Drive($client);
			$files_list = $drive_service->files->listFiles(array())->getFiles(); 
			echo json_encode($files_list);
			}else{
			// Request authorization from the user.
                	$app_url = getenv("APP_URL");
			$redirect_uri = $app_url . '/drive/handle/oauth';
			$client->setRedirectUri($redirect_uri);
			//$client->authenticate();
			$authUrl = $client->createAuthUrl();
			//echo "No Credentials";
			//echo filter_var($authUrl, FILTER_SANITIZE_URL);
			//error_log('vivek $authUrl: '.$authUrl);
			return Redirect::away($authUrl);

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
			return Redirect::away($authUrl);
		}    

	}
}
