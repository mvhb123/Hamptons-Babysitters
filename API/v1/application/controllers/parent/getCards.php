<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Filename				:	user
* Classname				:	User
* Description			:	Provide web services for user
* Create Date			:	20th Apr 2015
* Author				:	Anjali
* -------------------------------------------------------------------------------------------------------------------------------------------------
*/

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';

/**
 * GetCards Class
 *
 * Find Saved cards
 * @category	Controller
 * @author		Anjali
 */
class GetCards extends REST_Controller 
{
	// Global array to pass with each view
	var $data = array();
	var $api_time = array();

	function __construct()
	{
		parent::__construct();
		$this->load->model('parent/saved_card_model');
		$this->load->model('common_model');
	}
	
	
	/**
	 * Find saved cards
	 *
	 * @access	public
	 * @param	string user id
	 * @param	string token
	 * @return	array of payment profile id and name on card
	 */
	function index_post()
	{
		createFile('GetCards', print_r($_POST, true));
		log_message('debug','++ Get cards+++ Saved card List +++ : API call successfully.');
	
		$this->form_validation->set_rules('userid','User id','required|trim');
		$this->form_validation->set_rules('token','Token','required|trim');
		$userId=$this->input->post('userid');
		$userToken = $this->input->post('token');
	
		if ($this->form_validation->run() != FALSE)
		{
			log_message('debug','++ Get cards +++ Saved card List +++ : No form error');
	
			$checkToken = $this->common_model->check_valid_user_token($userId,$userToken);	// check user token
			if($checkToken)
			{
				$savedCards = $this->saved_card_model->getSavedCard($userId);
				if($savedCards)
				{
					log_message('debug','++ Get cards +++ Saved card List +++ : All data sent successfully');
					$status = $this->lang->line('rest_status_success');
					$message = $this->lang->line('record_found');
					$this->response(array("status"=>$status,'message'=>$message,"data"=>array("token_data"=>$userToken, "cardsList"=>$savedCards)), 202);
				}
				else
				{
					log_message('debug','++ Get cards +++ Saved card List +++ : No record found');
					$errors['record_not_found'] = $this->lang->line('record_not_found');
					$status = $this->lang->line('rest_status_failed');
					$this->response(array("errorCode"=>"FL2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
				}
			}
			else
			{
				log_message('debug','++ Get cards +++ Saved card List +++ : Token not matched.');
				$errors['invalid_token'] = $this->lang->line('invalid_token');
				$status = $this->lang->line('rest_status_failed');
				$this->response(array("errorCode"=>"ES2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
			}
		}
		else
		{
			log_message('debug','++ Get cards +++ Saved card List +++ : Got form validation error.');
			$errors = $this->form_validation->_error_array;
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"FVF","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}
	}
}
	
