<?php

class Friend extends Eloquent {
	
	// ##Ed this relationship is a bit tricky to define
	// One user can have many friends (users)
	// But each friendship is unique
	
	
	/*
	 * ##Ed
	 * One to One relationship
	 * one item has one image
	 *
	 * Source: http://laravel.com/docs/4.2/eloquent#one-to-one
	 *
	 * */
	
	/*
	public function user()
	{
		return $this->hasOne('User');
	}
	*/
}

?>