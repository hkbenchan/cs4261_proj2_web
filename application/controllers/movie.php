<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH.'libraries/REST_Controller.php');

class Movie extends REST_Controller {
		
	function __construct() {
		// Call the REST_Controller constructor
        parent::__construct();
		// $this->load->model('movie','movie_model');
	}	
	
	public function index_get() {
		$this->load->helper('file');
		$data = 'Some file data';
		$response_a = array();
		if ( ! write_file('./movies/i/file.php', $data))
		{
		     $response_a['message'] = 'Unable to write the file';
		}
		else
		{
		     $response_a['message'] = 'File written!';
		}
		$this->response($response_a, 200);
	}
	
	public function box_offices_get() {
		$this->load->helper('fetchinfo');
 		$this->load->helper('file');
		
		
		// check if it needs to update
		
		$result = fetch_rotten_tomato(1);
		//echo '<pre>'.print_r($result,true).'</pre>';
		//die();
		
		
	}
	
}