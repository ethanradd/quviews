<?php

class ReviewController extends \BaseController {

        // Filters
        public function __construct()
        {
            // pages that don't require log in to access
            $this->beforeFilter('auth', array('except' => array('feed', 'show')));
            // pages only admin can access
            $this->beforeFilter('allow_only_admin', array('only' => 'index'));
            // pages that don't require profile to access
            $this->beforeFilter('require_profile', array('except' => array('feed', 'show')));
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
            // get all the reviews // ##Ed later add function to sort reviews by item
			$reviews = Review::orderBy('id', 'DESC')->paginate(12);
            
            $data['header'] = "List all reviews";
            $data['title'] = "QuViews - Quick Reviews";
            $data['reviews'] = $reviews;
            
            // load the view and pass the nerds
            return View::make('reviews.index')->with($data);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create($id)
	{
            // get item
            $item = Item::find($id);
            
            // If item is a topic, you can't review it
            if(($item->locked)=="topic" ){
                Session::flash('message', 'Sorry, Discussion topics can not be reviewed!');
                return Redirect::to('items/' . $id);
            }
            
            // if user has already posted a review redirect
            // $profile = $user->profile()->first;
            $review = Review::whereAuthor_id(Auth::id())->whereItem_id($id)->first();
            if ($review) {
                Session::flash('message', 'Sorry, you have already reviewed this item, you can edit your current review');
                return Redirect::to('reviews/' . $review->id);
            }
            
            // get item category
            $item_category = Category::find($item->category_id);
            
            $data['header'] = "Review - " . $item->name . " (" . $item->year . ") <br /><span class=\"light_gray_font\"> by " . $item->creator . "</span>";
            $data['title'] = "QuViews - Review " . $item->name;
            $data['image_path'] = "images/items/medium/" . $item->image;
            $data['category'] = $item_category;
            $data['item'] = $item;
            
            return View::make('reviews.create', $data);
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
                 'passed_item_id' => 'required|numeric',
                 'body' => 'required',
                 'rank' => 'required'
             );
             
             // We can assume this will always be here because it's passed as hidden input
             // We need it for the error redirect
             $item_id = Input::get( 'passed_item_id' );
             
             $validator = Validator::make(Input::all(), $rules);
             
             // process the form
             if ($validator->fails()) {
                 return Redirect::to('reviews/create/' . $item_id)
                     ->withErrors($validator)
                     ->withInput();
             } else {
                
                // Check if user already posted a review
                $user_id = Auth::id();
                $existing_review = Review::where('item_id','=', $item_id)->where('author_id','=', $user_id)->count();
                
                if ($existing_review > 0) {
                    // A review already exists from this user
                    return Redirect::to('items/' . $item_id)->with('message', 'Sorry, you have already reviewed this item!');
                }
                
                // Store new Review
                $review = new Review;
                $review->item_id = $item_id;
                $review->author_id = Auth::id();
                $review->body = Input::get( 'body' );
                $review->rank = Input::get( 'rank' );
                $review->save();
                
                // redirect
                Session::flash('message_success', 'Successfully added a new Review!');
                return Redirect::to('reviews/feed/' . $item_id);
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
            // get review
            $review = Review::find($id);
			
			// if no report is found, redirect
			if (is_null($review)){
				// redirect
				return Redirect::to('/home')->with('message', 'That review was not found');
			}
            
            // get item
            $item = Item::find($review->item_id);
            
            // Get category
            $item_category = Category::find($item->category_id);
            
            // Get Review
            $review = DB::table('users')
                        ->join('reviews', 'users.id', '=', 'reviews.author_id')
                        ->leftjoin('profiles', 'users.id', '=', 'profiles.user_id')
                        ->leftjoin('items', 'reviews.item_id', '=', 'items.id')
                        ->leftjoin('categories', 'items.category_id', '=', 'categories.id')
                        ->select('users.username', 'reviews.id', 'reviews.author_id', 'reviews.body',
                                 'reviews.rank', 'reviews.created_at', 'reviews.updated_at',
                                 'profiles.id as profile_id', 'profiles.image as profile_image',
                                 'items.id as item_id', 'items.name as item_name', 'items.locked as item_locked',
                                 'categories.name as category_name', 'categories.image as category_image')
                        ->where('reviews.id', '=', $id)->first();
            
            
            // put data into array
            $data['header'] = $item->name . " (" . $item->year . ") <br /><br /><span class=\"light_gray_font\"> by " . $item->creator . "</span>";
            $data['title'] = "QuViews - " . $item->name . " (" . $item->year . ")" . " - Quick Review";
            $data['item'] = $item;
            $data['category'] = $item_category;
            $data['image_path'] = "images/items/medium/" . $item->image;
            $data['review'] = $review;
            
            // show the view and pass the data to it
            return View::make('reviews.show')->with($data);
	}
        
        
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
            // get the review
            $review = Review::find($id);
            
            // Check if session user is author or admin
            if ((Auth::user()->role != "admin") && (Auth::id() != $review->author_id)) {
                Session::flash('message', 'Only the review author can edit it!');
                return Redirect::to('reviews/'. $id);
            }
            
            // get item
            $item = Item::find($review->item_id);
            
            // Get category
            $item_category = Category::find($item->category_id);
            
            $data['header'] = "Edit Review - " . $item->name . " (" . $item->year . ")<br /><span class=\"light_gray_font\"> by " . $item->creator . "</span>";
            $data['title'] = "QuViews - Edit Review";
            $data['image_path'] = "images/items/medium/" . $item->image;
            $data['review'] = $review;
            $data['category'] = $item_category;
            $data['item'] = $item;
            
            // show the edit form and pass data
            return View::make('reviews.edit')->with($data);
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
                 'body' => 'required',
                 'rank' => 'required'
            );
            
            $validator = Validator::make(Input::all(), $rules);
    
            // process the form
            if ($validator->fails()) {
                return Redirect::to('reviews/' . $id . '/edit')
                    ->withErrors($validator)
                    ->withInput();
            } else {
                // Store new Review data
                $review = Review::find($id);
                $review->body = Input::get( 'body' );
                $review->rank = Input::get('rank');
                $review->save();
                
                // redirect
                Session::flash('message_success', 'Successfully updated Review!');
                return Redirect::to('reviews/'. $id);
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
            // find review
            $review = Review::find($id);
            
            // delete review
            $review->delete();
            
            // redirect
            Session::flash('message_success', 'Successfully deleted the Review!');
            return Redirect::to('home');
	}
        
/* Additional Controllers */

	/**
	 * Show Feed Of Review.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function feed($id)
	{
            // get item
            $item = Item::find($id);
            
            // If item is a topic, you can't see any reviews for it (there are none allowed)
            if(($item->locked)=="topic" ){
                Session::flash('message', 'Sorry, Discussion topics do not have reviews');
                return Redirect::to('items/' . $id);
            }
            
            // get item category
            $item_category = Category::find($item->category_id);
            
            // Get current reviews for item
            // ##Ed getting reply count for each reply, idea inspired by:
            // Source: http://laravel.io/forum/08-28-2014-join-count-group
            $reviews = DB::table('users')
                        ->join('reviews', 'users.id', '=', 'reviews.author_id')
                        ->leftjoin('profiles', 'users.id', '=', 'profiles.user_id')
                        ->select('users.username', 'reviews.id', 'reviews.author_id', 'reviews.body',
                                 'reviews.rank', 'reviews.created_at', 'reviews.updated_at',
                                 'profiles.id as profile_id', 'profiles.image as profile_image')
                        ->where('reviews.item_id', '=', $id)
                        ->orderBy('created_at', 'DESC')->paginate(15);
                        
            // Count Reviews
            $review_count = Review::where('item_id', '=', $id)->count();
            
            $data['header'] = "Quick Reviews Feed - " . $item->name . " (" . $item->year . ") <br /><span class=\"light_gray_font\"> by " . $item->creator . "</span>";
            $data['title'] = "QuViews - Read Reviews";
            $data['image_path'] = "images/items/medium/" . $item->image;
            $data['category'] = $item_category;
            $data['item'] = $item;
            $data['reviews'] = $reviews;
            $data['review_count'] = $review_count;
            
            return View::make('reviews.feed', $data);
	}


}
