<?php
/*
 * Filename				:	forget_password
* Classname				:	Forget_password
* Description			:	Provide web services for user Forget password
* Author				:	Anjali
* -------------------------------------------------------------------------------------------------------------------------------------------------
*/

	if ( ! defined('BASEPATH')) 
		exit('No direct script access allowed');

	require APPPATH.'/libraries/REST_Controller.php';

	class ForgetPassword extends REST_Controller 
	{
		// Global array to pass with each view
		var $data = array();
		var $api_time = array();
			
		function __construct()
		{
			parent::__construct();
			$this->load->model(array('common_model'));
		}
		
		//function for forget password
		function index_post() 
		{
			createFile('forgetPassword', print_r($_POST, true));
			log_message('debug','++ forgetPassword +++ Forget Password +++ : API call successfully.');
			$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
			if ($this->form_validation->run() != FALSE) 
			{
				log_message('debug','++ forgetPassword +++ Forget Password +++ : No Form validation error');
	
	            $userEmail = $this->input->post('email');
	            $usertype = $this->input->post('usertype');
	           	$checkByUserEmail = $this->common_model->checkByUserEmail($userEmail,$usertype);
	           
	            if($checkByUserEmail)
	            {
	                $user_id = $checkByUserEmail->userid;
	                $userEmail = $checkByUserEmail->username;
	                
	                $new_password = generatePassword($user_id);
	                $data['username']=$userEmail;
	                $data['password'] = $new_password;
	                $data['password_reset'] = 1;
	                $isupdate = $this->common_model->updatePassword($data);
	
	                if(isset($checkByUserEmail->username) && !empty($checkByUserEmail->username))
	                {
	                	$mailTemplate = getMailTemplates(array('mail_name'=>'reset_password'));
	                	$mailTemplate=$mailTemplate[0];
	                	
	                	$from = $mailTemplate['from'];
	                	$cc = explode(',',$mailTemplate['cc']);
	                	//$Bcc = explode(',',$mailTemplate['Bcc']);
	                	
	                	$to = $userEmail;
	                	$subject = $mailTemplate['subject'];
	                	
	                	$to_replace = array('{firstname}','{lastname}','{password}');
	                	$replace_with = array($checkByUserEmail->firstname,$checkByUserEmail->lastname,$new_password);
	                	
	                	$text = str_ireplace($to_replace,$replace_with,$mailTemplate['description']);
	                    sendEmail($from, $to, $subject, $text);		// call common_helper 
	                }
	
	                $status = $this->lang->line('rest_status_success');
	                $message = $this->lang->line('password_message');
	                $this->response(array("status"=>$status,'message'=>$message,), 202);
				}
	            else
	            {
	                $errors['invalid_email'] =  $this->lang->line('invalid_email');
	                $status = $this->lang->line('rest_status_failed');
	                $this->response(array("errorCode"=>"IE","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);				
	            }
	        } 
	        else 
	        {
	            log_message('debug','++ forgetPassword +++ Forget Password +++ : Got Form validation error');
	            $errors = $this->form_validation->_error_array;
	            $status = $this->lang->line('rest_status_failed');
	            $this->response(array("errorCode"=>"FVF","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);		
	        }
    	}
	}	