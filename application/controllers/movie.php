<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH.'libraries/REST_Controller.php');

class Movie extends REST_Controller {
	
	private $update_interval = 300; // 5 mins
	private $page_limit = 20; // 20 results per page
	
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
	
	protected function update($tomato_ref = 0, $path = '', $page_limit = 20, $page = 1) {
		if ($path == '') {
			$this->response(array('message'=>'Fail writing file.'), 500);
		}
		
		$this->load->helper('fetchinfo');
		$result = fetch_rotten_tomato($tomato_ref,$page_limit,$page);
		if ($result == FALSE) {
			$this->response(array('message'=>'api fail'),500);
		}
		if (! write_file(APPPATH.$path, serialize($result))) {
			$this->response(array('message'=>'Fail writing file.'), 500);
		}
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
			$this->update(1,'movies/i/box_offices.dat',$this->page_limit);
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
		$page_no = $this->input->get('page',true);
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
				$this->update(2,'movies/i/in_theaters_'.$page_no.'.dat',$this->page_limit,$page_no);
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
	
	public function opening_get() {
		$this->load->helper('file');
		
		// check if it needs to update
		$file_info = get_file_info(APPPATH.'movies/i/opening.dat');
		if ($file_info != FALSE && (time()-$file_info['date']<$this->update_interval))
		{
			// do nothing
		} else {
			// need to update
			$this->update(3,'movies/i/opening.dat',$this->page_limit);
		}

		$content = read_file(APPPATH.'movies/i/opening.dat');
		if ($content !== FALSE) {
			$this->response(unserialize($content), 200);
		} else {
			$this->response(array('message'=>'Fail to open'), 404);
		}
			
	}
	
	public function upcoming_get() {
		$this->load->helper('file');
		
		// check if it needs to update
		$file_info = get_file_info(APPPATH.'movies/i/upcoming.dat');
		if ($file_info != FALSE && (time()-$file_info['date']<$this->update_interval))
		{
			// do nothing
		} else {
			// need to update
			$this->update(4,'movies/i/upcoming.dat',$this->page_limit);
		}

		$content = read_file(APPPATH.'movies/i/upcoming.dat');
		if ($content !== FALSE) {
			$this->response(unserialize($content), 200);
		} else {
			$this->response(array('message'=>'Fail to open'), 404);
		}
	}
	
	
}