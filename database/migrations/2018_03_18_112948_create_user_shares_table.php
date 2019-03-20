<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserSharesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_shares', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
	    $table->softDeletes();	
            $table->integer('sharedBy');
            $table->integer('sharedTo');
            $table->enum('status',['waiting','processing','complete','accepted','rejected','error']);
            $table->text('reKey');
            $table->text('fileData');
	    $table->index('sharedBy');
	    $table->index('sharedTo');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_shares');
    }
}
