<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH.'libraries/REST_Controller.php');

class Login extends REST_Controller {
	
	function __construct() {
		// Call the REST_Controller constructor
        parent::__construct();
		$this->early_check();
		$this->load->model('membership_model','membership');
	}
	
	protected function early_check() {
		// extend to auth users
		echo "I'm early check.";
	}
	
	public function index_post() {
		
		$this->load->helper(array('form','security'));
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('fb_id','','xss_clean');
		$this->form_validation->set_rules('username','','required|min_length[6]|xss_clean');
		$this->form_validation->set_rules('password','','required|xss_clean|min_length[6]');
		$this->form_validation->set_rules('email','','required|valid_email');
		$this->form_validation->set_rules('fb_auth','','required|min_length[1]|max_length[1]|xss_clean');
		
		if ($this->form_validation->run() === FALSE) {
			$this->response(array('message'=>'Please check the input again.'), 404);
		}
		
		if (xss_clean($_POST['fb_auth']) == 'T') {
			$data = array(
				'fb_id' => xss_clean($_POST['fb_id']),
				'username' => xss_clean($_POST['username']),
				'password' => xss_clean($_POST['password']),
				'email' => xss_clean($_POST['email']),
			);
			$q = $this->membership->find($data, 'T');
		} else {
			$data = array(
				'username' => xss_clean($_POST['username']),
				'password' => xss_clean($_POST['password']),
				'email' => xss_clean($_POST['email']),
			);
			$q = $this->membership->find($data, 'F');
		}
		
		if ($q['code'] > 0) {
			// we have this entry, proceed
			$this->response(array('message'=>'You\'re login'), 200);
		} else {
			$this->response(array('message'=>'Login failed. Please check your input again.'), 404);
		}
		
	}
	
	
}