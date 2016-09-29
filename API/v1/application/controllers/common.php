<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Filename				:	user
* Classname				:	User
* Description			:	Provide web services for user
* Create Date			:	17th Feb 2015
* Author				:	Anjali
* -------------------------------------------------------------------------------------------------------------------------------------------------
*/

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';

class Common extends REST_Controller 
{
	// Global array to pass with each view
	var $data = array();
	var $api_time = array();

	function __construct()
	{
		parent::__construct();
		$this->load->model('common_model');
	}
	
	
	function viewKidprofile_post()
	{
		createFile('addEditKid', print_r($_POST, true));
		log_message('debug','++ User +++ View Kid +++ : API call successfully.');
	
		$this->form_validation->set_rules('userid','User id','required|trim');
		$this->form_validation->set_rules('child_id','Child id','required|trim');
		$this->form_validation->set_rules('token','Token','required|trim');
	
		if($this->form_validation->run())
		{
			$userId=$this->input->post('userid');
			$token = $this->input->post('token');
			$kid_id=$this->input->post('child_id');
			$checkToken = $this->common_model->check_valid_user_token($userId,$token);	// check user token
			if($checkToken)
			{
				log_message('debug','++ User +++ View Kid +++ : No Form validation error');

				$regResult = $this->common_model->viewKid($kid_id);
	
				if($regResult)
				{
					log_message('debug','++ User +++ View Kid +++ : record found');
					
					$regdata['child_detail'] = $regResult;
					$status = $this->lang->line('rest_status_success');
					$message = $this->lang->line('record_found');
					$this->response(array("status"=>$status,'message'=>$message,"data"=>array("child_detail" => $regdata['child_detail'],"token_data"=>$token)), 202);
				}
				else
				{
					log_message('debug','++ User +++ View Kid  +++  : data not updated');
					$errors['record_not_found'] = $this->lang->line('record_not_found');
					$status = $this->lang->line('rest_status_failed');
					$this->response(array("errorCode"=>"FR","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
				}
			}
			else
			{
				log_message('debug','++ user +++ View Kid  +++ : Token not matched.');
				$errors['invalid_token'] = $this->lang->line('invalid_token');
				$status = $this->lang->line('rest_status_failed');
				$this->response(array("errorCode"=>"ES2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
			}
		}
		else
		{
			log_message('debug','++ User +++ View Kid  +++  : Got Form validation error');
			$errors = $this->form_validation->_error_array;
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"FVF","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}
	}
	
	
	function get_state_list_post()
	{
		createFile('get_state_list', print_r($_POST, true));
		log_message('debug','++ Common +++ State list +++ : API call successfully.');
		$country_id=$this->input->post('country_id');
		
		if($country_id=="")
		{
			$country_id=1;
		}
		
		$stateList = $this->common_model->getStateList($country_id);
		if($stateList)
		{
			log_message('debug','++ User +++ State list +++ : record found');
			$status = $this->lang->line('rest_status_success');
			$message = $this->lang->line('record_found');
			$this->response(array("status"=>$status,'message'=>$message,"data"=>array("stateList" => $stateList)), 202);
		}
		else
		{
			log_message('debug','++ User +++ State list +++ : Record not found');
			$errors['record_not_found'] = $this->lang->line('record_not_found');
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"ER","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}
			
	}
	
	function get_country_list_post()
	{
		createFile('get_country_list', print_r($_POST, true));
		log_message('debug','++ Common +++ Country list +++ : API call successfully.');
	
		$countryList = $this->common_model->getCountryList();
	
		if($countryList)
		{
			log_message('debug','++ User +++ Country list +++ : record found');
			$status = $this->lang->line('rest_status_success');
			$message = $this->lang->line('record_found');
			$this->response(array("status"=>$status,'message'=>$message,"data"=>array("countryList" => $countryList)), 202);
		}
		else
		{
			log_message('debug','++ User +++ Country list +++ : Record not found');
			$errors['record_not_found'] = $this->lang->line('record_not_found');
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"ER","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}
			
	}
	
	function get_notification_setting_post()
	{
		createFile('get_notification_setting', print_r($_POST, true));
		log_message('debug','++ Common +++ Notification Setting +++ : API call successfully.');
	
		$this->form_validation->set_rules('userid','User id','required|trim');
		$this->form_validation->set_rules('token','Token','required|trim');
		
		if($this->form_validation->run())
		{
			$userId=$this->input->post('userid');
			$token = $this->input->post('token');
			$noti = $this->common_model->get_notification($userId);
		
			if($noti)
			{
				log_message('debug','++ User +++ Notification Setting +++ : record found');
				$status = $this->lang->line('rest_status_success');
				$message = $this->lang->line('record_found');
				$this->response(array("status"=>$status,'message'=>$message,"data"=>array("notify" => $noti,"token_data"=>$token)), 202);
			}
			else
			{
				log_message('debug','++ User +++ Notification Setting +++ : Record not found');
				$errors['record_not_found'] = $this->lang->line('record_not_found');
				$status = $this->lang->line('rest_status_failed');
				$this->response(array("errorCode"=>"ER","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
			}
		}
		else
		{
			log_message('debug','++ User +++ Notification Setting  +++  : Got Form validation error');
			$errors = $this->form_validation->_error_array;
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"FVF","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}
	}
	
	
	function update_notification_setting_post()
	{
		createFile('update_notification_setting', print_r($_POST, true));
		log_message('debug','++ Common +++ Update Notification Setting +++ : API call successfully.');
	
		$this->form_validation->set_rules('userid','User id','required|trim');
		$this->form_validation->set_rules('token','Token','required|trim');
		$this->form_validation->set_rules('notify','Notify','required|trim');
	
		if($this->form_validation->run())
		{
			$userId=$this->input->post('userid');
			$token = $this->input->post('token');
			$checkToken = $this->common_model->check_valid_user_token($userId,$token);	// check user token
			if($checkToken)
			{
				$noti = $this->common_model->set_notification($userId,$this->input->post('notify'));
		
				if($noti)
				{
					log_message('debug','++ User +++ Update Notification Setting +++ : record found');
					$status = $this->lang->line('rest_status_success');
					$message = $this->lang->line('records_updated');
					$this->response(array("status"=>$status,'message'=>$message,"data"=>array("notify" => $noti,"token_data"=>$token)), 202);
				}
				else
				{
					log_message('debug','++ User +++ Update Notification Setting +++ : Record not found');
					$errors['error_update'] = $this->lang->line('error_update').' notfication setting.';
					$status = $this->lang->line('rest_status_failed');
					$this->response(array("errorCode"=>"ER","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
				}
			}
			else
			{
				$errors['invalid_token'] = $this->lang->line('invalid_token');
				$status = $this->lang->line('rest_status_failed');
				$this->response(array("errorCode"=>"ES2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
			}
				
		}
		else
		{
			log_message('debug','++ User +++ Update Notification Setting  +++  : Got Form validation error');
			$errors = $this->form_validation->_error_array;
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"FVF","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}
	}
	
	function decrease_badge_count_post()
	{
		createFile('decrease_badge_count', print_r($_POST, true));
		log_message('debug','++ Common +++ Update Notification Count +++ : API call successfully.');
	
		$this->form_validation->set_rules('userid','User id','required|trim');
		$this->form_validation->set_rules('token','Token','required|trim');
		$this->form_validation->set_rules('notification_id','Notification id','required|trim');
	
		if($this->form_validation->run())
		{	
			$userId=$this->input->post('userid');
			$token = $this->input->post('token');
			$checkToken = $this->common_model->check_valid_user_token($userId,$token);	// check user token
			if($checkToken)
			{
				log_message('debug','++ User +++ Update Notification Count +++ : No Form validation error');
				$noti = $this->common_model->updateNotification_count($this->input->post('notification_id'),$userId);
		
				if($noti!== false ||$noti !== null )
				{
					log_message('debug','++ Common +++ Update Notification Count +++ : record found');
					$status = $this->lang->line('rest_status_success');
					$message = $this->lang->line('records_updated');
					$this->response(array("status"=>$status,'message'=>$message,"data"=>array("badge"=>$noti,"token_data"=>$token)), 202);
				}
				else
				{
					log_message('debug','++ Common +++ Update Notification Count +++ : Record not found');
					$errors['error_update'] = $this->lang->line('error_update').' notification count.';
					$status = $this->lang->line('rest_status_failed');
					$this->response(array("errorCode"=>"ER","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
				}
			}
			else
			{
				log_message('debug','++ user +++ Update Notification Count  +++ : Token not matched.');
				$errors['invalid_token'] = $this->lang->line('invalid_token');
				$status = $this->lang->line('rest_status_failed');
				$this->response(array("errorCode"=>"ES2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
			}
		}
		else
		{
			log_message('debug','++ Common +++ Update Notification Count  +++  : Got Form validation error');
			$errors = $this->form_validation->_error_array;
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"FVF","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}
	}
	
	function get_relation_to_child_post()
	{
		createFile('relation_to_child', print_r($_POST, true));
		log_message('debug','++ User +++ relation_to_child list +++ : API call successfully.');
		$preferenceList = $this->common_model->getChildRelation();
	
		if($preferenceList)
		{
			log_message('debug','++ User +++ relation_to_child list +++ : record found');
			$status = $this->lang->line('rest_status_success');
			$message = $this->lang->line('record_found');
			$this->response(array("status"=>$status,'message'=>$message,"data"=>$preferenceList), 202);
		}
		else
		{
			log_message('debug','++ User +++ relation_to_child list +++ : Record not found');
			$errors['record_not_found'] = $this->lang->line('record_not_found');
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"ER","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}
	}
	
	function get_special_needs_post()
	{
		createFile('relation_to_child', print_r($_POST, true));
		log_message('debug','++ User +++ relation_to_child list +++ : API call successfully.');
		$preferenceList = $this->common_model->getSpecialNeeds();
	
		if($preferenceList)
		{
			log_message('debug','++ User +++ relation_to_child list +++ : record found');
			$status = $this->lang->line('rest_status_success');
			$message = $this->lang->line('record_found');
			$this->response(array("status"=>$status,'message'=>$message,"data"=>$preferenceList), 202);
		}
		else
		{
			log_message('debug','++ User +++ relation_to_child list +++ : Record not found');
			$errors['record_not_found'] = $this->lang->line('record_not_found');
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"ER","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}
	}
	
	function readallnoification_post()
	{
		createFile('readallnoification', print_r($_POST, true));
		log_message('debug','++ Common +++  Read All Notification   +++ : API call successfully.');
	
		$this->form_validation->set_rules('userid','User id','required|trim');
		$this->form_validation->set_rules('token','Token','required|trim');
	
		if($this->form_validation->run())
		{
			$userId=$this->input->post('userid');
			$token = $this->input->post('token');
			$checkToken = $this->common_model->check_valid_user_token($userId,$token);	// check user token
			if($checkToken)
			{
				log_message('debug','++ User +++  Read All Notification   +++ : No Form validation error');
				$noti = $this->common_model->readallnotification($userId);
	
				if($noti!== false ||$noti !== null )
				{
					log_message('debug','++ Common +++  Read All Notification   +++ : record found');
					$status = $this->lang->line('rest_status_success');
					$message = $this->lang->line('records_updated');
					$this->response(array("status"=>$status,'message'=>$message,"data"=>array("badge"=>$noti,"token_data"=>$token)), 202);
				}
				else
				{
					log_message('debug','++ Common +++  Read All Notification   +++ : Record not found');
					$errors['error_update'] = $this->lang->line('error_update').' notification count.';
					$status = $this->lang->line('rest_status_failed');
					$this->response(array("errorCode"=>"ER","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
				}
			}
			else
			{
				log_message('debug','++ user +++ Read All Notification  +++ : Token not matched.');
				$errors['invalid_token'] = $this->lang->line('invalid_token');
				$status = $this->lang->line('rest_status_failed');
				$this->response(array("errorCode"=>"ES2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
			}
		}
		else
		{
			log_message('debug','++ Common +++ Update Notification  +++  : Got Form validation error');
			$errors = $this->form_validation->_error_array;
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"FVF","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}
	}
}