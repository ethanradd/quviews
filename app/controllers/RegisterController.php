<?php

class RegisterController extends BaseController {
    
    public function showRegister()
    {
        if(Auth::check()) {
            // redirect
            return Redirect::to('/home')->with('message', 'You can not register while logged in');
        }
        
        $data['header'] = "Sign Up";
        $data['title'] = "QuViews - Registration";

        return View::make('register', $data);
    }
    
    public function doRegister()
    {
    // validate
    // read more on validation at http://laravel.com/docs/validation
    $rules = array(
            'username'       => 'required|alpha_dash|unique:users,username|max:16',
            'email'      => 'required|email|unique:users,email',
            'password' => 'required|alpha_dash',
            'g-recaptcha-response' => 'required|captcha'
    );
    
    $validator = Validator::make(Input::all(), $rules);
    
    // process the form
    if ($validator->fails()) {
        return Redirect::to('/register')
            ->withErrors($validator)
            ->withInput();
    } else {
        // save user
        $user = new User;
        $user->email = Input::get('email');
        $user_email = $user->email; // for emailing
        $user->username = Input::get('username');
        $user->password = Hash::make(Input::get('password'));
        $user->validation = str_random(30);
        $validation_code =  $user->validation; // for emailing
        $user->role = "unvalidated";
        $user->save();
        
      Mail::send('emails.verify', ['validation_code' => $validation_code, 'user_email' => $user_email], function($message) {
            $message->to(Input::get('email'), Input::get('username'))
                ->subject('Verify your QuViews account');
        });
        
        // Passing multiple variables
        // Source: http://stackoverflow.com/questions/20110757/laravel-pass-more-than-one-variable-to-view
        $data['header'] = "Thanks! You Signed Up";
        $data['title'] = "QuViews - Registration Complete - Your Quick Reviews";
        $data['theEmail'] = Input::get('email');
        
        return View::make ('thanks')->with($data);
    }
}


    public function doVerify($verification_code, $user_email)
    {
        if( ! $verification_code)
        {
           return Redirect::to('home')->with('message', 'Sorry, no valid verification code was detected');
        }
        
        if( ! $user_email)
        {
           return Redirect::to('home')->with('message', 'Sorry, no valid user email was detected');
        }

        $user = User::whereValidation($verification_code)->whereEmail($user_email)->first();

        if ( ! $user)
        {
            return Redirect::to('home')->with('message', 'Sorry, could not verify the user, please retry the verification link sent to your email');
        }
        
        $user->role = "user_no_profile";
        $user->validation = 0;
        $user->save();
        
        // redirect
        Session::flash('message_success', 'You have successfully verified your account, you can now Log In!');
        return Redirect::to('login');
    }
    
    
    public function showPasswordReset()
    {
        $data['header'] = "Reset Password";
        $data['title'] = "QuViews - Reset Password";

        return View::make('password-reset', $data);
    }
    
    
    public function doPasswordReset()
    {
    // validate
    // read more on validation at http://laravel.com/docs/validation
    $rules = array(
            'email_username'       => 'required',
            'g-recaptcha-response' => 'required|captcha'
    );
    
    $validator = Validator::make(Input::all(), $rules);
    
    // process the form
    if ($validator->fails()) {
        return Redirect::to('/password-reset')
            ->withErrors($validator)
            ->withInput();
    } else {
        
        // Check if passed variable is email
        $rules = array(
                'email_username'       => 'required|email',
        );
        
        $validator = Validator::make(Input::all(), $rules);
        
        // process the form
        if ($validator->fails()) {
            // passed variable is username
            // Find User by username
            $user = User::whereUsername(Input::get('email_username'))->first();
        } else {
            // passed variable is email address
            // Find User by email address
            $user = User::whereEmail(Input::get('email_username'))->first();
        }
        
        if ($user) {
            // User is found
            
            // Only generate a new validation code if user has already been verified
            if ($user->role != "unvalidated") {
            $user->validation = str_random(30);
            }
            
            $validation_code =  $user->validation; // for emailing
            $user_email = $user->email; // for emailing
            $username = $user->username; // for emailing
            
            $user->save();
            
            // Mail user with unique key for resetting password
            Mail::send('emails.reset-password', ['validation_code' => $validation_code, 'user_email' => $user_email, 'username' => $username], function($message) use ($user_email, $username) {
                  $message->to($user_email, $username)
                  ->subject('Reset your QuViews Password');
            });
            
            // redirect
            Session::flash('message', 'Please use the link sent to your email address to reset your password');
            return Redirect::to('home');
            
        } else {
            // No User Found
            // redirect
            Session::flash('message', 'Sorry, we could not find a user with those credentials');
            return Redirect::to('/password-reset');
        }
    }
    }
    
    
    public function showPasswordResetNow($verification_code, $user_email)
    {
        if( ! $verification_code)
        {
           return Redirect::to('home')->with('message', 'Sorry, no valid verification code was detected');
        }
        
        if( ! $user_email)
        {
           return Redirect::to('home')->with('message', 'Sorry, no valid user email was detected');
        }
        
        $user = User::whereValidation($verification_code)->whereEmail($user_email)->first();
        
        if ( ! $user)
        {
            return Redirect::to('home')->with('message', 'Sorry, could not verify the user, please retry the password reset link sent to your email');
        }
        
        $data['header'] = "Reset Password";
        $data['title'] = "QuViews - Reset Password";
        $data['email'] = $user_email;
        
        return View::make('password-reset-now', $data);
    }
    
    public function doPasswordResetNow()
    {
    // validate
    $rules = array(
            'password' => 'required|alpha_dash',
    );
    
    $validator = Validator::make(Input::all(), $rules);
    
    // process the form
    if ($validator->fails()) {
        return Redirect::back()
            ->withErrors($validator)
            ->withInput();
    } else {
        // Update password
        
        $user_email = Input::get('email');
        
        // Get user
        $user = User::whereEmail($user_email)->first();
        
        $user->password = Hash::make(Input::get('password'));
        
        // Only make validation = 0 if user is already verfied
        if ($user->role != "unvalidated") {
        $user->validation = 0;
        }
        
        $user->save();
        
        // redirect
        Session::flash('message_success', 'You have successfully reset your password');
        return Redirect::to('/login');
    }
    }

}

?>