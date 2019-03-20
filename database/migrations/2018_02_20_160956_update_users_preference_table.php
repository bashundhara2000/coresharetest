<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersPreferenceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
	    Schema::table('users_preferences', function ($table) {
			    $table->integer('preferred_storage')->unsigned()->nullable()->change();
			    //$table->index('preferred_storage');
			    //$table->foreign('preferred_storage')->references('id')->on('users_storage_auth');
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
    }
}
