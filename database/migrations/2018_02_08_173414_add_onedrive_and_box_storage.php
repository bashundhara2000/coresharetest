<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOnedriveAndBoxStorage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
	DB::statement("ALTER TABLE users_storage_auth CHANGE COLUMN type type ENUM('google', 'dropbox', 'box', 'onedrive')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
	DB::statement("ALTER TABLE users_storage_auth CHANGE COLUMN type type ENUM('google', 'dropbox')");
    }
}
