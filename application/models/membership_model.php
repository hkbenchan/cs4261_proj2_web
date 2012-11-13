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
			return array('code'=> -1,'message'=>'email is already registered');
		}
		
		// insert the data now
		$query = $this->db->insert('user', $data);
		
		if ($this->db->affected_rows() > 0) {
			return array('code'=> 1, 'message'=>'success');
		}
	}
	
	function find_all() {
		$query = $this->db->get('user');
		
		if ($query->num_rows() > 0) {
			return array('code'=> 1, 'data' => $query);
		} else {
			return array('code'=> -1, 'data' => array());
		}
	}
	
	
	
}

?>