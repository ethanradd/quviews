<?php

class MessageController extends \BaseController {
    
        // Filters
        public function __construct()
        {
            // pages that don't require log in to access
            $this->beforeFilter('auth', array('except' => array()));
            // pages only admin can access
            $this->beforeFilter('allow_only_admin', array('only' => 'index'));
            // pages that don't require profile to access
            $this->beforeFilter('require_profile', array('except' => array()));
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
            // get all the messages
            // ##Ed disable this function, messages are privare, even admin shouldn't be able to view
            $messages = Message::all();
            
            $data['header'] = "List all messages";
            $data['title'] = "QuViews - List all messages - Your Quick Reviews";
            
            // load the view and pass the data
            return View::make('messages.index')->with('messages', $messages)->with($data);
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create($receiver_id)
	{
            // Is User follwong us / or is user admin?
            $friendship_following_me = App::make('FriendController')->check_friendship($receiver_id, Auth::id());
            
            /*
            if (($friendship_following_me == 0) AND (Auth::user()->role != "admin")) {
                Session::flash('message', 'You can only send messages to users who follow you!');
                return Redirect::to('home');
            }
            */
            
            // Get receiver User
            $receiver = User::find($receiver_id);
            
            // Get receiver profile
            $profile = Profile::whereUser_id($receiver_id)->first();
            
            // Get existing messages
            $messages = DB::table('users')
                        ->join('messages', 'users.id', '=', 'messages.sender_id')
                        ->leftjoin('profiles', 'users.id', '=', 'profiles.user_id')
                        ->select(
                                 'users.username', 'users.role',
                                 'messages.id', 'messages.sender_id', 'messages.receiver_id', 'messages.body', 'messages.receiver_read', 'messages.created_at', 'messages.updated_at',
                                 'profiles.id as profile_id', 'profiles.image as profile_image')
                        ->where('messages.receiver_id', '=', $receiver_id)
                        ->where('messages.sender_id', '=', Auth::id())
                        ->orWhere('messages.receiver_id', '=', Auth::id())
                        ->where('messages.sender_id', '=', $receiver_id)
                        ->orderBy('created_at', 'DESC')->paginate(15);
                        
                $message_count = Message::where('receiver_id', '=', $receiver_id)
                        ->where('sender_id', '=', Auth::id())
                        ->orWhere('receiver_id', '=', Auth::id())
                        ->where('sender_id', '=', $receiver_id)
                        ->count();
            
            $data['header'] = "Messages with " . $receiver->username;
            $data['title'] = "QuViews - Create new Message - Your Quick Reviews";
            $data['receiver'] = $receiver;
            $data['profile'] = $profile;
            $data['image_path'] = "images/profiles/medium/" . $profile->image;
            $data['messages'] = $messages;
            $data['message_count'] = $message_count;
            $data['friendship_following_me'] = $friendship_following_me;
            
            return View::make('messages.create', $data);
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
                 'passed_receiver_id' => 'required|numeric',
                 'passed_receiver_profile_id' => 'required|numeric',
                 'body' => 'required',
             );
             
             // We can assume this will always be here because it's passed as hidden input
             // We need it for the error redirect
             $receiver_id = Input::get( 'passed_receiver_id' );
             
             $receiver_profile_id = Input::get( 'passed_receiver_profile_id' );
             
            // CHECK: Is User follwing us / or is user admin?
            $friendship_following_me = App::make('FriendController')->check_friendship($receiver_id, Auth::id());
            
            if (($friendship_following_me == 0) AND (Auth::user()->role != "admin")) {
                Session::flash('message', 'You can only send messages to users who follow you!');
                return Redirect::back();
            }
             
             $validator = Validator::make(Input::all(), $rules);
             
             // process the form
             if ($validator->fails()) {
                 return Redirect::to('messages/create/' . $receiver_id)
                     ->withErrors($validator)
                     ->withInput();
             } else {
                
                // Store new Post
                $message = new Message;
                $message->sender_id = Auth::id();
                $message->receiver_id = $receiver_id;
                $message->body = Input::get( 'body' );
                $message->receiver_read = 0;
                $message->save();
                
                // redirect
                Session::flash('message_success', 'Successfully sent a Message!');
                return Redirect::to('messages/create/' . $receiver_id);
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
            // No Need to show individual messages
	}
        
        
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
            // Users shouldn't be able to edit messages
	}
        
        
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
            // Users shouldn't be able to edit messages
	}
        
        
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
            // find message
            $message = Message::find($id);
            
            // delete message
            $message->delete();
            
            // redirect
            Session::flash('message_success', 'Successfully deleted the message!');
            return Redirect::back();
	}
        
/* Additional Functions */

        // Counting unread messages from specific sender
        public function count_specific_unread_messages($sender_id, $receiver_id)
        {
            $unread_count = Message::where('receiver_id', '=', $receiver_id)->where('sender_id', '=', $sender_id)->where('receiver_read', '=', 0)->count();
            
            return $unread_count;
        }
        
	public function listMessages()
	{
            // Get user ID
            $user_id = Auth::id();
            
            // Get user
            $user = User::find($user_id);
            
            // Get profile
            $profile = Profile::whereUser_id($user_id)->first();
            
            // Get existing messages
            $messages = DB::table('users')
                        ->join('messages', 'users.id', '=', 'messages.sender_id')
                        ->leftjoin('profiles', 'users.id', '=', 'profiles.user_id')
                        ->select(
                                 (DB::raw('count(messages.sender_id) as messages_count')),
                                 'messages.sender_id', 'messages.receiver_id',
                                 'users.id as user_id', 'users.username', 'users.role',
                                 'profiles.id as profile_id', 'profiles.image as profile_image')
                        ->where('messages.receiver_id', '=',$user_id)
                        // ->where('messages.receiver_read', '=', 0)
                        ->groupBy('messages.sender_id')
                        ->orderBy('messages.created_at', 'DESC')->paginate(15);
                        
                        
            // ##Ed tried to get count of message senders, was too confusing,
            // simply count total of all messages sent, including read ones
            
            // $message_count = Message::where('receiver_id', '=', $user_id)->groupBy('sender_id')->count();
            $message_count = Message::where('receiver_id', '=', $user_id)->count();
            
            
            $unread_message_count = Message::where('receiver_id', '=', $user_id)->where('receiver_read', '=', 0)->count();
                        
            if ($unread_message_count == 1){
            $data['header'] = $user->username . "'s received Messages  <span class=\"light_gray_font \">[INBOX]</span> <span class=\"light_gray_font pull-right\">" . number_format($unread_message_count) . " unread msg</span>";
            } elseif ($unread_message_count > 1){
            $data['header'] = $user->username . "'s received Messages  <span class=\"light_gray_font \">[INBOX]</span> <span class=\"light_gray_font pull-right\">" . number_format($unread_message_count) . " unread msgs</span>";
            } else {
            $data['header'] = $user->username . "'s received Messages  <span class=\"light_gray_font \">[INBOX]</span> <span class=\"light_gray_font pull-right\"> no unread messages</span>";  
            }
            
            $data['title'] = "QuViews - Messages List";
            $data['messages'] = $messages;
            $data['message_count'] = $message_count;
            $data['image_path'] = "images/profiles/medium/" . $profile->image;
            $data['profile'] = $profile;
            
            // show the view and pass data
            return View::make('messages-list')->with($data);
        }
        
	public function listMessagesOutbox()
	{
            // Get user ID
            $user_id = Auth::id();
            
            // Get user
            $user = User::find($user_id);
            
            // Get profile
            $profile = Profile::whereUser_id($user_id)->first();
            
            // Get existing sent messages
            $messages = DB::table('users')
                        ->join('messages', 'users.id', '=', 'messages.receiver_id')
                        ->leftjoin('profiles', 'users.id', '=', 'profiles.user_id')
                        ->select(
                                 (DB::raw('count(messages.sender_id) as messages_count')),
                                 'messages.sender_id', 'messages.receiver_id',
                                 'users.id as user_id', 'users.username',
                                 'profiles.id as profile_id', 'profiles.image as profile_image')
                        ->where('messages.sender_id', '=',$user_id)
                        ->groupBy('messages.receiver_id')
                        ->orderBy('messages.created_at', 'DESC')->paginate(15);
                        
                        
            // ##Ed tried to get count of message senders, was too confusing,
            // simply count total of all messages sent, including read ones
            
            // $message_count = Message::where('receiver_id', '=', $user_id)->groupBy('sender_id')->count();
            $message_count = Message::where('sender_id', '=', $user_id)->count();
            
            $unread_message_count = Message::where('sender_id', '=', $user_id)->where('receiver_read', '=', 0)->count();
                        
            if ($unread_message_count == 1){
            $data['header'] = $user->username . "'s sent Messages  <span class=\"light_gray_font \">[OUTBOX]</span> <span class=\"light_gray_font pull-right\">" . number_format($unread_message_count) . " unread msg</span>";
            } elseif ($unread_message_count > 1){
            $data['header'] = $user->username . "'s sent Messages  <span class=\"light_gray_font \">[OUTBOX]</span> <span class=\"light_gray_font pull-right\">" . number_format($unread_message_count) . " unread msgs</span>";
            } else {
            $data['header'] = $user->username . "'s sent Messages  <span class=\"light_gray_font \">[OUTBOX]</span> <span class=\"light_gray_font pull-right\"> no unread messages</span>";  
            }
            
            $data['title'] = "QuViews - Messages List";
            $data['messages'] = $messages;
            $data['message_count'] = $message_count;
            $data['image_path'] = "images/profiles/medium/" . $profile->image;
            $data['profile'] = $profile;
            
            // show the view and pass data
            return View::make('messages-list-outbox')->with($data);
        }
}
