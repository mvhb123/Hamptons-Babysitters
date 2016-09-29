<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Filename				:	job
* Classname				:	Job
* Description			:	Provide web services for jobs(Parent)
* Author				:	Anjali
* -------------------------------------------------------------------------------------------------------------------------------------------------
*/

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';


/**
 * Job Class
 *
 * Functions related to job
 * @category	Controller
 * @author		Anjali
 */

class Job extends REST_Controller {

	// Global array to pass with each view
	var $data = array();
	var $api_time = array();

	function __construct()
	{
		parent::__construct();
		$this->load->model('parent/job_model');
		$this->load->model('parent/user_model');
		
		$this->load->model('parent/package_model');
		$this->load->model('common_model');
		$this->load->library('authorize_net');
		$this->load->library('AuthorizeCimLib','','authorizeCimLib');
	}

	
	/**
	 * Job list
	 * 
	 * @access	public
	 * @param	integer user id
	 * @param	string token
	 * @param	string job_status
	 * @return	array of jobs
	 */
	function jobList_post()
	{
		createFile('jobList', print_r($_POST, true));
		log_message('debug','++ Job +++ Job List +++ : API call successfully.');
	
		$this->form_validation->set_rules('userid','User id','required|trim');
		$this->form_validation->set_rules('token','Token','required|trim');
		$userId=$this->input->post('userid');
		$userToken = $this->input->post('token');
		$job_status = $this->input->post('job_status');
		
		//check job status 
		if($job_status == 'pending')
		{
			$jstatus = array('pending','new');
		}
		else
		{	
			$jstatus = $job_status;
		}
	
		if ($this->form_validation->run() != FALSE)
		{
			log_message('debug','++ Job +++ Job List +++ : No form error');
			
			$checkToken = $this->common_model->check_valid_user_token($userId,$userToken);	// check user token
			if($checkToken)
			{
				$perPage='';$offset='';
				$pageNo = $this->uri->segment(4);
				if($pageNo!=0) //if page number not equal to 0 find paginated result
				{	
					if(!is_numeric($pageNo)&&!empty($pageNo))	// check page no is valid or not like enter invalid char abcd
					{
						log_message('debug','++ Job +++ Job List +++ : Invalid page no.');
						$errors['invalid_page'] = $this->lang->line('invalid_page');
						$status = $this->lang->line('rest_status_failed');
						$this->response(array("errorCode"=>"IP","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
					}	// end check page no
						
					if ($this->input->post('perPage') != "")
						$perPage = $this->input->post('perPage');
					else
						$perPage = $this->config->item('page_per');
						
					$start = !empty($pageNo) ? $pageNo : 1;
					$offset = ($start - 1) * $perPage;
						
					$jobList = $this->job_model->jobListNew($userId,$job_status,$perPage,$offset);
					
					$pagination = paginationCountData($jobList[0],$perPage,$pageNo); // common helper for get pagination data
					if($pagination == false)	// check page no is valid or not like page no is greater then page count
					{
						log_message('debug','++ Job +++ Job List +++ : Invalid page no.');
						$errors['invalid_page'] = $this->lang->line('invalid_page');
						$status = $this->lang->line('rest_status_failed');
						$this->response(array("errorCode"=>"IP","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
					}
				}
				else // fetch all jobs with given status
				{
					$pagination='';
					$jobList = $this->job_model->jobListNew($userId,$job_status);
				}
			
				$noti=$this->common_model->notification_count($userId);
				
				if($jobList[1])
				{
					log_message('debug','++ Job +++ Job List +++ : All data sent successfully');
					$status = $this->lang->line('rest_status_success');
					$message = $this->lang->line('record_found');
					$this->response(array("status"=>$status,'message'=>$message,"data"=>array("pagination" => $pagination,"token_data"=>$userToken, "jobList"=>$jobList[1], 'notification_count'=>$noti)), 202);
				}
				else
				{
					log_message('debug','++ Job +++ Job List +++ : No record found');
					$errors['record_not_found'] = $this->lang->line('job_not_found');
					$status = $this->lang->line('rest_status_failed');
					$this->response(array("errorCode"=>"FL2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status , "data"=>array('notification_count'=>$noti)), 203);
				}
			}
			else
			{
				log_message('debug','++ Job +++ Job List +++ : Token not matched.');
				$errors['invalid_token'] = $this->lang->line('invalid_token');
				$status = $this->lang->line('rest_status_failed');
				$this->response(array("errorCode"=>"ES2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
			}
		}
		else
		{
			log_message('debug','++ Job +++ Job List +++ : Got form validation error.');
			$errors = $this->form_validation->_error_array;
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"FVF","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}
	}
	
	
	/**
	 * Cancel Job
	 *
	 * @access	public
	 * @param	integer user id
	 * @param	string token
	 * @param	integer job_id
	 * @return	array
	 */
	function cancelJob_post()
	{
		createFile('cancelJob', print_r($_POST, true));
		log_message('debug','++ Job +++ Job List +++ : API call successfully.');
		
		$this->form_validation->set_rules('userid','User id','required|trim');
		$this->form_validation->set_rules('token','Token','required|trim');
		$this->form_validation->set_rules('job_id','Job id','required|trim');
		
		if ($this->form_validation->run() != FALSE)
		{
			log_message('debug','++ Job +++ Cancel job +++ : No form error');
			
			$userId=$this->input->post('userid');
			$userToken = $this->input->post('token');
			$job_id = $this->input->post('job_id');
			
			$checkToken = $this->common_model->check_valid_user_token($userId,$userToken);	// check user token
			if($checkToken)
			{
				$regResult = $this->job_model->deleteJobNew($job_id);
				if($regResult == 1)
				{
					log_message('debug','++ Job +++ Cancel job +++ : All data inserted successfully');
					
					$status = $this->lang->line('rest_status_success');
					$message = $this->lang->line('Job_cancel_parent');
					$this->response(array("status"=>$status,'message'=>$message,"data"=>array("token_data"=>$userToken)), 202);
				}
				else
				{
					log_message('debug','++ Job +++ Cancel job +++  : data not updated');
					$errors['job_not_cancelled'] = $regResult;
					if($regResult==$this->lang->line('job_not_cancelled'))
						$errors['admin_contact'] = $this->config->item('admin_contact');
					$status = $this->lang->line('rest_status_failed');
					$this->response(array("errorCode"=>"FR","errorMessage"=>$errors,"errorDisplayMessage"=>$errors['job_not_cancelled'],"status"=>$status), 203);
				}
			}
			else
			{
				log_message('debug','++ Job +++ Cancel job +++ : Token not matched.');
				$errors['invalid_token'] = $this->lang->line('invalid_token');
				$status = $this->lang->line('rest_status_failed');
				$this->response(array("errorCode"=>"ES2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
			}
		}
		else
		{
			log_message('debug','++ Job +++ Cancel job +++  : Got Form validation error');
			$errors = $this->form_validation->_error_array;
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"FVF","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}
	}
	
	
	
	/**
	 * Job Details
	 *
	 * @access	public
	 * @param	integer user id
	 * @param	string token
	 * @param	integer job_id
	 * @return	array containing job details
	 */
	function jobDetail_post()
	{
		createFile('jobDetail', print_r($_POST, true));
		log_message('debug','++ Job(sitter) +++ Job Details +++ : API call successfully.');
	
		$this->form_validation->set_rules('userid','User id','required|trim');
		$this->form_validation->set_rules('token','Token','required|trim');
		$this->form_validation->set_rules('job_id','Job id','required|trim');
	
		if ($this->form_validation->run() != FALSE)
		{
			log_message('debug','++ Job(sitter) +++ Job Details +++  : No form error');
	
			$userId=$this->input->post('userid');
			$userToken = $this->input->post('token');
			$job_id = $this->input->post('job_id');
	
			$checkToken = $this->common_model->check_valid_user_token($userId,$userToken);	// check user token
			if($checkToken)
			{
				$job_detail = $this->job_model->jobDetails1($job_id); //find job details
				if($job_detail )
				{
					log_message('debug','++ Job(sitter) +++ Job Details +++ : All data inserted successfully');
	
					$status = $this->lang->line('rest_status_success');
					$message = $this->lang->line('record_found');
					$this->response(array("status"=>$status,'message'=>$message,"data"=>array("token_data"=>$userToken,"jobDetails"=>$job_detail)), 202);
				}
				else
				{
					log_message('debug','++ Job(sitter) +++ Job Details +++ : data not updated');
					$errors['record_not_found'] = $this->lang->line('job_not_found');
					$status = $this->lang->line('rest_status_failed');
					$this->response(array("errorCode"=>"FR","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
				}
			}
			else
			{
				log_message('debug','++ Job(sitter) +++ Job Details +++ : Token not matched.');
				$errors['invalid_token'] = $this->lang->line('invalid_token');
				$status = $this->lang->line('rest_status_failed');
				$this->response(array("errorCode"=>"ES2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
			}
		}
		else
		{
			log_message('debug','++ Job(sitter) +++ Job Details +++ : Got Form validation error');
			$errors = $this->form_validation->_error_array;
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"FVF","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}
	}
	
	
	/**
	 * Booking fee
	 *
	 * @access	public
	 * @param	integer user id
	 * @param	string token
	 * @param	string job_start_date 
	 * @param	string job_end_date
	 * @param	integer child_count
	 * @return	array containing fee summary 
	 */
	function calculateBookingFee_post()
	{
		createFile('calculateBookingFee', print_r($_POST, true));
		log_message('debug','++ Job +++ Booking fee +++ : API call successfully.');
	
		$this->form_validation->set_rules('userid','User id','required|trim');
		$this->form_validation->set_rules('token','Token','required|trim');
		$this->form_validation->set_rules('job_start_date','Start date/time','required|trim|compareCurrentDate');
		$this->form_validation->set_rules('job_end_date','End date','required|min_3_hr_difference|trim|compareDate');
		$this->form_validation->set_rules('child_count','Child count','required|trim');
		$this->form_validation->set_rules('current_time','current date time','trim');
		
		if ($this->form_validation->run() != FALSE)
		{
			log_message('debug','++ Job +++ Booking fee +++ : No form error');
				
			$userId=$this->input->post('userid');
			$userToken = $this->input->post('token');
			
			$checkToken = $this->common_model->check_valid_user_token($userId,$userToken);	// check user token
			if($checkToken)
			{
				$rate = 30;
				$current_date= date('Y-m-d H:i:s');
				$start_date = $this->input->post('job_start_date');
				$end_date = $this->input->post('job_end_date');
				$current_date=$this->input->post('current_time');
				$child_count = $this->input->post('child_count');
				
				$client_rate=$this->common_model->getRate($child_count);
				if($rate)
				{
					$rate=$client_rate->client_rate;
				}
				
				$credits = $this->user_model->check_credits($userId);
			
				$days =floor((strtotime(date('Y-m-d',strtotime($end_date)))-strtotime(date('Y-m-d',strtotime($start_date))))/(24*60*60));
				$days++;
				
				$hours = (strtotime(date('H:i:s',strtotime($end_date)))-strtotime(date('H:i:s',strtotime($start_date))))/(60*60);
				if($hours<0)
				{
					$days = $days-1;
					$hours = ($hours+24)* ($days);
				}
				else
				{
					$hours = $hours*$days;
				}
				
				/* $booking_fee = $hours * $rate;
				$booking_fee = "$".round($booking_fee,2)." (".round($hours,2)." hours @ $".$rate ."/hr)";
				
				$diff = strtotime($start_date)- strtotime($current_date);
				$diff_in_hrs = $diff/3600;
				$amount=0;
				if($diff_in_hrs<3 && $credits==0)
				{
					$amount = $this->config->item('immediate_job_price');
				}
				
				if($credits==false && $amount!=0)
				{
					$a = (25*$days) + $amount;
					//$credits_used = $days." Credits ($".$a.")";
					$credits_used = "$".$a;
					//$credits_used = $days." Credits($". 25*$days+$amount." for immediate booking )";
				}
				else 
				{
					//$credits_used = $days." Credits ($". 25*$days.")";
					$credits_used = "$". 25*$days;
				} */
				
				// changes as per client feedback
				$booking_fee = $hours * $rate; // $booking_fee for babysitting charge
				$total_charged=$booking_fee;
				$booking_fee = "$".round($booking_fee,2)." (".round($hours,2)." hours @ $".$rate ."/hr)";
				
				$diff = strtotime($start_date)- strtotime($current_date);
				$diff_in_hrs = $diff/3600;
				$amount=0;
				if($diff_in_hrs<3 && $credits==0)
				{
					$amount = $this->config->item('immediate_job_price');
				}
				
				if($credits==false ||$credits==0)
				{
					$a = (25*$days) + $amount;
					if($amount!=0)
						$credits_used = "$".$a." (".$days." Booking credits +$".$amount." for immediate booking )";
					else
						$credits_used = "$".$a." (".$days." Booking credits.)";
					$remaining_credit=0;
					$total_charged = $total_charged +$a;
				}
				else
				{
					if($credits>=$days)
					{
						$credits_used = $days." Credits Used."; // as per client feedback.
						$remaining_credit=$credits-$days;
					}
					else
					{
						$additional=$days-$credits;
						$credits_used = $credits." Credits + $".$additional*25;
						$remaining_credit = 0;
						$total_charged = $total_charged + $additional*25;
					}
				
				}
				// end changes as per client feedback
				
				log_message('debug','++ Job +++ Booking fee +++ : Data found');		 
				$status = $this->lang->line('rest_status_success');
				$message = $this->lang->line('record_found');
				$this->response(array("status"=>$status,'message'=>$message,"data"=>array("token_data"=>$userToken,"bookingFee"=>$booking_fee,"creditsUsed"=>$credits_used , "available_credits"=> $credits,"remaining_credits"=>$remaining_credit, "required_credits"=>$days, "total_charged"=>"$".$total_charged)), 202);
			}
			else
			{
				log_message('debug','++ Job +++ Booking fee +++ : Token not matched.');
				$errors['invalid_token'] = $this->lang->line('invalid_token');
				$status = $this->lang->line('rest_status_failed');
				$this->response(array("errorCode"=>"ES2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
			}
		}
		else
		{
			log_message('debug','++ Job +++ Booking fee +++  : Got Form validation error');
			$errors = $this->form_validation->_error_array;
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"FVF","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}
	}
	
	
	/**
	 * add edit job
	 *
	 * @access	public
	 * @param	integer user id
	 * @param	string token
	 * @param	string comma separated child id
	 * @param	string job_start_date
	 * @param	string job_end_date
	 * @param	string address_id or json string of address
	 * @param	string notes
	 * @param	string preferences comma separated preferences
	 * @param	string card_info required if credits not available and payment profile id not given
	 * @param	string name_on_card required if credits not available and payment profile id not given
	 * @param	string job_status optional
	 * @param	integer job_id optional
	 * @return	array of job details
	 */
	function addEditJob_post()
	{
		createFile('addJob', print_r($_POST, true));
		log_message('debug','++ Job +++ Add Edit job +++ : API call successfully.');
		$userId=$this->input->post('userid');
		$token = $this->input->post('token');
	
		$this->form_validation->set_rules(addJob_rules());
		
		if($this->input->post('is_special')==1||$this->input->post('is_special')=='1')
		{
			$this->form_validation->set_rules('special_need', 'Special need description', 'required');
		}
	
		if($this->input->post('address_id')=='')
		{
			if( $this->input->post('alternate_address') != '')
			{
				$this->form_validation->set_rules('alternate_address', 'address', 'address_check');
			}
			else
			{
				log_message('debug','++ Job +++ Add Edit Job +++ : form error');
				$errors['address_id'] = 'Address is required.';
				$status = $this->lang->line('rest_status_failed');
				$this->response(array("errorCode"=>"FL2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
			}
		}
		else
		{
			$address_id= $this->input->post('address_id');
		}
	
		if($this->input->post('job_start_date')!=''&&$this->input->post('job_end_date')!='')
		{
			$purchase_credit=0;
			$start_date=$this->input->post('job_start_date');
			$end_date=$this->input->post('job_end_date');
				
			$days =floor((strtotime(date('Y-m-d',strtotime($end_date)))-strtotime(date('y-m-d',strtotime($start_date))))/(24*60*60));
			$required_credits = $days;
			$required_credits++;
			
			if(date('H:i:s',strtotime($end_date))<date('H:i:s',strtotime($start_date)))
			{
				$required_credits++;
			}
				
			$sql= "select sum(slots) avail_credits from clients_subscription where end_date > now() and userid=".$userId." and slots > 0 order by client_sub_id asc limit 0,1";
			$res = $this->db->query($sql);
			$res = $res->result();
				
			if($res[0]->avail_credits<$required_credits)
			{
				if( $this->input->post('job_id')=='')
				{
					//$this->form_validation->set_rules('package_id', 'Package id', 'required');
					$purchase_credit=$required_credits-$res[0]->avail_credits;
					if($this->input->post('authorizenet_payment_profile_id')=='')
						$this->form_validation->set_rules(addcard_rules());
				}
			}
			/* else
			 {
			$res=$res->result_array();
			$sub_id=$res[0]['client_sub_id'];
			}
			*/
		}
			
		if($this->form_validation->run())
		{
			if( $this->input->post('alternate_address') != '')
			{
				$loc_address= json_decode($this->input->post('alternate_address'),true);

				$array1=array('billing_name'=>@$loc_address['billing_name']
						,'address_1'=>@$loc_address['address_1']
						,'streat_address'=>@$loc_address['streat_address']
						,'zipcode'=>@$loc_address['zipcode']
						,'city'=>@$loc_address['city']
						,'state'=>@$loc_address['state_id']
						,'country'=>@$loc_address['country']
						,'address_type'=>@$loc_address['address_type']);

				$array1['userid'] = $userId;
				$this->db->insert('address',$array1);
				$address_id = $this->db->insert_id();
			}

			$checkToken = $this->common_model->check_valid_user_token($userId,$token);	// check user token
			if($checkToken)
			{
				log_message('debug','++ Job +++ Add Edit Job +++ : No Form validation error');
				$child_id = explode(',',$this->input->post('child_id'));
				/* 	$start =strtotime($start_date);
				 	
				$end_date1 =date('Y-m-d',$start);
				$end_time = date('H:i:s',strtotime($end_date));
					
				$end = strtotime($end_date1.' '.$end_time); */
				$Job_data = array(
						'children'				=> $child_id,
						'sitter_id'				=> $this->input->post('sitter_user_id')?$this->input->post('sitter_user_id'):'',
						'address_id' 			=> $address_id,
						'userid'				=> $userId,
						'start_date'			=> $start_date,
						'end_date'				=> $end_date,
						'child_count'			=> count($child_id),
						'notes'					=> $this->input->post('notes')?$this->input->post('notes'):'',
						'is_special'			=> $this->input->post('is_special'),
						'special_need'			=> $this->input->post('special_need')?$this->input->post('special_need'):'',
						'prefer'				=> explode(',',$this->input->post('preferences')),
						'job_status'		    => ($this->input->post('job_status'))?$this->input->post('job_status'):'new',
				);
				$job_id = $this->input->post('job_id') ;

				if($this->input->post('preferences')&& $this->input->post('preferences')!==null)
				{
					$this->db->select('prefer_name');
					$this->db->join('preference_group','preference_group.group_id=preference_master.group_id');
					$this->db->where('preference_group.group_id',5);
					$this->db->where_in('prefer_id',$Job_data['prefer']);
					$prefer_name=$this->db->get('preference_master');
					if($prefer_name->num_rows>0)
					{
						$Job_data['is_special'] = '1'; 
					}
				}
				
				/*
				$this->db->select('child_id');
				$this->db->where_in('child_id',$Job_data['children']);
				$this->db->where('special_needs_status','Yes');
				$special_child = $this->db->get('children');
				if($special_child->num_rows>0)
				{
					$Job_data['is_special'] = '1';
				}  */
				
				if($job_id != '') //if job id is given edit job.
				{
					$regResult = $this->job_model->editJob($Job_data,$job_id);
					log_message('debug','++ Job +++ Add Edit Job +++ : All data inserted successfully');
					$status = $this->lang->line('rest_status_success');
					$message = $regResult[0];
					$this->response(array("status"=>$status,'message'=>$message,"data"=>array("job_detail" =>$regResult[1],"token_data"=>$token)), 202);
				}
				else
				{
						
					$regResult = $this->job_model->addJob($userId,$Job_data,$required_credits,$res[0]->avail_credits);
					if($regResult[1])
					{
						log_message('debug','++ Job +++ Add Edit Job +++ : All data inserted successfully');
						$status = $this->lang->line('rest_status_success');
						$message = $regResult[0];
						$this->response(array("status"=>$status,'message'=>$message,"data"=>array("job_detail" =>$regResult[1],"token_data"=>$token)), 202);
					}
					else
					{
						log_message('debug','++ Job +++ Add Edit Job +++  : data not updated');
						$errors['job_not_added'] = $regResult[0];
						$status = $this->lang->line('rest_status_failed');
						$this->response(array("errorCode"=>"FR","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
					}
				}
			}
			else
			{
				log_message('debug','++ Job +++ Add Edit Job +++ : Token not matched.');
				$errors['invalid_token'] = $this->lang->line('invalid_token');
				$status = $this->lang->line('rest_status_failed');
				$this->response(array("errorCode"=>"ES2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
			}
		}
		else
		{
			log_message('debug','++ Job +++ Add Edit Job +++  : Got Form validation error');
			$errors = $this->form_validation->_error_array;
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"FVF","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}
	}
	
	function assign_sitters_post()
	{
		createFile('assign_sitter', print_r($_POST, true));
		$job_id = $_POST['job_id'];
		$userid = $_POST['userid'];
		$jobno = $_POST['jobno'];
		$this->job_model->assign_sitters($job_id,$userid,$jobno);return $job_id;
	}
}
?>