<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReplies extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('replies', function($newtable)
            {
                $newtable->increments('id');
                $newtable->integer('author_id');
                $newtable->text('body');
                
                $newtable->integer('post_id');
                $newtable->integer('post_author_id');
                $newtable->integer('post_author_read');
                
                $newtable->integer('quote_id');
                $newtable->integer('quote_author_id');
                $newtable->integer('quote_author_read');
                
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
            Schema::drop('replies');
	}

}
