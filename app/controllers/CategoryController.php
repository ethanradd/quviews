<?php

class CategoryController extends \BaseController {
    
        // Filters
        public function __construct()
        { 
            // pages that don't require log in to access
            $this->beforeFilter('auth', array('except' => array('')));
            // pages only admin can access
            $this->beforeFilter('allow_only_admin', array('only'  => array('index', 'edit', 'create')));
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
            // get all the categories
		    $categories = Category::orderBy('id', 'DESC')->paginate(12);
            
            $data['header'] = "List all categories";
            $data['title'] = "QuViews - List all categories";
            $data['categories'] = $categories;
            
            // load the view and pass the nerds
            return View::make('categories.index')->with($data);
	}
        
        
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
            $data['header'] = "Add new Category";
            $data['title'] = "QuViews - Add new Category";
            
            return View::make('categories.create', $data);
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
                 'image'      => 'required'
             );
             
             $validator = Validator::make(Input::all(), $rules);
     
             // process the form
             if ($validator->fails()) {
                 return Redirect::to('categories/create')
                     ->withErrors($validator)
                     ->withInput();
             } else {
                
                // Store new Category
                $category = new Category;
                $category->name = Input::get( 'name' );
                $category->image = Input::get( 'image' );
                $category->save();
                
                // Save item id into variable for re-use
                $category_id = $category->id;
                
                // redirect
                Session::flash('message_success', 'Successfully added a new category!');
                return Redirect::to('categories');
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
            // Not needed
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
            $category = Category::find($id);
            
            $data['header'] = "QuViews - Edit Category";
            $data['title'] = "QuViews - Editing a Category";
            $data['category'] = $category;
            
            // show the edit form and pass the category data
            return View::make('categories.edit')->with($data);
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
                 'image'      => 'required'
             );
            
            $validator = Validator::make(Input::all(), $rules);
            
            // process the form
            if ($validator->fails()) {
                return Redirect::to('categories/' . $id . '/edit')
                    ->withErrors($validator)
                    ->withInput();
            } else {
                // Store new Category details
                $category = Category::find($id);
                $category->name = Input::get( 'name' );
                $category->image = Input::get( 'image' );
                $category->save();
                
                // redirect
                Session::flash('message_success', 'Successfully updated category!');
                return Redirect::to('categories');
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
            // find category
            $category = Category::find($id);
            
            // delete category
            $category->delete();
            
            // redirect
            Session::flash('message_success', 'Successfully deleted the category!');
            return Redirect::to('categories');
	}   
}
