<?php
if ( ! defined('BASEPATH')) 
	exit('No direct script access allowed');

//require(APPPATH.'libraries/REST_Controller.php');

class JobAlerts extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('common_model');
		$this->load->model('ipush');
		// -5:00 is newyork timezone offset.
		$this->db->query("SET time_zone = '-5:00'");
	}
	
	function alerts24hr()
	{
		// 24 hours alert
		$param['type']= 'sitter';
		$this->load->library('apn',$param);
		$body='';
		
		$sql = "SELECT job_id, sitter_user_id,date_format(job_start_date, '%Y-%m-%d %h:%i %p') as start_date
		FROM jobs, users WHERE users.userid = jobs.sitter_user_id and users.notify = 'Yes' and 
		(job_start_date between (NOW( )+ INTERVAL 1425 MINUTE ) and ( NOW( )+ INTERVAL 24 HOUR )) 
		and notification_sent = 0";
		
		$job = $this->db->query($sql);
		if($job->num_rows()>0)
		{
			$job = $job->result_array();
			$jobs = implode("','",array_column($job, 'job_id'));
			//print_r($jobs);
			$update = array('notification_sent'=>1);
			$where = "job_id in ('".$jobs."')";
			$this->db->where($where);
			$this->db->update('jobs',$update);
			
			$body="<br><br> Jobs starting in 24 hrs
			<table border='1'>
			<tr>
			<td>job_id</td>
			<td>Start date</td>
			</tr>";
				
			
			foreach($job as $job_noti )
			{
				$tr= $tr."<tr><td>".$job_noti['job_id']."</td><td>".$job_noti['start_date']."</td></tr>";
				$sub_message = $this->lang->line('job_reminder')." ".$job_noti['start_date'];
				$this->common_model->send_notification($job_noti['job_id'],$job_noti['sitter_user_id'],$sub_message);
			}
			
			$tr= $tr.'</table>';
			$body=$body.$tr;
		}
		
		if($body!='')
		{
			$this->load->library('email');
			$this->email->from('jobs@hamptonsbabysitters.com', 'Sofmen');
			$this->email->to('anjali.jain@sofmen.com');
			$this->email->subject('24 cron job started');
			$this->email->message($body);
			$this->email->send();
		}
	}
	
	function alerts2hr()
	{
		// 2 hours alert
		$sql = "SELECT job_id, sitter_user_id,client_user_id,date_format(job_start_date, '%Y-%m-%d %h:%i %p') as start_date
		FROM jobs, users WHERE users.userid = jobs.sitter_user_id and users.notify = 'Yes' and 
		(job_start_date between (NOW( )+ INTERVAL 2 HOUR ) and ( NOW( )+ INTERVAL 3 HOUR )) 
		and notification_sent = 0";
		
		$job = $this->db->query($sql);
		if($job->num_rows()>0)
		{
			$job = $job->result_array();
			$jobs = implode("','",array_column($job, 'job_id'));
			$update = array('notification_sent'=>1);
			 $where = "job_id in ('".$jobs."')";
			$this->db->where($where);
			$this->db->update('jobs',$update);
			
			$body="<br><br> Jobs starting in 24 hrs
			<table border='1'>
			<tr>
			<td>job_id</td>
			<td>Start date</td>
			</tr>";
			
			
			$param['type']= 'sitter';
			$this->load->library('apn',$param);
			foreach($job as $job_noti )
			{
				$tr= $tr."<tr><td>".$job_noti['job_id']."</td><td>".$job_noti['start_date']."</td></tr>";
				$sub_message = $this->lang->line('job_reminder')." ".$job_noti['start_date'];
				$this->common_model->send_notification($job_noti['job_id'],$job_noti['sitter_user_id'],$sub_message);
			}
			
			$tr= $tr.'</table>';
			$body=$body.$tr;
			
			$param['type']= 'client';
			$this->load->library('apn',$param);
			foreach($job as $job_noti )
			{
				$sub_message = $this->lang->line('job_reminder')." ".$job_noti['start_date'];
				$this->common_model->send_notification($job_noti['job_id'],$job_noti['client_user_id'],$sub_message);
			}
		}

		if($body!='')
		{
			$this->load->library('email');
			$this->email->from('jobs@hamptonsbabysitters.com', 'Sofmen');
			$this->email->to('anjali.jain@sofmen.com');
			$this->email->subject('2 hr cron job started');
			$this->email->message($body);
			$this->email->send();
		}
	}
}