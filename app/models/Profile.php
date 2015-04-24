<?php

class Profile extends Eloquent {
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
}

?>