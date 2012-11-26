<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Event_model extends CI_Model {
	
	public function get_event_lists_own($FB_ID = -1) {
		return $this->db->from('UserOwnsEvent')
					->join('Users','Users.ID = UserOwnsEvent.User_ID')
					->where('Users.FB_ID',$FB_ID)
					->get();
	}
	
	public function get_event_lists_invited($FB_ID = -1) {
		return $this->db->from('UserInvitedEvent')
						->join('Users','Users.ID = UserInvitedEvent.User_ID')
						->where('Users.FB_ID',$FB_ID)
						->get();
	}
	
	public function get_event_lists_all($FB_ID = -1) {
		
		$q1 = $this->get_event_lists_own($FB_ID);
		$q2 = $this->get_event_lists_invited($FB_ID);
		
		if (count($q1->result())+count($q2->result())>0) {
			return array('code' => 1,
						 'data' => array(
										'own'=>$q1->result(),
										'invited'=>$q2->result()
						 				)
					  	);
		} else {
			return array('code'=>-1,'data'=>'');
		}
		
	}
}