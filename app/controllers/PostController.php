<?php

class PostController extends \BaseController {

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
            // get all the posts // ##Ed later add function to sort posts by item
            $posts = Post::orderBy('id', 'DESC')->paginate(12);
            
            $data['header'] = "List all posts";
            $data['title'] = "QuViews - Discussion";
            $data['posts'] = $posts;
            
            // load the view and pass the nerds
            return View::make('posts.index')->with($data);
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
            
            // get item category
            $item_category = Category::find($item->category_id);
            
            // put item data into array
			if ($item->locked == "topic") {
		    $data['header'] = "Discuss - " . $item->name . " <span class=\"topic_label pull-right\">TOPIC</span>";
			} else {
            $data['header'] = "Discuss - " . $item->name . " (" . $item->year . ") <br /><span class=\"light_gray_font\"> by " . $item->creator . "</span>";
			}
            $data['title'] = "QuViews - Discuss " . $item->name;
            $data['image_path'] = "images/items/medium/" . $item->image;
            $data['category'] = $item_category;
            $data['item'] = $item;
            
            return View::make('posts.create', $data);
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
             );
             
             // We can assume this will always be here because it's passed as hidden input
             // We need it for the error redirect
             $item_id = Input::get( 'passed_item_id' );
             
             $validator = Validator::make(Input::all(), $rules);
             
             // process the form
             if ($validator->fails()) {
                 return Redirect::to('posts/create/' . $item_id)
                     ->withErrors($validator)
                     ->withInput();
             } else {
                
                // Store new Post
                $post = new Post;
                $post->item_id = $item_id;
                $post->author_id = Auth::id();
                $post->body = Input::get( 'body' );
                $post->save();
                
                // redirect
                Session::flash('message_success', 'Successfully added a new Post!');
                return Redirect::to('posts/feed/' . $item_id);
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

            $post = Post::find($id);
			
			// if no item is found, redirect
			if (is_null($post)){
				// redirect
				return Redirect::to('/home')->with('message', 'That post was not found');
			}
            
            // get item
            $item = Item::find($post->item_id);
            
            // Get category
            $item_category = Category::find($item->category_id);
            
            // get post join
            $post = DB::table('users')
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
                        ->where('posts.id', '=', $id)->first();
            
            // get post replies
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
                        ->where('replies.post_id', '=', $id)
                        ->orderBy('created_at', 'ASC')->paginate(15);
                        
            // Count Replies
            $reply_count = Reply::where('replies.post_id', '=', $id)->count();
                        
            // put data into array
            // put item data into array
			if ($item->locked == "topic") {
		    $data['header'] = $item->name . " <span class=\"topic_label pull-right\">TOPIC</span>";
			} else {
            $data['header'] = $item->name . " (" . $item->year . ") <br /><span class=\"light_gray_font\"> by " . $item->creator . "</span>";
			}
            $data['title'] = "QuViews - " . $item->name . " (" . $item->year . ")" . " - Post";
            $data['item'] = $item;
            $data['category'] = $item_category;
            $data['image_path'] = "images/items/medium/" . $item->image;
            $data['post'] = $post;
            $data['replies'] = $replies;
            $data['reply_count'] = $reply_count;
            
            // show the view and pass the data to it
            return View::make('posts.show')->with($data);
	}
        
        
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
            // get the post
            $post = Post::find($id);
            
            // Check if session user is author or admin
            if ((Auth::user()->role != "admin") && (Auth::id() != $post->author_id)) {
                Session::flash('message', 'Only the post author can edit it!');
                return Redirect::to('posts/'. $id);
            }
            
            // get item
            $item = Item::find($post->item_id);
            
            // Get category
            $item_category = Category::find($item->category_id);
            
            $data['header'] = "Edit Post - " . $item->name . " (" . $item->year . ")<br /><span class=\"light_gray_font\"> by " . $item->creator . "</span>";
            $data['title'] = "QuViews - Edit Post";
            $data['image_path'] = "images/items/medium/" . $item->image;
            $data['post'] = $post;
            $data['category'] = $item_category;
            $data['item'] = $item;
            
            // show the edit form and pass data
            return View::make('posts.edit')->with($data);
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
            );
            
            $validator = Validator::make(Input::all(), $rules);
    
            // process the form
            if ($validator->fails()) {
                return Redirect::to('posts/' . $id . '/edit')
                    ->withErrors($validator)
                    ->withInput();
            } else {
                // Store new Post data
                $post = Post::find($id);
                $post->body = Input::get( 'body' );
                $post->save();
                
                // redirect
                Session::flash('message_success', 'Successfully updated Post!');
                return Redirect::to('posts/'. $id);
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
            // find post
            $post = Post::find($id);
            
            // ##Ed optionally allow editing post to: post removed and keep replies
            
            // delete all related replies
            Reply::where("post_id", $id)->delete();
            
            // delete item
            $post->delete();
            
            // redirect
            Session::flash('message_success', 'Successfully deleted the Post!');
            return Redirect::to('home');
	}
        
/* Additional Controllers */

	/**
	 * Show Feed Of Posts.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function feed($id)
	{
            // get item
            $item = Item::find($id);
            
            // get item category
            $item_category = Category::find($item->category_id);
            
            // Get current posts for item
            // ##Ed getting reply count for each post, idea inspired by:
            // Source: http://laravel.io/forum/08-28-2014-join-count-group
            $posts = DB::table('users')
                        ->join('posts', 'users.id', '=', 'posts.author_id')
                        ->leftjoin('replies', 'posts.id', '=', 'replies.post_id')
                        ->leftjoin('profiles', 'users.id', '=', 'profiles.user_id')
                        ->select(
                                 (DB::raw('count(replies.post_id) as replies_count'))
                                 ,'users.username', 'posts.id', 'posts.author_id', 'posts.body', 'posts.created_at', 'posts.updated_at'
                                 ,'profiles.id as profile_id', 'profiles.image as profile_image')
                        ->where('posts.item_id', '=', $id)
                        ->groupBy('posts.id')
                        ->orderBy('created_at', 'DESC')->paginate(15);
                        
            // Count Posts
            $post_count = Post::where('item_id', '=', $id)->count();
            
            // put item data into array
			if ($item->locked == "topic") {
		    $data['header'] = "Discussion Feed - " . $item->name . " <span class=\"topic_label pull-right\">TOPIC</span>";
			} else {
            $data['header'] = "Discussion Feed - " . $item->name . " (" . $item->year . ") <br /><span class=\"light_gray_font\"> by " . $item->creator . "</span>";
			}
            $data['title'] = "QuViews - " . $item->name . " - Discussion Posts";
            $data['image_path'] = "images/items/medium/" . $item->image;
            $data['category'] = $item_category;
            $data['item'] = $item;
            $data['posts'] = $posts;
            $data['post_count'] = $post_count;
            
            return View::make('posts.feed', $data);
	}


}
