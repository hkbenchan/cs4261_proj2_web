<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH.'libraries/REST_Controller.php');

class Event extends REST_Controller {
	
	function __construct() {
		// Call the REST_Controller constructor
        parent::__construct();
		$this->load->model('event_model','event');
	}
	
	
	public function lists_get(){
		
		// get the user's event list by FB_ID
		$FB_ID = $this->input->get('FB_ID',true);
		if (is_numeric($FB_ID)) {
			$result = $this->event->get_event_lists_all($FB_ID);
			if ($result['code']>0) {
				$this->response($result,200);
			} else {
				$this->response(array('code'=>-1, 'message'=>'Zero result'),404);
			}
		} else {
			$this->response(array('code'=>-1, 'message'=>'Incorrect ID'),401);
		}
		
	}
	
	public function create_post(){
				
		$this->load->helper(array('form','security'));
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('FB_ID','required|numeric');
		$this->form_validation->set_rules('Title','required|min_length[1]|max_length[255]|xss_clean');
		$this->form_validation->set_rules('Description','xss_clean');
		$this->form_validation->set_rules('Date','required|xss_clean');
		//$this->form_validation->set_rules('');
		$User_id = FALSE;
		
		if ($this->form_validation->run() === FALSE) {
			$this->response(array('code'=>-1, 'message'=>'Please check your input again.'), 404);
		} else {
			// get the User_ID
			$this->load->model('membership_model','membership');
			
			$User_id = $this->membership->user_id_by_FB($this->input->post('FB_ID'));
			if ($User_id == FALSE) {
				$this->response(array('code'=>-1, 'message'=>'ID not found'), 401);
			}
						
			// create the event first
			$data = array(
				'Title' => $this->input->post('Title'),
				'Description' => $this->input->post('Description'),
				'Date' => $this->input->post('Date'),
			);
			$event_id = $this->event->create_event($data);
		}
		
		if ($event_id == FALSE) {
			$this->response(array('code'=>-1, 'message'=>'Cannot create new event.', 500));
		}
		
		if ($User_id == FALSE) {
			$this->response(array('code'=>-1, 'message'=>'ID not found'), 401);
		}
		// add owner to UserOwnsEvent
		$data = array(
			'User_ID' => $User_id,
			'Event_ID' => $event_id,
		);
		$result = $this->event->addOwnerEvent($data);
		
		if ($result == FALSE) {
			$this->response(array('code'=>-1,'message'=>'Cannot set onwer'),500);
		}
		
		// for each included user (FB_ID), add them to the UserInvitedEvent
		$invited_users = $this->input->post('invited');
		$i = 0; $j = 0;
		if ($invited_users == FALSE) {
			// finish
		} else {
			foreach ($invited_users as $id) {
				$User_id = FALSE;
				$User_id = $this->membership->user_id_by_FB(xss_clean($id));
				if ($User_id != FALSE) {
					$data = array(
						'User_ID' => $User_id,
						'Event_ID' => $event_id,
					);
					$result = $this->event->addInviteEvent($data);
					if ($result == TRUE)
						$j++;
				}
				$i++;
			}
		}
		
		$this->response(array('code'=>1,'message'=>'created event','invite'=>$i, 'invited'=>$j),200);

	}
	
	public function vote_movies_post(){
		
		// check if event exists (own)
			// true
			// check if the movie is in the event's movie list
			// check if user is already made a vote
				// true
				// reduce one vote from the event's movie list (old)
			// add one vote from the event's movie list (new)
			// modify the entry inside the UserOwnsEvents
		// check if event exists (invited)
			// true
			// check if the movie is in the event's movie list
			// check if user is already made a vote
				// true
				// reduce one vote from the event's movie list (old)
			// add one vote from the event's movie list (new)
			// modify the entry inside the UserInvitedEvents
			
	}
	
	public function edit_movie_lists_post(){
		
		// check if event exists (own)
			// true
			// compare the database lists with new lists
				// for each movie needs to remove
					// find if anyone voted it
						// set it to -1
					// remove the movies from the EventMovie
				// for each movie needs to add
					// add the movies to the EventMovie
		
	}
	
	public function finalize_movie_post(){
		
	}
}