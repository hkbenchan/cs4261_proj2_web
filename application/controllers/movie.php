<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH.'libraries/REST_Controller.php');

class Movie extends REST_Controller {
		
	function __construct() {
		// Call the REST_Controller constructor
        parent::__construct();
		// $this->load->model('movie','movie_model');
	}
	
/*	protected function early_checks() {
		// extend to auth users
		$this->load->model('membership','membership_model');
		$this->load->helper(array('form','security'));
		$this->load->library('form_validation');
		$this->form_validation->set_rules('password','','required|xss_clean|min_length[6]');
		$this->form_validation->set_rules('email','','required|valid_email');
		if ($this->form_validation->run() === FALSE) {
			$this->response(array('message'=>'Please check the input again.'), 404);
		}
		
		$data = array(
			'password' => xss_clean($_POST['password']),
			'email' => xss_clean($_POST['email']),
		);
		$q = $this->membership->find($data, 'F');
		if ($q['code'] < 0) {
			$this->response(array('message'=>'Request rejected as wrong crediental.'), 401);
		}
	}
*/
	protected function _check_login($username = '', $password = NULL)
	{
		if (empty($username))
		{
			return FALSE;
		}

		/*$valid_logins = & $this->config->item('rest_valid_logins');

		if ( ! array_key_exists($username, $valid_logins))
		{
			return FALSE;
		}

		// If actually NULL (not empty string) then do not check it
		if ($password !== NULL AND $valid_logins[$username] != $password)
		{
			return FALSE;
		}*/
		
		$this->load->model('membership_model','membership');
		$this->load->helper('security');
		
		if ($password === NULL)
			return FALSE;
		
		$data = array(
			'password' => xss_clean($password),
			'email' => xss_clean($username),
		);
		$q = $this->membership->find($data, 'F');
		if ($q['code'] < 0) {
			return FALSE;
		}

		return TRUE;
	}
	
	
	public function index_get() {
		$this->response(array('message'=>'Get movie'), 200);
	}
	
}