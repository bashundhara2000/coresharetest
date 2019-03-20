<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests;
use Auth;
use DB;
use App\Models\UserStorageAuth;
use Stevenmaguire\OAuth2\Client\Provider\Box;

class BoxController extends Controller
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
		$bx_client_id=getenv("BOX_CLIENT_ID");
		$bx_client_secret=getenv("BOX_CLIENT_SECRET");
		$callbackUrl = getenv("BOX_CALLBACK_URL");
		
		$provider = new Box([
				'clientId'          => $bx_client_id,
				'clientSecret'      => $bx_client_secret,
				'redirectUri'       => $callbackUrl
		]);


		if (! isset($_GET['code'])) {
			//Fetch the Authorization/Login URL
			$authUrl = $provider->getAuthorizationUrl();

			return Redirect::to(filter_var($authUrl, FILTER_SANITIZE_URL));
			//header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
		} else {

			$code = $_GET['code'];
			$state = $_GET['state'];
			// Try to get an access token (using the authorization code grant)
			$token = $provider->getAccessToken('authorization_code', [
					'code' => $code
			]);
			//Fetch the AccessToken
			error_log("Box token is ".print_r($token,true));
			//store it in DB
			$currentuserid = Auth::user()->id;
			$userauth = UserStorageAuth::firstOrNew(array('user_id' => $currentuserid , 'type'=>'box'));
			//$userauth=UserStorageAuth::find($currentuserid);
			$userauth->auth_token=json_encode($token);
			$userauth->type='box';
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
	
		error_log("Going to auth box");	
		$app_url = getenv("APP_URL");
		$bx_client_id=getenv("BOX_CLIENT_ID");
		$bx_client_secret=getenv("BOX_CLIENT_SECRET");
		$callbackUrl = getenv("BOX_CALLBACK_URL");
		$provider = new Box([
				'clientId'          => $bx_client_id,
				'clientSecret'      => $bx_client_secret,
				'redirectUri'       => $callbackUrl
		]);
		
		$currentuserid = Auth::user()->user_id;
		$user_credentials = UserStorageAuth::where([['user_id', '=', $currentuserid],['type','=','box']])->value('auth_token');
		echo $user_credentials;
		if ($user_credentials) {
			return Redirect::to('/user/home');

		} else {
			// If we don't have an authorization code then get one
			$authUrl = $provider->getAuthorizationUrl();
			return Redirect::to(filter_var($authUrl, FILTER_SANITIZE_URL));
			#return Redirect::away($authUrl);
		}    

	}
}
