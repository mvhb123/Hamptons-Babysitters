<?php

class JobbookingController extends Zend_Controller_Action
{
	

    public function init()
    {
    	
            echo "ads";die;
		$this->_helper->layout->disableLayout();
		$this->client = new Hb_Client();
		$this->jobs = new Hb_Jobs();
		if(!$this->_request->isPost())die('Direct Access is denied');

		$this->handleResponse();
	
	}
	
	
	
    private function handleResponse()
        {
		
            require_once APPLICATION_PATH.'/../library/Hb/anet_php_sdk/AuthorizeNet.php'; // The SDK

            $api_login_id = AUTHORIZENET_LOGIN_ID;
            $md5_setting = AUTHORIZENET_MD5_SETTING; // Your MD5 Setting
            $response = new AuthorizeNetSIM($api_login_id, $md5_setting);
//print_r($response);die;
            if ($response->isAuthorizeNet())
                {
                
                
                //cut code
	
                if ($response->approved)
                    {
                    
                    //cut
                     $data=array(
                         'userid'=>$_POST['x_userid'], 
                         'slots'=>0, 
                         'price'=>(float)$_POST['x_amount'],
                         'notes'=>"Booking amount charged",
                        'package_id'=>0,
                        'transaction_id'=>$response->transaction_id,
                        'payment_gateway'=>'AuthorizeNet',
                     );
 
		 try{
                     
                   // 
                     $subsId =  $this->client->addSubscription($data);
                     
                     $job_information = new Zend_Session_Namespace('job_information');
                    $job=$job_information->job_info;
                     
                    $this->jobs->create($job);
                    $job_id = $this->jobs->job_id;
                    
                    print_r($job_id);die;
		 
		 if($_POST['x_admin']!=''){
                     //for admin
	$this->view->redirect_url.= ADMIN_URL.'client/subscription/client/'.$_POST['x_userid'].'/?success=payment&subs='.$subsId;
}else {
    
	$this->view->redirect_url.= SITE_URL_HTTPS.'client/events/modify/' . $job_id.'/?success=payment&subs='.$subsId;

}
		 
		 
		}catch(Exception $e){
			throw new Exception('Error while adding Job');
		}
}


else
{
	if($_POST['x_admin']!=''){
	$this->view->redirect_url.= ADMIN_URL.'client/checkout/client/'.$_POST['x_userid'].'/?error=payment&msg='.$response->response_reason_text;
}else {
    
                $this->view->redirect_url= SITE_URL_HTTPS.'client/jobbookingcharge?error=payment&msg='.$response->response_reason_text;
}
                  
}

//echo AuthorizeNetDPM::getRelayResponseSnippet($redirect_url);
}






else
{
echo "Error. Check your MD5 Setting.";
}
	
	}
	public function indexAction(){
            
          
            $this->handleResponse();
            
		//  echo "axcxda";die;
	}
	
}
