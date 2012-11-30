<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Event_model extends CI_Model {
	
	public function get_event_lists_own($FB_ID = -1) {
		$r = $this->db->select('UserOwnsEvent.*, Event.*')
						->from('UserOwnsEvent')
						->join('Users','Users.ID = UserOwnsEvent.User_ID')
						->join('Event', 'Event.ID = UserOwnsEvent.Event_ID')
						->where('Users.FB_ID',$FB_ID)
						->get();
						
		return $r;
	}
	
	public function get_event_lists_invited($FB_ID = -1) {
		$r = $this->db->select('UserInvitedEvent.*, Event.*')
						->from('UserInvitedEvent')
						->join('Users','Users.ID = UserInvitedEvent.User_ID')
						->join('Event', 'Event.ID = UserOwnsEvent.Event_ID')
						->where('Users.FB_ID',$FB_ID)
						->get();
		
		return $r;
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
	
	public function updateOwnEventVote($data, $Movie_vote) {
		$this->db->update('UserOwnsEvent',array('Movie_vote'=>$Movie_vote),$data);

		if ($this->db->affected_rows()>0 && $this->db->affected_rows()<2)
			return TRUE;
		else
			return FALSE;
	}
	
	public function updateInviteEventVote($data, $Movie_vote) {
		$this->db->update('UserInvitedEvent',array('Movie_vote'=>$Movie_vote),$data);

		if ($this->db->affected_rows()>0 && $this->db->affected_rows()<2)
			return TRUE;
		else
			return FALSE;
	}
	
	public function getMovieList($Event_ID) {
		$result = $this->db->from('EventMovie')->where('Event_ID', $Event_ID)->get();
		return $result->result_array();
		/*
		if ($result->num_rows()>0) {
			return $result->result_array();
		} else {
			return 
		}
		*/
	}
	
	public function removeMovieFromList($Event_ID, $Movie_vote) {
	
		// find if anyone voted it and set it to -1
		
		$this->db->update('UserOwnsEvent',array('Movie_vote'=>-1), array('Event_ID' => $Event_ID, 'Movie_vote' => $Movie_vote));
		$this->db->update('UserInvitedEvent',array('Movie_vote'=>-1), array('Event_ID' => $Event_ID, 'Movie_vote' => $Movie_vote));
		
		// remove the movies from the EventMovie
		
		$this->db->delete('EventMovie', array('Event_ID' => $Event_ID, 'Movie_ID' => $Movie_vote));
		if ($this->db->affected_rows()>0 && $this->db->affected_rows()<2)
			return TRUE;
		else
			return FALSE;
	}
	
	public function addMovieFromList($Event_ID, $Movie_vote) {
		$this->db->insert('EventMovie', array('Event_ID' => $Event_ID, 'Movie_ID' => $Movie_vote));
		
		if ($this->db->affected_rows()>0 && $this->db->affected_rows()<2)
			return TRUE;
		else
			return FALSE;
	}
	
	public function getEventDetail($Event_ID) {
		$result = $this->db->from('Event')->where('ID', $Event_ID)->get();
		
		return $result->result_array();
	}
}