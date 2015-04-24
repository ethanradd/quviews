<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReports extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('reports', function($newtable)
            {
                $newtable->increments('id');
                $newtable->integer('author_id');
                $newtable->integer('reported_user_id');
                $newtable->text('reason');
                $newtable->integer('item_id');
                $newtable->string('item_type', 100);
                $newtable->integer('resolved');
				$newtable->text('action');
                $newtable->integer('admin_id');
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
            Schema::drop('reports');
	}

}
