<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChannels extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('channels', function($newtable)
            {
                $newtable->increments('id');
                $newtable->integer('category_id');
                $newtable->string('name', 100);
                $newtable->text('source');
                $newtable->text('description');
                $newtable->string('country', 50);
                $newtable->string('live', 50);
                $newtable->string('image', 100);
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
            Schema::drop('channels');
	}

}
