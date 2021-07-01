<?php

namespace App\Controllers;
use App\Controllers\Controller;
use App\Models\User;
use Respect\Validation\Validator as v; 
use App\Models\State;

class StatesController extends Controller{
	
	/**
	* List all users
	* 
	* @return
	*/
	public function index($request, $response,  $args){

            $states = State::all();
            return $this->view->render($response,'states/index.twig', ['state'=>$state]);
    

	}



	/**
	* Display a state
	* 
	* @return
	*/
	public function view($request, $response, $args){
	
	    $state = State::find( $args['id']);
		
		return $this->view->render($response,'states/view.twig', ['state'=>$state]);
		
	}


	
	/**
	* Create A New Post
	* 
	* @return
	*/
	public function add($request, $response,  $args){
	
        if($request->isPost()){
           
            /**
            * validate input before submission
            * @var 
            * 
            */ 
            $validation = $this->validator->validate($request, [
                'name' => v::notEmpty()	
            ]);


		//redirect if validation fails
		if($validation->failed()){
			$this->flash->addMessage('error', 'Validation Failed!'); 
		
			return $response->withRedirect($this->router->pathFor('states/add.twig')); 
		}
		
            $state = State::create([
                'name' => $request->getParam('name')
            ]);

                $this->flash->addMessage('success', 'State Added Successfully');
                //redirect to eg. states/view/8 
                return $response->withRedirect($this->router->pathFor('states.view', ['id'=>$state->id]));
           
        }
		return $this->view->render($response,'states/add.twig');
		
	}

    
	
	/**
	* Edit post
	* 
	* @return
	*/
	public function edit($request, $response,  $args){
	
              //find the post
            $state = State::find( $args['id']);

			//only admin and the person that created the post can edit or delete it.
			if ($this->auth->user()->role_id > 2 ) {
                
			$this->flash->addMessage('error', 'You are not allowed to perform this action!'); 
		
			return $this->view->render($response,'states/edit.twig', ['state'=>$state]);

			}

        //if form was submitted
        if($request->isPost()){
        
         $validation = $this->validator->validate($request, [
                'name' => v::notEmpty()	
            ]);
        //redirect if validation fails
		if($validation->failed()){
			$this->flash->addMessage('error', 'Validation Failed!'); 
		
			return $this->view->render($response,'states/edit.twig', ['state'=>$state]);
		}
		
            //save Data
            $state =  State::where('id', $args['id'])
                            ->update([
                                'name' => $request->getParam('name')
                                ]);
            
            if($state){
                $this->flash->addMessage('success', 'State Updated Successfully');
                //redirect to eg. states/view/8 
                return $response->withRedirect($this->router->pathFor('states.view', ['id'=>$args['id']]));
            }
        }
        
	    
		return $this->view->render($response,'states/edit.twig', ['state'=>$state]);
		
	}


/**
	* Delete a post
	* 
	* @return
	*/
	public function delete($request, $response,  $args){
		$state = State::find( $args['id']);
		
		//only owner and admin can delete
		if($this->auth->user()->role_id > 2 ) {
                
			$this->flash->addMessage('error', 'You are not allowed to perform this action!'); 
		
			return $this->view->render($response,'states/view.twig', ['state'=>$state]);

			}
			
			
		if($state->delete()){
			$this->flash->addMessage('success', 'State Deleted Successfully');
			return $response->withRedirect($this->router->pathFor('states.index'));
		}
	}

}