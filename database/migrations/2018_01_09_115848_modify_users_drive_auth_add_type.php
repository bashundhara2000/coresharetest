<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyUsersDriveAuthAddType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
	    Schema::rename("users_drive_auth", "users_storage_auth");
	    Schema::table('users_storage_auth', function (Blueprint $table) {
			    $table->enum('type',['google','dropbox']);
			    $table->string('display_name');
			 });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
	Schema::table('users_storage_auth', function (Blueprint $table) {
			    $table->dropColumn('type');
			    $table->dropColumn('display_name');
			    });
	Schema::rename("users_storage_auth", "users_drive_auth");
	
    }
}
