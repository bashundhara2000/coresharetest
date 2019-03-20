<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

$s = 'public.';
//Route::match(['get','post'], '/layouts/main','GoogleDriveStorageController@listFiles');
//Route::get('/',         ['as' => $s . 'home',   'uses' => 'UserController@getHome'])->middleware('auth:user');
Route::get('/',         ['as' => $s . 'home',   'uses' => 'UserController@getHome']);
Route::get('/contactus',         ['as' => $s . 'contact',   'uses' => 'UserController@getContact']);
Route::get('/home', function () {
    return redirect('/');
});
Route::get('/existingstorage',         ['as' => $s . 'existstorage',   'uses' => 'UserController@getExiststorage'])->middleware('auth:user');
Route::get('/sharemessage',         ['as' => $s . 'sharemessage',   'uses' => 'UserController@getSharemessage'])->middleware('auth:user');
Route::get('/settings',         ['as' => $s . 'settings',   'uses' => 'UserController@getUserprofiles'])->middleware('auth:user');
Route::get('/images/{filename}', function ($filename)
{
    return Image::make(storage_path('public/images/' . $filename))->response();
});


$s = 'social.';
Route::get('/social/redirect/{provider}',   ['as' => $s . 'redirect',   'uses' => 'Auth\SocialController@getSocialRedirect']);
Route::get('/social/handle/{provider}',     ['as' => $s . 'handle',     'uses' => 'Auth\SocialController@getSocialHandle']);
Route::get ( '/redirect/{service}', 'SocialAuthController@redirect' );
Route::get ( '/callback/{service}', 'SocialAuthController@callback' );

Route::get('/{type}/handle/oauth',     ['as' => 'drive' . 'handle',     'uses' => 'UserStorageController@linkStorage']);
Route::get('/{type}/link/oauth',     ['as' => 'drive' . 'handle',     'uses' => 'UserStorageController@addStorage']);
/*
Route::get('/drive/link/oauth',     ['as' => 'drive' . 'handle',     'uses' => 'DriveController@link']);
Route::get('/drive/handle/oauth',     ['as' => 'drive' . 'handle',     'uses' => 'UserStorageController@linkStorage']);
Route::get('/dropbox/handle/oauth',     ['as' => 'drive' . 'handle',     'uses' => 'DropBoxController@store']);
Route::get('/dropbox/link/oauth',     ['as' => 'drive' . 'handle',     'uses' => 'DropBoxController@link']);
Route::get('/box/handle/oauth',     ['as' => 'drive' . 'handle',     'uses' => 'BoxController@store']);
Route::get('/box/link/oauth',     ['as' => 'drive' . 'handle',     'uses' => 'BoxController@link']);
Route::get('/onedrive/handle/oauth',     ['as' => 'drive' . 'handle',     'uses' => 'OneDriveController@store']);
Route::get('/onedrive/link/oauth',     ['as' => 'drive' . 'handle',     'uses' => 'OneDriveController@link']);
*/
//Route::get('/uploadfile','UploadFileController@index');
//Route::post('/uploadfile','UploadFileController@showUploadFile');
Route::post('/uploadfile',     ['as' => 'drive' . 'handle',     'uses' => 'UserStorageController@uploadFile']);
Route::get('/sharefile','ShareFileController@shareFile');
Route::post('/sharefile','ShareFileController@shareFile');
Route::get('/showfile/{storageId}/{fileId}/{filePath}','UserStorageController@downloadFile');
Route::get('/deletefile/{storageId}/{fileId}/{filePath}','UserStorageController@deleteFile');
Route::get('/deleteaccount','UserController@deleteAccount');
Route::get('/downloadmasterkey','UserController@downloadMsk');
//Route::get('/showfile/{fileId}','UserStorageController@downloadFile');
//Route::get('/deletefile/{fileId}','DeleteController@deleteFile');


Route::group(['prefix' => 'admin', 'middleware' => 'auth:administrator'], function()
{
    $a = 'admin.';
    Route::get('/', ['as' => $a . 'home', 'uses' => 'AdminController@getHome']);
    Route::get('/home', function () {
    return redirect('/'); });

});

Route::group(['prefix' => 'user', 'middleware' => 'auth:user'], function()
{
    $a = 'user.';
    Route::get('/', ['as' => $a . 'home', 'uses' => 'UserController@getStorage']);
    Route::get('/home', function () {
        return redirect('/');
    });
    Route::group(['middleware' => 'activated'], function ()
    {
        $m = 'activated.';
        Route::get('protected', ['as' => $m . 'protected', 'uses' => 'UserController@getProtected']);
    });
    Route::get('/getUsers',     ['as' => 'userkey',     'uses' => 'UserController@getUsers']);
    Route::get('/getUsersPK/{id}',     ['as' => 'userkey',     'uses' => 'KeyStoreController@getOtherUsersPK']);
    Route::get('/EMSK',     ['as' => 'userkey',     'uses' => 'KeyStoreController@getEMSK']);
    Route::put('/EMSK',     ['as' => 'userkey',     'uses' => 'KeyStoreController@addEMSK']);
    Route::get('/publicKey',     ['as' => 'userkey',     'uses' => 'KeyStoreController@getPublicKey']);
    Route::put('/publicKey',     ['as' => 'userkey',     'uses' => 'KeyStoreController@addPublicKey']);
    Route::get('/shares',     ['as' => 'userstorage',     'uses' => 'ShareFileController@getShares']);
    Route::put('/shares/{id}/{status}',     ['as' => 'userstorage',     'uses' => 'ShareFileController@updateShares']);
    Route::get('/storage',     ['as' => 'userstorage',     'uses' => 'UserStorageController@getStorages']);
    Route::get('/storage/{id}',     ['as' => 'userstorage',     'uses' => 'UserStorageController@getStorage']);
    Route::get('/storage/{id}/files',     ['as' => 'userstorage',     'uses' => 'UserStorageController@getStorageFiles']);
    Route::post('/storage',     ['as' => 'userstorage',     'uses' => 'UserStorageController@addStorage']);
    Route::put('/storage/{id}', ['as' => 'userstorage', 'uses' => 'UserStorageController@updateStorage']);
    Route::delete('/storage/{id}', ['as' => 'userstorage', 'uses' => 'UserStorageController@removeStorage']);
    Route::put('/preferred/{id}', ['as' => 'userstorage', 'uses' => 'UserStorageController@updatePreferredStorage']);

});

Route::group(['middleware' => 'auth:all'], function()
{
    $a = 'authenticated.';
   // Route::get('/',         ['as' => 'userhome',   'uses' => 'UserController@getHome']);
    Route::get('/logout', ['as' => $a . 'logout', 'uses' => 'Auth\LoginController@logout']);
    Route::get('/activate/{token}', ['as' => $a . 'activate', 'uses' => 'ActivateController@activate']);
    Route::get('/activate', ['as' => $a . 'activation-resend', 'uses' => 'ActivateController@resend']);
    Route::get('not-activated', ['as' => 'not-activated', 'uses' => function () {
        return view('errors.not-activated');
    }]);
});
Auth::routes(['login' => 'auth.login']);
//Auth::routes(['register' => 'auth.register']);
