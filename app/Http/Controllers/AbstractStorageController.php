<?php

namespace App\Http\Controllers;


abstract class AbstractStorageController{

	abstract function getAuthorizationURL($state);
	abstract function storeAuthCredentials($userid,$token,$state);
	abstract function uploadFile($content,$mimeType,$file_info,$storage,$userid);
	abstract function downloadFile($storage,$fileId,$filePath);
	abstract function deleteFile($storage,$fileId,$filePath);
        abstract function listFiles($userid,$limit=10,$page=0);	
}


?>
