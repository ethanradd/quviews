<?php

class Review extends Eloquent {
	/*
	 * ##Ed
	 * One to One relationship
	 * one item has one image
	 *
	 * Source: http://laravel.com/docs/4.2/eloquent#one-to-one
	 *
	 * */
	public function user()
	{
		return $this->hasOne('User');
	}
	
	/*
	 * ##Ed
	 * One to Many relationship
	 * one item has many review
	 * hence one review belongs to an item
	 *
	 * Source: http://laravel.com/docs/4.2/eloquent#one-to-many
	 *
	 * */
	public function item()
	{
	    return $this->belongsTo('Item');
	}
}

?>