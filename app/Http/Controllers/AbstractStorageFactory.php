<?php

namespace App\Http\Controllers;

abstract class AbstractStorageFactory {

	abstract static function getStorageHandler($type);
}


?>
