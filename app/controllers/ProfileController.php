<?php

class ProfileController extends \BaseController {
    
        // Filters
        public function __construct()
        {
            // pages that don't require log in to access
            $this->beforeFilter('auth', array('except' => array('postsFeed', 'reviewsFeed', 'show')));
            // pages only admin can access
            $this->beforeFilter('allow_only_admin', array('only' => array('index', 'showChangeUserRole', 'banUser', 'reviveUser', 'promoteUser', 'contentStatistics', 'userStatistics', 'activityStatistics')));
            // pages that don't require profile to access
            $this->beforeFilter('require_profile', array('except' => array('postsFeed', 'reviewsFeed', 'show', 'create', 'store')));
            // all pages should kick out banned user
            $this->beforeFilter('kick_banned', array('except' => array('')));
        }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
            // get all the profiles
			$profiles = Profile::orderBy('id', 'DESC')->paginate(12);
            
            $data['header'] = "List all profiles";
            $data['title'] = "QuViews - List all profiles";
            $data['profiles'] = $profiles;
            
            // load the view and pass the data
            return View::make('profiles.index')->with($data);
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
            // If user alredy has a profile redirect
            $user_id =  Auth::id();
            $profile_count = Profile::whereUser_id($user_id)->count();
            
            // If a profile already exists
            if ($profile_count > 0) {
                $profile = Profile::whereUser_id($user_id)->first();
                $profile_id = $profile->id;
                
                // Redirect to user profile
                Session::flash('message', 'You already made your profile, you can edit it here');
                return Redirect::to('profiles/' . $profile_id . '/edit');
            }
            
            $data['header'] = "Create Profile";
            $data['title'] = "QuViews - Create Profile";
            
            return View::make('profiles.create', $data);
	}

        /**
          * Store a newly created resource in storage.
          *
          * @return Response
          */
         public function store()
         {
             // validate
             // read more on validation at http://laravel.com/docs/validation
             $rules = array(
                 //'first_name'       => 'required', // optional
                 //'last_name'       => 'required', // optional
                 'gender'       => 'required',
                 'birth_date'       => 'required|numeric',
                 'birth_month'       => 'required|numeric',
                 'birth_year'       => 'required|numeric',
                 'country'       => 'required',
                 'about'       => 'required'
                 // ,'image' => 'required|image|max:3000' // optional
             );
             
             $validator = Validator::make(Input::all(), $rules);
     
             // process the form
             if ($validator->fails()) {
                 return Redirect::to('profiles/create')
                     ->withErrors($validator)
                     ->withInput();
             } else {
                
				// If user passed profile image
				if (Input::file('image')) {
                // Add Profile Image
                $file = Input::file('image');
                $destinationPath = public_path() . '/images/profiles';
                $file_extension = $file->getClientOriginalExtension();
                // name image by unique user_id
                $filename = Auth::id() . '.' . $file_extension;
                $filename = strtolower($filename);
                $uploadSuccess = $file->move($destinationPath, $filename);
                
                // Save Thumbnail
                
                // full image file path to original image we are trying to shrink
                $original_file_path = public_path() . '/images/profiles/' . $filename;
				
				// full image name file path for medium
				$medium_file = public_path() . '/images/profiles/medium/' . $filename;
                
                // full image name file path for thumbnail
                $thumb_file = public_path() . '/images/profiles/small/' . $filename;
				
                // path to folder where medium will be stored
                $medium_folder = public_path() . '/images/profiles/medium/';
                
                // path to folder where thumbnail will be stored
                $thumb_folder = public_path() . '/images/profiles/small/';
				
				// generate and store medium image
                $im_medium = $this->thumbnail($original_file_path, 200);
                $medium_uploadSuccess = $this->imageToFile($im_medium, $medium_file, $medium_folder);
                
				// generate and store small image
                $im_small = $this->thumbnail($original_file_path, 100);
                $thumbnail_uploadSuccess = $this->imageToFile($im_small, $thumb_file, $thumb_folder);
                
                // ##Ed Add condition to check if file was uploaded successfully
				} else {
					// If user passed no profile image
					$filename = "default.jpg";
				}
                
                // Create Birthday
                $birth_year = Input::get( 'birth_year' );
                $birth_month = Input::get( 'birth_month' );
                $birth_date = Input::get( 'birth_date' );
                $birthday = $birth_year . '-' . $birth_month . '-' . $birth_date;
                
                // Store new Profile
                $profile = new Profile;
                $profile->user_id = Auth::id();
                $profile->first_name = Input::get( 'first_name' );
                $profile->last_name = Input::get( 'last_name' );
                $profile->gender = Input::get( 'gender' );
                $profile->birthday = $birthday;
                $profile->country = Input::get( 'country' );
                $profile->about = Input::get( 'about' );
                $profile->image = $filename;
                $profile->favorite_item_id = 0;
                $profile->save();
                
                // Mark role: user_no_profile as user (update)
                $user = User::find(Auth::id());
                $user->role = "user";
                $user->save();
                
                // redirect
                Session::flash('message_success', 'Successfully made your profile!');
                return Redirect::to('profiles/' . $profile->id);
             }
         }
         
         
	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
            // get profile
            $profile = Profile::find($id);
			
			// if no profile is found, redirect
			if (is_null($profile)){
				// redirect
				return Redirect::to('/home')->with('message', 'That profile was not found');
			}
            
            // Get profile's user
            $user = User::find($profile->user_id);
            
            // Get user ID
            $user_id = $user->id;
			
			// Redirect code for lgged in users
			if(Auth::check()) {
				
				// If profile is banned and user accessing page is not admin, redirect
				if (($user->role == "banned") && (Auth::user()->role != "admin")) {
					Session::flash('message_danger', 'Sorry, <b>' . $user->username . '</b> was BANNED!');
					return Redirect::to('home');
				}
				
				// If profile is deleted and user accessing page is not admin, redirect
				if (($user->role == "deleted") && (Auth::user()->role != "admin")) {
					Session::flash('message_danger', 'Sorry, <b>' . $user->username . '</b> had their account DELETED');
					return Redirect::to('home');
				}
			
			} else {
			// Redirect code for non-logged in users
			
				// If profile is banned and user accessing page is not admin, redirect
				if ($user->role == "banned") {
					Session::flash('message_danger', 'Sorry, that account was BANNED!');
					return Redirect::to('home');
				}
				
				// If profile is deleted and user accessing page is not admin, redirect
				if ($user->role == "deleted") {
					Session::flash('message_danger', 'Sorry, that account was DELETED');
					return Redirect::to('home');
				}
					
			}
            
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
                        ->where('reviews.author_id', '=', $user_id)
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
                        ->where('posts.author_id', '=', $user_id)
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
                        ->where('replies.author_id', '=', $user_id)
                        ->orderBy('created_at', 'DESC')->take(3)->get();
                        
            // Count Reviews
            $review_count = Review::where('author_id', '=', $user_id)->count();
            
            // Count Posts
            $post_count = Post::where('author_id', '=', $user_id)->count();
            
            // Count Replies
            $reply_count = Reply::where('author_id', '=', $user_id)->count();
            
            // Count Good reviews
            $good_review_count =  Review::where('author_id', '=', $user_id)->where('rank', '=', 'Good')->count();
            
            // Count Eh reviews
            $eh_review_count =  Review::where('author_id', '=', $user_id)->where('rank', '=', 'Eh')->count();
            
            // Count bad reviews
            $bad_review_count =  Review::where('author_id', '=', $user_id)->where('rank', '=', 'Bad')->count();
            
            // Get favorite item
            $favorite_item = Item::find($profile->favorite_item_id);
            
            // Check if friendship exists
            // Are we following user?
            $friendship_following_them = App::make('FriendController')->check_friendship(Auth::id(), $profile->user_id);
            
            // Is the user follwoing us?
            $friendship_following_me = App::make('FriendController')->check_friendship($profile->user_id, Auth::id());
            
            // Count followers
            $follower_count = App::make('FriendController')->count_followers($profile->user_id);
            
            // Count following
            $following_count = App::make('FriendController')->count_following($profile->user_id);
            
            // put profile data into array
			if ($user->role == "banned") {
				$status = " &nbsp; <span class=\"highlight_red pull-right\">BANNED</span>";
			} elseif ($user->role == "deleted") {
				$status = " &nbsp; <span class=\"highlight_red pull-right\">DELETED</span>";
			} elseif ($user->role == "admin") {
				$status = " &nbsp; <span class=\"admin_label pull-right\">ADMIN</span>";
			} else {
				$status = "";
			}
			
            $data['header'] = $user->username . "'s Profile" . $status;
            $data['title'] = "QuViews - " . $user->username . " - Quick Reviews";
            $data['image_path'] = "images/profiles/medium/" . $profile->image;
            $data['reviews'] = $reviews;
            $data['posts'] = $posts;
            $data['replies'] = $replies;
            $data['good_review_count'] =  $good_review_count;
            $data['eh_review_count'] =  $eh_review_count;
            $data['bad_review_count'] =  $bad_review_count;
            $data['user'] = $user;
            $data['profile'] = $profile;
            $data['review_count'] = $review_count;
            $data['post_count'] = $post_count;
            $data['reply_count'] = $reply_count;
            $data['favorite_item'] = $favorite_item;
            $data['follower_count'] = $follower_count;
            $data['following_count'] = $following_count;
            
            $data['friendship_following_them'] = $friendship_following_them;
            $data['friendship_following_me'] = $friendship_following_me;
            
            // show the view and pass data
            return View::make('profiles.show')->with($data);
	}
        
        
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
            // get profile
            $profile = Profile::find($id);
            
            // Check if user is not Admin or profile owner
            if ((Auth::id() != ($profile->user_id)) AND (Auth::user()->role != "admin")) {
                Session::flash('message', 'Sorry, you can only edit your own profile!');
                return Redirect::to('home');
            }
            
            // Get profile's user
            $user = User::find($profile->user_id);
            
            // Breakdown birthday
            $birth_day = $profile->birthday;
            $y = date('Y', strtotime($birth_day));
            $m = date('m', strtotime($birth_day));
            $d = date('d', strtotime($birth_day));
            
            $data['header'] = "Edit Profile";
            $data['title'] = "QuViews - Editing a Profile";
            $data['image_path'] = "images/profiles/medium/" . $profile->image;
            $data['profile'] = $profile;
            $data['user'] = $user;
            $data['year'] = $y;
            $data['month'] = $m;
            $data['date'] = $d;
            
            // show the edit form and pass the data
            return View::make('profiles.edit')->with($data);
	}
        
        
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
            // validate
            // read more on validation at http://laravel.com/docs/validation
             $rules = array(
                 // 'first_name'       => 'required', // optional
                 // 'last_name'       => 'required', // optional
                 'gender'       => 'required',
                 'birth_date'       => 'required|numeric',
                 'birth_month'       => 'required|numeric',
                 'birth_year'       => 'required|numeric',
                 'country'       => 'required',
                 'about'       => 'required',
             );
            
            // If user passed image, validate passed file
            if(Input::hasfile('image')) {
             $rules = array(
                 'image' => 'required|image|max:3000'
                 );
            }
            
            $validator = Validator::make(Input::all(), $rules);
            
            // process the form
            if ($validator->fails()) {
                return Redirect::to('profiles/' . $id . '/edit')
                    ->withErrors($validator)
                    ->withInput();
            } else {
                
                // Create Birthday
                $birth_year = Input::get( 'birth_year' );
                $birth_month = Input::get( 'birth_month' );
                $birth_date = Input::get( 'birth_date' );
                $birthday = $birth_year . '-' . $birth_month . '-' . $birth_date;
                
                // Store new Profile details
                $profile = Profile::find($id);
                
                $profile->first_name = Input::get( 'first_name' );
                $profile->last_name = Input::get( 'last_name' );
                $profile->gender = Input::get( 'gender' );
                $profile->birthday = $birthday;
                $profile->country = Input::get( 'country' );
                $profile->about = Input::get( 'about' );
                
                // If user passed image
                if(Input::hasfile('image')) {
                    // delete old image file
					$image_name = $profile->image;
					$old_filename = public_path() . '/images/profiles/' . $image_name;
					$old_filename_medium =  public_path() . '/images/profiles/medium/' . $image_name;
					$old_filename_thumbnail =  public_path() . '/images/profiles/small/' . $image_name;
					
					// delete main image
					if ((File::exists($old_filename)) && ($image_name != "default.jpg")) {
						File::delete($old_filename);
					}
					
					// delete medium
					if ((File::exists($old_filename_medium)) && ($image_name != "default.jpg")) {
						File::delete($old_filename_medium);
					}
					
					// delete thumbnail
					if ((File::exists($old_filename_thumbnail)) && ($image_name != "default.jpg")) {
						File::delete($old_filename_thumbnail);
					}
                    
                    // add new image file
                    $file = Input::file('image');
                    $destinationPath = public_path() . '/images/profiles';
                    $file_extension = $file->getClientOriginalExtension();
                    // name image by unique user_id
                    $filename = $profile->user_id . '.' . $file_extension;
                    $filename = strtolower($filename);
                    $uploadSuccess = $file->move($destinationPath, $filename);
                    
                    $profile->image = $filename;
                    
                    // Save Thumbnail
                    
                    // full image file path to original image we are trying to shrink
                    $original_file_path = public_path() . '/images/profiles/' . $filename;
					
					// full image name file path for medium
					$medium_file = public_path() . '/images/profiles/medium/' . $filename;
                    
                    // full image name file path for thumbnail
                    $thumb_file = public_path() . '/images/profiles/small/' . $filename;
                    
					// path to folder where medium will be stored
					$medium_folder = public_path() . '/images/profiles/medium/';
					
                    // path to folder where thumbnail will be stored
                    $thumb_folder = public_path() . '/images/profiles/small/';
					
					// generate and store medium image
					$im_medium = $this->thumbnail($original_file_path, 200);
					$medium_uploadSuccess = $this->imageToFile($im_medium, $medium_file, $medium_folder);
                    
					// generate and store small image
                    $im_small = $this->thumbnail($original_file_path, 100);
                    $thumbnail_uploadSuccess = $this->imageToFile($im_small, $thumb_file, $thumb_folder);
                    
                }
                
                $profile->save();
                
                // redirect
                Session::flash('message_success', 'Successfully updated profile!');
                return Redirect::to('profiles/' . $id);
            }
	}
        
        
	/**
	 * Remove the specified resource from storage.
	 *
	 * ##Ed NOTE: This code is not used in production, we delete account instead
	 * which only altres user role to "deleted"
	 * 
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
			// Get profile
			$profile = Profile::find($id);
			
			
            // delete old image file
			$image_name = $profile->image;
			$old_filename = public_path() . '/images/profiles/' . $image_name;
			$old_filename_medium =  public_path() . '/images/profiles/medium/' . $image_name;
			$old_filename_thumbnail =  public_path() . '/images/profiles/small/' . $image_name;
					
			// delete main image
			if ((File::exists($old_filename)) && ($image_name != "default.jpg")) {
				File::delete($old_filename);
			}
			
			// delete medium
			if ((File::exists($old_filename_medium)) && ($image_name != "default.jpg")) {
				File::delete($old_filename_medium);
			}
			
			// delete thumbnail
			if ((File::exists($old_filename_thumbnail)) && ($image_name != "default.jpg")) {
				File::delete($old_filename_thumbnail);
			}
			
			// Get user
			$user = User::find($profile->user_id);
			
			// change user role to show they have no profile
			$user->role = "user_no_profile";
			$user->save();
			
			// delete the profile
            $profile->delete();
            
            // redirect
            Session::flash('message_success', 'Successfully deleted profile for <b>' . $user->username . '</b>!');
            return Redirect::to('home');
	}
        
/* Additional Controllers */

	/**
	 * Show Feed Of Profile user reviews.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function reviewsFeed($id)
	{
            // get profile
            $profile = Profile::find($id);
            
            // Get profile's user
            $user = User::find($profile->user_id);
            
            // Get user ID
            $user_id = $user->id;
            
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
                        ->where('reviews.author_id', '=', $user_id)
                        ->orderBy('created_at', 'DESC')->paginate(15);
                        
            // Count Reviews
            $review_count = Review::where('author_id', '=', $user_id)->count();
            
            // Count Good reviews
            $good_review_count =  Review::where('author_id', '=', $user_id)->where('rank', '=', 'Good')->count();
            
            // Count Eh reviews
            $eh_review_count =  Review::where('author_id', '=', $user_id)->where('rank', '=', 'Eh')->count();
            
            // Count bad reviews
            $bad_review_count =  Review::where('author_id', '=', $user_id)->where('rank', '=', 'Bad')->count();
            
            // put data into array
            $data['header'] = $user->username . "'s Reviews Feed <span class=\"light_gray_font pull-right\">" . number_format($review_count) . " reviews</span>";
            $data['title'] = "QuViews - " . $user->username . " - Reviews Feed - Quick Reviews";
            $data['image_path'] = "images/profiles/medium/" . $profile->image;
            $data['reviews'] = $reviews;
            $data['good_review_count'] =  $good_review_count;
            $data['eh_review_count'] =  $eh_review_count;
            $data['bad_review_count'] =  $bad_review_count;
            $data['user'] = $user;
            $data['profile'] = $profile;
            $data['review_count'] = $review_count;
            
            // show the view and pass data
            return View::make('profiles.reviews-feed')->with($data);
	}
        
        
	/**
	 * Show Feed Of Profile user posts.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function postsFeed($id)
	{
            // get profile
            $profile = Profile::find($id);
            
            // Get profile's user
            $user = User::find($profile->user_id);
            
            // Get user ID
            $user_id = $user->id;
            
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
                        ->where('posts.author_id', '=', $user_id)
                        ->groupBy('posts.id')
                        ->orderBy('created_at', 'DESC')->paginate(15);
            
            // Count Posts
            $post_count = Post::where('author_id', '=', $user_id)->count();
            
            // put data into array
            $data['header'] = $user->username . "'s Posts Feed <span class=\"light_gray_font pull-right\">" . number_format($post_count) . " posts</span>";
            $data['title'] = "QuViews - " . $user->username . " - Posts Feed - Discussion";
            $data['image_path'] = "images/profiles/medium/" . $profile->image;
            $data['posts'] = $posts;
            $data['user'] = $user;
            $data['profile'] = $profile;
            $data['post_count'] = $post_count;
            
            // show the view and pass data
            return View::make('profiles.posts-feed')->with($data);
	}
        
	/**
	 * Show Feed Of Profile user replies.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function repliesFeed($id)
	{
            // get profile
            $profile = Profile::find($id);
            
            // Get profile's user
            $user = User::find($profile->user_id);
            
            // Get user ID
            $user_id = $user->id;
            
            // get user replies
            $replies = DB::table('users')
                        ->join('replies', 'users.id', '=', 'replies.author_id')
                        ->leftjoin('profiles', 'users.id', '=', 'profiles.user_id')
                        ->leftjoin('posts', 'replies.post_id', '=', 'posts.id')
                        ->leftjoin('items', 'posts.item_id', '=', 'items.id')
                        ->leftjoin('categories', 'items.category_id', '=', 'categories.id')
                        ->select('users.username', 'replies.id', 'replies.author_id', 'replies.body',
                                 'replies.quote_id', 'replies.created_at', 'replies.updated_at',
                                 'posts.id as post_id', 'posts.author_id as post_author_id', 'posts.body as post_body',
                                 'profiles.id as profile_id', 'profiles.image as profile_image',
                                 'items.id as item_id', 'items.name as item_name', 'items.locked as item_locked',
                                 'categories.name as category_name', 'categories.image as category_image')
                        ->where('replies.author_id', '=', $user_id)
                        ->orderBy('created_at', 'DESC')->paginate(15);
            
            // Count Replies
            $reply_count = Reply::where('author_id', '=', $user_id)->count();
            
            // put data into array
            $data['header'] = $user->username . "'s Replies Feed <span class=\"light_gray_font pull-right\">" . number_format($reply_count) . " replies</span>";
            $data['title'] = "QuViews - " . $user->username . " - Replies Feed - Discussion";
            $data['image_path'] = "images/profiles/medium/" . $profile->image;
            $data['replies'] = $replies;
            $data['user'] = $user;
            $data['profile'] = $profile;
            $data['reply_count'] = $reply_count;
            
            // show the view and pass data
            return View::make('profiles.replies-feed')->with($data);
	}
        
	public function notificationsFeed($id)
	{
            // get profile
            $profile = Profile::find($id);
            
            // Check if user is not Admin or profile owner
            if ((Auth::id() != ($profile->user_id)) AND (Auth::user()->role != "admin")) {
                Session::flash('message', 'Sorry, you can only view your own notifications!');
                return Redirect::to('home');
            }
            
            // Get profile's user
            $user = User::find($profile->user_id);
            
            // Get user ID
            $user_id = $user->id;
            
            // get user replies
            // Get unread replies / quotes
            $replies = DB::table('users')
                        ->join('posts', 'users.id', '=', 'posts.author_id')
                        ->leftjoin('replies', 'posts.id', '=', 'replies.post_id')
                        ->leftjoin('profiles', 'users.id', '=', 'profiles.user_id')
                        ->leftjoin('items', 'posts.item_id', '=', 'items.id')
                        ->leftjoin('categories', 'items.category_id', '=', 'categories.id')
                        ->select('users.username', 'replies.id', 'replies.author_id', 'replies.body',
                                 'replies.quote_id', 'replies.created_at', 'replies.updated_at',
								 'replies.quote_id', 'replies.quote_author_id', 'replies.quote_author_read', 'replies.post_author_read', 'replies.created_at', 'replies.updated_at',
                                 'posts.id as post_id', 'posts.author_id as post_author_id', 'posts.body as post_body',
                                 'profiles.id as profile_id', 'profiles.image as profile_image',
                                 'items.id as item_id', 'items.name as item_name', 'items.locked as item_locked',
                                 'categories.name as category_name', 'categories.image as category_image')
                        ->where('replies.post_author_id', '=', $user_id)->where('replies.post_author_read', '=', 0)
                        ->orWhere('replies.quote_author_id', '=', $user_id)->where('replies.quote_author_read', '=', 0)
                        ->orderBy('created_at', 'DESC')->paginate(15);
            
            // Count Replies
            $unread_reply_count = Reply::where('post_author_id', '=', $user_id)->where('post_author_read', '=', 0)->count();
            $unread_quote_count = Reply::where('quote_author_id', '=', $user_id)->where('quote_author_read', '=', 0)->count();
            $reply_count = $unread_reply_count + $unread_quote_count;
                        
            // put data into array
            
            //Handling plural of Notification / Notifications
            if ($reply_count == 1){
            $data['header'] = $user->username . "'s Notifications Feed <span class=\"light_gray_font pull-right\">" . number_format($reply_count) . " notification</span>"; 
            } else {
            $data['header'] = $user->username . "'s Notifications Feed <span class=\"light_gray_font pull-right\">" . number_format($reply_count) . " notifications</span>";    
            }
            
            $data['title'] = "QuViews - " . $user->username . " - Replies Feed - Discussion";
            $data['image_path'] = "images/profiles/medium/" . $profile->image;
            $data['replies'] = $replies;
            $data['user'] = $user;
            $data['profile'] = $profile;
            $data['reply_count'] = $reply_count;
            $data['unread_reply_count'] = $unread_reply_count;
            $data['unread_quote_count'] = $unread_quote_count;
            
            // show the view and pass data
            return View::make('profiles.notifications-feed')->with($data);
	}
    
	// Get ALL replies history
	public function conversationHistoryFeed($id)
	{
            // get profile
            $profile = Profile::find($id);
            
            // Check if user is not Admin or profile owner
            if ((Auth::id() != ($profile->user_id)) AND (Auth::user()->role != "admin")) {
                Session::flash('message', 'Sorry, you can only view your own conversation history!');
                return Redirect::to('home');
            }
            
            // Get profile's user
            $user = User::find($profile->user_id);
            
            // Get user ID
            $user_id = $user->id;
            
            // get user replies
            // Get all replies / quotes
            $replies = DB::table('users')
                        ->join('posts', 'users.id', '=', 'posts.author_id')
                        ->leftjoin('replies', 'posts.id', '=', 'replies.post_id')
                        ->leftjoin('profiles', 'users.id', '=', 'profiles.user_id')
                        ->leftjoin('items', 'posts.item_id', '=', 'items.id')
                        ->leftjoin('categories', 'items.category_id', '=', 'categories.id')
                        ->select('users.username', 'replies.id', 'replies.author_id', 'replies.body',
                                 'replies.quote_id', 'replies.created_at', 'replies.updated_at',
								 'replies.quote_id', 'replies.quote_author_id', 'replies.quote_author_read', 'replies.post_author_read', 'replies.created_at', 'replies.updated_at',
                                 'posts.id as post_id', 'posts.author_id as post_author_id', 'posts.body as post_body',
                                 'profiles.id as profile_id', 'profiles.image as profile_image',
                                 'items.id as item_id', 'items.name as item_name', 'items.locked as item_locked',
                                 'categories.name as category_name', 'categories.image as category_image')
						->where( 'replies.author_id', '!=', $user_id) // remove ourselves
                        ->where('replies.post_author_id', '=', $user_id)
                        ->orWhere('replies.quote_author_id', '=', $user_id)
                        ->orderBy('created_at', 'DESC')->paginate(15);
            
            // Count Replies
            $all_reply_count = Reply::where('post_author_id', '=', $user_id)->where( 'replies.author_id', '!=', $user_id)->count();
            $all_quote_count = Reply::where('quote_author_id', '=', $user_id)->where( 'replies.author_id', '!=', $user_id)->count();
            $reply_count = $all_reply_count + $all_quote_count;
                        
            // put data into array
            
            //Handling plural of Notification / Notifications
            if ($reply_count == 1){
            $data['header'] = $user->username . "'s Conversation History Feed <span class=\"light_gray_font pull-right\">" . number_format($reply_count) . "</span>"; 
            } else {
            $data['header'] = $user->username . "'s Conversation History Feed <span class=\"light_gray_font pull-right\">" . number_format($reply_count) . "</span>";    
            }
            
            $data['title'] = "QuViews - " . $user->username . " - Conversation History Feed";
            $data['image_path'] = "images/profiles/medium/" . $profile->image;
            $data['replies'] = $replies;
            $data['user'] = $user;
            $data['profile'] = $profile;
            $data['reply_count'] = $reply_count;
            $data['all_reply_count'] = $all_reply_count;
            $data['all_quote_count'] = $all_quote_count;
            
            // show the view and pass data
            return View::make('profiles.conversation-history-feed')->with($data);
	}
	
/* Functions to create thumbnails */

/**
     * Create a thumbnail image from $inputFileName no taller or wider than 
     * $maxSize. Returns the new image resource or false on error.
     * Author: mthorn.net
     */
    public static function thumbnail($inputFileName, $maxSize = 100)
    {
        $info = getimagesize($inputFileName);

        $type = isset($info['type']) ? $info['type'] : $info[2];

        // Check support of file type
        if ( !(imagetypes() & $type) )
        {
            // Server does not support file type
            return false;
        }

        $width  = isset($info['width'])  ? $info['width']  : $info[0];
        $height = isset($info['height']) ? $info['height'] : $info[1];

        // Calculate aspect ratio
        $wRatio = $maxSize / $width;
        $hRatio = $maxSize / $height;

        // Using imagecreatefromstring will automatically detect the file type
        $sourceImage = imagecreatefromstring(file_get_contents($inputFileName));

        // Calculate a proportional width and height no larger than the max size.
        if ( ($width <= $maxSize) && ($height <= $maxSize) )
        {
            // Input is smaller than thumbnail, do nothing
            return $sourceImage;
        }
        elseif ( ($wRatio * $height) < $maxSize )
        {
            // Image is horizontal
            $tHeight = ceil($wRatio * $height);
            $tWidth  = $maxSize;
        }
        else
        {
            // Image is vertical
            $tWidth  = ceil($hRatio * $width);
            $tHeight = $maxSize;
        }

        $thumb = imagecreatetruecolor($tWidth, $tHeight);

        if ( $sourceImage === false )
        {
            // Could not load image
            return false;
        }

        // Copy resampled makes a smooth thumbnail
        imagecopyresampled($thumb, $sourceImage, 0, 0, 0, 0, $tWidth, $tHeight, $width, $height);
        imagedestroy($sourceImage);

        return $thumb;
    }

    /**
     * Save the image to a file. Type is determined from the extension.
     * $quality is only used for jpegs.
     * Author: mthorn.net
     *
     * Source: 
     */
    public static function imageToFile($im, $fileName, $thumb_folder, $quality = 80)
    {
        
        if ( !$im || file_exists($fileName) )
        {
           return false;
        }
        
        /* ##Added code */
            
            // Check If user thumbnail directory exists
            /*
             This was previously:
             if(file_exists($thumbnail_folder) && is_dir($thumbnail_folder)) {
             
             but I think it was an error as theres no variable $thumbnail_folder passed,
             only $thumb_folder, it was probably a naming error
             if photo uploads don't work as planned, then revisit this code snippet again
            */
            if(file_exists($thumb_folder) && is_dir($thumb_folder)) {
            clearstatcache(); /* ##  in certain cases, you may want to clear the cached information. For instance,
                                if the same file is being checked multiple times within a single script,
                                reference: http://php.net/manual/en/function.clearstatcache.php */
            // If it exists Determine do nothing
            } else {
            // If it does not exist, make one
            mkdir($thumb_folder, 0777); /* ## Doesnt work, but should, find alt */
            }
            
        /* ----------- */

        $ext = strtolower(substr($fileName, strrpos($fileName, '.')));

        switch ( $ext )
        {
            case '.gif':
                imagegif($im, $fileName);
                break;
            case '.jpg':
            case '.jpeg':
                imagejpeg($im, $fileName, $quality);
                break;
            case '.png':
                imagepng($im, $fileName);
                break;
            case '.bmp':
                imagewbmp($im, $fileName);
                break;
            default:
                return false;
        }

        return true;
    }
	
/*
 * Remove Profile Image
 *
 *
 * */

	public function removeProfileImage($id)
	{
            // get profile
            $profile = Profile::find($id);
            
            // Check if user is not Admin or profile owner
            if ((Auth::id() != ($profile->user_id)) AND (Auth::user()->role != "admin")) {
                Session::flash('message', 'Sorry, you can only remove your own profile image!');
                return Redirect::to('home');
            }
            
            // delete profile image file
            $image_name = $profile->image;
			
			if ($image_name == "default.jpg") {
                // Redirect Back
                Session::flash('message', 'You can not remove the default profile image');
                return Redirect::back();
			}
			
            $old_filename = public_path() . '/images/profiles/' . $image_name;
			$old_filename_medium =  public_path() . '/images/profiles/medium/' . $image_name;
			$old_filename_thumbnail =  public_path() . '/images/profiles/small/' . $image_name;
            
			// delete main image
			if ((File::exists($old_filename)) && ($image_name != "default.jpg")) {
				File::delete($old_filename);
			}
			
			// delete medium
			if ((File::exists($old_filename_medium)) && ($image_name != "default.jpg")) {
				File::delete($old_filename_medium);
			}
			
			// delete thumbnail
			if ((File::exists($old_filename_thumbnail)) && ($image_name != "default.jpg")) {
				File::delete($old_filename_thumbnail);
			}
			
			// make profile image default
			$this->makeProfileImageDefault($id);

            // redirect
            Session::flash('message_success', 'Successfully removed profile image!');
            return Redirect::to('/profiles/' . $id);
	}
	
	// Make profile image default
	public function makeProfileImageDefault($id){
            // get profile
            $profile = Profile::find($id);
			
			// make profile image default
			$profile->image = "default.jpg";
			$profile->save();
	}
	
	
	public function showChangeUserRole($id)
    {
		$user = User::find($id);
		
		// get profile
		$profile = Profile::whereUser_id($id)->first();
		
        $data['header'] = "Change User Role";
        $data['title'] = "QuViews";
		$data['profile'] = $profile;
		$data['image_path'] = "images/profiles/medium/" . $profile->image;
		$data['user'] = $user;
        
        return View::make('/boss/change-user-role', $data);
    }
	
	// Ban User
	public function banUser($id){
		// Get user
		$user = User::find($id);
		
		// Remove all friendships
        App::make('FriendController')->remove_all_friendships($id);
		
		// Change user role
		$user->role = "banned";
		$user->save();
		
		// Get profile
		$profile = Profile::whereUser_id($id)->first();
		
		// Delete profile photo
		$this->removeProfileImage($profile->id);
		
        // redirect
        Session::flash('message_success', 'Successfully banned <b>' . $user->username . '</b>!');
        return Redirect::to('/profiles/' . $profile->id);
	}
	
	// Delete User
	/*
	 * Admin and id owner should have permission to execute this
	 *
	 */
	public function deleteUser($id){

		// If user executing deletion is not the id owner and not admin, redirect
		if((Auth::user()->role != "admin") && (Auth::id() != $id)){
			Session::flash('message', 'Sorry, you do not have permission to delete that user!');
			return Redirect::to('/home');
		}
		
		// Get user
		$user = User::find($id);

		// Remove all friendships
        App::make('FriendController')->remove_all_friendships($id);
		
		// Change user role
		$user->role = "deleted";
		$user->save();
		
		// Get profile
		$profile = Profile::whereUser_id($id)->first();
		
		// Delete profile photo
		$this->removeProfileImage($profile->id);
		
        // redirect
        Session::flash('message_success', 'Successfully deleted account for <b>' . $user->username . '</b>!');
        return Redirect::to('/home');
	}
	
	
	// Revive User
	/*
	 * Bring back users who were banned or deleted, downgrade an admin to user
	 *
	 */
	public function reviveUser($id){
		
		// Get user
		$user = User::find($id);
		
		// Change user role
		$user->role = "user";
		$user->save();
		
		// Get profile
		$profile = Profile::whereUser_id($id)->first();
		
        // redirect
        Session::flash('message_success', 'Successfully revived account for <b>' . $user->username . '</b>!');
        return Redirect::to('/profiles/' . $profile->id);
	}
	
	
	// Promote User
	/*
	 * Make user admin
	 *
	 */
	public function promoteUser($id){
		
		// Get user
		$user = User::find($id);
		
		// Change user role
		$user->role = "admin";
		$user->save();
		
		// Get profile
		$profile = Profile::whereUser_id($id)->first();
		
        // redirect
        Session::flash('message_success', 'Successfully made <b>' . $user->username . '</b> an admin!');
        return Redirect::to('/profiles/' . $profile->id);
	}
	
	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function userStatistics()
	{
	$user_no_profile = User::where('role', '=', "user_no_profile")->count();
	$user_admin = User::where('role', '=', "admin")->count();
	$user_active = User::where('role', '=', "user")->count();
	$user_banned = User::where('role', '=', "banned")->count();
	$user_deleted = User::where('role', '=', "deleted")->count();
	$user_all = User::count();
		
	
	$data['header'] = "USER Statistics";
	$data['title'] = "QuViews - BOSS - User Statistics";
	$data['user_no_profile'] = number_format($user_no_profile);
	$data['user_admin'] = number_format($user_admin);
	$data['user_active'] = number_format($user_active);
	$data['user_banned'] = number_format($user_banned);
	$data['user_deleted'] = number_format($user_deleted);
	$data['user_all'] = number_format($user_all);
	
	
	return View::make('/boss/user-statistics', $data);	
	}
	
	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function contentStatistics()
	{
	$item_all = Item::count();
	$item_movie = Item::where('category_id', '=', 1)->where('locked', '!=', "topic")->count();
	$item_tv = Item::where('category_id', '=', 2)->where('locked', '!=', "topic")->count();
	$item_music = Item::where('category_id', '=', 3)->where('locked', '!=', "topic")->count();
	$item_game = Item::where('category_id', '=', 4)->where('locked', '!=', "topic")->count();
	$item_book = Item::where('category_id', '=', 5)->where('locked', '!=', "topic")->count();
	$item_gadget = Item::where('category_id', '=', 6)->where('locked', '!=', "topic")->count();
	$item_all_topic = Item::where('locked', '=', "topic")->count();
	$item_all_locked = Item::where('locked', '=', "locked")->count();
	$item_all_editable = Item::where('locked', '=', "editable")->count();
	$channel_all = Channel::count();
	
		
	
	$data['header'] = "CONTENT Statistics";
	$data['title'] = "QuViews - BOSS - Content Statistics";
	$data['item_all'] = number_format($item_all);
	$data['item_movie'] = number_format($item_movie);
	$data['item_tv'] = number_format($item_tv);
	$data['item_music'] = number_format($item_music);
	$data['item_game'] = number_format($item_game);
	$data['item_book'] = number_format($item_book);
	$data['item_gadget'] = number_format($item_gadget);
	$data['channel_all'] = number_format($channel_all);
	$data['item_all_topic'] = number_format($item_all_topic);
	$data['item_all_locked'] = number_format($item_all_locked);
	$data['item_all_editable'] = number_format($item_all_editable);
	
	return View::make('/boss/content-statistics', $data);	
	}
	
	
	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function activityStatistics()
	{
	$review_all = Review::count();
	$post_all = Post::count();
	$reply_all = Reply::count();
	$message_all = Message::count();
		
	
	$data['header'] = "ACTIVITY Statistics";
	$data['title'] = "QuViews - BOSS - Activity Statistics";
	$data['review_all'] = number_format($review_all);
	$data['post_all'] = number_format($post_all);
	$data['reply_all'] = number_format($reply_all);
	$data['message_all'] = number_format($message_all);
	
	return View::make('/boss/activity-statistics', $data);	
	}
	
	// Suspend User
	// Add similar code to delete, but with additional details on suspension time span
	// This will need a new table to store suspension time span details
}
