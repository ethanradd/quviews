<?php

class Item extends Eloquent {
	
	/*
	 * ##Ed
	 * One to One relationship
	 * one item has one image
	 *
	 * Source: http://laravel.com/docs/4.2/eloquent#one-to-one
	 *
	 * */
	public function image()
	{
		return $this->hasOne('Image');
	}
	
	/*
	 * ##Ed
	 * One to Many relationship
	 * one item has many reviews 
	 *
	 * Source: http://laravel.com/docs/4.2/eloquent#one-to-many
	 *
	 * */
	public function reviews()
	{
		return $this->hasMany('Review');
	
		// ##Ed Optional: To order by created_at DESC directly from model
		// return $this->hasMany('Review')->orderBy('created_at', 'desc');
	}
	
	public function posts()
	{
		return $this->hasMany('Post');
	}
}

?>