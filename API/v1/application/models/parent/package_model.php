<?php 
/*
 * Filename			:	package_model
* Classname			:	Package_model
* Description		:	Used to write Mysql query for get data from the database for web services for packages
* controller		:	User
*
* ---------------------------------------------------------------------------------------------------------------------------------------------
*/

class Package_model extends CI_Model
{
	public function __construct()
	{
		parent:: __construct();
	}

	/**
	 * package list
	 *
	 * @access	public
	 * @return	bool
	 */
	function packageList()
	{
		$this->db->select('*');
		$this->db->where('status',0);
		$result = $this->db->get('packages');
		if($result->num_rows>0)
		{
			return $result->result_array();
		}
		else
			return false;	
	}
	
	/**
	 * buy credit
	 *
	 * @access	public
	 * @param	array
	 * @return	bool
	 */
	function buyPackage($data)
	{
		$this->db->insert('clients_subscription',$data);
		$result = $this->db->insert_id();
		if($result)
		{
			$st = $this->setClientSubscription($data['userid']);
			return $result;
		}
		else
			return false;
	}
	
	
	/**
	 * update client subscription
	 *
	 * @access	public
	 * @param	string user id
	 * @return	bool
	 */
	public function setClientSubscription($userid)
	{
		$sql="update clients_detail set available_credits =
		(select sum(slots) from clients_subscription where end_date > now()  
		and userid=$userid) where  userid=$userid";
		$this->db->query($sql);
		return true;
	}
	
	
	/**
	 * refund credits
	 *
	 * @access	public
	 * @param	string job_id
	*/
	public function reimburse($job_id)
	{
		$this->db->select('credits_used,client_user_id,subs_id');
		$this->db->where('job_id',$job_id);
		$job = $this->db->get('jobs');
		$job = $job->result_array();
		$job = $job[0];
		
		$sql="update clients_subscription set slots =slots+{$job['credits_used']} where  userid='".$job['client_user_id']."' and client_sub_id = '".$job['subs_id']."'";
		$this->db->query($sql);
		$this->setClientSubscription($job['client_user_id']);
	}
	
	/**
	 * package details
	 *
	 * @access	public
	 * @param	string package id
	 * @return	array of package details
	 */
	function package_details($package_id)
	{
		$this->db->select('*');
		$this->db->where('package_id',$package_id);
		$pack = $this->db->get('packages');
	 	
		if($pack->num_rows()>0)
			return $pack->row();
		else 
			return false;
	}
	
	function availableCredits($userId)
	{
		$this->db->select('packages.package_id,end_date,client_sub_id,package_name,credits,packages.price,slots as remaining_credits');
		$this->db->join('clients_subscription','clients_subscription.package_id=packages.package_id');
		$this->db->where('userid',$userId);
		$this->db->where('slots > 0');
		$this->db->where('end_date > now()');
		$pack = $this->db->get('packages');
		 
		$result['subscription']=array();
		$result['total_available_credits']=0;
		
		if($pack->num_rows()>0)
		{
			$result['subscription']=$pack->result();
			foreach($result['subscription'] as $row)
				$result['total_available_credits']+=$row->remaining_credits;
		}
		return $result;
	}
}