<?php
	if ( ! defined('BASEPATH')) 
		exit('No direct script access allowed');
	
	require(APPPATH.'libraries/REST_Controller.php');
 
	class Logout extends REST_Controller
	{
		function __construct()
		{
			parent::__construct();
			
			//loading required model
			$this->load->model('common_model');
		}
		
		
		function index_post()
		{
			createFile('logout', print_r($_POST, true));
			log_message('debug','++ logout +++ User Logout +++ : API call successfully.');
			$this->form_validation->set_rules(rule_logout());
			$userId = $this->input->post('userid');
			$userToken = $this->input->post('token');
			if($this->form_validation->run())
			{
				log_message('debug','++ logout +++ User Logout +++ : No Form validation error');
			
			/* 	$checkToken = $this->common_model->check_valid_user_token($userId,$userToken);	// check user token
				if($checkToken)
				{ */
				$this->common_model->deleteUserToken($userId,$userToken);
				$status = $this->lang->line('rest_status_success');
				$message = $this->lang->line('logout_success');
				$this->response(array("status"=>$status,'message'=>$message,"data"=>''), 202);
				/* }
				else
	            {
	            	log_message('debug','++ logout +++ User Logout +++ : Invaild credentials entered ');
	                $errors['invalid_token'] =  $this->lang->line('invalid_token');
	                $status = $this->lang->line('rest_status_failed');
	                $this->response(array("errorCode"=>"FL2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);				
	            } */
			}
			else 
			{
				log_message('debug','++ logout +++ User Logout +++ : Got Form validation error');
				$errors = $this->form_validation->_error_array;
				$status = $this->lang->line('rest_status_failed');
				$this->response(array("errorCode"=>"FVF","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
			}
		}
	}