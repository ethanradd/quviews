<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePosts extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('posts', function($newtable)
            {
                $newtable->increments('id');
                $newtable->integer('item_id');
                $newtable->integer('author_id');
                $newtable->text('body');
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
           Schema::drop('posts');
	}

}
