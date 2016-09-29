<?php 
/*
 * Filename			:	save_card_model
* Classname			:	save_card_model
* Description		:	Used to write Mysql query for get data from the database for web services for user details
* controller		:	save_card
*
* ---------------------------------------------------------------------------------------------------------------------------------------------
*/

class Saved_card_model extends CI_Model
{

	public function __construct()
	{
		parent:: __construct();
		$this->load->model('parent/user_model');
	}

	
	/**
	 * Add edit card
	 *
	 * @access	public
	 * @param	string userid
	 * @param	array of card details
	 * @return	bool
	 */
	function addEditCard($userid,$data)
 	{
		if(isset($data['saved_card_id']) && $data['saved_card_id']!='')
		{ 
			$card_id =$data['saved_card_id'];
			$this->db->where('saved_card_id',$data['saved_card_id']);
			$this->db->update('saved_card',$data);
			$data[0] = "Card details has been updated sucessfully.";
		}
		else 
		{
			$this->db->insert('saved_card',$data);
			$card_id = $this->db->insert_id();
			$data[0] = "Card details saved sucessfully";
		}
		
		$this->db->select('*');
		$this->db->where('saved_card_id',$card_id);
		$res = $this->db->get('saved_card');
		if($res->num_rows()>0)
		{
			$data[1] = $res->result();
			return $data;
		}
		
		return false;
 	}
 	
 	
 	/**
 	 * get saved cards
 	 *
 	 * @access	public
 	 * @param	string
 	 * @return	array containg card details
 	 */
 	function getSavedCard($userid)
 	{
 		$this->db->select('authorizenet_payment_profile_id,card_number,name_on_card,card_num_length');
 		$this->db->where('user_id',$userid);
 		$res = $this->db->get('client_payment_profile');
 		if($res->num_rows()>0)
 		{
 			$res= $res->result();
 			$i=0;
 			foreach($res as $row)
 			{
 				if($res[$i]->name_on_card==null)
 					$res[$i]->name_on_card='';
 				
 				if($res[$i]->card_num_length>0)
 				{
 					$res[$i]->card_number =  str_pad($res[$i]->card_number, $res[$i]->card_num_length, "X", STR_PAD_LEFT);
 				}
 				$res[$i]->authorizenet_payment_profile_id = base64_encode($row->authorizenet_payment_profile_id);
 				$i++;
 			}
 			return $res;
 		}
 	
 		return false;
 	}
}