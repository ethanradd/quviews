<?php

class ReportController extends \BaseController {
    
        // Filters
        public function __construct()
        { 
            // pages that don't require log in to access
            $this->beforeFilter('auth', array('except' => array('')));
            // pages only admin can access
            $this->beforeFilter('allow_only_admin', array('only'  => array('index', 'edit', 'show')));
            // pages that don't require profile to access
            $this->beforeFilter('require_profile', array('except' => array('')));
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
            // get all the reports
			$reports = Report::orderBy('id', 'DESC')->paginate(12);
            
            $data['header'] = "List all reports";
            $data['title'] = "QuViews - List all reports";
            $data['reports'] = $reports;
            
            // load the view and pass the data
            return View::make('reports.index')->with($data);
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create($item_id, $item_type)
	{
            // Get reported post
            if ($item_type == "post") {
                $item = Post::find($item_id);
                
                // Get reported user
                $reported_user = User::find($item->author_id);
            } elseif ($item_type == "reply") {
                $item = Reply::find($item_id);
                
                // Get reported user
                $reported_user = User::find($item->author_id);
            } elseif ($item_type == "review") {
                $item = Review::find($item_id);
                
                // Get reported user
                $reported_user = User::find($item->author_id);
            } elseif ($item_type == "item") {
                $item = Item::find($item_id);
                
                // Get reported user
                $reported_user = User::find($item->last_editor_id);
            } elseif ($item_type == "profile") {
                $item = Profile::find($item_id);
                
                // Get reported user
                $reported_user = User::find($item->user_id);
            }
            
            $data['header'] = "Add new Report";
            $data['title'] = "QuViews - Add new Report";
            $data['item_id'] = $item_id;
            $data['item_type'] = $item_type;
            
            $data['reported_user'] = $reported_user;
            $data['item'] = $item;
            $data['item_type'] = $item_type;
            
            return View::make('reports.create', $data);
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
                 'reported_user_id' => 'required|numeric',
                 'reason'       => 'required',
                 'item_id' => 'required|numeric',
                 'item_type'       => 'required'
             );
             
             $validator = Validator::make(Input::all(), $rules);
             
             // process the form
             if ($validator->fails()) {
                 return Redirect::back()
                     ->withErrors($validator)
                     ->withInput();
             } else {
                // Store new Report
                $report = new Report;
                $report->author_id = Auth::id();
                $report->reported_user_id = Input::get( 'reported_user_id' );
                $report->reason = Input::get( 'reason' );
                $report->item_id = Input::get( 'item_id' );
                $report->item_type = Input::get( 'item_type' );
                /*
                $report->resolved = "";
                $report->action = "";
                $report->admin_id = "";
                */
                $report->save();
                
                // redirect
                Session::flash('message_success', 'Successfully added a new report!');
                return Redirect::to('home');
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
            // get the report
            $report = Report::find($id);
			
			// if no report is found, redirect
			if (is_null($report)){
				// redirect
				return Redirect::to('/home')->with('message', 'That report was not found');
			}
			
			// get the reported user
			$reported_user = User::find($report->reported_user_id);
			$reported_user_profile = Profile::whereUser_id($reported_user->id)->first();
			
			// get the report author
			$author = User::find($report->author_id);
			$author_profile = Profile::whereUser_id($author->id)->first();
			
			// get item
            if ($report->item_type == "post") {
                $item = Post::find($report->item_id);
				$direct_link = "/posts/" . $report->item_id;
				
            } elseif ($report->item_type == "reply") {
                $item = Reply::find($report->item_id);
				$direct_link = "/replies/" . $report->item_id;
                
            } elseif ($report->item_type == "review") {
                $item = Review::find($report->item_id);
				$direct_link = "/reviews/" . $report->item_id;
                
            } elseif ($report->item_type == "item") {
                $item = Item::find($report->item_id);
				$direct_link = "/items/" . $report->item_id;
                
            } elseif ($report->item_type == "profile") {
                $item = Profile::find($report->item_id);
				$direct_link = "/profiles/" . $report->item_id;
				
            }
			
			// If item was not found
			if(is_null($item)) {
                Session::flash('message', 'Sorry, that <b>reported item</b> was not found! Maybe it was already DELETED');
                return Redirect::to('/reports/');
			}
			
            $data['header'] = "View Report";
            $data['title'] = "QuViews - View Report";
            $data['report'] = $report;
			$data['reported_user'] = $reported_user;
			$data['reported_user_profile'] = $reported_user_profile;
			$data['author'] = $author;
			$data['author_profile'] = $author_profile;
            $data['item'] = $item;
			$data['direct_link'] = $direct_link;
            
            return View::make('reports.show', $data);
	}
        
        
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
            // get the report
            $report = Report::find($id);
            
            // Only admin can edit
            if((Auth::user()->role) != "admin"){
                Session::flash('message', 'Sorry, you do not have access to edit a report!');
                return Redirect::to('home');
            }
            
            $data['header'] = "Edit Report";
            $data['title'] = "QuViews - Editing a Report";
            $data['report'] = $report;
            
            // show the edit form and pass the data
            return View::make('reports.edit')->with($data);
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
                 'action'       => 'required',
            );
            
            $validator = Validator::make(Input::all(), $rules);
            
            // process the form
            if ($validator->fails()) {
                return Redirect::to('reports/' . $id)
                    ->withErrors($validator)
                    ->withInput();
            } else {
                // Store new Report details
                $report = Report::find($id);
				
				// Save reported user id, for messaging
				$reported_user_id = $report->reported_user_id;
				
                $report->resolved = 1;
                $report->action = Input::get( 'action' );
                $report->admin_id = Auth::id();
                $report->save();
				
				// Message the user
                $message = new Message;
                $message->sender_id = Auth::id();
                $message->receiver_id = $reported_user_id;
                $message->body =
				"Moderation Message. <br /><br />
				This is to inform you there was a moderation one one of your activities, this is the mod action report: <br /><br /> [ "
				. Input::get( 'action' ) .
				" ] <br /><br /> please adhere to the rules and regulations of the site as per the Terms Of Service to avoid getting banned in the future.";
				
                $message->receiver_read = 0;
                $message->save();
                
                // redirect
                Session::flash('message_success', 'Successfully updated a report!');
                return Redirect::to('reports');
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
            // find report
            $report = Report::find($id);
            
            // delete report
            $report->delete();
            
            // redirect
            Session::flash('message_success', 'Successfully deleted the report!');
            return Redirect::to('reports');
	}
}
