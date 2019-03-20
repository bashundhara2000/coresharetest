<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersDriveAuth extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
	Schema::create('users_drive_auth', function (Blueprint $table) {
	    $table->increments('id');
	    $table->integer('user_id')->unsigned();
	    $table->text('auth_token');
            $table->timestamps();
	    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users_drive_auth');
    }
}
