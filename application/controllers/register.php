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
	
	public function fb_get()
	{
		$this->load->sparks('fb_ignited/1.2.0');
		$this->fb_me = $this->fb_ignited->fb_get_me();
		//  You can then check the status, if it hasn't already redirected.
		if ($this->fb_me) {
		        echo "Welcome back, {$this->fb_me['first_name']}!";
		} else {
		        echo "Welcome, Guest! Please login";
		}
	}
	
	
	public function fb_post()
	{
		// send me the fb auth
	}
}

/* End of file register.php */
/* Location: ./application/controllers/register.php */