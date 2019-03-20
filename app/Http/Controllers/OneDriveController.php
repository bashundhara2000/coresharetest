<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests;
use Auth;
use DB;
use App\Models\UserStorageAuth;
use Krizalys\Onedrive\Client;

class OneDriveController extends Controller
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
		$od_client_id=getenv("ONEDRIVE_CLIENT_ID");
		$od_client_secret=getenv("ONEDRIVE_CLIENT_SECRET");
		$callbackUrl = getenv("ONEDRIVE_CALLBACK_URL");
		


		if (! isset($_GET['code'])) {

			// we can skip this stage! or throw an error
			
			// Instantiates a OneDrive client bound to your OneDrive application.
			$onedrive = new Client([
					'client_id' => $od_client_id,
			]);
			$request->session()->put('onedrive.client.state', $onedrive->getState());

			// Gets a log in URL with sufficient privileges from the OneDrive API.
			$authUrl = $onedrive->getLogInUrl([
					'wl.signin',
					'wl.basic',
					'wl.contacts_skydrive',
					'wl.skydrive_update',
					'wl.offline_access',
			], $callbackUrl);


			return Redirect::to(filter_var($authUrl, FILTER_SANITIZE_URL));
			//header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
		} else {

			$code = $_GET['code'];
			$onedrive = new Client([
					'client_id' => $od_client_id,

					// Restore the previous state while instantiating this client to proceed in
					// obtaining an access token.
					'state' => $request->session()->get('onedrive.client.state')
			]);
			// Obtain the token using the code received by the OneDrive API.
			$onedrive->obtainAccessToken($od_client_secret, $code);
			$token=$onedrive->getState();
			$request->session()->put('onedrive.client.state', $onedrive->getState());
			// Try to get an access token (using the authorization code grant)
			//Fetch the AccessToken
			error_log("OD token is ".print_r($token,true));
			//store it in DB
			$currentuserid = Auth::user()->id;
			$userauth = UserStorageAuth::firstOrNew(array('user_id' => $currentuserid , 'type'=>'onedrive'));
			//$userauth=UserStorageAuth::find($currentuserid);
			$userauth->auth_token=json_encode($token);
			$userauth->type='onedrive';
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

		$app_url = getenv("APP_URL");
		$od_client_id=getenv("ONEDRIVE_CLIENT_ID");
		$od_client_secret=getenv("ONEDRIVE_CLIENT_SECRET");
		$callbackUrl = getenv("ONEDRIVE_CALLBACK_URL");


		$currentuserid = Auth::user()->user_id;
		$user_credentials = UserStorageAuth::where([['user_id', '=', $currentuserid],['type','=','box']])->value('auth_token');
		echo $user_credentials;
		if ($user_credentials) {
			return Redirect::to('/user/home');

		} else {
			// Instantiates a OneDrive client bound to your OneDrive application.
			$onedrive = new Client([
					'client_id' => $od_client_id,
			]);
			error_log("Client id is ".$od_client_id);
			$request->session()->put('onedrive.client.state', $onedrive->getState());

			// Gets a log in URL with sufficient privileges from the OneDrive API.
			$authUrl = $onedrive->getLogInUrl([
					'wl.signin',
					'wl.basic',
					'wl.contacts_skydrive',
					'wl.skydrive_update',
					'wl.offline_access',
			], $callbackUrl);



			return Redirect::to(filter_var($authUrl, FILTER_SANITIZE_URL));
		}    

	}
}
