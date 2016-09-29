<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
* Filename				:	resetPassword
* Classname				:	ResetPassword
* Description			:	Provide web services for Reset password
* Author				:	Anjali
* -------------------------------------------------------------------------------------------------------------------------------------------------
*/

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';

class ChangePassword extends REST_Controller
{
	// Global array to pass with each view
	var $data = array();
	var $api_time = array();
	
	function __construct()
	{
		parent::__construct();
		$this->load->model('common_model');
	}
	
	function index_post()
	{
		createFile('changePassword', print_r($_POST, true));
		log_message('debug','++ user +++ Update Password +++ : API called successfully');
		$this->form_validation->set_rules(change_password_rules());
	
		if ($this->form_validation->run() != FALSE)
		{
			log_message('debug','++ user +++ Update Password +++ : No form error');
			$userId = $this->input->post('userid');
			$userToken = $this->input->post('token');
			$password = $this->input->post('password');
			$cpassword = $this->input->post('current_password');
	
			$checkToken = $this->common_model->check_valid_user_token($userId,$userToken);	// check user token
			if($checkToken)
			{
				$data = array(
						'userid' 			=>$userId,
						'password'			=>$password,
						'password_reset'	=>0
				);
	
				$regResult = $this->common_model->changeCurrentPassword($userId,$cpassword,$data);
	
				if($regResult != false)
				{
					log_message('debug','++ user +++ Update Password +++ : All data inserted successfully');
					$data['user_detail'] = $regResult;
					$status = $this->lang->line('rest_status_success');
					$message = $this->lang->line('update_password');
					$this->response(array("status"=>$status,'message'=>$message,"data"=>array("user_detail" => $data['user_detail'],"token_data"=>$userToken)), 202);
				}
				else
				{
					log_message('debug','++ user +++ Update Password +++  : data not updated');
					$errors['update_error'] = $this->lang->line('wrong_password');
					$status = $this->lang->line('rest_status_failed');
					$this->response(array("errorCode"=>"FPU","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
				}
			}
			else
			{
				log_message('debug','++ user +++ Update Password +++ : Token not matched.');
				$errors['invalid_token'] = $this->lang->line('invalid_token');
				$status = $this->lang->line('rest_status_failed');
				$this->response(array("errorCode"=>"ES2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
			}
		}
		else
		{
			log_message('debug','++ user +++ Update Password +++  : Got Form validation error');
			$errors = $this->form_validation->_error_array;
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"FVF","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}
	
	}
}