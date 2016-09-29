<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Filename				:	job
* Classname				:	Job
* Description			:	Provide web services related to job (sitter)
* Author				:	Anjali
* -------------------------------------------------------------------------------------------------------------------------------------------------
*/

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';

class Job extends REST_Controller 
{
	// Global array to pass with each view
	var $data = array();
	var $api_time = array();

	function __construct()
	{
		parent::__construct();
		$this->load->model('sitter/job_model');
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
		createFile('jobList(sitter)', print_r($_POST, true));
		log_message('debug','++ Job(sitter) +++ Job List +++ : API call successfully.');
		
		$this->form_validation->set_rules('userid','User id','required|trim');
		$this->form_validation->set_rules('token','Token','required|trim');
		$userId=$this->input->post('userid');
		$userToken = $this->input->post('token');
		
		$job_status = $this->input->post('job_status');
		
		if ($this->form_validation->run() != FALSE)
		{
			log_message('debug','++ Job(sitter) +++ Job List +++ : No form error');
				
			$checkToken = $this->common_model->check_valid_user_token($userId,$userToken);	// check user token
			if($checkToken)
			{
				$this->db->select('userid');
				$this->db->where('userid',$userId);
				$this->db->where('status','unapproved');
				$profile_status=$this->db->get('users');
				if($profile_status->num_rows()>0)
				{
					log_message('debug','++ Job(sitter) +++ Job List +++ : Profile is not activated');
					$errors['profile_not_approved'] = $this->lang->line('profile_not_approved');
					$status = $this->lang->line('rest_status_failed');
					$this->response(array("errorCode"=>"FL2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
				}
				
				$perPage='';$offset='';
				$pageNo = $this->uri->segment(4);
				if($pageNo!=0)
				{	
					if(!is_numeric($pageNo)&&!empty($pageNo))	// check page no is valid or not like enter invalid char abcd
					{
						log_message('debug','++ Job(sitter) +++ Job List +++ : Invalid page no.');
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
						log_message('debug','++ Job(sitter) +++ Job List +++ : Invalid page no.');
						$errors['invalid_page'] = $this->lang->line('invalid_page');
						$status = $this->lang->line('rest_status_failed');
						$this->response(array("errorCode"=>"IP","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
					}
				}
				else 
				{
					$pagination='';
					$jobList = $this->job_model->jobListNew($userId,$job_status);				
				}

				$noti=$this->common_model->notification_count($userId);
				
				if(array_key_exists(1, $jobList))
				{
					$total_owed = "";$total_earned = "";$total_paid='';
					if(array_key_exists(2, $jobList))
					{
						$total_owed = $jobList[2];//$jobList[2] = owed
					}
					if(array_key_exists(3, $jobList))
					{
						$total_paid =  $jobList[3];//."(".date('Y').")";//$jobList[3] = paid
					}
					if(array_key_exists(4, $jobList))
					{
						$total_earned =  $jobList[4];//."(".date('Y').")";//$jobList[3] = earned
					}
					
					
					log_message('debug','++ Job(sitter) +++ Job List +++ : All data sent successfully');
					$status = $this->lang->line('rest_status_success');
					$message = $this->lang->line('record_found');
					$this->response(array("status"=>$status,'message'=>$message,"data"=>array("pagination" => $pagination,"token_data"=>$userToken,'notification_count'=>$noti, "jobList"=>$jobList[1],"total_paid"=>$total_paid,"total_owed"=>$total_owed,"total_earned"=>$total_earned)), 202);
				}
				else
				{
					log_message('debug','++ Job(sitter) +++ Job List +++ : No record found');
					$errors['record_not_found'] = $this->lang->line('job_not_found');
					$status = $this->lang->line('rest_status_failed');
					$this->response(array("errorCode"=>"FL2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status,  "data"=>array('notification_count'=>$noti)), 203);
				}
			}
			else
			{
				log_message('debug','++ Job(sitter) +++ Job List +++ : Token not matched.');
				$errors['invalid_token'] = $this->lang->line('invalid_token');
				$status = $this->lang->line('rest_status_failed');
				$this->response(array("errorCode"=>"ES2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
			}
		}
		else
		{
			log_message('debug','++ Job(sitter) +++ Job List +++ : Got form validation error.');
			$errors = $this->form_validation->_error_array;
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"FVF","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}
	}
	
	
	/**
	 * Cancel Job
	 *
	 * @access	public
	 * @param	string user id
	 * @param	string token
	 * @param	string job_id
	 * @return	array
	 */
	function cancelJob_post()
	{
		createFile('cancelJob', print_r($_POST, true));
		log_message('debug','++ Job(sitter) +++ Cancel Job +++ : API call successfully.');
	
		$this->form_validation->set_rules('userid','User id','required|trim');
		$this->form_validation->set_rules('token','Token','required|trim');
		$this->form_validation->set_rules('job_id','Job id','required|trim');
	
		if ($this->form_validation->run() != FALSE)
		{
			log_message('debug','++ Job(sitter) +++ Cancel Job +++  : No form error');
				
			$userId=$this->input->post('userid');
			$userToken = $this->input->post('token');
			$job_id = $this->input->post('job_id');
				
			$checkToken = $this->common_model->check_valid_user_token($userId,$userToken);	// check user token
			if($checkToken)
			{
				$job_cancelled = $this->job_model->cancelConfirm($job_id);
				if($job_cancelled )
				{
					log_message('debug','++ Job(sitter) +++ Cancel Job +++ : All data inserted successfully');
						
					$status = $this->lang->line('rest_status_success');
					$message = $this->lang->line('Job_cancel');
					$this->response(array("status"=>$status,'message'=>$message,"data"=>array("token_data"=>$userToken)), 202);
				}
				else
				{
					log_message('debug','++ Job(sitter) +++ Cancel Job +++ : data not updated');
					$errors['job_not_cancelled'] = $this->lang->line('job_not_cancelled');
					$errors['admin_contact'] = $this->config->item('admin_contact');
					$status = $this->lang->line('rest_status_failed');
					$this->response(array("errorCode"=>"FR","errorMessage"=>$errors,"errorDisplayMessage"=>$errors['job_not_cancelled'],"status"=>$status), 203);
				}
			}
			else
			{
				log_message('debug','++ Job(sitter) +++ Cancel Job +++ : Token not matched.');
				$errors['invalid_token'] = $this->lang->line('invalid_token');
				$status = $this->lang->line('rest_status_failed');
				$this->response(array("errorCode"=>"ES2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
			}
		}
		else
		{
			log_message('debug','++ Job(sitter) +++ Cancel Job +++ : Got Form validation error');
			$errors = $this->form_validation->_error_array;
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"FVF","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}
	}
	
	
	/**
	 * Confirm Job
	 *
	 * @access	public
	 * @param	string user id
	 * @param	string token
	 * @param	string job_id
	 * @return	array
	 */
	function confirmJob_post()
	{
		createFile('confirmJob', print_r($_POST, true));
		log_message('debug','++ Job(sitter) +++ Confirm Job +++ : API call successfully.');
	
		$this->form_validation->set_rules('userid','User id','required|trim');
		$this->form_validation->set_rules('token','Token','required|trim');
		$this->form_validation->set_rules('job_id','Job id','required|trim');
	
		if ($this->form_validation->run() != FALSE)
		{
			log_message('debug','++ Job(sitter) +++ Confirm Job +++  : No form error');
	
			$userId=$this->input->post('userid');
			$userToken = $this->input->post('token');
			$job_id = $this->input->post('job_id');
	
			$checkToken = $this->common_model->check_valid_user_token($userId,$userToken);	// check user token
			if($checkToken)
			{
				$job_confirmed = $this->job_model->confirmJob($job_id,$userId);
				if($job_confirmed===TRUE)
				{
					log_message('debug','++ Job(sitter) +++ Confirm Job +++ : All data inserted successfully');
	
					$status = $this->lang->line('rest_status_success');
					$message = $this->lang->line('job_confirmed');
					$this->response(array("status"=>$status,'message'=>$message,"data"=>array("token_data"=>$userToken)), 202);
				}
				else
				{
					log_message('debug','++ Job(sitter) +++ Confirm Job +++ : data not updated');
					$errors['job_not_confirmed'] =$job_confirmed;
					$status = $this->lang->line('rest_status_failed');
					$this->response(array("errorCode"=>"FR","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
				}
			}
			else
			{
				log_message('debug','++ Job(sitter) +++ Confirm Job +++ : Token not matched.');
				$errors['invalid_token'] = $this->lang->line('invalid_token');
				$status = $this->lang->line('rest_status_failed');
				$this->response(array("errorCode"=>"ES2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
			}
		}
		else
		{
			log_message('debug','++ Job(sitter) +++ Confirm Job +++  : Got Form validation error');
			$errors = $this->form_validation->_error_array;
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"FVF","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}
	}
	
	
	/**
	 * Job Details
	 *
	 * @access	public
	 * @param	string user id
	 * @param	string token
	 * @param	string job_id
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
				$job_detail = $this->job_model->jobDetails($job_id);
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
	 * Complete Job
	 *
	 * @access	public
	 * @param	string user id
	 * @param	string token
	 * @param	string job_id
	 * @param	string actual_start_date
	 * @param	string actual_end_date
	 * @param	string actual_child_count
	 */
	function complete_job_post()
	{
		createFile('complete_job', print_r($_POST, true));
		log_message('debug','++ Job(sitter) +++ Complete Job +++ : API call successfully.');
		$job_id=$this->input->post('job_id');
		$this->form_validation->set_rules('userid','User id','required|trim');
		$this->form_validation->set_rules('token','Token','required|trim');
		$this->form_validation->set_rules('job_id','Job id','required|trim');
		$this->form_validation->set_rules('actual_start_date','actual start date','check_date[jobs.job_start_date,job_id."'.$job_id.'"]');
		$this->form_validation->set_rules('actual_end_date','actual end date','check_date[jobs.job_end_date,job_id."'.$job_id.'"]');
		
		if ($this->form_validation->run() != FALSE)
		{
			log_message('debug','++ Job(sitter) +++ Complete Job +++  : No form error');
		
			$userId=$this->input->post('userid');
			$userToken = $this->input->post('token');
			$job_id = $this->input->post('job_id');
			$data = array(
					'job_id' 				=> $job_id,
					'sitter_user_id' 		=> $userId,
					'job_status' 			=> 'completed',
					'last_modified_date' 	=> date('Y-m-d H:i:s'),
					'last_modified_by'		=> $userId
					);
		
			/* if($this->input->post('actual_child_count')!='')
			{
				$data['actual_child_count']=$this->input->post('actual_child_count');
				$rates = $this->common_model->getRate($data['actual_child_count']);
				$data['client_updated_rate']=$rates->client_rate;
				$data['client_rate']=$rates->client_rate;
				$data['sitter_rate_pre']=$rates->sitter_rate;
				$data['rate']=$rates->sitter_rate;
			} */
			
			$children='';
			if($this->input->post('child_id')!='')
			{
				$children = explode(',',$this->input->post('child_id'));
			}
			
			if($this->input->post('notes')!='')
			{
				//$data['notes']=$this->input->post('notes');
				$data['note_by_sitter']=$this->input->post('notes'); //for job note
			}
			
			if($this->input->post('actual_start_date')!='')
			{
				$data['job_start_date'] = date('Y-m-d H:i:s', strtotime($this->input->post('actual_start_date')));
			}
			
			if($this->input->post('actual_end_date')!='')
			{
				$data['completed_date'] = date('Y-m-d H:i:s',strtotime($this->input->post('actual_end_date')));
			}
		
			$checkToken = $this->common_model->check_valid_user_token($userId,$userToken);	// check user token
			if($checkToken)
			{
				$job_completed = $this->job_model->completeJob($job_id,$data,$children);
				if($job_completed === true )
				{
					log_message('debug','++ Job(sitter) +++ Complete Job +++ : All data inserted successfully');
		
					$status = $this->lang->line('rest_status_success');
					$message = $this->lang->line('Job_complete');
					$this->response(array("status"=>$status,'message'=>$message,"data"=>array("token_data"=>$userToken)), 202);
				}
				else
				{
					log_message('debug','++ Job(sitter) +++ Complete Job +++ : data not updated');
					$errors['job_not_completed'] =$job_completed;// $this->lang->line('job_not_completed');
					$status = $this->lang->line('rest_status_failed');
					$this->response(array("errorCode"=>"FR","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
				}
			}
			else
			{
				log_message('debug','++ Job(sitter) +++ Complete Job +++ : Token not matched.');
				$errors['invalid_token'] = $this->lang->line('invalid_token');
				$status = $this->lang->line('rest_status_failed');
				$this->response(array("errorCode"=>"ES2","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
			}
		}
		else
		{
			log_message('debug','++ Job(sitter) +++ Complete Job +++ : Got Form validation error');
			$errors = $this->form_validation->_error_array;
			$status = $this->lang->line('rest_status_failed');
			$this->response(array("errorCode"=>"FVF","errorMessage"=>$errors,"errorDisplayMessage"=>$errors[key($errors)],"status"=>$status), 203);
		}
	}
	
}