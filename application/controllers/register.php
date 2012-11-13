<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH.'libraries/REST_Controller.php');

class Register extends REST_Controller {

	/**
	 *
	 */
	
	function __construct() {
		// Call the REST_Controller constructor
        parent::__construct();
		$this->load->model('membership_model','membership');
	}

	public function index_get()
	{
		echo "Register page.";
	}

	public function index_post()
	{
		// send me the detail: id, username, password, email, fb_auth
		$this->load->helper(array('form','security'));
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('fb_id','','xss_clean');
		$this->form_validation->set_rules('username','','required|min_length[6]|xss_clean');
		$this->form_validation->set_rules('password','','required|xss_clean|min_length[6]');
		$this->form_validation->set_rules('email','','required|valid_email');
		$this->form_validation->set_rules('fb_auth','','required|min_length[1]|max_length[1]|xss_clean');
		
		if ($this->form_validation->run() === FALSE) {
			$this->response(array('message'=>'Please check the input again.'), 404);
		} else {
			//add it into the server
			if (xss_clean($_POST['fb_auth']) == 'T') {
				$data = array(
					'fb_id' => xss_clean($_POST['fb_id']),
					'username' => xss_clean($_POST['username']),
					'password' => xss_clean($_POST['password']),
					'email' => xss_clean($_POST['email']),
				);
			} else {
				$data = array(
					'username' => xss_clean($_POST['username']),
					'password' => xss_clean($_POST['password']),
					'email' => xss_clean($_POST['email']),
				);
			}
			$q = $this->membership->register($data);
			
			if ($q['code'] > 0) {
				$this->response(array('message'=>'added'),200);
			} else {
				$this->response(array('message'=>$q['message']),404);
			}
			
		}
		
	}
	
	/*
	public function member_get()
	{
		$q = $this->membership->find_all();
		if ($q['code'] > 0) {
 			$this->response('<pre>'.print_r($q['data']->result(),true).'</pre>',200);
		} else {
			$this->response(array('message'=>'empty'),404);
		}
	}
	*/
	/*
	public function fb()
	{
		require_once(APPPATH. "libraries/facebook/facebook.php");

	  	$fb_config = array();
	  	$fb_config['appId'] = '175913622546611';
		$fb_config['secret'] = '175913622546611';
		$fb_config['fileUpload'] = false; // optional

		$facebook = new Facebook($fb_config);
		$user_id = $facebook->getUser();
		
		if($user_id) {

		      // We have a user ID, so probably a logged in user.
		      // If not, we'll get an exception, which we handle below.
		      try {

		        $user_profile = $facebook->api('/me','GET');
		        echo "Name: " . $user_profile['name'];

		      } catch(FacebookApiException $e) {
		        // If the user is logged out, you can have a 
		        // user ID even though the access token is invalid.
		        // In this case, we'll get an exception, so we'll
		        // just ask the user to login again here.
		        $login_url = $facebook->getLoginUrl(); 
		        //echo 'Please login.' . $e->getType() .' and '. $e->getMessage();
				$data = array(
					'login_url' => $login_url,
				);
				$this->load->view('register_message',$data);
		      }   
		    } else {

		      // No user, print a link for the user to login
		      $login_url = $facebook->getLoginUrl();
		      //echo 'Please login.';
				$data = array(
					'login_url' => $login_url,
				);
				$this->load->view('register_message',$data);

		    }
		
		// $this->load->spark('fb_ignited');
		// 	$fb_me = $this->fb_ignited->fb_get_me();
		// 	//  You can then check the status, if it hasn't already redirected.
		// 	if ($fb_me) {
		// 	        echo "Welcome back, {$fb_me['first_name']}!";
		// 	} else {
		// 	        echo "Welcome, Guest! Please login";
		// 	}
	}*/
}

/* End of file register.php */
/* Location: ./application/controllers/register.php */