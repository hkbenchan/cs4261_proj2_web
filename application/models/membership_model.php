<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Membership_model extends CI_Model {
	
/*	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
		$this->table = "user";
    }*/

	function register($data) {
		// lookup if email address already register
		$query = $this->db->select("email")->from('user')->where("email",$data['email'])->get();
		if ($query->num_rows() > 0) {
			return array('code'=> -1,'message'=>'Same email address is already registered');
		}
		
		// insert the data now
		$query = $this->db->insert('user', $data);
		
		if ($this->db->affected_rows() > 0) {
			return array('code'=> 1, 'message'=>'success');
		} else
			return array('code'=> -1, 'message'=>'Error: '. $this->db->_error_message());
	}
	
	function find_all() {
		$query = $this->db->get('user');
		
		if ($query->num_rows() > 0) {
			return array('code'=> 1, 'data' => $query);
		} else {
			return array('code'=> -1, 'data' => array());
		}
	}
	
	function find($data, $from_fb = 'F') {
		if ($from_fb == 'T') {
			$query = $this->db->from('user')->where('fb_id', $data['fb_id'])->where('password', $data['password'])->get();
		} else {
			$query = $this->db->from('user')->where('email', $data['email'])->where('password', $data['password'])->get();
		}
		
		if ($query->num_rows() > 0) {
			return array('code'=> 1, 'data' => $query);
		} else {
			return array('code'=> -1, 'data' => array());
		}
		
	}
	
}

?>