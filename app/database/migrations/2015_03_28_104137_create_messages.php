<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessages extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('messages', function($newtable)
            {
                $newtable->increments('id');
                $newtable->integer('sender_id');
                $newtable->integer('receiver_id');
                $newtable->text('body');
                $newtable->integer('receiver_read');
                $newtable->timestamps();
            });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
           Schema::drop('messages');
	}

}
