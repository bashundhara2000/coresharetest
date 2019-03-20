@extends('layouts.main')

@section('head')

@stop

@section('content')
<?php
use Illuminate\Http\RedirectResponse;
use App\Models\UserStorageAuth;
use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\Dropbox;
use AdammBalogh\Box\ViewClient;
use AdammBalogh\Box\Client\View\ApiClient;
use AdammBalogh\Box\Client\View\UploadClient;
use AdammBalogh\Box\Command\View;
use AdammBalogh\Box\Factory\ResponseFactory;
use AdammBalogh\Box\GuzzleHttp\Message\SuccessResponse;
use AdammBalogh\Box\GuzzleHttp\Message\ErrorResponse;
use AdammBalogh\Box\Client\OAuthClient;
use AdammBalogh\KeyValueStore\KeyValueStore;
use AdammBalogh\KeyValueStore\Adapter\NullAdapter;
use AdammBalogh\Box\Exception\ExitException;
use AdammBalogh\Box\Exception\OAuthException;
use GuzzleHttp\Exception\ClientException;
use Krizalys\Onedrive\Client as OneDriveClient;
use App\Providers\BoxAPI as BoxAPI;
use League\OAuth2\Client\Token\AccessToken;
use Stevenmaguire\OAuth2\Client\Provider\Box;



//if(Auth::user() == null ){

	//error_log("User not logged in");
        //return view('panels.user.home');
//}


$currentuserid = Auth::user()->id;
?>
<section id="mystorages" class="section-bg section-nh wow fadeInUp" style="visibility: visible; animation-name: fadeInUp;">

        <div class="section-header">
          <h3>My Storages</h3>
        </div>

 <div class="storageContainer">
 <div class="context-menu"> <ul> <li data-id="download"><i class="fa fa-download" aria-hidden="true"></i>&nbsp;<span>Download</span></li> <li data-id="share" data-toggle="modal" data-target="#share" data-backdrop="static" data-keyboard="false"><i class="fa fa-share" aria-hidden="true"></i>&nbsp;<span>Share</span></li> <li data-id="delete"><i class="fa fa-trash" aria-hidden="true"></i>&nbsp;<span>Delete</span></li> </ul> </div> <input type="hidden" value="" id="txt_id">
 <div class="row">
    <!--div  class="col-xs-12 sub-container">
       <div class="col-xs-6 col-sm-3">
         <p>{{Auth::user()->first_name . " " . Auth::user()->last_name}}</p>
       </div>
       <div class="col-xs-6 col-sm-9">
       </div>
       </div-->
    <div class="drivecontainer container-fluid">
     <div class="col-xs-12">
       <ul class="nav nav-tabs">
        <li class="active storagenavtabli" id="google"><a data-toggle="tab" href="#googletab"><img src="/assets/images/google_drive-active.png" class="img img-responsive imagegoogle"><span class="spanstorage">   Google</span></a>
        </li>
        <li class="storagenavtabli" id="dropbox"><a data-toggle="tab" href="#dropboxtab"><img src="/assets/images/coreshare_dropbox-active.png" class="img img-responsive imagegoogle"><span class="spanstorage"> Dropbox</span></a>
        </li>
        <li class="storagenavtabli" id="box"><a data-toggle="tab" href="#boxtab"><img src="/assets/images/coreshare_box-active.png" class="img img-responsive imagegoogle"><span class="spanstorage"> Box</span></a>
        </li>
        <li class="storagenavtabli"  id="onedrive"><a data-toggle="tab" href="#onedrivetab"><img src="/assets/images/coreshare_onedrive-active.png" class="img img-responsive imagegoogle"><span class="spanstorage"> Onedrive</span></a>
        </li>
        <li class="storagenavtabliright pull-right">
         <a class="btn btn-primary btn-md " id="uploadBtn" data-toggle="modal" data-target="#upload" data-backdrop="static" data-keyboard="false">
            <span class="glyphicon glyphicon-cloud-upload"></span> Upload
         </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="googletab tab-pane  active in" id="googletab">
          <div id="navtab-google" class="col-xs-12 col-sm-3">
          </div>
          <div id="navtab-content-google" class="navtab-content-google col-xs-12 col-sm-9">
          </div>
        </div>
        <div class="dropboxtab tab-pane in" id="dropboxtab">
          <div id="navtab-dropbox" class="col-xs-12 col-sm-3">
          </div>
          <div id="navtab-content-dropbox" class="navtab-content-dropbox col-xs-12 col-sm-9">
          </div>
        </div>
        <div class="boxtab tab-pane in" id="boxtab">
          <div id="navtab-box" class="col-xs-12 col-sm-3">
          </div>
          <div id="navtab-content-box" class="navtab-content-box col-xs-12 col-sm-9">
          </div>
        </div>
        <div class="onedrivetab tab-pane in" id="onedrivetab">
          <div id="navtab-onedrive" class="col-xs-12 col-sm-3">
          </div>
          <div id="navtab-content-onedrive" class="navtab-content-onedrive col-xs-12 col-sm-9">
          </div>
        </div>

        </div>
       </div>
      </div>
    </div>
  </div>
 </div>

</section>
		@stop
