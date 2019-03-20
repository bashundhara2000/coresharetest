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

class StorageController extends Controller{


	public function link(Request $request,$type){
		//check for authorized user, and redirect to the auth URL
		$downloadStorage = $request->input('type');

	}
	
	public function store(Request $request){

	}

}

?>
