<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfiles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('profiles', function($newtable)
            {
                $newtable->increments('id');
                $newtable->integer('user_id');
                $newtable->string('first_name', 100);
                $newtable->string('last_name', 100);
                $newtable->string('gender', 50);
                $newtable->date('birthday');
                $newtable->string('country', 50);
                $newtable->text('about');
                $newtable->string('image', 100);
                $newtable->integer('favorite_item_id');
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
            Schema::drop('profiles');
	}

}
