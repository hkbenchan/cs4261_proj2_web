<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH.'libraries/REST_Controller.php');

class Movie extends REST_Controller {
	
	private $update_interval = 300; // 5 mins
	private $page_limit = 20; // 20 results per page
	
	function __construct() {
		// Call the REST_Controller constructor
        parent::__construct();
		// $this->load->model('movie','movie_model');
	}	
	
	public function index_get() {
		$this->load->helper('file');
		$data = 'Some file data';
		$response_a = array();
		if (! write_file(APPPATH.'movies/i/file.php', $data)) {
			$response_a['message'] = 'Unable to write the file';
		} else {
			$response_a['message'] = 'File written!';
			$response_a['file_info'] = get_file_info(APPPATH.'movies/i/file.php');
		}
		$this->response($response_a, 200);
	}
	
	public function box_offices_get() {
 		$this->load->helper('file');
		
		// check if it needs to update
		$file_info = get_file_info(APPPATH.'movies/i/box_offices.dat');
		if ($file_info != FALSE && (time()-$file_info['date']<$this->update_interval))
		{
			// do nothing
		} else {
			// need to update
			$this->load->helper('fetchinfo');
			$result = fetch_rotten_tomato(1);
			if ($result == FALSE) {
				$this->response(array('message'=>'api fail'),500);
			}
			if (! write_file(APPPATH.'movies/i/box_offices.dat', serialize($result))) {
				$this->response(array('message'=>'Fail writing file.'), 500);
			}
		}
		
		$content = read_file(APPPATH.'movies/i/box_offices.dat');
		if ($content !== FALSE) {
			$this->response(unserialize($content), 200);
		} else {
			$this->response(array('message'=>'Fail to open'), 404);
		}
		
	}
	
	public function in_theaters_get() {
		$this->load->helper('file');
		echo $_GET['page']; die();
		$page_no = $this->input->get('page',true);
		echo "Page no: ".$page_no;
		if ($page_no == FALSE)
			$page_no = 1;
		if (is_numeric($page_no)) {
			
			// check if it needs to update
			
			$file_info = get_file_info(APPPATH.'movies/i/in_theaters_'.$page_no.'.dat');
			if ($file_info != FALSE && (time()-$file_info['date']<$this->update_interval))
			{
				// do nothing
			} else {
				// need to update
				$this->load->helper('fetchinfo');
				$result = fetch_rotten_tomato(2, $this->page_limit, $page_no);
				if ($result == FALSE) {
					$this->response(array('message'=>'api fail'),500);
				}
				if (! write_file(APPPATH.'movies/i/in_theaters_'.$page_no.'.dat', serialize($result))) {
					$this->response(array('message'=>'Fail writing file.'), 500);
				}
			}

			$content = read_file(APPPATH.'movies/i/in_theaters_'.$page_no.'.dat');
			if ($content !== FALSE) {
				$this->response(unserialize($content), 200);
			} else {
				$this->response(array('message'=>'Fail to open'), 404);
			}
			
			
		} else {
			$this->response(array('message'=>'Wrong page number.'), 404);
		}
	}
	
}