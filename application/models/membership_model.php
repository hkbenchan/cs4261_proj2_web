<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Membership_model extends CI_Model {
	
/*	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
		$this->table = "user";
    }*/

	function register($data) {
		// lookup if FB_ID already register
		$query = $this->db->select("FB_ID")->from('Users')->where("FB_ID",$data['FB_ID'])->get();
		if ($query->num_rows() > 0) {
			return array('code'=> -1,'message'=>'Same Facebook ID is already registered');
		}
		
		// insert the data now
		$query = $this->db->insert('Users', $data);
		
		if ($this->db->affected_rows() > 0) {
			return array('code'=> 1, 'message'=>'success');
		} else
			return array('code'=> -1, 'message'=>'Error: '. $this->db->_error_message());
	}
	
	function find_all() {
		$query = $this->db->get('Users');
		
		if ($query->num_rows() > 0) {
			return array('code'=> 1, 'data' => $query);
		} else {
			return array('code'=> -1, 'data' => array());
		}
	}
	
	function find($data, $from_fb = 'F') {
		if ($from_fb == 'T') {
			$query = $this->db->from('Users')->where('FB_ID', $data['FB_ID'])->where('Password', $data['Password'])->get();
		} else {
			$query = $this->db->from('Users')->where('Email', $data['Email'])->where('Password', $data['Password'])->get();
		}
		
		if ($query->num_rows() > 0) {
			return array('code'=> 1, 'data' => $query);
		} else {
			return array('code'=> -1, 'data' => array());
		}
		
	}
	
	function verify($data) {
		$query = $this->db->from('Users')
				->where('FB_ID', $data['FB_ID'])
				->where('Username', $data['Username'])
				->where('Email', $data['Email'])
				->get();
		if ($query->num_rows() > 0) {
			return array('code'=> 1, 'data' => $query);
		} else {
			return array('code'=> -1, 'data' => array());
		}		
				
	}
	
	function user_id_by_FB($fb_id) {
		$query = $this->db->select('ID')->from('Users')->where('FB_ID', $fb_id)->get();
		
		if ($query->num_rows() > 0) {
			$r = $query->first_row();
			return $r['ID'];
		} else {
			return FALSE;
		}
	}
	
	function login_email($email) {
		$query = $this->db->from('Users')->where('Email',$email)->get();
		
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return array();
		}
	}
	
}

?>