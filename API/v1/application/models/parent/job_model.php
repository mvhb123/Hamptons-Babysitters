<?php 
/*
 * Filename			:	user_model
* Classname			:	User_model
* Description		:	Used to write Mysql query for get data from the database for web services for user details
* controller		:	User
*
* ---------------------------------------------------------------------------------------------------------------------------------------------
*/

class Job_model extends CI_Model
{

	public function __construct()
	{
		parent:: __construct();
		$this->load->model('parent/user_model');
		$this->load->model('common_model');
		$this->load->model('parent/package_model');
	}
	
	
	/**
	 *  Add child to job
	 * 
	 * @param string $job_id
	 * @param array $child_id
	 */
	function addChilds($job_id,$child_id=array()){
	
		foreach($child_id as $ch){
			$query[] = "($job_id,$ch)";
		}
		$values = implode(',',$query);
	
		$sql="insert ignore into jobs_to_childs values $values";
		$res = $this->db->query($sql);
	}
	
	
	/**
	 * Update clients open job requests
	 * 
	 * @param string $userid
	 */
	function updateToClientRequests($userid)
	{
		$sql="update clients_detail set open_requests=(select count(*) from jobs where client_user_id = $userid and job_status in('new','pending','confirmed')) where userid= $userid";
		$this->db->query($sql);
	}
	
	
	/**
	 * Update credits 
	 * 
	 * @param string $userid
	 */
	public function updateCredits($userid){
	
		$sql="update clients_detail set available_credits =(select sum(slots) from clients_subscription where end_date > now()  and userid= ".$userid.") where  userid=".$userid;
		return $this->db->query($sql);
	}
	
	
	/**
	 * Add job preferences
	 * 
	 * @param array $prefer
	 * @param string $job_id
	 */
	public function addPreference($prefer,$job_id)
	{
		$this->db->query("Delete from job_preference where job_id=$job_id ");
	
		foreach($prefer as $ch){
			$query[] = "($job_id,$ch)";
		}
		$values = implode(',',$query);
	
		$sql="insert ignore into job_preference values $values";
		$res = $this->db->query($sql);
	
	}
	
	
	/**
	 * Get job's preference
	 * 
	 * @param string $job_id
	 * @return array
	 */
	function getPreferences($job_id)
	{
		$ret=array();

		$sql="(SELECT g.*,m.* FROM `preference_group` g join `preference_master` m join `job_preference` s
		on(g.group_id=m.group_id and s.prefer_id=m.prefer_id)
		where job_id=$job_id ) union (SELECT *
		FROM `preference_group` g
		JOIN `preference_master` m ON ( g.group_id = m.group_id ) where for_manage_sitter=1)";

		$res = $this->db->query($sql);

		$prefers = $res->result_array();

		foreach($prefers as $p)
		{
			$ret[$p['group_id']]['prefer'][$p['prefer_id']]=$p;
			if(isset($ret[$p['group_id']]))$ret[$p['group_id']]['label']=$p['label'];
		}
		return $ret;

	}
	
	
	/**
	 * get job's children
	 * 
	 * @param string $job_id
	 * @return array
	 */
	function getChildren($job_id){

		$r=array();
		$sql="select child_id from jobs_to_childs where job_id =$job_id ";

		$res = $this->db->query($sql);

		$result =  $res->$result_array();
		foreach($result as $res){
			$r[]=$res['child_id'];
		}
		return $r;
	}
	

	/**
	 * Child details
	 * 
	 * @param string $job_id
	 * @return array
	 */
	function getChildrenDetails($job_id){
	
		$r=array();
		$sql="select * from jobs_to_childs jc join children c on(c.child_id=jc.child_id)  where job_id =$job_id ";
	
		$res = $this->db->query($sql);
	
		$result =  $res->result_array();
		$i=0;
		foreach($result as $res)
		{
			$check[] = array();
			$check = replace_null($res);
				
			if($res['main_image'] != null && file_exists($this->config->item('image_path').$res['main_image']))
				$check['main_image'] = $this->config->item('image_url').$res['main_image'];
			else 
				$check['main_image'] = $this->config->item('image_url')."uploads/noimage.jpg";
			
			if($res['app_thumb'] != null && file_exists($this->config->item('image_path').$res['app_thumb']))
				$check['thumb_image'] = $this->config->item('image_url').$res['app_thumb'];
			else
				$check['thumb_image'] = $this->config->item('image_url')."uploads/noimage.jpg";
			
			if($res['orginal_image'] != null && file_exists($this->config->item('image_path').$res['orginal_image']))
				$check['orginal_image'] = $this->config->item('image_url').$res['orginal_image'];
			else
				$check['orginal_image'] = $this->config->item('image_url')."uploads/noimage.jpg";
			
			//$birthDate = $res['dob'];
			//$birthDate = explode("-", $birthDate);
			//$check['age'] = (string)(date("md", date("U", mktime(0, 0, 0, $birthDate[1],$birthDate[2] , $birthDate[0]))) > date("md") ? ((date("Y")-$birthDate[0])-1):(date("Y")-$birthDate[0]));

			$check['age'] =child_age($res['dob']);
			$r[$i]=(object)$check;
			$i++;
		}
		return $r;
	}

	
	/**
	 * Edit job
	 * 
	 * @param array $data
	 * @param string $job_id
	 * @return boolean|string
	 */
	function editJob($data,$job_id)
	{
		$this->db->select('master_job_id');
		$this->db->where('job_id',$job_id);	
		$master_job_id = $this->db->get('jobs');
		if($master_job_id->num_rows()>0)
		{
			$master_job_id=$master_job_id->result();
			$updateMaster = array('actual_start_date'=>date('Y-m-d H:i:s',strtotime($data['start_date'])),'actual_end_date'=>date('Y-m-d H:i:s',strtotime($data['end_date'])));
			$this->db->where('master_job_id',$master_job_id[0]->master_job_id);
			$this->db->update('job_master',$updateMaster);
		}
		
		$update = array(
				'client_user_id'=>$data['userid'],
				'jobs_posted_date'=>date('Y-m-d H:i:s'),
				'job_start_date'=>date('Y-m-d H:i:s',strtotime($data['start_date'])),
				'job_end_date'=>date('Y-m-d H:i:s',strtotime($data['end_date'])),
				'child_count'=>$data['child_count'],
				'actual_child_count'=>$data['child_count'],
				'sitter_user_id'=>$data['sitter_id'],
				'last_modified_date'=>date('Y-m-d H:i:s'),
				'last_modified_by'=>$data['userid'],
				'job_status'=>$data['job_status'],
				'is_special'=>$data['is_special'],
				'special_need'=>$data['special_need'],
				'notes'=>$data['notes'],
				'address_id'=>$data['address_id'],
		);

		$rate=$this->common_model->getRate($data['child_count']);
		
		if($rate)
		{
			$update['client_rate']=$rate->client_rate;
			$update['client_updated_rate']=$rate->client_rate;
			$update['rate']= $rate->sitter_rate;
			$update['sitter_rate_pre']= $rate->sitter_rate;
		}
		
		$this->db->where('job_id',$job_id);
		//$this->db->where('job_status !=','completed');
		$this->db->update('jobs',$update);
		
		$this->removeChilds($job_id);
		$this->addChilds($job_id,$data['children']);
		$this->updateToClientRequests($data['userid']);
		if(is_array($data['prefer'])&& !empty($data['prefer']) && $data['prefer'][0]!=''){
			$this->addPreference($data['prefer'],$job_id);
		}
		
		$result_data[0] = $this->lang->line('job_updated');
		$job_details = $this->jobDetails($job_id);
		$result_data[1] = $job_details;
		
		$param['type']= 'sitter';
		$this->load->library('apn',$param);
		$msg=$this->lang->line('job_update_noti');
		$this->common_model->send_notification($job_id,$job_details['sitter_user_id'],$msg);
		//jobMail($job_id,$job_details,'job_request_updated_to_client','client','mail');
		//jobMail($job_id,$job_details,'job_request_updated_to_admin','mail','client');
		return $result_data;
	}

	
	/**
	 * remove job children
	 * 
	 * @param string $job_id
	 * @param array $child_id
	 */
	function removeChilds($job_id,$child_id=array())
	{
		$values='';
		foreach($child_id as $ch)
		{
			$query[] = $child_id;
		}
		if(!empty($query))
		{
			$values = 'and child_id in ('.implode(',',$query).')';
		}
		$sql="delete from jobs_to_childs where job_id =".$job_id." ".$values;
		$res = $this->db->query($sql);
	}
	
	
	
	/**
	 * get job list
	 * 
	 * @param string $client_user_id
	 * @param string $jstatus
	 * @param integer $perPage
	 * @param integer $offset
	 * @return array
	 */
	function jobList($client_user_id,$jstatus,$perPage='',$offset='')
	{
		if($jstatus == 'confirmed' || $jstatus == 'completed' )
		{
			$sql = 'j.job_id,jobs_posted_date,job_start_date,job_end_date,client_user_id,j.sitter_user_id,job_status,round(ifnull( client_updated_rate,client_rate ),2 ) as rate,notes,address_id,
					u.username as sitter_username,u.firstname as sitter_firstname,u.lastname as sitter_lastname,u.phone as sitter_phone,
					job_start_date as actual_start_date,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ) actual_end_date';
			
			$this->db->select($sql, false);
			$this->db->join('users u','u.userid = j.sitter_user_id');
			$this->db->where('client_user_id',$client_user_id);
			if($jstatus == 'completed')
			{
				$this->db->where_in('j.job_status',array('completed','closed'));
				$this->db->order_by('job_start_date','DESC');
			}
			else 
			{
				$this->db->order_by('job_start_date','DESC');
				$this->db->where('job_status',$jstatus);
			}
			
			$result = $this->db->get('jobs j');
			$r[0] = $result->num_rows();
				
			if($perPage!='')
			{
				$this->db->select($sql, false);
				$this->db->join('users u','u.userid = j.sitter_user_id');
				$this->db->where('client_user_id',$client_user_id);
				if($jstatus == 'completed')
				{
					$this->db->where_in('j.job_status',array('completed','closed'));
					$this->db->order_by('job_start_date','DESC');
				}
				else 
				{
					$this->db->where('job_status',$jstatus);
					$this->db->order_by('job_start_date','DESC');
				}
				
				$result = $this->db->get_where('jobs j',array(),$perPage,$offset);
			}
		}
		else
		{
			$sql = 'j.job_id,jobs_posted_date,job_start_date,job_end_date,client_user_id,j.sitter_user_id,job_status,round(ifnull( client_updated_rate,client_rate ),2 ) as rate,notes,address_id';
			$this->db->select($sql,false);
			$this->db->where('client_user_id',$client_user_id);
			$this->db->where_in('j.job_status',array('pending','new'));
			$this->db->order_by('job_start_date','DESC');
			$result = $this->db->get('jobs j');
			$r[0] = 	$result->num_rows();
			if($perPage!='')
			{
				$this->db->select($sql,false);
				$this->db->where('client_user_id',$client_user_id);
				$this->db->where_in('j.job_status',array('pending','new'));
				$this->db->order_by('job_start_date','DESC');
				$result = $this->db->get_where('jobs j',array(),$perPage,$offset);
			}
		}
	    $res = $result->result();
		if($result->num_rows()>0)
		{
			$i=0;
			foreach($res as $row)
			{
				if($res[$i]->sitter_user_id == null ||$res[$i]->sitter_user_id == 0)
					$res[$i]->sitter_user_id='';
				if($res[$i]->rate == null ||$res[$i]->rate == 0)
					$res[$i]->rate='';
				if($res[$i]->notes == null ||$res[$i]->notes == 0)
					$res[$i]->notes='';
				
				if($res[$i]->job_status=='pending'||$res[$i]->job_status=='new')
				{
					$res[$i]->sitter_user_id="";
					$res[$i]->sitter_firstname = "";
					$res[$i]->sitter_lastname = "";
					$res[$i]->sitter_username ="";
					$res[$i]->sitter_phone ="";
				}
				elseif(abs(strtotime($res[$i]->job_start_date)-time())>86400 && $res[$i]->job_status =='completed')
				{
					$res[$i]->sitter_user_id="";
					$res[$i]->sitter_firstname = "";
					$res[$i]->sitter_lastname = "";
					$res[$i]->sitter_username ="";
					$res[$i]->sitter_phone ="";
				}
				$res[$i]->jobs_posted_date = dateFormat($row->jobs_posted_date);
				$res[$i]->job_start_date = dateFormat($row->job_start_date);
				$res[$i]->job_end_date = dateFormat($row->job_end_date);
				//$res[$i]->actual_start_date = dateFormat($row->actual_start_date);
				//$res[$i]->actual_end_date = dateFormat($row->actual_end_date);
				$res[$i]->children = $this->getChildrenDetails($row->job_id);
				$res[$i]->address = $this->common_model->getJobAddress($row->address_id);
				
				$i++;
			}
		}
		$r[1] = $res;
		return $r;
		
	}
	
	
	/**
	 * get job details
	 * 
	 * @param string $job_id
	 */
	function jobDetails($job_id)
	{
		if($job_id>0)
			$query_clientId = ' and job_id='.$job_id;
	
		$sql="select  j.job_id,client_user_id,sitter_user_id,job_start_date,job_end_date,u.username,u.firstname,u.phone,
		jobs_posted_date,child_count,job_status,notes,is_special,special_need,address_id,job_start_date as actual_start_date,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ) actual_end_date,completed_date,last_modified_date,total_assigned,total_paid  
		from jobs j join users u 
		on(j.client_user_id=u.userid )
		where u.status!='deleted'  $query_clientId ";
	
		$res = $this->db->query($sql);
		if($res->num_rows>0)
		{
			$results= $res->result_array();
		
			$results[0]['children']=$this->getChildrenDetails($results[0]['job_id']);
			if($results[0]['sitter_user_id']!=null)
			{
				$sitter=$this->user_model->sitterProfile($results[0]['sitter_user_id']);
				//if($sitter!=null && abs(strtotime($results[0]['job_start_date'])-time())<86400 &&$results[0]['job_status']!='completed')
				if($sitter!=null && abs(strtotime($results[0]['job_start_date'])-time())<10800 && $results[0]['job_status']!='completed') // 3 hr time frame for active job
				{
					$results[0]['sitter_firstname'] =$sitter->firstname;
					$results[0]['sitter_lastname'] =$sitter->lastname;
					$results[0]['sitter_username'] =$sitter->username;
					$results[0]['sitter_phone'] =$sitter->phone;
					$results[0]['sitter_main_image']=$sitter->main_image;
					$results[0]['sitter_thumb_image']=$sitter->thumb_image;
					$results[0]['sitter_orginal_image']=$sitter->orginal_image;
				}
				else
				{
					$results[0]['sitter_firstname'] ='';
					$results[0]['sitter_lastname'] ='';
					$results[0]['sitter_username'] ='';
					$results[0]['sitter_phone'] ='';
					$results[0]['sitter_main_image']='';
					$results[0]['sitter_thumb_image']='';
					$results[0]['sitter_orginal_image']='';
				}
			}
			else
			{
					$results[0]['sitter_firstname'] ='';
					$results[0]['sitter_lastname'] ='';
					$results[0]['sitter_username'] ='';
					$results[0]['sitter_phone'] ='';
					$results[0]['sitter_main_image']='';
					$results[0]['sitter_thumb_image']='';
					$results[0]['sitter_orginal_image']='';
			}
			
			$results[0] = replace_null($results[0]);
			if($results[0]['children']=='')
				$results[0]['children']=array();
			$results[0]['job_start_date'] = dateFormat($results[0]['job_start_date']);
			$results[0]['job_end_date'] = dateFormat($results[0]['job_end_date']);
			$results[0]['actual_start_date'] = dateFormat($results[0]['actual_start_date']);
			$results[0]['actual_end_date'] = dateFormat($results[0]['actual_end_date']);
			$results[0]['job_id'] = $job_id ;
			
			return $results[0];
		}
		else
		{
			return false;
		}
	}
	
	
	
	/**
	 * get job details api
	 * 
	 * @param string $job_id
	 * @return array|boolean
	 */
	function jobDetails1($job_id)
	{
		if($job_id>0)
			$query_clientId = ' and job_id='.$job_id;
	
		$sql="select  j.job_id,client_user_id,sitter_user_id,job_start_date,job_end_date,u.username,u.firstname,u.phone
		jobs_posted_date,child_count,job_status,is_special,special_need,notes,address_id,job_start_date as actual_start_date,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ) actual_end_date,completed_date,last_modified_date,total_assigned,total_paid
		from jobs j join users u
		on(j.client_user_id=u.userid )
		where u.status!='deleted'  $query_clientId ";
	
		$res = $this->db->query($sql);
		if($res->num_rows>0)
		{
			$results= $res->result_array();
	
			$results[0]['children']=$this->getChildrenDetails($results[0]['job_id']);
			if($results[0]['sitter_user_id']!=null)
			{
				$sitter=$this->user_model->sitterProfile($results[0]['sitter_user_id']);
				//if($sitter!=null && abs(strtotime($results[0]['job_start_date'])-time())<86400 && $results[0]['job_status']!='completed') // 24 hr time frame for active job
				if($sitter!=null && abs(strtotime($results[0]['job_start_date'])-time())<10800 && $results[0]['job_status']!='completed') // 3 hr time frame for active job
				{
					$results[0]['sitter_firstname'] =$sitter->firstname;
					$results[0]['sitter_lastname'] =$sitter->lastname;
					$results[0]['sitter_username'] =$sitter->username;
					$results[0]['sitter_phone'] =$sitter->phone;
					$results[0]['sitter_main_image']=$sitter->main_image;
					$results[0]['sitter_thumb_image']=$sitter->thumb_image;
					$results[0]['sitter_orginal_image']=$sitter->orginal_image;
				}
				else
				{
					$results[0]['sitter_user_id']="";
					$results[0]['sitter_firstname'] ='';
					$results[0]['sitter_lastname'] ='';
					$results[0]['sitter_username'] ='';
					$results[0]['sitter_phone'] ='';
					$results[0]['sitter_main_image']='';
					$results[0]['sitter_thumb_image']='';
					$results[0]['sitter_orginal_image']='';
				}
			}
			else
			{
				$results[0]['sitter_user_id']="";
				$results[0]['sitter_firstname'] ='';
				$results[0]['sitter_lastname'] ='';
				$results[0]['sitter_username'] ='';
				$results[0]['sitter_phone'] ='';
				$results[0]['sitter_main_image']='';
				$results[0]['sitter_thumb_image']='';
				$results[0]['sitter_orginal_image']='';
				
			}

			$results[0] = replace_null($results[0]);
			if($results[0]['children']=='')
				$results[0]['children']=array();
			$results[0]['job_start_date'] = dateFormat($results[0]['job_start_date']);
			$results[0]['job_end_date'] = dateFormat($results[0]['job_end_date']);
			$results[0]['actual_start_date'] = dateFormat($results[0]['actual_start_date']);
			$results[0]['actual_end_date'] = dateFormat($results[0]['actual_end_date']);
			$results[0]['address'] = $this->common_model->getJobAddress($results[0]['address_id']);
			
			return $results[0];
		}
		else
		{
			return false;
		}
	}
	

	/**
	 * delete job
	 * 
	 * @param string $job_id
	 * @return number|string
	 */
	function deleteJob($job_id)
	{
		$job = $this->jobDetails($job_id);
		
		if((strtotime(str_replace('-', '', $job['job_start_date']))-time())>86400 && $job['job_status']!= 'completed' && $job['job_status']!= 'cancelled')
		{
			$this->updateToClientRequests($job['client_user_id']);
			$update=array('job_status'=>'cancelled');
			$this->db->where('job_id',$job['job_id']);
			$this->db->update('jobs',$update);
			if($this->db->affected_rows()> 0 )
			{
				if($job['job_status']== 'new' || $job['job_status'] == 'pending')
					$this->package_model->reimburse($job_id);
				else
				{
					if($job['job_status']=='confirmed')
					{
						jobMail($job_id,$job,'job_cancelled','sitter','mail');
					}
				}
			}
			
			$param['type']= 'sitter';
			$this->load->library('apn',$param);
			$msg=$this->lang->line('job_cancel_noti');
			$this->common_model->send_notification($job_id,$job['sitter_user_id'],$msg);
			
			jobMail($job_id,$job,'job_request_cancelled','client','mail');
			
			return 1;
		}
		else     
		{
			if($job['job_status']=='completed' || $job['job_status']=='closed')
				$msg= "You cannot cancel a completed job.";
			else 
				$msg= $this->lang->line('job_not_cancelled');
			return $msg;
		}
	} 
	
	function addJob($userId,$data,$required_credit,$available_credits)
	{
		$imidiateJob=0;
		$imidiateJobId='';
		$start_date=$data['start_date'];
		$end_date=$data['end_date'];
		$current_date=date('Y-m-d H:i:s');
		
		if($this->input->post('current_time'))
			$current_date=$this->input->post('current_time');
		
		$start =strtotime($data['start_date']);
		
		$end_date =date('Y-m-d',$start);
		$end_time = date('H:i:s',strtotime($data['end_date']));
		
		$end = strtotime($end_date.' '.$end_time);
		
		$diff = $start- strtotime($current_date);
		$diff_in_hrs = $diff/3600;
		
		if($diff_in_hrs<3)
			$imidiateJob=1;
		
		if($required_credit>$available_credits)
		{
			$one_credit_pack=$this->package_model->package_details(1);
			
			$no_of_credit_to_purchase=$required_credit-$available_credits;
			
			$amount=0;
			if($diff_in_hrs<3 && $available_credits==0)
			{
				$amount = $this->config->item('immediate_job_price');
			}
		
			$pack_data='';
			$pay_data='';
			if($this->input->post('package_id')!='')
			{
				$pack = $this->package_model->package_details($this->input->post('package_id'));
				if($pack)
				{
				
					$amount = $amount+$pack->price;
					$no_of_credit_to_purchase= $no_of_credit_to_purchase-$pack->credits;
					
					$pack_data= array(
							'userid'				=> $userId,
							'slots'					=> $pack->credits,
							'total_credits'			=> $pack->credits,
							'price'					=> $amount,
							'start_date'			=> date('Y-m-d H:i:s',strtotime('+1 day 9am')),
							'end_date'				=> date('Y-m-d H:i:s',strtotime('+1 year 6pm')),
							'last_modified_by'		=> $userId,
							'last_modified_date'	=> date('Y-m-d H:i:s'),
							'package_id'			=> $pack->package_id,
							'payment_gateway'		=> 'Authorize.net',
							'notes'					=> $pack->package_name
					);
					
					if($no_of_credit_to_purchase>0)
					{
						$pay_data= array(
							'userid'				=> $userId,
							'slots'					=> $no_of_credit_to_purchase,
							'total_credits'			=> $no_of_credit_to_purchase,
							'price'					=> $amount,
							'start_date'			=> date('Y-m-d H:i:s',strtotime('+1 day 9am')),
							'end_date'				=> date('Y-m-d H:i:s',strtotime('+1 year 6pm')),
							'last_modified_by'		=> $userId,
							'last_modified_date'	=> date('Y-m-d H:i:s'),
							'package_id'			=> 0,
							'payment_gateway'		=> 'Authorize.net',
							'notes'					=> ucfirst(convert_number_to_words($no_of_credit_to_purchase)). " Credits"
						);
						
						while($no_of_credit_to_purchase>0)
						{
							$amount = $amount + $one_credit_pack->price;
							$no_of_credit_to_purchase--;
						}
						$pay_data['price'] = $amount-$pack->price;
					}
				}
				else
				{
					$data[0] = $this->lang->line('invalid_package');
					$data[1] = false;
					return $data;
				}
			}
			else 
			{
				$pay_data= array(
						'userid'				=> $userId,
						'slots'					=> $no_of_credit_to_purchase,
						'total_credits'			=> $no_of_credit_to_purchase,
						'price'					=> $amount,
						'start_date'			=> date('Y-m-d H:i:s',strtotime('+1 day 9am')),
						'end_date'				=> date('Y-m-d H:i:s',strtotime('+1 year 6pm')),
						'last_modified_by'		=> $userId,
						'last_modified_date'	=> date('Y-m-d H:i:s'),
						'package_id'			=> 0,
						'payment_gateway'		=> 'Authorize.net',
						'notes'					=> ucfirst(convert_number_to_words($no_of_credit_to_purchase)). " Credits"
				);
				
				createFile('purchase', print_r($pay_data, true));
				
				while($no_of_credit_to_purchase>0)
				{
					$amount = $amount + $one_credit_pack->price;
					$no_of_credit_to_purchase--;
				}
				$pay_data['price']=$amount;
			}
			
			$k= array('card_num'=>'','exp_date'=>'','card_code'=>'');
			if($this->input->post('authorizenet_payment_profile_id')=='')
			{
				$k = getCCInfo($this->input->post('card_info'));
				//$k['exp_date']='12/2016';
			}
			$payment_data = array(
					'user_id' =>$userId,
					'price' => $amount,
					'card_number' => $k['card_num'],
					'expiry_date' =>$k['exp_date'],
					'cvv'	=> $k['card_code'],
					'address'=>$this->input->post('streat_address'),
					'city'=>$this->input->post('city'),
					'state'=>$this->input->post('state'),
					'zipcode'=>$this->input->post('zipcode'),
					'country'=>$this->input->post('country'),
					'save_card'=>$this->input->post('save_card'),
					'name_on_card'=>$this->input->post('name_on_card'),
					'payment_profile_id'=>($this->input->post('authorizenet_payment_profile_id')!='')?base64_decode($this->input->post('authorizenet_payment_profile_id')):''
			);
			
			$transId = $this->common_model->processPayment($payment_data);
			
			if($transId[0]!=false)
			{	
				if($pack_data!='')
				{
					$pack_data['transaction_id']=$transId[0];
					$packageData = $this->package_model->buyPackage($pack_data);
					if($packageData==false)
					{
						$data[0] = $this->lang->line('package_not_found');
						$data[1] = false;
						return $data;
					}
				}
				
				if($pay_data!='')
				{
					$pay_data['transaction_id']=$transId[0];
					$packageData = $this->package_model->buyPackage($pay_data);
				}
				
				$additional = array(
						'{transaction_id}'=>$transId[0],
						'{credits}'=>$required_credit-$available_credits,
						'{amount}'=>$amount,
				);
				//if($subs['checkout_type'] !='free')
				clientMail($userId,'payment_confirmation','client','mail',$additional );
			}
			else
			{
				$data[0] = $transId[1];
				$data[1] = false;
				return $data;
			}
			
		}
		
		$insertMaster = array('actual_start_date'=>date('Y-m-d H:i:s',$start),'actual_end_date'=>date('Y-m-d H:i:s',strtotime($data['end_date'])));
		
		$this->db->insert('job_master',$insertMaster);
		$master_job_id = $this->db->insert_id();
		
		$rate = $this->common_model->getRate($data['child_count']);

		$log='';
		for($i=1;($i<=$required_credit);$i++)
		{	
			$sql= "select * from clients_subscription where end_date > now() and userid=".$data['userid']." and slots > 0 order by client_sub_id asc limit 0,1";
			$res = $this->db->query($sql);

			if($res->num_rows()<=0)
			{
				$data[0] = "You do not have enough credits.";
				$data[1] =false;
				return $data;
			}
		
			$result = $res->result_array();
			$result = $result[0];
			if($end-$start<0)
			{ 
				//$end = $end+(60*60*24);
				$end = strtotime('+1 day', $end);
				if ($end>strtotime($data['end_date']))
				{
					break;
				}
			}
			$insert = array( 
					'master_job_id'=>$master_job_id,
					'client_user_id'=>$data['userid'],
					'jobs_posted_date'=>date('Y-m-d H:i:s'),
					'job_start_date'=>date('Y-m-d H:i:s',$start),
					'job_end_date'=>date('Y-m-d H:i:s',$end),
					'child_count'=>$data['child_count'],
					'actual_child_count'=>$data['child_count'],
					'sitter_user_id'=>$data['sitter_id'],
					'job_added_by'=>$data['userid'],
					'last_modified_date'=>date('Y-m-d H:i:s'),
					'last_modified_by'=>$data['userid'],
					'job_status'=>$data['job_status'],
					'notes'=>$data['notes'],
					'is_special'=>$data['is_special'],
					'special_need'=>$data['special_need'],
					'address_id'=>$data['address_id'],
					'subs_id'=>$result['client_sub_id'],
					'credits_used'=>1
			);
			
			if($rate)
			{
				$insert['client_rate']=$rate->client_rate;
				$insert['sitter_rate_pre']= $rate->sitter_rate;
			}
			
			if($data['child_count']>4)
			{
				$insert['is_special'] = '1';
			}
			
			$this->db->insert('jobs',$insert);
			$this->job_id = $this->db->insert_id();
			
			if($i==1 && $imidiateJob ==1 )
			{
				$data_log=array('job_id'=>$this->job_id,
						'modified_by'=>$insert['job_added_by'],
						'modified_date'=>date('Y-m-d H:i:s'),
						'modification'=>'Immediate Job',
						'initial' =>0,
						'modified'=>1
				);
				$this->db->insert('job_logs', $data_log);
				$imidiateJobId=$this->job_id;
			}
	
			if($insert['is_special']=='1')
			{
				$data_log=array('job_id'=>$this->job_id,
						'modified_by'=>$insert['job_added_by'],
						'modified_date'=>date('Y-m-d H:i:s'),
						'modification'=>'Special Condition',
						'initial' =>0,
						'modified'=>1
				);
				$this->db->insert('job_logs', $data_log);
			}
			
			$slot_sql="update clients_subscription set slots =slots-1 where client_sub_id = ".$result['client_sub_id'];
			$this->db->query($slot_sql);
			
			$log=$log.'<\n>'.$slot_sql;
			createFile('slots', print_r($log, true));
			
			$this->addChilds($this->job_id,$data['children']);
			$this->updateToClientRequests($data['userid']);
			$this->updateCredits($data['userid']);
			
			if(is_array($data['prefer'])&& !empty($data['prefer']) && $data['prefer'][0]!='')
			{
				$this->addPreference($data['prefer'],$this->job_id );
			}
			
			if($insert['is_special']=='0')
			{
				$url= base_url().'parent/job/assign_sitters';
				$post = array(
						'api_key'=>'babysitter@123',
						'job_id' => $this->job_id,
						'userid' => $data['userid'],
						'jobno'	=>$i
				);
				
				ignore_user_abort(true);
				set_time_limit(0);
				
				$c = curl_init();
				curl_setopt($c, CURLOPT_URL, $url);
				curl_setopt($c,CURLOPT_POST,1);
				curl_setopt($c,CURLOPT_POSTFIELDS,$post);
				curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);  // Follow the redirects (needed for mod_rewrite)
				curl_setopt($c, CURLOPT_RETURNTRANSFER, true);  // Return from curl_exec rather than echoing
				curl_setopt($c, CURLOPT_FRESH_CONNECT, true);   // Always ensure the connection is fresh
				
				// Timeout super fast once connected, so it goes into async.
				curl_setopt( $c, CURLOPT_TIMEOUT, 1 );
				
				$output = curl_exec($c);
				curl_close($c);
				
				//$this->assign_sitters($this->job_id, $data['userid']);
			}
			else
			{
				
			}
			
			if(strtotime($data['end_date'])<strtotime('+1 day', $start))
			{
				break;
			}
			$start =strtotime('+1 day', $start);
			$end = strtotime(date('Y-m-d',$start).' '.$end_time);
			
			//$end =$end+(60*60*24);
		}
		
		$data[0] = $this->lang->line('job_added');
		$job_details = $this->jobDetails($this->job_id);
		
		$this->db->select('*');
		$this->db->where('user_id',$data['userid']);
		$payment_profile = $this->db->get('client_payment_profile');
		
		//if profile exist on authorize.net
		if($payment_profile->num_rows()>0)
		{
			$p = $payment_profile->row();
			$job_details['authorizenet_payment_profile_id'] = base64_encode($p->authorizenet_payment_profile_id);
		}
		else
		{
			$job_details['authorizenet_payment_profile_id'] ='';
		}
		
		if($imidiateJobId!='')
		{
			$immijob = $this->jobDetails($imidiateJobId);
			jobMail($imidiateJobId,$immijob,'immediate_job_admin','mail','client');
		}
		
		jobMail($this->job_id,$job_details,'job_request_recieved','client','mail');
		$data[1] = $job_details;
		return $data;
	}
	
	
	function assign_sitters($job_id,$userid,$jobno)
	{
		$sql = "select distinct userid from users where usertype = 'S' and (status='active' or status='' )";
		$all_sitter = $this->db->query($sql);
		if($all_sitter->num_rows()>0)
		{
			$param['type']= 'sitter';
			$this->load->library('apn',$param);
			
			$all_sitter = $all_sitter->result_array();
			createFile('assigned_sitters', print_r($all_sitter, true));
			
			
			foreach($all_sitter as $row)
			{
				$sql2 = "select job_status from jobs where job_id=".$job_id ;
				$res2 = $this->db->query($sql2);
				$jobstatusflag = $res2->result_array();
				 
				if($jobstatusflag[0]['job_status']!='new' && $jobstatusflag[0]['job_status']!='pending')
				{
					break;
				}
				elseif($jobstatusflag[0]['job_status']=='new')
				{
					$update=array('job_status'=>'pending');
					$this->db->where('job_id',$job_id);
					$this->db->update('jobs', $update);
				}

				$sent_insert=array(
						'job_id'=>$job_id,
						'sent_to'=>$row['userid'],
						'sent_by'=>$userid,
						'sent_date'=>date('Y-m-d H:i:s'),
						'sent_status'=>'new'	
				);
					
				$sql1="select * from job_sent where job_id=".$job_id." and sent_to =".$row['userid'];
				$res1 = $this->db->query($sql1);
				$flag = $res1->result_array();
				if(empty($flag))
				{
					$this->db->insert('job_sent',$sent_insert);
					$this->db->query('update sitters set available_jobs=available_jobs+1 where userid='.$row['userid']);
					
					if($jobno==1)
					{
						$msg=$this->lang->line('new_job_posted');
						$this->common_model->send_notification($job_id,$row['userid'],$msg);
					}
				}
				else
				{
					$this->db->query("update job_sent set sent_by=".$userid." and sent_date=now() and sent_status='new'");
				}
					
				// Confirm job to admin
				//jobMail($job_id,$job_details,'send_to_sitter','sitter','mail',$row['userid']);
					
			}
		}
		$this->db->query('update jobs set total_assigned = (select count(*) from job_sent where job_id ='.$job_id.') where job_id ='.$job_id);
	}
	
	/**
	 * get job list
	 *
	 * @param string $client_user_id
	 * @param string $jstatus
	 * @param integer $perPage
	 * @param integer $offset
	 * @return array
	 */
	function jobListNew($client_user_id,$jstatus,$perPage='',$offset='')
	{
		$ori_jstatus=$jstatus;
		if($jstatus == 'confirmed' || $jstatus == 'completed' || $jstatus=='active' )
		{
			$sql = 'j.job_id,jobs_posted_date,job_start_date,job_end_date,client_user_id,j.sitter_user_id,job_status,round(ifnull( client_updated_rate,client_rate ),2 ) as rate,notes,special_need,is_special,address_id,
			u.username as sitter_username,u.firstname as sitter_firstname,u.lastname as sitter_lastname,u.phone as sitter_phone,
			job_start_date as actual_start_date,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ) actual_end_date';
				
			$this->db->select($sql, false);
			$this->db->join('users u','u.userid = j.sitter_user_id','left');
			$this->db->where('client_user_id',$client_user_id);
			
			if($ori_jstatus == 'completed')
			{
				//$this->db->where_in('j.job_status',array('completed','closed')); //commented for cancelled job
				$where1= "((j.job_status='completed' OR j.job_status='closed') OR ( j.job_status = 'cancelled' AND j.immidiate_cancelled ='yes' ))"; //changes for cancelled job
				$this->db->where($where1); //changes for cancelled job
				$this->db->order_by('job_start_date','DESC');
			}
			else
			{
				if($ori_jstatus=='confirmed')
				{
					$where="((date_format( '".date('Y-m-d H:i:s')."'  , '%Y-%m-%d %H:%i' )  + INTERVAL 0 HOUR) not between (date_format( job_start_date, '%Y-%m-%d %H:%i' ) + INTERVAL -3 HOUR ) AND (date_format( job_end_date , '%Y-%m-%d %H:%i' )  + INTERVAL 3 HOUR))";
					$this->db->where($where);
				}
				else
				{
					$where="((date_format( '".date('Y-m-d H:i:s')."'  , '%Y-%m-%d %H:%i' )  + INTERVAL 0 HOUR) between (date_format( job_start_date, '%Y-%m-%d %H:%i' ) + INTERVAL -3 HOUR ) AND (date_format( job_end_date , '%Y-%m-%d %H:%i' )  + INTERVAL 3 HOUR))";
					$this->db->where($where);
					$jstatus='confirmed';
				}
				
				$this->db->order_by('job_start_date','ASC');
				$this->db->where('job_status',$jstatus);
			}
				
			$result = $this->db->get('jobs j');
			$r[0] = $result->num_rows();
	
			if($perPage!='')
			{
				$this->db->select($sql, false);
				$this->db->join('users u','u.userid = j.sitter_user_id');
				$this->db->where('client_user_id',$client_user_id);
				if($ori_jstatus == 'completed')
				{
					//$this->db->where_in('j.job_status',array('completed','closed'));  //commented for cancelled job
					$where1= "((j.job_status='completed' OR j.job_status='closed') OR ( j.job_status = 'cancelled' AND j.immidiate_cancelled ='yes' ))"; //changes for cancelled job
					$this->db->where($where1); //changes for cancelled job
					$this->db->order_by('job_start_date','DESC');
				}
				else
				{
					if($ori_jstatus=='confirmed')
					{
						$where="((date_format('".date('Y-m-d H:i:s')."', '%Y-%m-%d %H:%i' )  + INTERVAL 0 HOUR) not between (date_format( job_start_date, '%Y-%m-%d %H:%i' ) + INTERVAL -3 HOUR ) AND (date_format( job_end_date , '%Y-%m-%d %H:%i' )  + INTERVAL 3 HOUR))";
						$this->db->where($where);
					}
					else
					{
						$where="((date_format( '".date('Y-m-d H:i:s')."'  , '%Y-%m-%d %H:%i' )  + INTERVAL 0 HOUR) between (date_format( job_start_date, '%Y-%m-%d %H:%i' ) + INTERVAL -3 HOUR ) AND (date_format( job_end_date , '%Y-%m-%d %H:%i' )  + INTERVAL 3 HOUR))";
						$this->db->where($where);
						$jstatus='confirmed';
					}
					
					$this->db->where('job_status',$jstatus);
					$this->db->order_by('job_start_date','ASC');
				}
	
				$result = $this->db->get_where('jobs j',array(),$perPage,$offset);
			}
		}
		else
		{
			if($jstatus == 'cancelled') //changes for cancelled job.
			{
				$this->db->where('j.job_status','cancelled');
				$this->db->where('immidiate_cancelled','no');
				$this->db->order_by('job_start_date','DESC');
			}
			else 
			{
				$this->db->where_in('j.job_status',array('pending','new'));
				$this->db->order_by('job_start_date','ASC');
			} //end changes for cancelled job.
			
			$sql = 'j.job_id,jobs_posted_date,job_start_date,job_end_date,client_user_id,j.sitter_user_id,job_status,round(ifnull( client_updated_rate,client_rate ),2 ) as rate,notes,special_need,is_special,address_id';
			$this->db->select($sql,false);
			$this->db->where('client_user_id',$client_user_id);
			//$this->db->where_in('j.job_status',array('pending','new')); //commented for cancelled job
			//$this->db->order_by('job_start_date','ASC');
			$result = $this->db->get('jobs j');
			$r[0] = 	$result->num_rows();
			if($perPage!='')
			{
				if($jstatus == 'cancelled') //changes for cancelled job.
				{
					$this->db->where('j.job_status','cancelled');
					$this->db->where('immidiate_cancelled','no');
					$this->db->order_by('job_start_date','DESC');
				}
				else
				{
					$this->db->where_in('j.job_status',array('pending','new'));
					$this->db->order_by('job_start_date','ASC');
				}
				$this->db->select($sql,false);
				$this->db->where('client_user_id',$client_user_id);
				//$this->db->where_in('j.job_status',array('pending','new'));  //commented for cancelled job
				//$this->db->order_by('job_start_date','ASC');
				$result = $this->db->get_where('jobs j',array(),$perPage,$offset);
			}
		}
		$res = $result->result();
		if($result->num_rows()>0)
		{
			$i=0;
			foreach($res as $row)
			{
				if($res[$i]->sitter_user_id == null ||$res[$i]->sitter_user_id == 0)
					$res[$i]->sitter_user_id='';
				if($res[$i]->rate == null ||$res[$i]->rate == 0)
					$res[$i]->rate='';
				if($res[$i]->notes == null ||$res[$i]->notes == 0)
					$res[$i]->notes='';
				
				if($ori_jstatus == 'completed')
				{
					$res[$i]->total_hours = round((strtotime(date('Y-m-d H:i:s',strtotime($res[$i]->actual_end_date)))-strtotime(date('y-m-d H:i:s',strtotime($res[$i]->actual_start_date))))/(60*60),2);
					$res[$i]->total_paid =$res[$i]->total_hours*$res[$i]->rate;
				}
				
				
				if($ori_jstatus!='active')
				{
					$res[$i]->sitter_user_id="";
					$res[$i]->sitter_firstname = "";
					$res[$i]->sitter_lastname = "";
					$res[$i]->sitter_username ="";
					$res[$i]->sitter_phone ="";
				}
				$res[$i]->jobs_posted_date = dateFormat($row->jobs_posted_date);
				$res[$i]->job_start_date = dateFormat($row->job_start_date);
				$res[$i]->job_end_date = dateFormat($row->job_end_date);
				//$res[$i]->actual_start_date = dateFormat($row->actual_start_date);
				//$res[$i]->actual_end_date = dateFormat($row->actual_end_date);
				$res[$i]->children = $this->getChildrenDetails($row->job_id);
				$res[$i]->address = $this->common_model->getJobAddress($row->address_id);
	
				$i++;
			}
		}
		$r[1] = $res;
		return $r;
	
	}
	
	
	/**
	 * delete job
	 *
	 * @param string $job_id
	 * @return number|string
	 */
	function deleteJobNew($job_id)
	{
		$job = $this->jobDetails($job_id);
	
		if($job['job_status']!= 'completed' && $job['job_status']!= 'cancelled' && $job['job_status']!= 'closed')
		{
			if((strtotime(str_replace('-', '', $job['job_start_date']))-time())>86400)
				$update=array('job_status'=>'cancelled','cancelled_date'=>date('Y-m-d H:i:s'),'immidiate_cancelled'=>'no');
			else
				$update=array('job_status'=>'cancelled','cancelled_date'=>date('Y-m-d H:i:s'),'immidiate_cancelled'=>'yes');
			
			$this->db->where('job_id',$job['job_id']);
			$this->db->update('jobs',$update);
			
			if($this->db->affected_rows()> 0 )
			{
				$this->updateToClientRequests($job['client_user_id']);
					
				//if(strtotime(str_replace('-', '', $job['job_start_date']))>time())
					//$this->package_model->reimburse($job_id);
				$data_log=array('job_id'=>$job_id,
						'modified_by'=>$job['client_user_id'],
						'modified_date'=>date('Y-m-d H:i:s'),
						'modification'=>'Client Cancellation',
						'initial' =>0,
						'modified'=>0
				);
				$this->db->insert('job_logs', $data_log);
				
				if($job['job_status']=='confirmed')
				{
					jobMail($job_id,$job,'job_cancelled','sitter','mail');
				}
				
				$param['type']= 'sitter';
				$this->load->library('apn',$param);
				$msg=$this->lang->line('job_cancel_noti');
				$this->common_model->send_notification($job_id,$job['sitter_user_id'],$msg);
				
				jobMail($job_id,$job,'job_request_cancelled','client','mail');
				
				return 1;
			}
		}
		else
		{
			if($job['job_status']=='completed' || $job['job_status']=='closed')
				$msg= "You cannot cancel a completed job.";
			else
				$msg= "Unable to cancel Job."; 
			return $msg;
		}
	}
	
	
}
