<?php
if ( ! defined('BASEPATH')) 
	exit('No direct script access allowed');

require(APPPATH.'libraries/REST_Controller.php');

class Autocomplete_job extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('common_model');
		$this->load->model('ipush');
		$this->load->model('sitter/job_model');
	}
	
	function index()
	{
		// -5:00 is newyork timezone offset.
		$this->db->query("SET time_zone = '-5:00'");
		
		$body='';
		
		$sql = "SELECT job_id, sitter_user_id,client_user_id,date_format( job_end_date, '%Y-%m-%d %h:%i %p' ) as job_end_date
		FROM jobs WHERE ((date_format( job_end_date, '%Y-%m-%d %H:%i' ) + INTERVAL 3 HOUR ) 
		<= (date_format( NOW( ) , '%Y-%m-%d %H:%i' )  + INTERVAL 0 HOUR)) and job_status='confirmed'";
		$to_complete = $this->db->query($sql);
		
		if($to_complete->num_rows()>0)
		{
			$to_complete= $to_complete->result_array();
			$jobs = implode(",",array_column($to_complete, 'job_id'));			
			$body="<br><br> Jobs Completed:
			<table border='1'> 
				<tr>
					<td>job_id</td>
					<td>End date</td>
				</tr>";
			
			$update = array('job_status'=>'completed');
			$where = "job_id in (".$jobs.")";
			$this->db->where($where);
			$this->db->update('jobs',$update);
			
			$sub_message= "Your job has been completed.";
			$param['type']= 'sitter';
			$this->load->library('apn',$param);
			
			$tr="";
			
			foreach($to_complete as $row)
			{
				$tr= $tr."<tr><td>".$row['job_id']."</td><td>".$row['job_end_date']."</td></tr>";
				$sql ="UPDATE `sitters` SET `confirmed_jobs` = confirmed_jobs-1, `completed_jobs` = completed_jobs+1 WHERE `userid` = ".$row['sitter_user_id'];
				$this->db->query($sql);
				
				$complet_sql= "update jobs set completed_date=job_end_date where job_id=".$row['job_id'];
				$this->db->query($complet_sql);
				$this->common_model->send_notification($row['job_id'],$row['sitter_user_id'],$sub_message);
			}
			
			$tr= $tr.'</table>';
			$body=$body.$tr;

			$param['type']= 'client';
			$this->load->library('apn',$param);
			foreach($to_complete as $row)
			{
				$this->common_model->send_notification($row['job_id'],$row['client_user_id'],$sub_message);
				$job_info = $this->job_model->clientJobDetails($row['job_id']);
				//jobMail($row['job_id'],$job_info,'job_completed_to_client','client','mail');
 			}
		}
		
		if($body!='')
		{
			$this->load->library('email');
			$this->email->from('jobs@hamptonsbabysitters.com', 'Sofmen');
			$this->email->to('anjali.jain@sofmen.com');
			$this->email->subject('Autocomplete cron job started');
			$this->email->message($body);
			$this->email->send();
		}
	}
}