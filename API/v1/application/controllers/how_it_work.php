<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
* Filename			:	terms
* Classname			:	Terms
* Description		:	Provide web services for terms and conditions for patient and provider
* Create Date		:	18th Dec 2014
* Author			:	Anjali
* -------------------------------------------------------------------------------------------------------------------------------------------------
*/

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';
class How_it_work extends CI_Controller 
{
	function index()
	{
		$this->data['title'] = "How it works";
		$this->load->view('how_it_work_view',$this->data);
	}
}
