<?php

class ItemController extends \BaseController {
    
        // Filters
        public function __construct()
        {
            // Examples
            // Source: http://laravel-recipes.com/recipes/41/registering-before-filters-on-a-controller
            // $this->beforeFilter('auth', ['except' => 'login']);
            // $this->beforeFilter('auth', ['on' => 'index']);
            
            // Source: 
            // Exit if not ajax
            // $this->beforeFilter('ajax', array('only' => 'store'));
            // Exit if not a valid _token
            // $this->beforeFilter('csrf', array('only' => 'store'));
            
            // pages that don't require log in to access
            $this->beforeFilter('auth', array('except' => array('show', 'doSearchitem', 'showSearchitem', 'doRandomitem', 'feed')));
            // pages only admin can access
            $this->beforeFilter('allow_only_admin', array('only' => array('index', 'destroy', 'removeItemImage')));
            // non-admin can only access editable item
            // ##Ed where is this defined? or is it redundant
            $this->beforeFilter('allow_user_if_editable', array('only' => 'edit'));
            // pages that don't require profile to access
            $this->beforeFilter('require_profile', array('except' => array('show', 'doSearchitem', 'showSearchitem', 'doRandomitem', 'feed')));
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
            // get all the items
			$items = Item::orderBy('id', 'DESC')->paginate(12);
            
            $data['header'] = "List all items";
            $data['title'] = "QuViews - List all items - Your Quick Reviews";

            // load the view and pass the data
            return View::make('items.index')->with('items', $items)->with($data);
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
            $data['header'] = "Add new Item";
            $data['title'] = "QuViews - Add new Item - Your Quick Reviews";
            
            return View::make('items.create', $data);
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
                 'name'       => 'required',
                 'creator'      => 'required',
                 'year' => 'required|numeric',
                 'category_id' => 'required|numeric',
                 'image' => 'required|image|max:3000',
                 'locked'       => 'required',
                 'review' => 'required',
                 'rank' => 'required'
             );
             
             $validator = Validator::make(Input::all(), $rules);
     
             // process the form
             if ($validator->fails()) {
                 return Redirect::to('items/create')
                     ->withErrors($validator)
                     ->withInput();
                     // ->withInput(Input::except('password'));
             } else {
                
                // Add Image for new Item
                $file = Input::file('image');
                $destinationPath = public_path() . '/images/items';
                $file_extension = $file->getClientOriginalExtension();
                $filename = str_random(30) . '.' . $file_extension;
                $filename = strtolower($filename);
                $uploadSuccess = $file->move($destinationPath, $filename);
				
                // Save Thumbnail
                
                // full image file path to original image we are trying to shrink
                $original_file_path = public_path() . '/images/items/' . $filename;
				
				// full image name file path for medium
				$medium_file = public_path() . '/images/items/medium/' . $filename;
                
                // full image name file path for thumbnail
                $thumb_file = public_path() . '/images/items/small/' . $filename;
				
                // path to folder where medium will be stored
                $medium_folder = public_path() . '/images/items/medium/';
                
                // path to folder where thumbnail will be stored
                $thumb_folder = public_path() . '/images/items/small/';
				
				// generate and store medium image
                $im_medium = App::make('ProfileController')->thumbnail($original_file_path, 400);
                $medium_uploadSuccess = App::make('ProfileController')->imageToFile($im_medium, $medium_file, $medium_folder);
                
				// generate and store small image
                $im_small = App::make('ProfileController')->thumbnail($original_file_path, 100);
                $thumbnail_uploadSuccess = App::make('ProfileController')->imageToFile($im_small, $thumb_file, $thumb_folder);
				
                
                // ##Ed Add condition to check if file was uploaded successfully
                
                $locked = Input::get( 'locked' );
                
                // Store new Item
                $item = new Item;
                $item->category_id = Input::get( 'category_id' );
                $item->name = Input::get( 'name' );
                $item->creator = Input::get( 'creator' );
                $item->year = Input::get( 'year' );
                $item->last_editor_id = Auth::id();
                $item->locked = $locked;
                $item->image = $filename;
                $item->save();
                
                // Save item id into variable for re-use
                $item_id = $item->id;
                
                if ($locked == "topic") {
                // Topics Don't Have Reviews
                } else {
                // Store Review for new Item
                $review = new Review;
                $review->item_id = $item_id;
                $review->author_id = Auth::id();
                $review->body = Input::get('review');
                $review->rank = Input::get('rank');
                $review->save();
                }

                
                // redirect
                Session::flash('message_success', 'Successfully added a new item!');
                return Redirect::to('items/' . $item_id);
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
            // get item
            $item = Item::find($id);
			
			// if no item is found, redirect
			if (is_null($item)){
				// redirect
				return Redirect::to('/home')->with('message', 'That item was not found');
			}
            
            // Get item category
            $item_category = Category::find($item->category_id);
            
            // get item review(s)
            // ##Ed I came up with 3 solutions
            
            // Solution 1
            // ##Ed by one to many relationship in model
            // cons: adding conditions is hard
            // $reviews = Item::find($id)->reviews;
            
            // Solution 2
            //All reviews for our item
            // cons: needs to run php function in View to pull users
            // $reviews =  Review::where('item_id', '=', $id)->orderBy('created_at', 'DESC')->paginate(5);
            
            // Solution 3
            // cons: $review->created_at->diffForHumans() doesn't work
            // update: fixed problem above, source: 
            $reviews = DB::table('users')
                        ->join('reviews', 'users.id', '=', 'reviews.author_id')
                        ->leftjoin('profiles', 'users.id', '=', 'profiles.user_id')
                        ->select('users.username', 'reviews.id', 'reviews.author_id', 'reviews.body',
                                 'reviews.rank', 'reviews.created_at', 'reviews.updated_at', 
                                 'profiles.id as profile_id', 'profiles.image as profile_image')
                        ->where('reviews.item_id', '=', $id)
                        ->orderBy('created_at', 'DESC')->take(3)->get();
                        
            $posts = DB::table('users')
                        ->join('posts', 'users.id', '=', 'posts.author_id')
                        ->leftjoin('replies', 'posts.id', '=', 'replies.post_id')
                        ->leftjoin('profiles', 'users.id', '=', 'profiles.user_id')
                        ->select(
                                 (DB::raw('count(replies.post_id) as replies_count'))
                                 ,'users.username', 'posts.id', 'posts.author_id', 'posts.body',
                                 'posts.created_at', 'posts.updated_at',
                                 'profiles.id as profile_id', 'profiles.image as profile_image')
                        ->where('posts.item_id', '=', $id)
                        ->groupBy('posts.id')
                        ->orderBy('created_at', 'DESC')->take(3)->get(); 
            
            // Count Reviews
            $review_count = Review::where('item_id', '=', $id)->count();
            
            // Count Posts
            $post_count = Post::where('item_id', '=', $id)->count();
            
            // Count Good reviews
            $good_review_count =  Review::where('item_id', '=', $id)->where('rank', '=', 'Good')->count();
            
            // Count Eh reviews
            $eh_review_count =  Review::where('item_id', '=', $id)->where('rank', '=', 'Eh')->count();
            
            // Count bad reviews
            $bad_review_count =  Review::where('item_id', '=', $id)->where('rank', '=', 'Bad')->count();
            
            // Count number of favorites
            $favorite_count = Profile::where('favorite_item_id', '=', $id)->count();
            
            // put item data into array
			if ($item->locked == "topic") {
		    $data['header'] = $item->name . " <span class=\"topic_label pull-right\">TOPIC</span>";
			} else {
            $data['header'] = $item->name . " (" . $item->year . ") <br /><span class=\"light_gray_font\"> by " . $item->creator . "</span>";
			}
            $data['title'] = "QuViews - " . $item->name . " (" . $item->year . ")" . " - Quick Reviews";
            $data['image_path'] = "images/items/medium/" . $item->image;
            $data['category'] = $item_category;
            $data['reviews'] = $reviews;
            $data['posts'] = $posts;
            $data['good_review_count'] =  $good_review_count;
            $data['eh_review_count'] =  $eh_review_count;
            $data['bad_review_count'] =  $bad_review_count;
            $data['item'] = $item;
            $data['review_count'] = $review_count;
            $data['post_count'] = $post_count;
            $data['favorite_count'] = $favorite_count;
            
            // show the view and pass the data
            return View::make('items.show')->with($data);
	}
        
        
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
            // get the item
            $item = Item::find($id);
            
            
            // Is item locked from editing
            $locked = $item->locked;
            
            if (($locked == "locked") AND (Auth::user()->role != "admin")) {
                Session::flash('message', 'Sorry, this item has been locked from further editing!');
                return Redirect::to('items/' . $id);
            }
            
            // If item is a topic non admins cant edit
            if((($item->locked)=="topic") AND (Auth::user()->role != "admin")){
                Session::flash('message', 'Sorry, Discussion topics can not be edited!');
                return Redirect::to('items/' . $id);
            }
			
			// If user is not admin, and hasnt done at least 5 reviews, they cant edit
		    $user_id = Auth::id();
			
		    // Find user by id
		    $user = User::find($user_id);
			// Count reviews by user
			$existing_review = Review::where('author_id','=', $user_id)->count();
			
            // Do the check
            if(($existing_review < 5) AND (Auth::user()->role != "admin")){
                Session::flash('message', 'Sorry, you need to make at least 5 reviews to get permission to edit items!');
                return Redirect::to('items/' . $id);
            }
			
			
			
            
            $data['header'] = "Edit Item";
            $data['title'] = "QuViews - Editing an Item";
            $data['image_path'] = "images/items/medium/" . $item->image;
            $data['item'] = $item;
            
            // show the edit form and pass the item data
            return View::make('items.edit')->with($data);
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
                 'name'         => 'required',
                 'creator'      => 'required',
                 'year'         => 'required|numeric',
                 'category_id'  => 'required|numeric',
                 'locked'  => 'required'
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
                return Redirect::to('items/' . $id . '/edit')
                    ->withErrors($validator)
                    ->withInput();
            } else {
                // Store new Item details
                $item = Item::find($id);
                
                $item->category_id = Input::get( 'category_id' );
                $item->name = Input::get( 'name' );
                $item->creator = Input::get( 'creator' );
                $item->year = Input::get( 'year' );
                $item->last_editor_id = Auth::id();
                $item->locked = Input::get( 'locked' );
                
                // If user passed image
                if(Input::hasfile('image')) {
                    // delete old image file
                    $image_name = $item->image;
					
					$old_filename = public_path() . '/images/items/' . $image_name;
					$old_filename_medium =  public_path() . '/images/items/medium/' . $image_name;
					$old_filename_thumbnail =  public_path() . '/images/items/small/' . $image_name;
                    
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
                    $destinationPath = public_path() . '/images/items';
                    $file_extension = $file->getClientOriginalExtension();
                    $filename = str_random(25) . '.' . $file_extension;
                    $filename = strtolower($filename);
                    $uploadSuccess = $file->move($destinationPath, $filename);
                    
                    $item->image =  $filename;
					
                    // Save Thumbnail
                    
                    // full image file path to original image we are trying to shrink
                    $original_file_path = public_path() . '/images/items/' . $filename;
					
					// full image name file path for medium
					$medium_file = public_path() . '/images/items/medium/' . $filename;
                    
                    // full image name file path for thumbnail
                    $thumb_file = public_path() . '/images/items/small/' . $filename;
                    
					// path to folder where medium will be stored
					$medium_folder = public_path() . '/images/items/medium/';
					
                    // path to folder where thumbnail will be stored
                    $thumb_folder = public_path() . '/images/items/small/';
					
					// generate and store medium image
					$im_medium = App::make('ProfileController')->thumbnail($original_file_path, 400);
					$medium_uploadSuccess = App::make('ProfileController')->imageToFile($im_medium, $medium_file, $medium_folder);
                    
					// generate and store small image
                    $im_small = App::make('ProfileController')->thumbnail($original_file_path, 100);
                    $thumbnail_uploadSuccess = App::make('ProfileController')->imageToFile($im_small, $thumb_file, $thumb_folder);
                }
                
                $item->save();
                
                // redirect
                Session::flash('message_success', 'Successfully updated item!');
                return Redirect::to('items/' . $id);
            }
	}
        
        
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
            // find item
            $item = Item::find($id);
            
            // delete item image file
            $image_name = $item->image;

		    $old_filename = public_path() . '/images/items/' . $image_name;
		    $old_filename_medium =  public_path() . '/images/items/medium/' . $image_name;
		    $old_filename_thumbnail =  public_path() . '/images/items/small/' . $image_name;
            
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
            
            // delete all related item reviews (one to many relationship)
            // ##Ed More sophisticated solution involving model can be found:
            // http://stackoverflow.com/questions/14174070/automatically-deleting-related-rows-in-laravel-eloquent-orm
            Review::where("item_id", $id)->delete();
			
			
			// Get all posts ids
			$posts = Post::where("item_id", $id)->get();
		    $posts_ids = $posts->modelKeys();
			
            // delete all related replies
            Reply::whereIn("post_id", $posts_ids)->delete();
			
            // then delete all related posts
            Post::where("item_id", $id)->delete();
			
            // delete all related replies
            // Reply::where("item_id", $id)->delete();
            
            // delete item
            $item->delete();
            
            // redirect
            Session::flash('message_success', 'Successfully deleted the item!');
            return Redirect::to('home');
	}
        
/* Additional functions */

    public function showSearchitem()
    {
        $data['header'] = "Search for an Item";
        $data['title'] = "QuViews - Search for an Item - Quick Reviews";
        
        return View::make('search-item', $data);
    }
    
    public function doSearchitem()
    {
    // Input Validation
    $validation = Validator::make(
        array(
            'keyword' => Input::get( 'keyword' ),
        ),
        array(
            'keyword' => array( 'required', ),
        )
    );
     
    if ( $validation->fails() ) {
        $errors = $validation->messages();
        
        return Redirect::to('home')->with('message', 'Sorry, a search term is required');
    }
        
	$keyword = Input::get('keyword');
        
        /*
         * ##Ed this messes up pagination
        if(empty($keyword)) {
            return Redirect::to('home')->with('message', 'Please enter a search term');
        }
        */
	
	//query Item model
        /* ##Ed
         * without pagination
	$items = Item::where('title', 'LIKE', '%'.$keyword.'%')->get();
	*/
        
        // without JOIN
        //$items = Item::where('name', 'LIKE', '%'.$keyword.'%')->orWhere('creator', 'LIKE', '%'.$keyword.'%')->paginate(5);
        
        
        $items = DB::table('categories')
                        ->join('items', 'categories.id', '=', 'items.category_id')
                        ->select('items.id', 'items.name', 'items.year', 'items.creator', 'categories.name as category_name', 'categories.image as category_image')
                        ->where('items.name', 'LIKE', '%'.$keyword.'%')
                        ->orWhere('items.creator', 'LIKE', '%'.$keyword.'%')
                        ->orderBy('items.created_at', 'ASC')->paginate(10);
                        
        $items_count = Item::where('items.name', 'LIKE', '%'.$keyword.'%')
                        ->orWhere('items.creator', 'LIKE', '%'.$keyword.'%')
                        ->count();
                        
        // handling plural wording of result / results
        if($items_count == 1) {
        $data['header'] = number_format($items_count) . " search result for: <span class=\"light_gray_font\">" . $keyword . "</span>";
        } else {
        $data['header'] = number_format($items_count) . " search results for: <span class=\"light_gray_font\">" . $keyword . "</span>";  
        }
        $data['title'] = "QuViews - Search for an Item - Your Quick Reviews";
        // ##Ed note we appended the keyword to returned results
        // if we do not append the keyword, the pagination links will not work
        // the pagination links require the key word to keep querying the results
        // Source: http://laravel.com/docs/5.0/pagination#appending-to-pagination-links
        $data['items_count'] = $items_count;
	$data['items'] = $items->appends(['keyword' => $keyword]);
	
        return View::make('search-item', $data);
    }
    
    public function doRandomitem()
    {
        // Get a random row from table
        /*
         * ##Ed Note, rand(1) gets an error
         * rand(x) where x is greater than records we get error
         * find a more reliable solution, this can cause error if item table is empty
         * Source: https://www.youtube.com/watch?v=TWkJcYm8-3Q
         * 
         * $items = Item::all()->random(3);
         */
        
        /*
         * ##Ed this is a more solid solution than the first one above
         * we do not get an error limiting to 1
         * Source: https://www.youtube.com/watch?v=uxDFMT18oLs
         *
         */
        $items = Item::orderBy(DB::raw('RAND()'))->limit(1)->get();
        
        // If we don't get an item
        if (!isset($items)) {
            return Redirect::to('home')->with('message', 'Sorry, no item match was found');
        }
        
        /*
         * ##Ed $items[0]->id works as intended, closes [{}] into {} so we can reference ->id
         * look closer into how it works
         * was tired when I wrote it
         *
         */
        $item_id = $items[0]->id;
        
        // Go to random item page
        return Redirect::to('items/' . $item_id);
    }
    
    public function favoriteItem($id)
    {
        $item_id = $id;
        
        $user_id = Auth::id();
	
        // Find user by id
        $user = User::find($user_id);
        $username = $user->username;
        
        // Get user's profile
	$profile = Profile::whereUser_id($user_id)->first();
        
        // Add favorite item to profile (update)
        $profile->favorite_item_id = $item_id;
        $profile->save();
        
        // redirect
        Session::flash('message_success', 'Added favorite item!');
        return Redirect::to('profiles/' . $profile->id);
    }
    
    public function unFavoriteItem()
    {
        $user_id = Auth::id();
	
        // Find user by id
        $user = User::find($user_id);
        $username = $user->username;
        
        // Get user's profile
	$profile = Profile::whereUser_id($user_id)->first();
        
        // Empty favorite item (update with 0)
        $profile->favorite_item_id = 0;
        $profile->save();
        
        // redirect
        Session::flash('message_success', 'Removed favorite item!');
        return Redirect::to('profiles/' . $profile->id);
    }
    
    public function showCheckitem()
    {
        $data['header'] = "Check if Item exists";
        $data['title'] = "QuViews - Check if Item exists - Quick Reviews";
        
        return View::make('check-item', $data);
    }
    
    // Check if there's an item match before adding new item
    public function doCheckitem()
    {
             // validate
             $rules = array(
                 'name'       => 'required',
                 'category_id' => 'required|numeric'
             );
             
             $validator = Validator::make(Input::all(), $rules);
             
             // process the form
             if ($validator->fails()) {
                 return Redirect::to('add-item')
                     ->withErrors($validator)
                     ->withInput();
             } else {
        
	$name = Input::get('name');
        $category_id = Input::get('category_id');
        
        // Get category
        $item_category = Category::find($category_id );
        
        $items = DB::table('categories')
                        ->join('items', 'categories.id', '=', 'items.category_id')
                        ->select('items.id', 'items.name', 'items.year', 'items.creator', 'categories.name as category_name', 'categories.image as category_image')
                        ->where('items.name', 'LIKE', '%'.$name.'%')
                        ->where('items.category_id', '=', $category_id)
                        ->orderBy('items.name', 'DESC')->paginate(6);
                        
        $items_count = Item::where('items.name', 'LIKE', '%'.$name.'%')
                        ->where('items.category_id', '=', $category_id)
                        ->count();
                        
        // handling plural wording of result / results
        if($items_count == 1) {
        $data['header'] = number_format($items_count) . " possible duplicate " . $item_category->name . " match found for: <br /><br /> <span class=\"light_gray_font\">" . $name . "</span>";
        } elseif($items_count == 0) {
        $data['header'] = "No possible duplicate " . $item_category->name . " match found for: <br /><br /> <span class=\"light_gray_font\">" . $name . "</span>";
        } else {
        $data['header'] = number_format($items_count) . " possible duplicate " . $item_category->name . " matches found for: <br /><br /> <span class=\"light_gray_font\">" . $name . "</span>";  
        }
        $data['title'] = "QuViews - Check if Item exists - Your Quick Reviews";
        // ##Ed note we appended the keyword to returned results
        // if we do not append the keyword, the pagination links will not work
        // the pagination links require the key word to keep querying the results
        // Source: http://laravel.com/docs/5.0/pagination#appending-to-pagination-links
        $data['items_count'] = $items_count;
	$data['items'] = $items->appends(array('name' => $name, 'category_id' => $category_id));
        $data['category'] = $item_category;
        $data['name'] = $name;
        $data['category_id'] = $category_id;
	
        return View::make('check-item', $data);
    }}
    
	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function feed($item_category, $item_feed_type)
	{
            if ($item_feed_type == "reviews") {
            
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
                        ->where('categories.name', '=', $item_category)
                        ->orderBy('created_at', 'DESC')->paginate(12);
                        
            // Count Reviews
            $review_count = DB::table('users')
                        ->join('reviews', 'users.id', '=', 'reviews.author_id')
                        ->leftjoin('profiles', 'users.id', '=', 'profiles.user_id')
                        ->leftjoin('items', 'reviews.item_id', '=', 'items.id')
                        ->leftjoin('categories', 'items.category_id', '=', 'categories.id')
                        ->select('users.username', 'reviews.id', 'reviews.author_id', 'reviews.body',
                                 'reviews.rank', 'reviews.created_at', 'reviews.updated_at',
                                 'profiles.id as profile_id', 'profiles.image as profile_image',
                                 'items.id as item_id', 'items.name as item_name', 'items.locked as item_locked',
                                 'categories.name as category_name', 'categories.image as category_image')
                        ->where('categories.name', '=', $item_category)
                        ->count();
                        
            // Get Category
            $category = Category::whereName($item_category)->first();
            
            // put data into array
            
            if ($review_count == 1){
            $data['header'] = "Recent " . $item_category . " Reviews Feed  <span class=\"light_gray_font pull-right\">" . number_format($review_count) . " review</span>";
            } else {
            $data['header'] = "Recent " . $item_category . " Reviews Feed  <span class=\"light_gray_font pull-right\">" . number_format($review_count) . " reviews</span>";
            }
            $data['title'] = "QuViews - Reviews Feed - Quick Reviews";
            $data['reviews'] = $reviews;
            $data['review_count'] = $review_count;
            $data['category'] = $category;
            
            // show the view and pass data
            return View::make('reviews-feed')->with($data);
                        
            } elseif($item_feed_type == "posts") {
                
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
                        ->where('categories.name', '=', $item_category)
                        ->groupBy('posts.id')
                        ->orderBy('created_at', 'DESC')->paginate(12);
                        
            // Count Posts
            $post_count = DB::table('users')
                        ->join('posts', 'users.id', '=', 'posts.author_id')
                        ->leftjoin('profiles', 'users.id', '=', 'profiles.user_id')
                        ->leftjoin('items', 'posts.item_id', '=', 'items.id')
                        ->leftjoin('categories', 'items.category_id', '=', 'categories.id')
                        ->select(
                                  'users.username', 'posts.id', 'posts.author_id', 'posts.body',
                                  'posts.created_at', 'posts.updated_at',
                                  'profiles.id as profile_id', 'profiles.image as profile_image',
                                  'items.id as item_id', 'items.name as item_name', 'items.locked as item_locked',
                                  'categories.name as category_name', 'categories.image as category_image')
                        ->where('categories.name', '=', $item_category)
                        ->count();
                        
            // Get Category
            $category = Category::whereName($item_category)->first();
            
            // put data into array
            if ($post_count == 1){
            $data['header'] = "Recent " . $item_category . " Discussion posts Feed  <span class=\"light_gray_font pull-right\">" . number_format($post_count) . " post</span>";
            } else {
            $data['header'] = "Recent " . $item_category . " Discussion posts Feed  <span class=\"light_gray_font pull-right\">" . number_format($post_count) . " posts</span>";
            }
            $data['title'] = "QuViews - Posts Feed - Discussion";
            $data['posts'] = $posts;
            $data['post_count'] = $post_count;
            $data['category'] = $category;
            
            // show the view and pass data
            return View::make('posts-feed')->with($data);
            }
        }
		
	// Make item image default
	public function makeItemImageDefault($id){
            // get profile
            $item = Item::find($id);
			
			// make item image default
			$item->image = "default.jpg";
			$item->save();
	}
		
/*
 * Remove Item Image
 *
 *
 * */

	public function removeItemImage($id)
	{
            // get item
            $item = Item::find($id);
            
            // delete item image file
            $image_name = $item->image;

		    $old_filename = public_path() . '/images/items/' . $image_name;
		    $old_filename_medium =  public_path() . '/images/items/medium/' . $image_name;
		    $old_filename_thumbnail =  public_path() . '/images/items/small/' . $image_name;
            
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
			
			// make item image default
			$this->makeItemImageDefault($id);

            // redirect
            Session::flash('message_success', 'Successfully removed item image!');
            return Redirect::to('/items/' . $id);
	}
}
