<?php

class ChannelController extends \BaseController {

        // Filters
        public function __construct()
        {
            // pages that don't require log in to access
            $this->beforeFilter('auth', array('except' => array('show')));
            // pages only admin can access
            $this->beforeFilter('allow_only_admin', array('only'  => array('index', 'edit', 'create')));
            // pages that don't require profile to access
            $this->beforeFilter('require_profile', array('except' => array('show')));
        }
        
        
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
            // get all the channels
		    $channels = Channel::orderBy('id', 'DESC')->paginate(12);
            
            $data['header'] = "List all channels";
            $data['title'] = "QuViews - List all channels";
            $data['channels'] = $channels;
            
            // load the view and pass the nerds
            return View::make('channels.index')->with($data);
	}
        
        
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
            $data['header'] = "Add new Channel";
            $data['title'] = "QuViews - Add new Channel";
            
            return View::make('channels.create', $data);
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
                 'category_id' => 'required|numeric',
                 'name'       => 'required',
                 'source'      => 'required',
                 'description' => 'required',
                 'country'     => 'required',
                 'live'     => 'required',
                 'image' => 'required|image|max:3000'
             );
             
             $validator = Validator::make(Input::all(), $rules);
     
             // process the form
             if ($validator->fails()) {
                 return Redirect::to('channels/create')
                     ->withErrors($validator)
                     ->withInput();
                     // ->withInput(Input::except('password'));
             } else {
                // Store new Item
                $channel = new Channel;
                $channel->category_id = Input::get( 'category_id' );
                $channel->name = Input::get( 'name' );
                $channel->source = stripcslashes(Input::get( 'source' ));
                $channel->description = Input::get( 'description' );
                $channel->country = Input::get( 'country' );
                $channel->live = Input::get( 'live' );
                
                // Add Image for channel logo
                $file = Input::file('image');
				$destinationPath = public_path() . '/images/channels';
                $file_extension = $file->getClientOriginalExtension();
                $filename = str_random(25) . '.' . $file_extension;
                $filename = strtolower($filename);
                $uploadSuccess = $file->move($destinationPath, $filename);
                
                // Record image name in database
                $channel->image = $filename;
                
                // Save
                $channel->save();
                
                // redirect
                Session::flash('message_success', 'Successfully added a new Channel!');
                return Redirect::to('/channels');
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
            // get channel
            $channel = Channel::find($id);
			
			// if no item is found, redirect
			if (is_null($channel)){
				// redirect
				return Redirect::to('/home')->with('message', 'That channel was not found');
			}
            
            // get category
            $category = Category::find($channel->category_id);
            
            // put channel data into array
            $data['header'] = $channel->name;
            $data['title'] = "QuViews - " . $channel->name;
            $data['channel'] = $channel;
            $data['category'] = $category;
            $data['image_path'] = "images/channels/" . $channel->image;
            
            // show the view and pass the nerd to it
            return View::make('channels.show')->with($data);
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
            // get channel
            $channel = Channel::find($id);
            
            // get category
            $category = Category::find($channel->category_id);
            
            $data['header'] = "Edit Channel";
            $data['title'] = "QuViews - Editing Channel";
            $data['image_path'] = "images/channels/" . $channel->image;
            $data['channel'] = $channel;
            $data['category'] = $category;
            
            // show the edit form
            return View::make('channels.edit')->with($data);
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
                 'name'       => 'required',
                 'category_id' => 'required|numeric',
                 'source'      => 'required',
                 'description' => 'required',
                 'country'     => 'required',
                 'live'     => 'required'
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
                return Redirect::to('channels/' . $id . '/edit')
                    ->withErrors($validator)
                    ->withInput();
            } else {
                // Store new Channel new details
                $channel = Channel::find($id);
                $channel->name = Input::get( 'name' );
                $channel->category_id = Input::get( 'category_id' );
                $channel->source = stripcslashes(Input::get( 'source' ));
                $channel->description = Input::get( 'description' );
                $channel->country = Input::get( 'country' );
                $channel->live = Input::get( 'live' );
                
                
                // If user passed image
                if(Input::hasfile('image')) {
                    // delete old image file
                    $image_name = $channel->image;
                    $old_filename = public_path() . '/images/channels/' . $image_name;
                    
                    if (File::exists($old_filename)) {
                        File::delete($old_filename);
                    }
                    
                    // add new image file
                    $file = Input::file('image');
                    $destinationPath = public_path() . '/images/channels';
                    $file_extension = $file->getClientOriginalExtension();
                    $filename = str_random(25) . '.' . $file_extension;
                    $filename = strtolower($filename);
                    $uploadSuccess = $file->move($destinationPath, $filename);
                    
                    $channel->image =  $filename;
                }
                
                
                
                $channel->save();
                
                // redirect
                Session::flash('message_success', 'Successfully updated channel!');
                return Redirect::to('channels/' . $id);
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
            // get channel
            $channel = Channel::find($id);
            
            // delete channel logo image file
            $image_name = $channel->image;
            $filename = public_path(). '/images/channels/' . $image_name;
            
            if (File::exists($filename)) {
                File::delete($filename);
            }
            
            // delete channel
            $channel->delete();
            
            // redirect
            Session::flash('message_success', 'Successfully deleted the channel!');
            return Redirect::to('channels');
	}


}
