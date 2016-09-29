<?php 
/*
 * Filename			:	user_model
* Classname			:	User_model
* Description		:	Used to write Mysql query for get data from the database for web services for user details
* controller		:	User
*
* ---------------------------------------------------------------------------------------------------------------------------------------------
*/

class User_model extends CI_Model
{

	public function __construct()
	{
		parent:: __construct();
		$this->load->model('common_model');
	}
	
	
	function loginCheck($username,$userPassword,$devicetoken)
	{
		$select= "users.userid,users.firstname,users.lastname,users.status,users.username,users.dob,users.phone,users.profile_completed,users.miscinfo,users.notify,
				sitters.about_me,sitters.traits,sitters.exp_summary,sitters.available_jobs,sitters.confirmed_jobs,sitters.completed_jobs,
				sitters.earnings,sitters.cpr_holder,sitters.cpr_date, sitters.cpr_adult,
				sitters.cpr_adult_date,first_aid_cert,first_aid_cert_date,water_certification,
				water_cert_date,hampton_babysitter_training,babysitter_training_date,special_need_exp as otherpreference,
				main_image,thumb_image,app_thumb,orginal_image,local_phone,work_phone";
		$this->db->select($select);
		$this->db->join('user_profile','user_profile.userid = users.userid','left');
		$this->db->join('sitters','sitters.userid = users.userid','left');
		$this->db->where('username ', $username);
		$this->db->where('password',$userPassword);
		$this->db->where('usertype','S');
		$this->db->where('status !=', 'deleted');
		$result = $this->db->get('users');
		$data = $result->result_array();
		if($result->num_rows() > 0)
		{
			$check = array();
			$check = replace_null($data[0]);
			$data  = (object)$check;
			
			$data->preferences =$this->common_model->selected_prefer('sitter',$data->userid);
			
			$data->certification=array(
					'0'=>array(
							'name'=>'CPR Certification-Infant/Toddler',
							'key1' => 'cpr_holder',
							'key2' => 'cpr_date',
							'is_selected'=>$data->cpr_holder,
							'date'=>($data->cpr_date!= null)?date('M d Y', strtotime($data->cpr_date)):'',
							
					),
						
					'1'=>array(
							'name'=>'CPR Certification Adult',
							'key1' => 'cpr_adult',
							'key2' => 'cpr_adult_date',
							'is_selected'=>$data->cpr_adult,
							'date'=>($data->cpr_adult_date!= null)?date('M d Y', strtotime($data->cpr_adult_date)):''
					),
						
					'2'=> array(
							'name'=>'First-Aid Certification',
							'key1' => 'first_aid_cert',
							'key2' => 'first_aid_cert_date',
							'is_selected'=>$data->first_aid_cert,
							'date'=>($data->first_aid_cert_date!= null)?date('M d Y', strtotime($data->first_aid_cert_date)):''
					),
					'3'=>array(
							'name'=>'Water Certification',
							'key1' => 'water_certification',
							'key2' => 'water_cert_date',
							'is_selected'=>$data->water_certification,
							'date'=>($data->water_cert_date!= null)?date('M d Y', strtotime($data->water_cert_date)):''
							),
					
					/* '4'=>array(
							'name'=>'Infant Training',
							'key1' => 'infant_training',
							'key2' => 'infant_training_date',
							'is_selected'=>$data->infant_training,
							'date'=>($data->infant_training_date!= null)?date('M d Y', strtotime($data->infant_training_date)):''
							), */
					'4'=>array(
							'name'=>'Hamptons Babysitters Training',
							'key1' => 'hampton_babysitter_training',
							'key2' => 'babysitter_training_date',
							'is_selected'=>$data->hampton_babysitter_training,
							'date'=>($data->babysitter_training_date!= null)?date('M d Y', strtotime($data->babysitter_training_date)):''
							),
					/* '5'=>array(
							'name'=>'Have car',
							'key1' => 'have_car',
							'key2' => '',
							'is_selected'=>" ",
							'date'=>'NA'
							),
					'6'=>array(
							'name'=>'Have child',
							'key1' => 'have_child',
							'key2' => '',
							'is_selected'=>" ",
							'date'=>'NA'
							),
					'8'=>array(
							'name'=>'Criminal Record',
							'key1' => 'criminal_record',
							'key2' => '',
							'is_selected'=>$data->criminal_record,
							'date'=>'NA'
							),  
					'5'=>array(
							'name'=>'Clean Driving record',
							'key1' => 'clean_drive_record',
							'key2' => '',
							'is_selected'=>$data->clean_drive_record,
							'date'=>'NA'
							)*/);
			
			unset(
					$data->babysitter_training_date,
					$data->hampton_babysitter_training,$data->cpr_holder,$data->cpr_date,
					$data->cpr_adult,$data->cpr_adult_date,$data->first_aid_cert,$data->first_aid_cert_date,
					$data->water_certification,$data->water_cert_date);
			
			if($data->main_image != '' && file_exists($this->config->item('image_path').$data->main_image))
				$data->main_image = $this->config->item('image_url').$data->main_image;
			else
				$data->main_image = $this->config->item('image_url')."uploads/noimage.jpg";
			
			if($data->app_thumb != '' && file_exists($this->config->item('image_path').$data->app_thumb))
				$data->thumb_image = $this->config->item('image_url').$data->app_thumb;
			else
				$data->thumb_image = $this->config->item('image_url')."uploads/noimage.jpg";
			
			if($data->orginal_image != '' && file_exists($this->config->item('image_path').$data->orginal_image))
				$data->orginal_image = $this->config->item('image_url').$data->orginal_image;
			else
				$data->orginal_image = $this->config->item('image_url')."uploads/noimage.jpg";
			
			$data->dob = date('M d Y', strtotime($data->dob));
			$data->timezone = getTimeZoneAbbreviation();
			return $data;
		}
		else
			return false;
	}
	

	function parentProfile($user_id)
	{
		$this->db->select('users.userid,firstname,middlename,lastname,dob,usertype,current_city,user_profile.*');
		$this->db->where('users.userid',$user_id);
		$this->db->join('user_profile','user_profile.userid = users.userid');
		$this->db->where('users.userid',$user_id);
		$this->db->where('users.usertype','P');
		//$this->db->where('users.status','active');
		$this->db->where_in('users.status', array(' ','active'));
		$query = $this->db->get('users');
		$result = $query->result_array();
		
		if($query->num_rows>0)
		{
			$check = array();
			$check = replace_null($result[0]);
			$result[0]  = (object)$check;
				
			$birthDate = $result[0]->dob;
			$birthDate = explode("-", $birthDate);
			$result[0]->age = (string)(date("md", date("U", mktime(0, 0, 0, $birthDate[1],$birthDate[2] , $birthDate[0]))) > date("md") ? ((date("Y")-$birthDate[0])-1):(date("Y")-$birthDate[0]));
			return $result;
		}
		
	}

	

	function update_user_detail($user_id,$val)
	{
			
		$this->db->where('userid',$user_id);
		$this->db->update('users',$val);

		$affected_count = $this->db->affected_rows();
		if($affected_count>0)
		{
			return true;
		}

	}

	function update_user_detail1($user_id,$udata,$updata,$pdata,$sdata)
	{
		$this->db->where('userid',$user_id);
		//$this->db->where('users.status','active');
		//$this->db->where_in('users.status', array(' ','active'));
		$this->db->where('users.status !=','deleted');
		$this->db->update('users',$udata);

		$this->db->select('userid');
		$this->db->where('userid',$user_id);
		$pr = $this->db->get('user_profile');
		
		if($pr->num_rows()>0)
		{
			$this->db->where('userid',$user_id);
			$this->db->update('user_profile',$updata);
		}
		else 
		{
			$updata['userid']=$user_id;
			$this->db->insert('user_profile',$updata);
			
			$profile_status=array('profile_completed'=>1);
			$this->db->where('userid',$user_id);
			$this->db->update('users',$profile_status);
		}
		
		$this->db->where('userid',$user_id);
		$this->db->update('sitters',$sdata);
		
		if($pdata!='')
		{
			$preferences = explode(',', $pdata);
			
			$this->db->query("Delete from sitter_preference where sitter_user_id=$user_id ");
				
			foreach($preferences as $row)
			{
				$pre_data['sitter_user_id'] = $user_id;
				$pre_data['prefer_id'] = $row;
				$this->db->insert('sitter_preference',$pre_data);
			}
			
		}
		else
		{
			$this->db->query("Delete from sitter_preference where sitter_user_id=$user_id ");
		}
		
		/*----- Get user detail after update ----*/
		$select= "users.userid,users.firstname,users.lastname,users.status,users.username,users.profile_completed,users.dob,users.phone,users.miscinfo,users.notify,
				sitters.about_me,sitters.traits,sitters.exp_summary,sitters.available_jobs,sitters.confirmed_jobs,sitters.completed_jobs,
				sitters.earnings,sitters.have_car,sitters.cpr_holder,sitters.cpr_date, sitters.cpr_adult,
				sitters.cpr_adult_date,first_aid_cert,first_aid_cert_date,water_certification,
				water_cert_date,hampton_babysitter_training,babysitter_training_date, special_need_exp as otherpreference,
				main_image,thumb_image,app_thumb,orginal_image,local_phone,work_phone";
		$this->db->select($select);
		$this->db->join('user_profile','user_profile.userid = users.userid');
		$this->db->join('sitters','sitters.userid = users.userid');
		$this->db->where('users.userid ', $user_id);
		//$this->db->where('status', 'active');
		$this->db->where('status !=', 'deleted');
		$result = $this->db->get('users');
		
		if($result->num_rows>0)
		{
			$data = $result->result_array();
			$check = array();
			$check = replace_null($data[0]);
			$data  = (object)$check;
			
			$data->preferences =$this->common_model->selected_prefer('sitter',$data->userid);
			
			$data->certification=array(
					'0'=>array(
							'name'=>'CPR Certification-Infant/Toddler',
							'key1' => 'cpr_holder',
							'key2' => 'cpr_date',
							'is_selected'=>$data->cpr_holder,
							'date'=>($data->cpr_date!= NULL)?date('M d Y', strtotime($data->cpr_date)):'',
							
					),
						
					'1'=>array(
							'name'=>'CPR Certification Adult',
							'key1' => 'cpr_adult',
							'key2' => 'cpr_adult_date',
							'is_selected'=>$data->cpr_adult,
							'date'=>($data->cpr_adult_date!= NULL)?date('M d Y', strtotime($data->cpr_adult_date)):''
					),
						
					'2'=> array(
							'name'=>'First-Aid Certification',
							'key1' => 'first_aid_cert',
							'key2' => 'first_aid_cert_date',
							'is_selected'=>$data->first_aid_cert,
							'date'=>($data->first_aid_cert_date!= NULL)?date('M d Y', strtotime($data->first_aid_cert_date)):''
					),
					'3'=>array(
							'name'=>'Water Certification',
							'key1' => 'water_certification',
							'key2' => 'water_cert_date',
							'is_selected'=>$data->water_certification,
							'date'=>($data->water_cert_date!= null)?date('M d Y', strtotime($data->water_cert_date)):''
							),
					
					/* '4'=>array(
							'name'=>'Infant Training',
							'key1' => 'infant_training',
							'key2' => 'infant_training_date',
							'is_selected'=>$data->infant_training,
							'date'=>($data->infant_training_date!= null)?date('M d Y', strtotime($data->infant_training_date)):''
							), */
					'4'=>array(
							'name'=>'Hamptons Babysitters Training',
							'key1' => 'hampton_babysitter_training',
							'key2' => 'babysitter_training_date',
							'is_selected'=>$data->hampton_babysitter_training,
							'date'=>($data->babysitter_training_date!= null)?date('M d Y', strtotime($data->babysitter_training_date)):''
							),
					/*'5'=>array(
							'name'=>'Have car',
							'key1' => 'have_car',
							'key2' => '',
							'is_selected'=>$data->have_car,
							'date'=>'NA'
							),
					'6'=>array(
							'name'=>'Have child',
							'key1' => 'have_child',
							'key2' => '',
							'is_selected'=>$data->have_child,
							'date'=>'NA'
							),
					'8'=>array(
							'name'=>'Criminal Record',
							'key1' => 'criminal_record',
							'key2' => '',
							'is_selected'=>$data->criminal_record,
							'date'=>'NA'
							),  
					'5'=>array(
							'name'=>'Clean Driving record',
							'key1' => 'clean_drive_record',
							'key2' => '',
							'is_selected'=>$data->clean_drive_record,
							'date'=>'NA'
							)*/);
			
			unset(	
					$data->babysitter_training_date,
					$data->hampton_babysitter_training,$data->cpr_holder,$data->cpr_date,
					$data->cpr_adult,$data->cpr_adult_date,$data->first_aid_cert,$data->first_aid_cert_date,
					$data->water_certification,$data->water_cert_date);
			
			if($data->main_image != null && file_exists($this->config->item('image_path').$data->main_image))
				$data->main_image = $this->config->item('image_url').$data->main_image;
			else
				$data->main_image = $this->config->item('image_url')."uploads/noimage.jpg";
			
			if($data->app_thumb != null && file_exists($this->config->item('image_path').$data->app_thumb))
				$data->thumb_image = $this->config->item('image_url').$data->app_thumb;
			else
				$data->thumb_image = $this->config->item('image_url')."uploads/noimage.jpg";
			
			if($data->orginal_image != null && file_exists($this->config->item('image_path').$data->orginal_image))
				$data->orginal_image = $this->config->item('image_url').$data->orginal_image;
			else
				$data->orginal_image = $this->config->item('image_url')."uploads/noimage.jpg";
			
			$data->dob = date('M d Y', strtotime($data->dob));
			$data->timezone = getTimeZoneAbbreviation();
			return $data;
		}
		else
			return false;
	}


	function update_user_password($user_id,$cPassword,$val)
	{
		$this->db->where('userid',$user_id);
		$this->db->where('password',md5($cPassword));
		$this->db->select('password');
		$pawd = $this->db->get('users');
	
		if($pawd->num_rows() > 0)
		{
			$this->db->where('userid',$user_id);
			$this->db->update('users',$val);

			/*----- Get user detail after update ----*/
			$this->db->select('*');
			$this->db->where('userid', $user_id);
			$result = $this->db->get('users');
			$data = $result->row();
			return $data;
		}
		else
		{
			return false;
		}
	}
	
	function earnings($sitter_id,$job = false,$year='')
	{
		$data = array();
		
		$this->db->select('job_id,total_paid');
		$this->db->where('completed_date < now()');
		$this->db->where('total_paid != 0');
		$this->db->where('sitter_user_id', $sitter_id);
		//$this->db->where('job_id',$job_id);
		$result_job_earning = $this->db->get('jobs');
		if($result_job_earning->num_rows()>0)
			$data['earnings_by_job'] = $result_job_earning->result();
			
		$this->db->select('SUM(total_paid) total_paid , YEAR(completed_date) year');
		$this->db->where('sitter_user_id', $sitter_id);
		$this->db->where('YEAR(completed_date) != 0');
		$this->db->group_by("YEAR(completed_date)");
		$result_year_earning = $this->db->get('jobs');
		if($result_year_earning->num_rows()>0)
			$data['earnings_by_year'] = $result_year_earning->result();
		
		$this->db->select("SUM(total_paid) total_paid, SUM(TIMESTAMPDIFF(HOUR,actual_start_date,actual_end_date )) total_hours");
		//$this->db->select("total_paid, job_id,TIMESTAMPDIFF(HOUR,actual_start_date,actual_end_date ) total_hours");
		$this->db->join('job_master jm','jm.master_job_id = j.master_job_id');
		$this->db->where('sitter_user_id', $sitter_id);
		$result_hour_earning = $this->db->get('jobs j');
		if($result_hour_earning->num_rows()>0)
			$data['earnings_by_hours'] = $result_hour_earning->result();
		
		return $data;
	
	}
	
	
	//funcion for sitter status update.
	function update_user_status($user_id,$status)
	{
		$this->db->where('userid',$user_id);
		$this->db->select('userid,username,firstname,middlename,lastname,status,profile_completed,notify');
		$res = $this->db->get('users');
	
		if($res->num_rows() > 0)
		{
			$data = $res->row();
			$check = replace_null($data);
			$data  = (object)$check;
			
			if($data->status=='unapproved')
			{
				$r[0]=false;
				$r[1]= $this->lang->line('profile_not_approved');
				return $r;
			}
			
			if($data->profile_completed!=1)
			{
				$r[0]=false;
				$r[1]= 'Your profile is incomplete, Please complete your profile.';
				return $r;
			}
			
			if($data->status=='deleted')
			{
				$r[0]=false;
				$r[1]= "Your profile has been deleted.";
				return $r;
			}
			
			$data->status= $status;
			$val = array('status'=>$status);
			$this->db->where('userid',$user_id);
			$this->db->update('users',$val);
	
			if($this->db->affected_rows()>0)
			{
				$data->status= $status;
				if($status=='active')
				{
					$res = $this->db->query("select *,job_id,client_user_id as client_id from jobs where (job_status = 'pending' or job_status = 'new') and is_special = '0' and job_start_date > now() and job_id not in (select job_id from job_sent where sent_to=".$user_id.")");
					if($res->num_rows()>0)
					{
						$jobs = $res->result_array();
						$date=date('Y-m-d H:i:s');
						foreach($jobs as $job)
						{
							$values[] = "(".$job['job_id'].",".$user_id.",".$job['client_id'].",'".$date."','new')";
							$sql = "update jobs set total_assigned = ifnull(total_assigned,0)+1, job_status='pending' where job_id =". $job['job_id'];
							$res =$this->db->query($sql);
						}
					
						$values = implode(',', $values);
						$sql1 = "insert ignore into job_sent (`job_id`, `sent_to`, `sent_by`,`sent_date`, `sent_status`) values $values";
						$this->db->query($sql1);
					}
				}
			}
			$r[0]=true;
			$r[1]=$data;
			return $r;
		}
		else
		{
			$r[0]=false;
			$r[1]= "unable to update your status.";
			return $r;
		}
	}
	
}


