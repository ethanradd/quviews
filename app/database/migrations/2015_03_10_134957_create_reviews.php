<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReviews extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('reviews', function($newtable)
            {
                $newtable->increments('id');
                $newtable->integer('item_id');
                $newtable->integer('author_id');
                $newtable->text('body');
                $newtable->string('rank', 50);
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
            Schema::drop('reviews');
	}

}
