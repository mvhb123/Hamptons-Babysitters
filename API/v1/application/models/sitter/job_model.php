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
		$this->load->model('parent/package_model');
		$this->load->model('common_model');
	}
	
	function getChildrenDetails($job_id)
	{
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
			
			$check['age'] =child_age($res['dob']);		
			$r[$i]=(object)$check;
			$i++;
		}
		return $r;
	}
	
	
	function clientJobDetails($job_id)
	{
		if($job_id>0)
			$query_clientId = ' job_id='.$job_id;
		else 
			$query_clientId=1;
	
		$sql="select  j.job_id,client_user_id,sitter_user_id,job_start_date,job_end_date,u.username,u.firstname,u.phone,ifnull( client_updated_rate,client_rate ) rate,((TIMESTAMPDIFF(MINUTE, job_start_date ,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ))/60)*ifnull( client_updated_rate,client_rate )) total_paid,
		jobs_posted_date,ifnull( actual_child_count,child_count ) child_count,job_status,notes,address_id,job_start_date as actual_start_date,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ) actual_end_date,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ) completed_date,last_modified_date,total_assigned
		from jobs j join users u
		on(j.client_user_id=u.userid )
		where $query_clientId ";
	
		$res = $this->db->query($sql);
		if($res->num_rows>0)
		{
			$results= $res->result_array();
	
			$results[0]['children']=$this->getChildrenDetails($results[0]['job_id']);
			if($results[0]['sitter_user_id']!=null)
			{
				$sitter=$this->user_model->sitterProfile($results[0]['sitter_user_id']);
				if($sitter!=null && abs(strtotime($results[0]['job_start_date'])-time())<86400 &&$results[0]['job_status']!='completed')
				{
					$results[0]['sitter_firstname'] =$sitter->firstname;
					$results[0]['sitter_lastname'] =$sitter->lastname;
					$results[0]['sitter_username'] =$sitter->username;
					$results[0]['sitter_phone'] =$sitter->phone;
				}
				else
				{
					$results[0]['sitter_firstname'] ='';
					$results[0]['sitter_lastname'] ='';
					$results[0]['sitter_username'] ='';
					$results[0]['sitter_phone'] ='';
				}
			}
			else
			{
				$results[0]['sitter_firstname'] ='';
				$results[0]['sitter_lastname'] ='';
				$results[0]['sitter_username'] ='';
				$results[0]['sitter_phone'] ='';
			}
				
			$results[0] = replace_null($results[0]);
				
			$results[0]['job_start_date'] = dateFormat($results[0]['job_start_date']);
			$results[0]['job_end_date'] = dateFormat($results[0]['job_end_date']);
			$results[0]['actual_start_date'] = dateFormat($results[0]['actual_start_date']);
			$results[0]['actual_end_date'] = dateFormat($results[0]['actual_end_date']);
				
			return $results[0];
		}
		else
		{
			return false;
		}
	}
	
	function jobList($sitter_user_id,$jstatus,$perPage='',$offset='')
	{
		if($jstatus == 'confirmed' || $jstatus == 'completed' )
		{
			$tp_select_query='';
			if($jstatus == 'completed')
				$tp_select_query = ",total_paid,sitter_payment_status as payment_status";
			
			$select_sql = 'j.job_id,jobs_posted_date,job_start_date,job_end_date,completed_date,child_count,actual_child_count,client_user_id,j.sitter_user_id,job_status,round(ifnull( rate,sitter_rate_pre ),2 ) as rate,notes,address_id,
					u.username,u.firstname ,u.lastname ,u.phone ,p.local_phone,
					c.spouse_firstname,c.spouse_lastname,c.spouse_phone1,c.spouse_phone2,
					p.emergency_contact,p.emergency_phone,p.emergency_relation,job_start_date as actual_start_date,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ) actual_end_date'."$tp_select_query";
			
			$this->db->select($select_sql,false);
			$this->db->join('users u','u.userid = j.client_user_id');
			$this->db->join('clients_detail c','u.userid = c.userid');
			$this->db->join('user_profile p','u.userid = p.userid');
			//$this->db->join('job_master jm','jm.master_job_id = j.master_job_id');
			$this->db->where('sitter_user_id',$sitter_user_id);
			if($jstatus == 'completed')
			{
				$this->db->where_in('j.job_status',array('completed','closed'));
				$this->db->order_by('job_start_date','DESC');
			}
			else 
			{
				$this->db->where('j.job_status',$jstatus);
				$this->db->order_by('job_start_date','ASC');
			}
			$result = $this->db->get('jobs j');
			$data[0] = 	$result->num_rows();
			
			if($perPage!='')	
			{
				$this->db->select($select_sql,false);
				$this->db->join('users u','u.userid = j.client_user_id');
				$this->db->join('clients_detail c','u.userid = c.userid');
				$this->db->join('user_profile p','u.userid = p.userid');
				//$this->db->join('job_master jm','jm.master_job_id = j.master_job_id');
				$this->db->where('sitter_user_id',$sitter_user_id);
				if($jstatus == 'completed')
				{
					$this->db->where_in('j.job_status',array('completed','closed'));
					$this->db->order_by('job_start_date','DESC');
				}
				else 
				{
					$this->db->where('job_status',$jstatus);
					$this->db->order_by('job_start_date','ASC');
				}
				$result = $this->db->get_where('jobs j',array(),$perPage,$offset);
			}
		}
		else
		{
			$select_sql = 'j.job_id,jobs_posted_date,job_start_date,job_end_date,completed_date,child_count,actual_child_count,client_user_id,j.sitter_user_id,job_status,round(ifnull( rate,sitter_rate_pre ),2 ) as rate,notes,address_id,
						u.username ,u.firstname ,u.lastname ,u.phone ,p.local_phone,
						c.spouse_firstname,c.spouse_lastname,c.spouse_phone1,c.spouse_phone2,
						p.emergency_contact,p.emergency_phone,p.emergency_relation,job_start_date as actual_start_date,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ) actual_end_date';
			
			$this->db->select($select_sql,false);
			$this->db->join('users u','u.userid = j.client_user_id');
			$this->db->join('clients_detail c','u.userid = c.userid');
			$this->db->join('user_profile p','u.userid = p.userid');
			//$this->db->join('job_master jm','jm.master_job_id = j.master_job_id');
			$this->db->join('job_sent js','js.job_id = j.job_id');
			$this->db->where('j.job_start_date > now()');
			$this->db->where('js.sent_to',$sitter_user_id);
			$this->db->where('js.sent_status','new');
			$this->db->order_by('job_start_date','ASC');
			$this->db->where_in('j.job_status',array('pending'));
			$result = $this->db->get('jobs j');
			$data[0] = 	$result->num_rows();
			
			if($perPage!='')
			{
				$this->db->select($select_sql,false);
				$this->db->join('users u','u.userid = j.client_user_id');
				$this->db->join('clients_detail c','u.userid = c.userid');
				$this->db->join('user_profile p','u.userid = p.userid');
				//$this->db->join('job_master jm','jm.master_job_id = j.master_job_id');
				$this->db->join('job_sent js','js.job_id = j.job_id');
				$this->db->where('j.job_start_date > now()');
				$this->db->where('js.sent_to',$sitter_user_id);
				$this->db->where('js.sent_status','new');
				$this->db->order_by('job_start_date','ASC');
				$this->db->where_in('j.job_status',array('pending'));
				$result = $this->db->get_where('jobs j',array(),$perPage,$offset);
			}
		}
		
		$res = $result->result_array();
		
		if($result->num_rows()>0)
		{
			$i=0;
			foreach($res as $row)
			{
				$check[] = array();
				$check = replace_null($row);//print_r($row);
				if(abs(strtotime($res[$i]['job_start_date'])-time())<86400 || abs(time()-strtotime($res[$i]['completed_date']))<14400)
				{
					if($res[$i]['job_status']=='confirmed')
					{
						$check['job_status'] = 'active';
					}
				}
				else 
				{
					$check['firstname'] = "";
					$check['username'] = "";
					$check['lastname'] = "";
					$check['phone'] = "";
					$check['local_phone'] = "";
					if($res[$i]['job_status']=='confirmed')
					{
						$check['job_status'] = 'inactive';
					}
				}
				
				$check['parent_info']=array('username'=>$check['username'],
						'phone' => $check['phone'],
						'firstname'=>$check['firstname'],
						'lastname'=>$check['lastname'],
						'local_phone'=>$check['local_phone']
						);
				
				unset($check['username']);
				unset($check['firstname']);
				unset($check['lastname']);
				unset($check['phone']);
				unset($check['local_phone']);
				
				//$check['total_hours'] = round((strtotime(date('Y-m-d H:i:s',strtotime($res[$i]['actual_end_date'])))-strtotime(date('y-m-d H:i:s',strtotime($res[$i]['actual_start_date']))))/(60*60),2);
				$start_date = new DateTime($res[$i]['actual_end_date']);
				$end_date = new DateTime($res[$i]['actual_start_date']);
				$interval = $start_date->diff($end_date);
				$total_time   = $interval->format('%H:%i');
				$check['total_hours'] = $total_time;
				$check['total_paid'] = $check['total_hours']*$check['rate'];
				/* if($check['job_status']=='completed')
				{
					if($check['payment_status']=='paid')
					{
						$owned=$owned+$check['total_paid'];
					}
					else
					{	
						if(date('Y')==date('Y',strtotime($check['completed_date'])))
							$earned=$earned+$check['total_paid'];
					}
				} */
				
				$check['job_status']=ucwords(strtolower($check['job_status']));
				$check['jobs_posted_date'] = dateFormat($row['jobs_posted_date']);
				$check['job_start_date'] = dateFormat($row['job_start_date']);
				$check['job_end_date'] = dateFormat($row['job_end_date']);
				$check['actual_start_date'] = dateFormat($row['actual_start_date']);
				$check['actual_end_date'] = dateFormat($row['actual_end_date']);
				$check['children'] = $this->getChildrenDetails($row['job_id']);
				$check['address'] = $this->common_model->getJobAddress($row['address_id']);
				
				$r[$i]=(object)$check;
				$i++;
			}
			
			if($jstatus=='completed' ||$jstatus=='closed')
			{
				//total owed
				$this->db->select('SUM((TIMESTAMPDIFF(MINUTE, job_start_date ,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ))/60)*ifnull( rate,sitter_rate_pre )) total_owed');
				//$this->db->join('job_master m','m.master_job_id=jobs.master_job_id');
				//$this->db->where('job_status','completed');
				$this->db->where_in('job_status',array('completed','closed'));
				$this->db->where('sitter_user_id',$sitter_user_id);
				$this->db->where('sitter_payment_status','unpaid');
				$total_own = $this->db->get('jobs');
				if($total_own->num_rows()>0)
				{
					$total_own = $total_own->row();
					if($total_own->total_owed !=null)
						$data[2] =round($total_own->total_owed,2);
				}
				
				//total paid
				//job_start_date as actual_start_date,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ) actual_end_date
				$this->db->select('SUM((TIMESTAMPDIFF(MINUTE,job_start_date ,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ) )/60)*ifnull( rate,sitter_rate_pre )) total_paid');
				$this->db->join('job_master m','m.master_job_id=jobs.master_job_id');
				//$this->db->where('job_status','completed');
				$this->db->where_in('job_status',array('completed','closed'));
				$this->db->where('sitter_user_id',$sitter_user_id);
				$this->db->where('sitter_payment_status','paid');
				//$this->db->where('YEAR(completed_date) != 0');
				//$this->db->where('YEAR(completed_date) = YEAR(now())');
				$total_paid = $this->db->get('jobs');
				if($total_paid->num_rows()>0)
				{
					$total_paid = $total_paid->row();
					if($total_paid->total_paid !=null)
						$data[3] =round($total_paid->total_paid,2);
				}
				
				//total earned
				$this->db->select('SUM((TIMESTAMPDIFF(MINUTE,job_start_date,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date )  )/60)*ifnull( rate,sitter_rate_pre )) total_earned');
				$this->db->join('job_master m','m.master_job_id=jobs.master_job_id');
				//$this->db->where('job_status','completed');
				$this->db->where_in('job_status',array('completed','closed'));
				$this->db->where('sitter_user_id',$sitter_user_id);
				//$this->db->where('sitter_payment_status','paid');
				//$this->db->where('YEAR(completed_date) != 0');
				//$this->db->where('YEAR(completed_date) = YEAR(now())');
				$total_paid = $this->db->get('jobs');
				if($total_paid->num_rows()>0)
				{
					$total_paid = $total_paid->row();
					if($total_paid->total_earned !=null)
						$data[4] =round($total_paid->total_earned,2);
				}
			}
			
			$data[1] = $r;
		}
		
		return $data;
	}	
	
	
	function jobDetails($job_id)
	{
		if($job_id>0)
			$query_jobId = ' and job_id ='.$job_id;
		
		$sql="select j.job_id,client_user_id,sitter_user_id,job_start_date,job_end_date,special_need,is_special
		jobs_posted_date,child_count,round(ifnull( rate,sitter_rate_pre ),2 ) as rate,job_status,
		notes,address_id,job_start_date as actual_start_date,
		if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ) actual_end_date ,
		if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ) completed_date,last_modified_date,total_paid,total_assigned,
		u.firstname,u.lastname,u.phone,u.username,p.local_phone,c.spouse_firstname,c.spouse_lastname,c.spouse_phone1,c.spouse_phone2,
		p.emergency_contact,p.emergency_phone,p.emergency_relation from jobs j join users u join clients_detail c join user_profile p
		on(j.client_user_id=u.userid and u.userid = c.userid and u.userid = p.userid )
		where u.status!='deleted'  $query_jobId ";
	
		$res = $this->db->query($sql);
		
		if($res->num_rows>0)
		{
			$results= $res->result_array();
			$results[0]['preferences'] =$this->common_model->selected_prefer('job',$job_id);
			
			//$this->db->select('	address_id,billing_name,address_1,address_2,streat_address,zipcode,state,country,address_type');
			//$this->db->where('address_id',$results[0]['address_id']);
			//$jresult = $this->db->get('address');	
			//$job_address= $jresult->result_array();
			//$results[0]['address'] = (object)replace_null($job_address[0]);
		
			$results[0]['address'] = $this->common_model->getJobAddress($results[0]['address_id']);
			
			$results[0]['children']=$this->getChildrenDetails($results[0]['job_id']);
			
			$lowerlimit1 =strtotime('-3 hours',strtotime($results[0]['job_start_date']));
			$upperLimit1= strtotime('+3 hours',strtotime($results[0]['job_end_date']));
			if($results[0]['job_status']=='confirmed')
			{
				if( $lowerlimit1 <= time() && time() <= $upperLimit1)
				{
					$results[0]['job_status']='active';
				}
				else
				{
					$results[0]['job_status']='inactive';
					$results[0]['firstname']= "";
					$results[0]['lastname']= "";
					$results[0]['username']= "";
					$results[0]['phone']= "";
					$results[0]['local_phone']='';
				}
			
			}
			
			/* if((abs(strtotime($results[0]['job_start_date'])-time())>86400 && $results[0]['job_status']!='completed')||($results[0]['job_status']=='completed'&& abs(time()-strtotime($results[0]['completed_date']))>14400))
			{
				$results[0]['firstname']= "";
				$results[0]['lastname']= "";
				$results[0]['username']= "";
				$results[0]['phone']= "";
				$results[0]['local_phone']='';
			} */
						
			if($results[0]['sitter_user_id']!= null)
			{
				$sitter=$this->user_model->sitterProfile($results[0]['sitter_user_id']);
				
				if($sitter!=null)
				{
					$results[0]['sitter_firstname'] =$sitter->firstname;
					$results[0]['sitter_username'] =$sitter->username;
					$results[0]['sitter_phone'] =$sitter->phone;
				}
				else
				{
					$results[0]['sitter_firstname'] ='-';
					$results[0]['sitter_username'] ='';
					$results[0]['sitter_phone'] ='';
				}
			}
			else
			{	
				$results[0]['sitter_firstname'] ='-';
				$results[0]['sitter_username'] ='';
				$results[0]['sitter_phone'] ='';
			}
			
			$results[0]['parent_info']=array('username'=>$results[0]['username'],
					'phone' =>$results[0]['phone'],
					'firstname'=>$results[0]['firstname'],
					'lastname'=>$results[0]['lastname'],
					'local_phone'=>$results[0]['local_phone']
			);
			
		//	unset($results[0]['username']);
		//	unset($results[0]['firstname']);
		//	unset($results[0]['lastname']);
		//	unset($results[0]['phone']);
		//	unset($results[0]['local_phone']);
			
			$results[0]['total_hours'] = round((strtotime(date('Y-m-d H:i:s',strtotime($results[0]['actual_end_date'])))-strtotime(date('y-m-d H:i:s',strtotime($results[0]['actual_start_date']))))/(60*60),2);
			
			if($results[0]['total_paid']==0)
				$results[0]['total_paid'] = $results[0]['total_hours']*$results[0]['rate'];
			
			$start_date = new DateTime($results[0]['actual_end_date']);
			$end_date = new DateTime($results[0]['actual_start_date']);
			$interval = $start_date->diff($end_date);
			$total_time   = $interval->format('%H:%i');
			$result[0]['total_hours'] = $total_time;
			
			$results[0]['job_status'] = ucwords(strtolower($results[0]['job_status']));
			$results[0]['job_start_date'] = dateFormat($results[0]['job_start_date']);
			$results[0]['job_end_date'] = dateFormat($results[0]['job_end_date']);
			$results[0]['actual_start_date'] = dateFormat($results[0]['actual_start_date']);
			$results[0]['actual_end_date'] = dateFormat($results[0]['actual_end_date']);
			
			$results[0] = replace_null($results[0]);
			if($results[0]['children']=='')
				$results[0]['children']=array();
			return $results[0];
		}
		else
		{
			return false;
		}
	}
	
	
	function confirmJob($job_id,$sitter_id)
	{
		/*      where ( 
						j1.job_start_date
						BETWEEN DATE_SUB( j.job_start_date, INTERVAL 239 MINUTE ) 
						AND DATE_ADD( j.job_start_date, INTERVAL 239 MINUTE )
					  ) 
		 */
		$this->db->select('job_id,job_status');
		$this->db->where('job_id',$job_id);
		//$this->db->where('job_status =','cancelled');
		$status_sql=$this->db->get('jobs');
		if($status_sql->num_rows()>0)
		{
			$status_sql=$status_sql->row();
			if($status_sql->job_status == 'cancelled')
				return "Job has been cancelled by the parent. ";
			if($status_sql->job_status == 'confirmed')
				return "Job is already booked. ";
		}
		else
		{
			return "Job has been cancelled by the parent. ";
		}
		
		
		$sql1= "Select j1.job_id from jobs j, jobs j1
			    where ( 
			    		(j1.job_end_date
							BETWEEN DATE_SUB( j.job_start_date, INTERVAL 239 MINUTE )
							AND DATE_ADD( j.job_end_date, INTERVAL 239 MINUTE )
						) 
						OR
						(j1.job_start_date
							BETWEEN DATE_SUB( j.job_start_date, INTERVAL 239 MINUTE )
							AND DATE_ADD( j.job_end_date, INTERVAL 239 MINUTE )
						)
					  )
				AND j1.job_id != j.job_id
				AND j1.job_id !=".$job_id."
				AND j1.job_status =  'confirmed'
				AND j.job_id =".$job_id."
				AND j1.sitter_user_id=".$sitter_id;	
		$job_time = $this->db->query($sql1);
		
		if($job_time->num_rows()>0)
		{
			return "You cannot book a job within a 4 hour time frame of a previously booked job.";
		}
		
		
		$sql = "SELECT j1.job_id, date_format( j1.job_start_date, '%Y-%m-%d' ) start_date
				FROM jobs j, jobs j1
				WHERE date_format( j.job_start_date, '%Y-%m-%d' ) = date_format( j1.job_start_date, '%Y-%m-%d' )
				AND j1.job_id != j.job_id
				AND j1.job_status =  'confirmed'
				AND j.job_id =".$job_id."
				AND j1.sitter_user_id=".$sitter_id;
		$no_of_job = $this->db->query($sql);
		
		if($no_of_job->num_rows()>=2)
		{
			return $this->lang->line('more_than_2_job');
		}
		
		$this->db->query("update jobs set job_status='confirmed', sitter_user_id=$sitter_id where job_id = $job_id"."  and (sitter_user_id is null or sitter_user_id='' or sitter_user_id = 0) and job_status='pending'" );
	
		if($this->db->affected_rows()>0)
		{
			$this->db->query("update job_sent set sent_status ='locked' where job_id=$job_id ");
			$this->db->query("update job_sent set sent_status ='accepted' where job_id=$job_id  and sent_to=$sitter_id ");
			$this->db->query("UPDATE sitters s SET available_jobs =available_jobs-1,confirmed_jobs=confirmed_jobs+1 where userid=$sitter_id");
			
			//to mark alert resolved in job log
			$this->db->query("update job_logs set is_resolved=1 where job_id=".$job_id." and modification='Sitter Cancellation'");
			
		 	$job_info = $this->jobDetails($job_id);
			//$job_detail_email = jobString($job_info);
			
			$param['type']= 'parent';
			$this->load->library('apn',$param);
			$msg=$this->lang->line('job_confirm_noti');
			$this->common_model->send_notification($job_id,$job_info['client_user_id'],$msg);
			
			jobMail($job_id,$job_info,'jobs_confirmed_to_client','sitter','mail');
			jobMail($job_id,$job_info,'jobs_confirmed_to_client','client','mail');
			
			return true;
		}
		else
		{
			return $this->lang->line('job_not_confirmed');
		}
	}
	
	
	function cancelConfirm($job_id)
	{
		$this->db->select('job_start_date');
		$this->db->where('job_id',$job_id);
		$this->db->where('job_status','confirmed');
		$res = $this->db->get('jobs');
		if($res->num_rows()>0)
		{	
			$res =$res->result();
			if((strtotime($res[0]->job_start_date)-time())>=172800)
			{
				$job_info = $this->jobDetails($job_id);
				//$job_detail_email = jobString($job_info);
				$this->db->query("update jobs set job_status='pending', sitter_user_id=null where job_id = $job_id");
				$this->db->query("update job_sent set sent_status ='new' where job_id=$job_id ");
				$this->db->query("UPDATE sitters SET confirmed_jobs=confirmed_jobs-1 where userid=".$job_info['sitter_user_id']);
			
				$data_log=array('job_id'=>$job_id,
						'modified_by'=>$job_info['sitter_user_id'],
						'modified_date'=>date('Y-m-d H:i:s'),
						'modification'=>'Sitter Cancellation',
						'initial' =>0,
						'modified'=>0
				);
				$this->db->insert('job_logs', $data_log);
				/* $param['type']= 'parent';
				$this->load->library('apn',$param);
				$msg=$this->lang->line('job_cancel_noti'); 
				$this->common_model->send_notification($job_id,$job_info['client_user_id'],$msg);*/
				
				//jobMail($job_id,$job_info,'confirmed_job_cancelled_to_client','client','mail');
				return true;
			}
			else
				return false;
		}
		else 
			return false;
	}
	
	
	function completeJob($job_id,$data,$child_id='')
	{
		if($data['sitter_user_id']>0)
		{
			$job_info = $this->clientJobDetails($job_id);
			if(strtotime($job_info['job_start_date']) < time())
			{
				if($child_id!='')
				{
					foreach($child_id as $ch){
					$query[] = "($job_id,$ch)";
					}
					$values = implode(',',$query);
					
					$sql="insert ignore into jobs_to_childs values $values";
					$res = $this->db->query($sql);
					
					$log_data = array('job_id'=>$job_id,
							'modified_by'=>$data['sitter_user_id'],
							'modified_date'=> date('Y-m-d H:i:s'),
							'modification'=>'Added Child',
							'initial'=>$job_info['child_count'],
							'modified'=>$job_info['child_count'] + count($child_id));
						
					$this->db->insert('job_logs',$log_data);
					
					$data['actual_child_count'] = $log_data['modified'];
					$rates = $this->common_model->getRate($data['actual_child_count']);
					$data['client_updated_rate']=$rates->client_rate;
					$data['client_rate']=$rates->client_rate;
					$data['sitter_rate_pre']=$rates->sitter_rate;
					$data['rate']=$rates->sitter_rate;
				}
				
				/* if(array_key_exists('actual_child_count', $data))
				{
					$log_data = array('job_id'=>$job_id,
							'modified_by'=>$data['sitter_user_id'],
							'modified_date'=> date('Y-m-d H:i:s'),
							'modification'=>'child_count',
							'initial'=>$job_info['child_count'],
							'modified'=>$data['actual_child_count']);
					
					$this->db->insert('job_logs',$log_data);
				} */
				
				$this->db->where('job_id',$job_id);
				$this->db->where('job_status','confirmed');
				$this->db->update('jobs',$data);
				if($this->db->affected_rows()>0)
				{
					$this->db->query('update sitters set earnings=(select sum(total_paid) from jobs where sitter_user_id="'.$data['sitter_user_id'].'" ) where userid="'.$data['sitter_user_id'].'"');
					$this->db->query("update job_sent set sent_status ='locked' where job_id=$job_id ");
					$this->db->query("UPDATE sitters s SET confirmed_jobs=confirmed_jobs-1,completed_jobs=completed_jobs+1 where userid=".$data['sitter_user_id']);
					
					if(isset($data['completed_date']))
					{
						$log_data = array('job_id'=>$job_id,
								'modified_by'=>$data['sitter_user_id'],
								'modified_date'=> date('Y-m-d H:i:s'),
								'modification'=>'Adjust Hours',
								'initial'=>date('Y-m-d H:i:s',strtotime($job_info['job_end_date'])),
								'modified'=>$data['completed_date']
								);
						
						$this->db->insert('job_logs',$log_data);
					}
					
					$job_info = $this->clientJobDetails($job_id);
					
					$param['type']= 'parent';
					$this->load->library('apn',$param);
					$msg=$this->lang->line('Job_complete');
					$this->common_model->send_notification($job_id,$job_info['client_user_id'],$msg);
					
					jobMail($job_id,$job_info,'job_completed_to_client','client','mail');
					return true;
				}
				else
				{
					return $this->lang->line('job_not_completed');
				}
			}
			else 
			{
				return $this->lang->line('job_not_started');;
			}
		}
		else 
			return $this->lang->line('job_not_completed');
	}
	
	public function setAvailabileJobs()
	{
		$this->db->query("UPDATE sitters s SET available_jobs =
				( SELECT count( job_id ) FROM job_sent WHERE sent_status = 'new' AND sent_to = s.userid )");
	}
	
	public function setConfirmedJobs()
	{
		$this->db->query("UPDATE sitters s SET confirmed_jobs =
		( SELECT count( job_id ) FROM jobs WHERE job_status = 'confirmed' AND sitter_user_id = s.userid )");
	}
	
	public function setCompletedJobs()
	{
		$this->db->query("UPDATE sitters s SET completed_jobs =
		( SELECT count( job_id ) FROM jobs WHERE job_status = 'completed' AND sitter_user_id = s.userid )");
	
		$this->db->query("UPDATE clients_detail c SET events_compeleted =
		( SELECT count( job_id ) FROM jobs WHERE job_status = 'completed' AND client_user_id = c.userid )");
	}
	
	
	function jobListNew($sitter_user_id,$jstatus,$perPage='',$offset='')
	{
		$ori_jstatus=$jstatus;
		if($jstatus != 'pending')
		{
			$tp_select_query='';
			if($ori_jstatus == 'completed'||$ori_jstatus == 'closed')
				$tp_select_query = ",total_paid,sitter_payment_status as payment_status";
				
			$select_sql = 'j.job_id,jobs_posted_date,job_start_date,job_end_date,completed_date,child_count,actual_child_count,client_user_id,j.sitter_user_id,job_status,round(ifnull( rate,sitter_rate_pre ),2 ) as rate,special_need,notes,address_id,
			u.username,u.firstname ,u.lastname ,u.phone ,p.local_phone,
			c.spouse_firstname,c.spouse_lastname,c.spouse_phone1,c.spouse_phone2,
			p.emergency_contact,p.emergency_phone,p.emergency_relation,job_start_date as actual_start_date,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ) actual_end_date'."$tp_select_query";
				
			$this->db->select($select_sql,false);
			$this->db->join('users u','u.userid = j.client_user_id','left');
			$this->db->join('clients_detail c','u.userid = c.userid');
			$this->db->join('user_profile p','u.userid = p.userid');
			//$this->db->join('job_master jm','jm.master_job_id = j.master_job_id');
			$this->db->where('sitter_user_id',$sitter_user_id);
			
			if($ori_jstatus == 'completed')
			{
				$where1= "(j.job_status='completed' OR ( j.job_status ='cancelled' AND immidiate_cancelled ='yes' ))";
				$this->db->where($where1);
				$this->db->order_by('job_start_date','DESC');
			}
			else if($ori_jstatus == 'closed')
			{
				$this->db->where('j.job_status','closed');
				$this->db->order_by('job_start_date','DESC');
			}
			else if($ori_jstatus == 'cancelled')
			{
				$this->db->where('j.job_status','cancelled');
				$this->db->where('j.immidiate_cancelled','no');
				$this->db->order_by('job_start_date','DESC');
			}
			else
			{
				if($ori_jstatus=='confirmed')
				{
					$where="((date_format('".date('Y-m-d H:i:s')."' , '%Y-%m-%d %H:%i' )  + INTERVAL 0 HOUR) not between (date_format( job_start_date, '%Y-%m-%d %H:%i' ) + INTERVAL -3 HOUR ) AND (date_format( job_end_date , '%Y-%m-%d %H:%i' )  + INTERVAL 3 HOUR))";
					$this->db->where($where);
				}
				else
				{
					$where="((date_format( '".date('Y-m-d H:i:s')."' , '%Y-%m-%d %H:%i' )  + INTERVAL 0 HOUR) between (date_format( job_start_date, '%Y-%m-%d %H:%i' ) + INTERVAL -3 HOUR ) AND (date_format( job_end_date , '%Y-%m-%d %H:%i' )  + INTERVAL 3 HOUR))";
					$this->db->where($where);
					$jstatus='confirmed';
				}
				
				$this->db->where('j.job_status',$jstatus);
				$this->db->order_by('job_start_date','ASC');
			}
			$result = $this->db->get('jobs j');
			$data[0] = 	$result->num_rows();
			
			if($perPage!='')
			{
				$this->db->select($select_sql,false);
				$this->db->join('users u','u.userid = j.client_user_id','left');
				$this->db->join('clients_detail c','u.userid = c.userid');
				$this->db->join('user_profile p','u.userid = p.userid');
				//$this->db->join('job_master jm','jm.master_job_id = j.master_job_id');
				$this->db->where('sitter_user_id',$sitter_user_id);
				if($ori_jstatus == 'completed')
				{
					$where1= "(j.job_status='completed' OR ( j.job_status = 'cancelled' AND j.immidiate_cancelled ='yes' ))";
					$this->db->where($where1);
					$this->db->order_by('job_start_date','DESC');
				}
				else if($ori_jstatus == 'closed')
				{
					$this->db->where('j.job_status','closed');
					$this->db->order_by('job_start_date','DESC');
				}
				else if($ori_jstatus == 'cancelled')
				{
					$this->db->where('j.job_status','cancelled');
					$this->db->where('j.immidiate_cancelled','no');
					$this->db->order_by('job_start_date','DESC');
				}
				else
				{
					if($ori_jstatus=='confirmed')
					{
						$where="((date_format( NOW( ) , '%Y-%m-%d %H:%i' )  + INTERVAL 0 HOUR) not between (date_format( job_start_date, '%Y-%m-%d %H:%i' ) + INTERVAL -3 HOUR ) AND (date_format( job_end_date , '%Y-%m-%d %H:%i' )  + INTERVAL 3 HOUR))";
						$this->db->where($where);
					}
					else
					{
						$where="((date_format( NOW( ) , '%Y-%m-%d %H:%i' )  + INTERVAL 0 HOUR) between (date_format( job_start_date, '%Y-%m-%d %H:%i' ) + INTERVAL -3 HOUR ) AND (date_format( job_end_date , '%Y-%m-%d %H:%i' )  + INTERVAL 3 HOUR))";
						$this->db->where($where);
						$jstatus='confirmed';
					}
					
					$this->db->where('j.job_status',$jstatus);
					$this->db->order_by('job_start_date','ASC');
				}
				$result = $this->db->get_where('jobs j',array(),$perPage,$offset);
			}
			//print_r($this->db->last_query());
		}
		else
		{
			$select_sql = 'j.job_id,jobs_posted_date,job_start_date,job_end_date,completed_date,child_count,actual_child_count,client_user_id,j.sitter_user_id,job_status,round(ifnull( rate,sitter_rate_pre ),2 ) as rate,special_need,notes,address_id,
			u.username ,u.firstname ,u.lastname ,u.phone ,p.local_phone,
			c.spouse_firstname,c.spouse_lastname,c.spouse_phone1,c.spouse_phone2,
			p.emergency_contact,p.emergency_phone,p.emergency_relation,job_start_date as actual_start_date,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ) actual_end_date';
				
			$this->db->select($select_sql,false);
			$this->db->join('users u','u.userid = j.client_user_id');
			$this->db->join('clients_detail c','u.userid = c.userid');
			$this->db->join('user_profile p','u.userid = p.userid');
			$this->db->join('job_sent js','js.job_id = j.job_id');
			$this->db->where('j.job_start_date > now()');
			$this->db->where('js.sent_to',$sitter_user_id);
			$this->db->where('js.sent_status','new');
			$this->db->order_by('job_start_date','ASC');
			$this->db->where_in('j.job_status',array('pending'));
			$result = $this->db->get('jobs j');
			$data[0] = 	$result->num_rows();
				
			if($perPage!='')
			{
				$this->db->select($select_sql,false);
				$this->db->join('users u','u.userid = j.client_user_id');
				$this->db->join('clients_detail c','u.userid = c.userid');
				$this->db->join('user_profile p','u.userid = p.userid');
				$this->db->join('job_sent js','js.job_id = j.job_id');
				$this->db->where('j.job_start_date > now()');
				$this->db->where('js.sent_to',$sitter_user_id);
				$this->db->where('js.sent_status','new');
				$this->db->order_by('job_start_date','ASC');
				$this->db->where_in('j.job_status',array('pending'));
				$result = $this->db->get_where('jobs j',array(),$perPage,$offset);
			}
		}
	
		$res = $result->result_array();
	
		if($result->num_rows()>0)
		{
			$i=0;
			foreach($res as $row)
			{
				$check[] = array();
				$check = replace_null($row);//print_r($row);
				if($ori_jstatus=='active')
				{
					$check['job_status'] = 'active';
				}
				else
				{
					$check['firstname'] = "";
					$check['username'] = "";
					$check['lastname'] = "";
					$check['phone'] = "";
					$check['local_phone'] = "";
					if($res[$i]['job_status']=='confirmed')
					{
						$check['job_status'] = 'inactive';
					}
				}
	
				$check['parent_info']=array('username'=>$check['username'],
						'phone' => $check['phone'],
						'firstname'=>$check['firstname'],
						'lastname'=>$check['lastname'],
						'local_phone'=>$check['local_phone']
				);
	
				unset($check['username']);
				unset($check['firstname']);
				unset($check['lastname']);
				unset($check['phone']);
				unset($check['local_phone']);
	
				$check['total_hours'] = round((strtotime(date('Y-m-d H:i:s',strtotime($res[$i]['actual_end_date'])))-strtotime(date('y-m-d H:i:s',strtotime($res[$i]['actual_start_date']))))/(60*60),2);
				
				$check['total_paid'] = $check['total_hours']*$check['rate'];
				
				$start_date = new DateTime($res[$i]['actual_end_date']);
				$end_date = new DateTime($res[$i]['actual_start_date']);
				$interval = $start_date->diff($end_date);
				$total_time   = $interval->format('%H:%i');
				$check['total_hours'] = $total_time;
				
				if(strtotime($res[$i]['job_start_date'])<time())
				{
					$check['is_expired'] = "Yes";
				}
				else
				{
					$check['is_expired'] = "No";
				}
	
				$check['job_status']=ucwords(strtolower($check['job_status']));
				$check['jobs_posted_date'] = dateFormat($row['jobs_posted_date']);
				$check['job_start_date'] = dateFormat($row['job_start_date']);
				$check['job_end_date'] = dateFormat($row['job_end_date']);
				$check['actual_start_date'] = dateFormat($row['actual_start_date']);
				$check['actual_end_date'] = dateFormat($row['actual_end_date']);
				$check['children'] = $this->getChildrenDetails($row['job_id']);
				$check['address'] = $this->common_model->getJobAddress($row['address_id']);
	
				$r[$i]=(object)$check;
				$i++;
			}
				
			if($jstatus=='completed' ||$jstatus=='closed')
			{
				$where_status="((job_status in  ('completed','closed')) or (job_status='cancelled' and immidiate_cancelled = 'yes'))";
				
				//total owed
				//$this->db->select('SUM((TIMESTAMPDIFF(MINUTE, job_start_date ,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ))/60)*ifnull( rate,sitter_rate_pre )) total_owed');
				$this->db->select("sum(if(job_status = 'cancelled' and  immidiate_cancelled = 'Yes' and job_start_date > cancelled_date , 3, ( ((TIMESTAMPDIFF(MINUTE, job_start_date ,if( completed_date=0 OR completed_date is null or completed_date < job_start_date   ,job_end_date, completed_date ))/60) ) ))*ifnull( rate,sitter_rate_pre )) total_owed",false);
				
				/* 
				 * for immidiate cancelled job
				 * $this->db->where_in('job_status',array('completed','closed')); 
				 */
				$this->db->where($where_status);
				
				$this->db->where('sitter_user_id',$sitter_user_id);
				$this->db->where('sitter_payment_status','unpaid');
				$total_own = $this->db->get('jobs');
				if($total_own->num_rows()>0)
				{
					$total_own = $total_own->row();
					if($total_own->total_owed !=null)
						$data[2] =round($total_own->total_owed,2);
				}
	
				//total paid
				//job_start_date as actual_start_date,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ) actual_end_date
				//$this->db->select('SUM((TIMESTAMPDIFF(MINUTE,job_start_date ,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ) )/60)*ifnull( rate,sitter_rate_pre )) total_paid');
				$this->db->select("sum(if(   job_status = 'cancelled' and  immidiate_cancelled = 'Yes' and job_start_date > cancelled_date , 3, ( ((TIMESTAMPDIFF(MINUTE, job_start_date ,if( completed_date=0 OR completed_date is null or completed_date < job_start_date   ,job_end_date, completed_date ))/60) ) ))*ifnull( rate,sitter_rate_pre )) total_paid",false);
				$this->db->join('job_master m','m.master_job_id=jobs.master_job_id');
				//$this->db->where('job_status','completed');
				
				/*
				$this->db->where_in('job_status',array('completed','closed'));
				*/
				$this->db->where($where_status);
				
				$this->db->where('sitter_user_id',$sitter_user_id);
				$this->db->where('sitter_payment_status','paid');
				//$this->db->where('YEAR(completed_date) != 0');
				//$this->db->where('YEAR(completed_date) = YEAR(now())');
				$total_paid = $this->db->get('jobs');
				if($total_paid->num_rows()>0)
				{
					$total_paid = $total_paid->row();
					if($total_paid->total_paid !=null)
						$data[3] =round($total_paid->total_paid,2);
				}
	
				//total earned
				//$this->db->select('SUM((TIMESTAMPDIFF(MINUTE,job_start_date,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date )  )/60)*ifnull( rate,sitter_rate_pre )) total_earned'); 
				$this->db->select("sum(if(   job_status = 'cancelled' and  immidiate_cancelled = 'Yes' and job_start_date > cancelled_date , 3, ( ((TIMESTAMPDIFF(MINUTE, job_start_date ,if( completed_date=0 OR completed_date is null or completed_date < job_start_date   ,job_end_date, completed_date ))/60) ) ))*ifnull( rate,sitter_rate_pre )) total_earned",false); //fix for sitter earnings
								
				$this->db->join('job_master m','m.master_job_id=jobs.master_job_id');
				//$this->db->where('job_status','completed');
				
				/*
				$this->db->where_in('job_status',array('completed','closed'));
				*/
				$this->db->where($where_status);
				
				$this->db->where('sitter_user_id',$sitter_user_id);
				//$this->db->where('sitter_payment_status','paid');
				//$this->db->where('YEAR(completed_date) != 0');
				//$this->db->where('YEAR(completed_date) = YEAR(now())');
				$total_paid = $this->db->get('jobs');
				if($total_paid->num_rows()>0)
				{
					$total_paid = $total_paid->row();
					if($total_paid->total_earned !=null)
						$data[4] =round($total_paid->total_earned,2);
				}
			}
				
			$data[1] = $r;
		}
	
		return $data;
	}
	
}