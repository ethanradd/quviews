<?php

class FriendController extends \BaseController {
    
        // Filters
        public function __construct()
        {
            // pages that don't require log in to access
            $this->beforeFilter('auth', array('except' => array()));
            
            // pages only admin can access
            // $this->beforeFilter('allow_only_admin', array('only' => 'index'));
            
            // pages that don't require profile to access
            $this->beforeFilter('require_profile', array('except' => array()));
        }
        
	/**
	 * Check if a friendship exists between two ids
	 *
	 */
	public function check_friendship($user_id, $friend_id)
	{
            $friendship_count = Friend::where('user_id', '=', $user_id)->where('friend_id', '=', $friend_id)->count();
            return $friendship_count;
        }
        
	/**
	 * Add Friend
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function add_friend($friend_id)
	{
            // Check if user is trying to add themselves
            if ($friend_id == Auth::id()){
                // Redirect Back
                Session::flash('message', 'You can not follow yourself! (Silly)');
                return Redirect::back();
            }
            
            // Check if a friendship already exists
            $friendship_count = $this->check_friendship(Auth::id(), $friend_id);
			
			// Check id user was banned or deleted
			$user = User::find($friend_id);
			
			if ((($user->role) == "banned") || (($user->role) == "deleted")){
                // Redirect
                Session::flash('message', 'You can not follow users who were <b>banned</b> or <b>deleted their account</b>!');
				return Redirect::to('home');
			}
            
            if ($friendship_count > 0) {
                // Redirect Back
                Session::flash('message', 'You were already following the user!');
                return Redirect::back();
            } else {
                // make the friendship
                $friend = new Friend;
                $friend->user_id = Auth::id();
                $friend->friend_id = $friend_id;
                $friend->friend_notified = 0;
                $friend->save();
                
                // Redirect Back
                Session::flash('message_success', 'You have followed <b>' . $user->username . '</b>!');
                return Redirect::back();
            }
        }
        
	/**
	 * Remove Friend
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function remove_friend($friend_id)
	{
            // Check if user is trying to remove themselves
            if ($friend_id == Auth::id()){
                // Redirect Back
                Session::flash('message', 'You can not unfollow yourself!');
                return Redirect::back();
            }
            
            // Check if a friendship already exists
            $friendship_count = $this->check_friendship(Auth::id(), $friend_id);
            
            if ($friendship_count == 0) {
                // Redirect Back
                Session::flash('message', 'You were already not following the user!');
                return Redirect::back();
            } else {
                // remove the friendship
                $friendship = Friend::whereUser_id(Auth::id())->whereFriend_id($friend_id)->first();
                $friendship->delete();
                
                // Redirect Back
                Session::flash('message_success', 'You have unfollowed a user!');
                return Redirect::back();
            }
        }
        
	/**
	 * Count followers
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function count_followers($user_id)
	{
            $follower_count = Friend::where('friend_id', '=', $user_id)->count();
            return $follower_count;
    }
        
	/**
	 * Count following
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function count_following($user_id)
	{
            $following_count = Friend::where('user_id', '=', $user_id)->count();
            return $following_count;
    }
        
	public function listFollowing($profile_id)
	{
            // get profile
            $profile = Profile::find($profile_id);
            
            // Get profile's user
            $user = User::find($profile->user_id);
            
            // Get user ID
            $user_id = $user->id;
            
            // get users being followed
            $following = DB::table('users')
                        ->join('profiles', 'users.id', '=', 'profiles.user_id')
                        ->leftjoin('friends', 'users.id', '=', 'friends.friend_id')
                        ->select('users.username', 'users.id as user_id',
                                 'profiles.id as profile_id', 'profiles.image as profile_image')
                        ->where('friends.user_id', '=', $user_id)
                        ->orderBy('friends.created_at', 'DESC')->paginate(15);
                        
                        
            $following_count = $this->count_following($user_id);
                        
            if ($following_count == 1){
            $data['header'] = $user->username . "'s Following List  <span class=\"light_gray_font pull-right\">" . number_format($following_count) . " user</span>";
            } else {
            $data['header'] = $user->username . "'s Following List  <span class=\"light_gray_font pull-right\">" . number_format($following_count) . " users</span>";
            }
            $data['title'] = "QuViews - Following List";
            $data['following'] = $following;
            $data['following_count'] = $following_count;
            $data['image_path'] = "images/profiles/medium/" . $profile->image;
            $data['profile'] = $profile;
            
            // show the view and pass data
            return View::make('profiles.following-list')->with($data);
        }
        
	public function listFollowers($profile_id)
	{
            // get profile
            $profile = Profile::find($profile_id);
            
            // Get profile's user
            $user = User::find($profile->user_id);
            
            // Get user ID
            $user_id = $user->id;
            
            // get users being followed
            $followers = DB::table('users')
                        ->join('profiles', 'users.id', '=', 'profiles.user_id')
                        ->leftjoin('friends', 'users.id', '=', 'friends.user_id')
                        ->select('users.username', 'users.id as user_id',
                                 'profiles.id as profile_id', 'profiles.image as profile_image',
                                 'friends.id as friend_id', 'friends.friend_notified')
                        ->where('friends.friend_id', '=', $user_id)
                        ->orderBy('friends.created_at', 'DESC')->paginate(15);
                        
                        
            $followers_count = $this->count_followers($user_id);
                        
            if ($followers_count == 1){
            $data['header'] = $user->username . "'s Followers List  <span class=\"light_gray_font pull-right\">" . number_format($followers_count) . " user</span>";
            } else {
            $data['header'] = $user->username . "'s Followers List  <span class=\"light_gray_font pull-right\">" . number_format($followers_count) . " users</span>";
            }
            $data['title'] = "QuViews - Followers List";
            $data['followers'] = $followers;
            $data['followers_count'] = $followers_count;
            $data['image_path'] = "images/profiles/medium/" . $profile->image;
            $data['profile'] = $profile;
            
            // show the view and pass data
            return View::make('profiles.followers-list')->with($data);
        }
        
/* Show Feeds of Users being followed */

	/**
	 * Display the overall Feed with Reviews, Posts, Replies
	 *
	 */
	public function showFollowingFeed()
	{
            
            // Get user ID
            $user_id = Auth::id();
            
            // Get user
            $user = User::find($user_id);
            
            // get user profile
            $profile = Profile::whereUser_id($user_id)->first();
            
            
            // get users being followed ID's
            /* $following = DB::table('users')
                        ->join('friends', 'users.id', '=', 'friends.friend_id')
                        ->select('users.id as user_id')
                        ->where('friends.user_id', '=', $user_id)->get(); */
                        
            $following = DB::table('friends')->where('friends.user_id', '=', $user_id)->lists('friend_id');
            
            /* $following = join(", ", array_values($following)); */
            
            // get user review(s)
            $reviews = DB::table('users')
                        ->join('reviews', 'users.id', '=', 'reviews.author_id')
                        ->leftjoin('profiles', 'users.id', '=', 'profiles.user_id')
                        ->leftjoin('items', 'reviews.item_id', '=', 'items.id')
                        ->leftjoin('categories', 'items.category_id', '=', 'categories.id')
                        ->select('users.username', 'reviews.id', 'reviews.author_id', 'reviews.body',
                                 'reviews.rank', 'reviews.created_at', 'reviews.updated_at',
                                 'profiles.id as profile_id', 'profiles.image as profile_image',
                                 'items.id as item_id', 'items.name as item_name', 'items.locked as item_locked',
                                 'categories.name as category_name', 'categories.image as category_image')
                        /* ->where('reviews.author_id', 'IN', ' (' . $following . ')') */
                        ->whereIn('reviews.author_id', $following )
                        ->orderBy('created_at', 'DESC')->take(3)->get();
                        
            // get user post(s)
            $posts = DB::table('users')
                        ->join('posts', 'users.id', '=', 'posts.author_id')
                        ->leftjoin('replies', 'posts.id', '=', 'replies.post_id')
                        ->leftjoin('profiles', 'users.id', '=', 'profiles.user_id')
                        ->leftjoin('items', 'posts.item_id', '=', 'items.id')
                        ->leftjoin('categories', 'items.category_id', '=', 'categories.id')
                        ->select(
                                 (DB::raw('count(replies.post_id) as replies_count'))
                                 ,'users.username', 'posts.id', 'posts.author_id', 'posts.body',
                                  'posts.created_at', 'posts.updated_at',
                                  'profiles.id as profile_id', 'profiles.image as profile_image',
                                  'items.id as item_id', 'items.name as item_name', 'items.locked as item_locked',
                                  'categories.name as category_name', 'categories.image as category_image')
                        /* ->where('posts.author_id', 'IN', ' (' . $following . ')') */
                        ->whereIn('posts.author_id', $following )
                        ->groupBy('posts.id')
                        ->orderBy('created_at', 'DESC')->take(3)->get();
                        
            // get user replies
            $replies = DB::table('users')
                        ->join('replies', 'users.id', '=', 'replies.author_id')
                        ->leftjoin('profiles', 'users.id', '=', 'profiles.user_id')
                        ->leftjoin('posts', 'replies.post_id', '=', 'posts.id')
                        ->leftjoin('items', 'posts.item_id', '=', 'items.id')
                        ->leftjoin('categories', 'items.category_id', '=', 'categories.id')
                        ->select('users.username', 'replies.id', 'replies.author_id', 'replies.body',
                                 'replies.quote_id', 'replies.quote_author_id', 'replies.quote_author_read', 'replies.post_author_read', 'replies.created_at', 'replies.updated_at',
                                 'posts.id as post_id', 'posts.author_id as post_author_id', 'posts.body as post_body',
                                 'profiles.id as profile_id', 'profiles.image as profile_image',
                                 'items.id as item_id', 'items.name as item_name', 'items.locked as item_locked',
                                 'categories.name as category_name', 'categories.image as category_image')
                        /* ->where('replies.author_id', 'IN', $following) */
                        ->whereIn('replies.author_id', $following )
                        ->orderBy('created_at', 'DESC')->take(3)->get();
            
            // Count Reviews
            $review_count = Review::whereIn('author_id', $following )->count();
            
            // Count Posts
            $post_count = Post::whereIn('author_id', $following )->count();
            
            // Count Replies
            $reply_count = Reply::whereIn('author_id', $following )->count();
            
            // put data into array
            $data['header'] = "Activities from users you are following";
            $data['title'] = "QuViews - Activities from users you are following - Quick Reviews";
            
            $data['reviews'] = $reviews;
            $data['posts'] = $posts;
            $data['replies'] = $replies;
            
            $data['user'] = $user;
            $data['profile'] = $profile;
            
            $data['review_count'] = $review_count;
            $data['post_count'] = $post_count;
            $data['reply_count'] = $reply_count;
            
            // show the view and pass data
            return View::make('following-feed')->with($data);
	}
        
	/**
	 * Display reviews feed
	 *
	 */
	public function showFollowingReviewsFeed()
	{
            
            // Get user ID
            $user_id = Auth::id();
            
            // Get user
            $user = User::find($user_id);
            
            // get user profile
            $profile = Profile::whereUser_id($user_id)->first();
            
            $following = DB::table('friends')->where('friends.user_id', '=', $user_id)->lists('friend_id');
            
            // get user review(s)
            $reviews = DB::table('users')
                        ->join('reviews', 'users.id', '=', 'reviews.author_id')
                        ->leftjoin('profiles', 'users.id', '=', 'profiles.user_id')
                        ->leftjoin('items', 'reviews.item_id', '=', 'items.id')
                        ->leftjoin('categories', 'items.category_id', '=', 'categories.id')
                        ->select('users.username', 'reviews.id', 'reviews.author_id', 'reviews.body',
                                 'reviews.rank', 'reviews.created_at', 'reviews.updated_at',
                                 'profiles.id as profile_id', 'profiles.image as profile_image',
                                 'items.id as item_id', 'items.name as item_name', 'items.locked as item_locked',
                                 'categories.name as category_name', 'categories.image as category_image')
                        ->whereIn('reviews.author_id', $following )
                        ->orderBy('created_at', 'DESC')->paginate(15);
            
            // Count Reviews
            $review_count = Review::whereIn('author_id', $following )->count();
            
            // put data into array
            // Handling plural
            if ($review_count == 1){
            $data['header'] = "Reviews from users you are following <span class=\"light_gray_font pull-right\">" . number_format($review_count) . " review</span>"; 
            } else {
            $data['header'] = "Reviews from users you are following  <span class=\"light_gray_font pull-right\">" . number_format($review_count) . " reviews</span>";    
            }
            $data['title'] = "QuViews - Reviews from users you are following - Quick Reviews";
            $data['reviews'] = $reviews;
            $data['user'] = $user;
            $data['profile'] = $profile;
            $data['review_count'] = $review_count;
            
            // show the view and pass data
            return View::make('following-reviews-feed')->with($data);
	}
        
        
	/**
	 * Display Posts Feed
	 *
	 */
	public function showFollowingPostsFeed()
	{
            
            // Get user ID
            $user_id = Auth::id();
            
            // Get user
            $user = User::find($user_id);
            
            // get user profile
            $profile = Profile::whereUser_id($user_id)->first();
            
            $following = DB::table('friends')->where('friends.user_id', '=', $user_id)->lists('friend_id');
            
            // get user post(s)
            $posts = DB::table('users')
                        ->join('posts', 'users.id', '=', 'posts.author_id')
                        ->leftjoin('replies', 'posts.id', '=', 'replies.post_id')
                        ->leftjoin('profiles', 'users.id', '=', 'profiles.user_id')
                        ->leftjoin('items', 'posts.item_id', '=', 'items.id')
                        ->leftjoin('categories', 'items.category_id', '=', 'categories.id')
                        ->select(
                                 (DB::raw('count(replies.post_id) as replies_count'))
                                 ,'users.username', 'posts.id', 'posts.author_id', 'posts.body',
                                  'posts.created_at', 'posts.updated_at',
                                  'profiles.id as profile_id', 'profiles.image as profile_image',
                                  'items.id as item_id', 'items.name as item_name', 'items.locked as item_locked',
                                  'categories.name as category_name', 'categories.image as category_image')
                        ->whereIn('posts.author_id', $following )
                        ->groupBy('posts.id')
                        ->orderBy('created_at', 'DESC')->paginate(15);
            
            // Count Posts
            $post_count = Post::whereIn('author_id', $following )->count();
            
            // put data into array
            // Handling plural
            if ($post_count == 1){
            $data['header'] = "Posts from users you are following <span class=\"light_gray_font pull-right\">" . number_format($post_count) . " post</span>"; 
            } else {
            $data['header'] = "Posts from users you are following  <span class=\"light_gray_font pull-right\">" . number_format($post_count) . " posts</span>";    
            }
            
            $data['title'] = "QuViews - Posts from users you are following - Quick Reviews";
            $data['posts'] = $posts;
            $data['user'] = $user;
            $data['profile'] = $profile;
            $data['post_count'] = $post_count;
            
            // show the view and pass data
            return View::make('following-posts-feed')->with($data);
	}
        
        
	/**
	 * Display Replies Feed
	 *
	 */
	public function showFollowingRepliesFeed()
	{
            
            // Get user ID
            $user_id = Auth::id();
            
            // Get user
            $user = User::find($user_id);
            
            // get user profile
            $profile = Profile::whereUser_id($user_id)->first();
            
            $following = DB::table('friends')->where('friends.user_id', '=', $user_id)->lists('friend_id');
            
            // get user replies
            $replies = DB::table('users')
                        ->join('replies', 'users.id', '=', 'replies.author_id')
                        ->leftjoin('profiles', 'users.id', '=', 'profiles.user_id')
                        ->leftjoin('posts', 'replies.post_id', '=', 'posts.id')
                        ->leftjoin('items', 'posts.item_id', '=', 'items.id')
                        ->leftjoin('categories', 'items.category_id', '=', 'categories.id')
                        ->select('users.username', 'replies.id', 'replies.author_id', 'replies.body',
                                 'replies.quote_id', 'replies.quote_author_id', 'replies.quote_author_read', 'replies.post_author_read', 'replies.created_at', 'replies.updated_at',
                                 'posts.id as post_id', 'posts.author_id as post_author_id', 'posts.body as post_body',
                                 'profiles.id as profile_id', 'profiles.image as profile_image',
                                 'items.id as item_id', 'items.name as item_name', 'items.locked as item_locked',
                                 'categories.name as category_name', 'categories.image as category_image')
                        /* ->where('replies.author_id', 'IN', $following) */
                        ->whereIn('replies.author_id', $following )
                        ->orderBy('created_at', 'DESC')->paginate(15);
            
            // Count Replies
            $reply_count = Reply::whereIn('author_id', $following )->count();
            
            // put data into array
            
            // Handling plural
            if ($reply_count == 1){
            $data['header'] = "Replies from users you are following <span class=\"light_gray_font pull-right\">" . number_format($reply_count) . " reply</span>"; 
            } else {
            $data['header'] = "Replies from users you are following  <span class=\"light_gray_font pull-right\">" . number_format($reply_count) . " replies</span>";    
            }
            
            $data['title'] = "QuViews - Replies from users you are following - Quick Reviews";
            $data['replies'] = $replies;
            $data['user'] = $user;
            $data['profile'] = $profile;
            $data['reply_count'] = $reply_count;
            
            // show the view and pass data
            return View::make('following-replies-feed')->with($data);
	}
	
	/**
	 * Remove all relationship
	 *
	 * Used when banning a user, or deleting a profile
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function remove_all_friendships($user_id)
	{
		// remove all friendships
		$friendship_delete = Friend::where('user_id', '=', $user_id)->orWhere('friend_id', '=', $user_id)->delete();
		
    }

}
