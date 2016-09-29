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

class Package extends REST_Controller {

	// Global array to pass with each view
	var $data = array();
	var $api_time = array();

	function __construct()
	{
		parent::__construct();
		$this->load->model('parent/package_model');
		$this->load->model('parent/user_model');
		$this->load->model('common_model');
		$this->load->library('authorize_net');
		$this->load->library('AuthorizeCimLib','','authorizeCimLib');
	}
	
	
	/**
	 * Package List
	 *
	 * @access	public
	 * @param	string user id
	 * @param	string token
	 * @param	string job_start_date (dd mmm yyyy h:i A)
	 * @return	array containing package list
	 */
	function index_post()
	{
		createFile('packageList', print_r($_POST, true));
		log_message('debug','++ Package +++ Package List +++ : API call successfully.');
		
		$this->form_validation->set_rules('userid','User id','required|trim');
		$this->form_validation->set_rules('token','Token','required|trim');
		if($this->input->post('job_start_date')=='')
			$this->form_validation->set_rules('job_start_date','Job start date/time', 'trim|compareCurrentDate');
		$userId=$this->input->post('userid');
		$userToken = $this->input->post('token');
		
		if ($this->form_validation->run() != FALSE)
		{
			log_message('debug','++ Package +++ Package List +++ : No form error');
				
			$checkToken = $this->common_model->check_valid_user_token($userId,$userToken);	// check user token
			if($checkToken)
			{
				$credits = $this->user_model->check_credits($userId);
				$pack = array();
				$start_date = $this->input->post('job_start_date');
				/*check for immidiate job
				 *  if($start_date!='')
				{
					$start = strtotime($start_date.' '.getTimeZoneAbbreviation());
					$diff = $start - strtotime(date('Y-m-d H:i:s'));
					$diff_in_hrs = $diff/3600;
					if($diff_in_hrs<3)
					{
						$packageData = array( 0 => Array
												(
												'package_id' => '1',
												'credits' => '1',
												'price' =>"".$this->config->item('immediate_credit_price'),
												'package_name' => 'One Credit',
												'ordering' => '1'
												)
									  		);
					}
					else
					{
						$packageData = $this->package_model->packageList();
					}
				}
				else
				{
					$packageData = $this->package_model->packageList();
				} */
				
				$packageData = $this->package_model->packageList();
				if($packageData)
				{
					log_message('debug','++ Package +++ Package List +++ : All data sent successfully');
					$status = $this->lang->line('rest_status_success');
					$message = $this->lang->line('record_found');
					$this->response(array("status"=>$status,'message'=>$message,"data"=>array("token_data"=>$userToken, "packageData"=>$packageData, "available_credits"=>$credits)), 202);
				}
				else
				{
					log_message('debug','++ Package +++ Package List +++ : No record found');
					$errors['record_not_found'] = $this->lang->line('package_not_found');
					$status = $this->lang->line('rest_status_failed');
					$this->response(array("errorCode"=>"FL2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
				}
			}
			else
			{
				log_message('debug','++ Package +++ Package List +++ : Token not matched.');
				$errors['invalid_token'] = $this->lang->line('invalid_token');
				$status = $this->lang->line('rest_status_failed');
				$this->response(array("errorCode"=>"ES2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
			}
		}
		else
		{
			log_message('debug','++ Package +++ Package List +++ : Got form validation error.');
			$errors = $this->form_validation->_error_array;
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"FVF","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}
	}
	
	
	/**
	 * Add edit credit card
	 *
	 * @access	public
	 * @param	integer user id
	 * @param	string token
	 * @param	string card info
	 * @return	array containing card details
	 */
	function addEditCard_post()
	{
		createFile('addEditCard', print_r($_POST, true));
		$this->form_validation->set_rules(addcard_rules());
		if ($this->form_validation->run() != FALSE)
		{
			log_message('debug','++ Package +++ Package List +++ : No form error');
			$userId=$this->input->post('userid');
			$userToken = $this->input->post('token');
			$checkToken = $this->common_model->check_valid_user_token($userId,$userToken);	// check user token
			if($checkToken)
			{	
				$k= array('card_num'=>'','exp_date'=>'','card_code'=>'');
				$k = getCCInfo($this->input->post('card_info'));
				$card_data = array( 
									'user_id' =>$userId,
									'card_number' => $k['card_num'],
									'expiry_date' =>$k['exp_date'],
									'cvv'	=> $k['card_code'],
									'address'=>$this->input->post('streat_address'),
									'city'=>$this->input->post('city'),
									'state'=>$this->input->post('state'),
									'zipcode'=>$this->input->post('zipcode'),
									'name_on_card'=>$this->input->post('name_on_card'),
									'country'=>$this->input->post('country'),
						);
				$card_result = $this->common_model->addEditCard($card_data);
				if($card_result[0])
				{
					log_message('debug','++ Package +++ Add edit card +++ : All data sent successfully');
					$status = $this->lang->line('rest_status_success');
					$message = $this->lang->line('card_saved');
					$this->response(array("status"=>$status,'message'=>$message,"data"=>array("token_data"=>$userToken,'card_detail'=>$card_result[0])), 202);
				}
				else 
				{
					log_message('debug','++ Package +++ Add edit card +++ : Transaction failed');
					$errors['transaction_failed'] = $card_result[1];
					$status = $this->lang->line('rest_status_failed');
					$this->response(array("errorCode"=>"FL2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
				}
			}
			else
			{
				log_message('debug','++ Package +++ Add edit card +++ : Token not matched.');
				$errors['invalid_token'] = $this->lang->line('invalid_token');
				$status = $this->lang->line('rest_status_failed');
				$this->response(array("errorCode"=>"ES2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
			}
		}
		else
		{
			log_message('debug','++ Package +++ Add edit card +++ : Got form validation error.');
			$errors = $this->form_validation->_error_array;
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"FVF","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}
	}
	
	
	/**
	 * Buy booking credits
	 *
	 * @access	public
	 * @param	integer user id
	 * @param	string token
	 * @param	integer package_id
	 * @param	string authorizenet_payment_profile_id optional
	 * @param	string card info optional
	 * @return	array containing package details
	 */
	function buyPackage_post()
	{
		createFile('buyPackage', print_r($_POST, true));
		log_message('debug','++ Package +++ Buy Package +++ : API call successfully.');
		$this->form_validation->set_rules(buyPackage_rules());
	
		if($this->input->post('authorizenet_payment_profile_id')=='')
		{
			$this->form_validation->set_rules(addcard_rules());
		}
		
		if ($this->form_validation->run() != FALSE)
		{
			log_message('debug','++ Package +++ Buy Package +++ : No form error');
			$userId=$this->input->post('userid');
			$userToken = $this->input->post('token');
			
			$checkToken = $this->common_model->check_valid_user_token($userId,$userToken);	// check user token
			if($checkToken)
			{
				$pack = $this->package_model->package_details($this->input->post('package_id'));
				
				if($pack)
				{	
					$k= array('card_num'=>'','exp_date'=>'','card_code'=>'');
					if($this->input->post('authorizenet_payment_profile_id')=='')
					{
						$k = getCCInfo($this->input->post('card_info'));
					}
					$payment_data = array(
							'user_id' =>$userId,
							'price' => $pack->price,
							'card_number' => $k['card_num'],
							'expiry_date' =>$k['exp_date'],
							'cvv'	=> $k['card_code'],
							'address'=>$this->input->post('streat_address'),
							'city'=>$this->input->post('city'),
							'state'=>$this->input->post('state'),
							'zipcode'=>$this->input->post('zipcode'),
							'country'=>$this->input->post('country'),
							'name_on_card'=>$this->input->post('name_on_card'),
							'save_card'=>$this->input->post('save_card'),
							'payment_profile_id'=>($this->input->post('authorizenet_payment_profile_id')!='')?base64_decode($this->input->post('authorizenet_payment_profile_id')):''
					);
					
					$transId = $this->common_model->processPayment($payment_data);
					if($transId[0]!=false ||$transId[0]!=null)
					{
						$data= array(
								'userid'				=> $userId,
								'slots'					=> $pack->credits,
								'total_credits'			=> $pack->credits,
								'price'					=> $pack->price,
								'start_date'			=> date('Y-m-d H:i:s',strtotime('+1 day 9am')),
								'end_date'				=> date('Y-m-d H:i:s',strtotime('+1 year 6pm')),
								'last_modified_by'		=> $userId,
								'last_modified_date'	=> date('Y-m-d H:i:s'),
								'package_id'			=> $pack->package_id,
								'transaction_id'		=> $transId[0],
								'payment_gateway'		=> 'Authorize.net',
								'notes'					=> $pack->package_name
								);
						
						$packageData = $this->package_model->buyPackage($data);
						if($packageData!=false)
						{
							log_message('debug','++ Package +++ Buy Package +++ : All data sent successfully');
							$status = $this->lang->line('rest_status_success');
							$message = $this->lang->line('credit_parchased');
							$this->response(array("status"=>$status,'message'=>$message,"data"=>array("token_data"=>$userToken, "packageData"=>$packageData)), 202);
						}
						else 
						{
							log_message('debug','++ Package +++ Buy Package +++ : No record found');
							$errors['record_not_found'] = $this->lang->line('package_not_found');
							$status = $this->lang->line('rest_status_failed');
							$this->response(array("errorCode"=>"FL2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
						}
					}
					else
					{
						log_message('debug','++ Package +++ Buy Package +++ : Transaction failed');
						$errors['transaction_failed'] = $transId[1];
						$status = $this->lang->line('rest_status_failed');
						$this->response(array("errorCode"=>"FL2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
					}
				}
				else
				{
					log_message('debug','++ Package +++ Buy Package +++ : Invaild package selected');
					$errors['invalid_package'] = $this->lang->line('invalid_package');
					$status = $this->lang->line('rest_status_failed');
					$this->response(array("errorCode"=>"FL2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
				}
			}
			else
			{
				log_message('debug','++ Package +++ Buy Package +++ : Token not matched.');
				$errors['invalid_token'] = $this->lang->line('invalid_token');
				$status = $this->lang->line('rest_status_failed');
				$this->response(array("errorCode"=>"ES2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
			}
		}
		else
		{
			log_message('debug','++ Package +++ Buy Package +++ : Got form validation error.');
			$errors = $this->form_validation->_error_array;
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"FVF","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}
	}
	
	
	function availableCredits_post()
	{
		createFile('availableCredits', print_r($_POST, true));
		log_message('debug','++ Package +++ Available Package List +++ : API call successfully.');
	
		$this->form_validation->set_rules('userid','User id','required|trim');
		$this->form_validation->set_rules('token','Token','required|trim');
		
		$userId=$this->input->post('userid');
		$userToken = $this->input->post('token');
	
		if ($this->form_validation->run() != FALSE)
		{
			log_message('debug','++ Package +++ Available Package List +++ : No form error');
	
			$checkToken = $this->common_model->check_valid_user_token($userId,$userToken);	// check user token
			if($checkToken)
			{
				$pack = array();
				
				$packageData = $this->package_model->availableCredits($userId);
	
				if($packageData)
				{
					log_message('debug','++ Package +++ Available Package List +++ : All data sent successfully');
					$status = $this->lang->line('rest_status_success');
					$message = $this->lang->line('record_found');
					$this->response(array("status"=>$status,'message'=>$message,"data"=>array("token_data"=>$userToken, "packageData"=>$packageData['subscription'],"available_credits"=>$packageData['total_available_credits'])), 202);
				}
				else
				{
					log_message('debug','++ Package +++ Available Package List +++ : No record found');
					$errors['record_not_found'] = $this->lang->line('package_not_found');
					$status = $this->lang->line('rest_status_failed');
					$this->response(array("errorCode"=>"FL2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
				}
			}
			else
			{
				log_message('debug','++ Package +++ Available Package List +++ : Token not matched.');
				$errors['invalid_token'] = $this->lang->line('invalid_token');
				$status = $this->lang->line('rest_status_failed');
				$this->response(array("errorCode"=>"ES2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
			}
		}
		else
		{
			log_message('debug','++ Package +++ Available Package List +++ : Got form validation error.');
			$errors = $this->form_validation->_error_array;
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"FVF","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}
	}
}
