<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Filename				:	user
* Classname				:	User
* Description			:	Provide web services for user (Parent)
* Author				:	Anjali
* -------------------------------------------------------------------------------------------------------------------------------------------------
*/

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';

class User extends REST_Controller {

	// Global array to pass with each view
	var $data = array();
	var $api_time = array();

	function __construct()
	{
		parent::__construct();
		$this->load->model('parent/user_model');
	}

	
	/**
	 * 
	 * @param string $str
	 * @return boolean
	 */
	public function child_check($str)
	{
		$r= true;
		
		foreach($str as $row)
		{
			if(!array_key_exists("child_name",$row)||!isset($row['child_name']) || $row['child_name']===null ||trim($row['child_name'])=='')
			{
				$this->form_validation->set_message('child_check', 'Child name is missing');	
				$r= FALSE;
			} 
			if(!array_key_exists("child_dob",$row)||!isset($row['child_dob'])||$row['child_dob']=== null||trim($row['child_dob'])=='')
			{
				$this->form_validation->set_message('child_check', "Child's dob is missing");
				$r= FALSE;
			}
			
			if(!array_key_exists("special_needs_status",$row)||!isset($row['special_needs_status'])||$row['special_needs_status']=== null)
			{
				$this->form_validation->set_message('child_check', "Special need is missing");
				$r= FALSE;
			}
			else
			{
				if($row['special_needs_status']=='Yes')
				{
					if(!array_key_exists("special_needs",$row)||!isset($row['special_needs'])||$row['special_needs']=== null||trim($row['special_needs'])=== '')
					{
						$this->form_validation->set_message('child_check', "Special needs is missing");
						$r= FALSE;
					}
				}
			}
			
			if(!array_key_exists("allergy_status",$row)||!isset($row['allergy_status'])||$row['allergy_status']=== null)
			{
				$this->form_validation->set_message('child_check', "Allergy is missing");
				$r= FALSE;
			}
			else
			{
				if($row['allergy_status']=='Yes')
				{
					if(!array_key_exists("allergies",$row)||!isset($row['allergies'])||$row['allergies']=== null||trim($row['allergies'])=== '')
					{	
						$this->form_validation->set_message('child_check', "Allergies is missing");
						$r= FALSE;
					}
				}
			}
			
			if(!array_key_exists("medicator_status",$row)||!isset($row['medicator_status'])||$row['medicator_status']=== null)
			{
				$this->form_validation->set_message('child_check', "Medication is missing");
				$r= FALSE;
			}
			/*else 
			{
				if($row['medicator_status']=='Yes')
				{
					if(!array_key_exists("medicator",$row)||!isset($row['medicator'])||$row['medicator']=== null||trim($row['medicator'])=== '')
					{	
						$this->form_validation->set_message('child_check', "Medications is missing");
						$r= FALSE;
					}
				}
			}*/
				
			 if(!array_key_exists("sex",$row)||!isset($row['sex'])||$row['sex']=== null||$row['sex']=='')
			{
				$this->form_validation->set_message('child_check', "Child's Gender is required field");
				$r= FALSE;
			}	 
		}
		return $r;
	}
	
	/**
	 * 
	 * @param string firstname
	 * @param string lastname
	 * @param string phone
	 * @param string username
	 * @param string password
	 * @param string local_phone
	 * @param string work_phone
	 * @param string emergency_contact
	 * @param string emergency_relation
	 * @param string emergency_phone
	 * @param string local_address
	 * @param string billing_address
	 * @param string relationship
	 * @param string spouse_firstname
	 * @param string spouse_lastname
	 * @param string spouse_relation
	 * 
	 */
	function registration_post()
	{
		createFile('registration', print_r($_POST, true));
		log_message('debug','++ User +++ Parent Registration +++ : API call successfully.');
		$this->form_validation->set_rules(registration_rules());
		$child_profile = $this->input->post('child_profile');
		
		if($child_profile!='')
			$this->form_validation->set_rules('child_profile', 'child profile', 'callback_child_check');
		if($this->input->post('local_address')!='')
			$this->form_validation->set_rules('local_address', 'Local address', 'address_check');
		
		if($this->form_validation->run())
		{
			log_message('debug','++ User +++ Parent Registration +++ : No Form validation error');

			$d = $this->input->post('deviceToken');
			if(isset($d) && !empty($d))
				$deviceToken = $d;
			else
				$deviceToken = '';
			
			$device_id=$this->input->post('device_id');
			if(!isset($device_id) || empty($device_id))
				$device_id = '';
			
			$originalvalue = $this->input->post('dob');
			$dob = date('Y-m-d', strtotime($originalvalue));
			$udata = array(
					'firstname'				=> $this->input->post('firstname'),
					'lastname'				=> $this->input->post('lastname'),
					'dob'				    => $dob,
					'phone'			        => $this->input->post('phone'),
					'username'				=> $this->input->post('username'),
					'usertype'				=> 'P',
					'password'				=> $this->input->post('password'),
					'joining_date'			=> date('Y-m-d H:i:s'),
					'profile_completed'		=> 1,
					'modified_date'			=> date('Y-m-d H:i:s')
			);

			$pdata= array(
					'local_phone'			=> $this->input->post('local_phone'),
					'work_phone'			=> $this->input->post('work_phone'),
					'emergency_contact'		=> $this->input->post('emergency_contact'),
					'emergency_relation'	=> $this->input->post('emergency_relation'),
					'emergency_phone'		=> $this->input->post('emergency_phone')
			);

			$adata= array(
					'local_address'			=> $this->input->post('local_address'),
					'billing_address'		=> $this->input->post('billing_address')
			);

			$cd_data= array(
					'spouse_firstname'			=> $this->input->post('spouse_firstname'),
					'spouse_lastname'			=> $this->input->post('spouse_lastname'),
					'spouse_relation'			=> $this->input->post('spouse_relation'),
					'spouse_phone1'				=> $this->input->post('spouse_phone1'),
					'spouse_phone2'				=> $this->input->post('spouse_phone2'),
					'spouse_email'				=> $this->input->post('spouse_email'),
			);

			$regResult = $this->user_model->register($udata,$pdata,$adata,$cd_data);

			if($regResult)
			{
				$rand = rand(5,100);
				$randStr = $rand.md5(time());
				$generate_token = substr($randStr,1,$rand);
				
				$userId = $regResult->userid;
				$token = $this->common_model->addUserToken($userId, $deviceToken, $generate_token, $device_id);
				
				if($child_profile!='')
				{
					createFile('child_pic', print_r($_FILES, true));
					$pic_upload_sucess = $this->user_model->addChild($child_profile,$userId);
					if($pic_upload_sucess== false)
					{
						log_message('debug','++ User +++ Parent Registration +++ : All data inserted successfully except file');
						$regdata['user_detail'] = $regResult;
						$status = $this->lang->line('rest_status_success');
						$message = $this->lang->line('register_message').", but child image upload failed";
						$this->response(array("status"=>$status,'message'=>$message,"data"=>array("user_detail" => $regdata['user_detail'],"token_data"=>$token)), 202);
					}
				}	
				log_message('debug','++ User +++ Parent Registration +++ : All data inserted successfully');
				
				$this->db->select('child_id');
				$this->db->where('parent_user_id',$regResult->userid);
				$child_count=$this->db->get('children');
				$regResult->child_count=$child_count->num_rows();
				
				$regdata['user_detail'] = $regResult;
				clientMail($userId,'client_registration');
				
				$status = $this->lang->line('rest_status_success');
				$message = $this->lang->line('register_message');
				$this->response(array("status"=>$status,'message'=>$message,"data"=>array("user_detail" => $regdata['user_detail'],"token_data"=>$token)), 202);
			}
			else
			{
				log_message('debug','++ User +++ Parent Registration +++  : data not updated');
				$errors['error_registration'] = $this->lang->line('error_registration');
				$status = $this->lang->line('rest_status_failed');
				$this->response(array("errorCode"=>"FR","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
			}
		}
		else
		{
			log_message('debug','++ User +++ Parent Registration +++  : Got Form validation error');
			$errors = $this->form_validation->_error_array;
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"FVF","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}
	}
	
	function parentLogin_post()
	{
		createFile('parentLogin', print_r($_POST, true));
		log_message('debug','++ login +++ Parent Login +++ : API call successfully.');
		$username = $this->input->post('username');
		$userPassword = $this->input->post('password');
		$this->form_validation->set_rules(rule_login());
		if($this->form_validation->run())
		{
			log_message('debug','++ login +++ Parent Login +++ : No Form validation error');
			$d = $this->input->post('deviceToken');
			if(isset($d) && !empty($d))
				$deviceToken = $d;
			else
				$deviceToken = '';
			
			$device_id=$this->input->post('device_id');
			if(!isset($device_id) || empty($device_id))
				$device_id = '';
	
			$loginResult = $this->user_model->loginCheck($username,$userPassword,$deviceToken);
			if($loginResult)
			{
				$rand = rand(5,100);
				$randStr = $rand.md5(time());
				$generate_token = substr($randStr,1,$rand);
	
				$userId = $loginResult->userid;
				$token = $this->common_model->addUserToken($userId, $deviceToken, $generate_token, $device_id);
				$data['user_detail'] = $loginResult;
				$noti=$this->common_model->notification_count($userId);
				$status = $this->lang->line('rest_status_success');
				$message = $this->lang->line('login_success');
	
				$this->response(array("status"=>$status,'message'=>$message,"data"=>array('user_detail' => $data['user_detail'],"token_data"=>$token,'notification_count'=>$noti)), 202);
			}
			else
			{
				$errors['invalid_credentials'] =  $this->lang->line('invalid_credentials');
				$status = $this->lang->line('rest_status_failed');
				$this->response(array("errorCode"=>"FL2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
			}
		}
		else
		{
			log_message('debug','++ login +++ Parent Login +++ : Got Form validation error');
			$errors = $this->form_validation->_error_array;
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"FVF","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}
	}
	
	function sitterProfile_post()
	{
		createFile('sitterProfile', print_r($_POST, true));
		log_message('debug','++ user +++ Sitter Profile +++ : API called successfully');
		$this->form_validation->set_rules(view_sitter_rules());
	
		if ($this->form_validation->run() != FALSE)
		{
			log_message('debug','++ user +++ Sitter Profile +++ : No form error');
			$userId = $this->input->post('userid');
			$userToken = $this->input->post('token');
			$sitter_id = $this->input->post('sitter_id');
				
			$checkToken = $this->common_model->check_valid_user_token($userId,$userToken);	// check user token
			if($checkToken)
			{
				$profileData = $this->user_model->sitterProfile($sitter_id);
				if($profileData)
				{
					log_message('debug','++ user +++ Sitter Profile +++ : All data sent successfully');
					$status = $this->lang->line('rest_status_success');
					$message = $this->lang->line('record_found');
					$this->response(array("status"=>$status,'message'=>$message,"data"=>array("token_data"=>$userToken, "profileData"=>$profileData)), 202);
				}
				else
				{
					log_message('debug','++ user +++ Sitter Profile +++ : No record found');
					$errors['record_not_found'] = $this->lang->line('sitter_not_found');
					$status = $this->lang->line('rest_status_failed');
					$this->response(array("errorCode"=>"FL2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
				}
			}
			else
			{
				log_message('debug','++ user +++ Sitter Profile +++ : Token not matched.');
				$errors['invalid_token'] = $this->lang->line('invalid_token');
				$status = $this->lang->line('rest_status_failed');
				$this->response(array("errorCode"=>"ES2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
			}
		}
		else
		{
			log_message('debug','++ user +++ Sitter Profile +++ : Got form validation error.');
			$errors = $this->form_validation->_error_array;
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"FVF","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}
	}
	
	function edit_profile_post()
	{
		createFile('pedit_profile', print_r($_POST, true));
		log_message('debug','++ User +++ User Edit Profile +++ : API call successfully.');
		$this->form_validation->set_rules('userid','User id','required|trim');
		$this->form_validation->set_rules('token','Token','required|trim');
		$userId=$this->input->post('userid');
		$token = $this->input->post('token');
		
		$this->form_validation->set_rules(parentEditProfile_rules());
		if($this->input->post('local_address')!='')
			$this->form_validation->set_rules('local_address', 'Local address', 'address_check');
		
		if($this->form_validation->run())
		{
			$checkToken = $this->common_model->check_valid_user_token($userId,$token);	// check user token
			if($checkToken)
			{
				log_message('debug','++ User +++ User Edit Profile +++ : No Form validation error');
				
				$originalvalue = $this->input->post('dob');
				$dob = date('Y-m-d', strtotime($originalvalue));
				
				$udata = array(
						'firstname'				=> $this->input->post('firstname'),
						'lastname'				=> $this->input->post('lastname'),
						'dob'				    => $dob,
						'phone'			        => $this->input->post('phone'),
						'username'				=> $this->input->post('username'),
						'modified_date'			=> date('Y-m-d H:i:s'),
				);
				
				$pdata= array(
						'local_phone'			=> $this->input->post('local_phone'),
						'work_phone'			=> $this->input->post('work_phone'),
						'emergency_contact'		=> $this->input->post('emergency_contact'),
						'emergency_relation'	=> $this->input->post('emergency_relation'),
						'emergency_phone'		=> $this->input->post('emergency_phone')
				);
				
				$adata= array(
						'local_address'			=> $this->input->post('local_address'),
						'billing_address'		=> $this->input->post('billing_address')
				);
				
				$cd_data= array(
						'spouse_firstname'			=> $this->input->post('spouse_firstname'),
						'spouse_lastname'			=> $this->input->post('spouse_lastname'),
						'spouse_relation'			=> $this->input->post('spouse_relation'),
						'spouse_phone1'				=> $this->input->post('spouse_phone1'),
						'spouse_phone2'				=> $this->input->post('spouse_phone2'),
						'spouse_email'				=> $this->input->post('spouse_email'),
				);
				
				$regResult = $this->user_model->update_user_detail1($userId,$udata,$pdata,$adata,$cd_data);
				
				if($regResult)
				{
					if(!empty($_FILES['profile_pic']['name']))
					{
						createFile('profile_pic', print_r($_FILES['profile_pic'], true));
													
						$_FILES['profile_pic']['tmp_name']= $_FILES['profile_pic']['tmp_name'];
						$_FILES['profile_pic']['size']= $_FILES['profile_pic']['size'];
						$_FILES['profile_pic']['date_added'] = date('Y-m-d H:i:s');
					
						$uploads_dir_file = $this->config->item('image_path').'uploads/profile_images/orginal/'.$userId.'__'.$_FILES['profile_pic']['name'];
					
						if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $uploads_dir_file))
						{
							$thumb = $this->config->item('image_path').'uploads/profile_images/thumb/'.$userId.'__'.$_FILES['profile_pic']['name'];
							$full = $this->config->item('image_path').'uploads/profile_images/full/'.$userId.'__'.$_FILES['profile_pic']['name'];
							resizeImage($uploads_dir_file,$thumb,THUMB_SIZE_WIDTH,THUMB_SIZE_HIEGHT);
							resizeImage($uploads_dir_file,$full,LARGE_SIZE_WIDTH,LARGE_SIZE_HIEGHT);
							
							$folderName=$this->config->item('image_path').'uploads/profile_images/app_thumb/';
							if(!file_exists($folderName))
								mkdir($folderName,0777,true);
							$appthumb = $this->config->item('image_path').'uploads/profile_images/app_thumb/'.$userId.'__'.$_FILES['profile_pic']['name'];
							resizeImage($uploads_dir_file,$appthumb,APPTHUMB_SIZE_WIDTH,APPTHUMB_SIZE_HIEGHT);
							
							$file_data=array();
							$file_data['userid']=$userId;
							$file_data['main_image']='uploads/profile_images/full/'.$userId.'__'.$_FILES['profile_pic']['name'];
							$file_data['thumb_image']='uploads/profile_images/thumb/'.$userId.'__'.$_FILES['profile_pic']['name'];
							$file_data['orginal_image']='uploads/profile_images/orginal/'.$userId.'__'.$_FILES['profile_pic']['name'];
							$file_data['app_thumb']='uploads/profile_images/app_thumb/'.$userId.'__'.$_FILES['profile_pic']['name'];
							save_profile_image($file_data,$userId);
						}
						else
						{
							$mme =mime_content_type_new( $_FILES['profile_pic']['name']);
							log_message('debug','++ registration +++ User Edit Profile +++ : File upload failed');
							createFile('profile_pic_upload', print_r($mme, true));
						}
					}
					
					log_message('debug','++ User +++ User Edit Profile +++ : All data inserted successfully');
					$regdata['user_detail'] = $regResult;
					$status = $this->lang->line('rest_status_success');
					$message = $this->lang->line('profile_update');
					$this->response(array("status"=>$status,'message'=>$message,"data"=>array("user_detail" => $regdata['user_detail'],"token_data"=>$token)), 202);
				}
				else
				{
					log_message('debug','++ User +++ User Edit Profile +++  : data not updated');
					$errors['update_error'] = $this->lang->line('error_update')." profile";
					$status = $this->lang->line('rest_status_failed');
					$this->response(array("errorCode"=>"FR","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
				}
			}
			else
			{
				log_message('debug','++ user +++ Edit Profile +++ : Token not matched.');
				$errors['invalid_token'] = $this->lang->line('invalid_token');
				$status = $this->lang->line('rest_status_failed');
				$this->response(array("errorCode"=>"ES2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
			}
		}
		else
		{
			log_message('debug','++ User +++ User Edit Profile +++  : Got Form validation error');
			$errors = $this->form_validation->_error_array;
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"FVF","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}	
	}
	
	function addEditKid_post()
	{
		createFile('addEditKid', print_r($_POST, true));
		log_message('debug','++ User +++ Add Edit Kid +++ : API call successfully.');
		$userId=$this->input->post('userid');
		$token = $this->input->post('token');
		$job_id = $this->input->post('job_id'); // for adding child to job
		$this->form_validation->set_rules(addKid_rules());
		
		if($this->form_validation->run())
		{
			$checkToken = $this->common_model->check_valid_user_token($userId,$token);	// check user token
			if($checkToken)
			{
				$originalvalue = $this->input->post('dob');
				$dob = date('Y-m-d', strtotime($originalvalue));

				if($this->input->post('child_id')==''|| $this->input->post('child_id')==null)
				{
					$this->db->select('usertype');
					$this->db->where('userid',$userId);
					$usertype=$this->db->get('users');
					$usertype=$usertype->row();
					
					if($usertype->usertype=='P')
					{
						$parent_id = $userId;
					}
					else 
					{
						$parent_id = 0;
					}
				}
				else
				{
					$parent_id = $userId;
				}
				
				$kid_data = array(
						'child_id'				=> $this->input->post('child_id')?$this->input->post('child_id'):'',
						'parent_user_id' 		=>$parent_id,
						'child_name'			=>$this->input->post('child_name'),
						'child_relation'		=>$this->input->post('child_relation'),
						'sex'					=>$this->input->post('sex'),
						'dob'					=>$dob,
						'allergy_status'		=>$this->input->post('allergy_status'),
						'allergies'				=>$this->input->post('allergies'),
						'medicator_status'		=>$this->input->post('medicator_status'),
						'medicator'				=>$this->input->post('medicator'),
						'notes'					=>$this->input->post('notes'),
						'fav_food'				=>$this->input->post('fav_food'),
						'fav_book'				=>$this->input->post('fav_book'),
						'fav_cartoon'			=>$this->input->post('fav_cartoon'),
						'special_needs_status'	=>$this->input->post('special_needs_status'),
						'special_needs'			=>$this->input->post('special_needs'),
						'parent_name'			=>$this->input->post('parent_name'),
						'parent_contact'		=>$this->input->post('parent_contact'),
						);
				log_message('debug','++ User +++ Add Edit Kid +++ : No Form validation error');
		
				if($job_id && $job_id!=null && $job_id!='') //for adding child to job.
					$regResult = $this->user_model->addEditKid($userId,$kid_data,$job_id);//for adding child to job.
				else
					$regResult = $this->user_model->addEditKid($userId,$kid_data);
		
				if($regResult)
				{
					log_message('debug','++ User +++ Add Edit Kid +++ : All data updated successfully');
					$child_id = $regResult[1]->child_id;
					
					if(!empty($_FILES['child_pic']['name']))
					{
						createFile('child_pic', print_r($_FILES['child_pic'], true));
							
						$_FILES['child_pic']['tmp_name']= $_FILES['child_pic']['tmp_name'];
						$_FILES['child_pic']['size']= $_FILES['child_pic']['size'];
						$_FILES['child_pic']['date_added'] = date('Y-m-d H:i:s');
							
						$uploads_dir_file = $this->config->item('image_path').'uploads/children/orginal/'.$child_id.'__'.$_FILES['child_pic']['name'];
							
						if (move_uploaded_file($_FILES['child_pic']['tmp_name'], $uploads_dir_file))
						{
							$thumb = $this->config->item('image_path').'uploads/children/thumb/'.$child_id.'__'.$_FILES['child_pic']['name'];
							$full = $this->config->item('image_path').'uploads/children/full/'.$child_id.'__'.$_FILES['child_pic']['name'];
							resizeImage($uploads_dir_file,$thumb,THUMB_SIZE_WIDTH,THUMB_SIZE_HIEGHT);
							resizeImage($uploads_dir_file,$full,LARGE_SIZE_WIDTH,LARGE_SIZE_HIEGHT);
							
							$folderName=$this->config->item('image_path').'uploads/children/app_thumb/';
							if(!file_exists($folderName))
								mkdir($folderName,0777,true);
							$appthumb = $this->config->item('image_path').'uploads/children/app_thumb/'.$child_id.'__'.$_FILES['child_pic']['name'];
							resizeImage($uploads_dir_file,$appthumb,APPTHUMB_SIZE_WIDTH,APPTHUMB_SIZE_HIEGHT);
							
							$file_data=array();
							$file_data['child_id']=$child_id;
							$file_data['main_image']='uploads/children/full/'.$child_id.'__'.$_FILES['child_pic']['name'];
							$file_data['thumb_image']='uploads/children/thumb/'.$child_id.'__'.$_FILES['child_pic']['name'];
							$file_data['orginal_image']='uploads/children/orginal/'.$child_id.'__'.$_FILES['child_pic']['name'];
							$file_data['app_thumb']='uploads/children/app_thumb/'.$child_id.'__'.$_FILES['child_pic']['name'];
								
							save_child_image($file_data,$child_id);
							$regResult[1]->main_image =  $this->config->item('image_url').'/'.$file_data['main_image'];
							$regResult[1]->thumb_image =  $this->config->item('image_url').'/'.$file_data['app_thumb'];
							$regResult[1]->orginal_image =  $this->config->item('image_url').'/'.$file_data['orginal_image'];
						}
						else
						{
							$mme =mime_content_type_new( $_FILES['child_pic']['name']);
							log_message('debug','++ registration +++ User Edit kid Profile +++ : File upload failed');
							createFile('child_pic_upload', print_r($mme, true));
						}
					}
					
					$status = $this->lang->line('rest_status_success');
					$message = $regResult[0];
					$this->response(array("status"=>$status,'message'=>$message,"data"=>array("child_detail" => /* (object) */$regResult[1],"token_data"=>$token)), 202);
				}
				else
				{
					log_message('debug','++ User +++ Add Edit Kid +++  : data not updated');
					$errors['update_error'] = $this->lang->line('error_update')." profile";
					$status = $this->lang->line('rest_status_failed');
					$this->response(array("errorCode"=>"FU","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
				}
			}
			else
			{
				log_message('debug','++ user +++ Add Edit Kid +++ : Token not matched.');
				$errors['invalid_token'] = $this->lang->line('invalid_token');
				$status = $this->lang->line('rest_status_failed');
				$this->response(array("errorCode"=>"ES2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
			}
		}
		else
		{
			log_message('debug','++ User +++ Add Edit Kid +++  : Got Form validation error');
			$errors = $this->form_validation->_error_array;
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"FVF","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}
	}
	
	function getKids_post()
	{
		createFile('getKids', print_r($_POST, true));
		log_message('debug','++ User +++ View Kid +++ : API call successfully.');
	
		$this->form_validation->set_rules('userid','User id','required|trim');
		$this->form_validation->set_rules('token','Token','required|trim');
	
		if($this->form_validation->run())
		{
			$userId=$this->input->post('userid');
			$token = $this->input->post('token');
			$checkToken = $this->common_model->check_valid_user_token($userId,$token);	// check user token
			if($checkToken)
			{
				log_message('debug','++ User +++ View Kid +++ : No Form validation error');
	
				$regResult = $this->user_model->getKids($userId);
	
				if($regResult)
				{
					log_message('debug','++ User +++ View Kid +++ : record found');
					$status = $this->lang->line('rest_status_success');
					$message = $this->lang->line('record_found');
					$this->response(array("status"=>$status,'message'=>$message,"data"=>array("child_list" => $regResult,"token_data"=>$token)), 202);
				}
				else
				{
					log_message('debug','++ User +++ View Kid +++  : Record not found');
					$errors['record_not_found'] = $this->lang->line('child_not_found');
					$status = $this->lang->line('rest_status_failed');
					$this->response(array("errorCode"=>"ER","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
				}
			}
			else
			{
				log_message('debug','++ user +++ View Kid +++ : Token not matched.');
				$errors['invalid_token'] = $this->lang->line('invalid_token');
				$status = $this->lang->line('rest_status_failed');
				$this->response(array("errorCode"=>"ES2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
			}
		}
		else
		{
			log_message('debug','++ User +++ View Kid +++  : Got Form validation error');
			$errors = $this->form_validation->_error_array;
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"FVF","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}
	}
	
	function deleteKid_post()
	{
		createFile('deleteKid', print_r($_POST, true));
		log_message('debug','++ User +++ delete Kid +++ : API call successfully.');
	
		$this->form_validation->set_rules('userid','User id','required|trim');
		$this->form_validation->set_rules('token','Token','required|trim');
		$this->form_validation->set_rules('child_id','Child id','required|trim');
	
		if($this->form_validation->run())
		{
			$userId=$this->input->post('userid');
			$token = $this->input->post('token');
			$kid_id= $this->input->post('child_id');
			$checkToken = $this->common_model->check_valid_user_token($userId,$token);	// check user token
			if($checkToken)
			{
				log_message('debug','++ User +++ delete Kid +++ : No Form validation error');
	
				$regResult = $this->user_model->deleteKid($userId,$kid_id);
	
				if($regResult)
				{
					log_message('debug','++ User +++ delete Kid +++ :child deleted');
					$status = $this->lang->line('rest_status_success');
					$message = $this->lang->line('child_delete');
					$this->response(array("status"=>$status,'message'=>$message,"data"=>array("token_data"=>$token)), 202);
				}
				else
				{
					log_message('debug','++ User +++ delete Kid +++  : Record not found');
					$errors['record_not_found'] = $this->lang->line('child_not_found');
					$status = $this->lang->line('rest_status_failed');
					$this->response(array("errorCode"=>"ER","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
				}
			}
			else
			{
				log_message('debug','++ user +++ delete Kid +++ : Token not matched.');
				$errors['invalid_token'] = $this->lang->line('invalid_token');
				$status = $this->lang->line('rest_status_failed');
				$this->response(array("errorCode"=>"ES2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
			}
		}
		else
		{
			log_message('debug','++ User +++ delete Kid +++  : Got Form validation error');
			$errors = $this->form_validation->_error_array;
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"FVF","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}
	}
	
	
	function check_credits_post()
	{
		createFile('check_credits', print_r($_POST, true));
		log_message('debug','++ User +++ User Credits +++ : API call successfully.');
	
		$this->form_validation->set_rules('userid','User id','required|trim');
		$this->form_validation->set_rules('token','Token','required|trim');
		/* 
		$this->form_validation->set_rules('job_start_date','Start date','trim');
		$this->form_validation->set_rules('job_end_date','end_date','trim');
		*/
		if($this->form_validation->run())
		{
			$userId = $this->input->post('userid');
			$token = $this->input->post('token');
			$checkToken = $this->common_model->check_valid_user_token($userId,$token);	// check user token
			if($checkToken)
			{
				log_message('debug','++ User +++ User Credits +++ : No Form validation error');
				
				$credits = $this->user_model->check_credits($userId);
	
				if($credits)
				{
					log_message('debug','++ User +++ User Credits +++ : record found');
					$status = $this->lang->line('rest_status_success');
					$message = $this->lang->line('record_found');
					$this->response(array("status"=>$status,'message'=>$message,"data"=>array("available_credits" => $credits,"token_data"=>$token)), 202);
				}
				else
				{
					log_message('debug','++ User +++ User Credits +++ : Record not found');
					$errors['record_not_found'] = $this->lang->line('record_not_found');
					$status = $this->lang->line('rest_status_failed');
					$this->response(array("errorCode"=>"ER","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
				}
			}
			else
			{
				log_message('debug','++ User +++ User Credits +++ : Token not matched.');
				$errors['invalid_token'] = $this->lang->line('invalid_token');
				$status = $this->lang->line('rest_status_failed');
				$this->response(array("errorCode"=>"ES2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
			}
		}
		else
		{
			log_message('debug','++ User +++ User Credits +++ : Got Form validation error');
			$errors = $this->form_validation->_error_array;
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"FVF","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}
	}
	
	function get_job_prefer_list_post()
	{
		createFile('get_job_prefer_list', print_r($_POST, true));
		log_message('debug','++ User +++ Job Preference list +++ : API call successfully.');
	
		$this->form_validation->set_rules('userid','User id','required|trim');
		$this->form_validation->set_rules('token','Token','required|trim');
	
		if($this->form_validation->run())
		{
			$userId = $this->input->post('userid');
			$token = $this->input->post('token');
			$checkToken = $this->common_model->check_valid_user_token($userId,$token);	// check user token
			if($checkToken)
			{
				log_message('debug','++ User +++ Job Preference list +++ : No Form validation error');
	
				$preferenceList = $this->user_model->getJobPreferenceList();
	
				if($preferenceList)
				{
					log_message('debug','++ User +++ Job Preference list +++ : record found');
					$status = $this->lang->line('rest_status_success');
					$message = $this->lang->line('record_found');
					$this->response(array("status"=>$status,'message'=>$message,"data"=>array("jobPreferList" => $preferenceList,"token_data"=>$token)), 202);
				}
				else
				{
					log_message('debug','++ User +++ Job Preference list +++ : Record not found');
					$errors['record_not_found'] = $this->lang->line('record_not_found');
					$status = $this->lang->line('rest_status_failed');
					$this->response(array("errorCode"=>"ER","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
				}
			}
			else
			{
				log_message('debug','++ User +++ Job Preference list +++ : Token not matched.');
				$errors['invalid_token'] = $this->lang->line('invalid_token');
				$status = $this->lang->line('rest_status_failed');
				$this->response(array("errorCode"=>"ES2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
			}
		}
		else
		{
			log_message('debug','++ User +++ Job Preference list +++ : Got Form validation error');
			$errors = $this->form_validation->_error_array;
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"FVF","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}
	}
}