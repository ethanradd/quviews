<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItems extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('items', function($newtable)
            {
                $newtable->increments('id');
                $newtable->integer('category_id');
                $newtable->string('name', 100);
                $newtable->string('creator', 100);
                $newtable->smallInteger('year');
                $newtable->integer('last_editor_id');
                $newtable->string('locked', 50);
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
            Schema::drop('items');
	}

}
