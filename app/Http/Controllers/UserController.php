<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Response;
use App\Models\UserPreferences;
class UserController extends Controller
{

    public function getStorage()
    {
        $socialproviders = DB::table('social_logins')->select('id','user_id','provider')->get();
                $user_id = Auth::user()->id;
        foreach($socialproviders as $socialprovider){
        if($socialprovider->user_id == $user_id){
           $userprovider = $socialprovider->provider;
         }
        }
       if(isset($userprovider)){
        return view('panels.user.storage')->with('userprovider', $userprovider);
      }else{
        return view('panels.user.storage');
     }
    }

     public function deleteAccount()
    {

                $user_id = Auth::user()->id;

              DB::table('users')->where('id',$user_id)->delete(); 

        return redirect('login');

    }

    public function getHome()
    {
        return view('panels.user.home');

    }

    public function getSharemessage()
    {

        return view('panels.user.sharemessage');

    }

    public function getUserprofiles()
    {

        return view('panels.user.settings');

    }

    public function getContact()
    {

        return view('panels.user.contact');

    }
    public function getExiststorage()
    {

        return view('panels.user.existstorage');

    }
    public function getProtected()
    {

        return view('panels.user.protected');

    }
    public function downloadMsk()
    {

	    $txt = "";
	    $datas = DB::table('users_preferences')->select('user_id','emsk')->get();
	    foreach($datas as $data){
		    $txt .= $data->emsk;
		    $length = strlen((string)$txt);
	    }
	    error_log("user data".print_r($length,true));
	    $txtname = 'msk.csv';
	    $headers = ['Content-type'=>'csv/plain', 'test'=>'YoYo', 'Content-Disposition'=>sprintf('attachment; filename="%s"', $txtname),'X-BooYAH'=>'WorkyWorky','Content-Length'=>$length];
	    return \Response::make($txt , 200, $headers );
    }

    public function getUsers(Request $request){
	
	$prefix = $request->input('q');
	$users = User::select('email','id')
                ->where('email', 'like', $prefix.'%')
		->limit(10)
                ->get();
	if($users==null){
		return Response::make([]);
	}
	/*$values=array();

	foreach($users as $key=>$value){

		array_push($values,$value['email']);
	}
	*/
	return Response::make(json_encode($users));
	
   }

   public function getSocialProviders()
    {
        $socialprovider = DB::table('social_logins')->select('id','user_id','provider')->get();
        return view('layouts.main')->with('social_logins', $socialprovider);
    }
}
