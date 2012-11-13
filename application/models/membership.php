<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Membership extends CI_Model {
	
	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

	function register($data) {
		// lookup if email address already register
		$query = $this->db->select("email")->from("users")->where("email",$data['email'])->get();
		if ($query->num_rows() > 0) {
			return array('code'=> -1,'message'=>'email is already registered');
		}
		
		// insert the data now
		$query = $this->db->insert('users', $data);
		
		if ($this->db->affected_rows() > 0) {
			return array('code'=> 1, 'message'=>'success', )
		}
	}
	
	function find_all() {
		$query = $this->db->get('users');
		
		if ($query->num_rows() > 0) {
			return array('code'=> 1, 'data' => $query);
		} else {
			return array('code'=> -1, 'data' => array());
		}
	}
	
	
	
}

?>