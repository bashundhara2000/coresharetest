<?php

namespace App\Http\Controllers;

use App\Models\UserPreferences;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\RedirectResponse;
use Auth;
use DB;

class KeyStoreController extends Controller
{

    public function addEMSK(Request $request)
    {
	$user_id = Auth::user()->id;

	$EMSK = json_decode($request->getContent(),true);
        $userpref = UserPreferences::firstOrNew(array('user_id' => $user_id ));
	$userpref->emsk=json_encode($EMSK);
	$userpref->save();

	return Response::make($EMSK);

    }

    public function getEMSK(Request $request){

	$user_id = Auth::user()->id;
	$emsk = UserPreferences::where('user_id', '=', $user_id)->value('emsk');
	return Response::make($emsk);

    }

    public function addPublicKey(Request $request)
    {
	$user_id = Auth::user()->id;

	$pk = json_decode($request->getContent(),true);
        $userpref = UserPreferences::firstOrNew(array('user_id' => $user_id ));
	$userpref->publicKey=json_encode($pk);
	$userpref->save();

	return Response::make($pk);

    }
    
    public function getOtherUsersPK(Request $request,$user_id){

	$pk = UserPreferences::where('user_id', '=', $user_id)->value('publicKey');
	return Response::make($pk);

    }

    public function getPublicKey(Request $request){

	$user_id = Auth::user()->id;

	$pk = UserPreferences::where('user_id', '=', $user_id)->value('publicKey');
	return Response::make($pk);

    }


}
