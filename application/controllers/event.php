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
		
		$this->form_validation->set_rules('FB_ID','','required|numeric');
		$this->form_validation->set_rules('Title','','required|min_length[1]|max_length[255]|xss_clean');
		$this->form_validation->set_rules('Description','','xss_clean');
		$this->form_validation->set_rules('Date','','required|xss_clean');
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
		
		$this->response(array('code'=>1,'message'=>'created event','event_id'=>$event_id,'invite'=>$i, 'invited'=>$j),200);

	}
	
	public function vote_movies_post(){
		
		$this->load->helper(array('form','security'));
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('FB_ID','','required|numeric');
		$this->form_validation->set_rules('Event_ID','', 'required|numeric');
		$this->form_validation->set_rules('Movie_ID','', 'required|numeric');
		
		if ($this->form_validation->run() == FALSE) {
			$this->response(array('code'=>-1, 'message'=>'Please check your input again.'), 404);
		}
		
		// get the User_ID
		$this->load->model('membership_model','membership');
		
		$User_id = $this->membership->user_id_by_FB($this->input->post('FB_ID'));
		if ($User_id == FALSE) {
			$this->response(array('code'=>-1, 'message'=>'ID not found'), 401);
		}
		
		// check if event exists (own)
		$result = $this->event->findOwnEvent($User_id, $this->input->post('Event_ID'));
		if ($result != FALSE) {
			// check if the movie is in the event's movie list
			
			$result_EM = $this->event->findEventMovie($this->input->post('Event_ID'), $this->input->post('Movie_ID'));
			
			if ($result_EM == FALSE) {
				$this->response(array('code'=>-1, 'message'=>'Movie is not included in the list'), 401);
			}
			
			// check if user is already made a vote
			$r = $result->first_row('array');
			$r_EM = $result_EM->first_row('array');
			if ($r['Movie_vote'] != -1) {
				// reduce one vote from the event's movie list (old)
				$data = array(
					'Event_ID' => $r['Event_ID'],
					'Movie_ID' => $r['Movie_vote'],
				);
				$r2 = $this->event->reduceVote($data);
				if ($r2 == FALSE)
					$this->response(array('code'=>-1, 'message'=>'Fail to remove vote'), 500);
			}
			
			// add one vote from the event's movie list (new)
			
			$data = array(
				'Event_ID' => $r['Event_ID'],
				'Movie_ID' => $this->input->post('Movie_ID'),
			);
			$r2 = $this->event->addVote($data, $r_EM['no_of_vote']+1);
			
			if ($r2 == FALSE)
				$this->response(array('code'=>-1, 'message'=>'Fail to add vote'), 500);
			
			// modify the entry inside the UserOwnsEvents
			
			$data = array(
				'Event_ID' => $r['Event_ID'],
				'User_ID' => $User_id,
			);
			
			$r2 = $this->event->updateOwnEventVote($data, $this->input->post('Movie_ID'));
			
			if ($r2 == FALSE)
				$this->response(array('code'=>-1, 'message'=>'Fail to modify vote record'), 500);
			else
				$this->response(array('code'=>1, 'message'=>'success'), 200);
			
			
		} else {
			// check if event exists (invited)
			$result = $this->event->findInviteEvent($User_id, $this->input->post('Event_ID'));
			if ($result != FALSE) {
				// check if the movie is in the event's movie list
				
				$result_EM = $this->event->findEventMovie($this->input->post('Event_ID'), $this->input->post('Movie_ID'));

				if ($result_EM == FALSE) {
					$this->response(array('code'=>-1, 'message'=>'Movie is not included in the list'), 401);
				}

				// check if user is already made a vote
				$r = $result->first_row('array');
				$r_EM = $result_EM->first_row('array');
				if ($r['Movie_vote'] != -1) {
					// reduce one vote from the event's movie list (old)
					$data = array(
						'Event_ID' => $r['Event_ID'],
						'Movie_ID' => $r['Movie_vote'],
					);
					$r2 = $this->event->reduceVote($data);
					if ($r2 == FALSE)
						$this->response(array('code'=>-1, 'message'=>'Fail to remove vote'), 500);
				}

				// add one vote from the event's movie list (new)

				$data = array(
					'Event_ID' => $r['Event_ID'],
					'Movie_ID' => $this->input->post('Movie_ID'),
				);
				$r2 = $this->event->addVote($data, $r_EM['no_of_vote']+1);

				if ($r2 == FALSE)
					$this->response(array('code'=>-1, 'message'=>'Fail to add vote'), 500);

				// modify the entry inside the UserOwnsEvents

				$data = array(
					'Event_ID' => $r['Event_ID'],
					'User_ID' => $User_id,
				);

				$r2 = $this->event->updateInviteEventVote($data, $this->input->post('Movie_ID'));

				if ($r2 == FALSE)
					$this->response(array('code'=>-1, 'message'=>'Fail to modify vote record'), 500);
				else
					$this->response(array('code'=>1, 'message'=>'success'), 200);
			} else {
				$this->response(array('code'=>-1,'message'=>'You do not have permission'), 401);
			}
		}
	}
	
	public function edit_movie_lists_post(){
		
		
		$this->load->helper(array('form','security'));
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('FB_ID','','required|numeric');
		$this->form_validation->set_rules('Event_ID','','required|numeric');
		
		if ($this->form_validation->run() == FALSE) {
			$this->response(array('code'=>-1, 'message'=>'Please check your input again.'), 404);
		}
		
		// get the User_ID
		$this->load->model('membership_model','membership');
		$Event_ID = $this->input->post('Event_ID');
		$User_id = $this->membership->user_id_by_FB($this->input->post('FB_ID'));
		if ($User_id == FALSE) {
			$this->response(array('code'=>-1, 'message'=>'ID not found'), 401);
		}
		
		// check if event exists (own)
		
		$result = $this->event->findOwnEvent($User_id, $Event_ID);
		if ($result != FALSE) {
			$Movie_ID = $this->input->post('Movie_ID');
			if ($Movie_ID == false) {
				$Movie_ID = array();
			}
			
			$processed = array();
			$j = count($Movie_ID);
			for ($i = 0; $i< $j; $i++) {
				$processed[$i] = FALSE;
			}
			
			$result2 = $this->event->getMovieList($Event_ID);
			
			// compare the database lists with new lists
			foreach ($result2 as $id => $row) {
				// for each movie needs to remove
				$tmp = array_search($row['Movie_ID'],$Movie_ID);
				if ($tmp == FALSE) {
					$this->event->removeMovieFromList($Event_ID, $row['Movie_ID']);
				} else {
					$processed[$tmp] = TRUE;
				}
			}
			
			// for each movie needs to add
			for ($i = 0; $i< $j; $i++) {
				if ($processed[$i] == FALSE) {
					// add the movies to the EventMovie
					$add_movie = xss_clean($Movie_ID[$i]);
					$this->event->addMovieFromList($Event_ID, $add_movie);
				}
			}
			
			$this->response(array('code'=>1, 'message'=>'List updated.'), 200);	
				
		} else {
			$this->response(array('code'=>-1, 'message'=>'You do not own this event.'), 401);
		}
		
	}
	
	public function finalize_movie_post(){
		$this->response(array(),404);
	}
	
	public function get_event_get() {
		// $this->load->helper(array('form','security'));
		// 		$this->load->library('form_validation');
		// 		
		// 		$this->form_validation->set_rules('FB_ID','','required|numeric');
		// 		$this->form_validation->set_rules('Event_ID','','required|numeric');
		// 		
		
		if (!(is_numeric($this->input->get('FB_ID')) && (is_numeric($this->input->get('Event_ID'))) )) {
			$this->response(array('code'=>-1, 'message'=>'Please check your input again.'), 404);
		}
		
		// get the User_ID
		$this->load->model('membership_model','membership');
		
		$User_id = $this->membership->user_id_by_FB((int)$this->input->get('FB_ID'));
		if ($User_id == FALSE) {
			$this->response(array('code'=>-1, 'message'=>'ID not found'), 401);
		}
		
		$Event_ID = $this->input->get('Event_ID');
		
		// check if user is involved in this event
		$result = $this->event->findOwnEvent($User_id, $Event_ID);
		$pass_test = false;
		
		if ($result != FALSE) {
			$pass_test = true;
		} else {
			// check if event exists (invited)
			$result = $this->event->findInviteEvent($User_id, $Event_ID);
			if ($result != FALSE) {
				$pass_test = true;
			}
		}
		
		if ($pass_test == false) {
			$this->response(array('code'=> -1, 'message' => 'You have no connection with this event.'), 401);
		}
		
		$result = $this->event->getMovieList($Event_ID);
		if (count($result) > 0) {
			$this->response(array('code'=> 1, 'data'=>$result), 200);
		} else {
			$this->response(array('code'=> -1, 'data'=>array()), 404);
		}
	}
	
}