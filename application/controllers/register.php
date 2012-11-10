<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH.'libraries/REST_Controller.php');

class Register extends CI_Controller {

	/**
	 *
	 */

	public function index_get()
	{
		echo "Register page.";
	}

	public function index_post()
	{
		// send me the detail
	}
	
	public function fb()
	{
		require_once(APPPATH. "libraries/facebook/facebook.php");

	  	$fb_config = array();
	  	$fb_config[‘appId’] = '175913622546611';
		$fb_config[‘secret’] = '175913622546611';
		$fb_config[‘fileUpload’] = false; // optional

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
		        echo 'Please login.' . $e->getType() .' and '. $e->getMessage();
		      }   
		    } else {

		      // No user, print a link for the user to login
		      $login_url = $facebook->getLoginUrl();
		      echo 'Please login.';

		    }
		
		// $this->load->spark('fb_ignited');
		// 	$fb_me = $this->fb_ignited->fb_get_me();
		// 	//  You can then check the status, if it hasn't already redirected.
		// 	if ($fb_me) {
		// 	        echo "Welcome back, {$fb_me['first_name']}!";
		// 	} else {
		// 	        echo "Welcome, Guest! Please login";
		// 	}
	}
	
	
	public function fb_post()
	{
		// send me the fb auth
	}
}

/* End of file register.php */
/* Location: ./application/controllers/register.php */