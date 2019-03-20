<?php

namespace App\Http\Controllers;

use \App\Http\Controllers\GoogleDriveStorageController;
use \App\Http\Controllers\DropBoxStorageController;
use \App\Http\Controllers\BoxStorageController;
use \App\Http\Controllers\OneDriveStorageController;

class StorageFactory extends AbstractStorageFactory{



	public static function getStorageHandler($type){


		$controller = NULL;

		switch ($type) {
			case "google":
				$controller= new GoogleDriveStorageController;	
			break;		
			case "dropbox":
				$controller= new DropBoxStorageController;	
			break;		
			case "onedrive":
				$controller= new OneDriveStorageController;	
			break;		
			case "box":
				$controller= new BoxStorageController;	
			break;		
			default:
				$controller= new GoogleDriveStorageController;
			break;	
		}

		return $controller;
	}



}


?>
