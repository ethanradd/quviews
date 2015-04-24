<?php

class ReplyController extends \BaseController {
        // Filters
        public function __construct()
        {
            // pages that don't require log in to access
            $this->beforeFilter('auth', array('except' => array('show')));
            // pages only admin can access
            $this->beforeFilter('allow_only_admin', array('only' => 'index'));
            // pages that don't require profile to access
            $this->beforeFilter('require_profile', array('except' => array('show')));
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
            // get all the replies
			$replies = Reply::orderBy('id', 'DESC')->paginate(12);
            
            $data['header'] = "List all post replies";
            $data['title'] = "QuViews - List all post replies";
            $data['replies'] = $replies;
            
            // load the view and pass the data
            return View::make('replies.index')->with($data);
	}
        
        
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create($id, $quote_id = NULL)
	{
            
            // If there is a quoted reply, get reply
            if ($quote_id) {
                $quote = Reply::find($quote_id);
            } else {
                $quote = NULL;
            }
            
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
                        
            $item = Item::find($post->item_id);
            
            $category = Category::find($item->category_id);
            
            // put item data into array
			if ($item->locked == "topic") {
		    $data['header'] = "Discuss - " . $item->name . " <span class=\"topic_label pull-right\">TOPIC</span>";
			} else {
            $data['header'] = "Discuss - " . $item->name . " (" . $item->year . ")<br /><span class=\"light_gray_font\"> by " . $item->creator . "</span>";
			}
            $data['title'] = "QuViews - Add new Post - Your Quick Reviews";
            $data['image_path'] = "images/items/" . $item->image;
            $data['post'] = $post;
            $data['quote'] = $quote;
            $data['item'] = $item;
            $data['category'] = $category;
            
            return View::make('replies.create')->with($data);
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
                 'passed_post_id' => 'required|numeric',
                 'body' => 'required',
             );
             
             // We can assume this will always be here because it's passed as hidden input
             // We need it for the error redirect
             $post_id = Input::get( 'passed_post_id' );
             
             $validator = Validator::make(Input::all(), $rules);
     
             // process the form
             if ($validator->fails()) {
                 return Redirect::to('replies/create/' . $post_id)
                     ->withErrors($validator)
                     ->withInput();
             } else {
                
                // Get original post
                $post = Post::find($post_id);
                
                // Get quoted reply, if it has been passed
                $quote_id = Input::get( 'passed_quote_id' );
                
                if ($quote_id) {
                    $quote = Reply::find($quote_id);
                }
                
                // Store new Reply
                $reply = new Reply;
                $reply->author_id = Auth::id();
                $reply->body = Input::get( 'body' );
                $reply->post_id = $post_id;
                $reply->post_author_id = $post->author_id;
                $reply->post_author_read = 0;
                
                if ($quote_id) {
                $reply->quote_id = $quote_id;
                $reply->quote_author_id = $quote->author_id;
                $reply->quote_author_read = 0;
                }
                
                $reply->save();
                
                // redirect
                Session::flash('message_success', 'Successfully added a new Reply!');
                return Redirect::to('posts/' . $post_id);
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
            // get the reply
            $reply = Reply::find($id);
			
			// if no item is found, redirect
			if (is_null($reply)){
				// redirect
				return Redirect::to('/home')->with('message', 'That reply was not found');
			}
            
            // Get original post
            $post = Post::find($reply->post_id);
            
            // get item
            $item = Item::find($post->item_id);
            
            // Get category
            $item_category = Category::find($item->category_id);
            
            // get the reply
            $reply = DB::table('users')
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
                        ->where('replies.id', '=', $id)->first();
                        
                        
            // Get Original post (joined with other tables)
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
                        ->where('posts.id', '=', $reply->post_id)->first();
            
            // put item data into array
			if ($item->locked == "topic") {
		    $data['header'] = $item->name . " <span class=\"topic_label pull-right\">TOPIC</span>";
			} else {
            $data['header'] = $item->name . " (" . $item->year . ") <br /><span class=\"light_gray_font\"> by " . $item->creator . "</span>";
			}
            $data['title'] = "QuViews - " . $item->name . " (" . $item->year . ")" . " - Reply";
            $data['item'] = $item;
            $data['category'] = $item_category;
            $data['image_path'] = "images/items/" . $item->image;
            $data['post'] = $post;
            $data['reply'] = $reply;
            
        return View::make('replies.show')->with($data);
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
            // get the reply
            $reply = Reply::find($id);
            
            // get original post
            $post = Post::find($reply->post_id);
            
            // Check if session user is author or admin
            if ((Auth::user()->role != "admin") && (Auth::id() != $reply->author_id)) {
                Session::flash('message', 'Only the post author can edit it!');
                return Redirect::to('posts/'. $post->id);
            }
            
            // get post author
            $post_author = User::find($post->author_id);
            
            // get reply author
            $reply_author = User::find($reply->author_id);
            
            // If there is a quoted reply, get reply
            if ($reply->quote_id != 0) {
                $quote = Reply::find($reply->quote_id);
            } else {
                $quote = NULL;
            }
            
            // Was post edited?
            if ($post->created_at == $post->updated_at) {
                $post_timestamp = $post->created_at->diffForHumans();
            } else {
                $post_timestamp = "<b>Edited:</b> " . $post->updated_at->diffForHumans();
            }
            
            // Was reply edited?
            if ($reply->created_at == $reply->updated_at) {
                $reply_timestamp = $reply->created_at->diffForHumans();
            } else {
                $reply_timestamp = "<b>Edited:</b> " . $reply->updated_at->diffForHumans();
            }
            
            // get item
            $item = Item::find($post->item_id);
            
            // Get item category
            $item_category = Category::find($item->category_id);
            
            $data['header'] = "Edit Reply - " . $item->name . " (" . $item->year . ")<br /><span class=\"light_gray_font\"> by " . $item->creator . "</span>";
            $data['title'] = "QuViews - Editing a Reply";
            $data['image_path'] = "images/items/" . $item->image;
            $data['post'] = $post;
            $data['reply'] = $reply;
            $data['post_timestamp'] = $post_timestamp;
            $data['reply_timestamp'] = $reply_timestamp;
            $data['post_author'] = $post_author;
            $data['item'] = $item;
            $data['category'] = $item_category;
            $data['quote'] = $quote;
            
        return View::make('replies.edit')->with($data);
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
                return Redirect::to('replies/' . $id . '/edit')
                    ->withErrors($validator)
                    ->withInput();
            } else {
                // Store new Reply
                $reply = Reply::find($id);
                $reply->body = Input::get( 'body' );
                $reply->save();
                
                // redirect
                Session::flash('message_success', 'Successfully updated Reply!');
                return Redirect::to('posts/' . $reply->post_id);
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
            // find reply
            $reply = Reply::find($id);
            
            // get post id
            $post_id = $reply->post_id;
            
            // Check if session user is author or admin
            if ((Auth::user()->role != "admin") && (Auth::id() != $reply->author_id)) {
                Session::flash('message', 'Only the post author can edit it!');
                return Redirect::to('posts/'. $post_id);
            }
            
            // delete item
            // $reply->delete();
            
            // Since we have a complicated quoting system between replies, don't delete it, keep it
            // but rewrite it as "// Reply Deleted"
            $reply->body = "// Reply Deleted";
            
            // Remove any quote
            $reply->quote_id = "";
            $reply->quote_author_id = "";
            $reply->quote_author_read = "";
            
            $reply->save();
            
            // redirect
            Session::flash('message_success', 'Successfully deleted the reply!');
            return Redirect::to('posts/' . $post_id);
	}


}
