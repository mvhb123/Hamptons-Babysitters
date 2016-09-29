<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Filename				:	user
* Classname				:	User
* Description			:	Provide web services for user(Sitter)
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
		$this->load->model('common_model');
		$this->load->model('sitter/user_model');
	}

	
	/**
	 * login
	 *
	 * @access	public
	 * @param	string username
	 * @param	string password
	 * @param	string device_token
	 * @return	array containing user details
	 */
	function sitterLogin_post()
	{
		createFile('Login', print_r($_POST, true));
		log_message('debug','++ login +++ User Login +++ : API call successfully.');
		$username = $this->input->post('username');
		$userPassword = $this->input->post('password');
		$this->form_validation->set_rules(rule_login());
		if($this->form_validation->run())
		{
			log_message('debug','++ login +++ User Login +++ : No Form validation error');
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
	
				$this->response(array("status"=>$status,'message'=>$message,"data"=>array('user_detail' => $data['user_detail'],"token_data"=>$token, 'notification_count'=>$noti)), 202);
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
			log_message('debug','++ login +++ User Login +++ : Got Form validation error');
			$errors = $this->form_validation->_error_array;
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"FVF","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}
	}
	

	/**
	 * login
	 *
	 * @access	public
	 * @param	string userid
	 * @param	string token
	 * @param	string paren_id
	 * @return	array containing parent details
	 */
	function parentProfile_post()
	{
		createFile('parentProfile', print_r($_POST, true));
		log_message('debug','++ user +++ Parent Profile +++ : API called successfully');
		$this->form_validation->set_rules(view_parent_rules());
		
		if ($this->form_validation->run() != FALSE)
		{
			log_message('debug','++ user +++ Parent Profile +++ : No form error');
			$userId = $this->input->post('userid');
			$userToken = $this->input->post('token');
			$parent_id = $this->input->post('parent_id');
			
			$checkToken = $this->common_model->check_valid_user_token($userId,$userToken);	// check user token
			if($checkToken)
			{         		
                $profileData = $this->user_model->parentProfile($parent_id);              
				if($profileData)
				{
					log_message('debug','++ user +++ Parent Profile +++ : All data sent successfully');
					$status = $this->lang->line('rest_status_success');
					$message = $this->lang->line('record_found');
					$this->response(array("status"=>$status,'message'=>$message,"data"=>array("token_data"=>$userToken, "profileData"=>$profileData[0])), 202);
				}
				else
				{
					log_message('debug','++ user +++ Parent Profile +++ : No record found');
					$errors['record_not_found'] = $this->lang->line('record_not_found');
					$status = $this->lang->line('rest_status_failed');
					$this->response(array("errorCode"=>"FL2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
				}
			}
			else
			{
				log_message('debug','++ user +++ Parent Profile +++ : Token not matched.');
				$errors['invalid_token'] = $this->lang->line('invalid_token');
				$status = $this->lang->line('rest_status_failed');
				$this->response(array("errorCode"=>"ES2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
			}
		}
		else
		{
			log_message('debug','++ user +++ Parent Profile +++ : Got form validation error.');
			$errors = $this->form_validation->_error_array;
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"FVF","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}
	}

	
	/**
	 * Edit profile
	 *
	 * @access	public
	 * @param	string user id
	 * @param	string token
	 * @param	string cpr_holder(yes/no)
	 * @param	string cpr_adult(yes/no)
	 * @param	string first_aid_cert(yes/no)
	 * @param	string water_certification(yes/no)
	 * @param	string infant_training(yes/no)
	 * @param	string hampton_babysitter_training(yes/no)
	 * @param	string cpr_date
	 * @param	string cpr_adult_date
	 * @param	string first_aid_cert_date
	 * @param	string water_cert_date
	 * @param	string infant_training_date
	 * @param	string babysitter_training_date
	 * @param	string firstname
	 * @param	string lastname
	 * @param	string phone
	 * @param	string username
	 * @param	string otherpreference
	 * @param	string criminal_record(yes/no)
	 * @param	string clean_drive_record(yes/no)
	 * @param	string have_child(yes/no)
	 * @param	string have_car(yes/no)
	 * @param	string about_me
	 * @param	string preference (comma separated prefer_id)
	 * @param	string delete_pic (yes/no)
	 * @return	array of job details
	 */
	function edit_profile_post()
	{	
		createFile('edit_profile', print_r($_POST, true));
		log_message('debug','++ User +++ User Edit Profile +++ : API call successfully.');
		$this->form_validation->set_rules(sitterEditProfile_rules());
		
		if($this->input->post('cpr_holder')=='yes'||$this->input->post('cpr_holder')=='Yes')
			$this->form_validation->set_rules('cpr_date', 'CPR certification date', 'required|trim');
		
		if($this->input->post('cpr_adult')=='yes'||$this->input->post('cpr_adult')=='Yes')
			$this->form_validation->set_rules('cpr_adult_date', 'CPR certification adult date', 'required|trim');
		
		if($this->input->post('first_aid_cert')=='yes'||$this->input->post('first_aid_cert')=='Yes')
			$this->form_validation->set_rules('first_aid_cert_date', 'First aid certification date', 'required|trim');
		
		if($this->input->post('water_certification')=='yes'||$this->input->post('water_certification')=='Yes')
			$this->form_validation->set_rules('water_cert_date', 'Water certification date', 'required|trim');
		
		//if($this->input->post('infant_training')=='yes' ||$this->input->post('infant_training')=='Yes')
			//$this->form_validation->set_rules('infant_training_date', 'Infant Training date', 'required|trim');
		
		if($this->input->post('hampton_babysitter_training')=='yes'||$this->input->post('hampton_babysitter_training')=='Yes')
			$this->form_validation->set_rules('babysitter_training_date', 'Hampton babysitter training date', 'required|trim'); 
		
		if($this->form_validation->run())
		{
			log_message('debug','++ User +++ User Edit Profile +++ : No Form validation error');
			$userId=$this->input->post('userid');
			$token = $this->input->post('token');
			
			$checkToken = $this->common_model->check_valid_user_token($userId,$token);	// check user token
			if($checkToken)
			{
				$originalvalue = $this->input->post('dob');
				$dob = date('Y-m-d', strtotime($originalvalue));
				
				$udata = array(
						'firstname'			=> $this->input->post('firstname'),
						'lastname'			=> $this->input->post('lastname'),
						'dob'			 	=> $dob,
						'phone'			    => $this->input->post('phone'),
						'username'			=> $this->input->post('username'),
						'modified_date'	    => date('Y-m-d H:i:s'),
				);
				
				$sdata= array(
						'about_me' 			 			=> $this->input->post('about_me'),
						//'have_car'						=> $this->input->post('have_car'),
						'cpr_holder'					=> $this->input->post('cpr_holder'),
						'cpr_adult'			 			=> $this->input->post('cpr_adult'),
						'first_aid_cert' 				=> $this->input->post('first_aid_cert'),
						//'clean_drive_record'			=> $this->input->post('clean_drive_record'),
						//'criminal_record'	 			=> $this->input->post('criminal_record'),
						//'have_child' 		 			=> $this->input->post('have_child'),
						'water_certification' 		 	=> $this->input->post('water_certification'),
						//'infant_training' 		 		=> $this->input->post('infant_training'),
						'hampton_babysitter_training' 	=> $this->input->post('hampton_babysitter_training'),
						'cpr_date' 		 				=> ($this->input->post('cpr_date'))?date('Y-m-d H:i:s',strtotime($this->input->post('cpr_date'))):null,
						'cpr_adult_date' 			 	=> ($this->input->post('cpr_adult_date'))?date('Y-m-d H:i:s',strtotime($this->input->post('cpr_adult_date'))):null,
						'first_aid_cert_date' 		 	=> ($this->input->post('first_aid_cert_date'))?date('Y-m-d H:i:s',strtotime($this->input->post('first_aid_cert_date'))):null,
						'water_cert_date' 			 	=> ($this->input->post('water_cert_date'))?date('Y-m-d H:i:s',strtotime($this->input->post('water_cert_date'))):null,
						//'infant_training_date' 		 	=> ($this->input->post('infant_training_date'))?date('Y-m-d H:i:s',strtotime($this->input->post('infant_training_date'))):null,
						'babysitter_training_date' 		=> ($this->input->post('babysitter_training_date'))?date('Y-m-d H:i:s',strtotime($this->input->post('babysitter_training_date'))):null,
						'special_need_exp' 			 	=> $this->input->post('otherpreference')
						);
				
				$updata= array(
						'local_phone'			=> $this->input->post('local_phone'),
						'work_phone'			=> $this->input->post('work_phone')
				);
				
				if($this->input->post('delete_pic')=='yes')
				{
					$this->db->select('main_image,thumb_image,orginal_image,app_thumb');
					$this->db->where('userid',$userId);
					$pro_image=$this->db->get('user_profile');
					if($pro_image->num_rows()>0)
					{
						$img_data = array(
								'main_image' => null,
								'orginal_image' => null,
								'thumb_image' => null,
								'app_thumb' => null
						);
							
						$this->db->where('userid',$userId);
						$this->db->update('user_profile',$img_data);
				
						$pro_image= $pro_image->row();
						if($pro_image->main_image!==null)
							unlink($this->config->item('image_path').$pro_image->main_image);
						if($pro_image->orginal_image!==null)
							unlink($this->config->item('image_path').$pro_image->orginal_image);
						if($pro_image->thumb_image!==null)
							unlink($this->config->item('image_path').$pro_image->thumb_image);
						if($pro_image->app_thumb!==null)
							unlink($this->config->item('image_path').$pro_image->app_thumb);
					}
				}
				
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
						
						$updata['main_image']='uploads/profile_images/full/'.$userId.'__'.$_FILES['profile_pic']['name'];
						$updata['thumb_image']='uploads/profile_images/thumb/'.$userId.'__'.$_FILES['profile_pic']['name'];
						$updata['orginal_image']='uploads/profile_images/orginal/'.$userId.'__'.$_FILES['profile_pic']['name'];
						$updata['app_thumb']='uploads/profile_images/app_thumb/'.$userId.'__'.$_FILES['profile_pic']['name'];
						
					}
					else
					{
						$mme =mime_content_type_new( $_FILES['profile_pic']['name']);
						log_message('debug','++ registration +++ User Edit Profile +++ : File upload failed');
						createFile('profile_pic_upload', print_r($_FILES['profile_pic']['error'], true));
					}
				}
				
				$pdata= $this->input->post('preferences');
				$regResult = $this->user_model->update_user_detail1($userId,$udata,$updata,$pdata,$sdata);
				
				if($regResult)
				{
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
	
	
	
	/**
	 * Sitter earnings
	 *
	 * @access	public
	 * @param	string user id
	 * @param	string token
	 * @return	array of job details
	 */
	function sitterEarnings_post()
	{
		createFile('sitterEarnings', print_r($_POST, true));
		log_message('debug','++ user +++ Parent Profile +++ : API called successfully');
		$this->form_validation->set_rules(view_parent_rules());
		
		if ($this->form_validation->run() != FALSE)
		{
			log_message('debug','++ user +++ Parent Profile +++ : No form error');
			$userId = $this->input->post('userid');
			$userToken = $this->input->post('token');
			
			$checkToken = $this->common_model->check_valid_user_token($userId,$userToken);	// check user token
			if($checkToken)
			{
				$profileData = $this->user_model->earnings($userId,true,'2014');
			
				if($profileData)
				{
					log_message('debug','++ user +++ Parent Profile +++ : All data sent successfully');
					$status = $this->lang->line('rest_status_success');
					$message = $this->lang->line('record_found');
					$this->response(array("status"=>$status,'message'=>$message,"data"=>array("token_data"=>$userToken, "profileData"=>$profileData)), 202);
				}
				else
				{
					log_message('debug','++ user +++ Parent Profile +++ : No record found');
					$errors['record_not_found'] = $this->lang->line('record_not_found');
					$status = $this->lang->line('rest_status_failed');
					$this->response(array("errorCode"=>"FL2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
				}
			}
			else
			{
				log_message('debug','++ user +++ Parent Profile +++ : Token not matched.');
				$errors['invalid_token'] = $this->lang->line('invalid_token');
				$status = $this->lang->line('rest_status_failed');
				$this->response(array("errorCode"=>"ES2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
			}
		}
		else
		{
			log_message('debug','++ user +++ Parent Profile +++ : Got form validation error.');
			$errors = $this->form_validation->_error_array;
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"FVF","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}
	}
	
	
	
	/**
	 * Sitter preference list
	 *
	 * @access	public
	 * @return	array of sitter preference list
	 */
	function get_sitter_prefer_list_post()
	{
		createFile('get_sitter_prefer_list', print_r($_POST, true));
		log_message('debug','++ User +++ Sitter Preference list +++ : API call successfully.');

		$preferenceList = $this->common_model->preferenceList();

		if($preferenceList)
		{
			log_message('debug','++ User +++ Sitter Preference list +++ : record found');
			$status = $this->lang->line('rest_status_success');
			$message = $this->lang->line('record_found');
			$certification=array(
					'0'=>array(
							'name'=>'CPR Certification-Infant/Toddler',
							'key1' => 'cpr_holder',
							'key2' => 'cpr_date',
							'is_selected'=>'yes/no',
							'date'=>'',
							
					),
						
					'1'=>array(
							'name'=>'CPR Certification Adult',
							'key1' => 'cpr_adult',
							'key2' => 'cpr_adult_date',
							'is_selected'=>'yes/no',
							'date'=>'',
					),
						
					'2'=> array(
							'name'=>'First-Aid Certification',
							'key1' => 'first_aid_cert',
							'key2' => 'first_aid_cert_date',
							'is_selected'=>'yes/no',
							'date'=>'',
					),
					'3'=>array(
							'name'=>'Water Certification',
							'key1' => 'water_certification',
							'key2' => 'water_cert_date',
							'is_selected'=>'yes/no',
							'date'=>'',
							),
					
					/* '4'=>array(
							'name'=>'Infant Training',
							'key1' => 'infant_training',
							'key2' => 'infant_training_date',
							'is_selected'=>'yes/no',
							'date'=>'',
							), */
					'4'=>array(
							'name'=>'Hamptons Babysitters Training',
							'key1' => 'hampton_babysitter_training',
							'key2' => 'babysitter_training_date',
							'is_selected'=>'yes/no',
							'date'=>'',
							),
					/*'5'=>array(
							'name'=>'Have car',
							'key1' => 'have_car',
							'key2' => '',
							'is_selected'=>'yes/no',
							'date'=>'NA',
							),
					'6'=>array(
							'name'=>'Have child',
							'key1' => 'have_child',
							'key2' => '',
							'is_selected'=>'yes/no',
							'date'=>'NA',
							),
					'8'=>array(
							'name'=>'Criminal Record',
							'key1' => 'criminal_record',
							'key2' => '',
							'is_selected'=>'yes/no',
							'date'=>'NA',
							), 
					'5'=>array(
							'name'=>'Clean Driving record',
							'key1' => 'clean_drive_record',
							'key2' => '',
							'is_selected'=>'yes/no',
							'date'=>'NA',
							) */);
			$this->response(array("status"=>$status,'message'=>$message,"data"=>array("sitterPreferList" => $preferenceList , "certifications"=>$certification)), 202);
		}
		else
		{
			log_message('debug','++ User +++ Sitter Preference list +++ : Record not found');
			$errors['record_not_found'] = $this->lang->line('record_not_found');
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"ER","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}
	}
	
	
	/**
	 * updateStatus
	 *
	 * @access	public
	 * @param	int userid
	 * @param	string token
	 * @param	string status active/inactive
	 * @return	array containing user details
	 */
	function updateStatus_post()
	{
		createFile('updateStatus', print_r($_POST, true));
		log_message('debug','++ updateStatus +++ Sitter Status +++ : API call successfully.');
		
		$this->form_validation->set_rules('userid','User Id', 'required');
		$this->form_validation->set_rules('token','Token', 'required');
		$this->form_validation->set_rules('status','Status', 'required');
		
		if($this->form_validation->run())
		{
			log_message('debug','++ updateStatus +++ Sitter Status +++ : No Form validation error');
			
			$userId = $this->input->post('userid');
			$userToken = $this->input->post('token');
			$status = $this->input->post('status');
				
			
			$checkToken = $this->common_model->check_valid_user_token($userId,$userToken);	// check user token
			if($checkToken)
			{
				$Result = $this->user_model->update_user_status($userId,$status);
				if($Result[0])
				{
					$data['user_detail'] = $Result[1];
					$status = $this->lang->line('rest_status_success');
					$message = $this->lang->line('status_updated');
					$this->response(array("status"=>$status,'message'=>$message,"data"=>array('user_detail' => $data['user_detail'],"token_data"=>$userToken)), 202);
				}
				else
				{
					log_message('debug','++ updateStatus +++ Sitter Status +++ : Record not found');
					$errors['update_error'] = $Result[1];
					$status = $this->lang->line('rest_status_failed');
					$this->response(array("errorCode"=>"FL2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
				}
			}
			else
			{
				log_message('debug','++ updateStatus +++ Sitter Status +++ : Token not matched.');
				$errors['invalid_token'] = $this->lang->line('invalid_token');
				$status = $this->lang->line('rest_status_failed');
				$this->response(array("errorCode"=>"ES2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
			}
		}
		else
		{
			log_message('debug','++ updateStatus +++ Sitter Status +++ : Got Form validation error');
			$errors = $this->form_validation->_error_array;
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"FVF","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}
	}
	
}