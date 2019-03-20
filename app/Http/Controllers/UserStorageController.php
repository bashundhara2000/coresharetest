<?php

namespace App\Http\Controllers;

use App\Models\UserStorageAuth;
use App\Models\UserPreferences;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\RedirectResponse;
use Auth;
use DB;
use SplFileInfo;

class UserStorageController extends Controller
{
    public function addStorage(Request $request)
    {
	$user_id = Auth::user()->id;

	$firstStorage = DB::table('users_preferences')->where('user_id', $user_id)->whereNotNull('preferred_storage')->get();	

	$displayName = $request->input('name');
	$type = $request->input('type');
	$isPreferred = $request->input('isPreferred');

		error_log(print_r($firstStorage,true));
	if($firstStorage == null){
		$isPreferred=true;
	}

	$storage = new UserStorageAuth;
	$storage->user_id=$user_id;
	$storage->display_name=$displayName;
	$storage->type=$type;
	$storage->save();
	
	//check for duplicate name ?

	if($isPreferred=='true'){
		error_log(print_r($isPreferred,true));
		//set this id as preferred storage in user pref
		$userpref = UserPreferences::firstOrNew(array('user_id' => $user_id ));
		//$userpref = UserPreferences::where('user_id',$user_id);
		$userpref->preferred_storage=$storage->id;
		$userpref->save();
	}
	$controller = StorageFactory::getStorageHandler($type);
	$authUrl =$controller->getAuthorizationURL($storage->id);//pass the id as state
	//return Redirect::away($authUrl);
	return Response::make($authUrl);

    }
       public function deleteFile(Request $request,$storageId,$fileId,$filePath){
                
                $currentuserid = Auth::user()->id;
	    	$storage = UserStorageAuth::select('auth_token','type','id')->where([['user_id','=',$currentuserid],['id','=',$storageId]])->get();
	    	if($storage==null){
		    return Response::make("ERROR : No permission");
		 }

	    	$storageType=$storage[0]->type;
		$controller = StorageFactory::getStorageHandler($storageType);
	
                        try {

				$controller->deleteFile($storage[0],$fileId,$filePath);

                            } catch (Exception $e) {

                                return "Error: " . $e->getMessage();

                            }
		
				return "OK";
                //return Redirect::to('/user');
 
	}
   
       public function downloadFile(Request $request,$storageId,$fileId,$filePath){
                
		$content="";
                $currentuserid = Auth::user()->id;
	    	$storage = UserStorageAuth::select('auth_token','type','id')->where([['user_id','=',$currentuserid],['id','=',$storageId]])->get();
	    	if($storage==null){
		    return Response::make("ERROR : No permission");
		 }

	    	$storageType=$storage[0]->type;
                $handle = fopen("/tmp/".$filePath, 'w') or die('Cannot create temp file:  '); //implicitly creates file
                $mimeType=mime_content_type("/tmp/".$filePath);
        
		$controller = StorageFactory::getStorageHandler($storageType);
		$content=$controller->downloadFile($storage[0],$fileId,$filePath);
	
		       
		 error_log("mime type is".print_r($mimeType,true));
                        return response($content)->header('Content-Type',$mimeType) ;
 
	}
 
    public function uploadFile(Request $request)
    {

	    $storage_id = $request->input('storage');
	    error_log("Stroage is ".$storage_id);
	    $user_id = Auth::user()->id;
	    $storage = UserStorageAuth::select('auth_token','type','id')->where([['user_id','=',$user_id],['id','=',$storage_id]])->get();
	    if($storage==null){
		    return Response::make("ERROR : No permission");
	    }
	    $uploadStorage=$storage[0]->type;
	    $tmp_file = $request->file;
	    $destinationPath = 'uploads';
	    $fileName=$tmp_file->getClientOriginalName();
	    $mimeType=$tmp_file->getMimeType();
	    $tmp_file->move(base_path().'/uploads',$fileName);
	    $file = fopen(base_path().'/uploads/'.$fileName, "r") or die("Unable to open file!");
	    $file_info = new SplFileInfo(base_path().'/uploads/'.$fileName);

	    error_log("Stroage is ".print_r($file_info,true));
	    $content = file_get_contents($file_info->getRealPath());

	    $user_credentials =$storage[0]->auth_token;


	$controller = StorageFactory::getStorageHandler($uploadStorage);
	$fileId=$controller->uploadFile($content,$mimeType,$file_info,$storage[0],$user_id);
	
	return Response::make($fileId);
    }
    
    public function getStorageFiles(Request $request,$id){

	$user_id = Auth::user()->id;
	error_log("User id is ".$user_id);
	$storage = UserStorageAuth::select('display_name','type','id')->where([['user_id','=',$user_id],['id','=',$id]])->get();
	if($storage==null){
		return Response::make("ERROR : No permission");
	}
	error_log(print_r($storage[0]->display_name,true));
	$controller = StorageFactory::getStorageHandler($storage[0]->type);
	$files=$controller->listFiles($storage[0]->id);
	return Response::make(json_encode($files));

    }

    public function linkStorage(Request $request,$type){

	$code = $request->input('code');
	$state = $request->input('state');
	$user_id = Auth::user()->id;

	$controller = StorageFactory::getStorageHandler($type);
	
	return $controller->storeAuthCredentials($user_id,$code,$state);
    }
    
    public function getStorage(Request $request,$id){

	$user_id = Auth::user()->id;
	error_log("User id is ".$user_id);
	$storage = UserStorageAuth::select('display_name','type','id')->where([['user_id','=',$user_id],['id','=',$id]])->get();
	return Response::make(json_encode($storage));

    }

    public function getStorages(Request $request){

	$user_id = Auth::user()->id;
	error_log("User id is ".$user_id);
	$storages = DB::table('users_storage_auth as a')
		   	->join('users_preferences as p', 'a.user_id', '=', 'p.user_id')
		   	->select('a.display_name', 'a.type', 'a.id' ,DB::raw('(CASE WHEN a.id =p.preferred_storage  THEN 1 ELSE 0 END) AS preferred'))
			->where('a.user_id', '=', $user_id)
            	   	->get();
	return Response::make(json_encode($storages));

    }
    public function removeStorage(Request $request,$id){

	$user_id = Auth::user()->id;
	$userpref = UserPreferences::firstOrNew(array('user_id' => $user_id ));
	if($userpref->preferred_storage==$id){

		return Response::make("FAILED");
	}
	$displayName = $request->input('display_name');
	$storage = UserStorageAuth::where('user_id', $user_id)->where('id',$id);
	if(null == $storage){
		return Response::make("FAILED");
	 }
	$storage->delete();	
	
	return Response::make("OK");
    }
   
    public function updatePreferredStorage(Request $request,$id){

	$user_id = Auth::user()->id;
		$userpref = UserPreferences::firstOrNew(array('user_id' => $user_id ));
		$userpref->preferred_storage=$id;
		$userpref->save();
	return Response::make("success");

    }
    
   public function updateStorage(Request $request,$id){

	$displayName = $request->input('display_name');
	$user_id = Auth::user()->id;
	$storage = UserStorageAuth::where('user_id', $user_id)->where('id',$id)->update(['display_name'=>$displayName]);
	//$storage->save();
	
 	$storage = UserStorageAuth::select('display_name','type','id')->where('id', $id)->get();
	return Response::make(json_encode($storage));

    }

}
