<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH.'libraries/REST_Controller.php');

class Event extends REST_Controller {
	
	function __construct() {
		// Call the REST_Controller constructor
        parent::__construct();
		$this->load->model('event','event_model');
	}
	
	
	public function lists_get(){
		
		// get the user's event list by FB_ID
		$FB_ID = $this->input->get('FB_ID',true);
		if (is_numeric($FB_ID)) {
			$result = $this->event->get_event_list_all($FB_ID);
			if ($result['code']>0) {
				$this->response($result,200);
			} else {
				$this->response(array('code'=>-1, 'message'=>'Zero result'),404);
			}
		} else {
			$this->response(array('code'=>-1, 'message'=>'Incorrect ID'),401);
		}
		
	}
	
	
}