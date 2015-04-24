<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFriends extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('friends', function($newtable)
            {
                $newtable->increments('id');
                $newtable->integer('user_id');
                $newtable->integer('friend_id');
                $newtable->integer('friend_notified');
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
            Schema::drop('friends');
	}

}
