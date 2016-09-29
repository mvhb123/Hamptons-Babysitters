<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ipush extends CI_Model {

	function __construct()
	{
		parent::__construct();
                    
	}
	
    function send_notifications($device_id, $notifcation_message,$notification_count,$data)
	{
		
		$device_token = $device_id;
		$this->load->library('apn');
		$this->apn->payloadMethod = 'enhance'; // you can turn on this method for debuggin purpose
		$this->apn->connectToPush();
		
		// adding custom variables to the notification
		$this->apn->setData(array( 'someKey' => true ));
		$send_result = $this->apn->sendMessage($device_token, $notifcation_message, /*badge*/ $notification_count, /*sound*/ 'default',FALSE,'',$data );
		if($send_result)
			log_message('debug','Sending successful');
		else
		{
			log_message('error',$this->apn->error);
			return $this->apn->error;	
		}
	
		
		$this->apn->disconnectPush();
		
		return true;
	}
	
	// designed for retreiving devices, on which app not installed anymore
	public function apn_feedback()
	{
		$this->load->library('apn');

		$unactive = $this->apn->getFeedbackTokens();
		
		if (!count($unactive))
		{
			log_message('info','Feedback: No devices found. Stopping.');
			return false;
		}
		
		foreach($unactive as $u)
		{
			$devices_tokens[] = $u['devtoken'];
		}
	
		/*
		print_r($unactive) -> Array ( [0] => Array ( [timestamp] => 1340270617 [length] => 32 [devtoken] => 002bdf9985984f0b774e78f256eb6e6c6e5c576d3a0c8f1fd8ef9eb2c4499cb4 ) ) 
		*/
	}
}
