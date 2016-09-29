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
		$this->load->library('AuthorizeCimLib','','authorizeCimLib');
	}

	/**
	 * 
	 * @param string $username
	 * @param string $userPassword
	 * @param string $devicetoken
	 * @return StdClass|boolean
	 */
	function loginCheck($username,$userPassword,$devicetoken)
	{
		/*----- Get user detail after login ----*/
		$this->db->select('users.userid,users.firstname,users.lastname,users.username,users.profile_completed,users.status,users.dob,users.phone,users.miscinfo,users.notify
				,user_profile.main_image,user_profile.thumb_image,app_thumb,user_profile.orginal_image,user_profile.local_phone,user_profile.work_phone,
				user_profile.emergency_contact,user_profile.emergency_phone,user_profile.emergency_relation
				,clients_detail.*');
		$this->db->join('user_profile','user_profile.userid = users.userid','left');
		$this->db->join('clients_detail','clients_detail.userid = users.userid','left');
		$this->db->where('username ', $username);
		$this->db->where('password',$userPassword);
		//$this->db->where_in('status', array(' ','active'));
		$this->db->where('status !=', 'deleted');
		$this->db->where('usertype','P');
		$result = $this->db->get('users');
		$data = $result->result_array();
		
		if($result->num_rows() > 0)
		{
			$check = array();
			$check = replace_null($data[0]);
			$data  = (object)$check;
			
			$this->db->select('address_id,billing_name,address_1,streat_address,zipcode,city,s.name as state,state as state_id,address_type');
			$this->db->join('states s','s.zone_id = address.state');
			$this->db->where('address.userid ', $data->userid);
			$result = $this->db->get('address');
			$address = $result->result_array();
			$count_bill=0;
			if($result->num_rows()>0)
			{
				foreach($address as $row )
				{
					if($row['address_type'] =='local')
					{	
						$data->local_address =(object)replace_null($row);
					}
					else
					{
						if($row['address_type'] == 'billing')
						{
							$data->billing_address = (object)replace_null($row);
							$count_bill++;
						}
					}
				}
			}
			else
			{
				$data->local_address=array( 'address_id'=>'',
											'billing_name'=>'',
											'address_1'=>'',
											'streat_address'=>'',
											'city'=>'',
											'zipcode'=>'',
											'state'=>'',
											'state_id'=>'',
											'address_type'=>'',
											);
			}
			$this->db->select('child_id');
			$this->db->where('parent_user_id',$data->userid);
			$child_count=$this->db->get('children');
			$data->child_count=$child_count->num_rows();
			
			$this->db->select('authorizenet_payment_profile_id');
			$this->db->where('user_id',$data->userid);
			$pay_profile=$this->db->get('client_payment_profile');
			if($pay_profile->num_rows()>0)
			{
				$pay_profile=$pay_profile->result_array();
				$data->authorizenet_payment_profile_id = base64_encode($pay_profile[0]['authorizenet_payment_profile_id']);
			}
			else
			{
				$data->authorizenet_payment_profile_id ='';
			}
			
			$data->profile_completed = strval($data->profile_completed);
			if($count_bill==0)
				$data->billing_address = '';
			
			if($data->main_image != "" && file_exists($this->config->item('image_path').$data->main_image))
				$data->main_image = $this->config->item('image_url').$data->main_image;
			else
				$data->main_image = $this->config->item('image_url')."uploads/noimage.jpg";
			
			if($data->app_thumb != "" && file_exists($this->config->item('image_path').$data->app_thumb))
				$data->thumb_image = $this->config->item('image_url').$data->app_thumb;
			else
				$data->thumb_image = $this->config->item('image_url')."uploads/noimage.jpg";
			
			if($data->orginal_image != ""&& file_exists($this->config->item('image_path').$data->orginal_image))
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

	
	/**
	 * 
	 * @param string $user_id
	 * @return StdClass|null
	 */
	function sitterProfile($user_id)
	{
		$this->db->select('users.userid,users.firstname,users.lastname,users.username,users.dob,users.phone,users.miscinfo,users.notify,
				sitters.about_me,sitters.traits,sitters.exp_summary,sitters.available_jobs,sitters.confirmed_jobs,sitters.completed_jobs,
				sitters.earnings,sitters.have_car,sitters.cpr_holder,sitters.cpr_date, sitters.cpr_adult,
				sitters.cpr_adult_date,first_aid_cert,first_aid_cert_date,water_certification,
				water_cert_date,infant_training,infant_training_date,hampton_babysitter_training,babysitter_training_date,
				clean_drive_record,criminal_record,have_child,special_need_exp as otherpreference,
				main_image,thumb_image,app_thumb,orginal_image,local_phone,work_phone');
		$this->db->join('sitters','sitters.userid = users.userid');
		$this->db->join('user_profile','user_profile.userid = users.userid');
		$this->db->where('users.userid',$user_id);
		$this->db->where('users.usertype','S');
		//$this->db->where_in('users.status', array(' ','active'));
		$query = $this->db->get('users');
		$result = $query->result_array();
		
		if($query->num_rows>0)
		{
			$check = array();
			$check = replace_null($result[0]);
			$result  = (object)$check;
			
			$result->preferences =$this->common_model->selected_prefer('sitter',$result->userid);
			$result->certification=array(
					'0'=>array(
							'name'=>'CPR Certification-Infant/Toddler',
							'key1' => 'cpr_holder',
							'key2' => 'cpr_date',
							'is_selected'=>$result->cpr_holder,
							'date'=>($result->cpr_date!= null)?date('M d Y', strtotime($result->cpr_date)):'',
								
					),
			
					'1'=>array(
							'name'=>'CPR Certification Adult',
							'key1' => 'cpr_adult',
							'key2' => 'cpr_adult_date',
							'is_selected'=>$result->cpr_adult,
							'date'=>($result->cpr_adult_date!= null)?date('M d Y', strtotime($result->cpr_adult_date)):''
					),
			
					'2'=> array(
							'name'=>'First-Aid Certification',
							'key1' => 'first_aid_cert',
							'key2' => 'first_aid_cert_date',
							'is_selected'=>$result->first_aid_cert,
							'date'=>($result->first_aid_cert_date!= null)?date('M d Y', strtotime($result->first_aid_cert_date)):''
					),
					'3'=>array(
							'name'=>'Water Certification',
							'key1' => 'water_certification',
							'key2' => 'water_cert_date',
							'is_selected'=>$result->water_certification,
							'date'=>($result->water_cert_date!= null)?date('M d Y', strtotime($result->water_cert_date)):''
					),
						
					'4'=>array(
							'name'=>'Infant Training',
							'key1' => 'infant_training',
							'key2' => 'infant_training_date',
							'is_selected'=>$result->infant_training,
							'date'=>($result->infant_training_date!= null)?date('M d Y', strtotime($result->infant_training_date)):''
					),
					'5'=>array(
							'name'=>'Hamptons Babysitters Training',
							'key1' => 'hampton_babysitter_training',
							'key2' => 'babysitter_training_date',
							'is_selected'=>$result->hampton_babysitter_training,
							'date'=>($result->babysitter_training_date!= null)?date('M d Y', strtotime($result->babysitter_training_date)):''
					),
					'6'=>array(
							'name'=>'Have car',
							'key1' => 'have_car',
							'key2' => '',
							'is_selected'=>$result->have_car,
							'date'=>'NA'
					),
					'7'=>array(
							'name'=>'Have child',
							'key1' => 'have_child',
							'key2' => '',
							'is_selected'=>$result->have_child,
							'date'=>'NA'
					),
					'8'=>array(
							'name'=>'Criminal Record',
							'key1' => 'criminal_record',
							'key2' => '',
							'is_selected'=>$result->criminal_record,
							'date'=>'NA'
					),
					'9'=>array(
							'name'=>'Clean Driving record',
							'key1' => 'clean_drive_record',
							'key2' => '',
							'is_selected'=>$result->clean_drive_record,
							'date'=>'NA'
					));
				
			unset(	$result->have_car,$result->have_child,$result->criminal_record,
					$result->clean_drive_record,$result->infant_training,$result->babysitter_training_date,
					$result->infant_training_date,$result->hampton_babysitter_training,$result->cpr_holder,$result->cpr_date,
					$result->cpr_adult,$result->cpr_adult_date,$result->first_aid_cert,$result->first_aid_cert_date,
					$result->water_certification,$result->water_cert_date);
				
			$result->education="";
			$this->db->select('degree');
			$this->db->where('user_id',$user_id);
			$edu=$this->db->get('education');
			if($edu->num_rows()>0)
			{
				$edu=$edu->result();
				foreach($edu as $row)
				{
					if($row->degree != '')
						$result->education = $result->education.','.$row->degree;
				}
				$result->education = trim($result->education,',');
			}
			
			
			if($result->main_image != "" && file_exists($this->config->item('image_path').$result->main_image))
				$result->main_image = $this->config->item('image_url').$result->main_image;
			else
				$result->main_image = $this->config->item('image_url')."uploads/noimage.jpg";
			
			if($result->app_thumb != "" && file_exists($this->config->item('image_path').$result->app_thumb))
				$result->thumb_image = $this->config->item('image_url').$result->app_thumb;
			else
				$result->thumb_image = $this->config->item('image_url')."uploads/noimage.jpg";
			
			if($result->orginal_image != "" && file_exists($this->config->item('image_path').$result->orginal_image))
				$result->orginal_image = $this->config->item('image_url').$result->orginal_image;
			else
				$result->orginal_image = $this->config->item('image_url')."uploads/noimage.jpg";
						
			$birthDate = $result->dob;
			$birthDate = explode("-", $birthDate);
			$result->age = (string)(date("md", date("U", mktime(0, 0, 0, $birthDate[1],$birthDate[2] , $birthDate[0]))) > date("md") ? ((date("Y")-$birthDate[0])-1):(date("Y")-$birthDate[0]));
			
			return $result;
		}
	}

	
	
	/**
	 * 
	 * @param string $user_id
	 * @param array $val
	 * @return bool|null
	 */
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
	
	/**
	 * 
	 * @param array $udata
	 * @param array $pdata
	 * @param array $adata
	 * @param array $cddata
	 */
	function register($udata,$pdata,$adata,$cddata)
	{
		$this->db->insert('users',$udata);
		$user_id =$this->db->insert_id();
	
		$pdata['userid']=$user_id;
		$this->db->insert('user_profile',$pdata);
		$up_id=$this->db->insert_id();
	
		global $error_user_id	;
		$error_user_id = $user_id;
		
		$cddata['userid']=$user_id;
		$this->db->insert('clients_detail',$cddata);
		$cd_id=$this->db->insert_id();
	
		
		if($adata['local_address']!='')
		{
			$loc_address= json_decode($adata['local_address'],true);
			$array1=array('billing_name'=>@$loc_address['billing_name']
					,'address_1'=>@$loc_address['address_1']
					,'streat_address'=>@$loc_address['streat_address']
					,'zipcode'=>@$loc_address['zipcode']
					,'city'=>@$loc_address['city']
					,'state'=>@$loc_address['state']
					,'country'=>@$loc_address['country']
					,'address_type'=>@$loc_address['address_type']
					);

			$array1['userid']=$user_id;
			$query  = $this->db->insert('address',$array1);
			$lad_id=$this->db->insert_id();	
		}
		
		
		if($adata['billing_address']!='')
		{
			$bill_address= json_decode($adata['billing_address'],true);
			
			$array1=array('billing_name'=>@$bill_address['billing_name'],
					'address_1'=>@$bill_address['address_1'],
					'streat_address'=>@$bill_address['streat_address'],
					'zipcode'=>@$bill_address['zipcode'],
					'city'=>@$bill_address['city'],
					'state'=>@$bill_address['state'],
					'country'=>@$bill_address['country'],
					'address_type'=>@$bill_address['address_type']
			);
			
			$array1['userid']=$user_id;
			$this->db->insert('address',$array1);
		}
		
		/*----- Get user detail after insert ----*/
		$this->db->select('users.userid,users.firstname,users.lastname,users.profile_completed,users.status,users.username,users.dob,users.phone,users.miscinfo,users.notify
				,user_profile.main_image,user_profile.thumb_image,app_thumb,user_profile.orginal_image,user_profile.local_phone,user_profile.work_phone,
				user_profile.emergency_contact,user_profile.emergency_phone,user_profile.emergency_relation,
				clients_detail.*');
		$this->db->join('user_profile','user_profile.userid = users.userid');
		$this->db->join('clients_detail','clients_detail.userid = users.userid');
		$this->db->where('users.userid ', $user_id);
		$this->db->where_in('status', array(' ','active'));
		$result = $this->db->get('users');
		$data = $result->result_array();
		
		if($result->num_rows() > 0)
		{
			$check = array();
			$check = replace_null($data[0]);
			$data  = (object)$check;
	
			$this->db->select('address_id,billing_name,address_1,streat_address,city,zipcode,s.name as state,state as state_id,address_type');
			$this->db->join('states s','s.zone_id = address.state');
			$this->db->where('address.userid ', $user_id);
			$result = $this->db->get('address');
			$address = $result->result_array();
			$count_bill=0;
			foreach($address as $row )
			{
				if($row['address_type'] =='local')
				{
					$data->local_address = (object)replace_null($row);
				}
				else
				{
					if($row['address_type'] == 'billing')
					{
						$data->billing_address = (object)replace_null($row);
						$count_bill++;
					}
				}
			}
			if($count_bill==0)
				$data->billing_address = '';
			
			if($data->main_image != "" && file_exists($this->config->item('image_path').$data->main_image))
				$data->main_image = $this->config->item('image_url').$data->main_image;
			else
				$data->main_image = $this->config->item('image_url')."uploads/noimage.jpg";
			
			if($data->app_thumb != "" && file_exists($this->config->item('image_path').$data->app_thumb))
				$data->thumb_image = $this->config->item('image_url').$data->app_thumb;
			else
				$data->thumb_image = $this->config->item('image_url')."uploads/noimage.jpg";
			
			if($data->orginal_image != "" && file_exists($this->config->item('image_path').$data->orginal_image))
				$data->orginal_image = $this->config->item('image_url').$data->orginal_image;
			else
				$data->orginal_image = $this->config->item('image_url')."uploads/noimage.jpg";
			
			$data->dob = date('M d Y', strtotime($data->dob));
			$data->timezone = getTimeZoneAbbreviation();
			$data->authorizenet_payment_profile_id='';
			return $data;
		}
		else
			return false;
	}
	

	/**
	 * 
	 * @param string $user_id
	 * @param array $udata
	 * @param array $pdata
	 * @param array $adata
	 * @param array $cddata
	 * @return StdClass|null
	 */
	function update_user_detail1($user_id,$udata,$pdata,$adata,$cddata)
	{
		$this->db->select('username,authorizenet_profile_id');
		$this->db->join('client_payment_profile cp','cp.user_id=users.userid');
		$this->db->where('userid',$user_id);
		$this->db->where('users.status !=','deleted');
		$uname=$this->db->get('users');
		
		$this->db->where('userid',$user_id);
		$this->db->where('users.status !=','deleted');
		$this->db->update('users',$udata);
		
		if($uname->num_rows()>0)
		{ 
			$uname=$uname->row();
			if($uname->username!=$udata['username'])
			{
				// Create the and updated basic profile
				$this->authorizeCimLib->set_data('email',$udata['username'] );
				$this->authorizeCimLib->set_data('description', 'email updated for user id ' . $user_id);
				$this->authorizeCimLib->set_data('merchantCustomerId', $user_id);
				$this->authorizeCimLib->update_customer_profile($uname->authorizenet_profile_id);
			}
		}

		$this->db->select('userid');
		$this->db->where('userid',$user_id);
		$pr = $this->db->get('user_profile');
		
		if($pr->num_rows()>0)
		{
			$this->db->where('userid',$user_id);
			$this->db->update('user_profile',$pdata);
		}
		else
		{
			$pdata['userid']=$user_id;
			$this->db->insert('user_profile',$pdata);
			
			$profile_status=array('profile_completed'=>1,'status'=>'active');
			$this->db->where('userid',$user_id);
			$this->db->update('users',$profile_status);
		}
		
		$this->db->where('userid',$user_id);
		$this->db->update('clients_detail',$cddata);
		
		if($adata['local_address']!='')
		{
			$loc_address= json_decode($adata['local_address'],true);
			$array1=array(
					'address_id' =>@$loc_address['address_id']
					,'billing_name'=>@$loc_address['billing_name']
					,'address_1'=>@$loc_address['address_1']
					,'streat_address'=>@$loc_address['streat_address']
					,'zipcode'=>@$loc_address['zipcode']
					,'city'=>@$loc_address['city']
					,'state'=>@$loc_address['state']
					,'country'=>@$loc_address['country']
					,'address_type'=>@$loc_address['address_type']
					);
			
			if(is_numeric($array1['state']))
			{
				$this->db->where('userid',$user_id);
				if($array1['address_id']!= '' && $array1['address_id']!= null)
				{
					$this->db->where('address.address_id',$array1['address_id']);
					$this->db->update('address',$array1);
				}
				else
				{
					$array1['userid']=$user_id;
					$this->db->insert('address',$array1);
				}
			}
		}
		
	/* 	if($adata['billing_address']!='')
		{
			$bill_address= json_decode($adata['billing_address'],true);
			$array1=array('address_id'=>'','billing_name'=>"",'address_1'=>"",'streat_address'=>"",'zipcode'=>'','city'=>'','state'=>'','country'=>'','address_type'=>'');
			$bill_address = array_intersect_key($bill_address,$array1);
			$this->db->where('userid',$user_id);
			$this->db->where('address.address_id',$bill_address['address_id']);
			$this->db->update('address',$bill_address);
		}
 */
		/*----- Get user detail after update ----*/
		$this->db->select('users.userid,users.firstname,users.lastname,users.profile_completed,users.status,users.username,users.dob,users.phone,users.miscinfo,users.notify
				,user_profile.main_image,user_profile.thumb_image,user_profile.app_thumb,user_profile.orginal_image,user_profile.local_phone,user_profile.work_phone,
				user_profile.emergency_contact,user_profile.emergency_phone,user_profile.emergency_relation
				,clients_detail.*');
		$this->db->join('user_profile','user_profile.userid = users.userid','left');
		$this->db->join('clients_detail','clients_detail.userid = users.userid');
		$this->db->where('users.userid ', $user_id);
		$this->db->where('status !=','deleted');
		$result = $this->db->get('users');
		$data = $result->result_array();
		
		
		if($result->num_rows() > 0)
		{
			$check = array();
			$check = replace_null($data[0]);
			$data  = (object)$check;
			
			$this->db->select('address_id,billing_name,address_1,streat_address,city,zipcode,s.name as state,state as state_id,address_type');
			$this->db->join('states s','s.zone_id = address.state');
			$this->db->where('address.userid ', $user_id);
			$result = $this->db->get('address');
			$address = $result->result_array();
			$count_bill=0;
			foreach($address as $row )
			{
				if($row['address_type'] =='local')
				{
					$data->local_address = (object)replace_null($row);
				}
				else
				{
					if($row['address_type'] == 'billing')
					{
						$data->billing_address = (object)replace_null($row); 
						$count_bill++;
					}
				}
			}
			if($count_bill==0)
				$data->billing_address = '';
			
			$this->db->select('child_id');
			$this->db->where('parent_user_id',$data->userid);
			$child_count=$this->db->get('children');
			$data->child_count=$child_count->num_rows();
			
			$this->db->select('authorizenet_payment_profile_id');
			$this->db->where('user_id',$data->userid);
			$pay_profile=$this->db->get('client_payment_profile');
			if($pay_profile->num_rows()>0)
			{
				$pay_profile=$pay_profile->result_array();
				$data->authorizenet_payment_profile_id = base64_encode($pay_profile[0]['authorizenet_payment_profile_id']);
			}
			else
			{
				$data->authorizenet_payment_profile_id ='';
			}
			
			$data->profile_completed = strval($data->profile_completed);
			
			if($data->main_image != "" && file_exists($this->config->item('image_path').$data->main_image))
				$data->main_image = $this->config->item('image_url').$data->main_image;
			else
				$data->main_image = $this->config->item('image_url')."uploads/noimage.jpg";
			
			if($data->app_thumb != "" && file_exists($this->config->item('image_path').$data->app_thumb))
				$data->thumb_image = $this->config->item('image_url').$data->app_thumb;
			else
				$data->thumb_image = $this->config->item('image_url')."uploads/noimage.jpg";
			
			if($data->orginal_image != "" && file_exists($this->config->item('image_path').$data->orginal_image))
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

	
	/**
	 * 
	 * @param string $user_id
	 * @param array $val
	 * @return bool|StdClass
	 */
	function addEditKidold($user_id,$val)
	{
		if(isset($val['child_id']) && $val['child_id']!='')
		{
			$child_id = $val['child_id'];
			$this->db->where('child_id',$val['child_id']);
			$this->db->update('children',$val);
			$data[0] = $this->lang->line('child_updated');
		}
		else 
		{
			$this->db->insert('children',$val);
			$child_id = $this->db->insert_id();
			$data[0] = $this->lang->line('child_added');
		}
		
		$this->db->where('child_id',$child_id);
		$this->db->select('*');
		$query = $this->db->get('children');
		if($query->num_rows()>0)
		{
			$result = $query->result_array();

			$check = array();
			$check = replace_null($result[0]);
			$result  = (object)$check;
			
			$result->age =child_age($result->dob);
			
			if($result->main_image != null && file_exists($this->config->item('image_path').$result->main_image))
				$result->main_image = $this->config->item('image_url').$result->main_image;
			else
				$result->main_image = $this->config->item('image_url')."uploads/noimage.jpg";
			
			if($result->app_thumb != null && file_exists($this->config->item('image_path').$result->app_thumb))
				$result->thumb_image = $this->config->item('image_url').$result->app_thumb ;
			else
				$result->thumb_image = $this->config->item('image_url')."uploads/noimage.jpg";
			
			if($result->orginal_image != null && file_exists($this->config->item('image_path').$result->orginal_image))
				$result->orginal_image = $this->config->item('image_url').$result->orginal_image;
			else
				$result->orginal_image = $this->config->item('image_url')."uploads/noimage.jpg";
			
			
			$result->dob = date('M d Y', strtotime($result->dob));
			$data[1] = $result;
			return $data; 
		}
		else 
			return false;
	}
	
	
	/**
	 * 
	 * @param string $user_id
	 * @return StdClass|bool
	 */
	function getkids($user_id)
	{ 
		$this->db->select('*');
		$this->db->where('parent_user_id',$user_id);
		$this->db->where('is_deleted',0);
		$query = $this->db->get('children');
		if($query->num_rows()>0)
		{
			$data = $query->result_array();
			
			$i=0;
			foreach($data as $row)
			{
				$check[] = array();
				$check[$i] = replace_null($row);
				
				if($row['main_image'] != null && file_exists($this->config->item('image_path').$row['main_image']))
					$check[$i]['main_image'] = $this->config->item('image_url').$row['main_image'];
				else
					$check[$i]['main_image'] = $this->config->item('image_url')."uploads/noimage.jpg";
				
				if($row['app_thumb'] != null && file_exists($this->config->item('image_path').$row['app_thumb']))
					$check[$i]['thumb_image'] = $this->config->item('image_url').$row['app_thumb'];
				else
					$check[$i]['thumb_image'] = $this->config->item('image_url')."uploads/noimage.jpg";
				
				if($row['orginal_image'] != null && file_exists( $this->config->item('image_path').$row['orginal_image']))
					$check[$i]['orginal_image'] = $this->config->item('image_url').$row['orginal_image'];
				else
					$check[$i]['orginal_image'] = $this->config->item('image_url')."uploads/noimage.jpg";
				
				$check[$i]['age'] = child_age($check[$i]['dob']);	
				
				$check[$i]['dob'] = date('M d Y', strtotime($check[$i]['dob']));
				$i++;
			}
			$data = (object)$check;
			return $data;
		}
		else 
		{
			return false;
		}
	}
	
	
	/**
	 * 
	 * @param string $user_id
	 * @param string $kid_id
	 * @return boolean
	 */
	function deleteKid($user_id,$kid_id)
	{
		$this->db->select('child_id');
		$this->db->where('child_id',$kid_id);
		$this->db->where('is_deleted',0);
		$this->db->where('parent_user_id',$user_id);
		$query = $this->db->get('children');
		if($query->num_rows()>0)
		{
			$update= array('is_deleted'=>1);
			$this->db->where('child_id',$kid_id);
			$this->db->where('parent_user_id',$user_id);
			$this->db->update('children',$update);
			return true;
		}
		else
		{
			return false;
		}
	}
	
	
	/**
	 * 
	 * @param string $userId
	 * @return number|boolean
	 */
	function check_credits($userId)
	{
		$this->db->select('sum(slots) as credits');
		$this->db->where('end_date > now()');
		$this->db->where('userid',$userId);
		$res = $this->db->get('clients_subscription');
		$result = $res->result_array();
		if($res->num_rows()>0)
			return 	(int)$result[0]['credits'];
		else
			return false;
	}
	
	
	/**
	 * 
	 * @param array $child_profile
	 * @param string $user_id
	 * @return bool
	 */
	function addChild($child_profile,$user_id)
	{	
		$upload_sucess=true;
		$child_id='';
		foreach($child_profile as $ch)
		{
			$child_detail = array();
			
			$child_detail['child_name']				= @$ch['child_name'];
			$child_detail['dob']					= date('Y-m-d', strtotime(@$ch['child_dob']));
			$child_detail['sex']					= @$ch['sex'];
			$child_detail['child_relation']			= @$ch['child_relation'];
			$child_detail['allergy_status']			= @$ch['allergy_status'];
			$child_detail['allergies']				= @$ch['allergies'];
			$child_detail['medicator_status']		= @$ch['medicator_status'];
			$child_detail['medicator']				= @$ch['medicator'];
			$child_detail['special_needs_status']	= @$ch['special_needs_status'];
			$child_detail['special_needs']			= @$ch['special_needs'];
			$child_detail['notes']					= @$ch['notes'];
			$child_detail['fav_food']				= @$ch['fav_food'];
			$child_detail['fav_book']				= @$ch['fav_book'];
			$child_detail['fav_cartoon']			= @$ch['fav_cartoon'];
			$child_detail['parent_name']			= @$ch['parent_name'];
			$child_detail['parent_contact']			= @$ch['parent_contact'];
			
			$child_pic_data= "";
			$pic_ext='';
			if(array_key_exists('child_pic',$ch))
			{
				$child_pic_data = $ch['child_pic'];
				unset($ch['child_pic']);
			}
		
			$this->db->insert('children',$child_detail);
			$child = $this->db->insert_id();
			$child_id[] = $child;
			if($child_pic_data!='')
			{
				if($_FILES[$child_pic_data]['name']!='')
				{
					$date=date('Ymdhmis');
					$uploads_dir_file = $this->config->item('image_path').'uploads/children/orginal/'.$child.'__'.$_FILES[$child_pic_data]['name'];
					
					if(move_uploaded_file($_FILES[$child_pic_data]['tmp_name'], $uploads_dir_file))
					{   
						$thumb = $this->config->item('image_path').'uploads/children/thumb/'.$child.'__'.$_FILES[$child_pic_data]['name'];
						$full = $this->config->item('image_path').'uploads/children/full/'.$child.'__'.$_FILES[$child_pic_data]['name'];
						resizeImage($uploads_dir_file,$thumb,THUMB_SIZE_WIDTH,THUMB_SIZE_HIEGHT);
						resizeImage($uploads_dir_file,$full,LARGE_SIZE_WIDTH,LARGE_SIZE_HIEGHT);
						
						$folderName=$this->config->item('image_path').'uploads/children/app_thumb/';
						if(!file_exists($folderName))
							mkdir($folderName,0777,true);
						$appthumb = $this->config->item('image_path').'uploads/children/app_thumb/'.$child.'__'.$_FILES[$child_pic_data]['name'];
						resizeImage($uploads_dir_file,$appthumb,APPTHUMB_SIZE_WIDTH,APPTHUMB_SIZE_HIEGHT);
						
						$file_data=array();
						$file_data['child_id']=$child;
						$file_data['main_image']='uploads/children/full/'.$child.'__'.$_FILES[$child_pic_data]['name'];
						$file_data['thumb_image']='uploads/children/thumb/'.$child.'__'.$_FILES[$child_pic_data]['name'];
						$file_data['orginal_image']='uploads/children/orginal/'.$child.'__'.$_FILES[$child_pic_data]['name'];
						$file_data['app_thumb']='uploads/children/app_thumb/'.$child.'__'.$_FILES[$child_pic_data]['name'];
						
						save_child_image($file_data,$child);
						
					}
					else
					{
						$upload_sucess=false;
					}
				}
			}
		}
		$this->db->where_in('child_id',$child_id);
		$d['parent_user_id']=$user_id;
		$this->db->update('children',$d);
		return $upload_sucess;
	}
	
	
	/**
	 * get all job preferences 
	 * 
	 * @return array
	 */
	function getJobPreferenceList()
	{
		$this->db->select("prefer_id,pg.group_name,prefer_name");
		$this->db->join('preference_group pg','pg.group_id=pm.group_id');
		$this->db->where('pg.visible_to_client',1);
		$this->db->order_by('pm.group_id');
		$res = $this->db->get('preference_master pm');
		return $res->result_array();
	}
	
	/**
	 * updated for adding child from sitter App
	 * @param int $user_id
	 * @param array $val
	 * @return bool|StdClass
	 */
	function addEditKid($user_id,$val,$job_id=null)
	{
		if(isset($val['child_id']) && $val['child_id']!='')
		{
			$child_id = $val['child_id'];
			$this->db->where('child_id',$val['child_id']);
			$this->db->update('children',$val);
			$data[0] = $this->lang->line('child_updated');
		}
		else
		{
			$this->db->insert('children',$val);
			$child_id = $this->db->insert_id();
				
			if($job_id!=null && $val['parent_user_id']==0)
			{
				$this->load->model('sitter/job_model');
				$job_info = $this->job_model->clientJobDetails($job_id);
	
				$sql="insert ignore into jobs_to_childs values ($job_id,$child_id)";
				$res = $this->db->query($sql);
					
				$log_data = array('job_id'=>$job_id,
						'modified_by'=>$job_info['sitter_user_id'],
						'modified_date'=> date('Y-m-d H:i:s'),
						'modification'=>'Added Child',
						'initial'=>$job_info['child_count'],
						'modified'=>$job_info['child_count'] + count($child_id));
	
				$this->db->insert('job_logs',$log_data);
					
				$jobdata['actual_child_count'] = $log_data['modified'];
				$rates = $this->common_model->getRate($jobdata['actual_child_count']);
				$jobdata['client_updated_rate']=$rates->client_rate;
				$jobdata['client_rate']=$rates->client_rate;
				$jobdata['sitter_rate_pre']=$rates->sitter_rate;
				$jobdata['rate']=$rates->sitter_rate;
	
				$this->db->where('job_id',$job_id);
				$this->db->update('jobs',$jobdata);
			}
				
			$data[0] = $this->lang->line('child_added');
		}
	
		$this->db->where('child_id',$child_id);
		$this->db->select('*');
		$query = $this->db->get('children');
		if($query->num_rows()>0)
		{
			$result = $query->result_array();
	
			$check = array();
			$check = replace_null($result[0]);
			$result  = (object)$check;
				
			$result->age =child_age($result->dob);
				
			if($result->main_image != null && file_exists($this->config->item('image_path').$result->main_image))
				$result->main_image = $this->config->item('image_url').$result->main_image;
			else
				$result->main_image = $this->config->item('image_url')."uploads/noimage.jpg";
				
			if($result->app_thumb != null && file_exists($this->config->item('image_path').$result->app_thumb))
				$result->thumb_image = $this->config->item('image_url').$result->app_thumb ;
			else
				$result->thumb_image = $this->config->item('image_url')."uploads/noimage.jpg";
				
			if($result->orginal_image != null && file_exists($this->config->item('image_path').$result->orginal_image))
				$result->orginal_image = $this->config->item('image_url').$result->orginal_image;
			else
				$result->orginal_image = $this->config->item('image_url')."uploads/noimage.jpg";
				
				
			$result->dob = date('M d Y', strtotime($result->dob));
			$data[1] = $result;
			return $data;
		}
		else
			return false;
	}
}
