<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Event_model extends CI_Model {
	
	public function get_event_lists_own($FB_ID = -1) {
		return $this->db->select('UserOwnsEvent.*')
						->from('UserOwnsEvent')
						->join('Users','Users.ID = UserOwnsEvent.User_ID')
						->where('Users.FB_ID',$FB_ID)
						->get();
	}
	
	public function get_event_lists_invited($FB_ID = -1) {
		return $this->db->select('UserInvitedEvent.*')
						->from('UserInvitedEvent')
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
	
	public function create_event($data) {
		$this->db->insert('Event',$data);
		
		if ($this->db->affected_rows()>0) 
			return $this->db->insert_id();
		else
			return FALSE;
	}
	
	public function addOwnerEvent($data) {
		$this->db->insert('UserOwnsEvent', $data);
		
		if ($this->db->affected_rows()>0)
			return TRUE;
		else
			return FALSE;
	}
	
	public function addInviteEvent($data) {
		$this->db->insert('UserInvitedEvent', $data);
		
		if ($this->db->affected_rows()>0)
			return TRUE;
		else
			return FALSE;
	}
	
	public function findOwnEvent($User_ID, $Event_ID) {
		$result = $this->db->from('UserOwnsEvent')
						->where('User_ID', $User_ID)
						->where('Event_ID', $Event_ID)
						->get();
		if ($result->num_rows() > 0) {
			return $result;
		} else {
			return FALSE;
		}
	}
	
	public function findInviteEvent($User_ID, $Event_ID) {
		$result = $this->db->from('UserInvitedEvent')
						->where('User_ID', $User_ID)
						->where('Event_ID', $Event_ID)
						->get();
		if ($result->num_rows() > 0) {
			return $result;
		} else {
			return FALSE;
		}
	}
	
	public function findEventMovie($Event_ID, $Movie_ID) {
		$result = $this->db->from('EventMovie')
						->where('Event_ID', $Event_ID)
						->where('Movie_ID', $Movie_ID)
						->get();
		if ($result->num_rows() > 0) {
			return $result;
		} else {
			return FALSE;
		}
	}
	
	public function reduceVote($data) {
		$r = $this->findEventMovie($data['Event_ID'], $data['Movie_ID']);
		if ($r == FALSE)
			return FALSE;
		$r_r = $r->first_row('array');
		$no_of_vote = $r_r['no_of_vote'] - 1;
		if ($no_of_vote<0)
			$no_of_vote = 0;
		$this->db->update('EventMovie',array('no_of_vote'=>$no_of_vote),$data);
		
		if ($this->db->affected_rows()>0 && $this->db->affected_rows()<2)
			return TRUE;
		else
			return FALSE;
		
	}
	
	public function addVote($data, $no_of_vote) {
		$this->db->update('EventMovie',array('no_of_vote'=>$no_of_vote),$data);
		
		if ($this->db->affected_rows()>0 && $this->db->affected_rows()<2)
			return TRUE;
		else
			return FALSE;
	}
	
	public function updateOwnEventVote($data, $Movie_ID) {
		$this->db->update('EventMovie',array('Movie_ID'=>$Movie_ID),$data);

		if ($this->db->affected_rows()>0 && $this->db->affected_rows()<2)
			return TRUE;
		else
			return FALSE;
	}
	
	public function updateInviteEventVote($data) {
		
	}
}