<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Common_model extends CI_Model 
{
    function __construct()
    {
            parent::__construct();
            $this->load->model('parent/package_model');
            $this->load->library('authorize_net');
            $this->load->library('AuthorizeCimLib','','authorizeCimLib');
            $this->load->helper('common'); //added alternatively
    }

   /*  function is_token_already_exists($token) 
    {
            $this->db->select('token_id');
            $this->db->where('token', $token);
            $query = $this->db->get('user_token', 1, 0);
            if ($query->num_rows() > 0) 
            {
                    $token = generate_token();
                    $token = $this->is_token_already_exists($token);
            } 
            return $token;
    } */


    function check_valid_user_token($user_id, $token)
    {
            $this->db->select('token_id');
            $this->db->where('userid', $user_id);
            $this->db->where('token', $token);
            $query = $this->db->get('user_token', 1, 0);
            if ($query->num_rows() > 0)
                    return true;
            else
                    return false;
    }

    
    //===== update password
    function changeCurrentPassword($userId,$cpassword,$data)
    {
    	$this->db->where('userid',$userId);
    	$this->db->where('password',$cpassword);
    	$this->db->select('password');
    	//$this->db->where('users.status','active');
    	$this->db->where_in('users.status', array(' ','active'));
    	$pawd = $this->db->get('users');

    	if($pawd->num_rows() > 0)
    	{
    		$this->db->where('userid',$userId);
    		$this->db->update('users',$data);
    	
    		/*----- Get user detail after update ----*/
    		$this->db->select('*');
    		$this->db->where('userid', $userId);
    		$result = $this->db->get('users');
    		$data = $result->row();
    		return $data;
    	}
    	else
    	{
    		return false;
    	}
    }
  
    function addUserToken($userId, $deviceToken, $generate_token, $device_id)
    {
    	$data = array(
    			'userid' => $userId,
    			'deviceToken' => $deviceToken,
    			'token' => $generate_token,
    			'device_id'=>$device_id,
    			'last_update'=>date('Y-m-d H:i:s')
    	);
    	
    	if($device_id!='' && $device_id!=null)
    	{
    		$this->db->select('token_id');
    		$this->db->where('device_id',$device_id);
    		$token_id = $this->db->get('user_token');
    		if($token_id->num_rows()>0)
    		{
    			$this->db->where('device_id',$device_id);
    			$this->db->update('user_token', $data);
    		}
    		else
    		{
    			$this->db->insert('user_token',$data);
    		}
    	}
    	else
    	{	
    		$this->db->insert('user_token',$data);
    	}
    	
    	return $generate_token;
    	
    	/* //set device token empty where given device token match
    	$data['deviceToken'] = '';
    	$this->db->where('deviceToken', $deviceToken);
    	$this->db->where('userid !=',$userId);
    	$this->db->update('user_token', $data);
    	
    	//find whether user id exist in user token table
        $this->db->select('token_id,token,deviceToken');
        $this->db->where('userid',$userId);
        $this->db->where('deviceToken', $deviceToken);
        $this->db->where('deviceToken !=', '');
        $result = $this->db->get('user_token');
        $deviceData = $result->row();   
        
        if($result->num_rows() > 0)
        {   
        	$data['deviceToken'] = $deviceToken;
        	$data['token']= $generate_token;
        	$data['last_update']=date('Y-m-d H:i:s');
        	$this->db->where('userid', $userId);
        	$this->db->where('deviceToken', $deviceToken);
        	$this->db->update('user_token', $data);
			return $generate_token;
        }
        else
        {
            $data = array(
                'userid' => $userId,
                'deviceToken' => $deviceToken,
                'token' => $generate_token,
            	'last_update'=>date('Y-m-d H:i:s')
            );
            $this->db->insert('user_token',$data);
            return $generate_token;
        } */
    }
    
    function deleteUserToken($userId,$token)
    {
    	$this->db->where('userid', $userId);
    	$this->db->where('token', $token);
    	$this->db->delete('user_token');	
    }

   
    function hoursRange( $lower = 0, $upper = 86400, $step = 3600, $format = '' ) {
        $times = array();

        if ( empty( $format ) ) {
            $format = 'g:i a';
        }

        foreach ( range( $lower, $upper, $step ) as $increment ) {
            $increment = gmdate( 'H:i', $increment );

            list( $hour, $minutes ) = explode( ':', $increment );

            $date = new DateTime( $hour . ':' . $minutes );

            $times[(string) $increment] = $date->format( $format );
        }

        return $times;
    }
    
    
    //===== update password
    public function updatePassword($data)
    {
    	$this->db->where('username',$data['username']);
        $this->db->update('users',$data);
        if($this->db->affected_rows())
        	return true;
        else
        	return false;
    }
        
    
    function getUserInfo($user_id)
    {
    	$this->db->select('userid,username,phone,firstname,lastname');
    	$this->db->where('userid', $user_id);
    	$result = $this->db->get('users');
    	
    	if($result->num_rows > 0)
    	{
    		$result = $result->result_array();
    		return $result[0];
    	}	
    	else
    		return false;
    }
    
   
    function checkByUserEmail($username,$usertype)
    {
    	$this->db->select('userid,username,firstname,lastname');
    	$this->db->where('username', $username);
    	if($usertype!='')
    	{
    		$this->db->where('usertype', $usertype);
    	}
    	$result = $this->db->get('users');
    
    	if($result->num_rows > 0)
    	{
    		$result = $result->result();
    		return $result[0];
    	}
    	else
    		return false;
    }
    
    
    function getDeviceToken($user_id)
    {
    	$this->db->select('deviceToken');
    	$this->db->join('users u','u.userid = user_token.userid');
    	$this->db->where('notify','Yes');
    	$this->db->where('u.userid',$user_id);
    	$dt = $this->db->get('user_token');
    	return $dt->result_array();	
    }
    
    function viewKid($kid_id)
    {
    	
    	$this->db->select('*');
    	$this->db->where('child_id',$kid_id);
    	$dt = $this->db->get('children');
    	if($dt->num_rows()>0)
    	{
    		$dt= $dt->result();
    		foreach($dt as $res)
    		{
    			if($res->main_image != null && file_exists($this->config->item('image_path').$res->main_image))
    				$dt[0]->main_image = $this->config->item('image_url').$res->main_image;
    			else
    				$dt[0]->main_image = $this->config->item('image_url')."uploads/noimage.jpg";
    			if($res->app_thumb != null && file_exists($this->config->item('image_path').$res->app_thumb))
    				$dt[0]->thumb_image = $this->config->item('image_url').$res->app_thumb;
    			else
    				$dt[0]->thumb_image = $this->config->item('image_url')."uploads/noimage.jpg";
    			if($res->orginal_image != null && file_exists($this->config->item('image_path').$res->orginal_image))
    				$dt[0]->orginal_image = $this->config->item('image_url').$res->orginal_image;
    			else
    				$dt[0]->orginal_image = $this->config->item('image_url')."uploads/noimage.jpg";
    			//$birthDate = $res->dob;
    			//$birthDate = explode("-", $birthDate);
    			//$dt[0]->age = (string)(date("md", date("U", mktime(0, 0, 0, $birthDate[1],$birthDate[2] , $birthDate[0]))) > date("md") ? ((date("Y")-$birthDate[0])-1):(date("Y")-$birthDate[0]));
    			$dt[0]->age = child_age($res->dob);	
    		}
    		$check = replace_null($dt[0]);
    		return (object)$check;
    	}
    		
    	else
    		return false;
    }
    
    function timediff($time, $format = '%dDays %hHs %m Mins ') {
    
    	 
    	$difference = strtotime($time)-time();
    	if($difference < 0)
    		return false;
    	else{
    
    		$min_only = intval(floor($difference / 60));
    		$hour_only = intval(floor($difference / 3600));
    
    		$days = intval(floor($difference / 86400));
    		$difference = $difference % 86400;
    		$hours = intval(floor($difference / 3600));
    		$difference = $difference % 3600;
    		$minutes = intval(floor($difference / 60));
    		if($minutes == 60){
    			$hours = $hours+1;
    			$minutes = 0;
    		}
    
    		if($days == 0){
    			$format = str_replace('Days', '?', $format);
    			$format = str_replace('Ds', '?', $format);
    			$format = str_replace('%d', '', $format);
    		}
    		if($hours == 0){
    			$format = str_replace('Hours', '?', $format);
    			$format = str_replace('Hs', '?', $format);
    			$format = str_replace('%h', '', $format);
    		}
    		if($minutes == 0){
    			$format = str_replace('Minutes', '?', $format);
    			$format = str_replace('Mins', '?', $format);
    			$format = str_replace('Ms', '?', $format);
    			$format = str_replace('%m', '', $format);
    		}
    
    		$format = str_replace('?,', '', $format);
    		$format = str_replace('?:', '', $format);
    		$format = str_replace('?', '', $format);
    
    		$timeLeft = str_replace('%d', number_format($days), $format);
    		$timeLeft = str_replace('%ho', number_format($hour_only), $timeLeft);
    		$timeLeft = str_replace('%mo', number_format($min_only), $timeLeft);
    		$timeLeft = str_replace('%h', number_format($hours), $timeLeft);
    		$timeLeft = str_replace('%m', number_format($minutes), $timeLeft);
    
    		if($days == 1){
    			$timeLeft = str_replace('Days', 'Day', $timeLeft);
    			$timeLeft = str_replace('Ds', 'D', $timeLeft);
    		}
    		if($hours == 1 || $hour_only == 1){
    			$timeLeft = str_replace('Hours', 'Hour', $timeLeft);
    			$timeLeft = str_replace('Hs', 'H', $timeLeft);
    		}
    		if($minutes == 1 || $min_only == 1){
    			$timeLeft = str_replace('Minutes', 'Minute', $timeLeft);
    			$timeLeft = str_replace('Mins', 'Min', $timeLeft);
    			$timeLeft = str_replace('Ms', 'M', $timeLeft);
    		}
    
    		return $timeLeft;
    	}
    }
    
    
    function preferenceList()
    {
    	$this->db->select("prefer_id,prefer_name");
		$this->db->join('preference_group pg','pg.group_id=pm.group_id');
		$this->db->where('group_name','area');
    	$res = $this->db->get('preference_master pm');
    	$res =$res->result();
    	$data['area'] = $res;
    	
    	$this->db->select("prefer_id,prefer_name");
    	$this->db->join('preference_group pg','pg.group_id=pm.group_id');
    	$this->db->where('group_name','language');
    	$res = $this->db->get('preference_master pm');
    	$res =$res->result();
    	$data['language'] = $res;
    	
    	$this->db->select("prefer_id,prefer_name");
    	$this->db->join('preference_group pg','pg.group_id=pm.group_id');
    	$this->db->where('group_name','other');
    	$res = $this->db->get('preference_master pm');
    	$res =$res->result();
    	$data['other'] = $res;
    	
    	$this->db->select("prefer_id,prefer_name");
    	$this->db->join('preference_group pg','pg.group_id=pm.group_id');
    	$this->db->where('group_name','child_preferences');
    	$res = $this->db->get('preference_master pm');
    	$res =$res->result();
    	$data['child_preferences'] = $res;
    
    	return $data;
    }
    
    function getStateList($country_id)
    {
    	$this->db->select('zone_id as state_id,country_id,name as state');
    	$this->db->where('country_id',$country_id);
    	$states= $this->db->get('states');
    	return $states->result();
    }
    
    function getCountryList()
    {
    	$this->db->select('country_id,name');
    	$countries =$this->db->get('country');
    	return $countries->result();
    }
    
    function getJobAddress($address_id)
    {
    	$this->db->select('billing_name,address_1,streat_address,city,s.name as state,state as state_id,zipcode');
    	$this->db->join('states s','address.state = s.zone_id');
    	$this->db->where('address_id',$address_id);
    	$res = $this->db->get('address');
    	
    	if($res->num_rows()>0)
    	{	
    		$res = $res->result_array();
    		return replace_null($res[0]);
    	}
    	else 
    	{
    		$address =  new stdClass();	
    		$address->billing_name='';
    		$address->address_1='';
    		$address->streat_address='';
    		$address->city='';
    		$address->state='';
    		$address->state_id='';
    		$address->zipcode='';
    		return $address;
    	}
    }
    
    function selected_prefer($prefer,$user_id)
    {
    	$data =array();
    	if($prefer=='sitter') //sitter preferences
    	{
    		$data['language'] = array();
    		$data['area']= array();
    		$data['other'] = array();
    		$data['child_preferences'] = array();
    		
    		$this->db->select("sp.prefer_id,prefer_name");
	    	$this->db->join('preference_group pg','pg.group_id=pm.group_id');
	    	$this->db->join('sitter_preference sp','sp.prefer_id=pm.prefer_id');
	    	$this->db->where('group_name','area');
	    	$this->db->where('sitter_user_id',$user_id);
	    	$res = $this->db->get('preference_master pm');
	    	if($res->num_rows()>0)
	    	{	
	    		$res =$res->result();
	    		$data['area'] = $res;
	    	}
	    	
	    	$this->db->select("sp.prefer_id,prefer_name");
	    	$this->db->join('preference_group pg','pg.group_id=pm.group_id');
	    	$this->db->join('sitter_preference sp','sp.prefer_id=pm.prefer_id');
	    	$this->db->where('group_name','language');
	    	$this->db->where('sitter_user_id',$user_id);
	    	$res = $this->db->get('preference_master pm');
	    	if($res->num_rows()>0)
	    	{
	    		$res =$res->result();
	    		$data['language'] = $res;
	    	}
	    	
	    	
	    	$this->db->select("sp.prefer_id,prefer_name");
	    	$this->db->join('preference_group pg','pg.group_id=pm.group_id');
	    	$this->db->join('sitter_preference sp','sp.prefer_id=pm.prefer_id');
	    	$this->db->where('group_name','other');
	    	$this->db->where('sitter_user_id',$user_id);
	    	$res = $this->db->get('preference_master pm');
	    	if($res->num_rows()>0) 
	    	{
	    		$res =$res->result();
	    		$data['other'] = $res;
	    	}
	    	
	    	$this->db->select("sp.prefer_id,prefer_name");
	    	$this->db->join('preference_group pg','pg.group_id=pm.group_id');
	    	$this->db->join('sitter_preference sp','sp.prefer_id=pm.prefer_id');
	    	$this->db->where('group_name','child_preferences');
	    	$this->db->where('sitter_user_id',$user_id);
	    	$res = $this->db->get('preference_master pm');
	    	if($res->num_rows()>0)
	    	{
	    		$res =$res->result();
	    		$data['child_preferences'] = $res; 
	    	}
    	}
    	else //Job preferences
    	{
    		$this->db->select("jp.prefer_id,prefer_name");
    		$this->db->join('preference_group pg','pg.group_id=pm.group_id');
    		$this->db->join('job_preference jp','jp.prefer_id=pm.prefer_id');
    		$this->db->where('group_name','language');
    		$this->db->where('job_id',$user_id);
    		$res = $this->db->get('preference_master pm');
    		if($res->num_rows()>0)
    		{
    			$res =$res->result();
    			$data['language'] = $res;
    		}
    		
    		$this->db->select("jp.prefer_id,prefer_name");
    		$this->db->join('preference_group pg','pg.group_id=pm.group_id');
    		$this->db->join('job_preference jp','jp.prefer_id=pm.prefer_id');
    		$this->db->where('group_name','other');
    		$this->db->where('job_id',$user_id);
    		$res = $this->db->get('preference_master pm');
    		if($res->num_rows()>0)
    		{
    			$res =$res->result();
    			$data['other'] = $res;
    		}
    	}
    	return $data;
    }
    
    function get_notification($user_id)
    {
    	$this->db->select('notify');
    	$this->db->where('userid',$user_id);
    	$res= $this->db->get('users');
    	if($res->num_rows()>0)
    	{
    		$res = $res->row();
    		return $res->notify;
    	}
    	else
    	{
    		return false;
    	}
    }
    
    
    function set_notification($user_id,$notify)
    {
    	$data['notify']=$notify;
    	$this->db->where('userid',$user_id);
    	$this->db->update('users',$data);
    
    	$this->db->select('notify');
    	$this->db->where('userid',$user_id);
    	$res= $this->db->get('users');
    	if($res->num_rows()>0)
    	{
    		$res = $res->row();
    		return $res->notify;
    	}
    	else
    	{
    		return false;
    	}
    }
    
  	function updateNotification_count($noti_id,$user_id)
  	{
    	$this->db->where('notification_id',$noti_id);
    	$this->db->update('notification',array('is_read' => 1));
    	
    	$this->db->select('notification_id');
    	$this->db->where('userid',$user_id);
    	$this->db->where('is_read',0);
    	$result= $this->db->get('notification');
    	$notification_count = $result->num_rows();
    	return $notification_count;
    }
    
    function readallnotification($user_id)
    {
    	$this->db->where('userid',$user_id);
    	$this->db->update('notification',array('is_read' => 1));
    	 
    	$this->db->select('notification_id');
    	$this->db->where('userid',$user_id);
    	$this->db->where('is_read',0);
    	$result= $this->db->get('notification');
    	$notification_count = $result->num_rows();
    	return $notification_count;
    }
    
    function send_notification($job_id,$user_id,$msg)
    {
    	$dt = $this->getDeviceToken($user_id);
    	if(!empty($dt))
    	{
    		$notiData = array(
    				'userid' => $user_id,
    				'job_id' => $job_id,
    				'date_added' => date('Y-m-d H:i:s'),
    				'date_updated' => date('Y-m-d H:i:s')
    		);
    		
    		$this->db->insert('notification',$notiData);
    		$lastNotiId = $this->db->insert_id();
    		
    		$this->db->select('notification_id');
    		$this->db->where('userid',$user_id);
    		$this->db->where('is_read',0);
    		$result= $this->db->get('notification');
    		$notification_count = $result->num_rows();
    		createFile('assign_sitters', print_r($dt, true));
    		$notification_sent="";
    		foreach($dt as $rowdt)
    		{
    			if(!empty($rowdt['deviceToken']) && $rowdt['deviceToken']!='0' && $rowdt['deviceToken']!='')
    			{
    				//$loginInfo = $this->common_model->getUserInfo($data['to_user']);
    				$noti_data = array(
    						'job_id'=>$job_id,
    						'user_id'=>$user_id,
    						'notification_id'=>$lastNotiId
    				);
    				
    				$ipus_result = $this->ipush->send_notifications($rowdt['deviceToken'],$msg,$notification_count,$noti_data);
    				if($ipus_result !== true)
    				{
    					$success_message = ' But '.$ipus_result;
    					$notification_sent = $notification_sent.$user_id."  ".$rowdt['deviceToken']."_____";
    					//print_r($success_message);
    				}
    				else {
    					//var_dump($ipus_result);
    				}
    			}
    		}
    		createFile('sent_to', print_r($notification_sent,true));
    	}
    }
    
    //function for fetching rates
    function getRate($child_count)
    {
    	if($child_count>4)
    	{
    		$child_count=4;
    	}
    	$this->db->select('client_rate,sitter_rate');
    	$this->db->where('child_count',$child_count);
    	$rate=$this->db->get('rates');
    	if($rate->num_rows()>0)
    	{
    		$rate=$rate->result();
    		return $rate[0];
    	}
    	else 
    		return false;
    }
    
    
    //function for creating customer profile on authorize.net
    function createCustomerProfile($username,$userId)
    {
    	$this->authorizeCimLib->set_data('email',$username);
    	$this->authorizeCimLib->set_data('description', 'Customer profile.' .$userId);
    	$this->authorizeCimLib->set_data('merchantCustomerId', $userId);
    
    	if(! $payresult['profileid'] = $this->authorizeCimLib->create_customer_profile())
    	{
    		$response[0]=false;
    		$response[1]=$this->authorizeCimLib->get_error_msg();
    		
    		if(preg_match("/A duplicate record with ID [0-9]* already exists./", $response[1], $match))
    		{
    			if(preg_match("/ [0-9]* /", $response[1], $match1))
    			{
    				$res[0] = intval(trim($match1[0]," ")); ;
    				return $res;
    			}
    		}
    		return $response;
    	}
    
    	$response[0]=$payresult['profileid'];
    	return $response;
    }
    
    //function for creating payment profile on authorize.net
    function createPaymentProfile($data)
    {
    	// Create the Payment Profile
    	$this->authorizeCimLib->set_data('customerProfileId', $data['customerProfileId']);
    	$this->authorizeCimLib->set_data('billToFirstName', $data['billToFirstName']);
    	$this->authorizeCimLib->set_data('billToLastName',$data['billToLastName']);
    	$this->authorizeCimLib->set_data('billToAddress', $data['billToAddress']);
    	$this->authorizeCimLib->set_data('billToCity', $data['billToCity']);
    	$this->authorizeCimLib->set_data('billToState', $data['billToState']);
    	$this->authorizeCimLib->set_data('billToZip', $data['billToZip']);
    	$this->authorizeCimLib->set_data('billToCountry', $data['billToCountry']);
    	$this->authorizeCimLib->set_data('billToPhoneNumber',$data['billToPhoneNumber']);
    	$this->authorizeCimLib->set_data('cardNumber', $data['cardNumber']);
    	$this->authorizeCimLib->set_data('expirationDate', $data['expirationDate']);
    
    	
    	$this->authorizeCimLib->set_validationmode($this->config->item('validation_mode'));
    	$payresult['paymentprofileid'] = $this->authorizeCimLib->create_customer_payment_profile();
    	if($payresult['paymentprofileid']==null||$payresult['paymentprofileid']==-1)
    	{
    		$response[0]=false;
    		$response[1]=$this->authorizeCimLib->get_error_msg();
    		return $response;
    	}
    
    	// Find out if it was approved or not.
    	//$this->_validateresponse();
    	
    	$a = $this->authorizeCimLib->get_direct_response();
    	$response[0]= $payresult['paymentprofileid'];
    	//$this->authorizeCimLib->clear_data();
    	$response[1] = $a[50];
    	return $response;
    
    }
    
    //function for updating payment profile on authorize.net
    function updatePaymentProfile($data,$payment_profile_id)
    {
    	$this->authorizeCimLib->set_data('customerProfileId', $data['customerProfileId']);
    	$this->authorizeCimLib->set_data('billToFirstName', $data['billToFirstName']);
    	$this->authorizeCimLib->set_data('billToLastName',$data['billToLastName']);
    	$this->authorizeCimLib->set_data('billToAddress', $data['billToAddress']);
    	$this->authorizeCimLib->set_data('billToCity', $data['billToCity']);
    	$this->authorizeCimLib->set_data('billToState', $data['billToState']);
    	$this->authorizeCimLib->set_data('billToZip', $data['billToZip']);
    	$this->authorizeCimLib->set_data('billToCountry', $data['billToCountry']);
    	$this->authorizeCimLib->set_data('billToPhoneNumber',$data['billToPhoneNumber']);
    	$this->authorizeCimLib->set_data('cardNumber', $data['cardNumber']);
    	$this->authorizeCimLib->set_data('expirationDate', $data['expirationDate']);
    
    	//$this->authorizeCimLib->setData($data);
    	$this->authorizeCimLib->set_validationmode($this->config->item('validation_mode'));
    	$payresult['paymentprofileid']=$this->authorizeCimLib->update_customer_payment_profile($data['customerProfileId'],$payment_profile_id);
    	//var_dump($pay_id);
    	
    	if($payresult['paymentprofileid']==null||$payresult['paymentprofileid']==-1)
    	{
    	//var_dump ($data);die;
    		$response[0]=false;
    		$response[1]=$this->authorizeCimLib->get_error_msg();
    		return $response;
    	}
    
    	$this->authorizeCimLib->clear_data();
    	$response[0]=$payresult['paymentprofileid'];
    	return $response;
    }
    
    
    //function for transaction without saving card
    function aimTransaction($auth_net)
    {
    	$this->authorize_net->setData($auth_net);
    		
    	// Try to AUTH_CAPTURE
    	if( $this->authorize_net->authorizeAndCapture() )
    	{
    
    		if($this->authorize_net->getApprovalCode())
    			$response[0]=$this->authorize_net->getTransactionId();
    		else
    		{
    			$response[0]=false;
    			$response[1]='something went wrong';
    		}
    	}
    	else
    	{
    		$response[0]=false;
    		$response[1]=$this->authorize_net->getError();
    	}
    	return $response;
    }
    
    //function for charging saved card
    function profileTransaction($profile_id,$payment_profile_id,$amount,$cvv)
    {
    	$this->authorizeCimLib->set_data('amount', $amount);
    	$this->authorizeCimLib->set_data('customerProfileId', $profile_id);
    	$this->authorizeCimLib->set_data('customerPaymentProfileId',$payment_profile_id);
    	$this->authorizeCimLib->set_data('cardCode', $cvv);
    	$payresult['approvalcode'] = $this->authorizeCimLib->create_customer_transaction_profile('profileTransAuthCapture');
    	// Types: 'profileTransAuthCapture', 'profileTransCaptureOnly', 'profileTransAuthOnly'
    	if($payresult['approvalcode']==Null ||$payresult['approvalcode']==0)
    	{
    		$response[0]=false;
    		$response[1]=$this->authorizeCimLib->get_error_msg();
    		//return $response;
    	}
    
    	if($payresult['approvalcode'][6]!=null && $payresult['approvalcode'][6]!=0 )
    	{
    		$response[0]= $payresult['approvalcode'][6];
    	}
    	else
    	{
    		$response[0]= false;
    		$response[1]= $payresult['approvalcode'][3];//$payresult['approvalcode'][3];
    	}
    	return $response;
    }
    
    //function for creating shipping profile
    function createShippingProfile($ship_data)
    {
    	$this->authorizeCimLib->set_data('customerProfileId', $ship_data['customerProfileId']);
    	$this->authorizeCimLib->set_data('shipToFirstName', $ship_data['shipToFirstName']);
    	$this->authorizeCimLib->set_data('shipToLastName', $ship_data['shipToLastName']);
    	$this->authorizeCimLib->set_data('shipToPhoneNumber', $ship_data['shipToPhoneNumber']);
    
    	if(! $payresult['shippingprofileid'] = $this->authorizeCimLib->create_customer_shipping_profile())
    	{
    		$response[0]=false;
    		$response[1]=$this->authorizeCimLib->get_error_msg();
    		return $response;
    	}
    
    	$response[0]= $payresult['shippingprofileid'];
    	$this->authorizeCimLib->clear_data();
    	return $response;
    }
    
    
    //function for deleting customer profile.
    function deleteProfile($id)
    {
    	if(! $this->authorizeCimLib->delete_customer_profile($id))
    	{
    		echo '<p> Error: ' . $this->authorizeCimLib->get_error_msg() . '</p>';
    		die();
    	}
    }
    
    
    //function for processing payment
    function processPayment($data)
    {
    	$userInform = $this->common_model->getUserInfo($data['user_id']);
    	$auth_pay_profile = $data['payment_profile_id'];
    	$transId='';
    	$cvv=$data['cvv'];
    	//if payment profile id not given in resquest
    	if($auth_pay_profile=="" || $auth_pay_profile== NULL)
    	{
    		if($data['save_card'] == 0)//if save card not checked
    		{
    			$auth_net = array(
    					'x_card_num'			=> $data['card_number'],// Visa
    					'x_exp_date'			=> $data['expiry_date'],//mm/yy
    					'x_card_code'			=> $data['cvv'],
    					'x_description'			=> 'Thanks for purchasing booking credits',
    					'x_amount'				=> $data['price'],
    					'x_first_name'			=> $userInform['firstname'],
    					'x_last_name'			=> $userInform['lastname'],
    					'x_address'				=> $data['address'],
    					'x_city'				=> $data['city'],
    					'x_state'				=> $data['state'],
    					'x_zip'					=> $data['zipcode'],
    					'x_country'				=> $data['country'],
    					'x_phone'				=> $userInform['phone'],
    					'x_email'				=> $userInform['username'],
    					'x_customer_ip'			=> $this->input->ip_address(),
    			);
    				
    			$transId = $this->aimTransaction($auth_net);
    			return $transId ;
    		}
    		else //if save card is checked
    		{
    			//check whether profile is created on authorize.net
    			$this->db->select('*');
    			$this->db->where('user_id',$data['user_id']);
    			$payment_profile = $this->db->get('client_payment_profile');
    			//if profile exist on authorize.net
    		
    			if($payment_profile->num_rows()>0)
    			{
    				$payment_pro = $payment_profile->result_array();
    				$card_detail = array(
    						'customerProfileId'		=> $payment_pro[0]['authorizenet_profile_id'],
    						'cardNumber' 			=> $data['card_number'],
    						'expirationDate' 		=> $data['expiry_date'],
    						'billToFirstName'		=> $userInform['firstname'],
    						'billToLastName'		=> $userInform['lastname'],
    						'billToAddress'			=> $data['address'],
    						'billToCity'			=> $data['city'],
    						'billToState'			=> $data['state'],
    						'billToZip'				=> $data['zipcode'],
    						'billToCountry'			=> $data['country'],
    						'billToPhoneNumber'		=> $userInform['phone'],
    				);
    
    				//update payment profile
    				$payment_id=$this->updatePaymentProfile($card_detail,$payment_pro[0]['authorizenet_payment_profile_id']);
    				$edate=explode('/',$data['expiry_date']);
    				$ex_date=$edate[0]."/".$data['expiry_date'];
    				$ex_date = date('Y-m-t',strtotime($ex_date));
    					
    				if($payment_id[0])
    				{
    					$profile = array(
    							'user_id'=>$data['user_id'],
    							'card_number'=>substr($data['card_number'], -4),
    							'card_num_length'=>strlen($data['card_number']),
    							'exp_date'=>$ex_date,
    							'name_on_card'=>$data['name_on_card'],
    							'last_updated'=>date('Y-m-d H:i:s')
    					);
    						
    					$this->db->where('user_id',$data['user_id']);
    					$this->db->update('client_payment_profile',$profile);
    
    					//create profile transaction
    					$transId = $this->profileTransaction($payment_pro[0]['authorizenet_profile_id'],$payment_pro[0]['authorizenet_payment_profile_id'],$data['price'],$cvv);
    					return $transId;
    				}
    				else
    				{
    					return $payment_id;
    				}
    			}
    			//if profile does not exist on authorize.net
    			else
    			{
    				$profile_id = $this->createCustomerProfile($userInform['username'],$data['user_id']);
    				//if customer profile created sucessfully
    				if($profile_id[0])
    				{
    					$card_detail = array(
    							'customerProfileId'	=>$profile_id[0],
    							'cardNumber' 		=> $data['card_number'],
    							'expirationDate' 	=> $data['expiry_date'],
    							'billToFirstName'	=> $userInform['firstname'],
    							'billToLastName'	=> $userInform['lastname'],
    							'billToAddress'		=> $data['address'],
    							'billToCity'		=> $data['city'],
    							'billToState'		=> $data['state'],
    							'billToZip'			=> $data['zipcode'],
    							'billToCountry'		=> $data['country'],
    							'billToPhoneNumber'	=> $userInform['phone'],
    					);
    					
    					//create payment profile
    					$payment_id = $this->createPaymentProfile($card_detail);
    					//if payment profile created sucessfully
    					if($payment_id[0]&&$payment_id!=-1)
    					{
    						//create shipping profile
    						$shipping_data = array(
    								'customerProfileId'	=>$profile_id[0],
    								'shipToFirstName'   =>$userInform['firstname'],
    								'shipToLastName'	=>$userInform['lastname'],
    								'shipToPhoneNumber' =>$userInform['phone'],
    
    						);
    						$shipping_id = $this->createShippingProfile($shipping_data);
    
    						if($shipping_id[0])
    						{
    							$edate=explode('/',$data['expiry_date']);
    							$ex_date=$edate[0]."/".$data['expiry_date'];
    							$ex_date = date('Y-m-t',strtotime($ex_date));
    							$profile = array(
    									'user_id'=>$data['user_id'],
    									'authorizenet_profile_id'=>$profile_id[0],
    									'authorizenet_payment_profile_id'=>$payment_id[0],
    									'authorizenet_shipping_id'=>$shipping_id[0],
    									'card_number'=>substr($data['card_number'],-4),
    									'card_num_length'=>strlen($data['card_number']),
    									'exp_date'=>$ex_date,
    									'name_on_card'=>$data['name_on_card'],
    									'date_added'=>date('Y-m-d H:i:s')
    							);
    								
    							$this->db->insert('client_payment_profile',$profile);
    							$transId = $this->profileTransaction($profile_id[0],$payment_id[0],$data['price'],$cvv);
    							return $transId ;
    						}
    						else
    						{
    							$this->deleteProfile($profile_id[0]);
    							return $shipping_id;
    						}
    					}
    					else
    					{
    						$this->deleteProfile($profile_id[0]);
    						return $payment_id;
    					}
    				}
    				else
    				{
    					return $profile_id;
    				}
    			}
    		}
    	}
    	else
    	{
    		//check whether profile is created on authorize.net
    		$this->db->select('*');
    		$this->db->where('user_id',$data['user_id']);
    		$this->db->where('authorizenet_payment_profile_id',$auth_pay_profile);
    		$payment_profile = $this->db->get('client_payment_profile');
    		if($payment_profile->num_rows()>0)
    		{
    			$payment_pro = $payment_profile->result_array();
    			$transId =$this->profileTransaction($payment_pro[0]['authorizenet_profile_id'],$auth_pay_profile,$data['price'],$cvv);
    			return $transId ;
    		}
    	}
    }
    
    
    //function for add edit credit card
    function addEditCard($data)
    {
    	$userInform = $this->common_model->getUserInfo($data['user_id']);
    	$transId='';
    	$cvv=$data['cvv'];
    	
    	//check whether profile is created on authorize.net
    	$this->db->select('*');
    	$this->db->where('user_id',$data['user_id']);
    	$payment_profile = $this->db->get('client_payment_profile');
    	
    	if($payment_profile->num_rows()>0)
    	{
    		$payment_pro = $payment_profile->result_array();
    		$card_detail = array(
    						'customerProfileId'		=> $payment_pro[0]['authorizenet_profile_id'],
    						'cardNumber' 			=> $data['card_number'],
    						'expirationDate' 		=> $data['expiry_date'],
    						'billToFirstName'		=> $userInform['firstname'],
    						'billToLastName'		=> $userInform['lastname'],
    						'billToAddress'			=> $data['address'],
    						'billToCity'			=> $data['city'],
    						'billToState'			=> $data['state'],
    						'billToZip'				=> $data['zipcode'],
    						'billToCountry'			=> $data['country'],
    						'billToPhoneNumber'		=> $userInform['phone'],
    				);
    	
    		//update payment profile
    		$payment_id=$this->updatePaymentProfile($card_detail,$payment_pro[0]['authorizenet_payment_profile_id']);
    		$edate=explode('/',$data['expiry_date']);
    		$ex_date=$edate[0]."/".$data['expiry_date'];
    		$ex_date = date('Y-m-t',strtotime($ex_date));
    		
    		if($payment_id[0])
    		{
    			$profile = array(
    						'user_id'=>$data['user_id'],
    						'card_number'=>substr($data['card_number'], -4),
    						'card_num_length'=>strlen($data['card_number']),
    						'exp_date'=>$ex_date,
    						'name_on_card'=>$data['name_on_card'],
    						'last_updated'=>date('Y-m-d H:i:s')
    					);
    	
    			$this->db->where('user_id',$data['user_id']);
    			$this->db->update('client_payment_profile',$profile);
    			
    			$this->db->select('authorizenet_payment_profile_id,card_number,name_on_card,card_num_length');
    			$this->db->where('user_id',$data['user_id']);
    			$payment = $this->db->get('client_payment_profile');
    			if($payment->num_rows()>0)
    			{
    				$payment=$payment->result();
    				
    				if($payment[0]->card_num_length>0)
    				{
    					$payment[0]->card_number =  str_pad($payment[0]->card_number, $payment[0]->card_num_length, "X", STR_PAD_LEFT);
    				}
    				
    				$payment[0]->authorizenet_payment_profile_id = base64_encode($payment[0]->authorizenet_payment_profile_id);
    				return $payment;
    			}
    					
    		}
    				
    		return $payment_id;
    				
    	}
    	//if profile does not exist on authorize.net
    	else
    	{
    		$profile_id = $this->createCustomerProfile($userInform['username'],$data['user_id']);
    		//if customer profile created sucessfully
    		if($profile_id[0])
    		{
    			$card_detail = array(
    							'customerProfileId'	=>$profile_id[0],
    							'cardNumber' 		=> $data['card_number'],
    							'expirationDate' 	=> $data['expiry_date'],
    							'billToFirstName'	=> $userInform['firstname'],
    							'billToLastName'	=> $userInform['lastname'],
    							'billToAddress'		=> $data['address'],
    							'billToCity'		=> $data['city'],
    							'billToState'		=> $data['state'],
    							'billToZip'			=> $data['zipcode'],
    							'billToCountry'		=> $data['country'],
    							'billToPhoneNumber'	=> $userInform['phone'],
    					);
    		//create payment profile
    		$payment_id = $this->createPaymentProfile($card_detail);
    		
    		//if payment profile created sucessfully
    		if($payment_id[0])
    		{
    			//create shipping profile
    			$shipping_data = array(
    								'customerProfileId'	=>$profile_id[0],
    								'shipToFirstName'   =>$userInform['firstname'],
    								'shipToLastName'	=>$userInform['lastname'],
    								'shipToPhoneNumber' =>$userInform['phone'],
    	
    						);
    			$shipping_id = $this->createShippingProfile($shipping_data);
    	
    			if($shipping_id[0])
    			{
    				$edate=explode('/',$data['expiry_date']);
    				$ex_date=$edate[0]."/".$data['expiry_date'];
    				$ex_date = date('Y-m-t',strtotime($ex_date));
    				$profile = array(
    									'user_id'=>$data['user_id'],
    									'authorizenet_profile_id'=>$profile_id[0],
    									'authorizenet_payment_profile_id'=>$payment_id[0],
    									'authorizenet_shipping_id'=>$shipping_id[0],
    									'card_number'=>substr($data['card_number'],-4),
    									'card_num_length'=>strlen($data['card_number']),
    									'exp_date'=>$ex_date,
    									'name_on_card'=>$data['name_on_card'],
    									'date_added'=>date('Y-m-d H:i:s')
    							);
    				
    				$this->db->insert('client_payment_profile',$profile);
    				
    				$this->db->select('authorizenet_payment_profile_id,card_number,name_on_card,card_num_length');
    				$this->db->where('user_id',$data['user_id']);
    				$payment = $this->db->get('client_payment_profile');	
    				if($payment->num_rows()>0)
    				{
    					$payment=$payment->result();
    					
    					if($payment[0]->card_num_length>0)
    					{
    						$payment[0]->card_number =  str_pad($payment[0]->card_number, $payment[0]->card_num_length, "X", STR_PAD_LEFT);
    					}
    					
    					$payment[0]->authorizenet_payment_profile_id = base64_encode($payment[0]->authorizenet_payment_profile_id);
    					return $payment;
    				}	
    			}
    			else
    			{
    				$this->deleteProfile($profile_id[0]);
    				return $shipping_id;
    			}
    		}
    		else
    		{
    			$this->deleteProfile($profile_id[0]);
    		}	
    		
    		return $payment_id;
    					
    	}
    	else
    	{
    		return $profile_id;
    	}
    }
  }
  
  //function for fetching relation to child
  function getChildRelation()
  {
  	$this->db->select("*");
  	$res = $this->db->get('relation_to_child');
  	return $res->result();
  }
  
  
  //function for fetching special needs
  function getSpecialNeeds()
  {
  	$this->db->select("*");
  	$res = $this->db->get('special_needs');
  	return $res->result();
  }
  
  
  //function for notification count
  function notification_count($user_id)
  {
  	$this->db->select('notification_id');
  	$this->db->where('userid',$user_id);
  	$this->db->where('is_read',0);
  	$result= $this->db->get('notification');
  	$notification_count = $result->num_rows();
  	return $notification_count;
  }
  
}
