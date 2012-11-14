<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH.'libraries/REST_Controller.php');

class Movie extends REST_Controller {
		
	function __construct() {
		// Call the REST_Controller constructor
        parent::__construct();
		// $this->load->model('movie','movie_model');
	}	
	
	public function index_get() {
		$this->response(array('message'=>'Get movie'), 200);
	}
	
	public function box_offices_get() {
		
	}
	
}