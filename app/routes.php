<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

/*

// Default home link

Route::get('/', function()
{
	return View::make('hello');
});

*/

/* -- [INDEX] -------------------------------------------------------------------------- */

Route::get('/', function()
{
	// index should redirect to home
	return Redirect::to('/home');
});

/* -- [HOME] -------------------------------------------------------------------------- */

Route::get('/home', function()
{
	// Get latest Items to display on home page
	// We only get 'locked' items meaning they have been approved by admin
        $items = DB::table('categories')
                        ->join('items', 'categories.id', '=', 'items.category_id')
                        ->select('items.id', 'items.name', 'items.year', 'items.creator', 'items.image', 'items.locked', 'categories.name as category_name', 'categories.image as category_image')
						->where('items.locked', '=', "locked")
                        ->orderBy('items.updated_at', 'DESC')->take(8)->get();
						
	// Get Topics to display on home page
        $topics = DB::table('categories')
                        ->join('items', 'categories.id', '=', 'items.category_id')
                        ->select('items.id', 'items.name', 'items.year', 'items.creator', 'items.image', 'items.locked', 'categories.name as category_name', 'categories.image as category_image')
						->where('items.locked', '=', "topic")
                        ->orderBy('items.created_at', 'DESC')->get();
			
        // get all the channels
        $channels = Channel::all();
	
	$data['header'] = "Home";
	$data['title'] = "QuViews - Home - Your Quick Reviews";
	$data['items'] = $items;
	$data['channels'] = $channels;
	$data['topics'] = $topics;
	
	return View::make('home', $data);
});

/* -- [ADD-ITEMS "HOME" PAGE] -------------------------------------------------------------------------- */

// ##Ed this is needed to pass item name through a check
// In the next page after submit a messag is attched whether item matches anything in database

Route::get('/add-item', array(
	'before' => 'auth',
	function()
{
	
	$data['header'] = "Add Item";
	$data['title'] = "QuViews - Add Item - Your Quick Reviews";
	
	return View::make('add-item', $data);
}
));


/* -- [REGISTER] -------------------------------------------------------------------------- */

Route::get('/register', 'RegisterController@showRegister');
Route::post('/register', 'RegisterController@doRegister');

// email verification
Route::any('/verify/{verification_code}/{user_email}', 'RegisterController@doVerify');

/* -- [PASSWORD-RESET] -------------------------------------------------------------------------- */
// Form to request unique key for password reset email link
Route::get('/password-reset', 'RegisterController@showPasswordReset');
Route::post('/password-reset', 'RegisterController@doPasswordReset');

// Form where we enter new password
Route::get('/password-reset-now/{verification_code}/{user_email}', 'RegisterController@showPasswordResetNow');
Route::post('/password-reset-now', 'RegisterController@doPasswordResetNow');

/* -- [LOG IN] -------------------------------------------------------------------------- */

Route::get('/login', function()
{
    if(Auth::check()) {
		// redirect
         return Redirect::to('/home')->with('message', 'You are already logged in!');
    }
	
	$data['header'] = "Log In";
	$data['title'] = "QuViews - Log In - Your Quick Reviews";

	return View::make('login', $data);
});

Route::post('/login', function()
{
	//$credentials = Input::only('username', 'password');
	//if (Auth::attempt($credentials)) {

	$rules = array(
		'username' => 'required',
		'password' => 'required'
	);
	
	$validator = Validator::make(Input::all(), $rules);
	
	// process the form
	if ($validator->fails()) {
	    return Redirect::to('/login')
		->withErrors($validator)
		->withInput(Input::except('password'));
	} else {
	
	$username = Input::get('username');
	$password = Input::get('password');
	
	// Did user select 'remember me' ?
	$remember = (Input::has('remember')) ? true : false;
	
	if (Auth::attempt(array('username' => $username, 'password' => $password))) {
		// If the account exists
		// Check if the account has been validated (member)
		if (Auth::attempt(array('username' => $username, 'password' => $password, 'role' => "user"), $remember)) {
			return Redirect::intended('/');
		// check if account is user without a profile
		} elseif (Auth::attempt(array('username' => $username, 'password' => $password, 'role' => "user_no_profile"), $remember)) {
			return Redirect::to('profiles/create');
		// check if account is admin
		} elseif (Auth::attempt(array('username' => $username, 'password' => $password, 'role' => "admin"), $remember)) {
			return Redirect::intended('/');
		// check if account is BANNED
		} elseif (Auth::attempt(array('username' => $username, 'password' => $password, 'role' => "banned"), $remember)) {
			Auth::logout();
			return Redirect::to('home')->with('message_danger', 'Sorry, your account was BANNED!');
		// check if account is DELETED
		} elseif (Auth::attempt(array('username' => $username, 'password' => $password, 'role' => "deleted"), $remember)) {
			Auth::logout();
			return Redirect::to('home')->with('message_danger', 'Sorry, that account was DELETED!');
		} else {
		// Account exists but isn't validated
		Auth::logout();
		return Redirect::to('login')->with('message', 'Please, first validate your account through the link sent to your email address. <br /><br /> (Check your junk mail folder if you can not see the email)');
		}
	}
	
	// Account doesn't exist at all
	return Redirect::to('login')->with('message', 'Sorry, Password / Username is incorrect :(');
}
});

/* -- [LOG OUT] -------------------------------------------------------------------------- */

Route::get('/logout', function()
{
	$data['header'] = "Logged Out";
	$data['title'] = "QuViews - Log Out - Your Quick Reviews";

	Auth::logout();
	return View::make('logout', $data);
});

/* -- [ABOUT PAGE] -------------------------------------------------------------------------- */

Route::get('/about', array(
	function()
{
	$data['header'] = "About QuViews";
	$data['title'] = "QuViews - About - Your Quick Reviews";

	return View::make('about', $data);
}

));

/* -- [CATEGORIES] -------------------------------------------------------------------------- */

// Using route::resource for Categories
Route::resource('categories', 'CategoryController');

/* -- [ITEMS] -------------------------------------------------------------------------- */

// Using route::resource for Items
Route::resource('items', 'ItemController');

// ##Ed note we used any instead of post, this is because the page implements pagination
// we both post to this page and get from this page during pagination
Route::any('/search-item', 'ItemController@doSearchitem');

Route::any('/check-item', 'ItemController@doCheckitem');

// Search for random item
Route::get('/random-item', 'ItemController@doRandomitem');

// Make Item favorite
Route::get('/favorite-item/{item_id}', 'ItemController@favoriteItem');

// Undo Item favorite
Route::get('/unfavorite-item', 'ItemController@unFavoriteItem');

// Remove item image
Route::get('items/remove-item-image/{item_id}', array('as' => 'remove_item_image', 'uses' => 'ItemController@removeItemImage'));

/* -- [POSTS] -------------------------------------------------------------------------- */

// Using route:resource for Posts
Route::resource('posts', 'PostController');

// ##Ed Needed a new route for create because we pass $item_id
// Source: http://stackoverflow.com/questions/20730582/passing-argument-from-view-of-one-resource-to-create-method-of-another-with-lara
Route::get('posts/create/{item_id}', array('as' => 'createpost', 'uses' => 'PostController@create'));

Route::get('posts/feed/{item_id}', array('as' => 'createfeed', 'uses' => 'PostController@feed'));

/* -- [REPLIES] -------------------------------------------------------------------------- */

// Using route:resource for Replies
Route::resource('replies', 'ReplyController');

// ##Ed Needed a new route for create because we pass $post_id
// Source: http://stackoverflow.com/questions/20730582/passing-argument-from-view-of-one-resource-to-create-method-of-another-with-lara
// Note, the second optional parameter {quote_id?}
// Source: http://stackoverflow.com/questions/22877725/pass-many-optional-parameters-to-route-in-laravel-4
// Source 2: http://stackoverflow.com/questions/18846688/laravel-4-optional-parameter
Route::get('replies/create/{post_id}/{quote_id?}', array('as' => 'createreply', 'uses' => 'ReplyController@create'));


/* -- [REVIEWS] -------------------------------------------------------------------------- */

// Using route:resource for Reviews
Route::resource('reviews', 'ReviewController');

// ##Ed Needed a new route for create because we pass $item_id
// Source: http://stackoverflow.com/questions/20730582/passing-argument-from-view-of-one-resource-to-create-method-of-another-with-lara
Route::get('reviews/create/{item_id}', array('as' => 'createreview', 'uses' => 'ReviewController@create'));

Route::get('reviews/feed/{item_id}', array('as' => 'createfeed', 'uses' => 'ReviewController@feed'));

/* -- [CHANNELS] -------------------------------------------------------------------------- */

Route::resource('channels', 'ChannelController');

/* -- [PROFILES] -------------------------------------------------------------------------- */

// Using route::resource for Profiles
Route::resource('profiles', 'ProfileController');

// User profile reviews feed
Route::get('profiles/reviews-feed/{profile_id}', array('as' => 'create_reviews_feed', 'uses' => 'ProfileController@reviewsFeed'));

// User profile posts feed
Route::get('profiles/posts-feed/{profile_id}', array('as' => 'create_posts_feed', 'uses' => 'ProfileController@postsFeed'));

// User profile replies feed
Route::get('profiles/replies-feed/{profile_id}', array('as' => 'create_replies_feed', 'uses' => 'ProfileController@repliesFeed'));

// User profile replies feed
Route::get('profiles/notifications-feed/{profile_id}', array('as' => 'create_notifications_feed', 'uses' => 'ProfileController@notificationsFeed'));

// User profile conversation history feed
Route::get('profiles/conversation-history-feed/{profile_id}', array('as' => 'create_conversation_history_feed', 'uses' => 'ProfileController@conversationHistoryFeed'));

// Remove profile image
Route::get('profiles/remove-profile-image/{profile_id}', array('as' => 'remove_profile_image', 'uses' => 'ProfileController@removeProfileImage'));

/* -- [FILTERS] -------------------------------------------------------------------------- */

// Custom filters

// Allow only admin
// Source: http://stackoverflow.com/questions/25964094/laravel-4-filter-group-routes-for-different-roles
// ##Ed Don't show message "Only Admin Allowed in production code"
Route::filter('allow_only_admin', function()
{
    if (Auth::user()->role != "admin" ){
	Session::flash('message', 'Oops! Wrong turn :(');
        return Redirect::guest('home');
    }
});

Route::filter('require_profile', function()
{
	if (Auth::user()->role == "user_no_profile") {
	    Session::flash('message', 'Please setup your profile before proceeding');
            return Redirect::guest('profiles/create');
	}
});

// Logout banned users, and deleted users
Route::filter('kick_banned', function()
{
	if ((Auth::check()) && (Auth::user()->role == "banned")) {
		Auth::logout();
		return Redirect::to('home')->with('message_danger', 'Sorry, your account was BANNED!');
	} elseif ((Auth::check()) && (Auth::user()->role == "deleted")) {
		Auth::logout();
		return Redirect::to('home')->with('message_danger', 'Sorry, that account was DELETED!');
	}
});

/* -- [HOME FEED] ----------------------------------------------------------------------------------- */

Route::get('feed/{item_type}/{item_feed_type}', array('as' => 'itemfeed', 'uses' => 'ItemController@feed'));

/* -- [FRIENDS] ----------------------------------------------------------------------------------- */
// Add Friend
Route::get('add_friend/{friend_id}', array('as' => 'addfriend', 'uses' => 'FriendController@add_friend'));

// Remove Friend
Route::get('remove_friend/{friend_id}', array('as' => 'removefriend', 'uses' => 'FriendController@remove_friend'));

// Following List
Route::get('profiles/following-list/{profile_id}', array('as' => 'createfollowinglist', 'uses' => 'FriendController@listFollowing'));

// Followers List
Route::get('profiles/followers-list/{profile_id}', array('as' => 'createfollowerslist', 'uses' => 'FriendController@listFollowers'));

// Following Feed
Route::get('following-feed', array('as' => 'createfollowingfeed', 'uses' => 'FriendController@showFollowingFeed'));

// Following Reviews Feed
Route::get('following-reviews-feed', array('as' => 'createfollowingreviewsfeed', 'uses' => 'FriendController@showFollowingReviewsFeed'));

// Following Posts Feed
Route::get('following-posts-feed', array('as' => 'createfollowingpostsfeed', 'uses' => 'FriendController@showFollowingPostsFeed'));

// Following Replies Feed
Route::get('following-replies-feed', array('as' => 'createfollowingrepliesfeed', 'uses' => 'FriendController@showFollowingRepliesFeed'));

/* -- [REPORTS] -------------------------------------------------------------------------- */

// Using route::resource for Reports
Route::resource('reports', 'ReportController');

// ##Ed a mew link for create because we pass ids
Route::get('reports/create/{item_id}/{item_type}', array('as' => 'createreport', 'uses' => 'ReportController@create'));

/* -- [MESSAGES] -------------------------------------------------------------------------- */

// Using route::resource for Messages
Route::resource('messages', 'MessageController');

// ##Ed a mew link for create because we pass ids
Route::get('messages/create/{receiver_id}', array('as' => 'createmessage', 'uses' => 'MessageController@create'));

// Messages List
Route::get('messages-list', array('as' => 'createmessageslist', 'uses' => 'MessageController@listMessages'));

// Messages List Outbox
Route::get('messages-list-outbox', array('as' => 'createmessageslistoutbox', 'uses' => 'MessageController@listMessagesOutbox'));

/* -- [ BOSS ] -------------------------------------------------------------------------- */

Route::get('boss/', function()
{
	// index should redirect to home
	return Redirect::to('/boss/home');
});

Route::get('/boss/home', function()
{
	// Allow only admin
    if (Auth::user()->role != "admin" ){
	Session::flash('message', 'Oops! Wrong turn :(');
        return Redirect::guest('home');
    }
	
	$data['header'] = "BOSS Home";
	$data['title'] = "QuViews - BOSS";
	
	return View::make('/boss/home', $data);
});

Route::get('/boss/change-user-role/{user_id}', array('as' => 'change-user-role', 'uses' => 'ProfileController@showChangeUserRole'));

Route::any('/boss/user-statistics', array('as' => 'user-statistics', 'uses' => 'ProfileController@userStatistics'));

Route::any('/boss/content-statistics', array('as' => 'content-statistics', 'uses' => 'ProfileController@contentStatistics'));

Route::any('/boss/activity-statistics', array('as' => 'activity-statistics', 'uses' => 'ProfileController@activityStatistics'));

Route::any('/ban-user/{user_id}', array('as' => 'ban-user', 'uses' => 'ProfileController@banUser'));

Route::any('/delete-user/{user_id}', array('as' => 'delete-user', 'uses' => 'ProfileController@deleteUser'));

Route::any('/revive-user/{user_id}', array('as' => 'revive-user', 'uses' => 'ProfileController@reviveUser'));

Route::any('/promote-user/{user_id}', array('as' => 'promote-user', 'uses' => 'ProfileController@promoteUser'));

