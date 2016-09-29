<?php

class Hb_Common extends Hb_User
{

    public function __construct()
    {
        $this->db = Zend_Db_Table::getDefaultAdapter();
        $this->jobs = new Hb_Jobs();
        $this->user = new Hb_User();
        $this->client = new Hb_Client();
        $this->notification= new Hb_Notification();
        parent::__construct();

        $this->flashMessenger =new Zend_Controller_Action_Helper_FlashMessenger();

    }

    // Parse the response back from Authorize.net
    public function parseResponse( $response )
    {
        if( $response === FALSE )
        {
            $this->flashMessenger->addMessage(array('error'=> 'There was a problem while contacting the payment gateway. Please try again.'));;
            return FALSE;
        }
        elseif( $response->error ==1  )
        {
            $this->flashMessenger->addMessage(array('error'=>$response->response_reason_text));;
            return FALSE;
        }
        else if($response->approved ==1 )
        {
            return TRUE;
        }
        $this->flashMessenger->addMessage(array('error'=> 'Received an unknown response from the payment gateway. Please try again.'));;
        return FALSE;
    }

    public function aimTransaction($auth_net)
    {

        require_once APPLICATION_PATH.'/../library/Hb/anet_php_sdk/AuthorizeNet.php';
        $sale = new AuthorizeNetAIM(AUTHORIZENET_LOGIN_ID,AUTHORIZENET_TRAN_KEY);
        $sandbox = X_TEST;
        if($sandbox != true)
            $sandbox = false;

        $sale->setSandbox($sandbox);

        $sale->setFields(
            $auth_net
        );
        $response =  $sale->authorizeAndCapture();
        return $response;

    }

    //function for creating customer profile on authorize.net
    function createCustomerProfile($username,$userId)
    {

        require_once APPLICATION_PATH.'/../library/Hb/anet_php_sdk/AuthorizeNet.php';
        $cust = new AuthorizeNetCIM;
        $sandbox = X_TEST;
        if($sandbox != true)
            $sandbox = false;

        $cust->setSandbox($sandbox);


        $response = $cust->createCustomerProfile($username,$userId);


        if($response->isOk())
        {
            $profile_id = $response->getCustomerProfileId();
            return $profile_id;

        }
        else
        {

            $message = $response->getMessageText();
            $this->flashMessenger->addMessage(array('error'=> $message));;
           return FALSE;
        }

        $this->flashMessenger->addMessage(array('error'=> 'Received an unknown response from the payment gateway. Please try again.'));;
        return FALSE;

    }
    //function for creating customer profile on authorize.net
    function createCustomerProfileComplete($userId,$username,$payment_profile,$shipping_data)
    {

        require_once APPLICATION_PATH.'/../library/Hb/anet_php_sdk/AuthorizeNet.php';
        $cust = new AuthorizeNetCIM;
        $sandbox = X_TEST;
        if($sandbox != true)
            $sandbox = false;

        $cust->setSandbox($sandbox);

        $customerProfile = new AuthorizeNetCustomer;
        $customerProfile->description = 'Customer profile.' .$userId;
        $customerProfile->merchantCustomerId = $userId;
        $customerProfile->email = $username;

        // Add payment profile.
        $paymentProfile = new AuthorizeNetPaymentProfile;
        $paymentProfile->customerType = "individual";
        $paymentProfile->payment->creditCard->cardNumber =$payment_profile['cardNumber'];
        $paymentProfile->payment->creditCard->expirationDate = $payment_profile['expirationDate'];
        $paymentProfile->payment->creditCard->cardCode = $payment_profile['card_code'];
        $paymentProfile->billTo->firstName = $payment_profile['billToFirstName'];
        $paymentProfile->billTo->lastName = $payment_profile['billToLastName'];
        $paymentProfile->billTo->address = $payment_profile['billToAddress'];
        $paymentProfile->billTo->city = $payment_profile['billToCity'];
        $paymentProfile->billTo->state = $payment_profile['billToState'];
        $paymentProfile->billTo->zip = $payment_profile['billToZip'];
        $paymentProfile->billTo->country = $payment_profile['billToCountry'];
        $paymentProfile->billTo->phoneNumber = $payment_profile['billToPhoneNumber'];

        $customerProfile->paymentProfiles[] = $paymentProfile;

        // Add shipping address.
        $address = new AuthorizeNetAddress;
        $address->firstName = $shipping_data['shipToFirstName'];
        $address->lastName = $shipping_data['shipToLastName'];
        $address->address =$payment_profile['billToAddress'];
        $address->city = $payment_profile['billToCity'];
        $address->state =$payment_profile['billToState'];
        $address->zip = $payment_profile['billToZip'];
        $address->country = $payment_profile['billToCountry'];
        $address->phoneNumber = $shipping_data['shipToPhoneNumber'];
        $customerProfile->shipToList[] = $address;


        $response = $cust->createCustomerProfileOriginal($customerProfile);



        if($response->isOk())
        {
            $profile_id = $response->getCustomerProfileId();
            $payment_id =$response->getCustomerPaymentProfileIds();
            $shipping_ids =$response->getCustomerShippingAddressIds();
            $array =  array(
                'profileId'=>$profile_id,
                'paymentProfileId'=>$payment_id,
                'ShippingAddressId'=>$shipping_ids,
            );

           return $array;
        }
        else
        {

            $message = $response->getMessageText();
            $this->flashMessenger->addMessage(array('error'=> $message));;
            return FALSE;
        }

        $this->flashMessenger->addMessage(array('error'=> 'Received an unknown response from the payment gateway. Please try again.'));;
        return FALSE;

    }

    public function profileTransaction($amount,$customerProfileId,$paymentProfileId,$customerAddressId)
    {

        require_once APPLICATION_PATH.'/../library/Hb/anet_php_sdk/AuthorizeNet.php';
        $request = new AuthorizeNetCIM;
        $sandbox = X_TEST;
        if($sandbox != true)
            $sandbox = false;

        $request->setSandbox($sandbox);

        // Create Auth & Capture Transaction
        $transaction = new AuthorizeNetTransaction;
        $transaction->amount = $amount;
        $transaction->customerProfileId = $customerProfileId;
        $transaction->customerPaymentProfileId = $paymentProfileId;
        $transaction->customerShippingAddressId = $customerAddressId;


        $response = $request->createCustomerProfileTransactionOriginal("AuthCapture", $transaction);


        if($response->isOk())
        {
            $transactionResponse = $response->getTransactionResponse();
            return ($transactionResponse);
        }

        $transactionResponse = $response->getTransactionResponse();
        return ($transactionResponse);

    }

    public function updatePaymentProfile($profile_id,$payment_profile_id,$payment_profile)
    {
        require_once APPLICATION_PATH.'/../library/Hb/anet_php_sdk/AuthorizeNet.php';
        $request = new AuthorizeNetCIM;
        $sandbox = X_TEST;
        if($sandbox != true)
            $sandbox = false;

        $request->setSandbox($sandbox);

        // Add payment profile.
        $paymentProfile = new AuthorizeNetPaymentProfile;
        $paymentProfile->payment->creditCard->cardNumber =$payment_profile['cardNumber'];
        $paymentProfile->payment->creditCard->expirationDate = $payment_profile['expirationDate'];
        $paymentProfile->payment->creditCard->cardCode = $payment_profile['card_code'];
        $paymentProfile->billTo->firstName = $payment_profile['billToFirstName'];
        $paymentProfile->billTo->lastName = $payment_profile['billToLastName'];
        $paymentProfile->billTo->address = $payment_profile['billToAddress'];
        $paymentProfile->billTo->city = $payment_profile['billToCity'];
        $paymentProfile->billTo->state = $payment_profile['billToState'];
        $paymentProfile->billTo->zip = $payment_profile['billToZip'];
        $paymentProfile->billTo->country = $payment_profile['billToCountry'];
        $paymentProfile->billTo->phoneNumber = $payment_profile['billToPhoneNumber'];
        $response = $request->updateCustomerPaymentProfileOriginal($profile_id,$payment_profile_id, $paymentProfile);

        if($response->isOk())
        {
            $transactionResponse = $response->getTransactionResponse();
            return true;
        }

        return false;

    }

    public function processPayment($user_id,$data)
    {
        if(empty($user_id))
            return false;


        $userInform = $this->user->getUserInfo($user_id);

        $userInform = (array)$userInform;
        if(empty($data['save_card']) || $data['save_card'] == 0)//if save card not checked
        {

            $auth_net = array(
                'card_num'			=> $data['x_card_num'],// Visa
                'exp_date'			=> $data['x_exp_date'],//mm/yy
                'card_code'			=> $data['x_card_code'],
                'description'			=> 'Thanks for purchasing booking credits',
                'amount'				=> $data['amount'],
                'first_name'			=> $userInform['firstname'],
                'last_name'			=> $userInform['lastname'],
                'address'				=> $data['x_address'],
                'city'				=> $data['x_city'],
                'state'				=> $data['x_state'],
                'zip'					=> $data['x_zip'],
                'country'				=> $data['x_country'],
                'phone'				=> $userInform['phone'],
                'email'				=> $userInform['username'],
                'customer_ip'			=> $data['x_customer_ip'],
            );
            $response = $this->aimTransaction($auth_net);
            return $response ;
        }
        else if($data['save_card'] == 1) //if save card is checked
        {
            ##check whether profile is created on authorize.net
            $payment_profile =   $this->client->getClientPaymentInfo($user_id);


            ##if profile exist on authorize.net
            if( !empty($payment_profile) && $payment_profile != false)
            {


                $payment_pro = (array)$payment_profile;
                $card_detail = array(
                    'cardNumber' 			=> $data['x_card_num'],
                    'expirationDate' 		=> $data['x_exp_date'],
                    'card_code'			=> $data['x_card_code'],
                    'billToFirstName'		=> $userInform['firstname'],
                    'billToLastName'		=> $userInform['lastname'],
                    'billToAddress'			=> $data['x_address'],
                    'billToCity'			=> $data['x_city'],
                    'billToState'			=> $data['x_state'],
                    'billToZip'				=> $data['x_zip'],
                    'billToCountry'			=> $data['x_country'],
                    'billToPhoneNumber'		=> $userInform['phone'],
                );


                //update payment profile
                 $is_update = $this->updatePaymentProfile($payment_pro['authorizenet_profile_id'],$payment_pro['authorizenet_payment_profile_id'],$card_detail);

                $edate=explode('/',$data['x_exp_date']);
                $ex_date=$edate[0]."/".$data['x_exp_date'];
                $ex_date = date('Y-m-t',strtotime($ex_date));

                if($is_update)
                {


                    $profile = array(
                        'user_id'=>$user_id,
                        'card_number'=>substr($data['x_card_num'], -4),
                        'card_num_length'=>strlen($data['x_card_num']),
                        'exp_date'=>$ex_date,
                        'name_on_card'=> ' ',
                        'last_updated'=>date('Y-m-d H:i:s')
                    );

                    $this->client->UpdatePaymentInfo($payment_pro['client_payment_profile_id'],$profile);

                    //create profile transaction
                    $response = $this->profileTransaction( $data['amount'],$payment_pro['authorizenet_profile_id'],$payment_pro['authorizenet_payment_profile_id'],$payment_pro['authorizenet_shipping_id']);


                    return $response ;

                    //                    $transId = $this->profileTransaction($payment_pro[0]['authorizenet_profile_id'],$payment_pro[0]['authorizenet_payment_profile_id'],$data['price'],$cvv);
                    //                    return $transId;
                }
                else
                {
                    $this->flashMessenger->addMessage(array('error'=> 'Received an unknown response from the payment gateway. Please try again.'));;
                    return false;
                }
            }
            //if profile does not exist on authorize.net
            else
            {

                $payment_profile = array(
                    'cardNumber' 		=> $data['x_card_num'],
                    'expirationDate' 	=> $data['x_exp_date'],
                    'card_code'			=> $data['x_card_code'],
                    'billToFirstName'	=> $userInform['firstname'],
                    'billToLastName'	=> $userInform['lastname'],
                    'billToAddress'		=> $data['x_address'],
                    'billToCity'		=> $data['x_city'],
                    'billToState'		=> $data['x_state'],
                    'billToZip'			=> $data['x_zip'],
                    'billToCountry'		=> $data['x_country'],
                    'billToPhoneNumber'	=> $userInform['phone'],
                );

                //create shipping profile
                $shipping_data = array(
                    'shipToFirstName'   =>$userInform['firstname'],
                    'shipToLastName'	=>$userInform['lastname'],
                    'shipToPhoneNumber' =>$userInform['phone'],

                );

                $profile_info = $this->createCustomerProfileComplete($user_id,$userInform['username'],$payment_profile,$shipping_data);



                if(empty($profile_info) || $profile_info == false)
                {
                    return false;
                }

                //if customer profile created sucessfully
                if(!empty($profile_info))
                {
                    $edate=explode('/',$data['x_exp_date']);
                    $ex_date=$edate[0]."/".$data['x_exp_date'];
                    $ex_date = date('Y-m-t',strtotime($ex_date));
                    $profile = array(
                        'user_id'=>$user_id,
                        'authorizenet_profile_id'=>$profile_info['profileId'],
                        'authorizenet_payment_profile_id'=>$profile_info['paymentProfileId'],
                        'authorizenet_shipping_id'=>$profile_info['ShippingAddressId'],
                        'card_number'=>substr($data['x_card_num'],-4),
                        'card_num_length'=>strlen($data['x_card_num']),
                        'exp_date'=>$ex_date,
                        'name_on_card'=>' ',
                        'date_added'=>date('Y-m-d H:i:s')
                    );

                    $this->client->saveclientpaymentdetails($profile);

                    $response = $this->profileTransaction( $data['amount'],$profile_info['profileId'],$profile_info['paymentProfileId'],$profile_info['ShippingAddressId']);
                    return $response ;
                }

            }
        }

    }

    /**
     * Please if you are processing update request is_special, process after this
     * function to update logic entities as per child count increase or decrease
     *
     * @param  integer $job_id,
     * @param string $call_type
     * @param integer $previously_special
     * @return boolean true|false
     */

    public function updateJobChildQunatityInfo($job_id,$call_type = '',$previously_special = 0)
    {
        if(empty($job_id))
            return false;

        $children = $this->jobs->getChildren($job_id);
        $child_count = count($children);

        $this->update_child_count($job_id,$child_count); // also updating is_sepcial condition here if more then 4
        $rate_info = $this->getRate($child_count);

        $job_info = $this->jobs->getJobInfo($job_id,true);
        $this->updateToClientRequests($job_info->client_user_id);

        if(!empty($rate_info))
        {
            $update = array();
            $update['client_rate']=$rate_info->client_rate;
            $update['client_updated_rate']=$rate_info->client_rate;
            $update['rate']= $rate_info->sitter_rate;
            $update['sitter_rate_pre']= $rate_info->sitter_rate;
            $n =  $this->jobs->updateJob($job_id,$update);
        }

        if($call_type == 'sitter')
        {
            // send notification to client if exists
            $sitter_info = $this->user->getUserInfo($job_info->sitter_user_id);
            $message = $sitter_info->firstname." has done some modification in #".$job_id." posted by you";

            $custom_data = array('job_id' => $job_id, 'user_id' => $job_info->client_user_id);
            $this->clientNotificationSendProcess($job_id,$message,$job_info->client_user_id,$custom_data);

            $this->jobs->jobMail($job_id, 'job_request_updated_to_client', 'client', 'mail');
            $this->jobs->jobMail($job_id, 'job_request_updated_to_admin', 'mail', 'sitter');

        }

        if($call_type == 'client')
        {
            // send notification to client if exists
            $client_info = $this->user->getUserInfo($job_info->client_user_id);
            $message = $client_info->firstname." has done some modification in #".$job_id." posted by you";
            $noti_type = 5;//5 for client mofication job 4 for admin confirmed job , 3 for admin modification, 2 for removed from job , 1 for new job request
            $custom_data = array('job_id' => $job_id, 'user_id' => $job_info->sitter_user_id
            ,'job_status' => $job_info->job_status
            ,'notification_type' =>$noti_type
            );

            $this->sittertNotificationSendProcess($job_id,$message,$job_info->sitter_user_id,$custom_data);

            $this->jobs->jobMail($job_id, 'job_request_updated_to_sitter', 'sitter', 'mail');
            $this->jobs->jobMail($job_id, 'job_request_updated_to_admin', 'mail', 'client');

        }


        if ($call_type == 'client' && in_array($job_info->job_status, array('pending', 'new'))) {
            if ($job_info->is_special == '1' && $previously_special != '1') {
                $this->miscRemoveAllSitter($job_id);
            }
            else if ($job_info->is_special == '0' && $previously_special == '1')
            {
                $this->miscAssignSitter($job_id,$job_info->client_user_id);
            }
        }

    }

    /**
     * function to send notification to client activity on babysitter
     *
     * @param  integer $job_id,
     * @param integer $sitter_user_id
     * @param array $custom_data
     * @return string $message
     */
    public function sittertNotificationSendProcess($job_id,$message,$sitter_user_id,$custom_data = array())
    {
        $job_info = $this->jobs->getJobInfo($job_id,true);
        $token_list = $this->getUserDeviceTokenlist($sitter_user_id,true);
        if(!empty($token_list))
        {
            $this->ipush = new Hb_Ipushnotification();

            $sitter_info = $this->user->getUserInfo($sitter_user_id);

            if($sitter_info->notify == 'Yes')
            {
                if(!empty($token_list))
                {
                    foreach($token_list as $key=>$value)
                    {

                        if(!empty($value->deviceToken))
                        {
                            $noti_data = array();
                            $noti_data['job_id'] = $job_id;
                            $noti_data['userid'] = $sitter_user_id;
                            $noti_data['date_added'] = date('Y-m-d H:i:s');
                            $noti_data['date_updated'] = date('Y-m-d H:i:s');
                            $notification_id = $this->notification->insert_notification_data($noti_data);

                            $custom_data['notification_id'] = $notification_id;
                            $count = $this->notification->get_unread_count($sitter_user_id);
                            $this->ipush->sendIosNotification($value->deviceToken, $message,$count,$custom_data );
                        }

                    }
                }

            }
        }
    }


    /**
     * function to send notification to client activity on babysitter
     *
     * @param  integer $job_id,
     * @param integer $client_user_id
     * @param array $custom_data
     * @return string $message
     */
    public function clientNotificationSendProcess($job_id,$message,$client_user_id,$custom_data = array())
    {
        $job_info = $this->jobs->getJobInfo($job_id,true);
        $token_list = $this->getUserDeviceTokenlist($client_user_id,true);
        if(!empty($token_list))
        {
            $this->ipush = new Hb_Ipushnotification();

            $client_info = $this->user->getUserInfo($client_user_id);

            if($client_info->notify == 'Yes')
            {
                if(!empty($token_list))
                {
                    foreach($token_list as $key=>$value)
                    {

                        if(!empty($value->deviceToken))
                        {
                            $noti_data = array();
                            $noti_data['job_id'] = $job_id;
                            $noti_data['userid'] = $client_user_id;
                            $noti_data['date_added'] = date('Y-m-d H:i:s');
                            $noti_data['date_updated'] = date('Y-m-d H:i:s');
                            $notification_id = $this->notification->insert_notification_data($noti_data);

                            $custom_data['notification_id'] = $notification_id;
                            $count = $this->notification->get_unread_count($client_user_id);
                            $this->ipush->sendIosNotification($value->deviceToken, $message,$count,$custom_data );
                        }

                    }
                }

            }
        }
    }

    /******************
     * function to get device token as per user id
     *
     * @param integer $user_id
     * @param boolean $cache
     * @return array
     */
    public function getUserDeviceTokenlist($user_id,$cache= false)
    {

        if(empty($user_id))
            return false;


        if($cache==true && !empty($this->get_user_token[$user_id]))
            return $this->get_user_token[$user_id];


        $select = $this->db->select()->where('userid=?',$user_id)->from('user_token');
        $stmt = $select->query();
        $info = $stmt->fetchAll();

        $this->get_user_token[$user_id] = $info;
        return $info;
    }


    /**
     * Update child count in system
     *
     * @param  integer $job_id,
     * @param integer $actual_child_count
     * @return object  rate object
     */
    protected function update_child_count($job_id, $actual_child_count)
    {

        $last_modified_date = date('Y-m-d H:i:s');
        $last_modified_by = Zend_Auth::getInstance()->getIdentity()->userid;

        $job_info = $this->jobs->getJobInfo($job_id);
        if($job_info == false)
            return false;

        $c_count =$job_info->child_count;
        if ($c_count != $actual_child_count) {

            $update_data = array();
            $update_data['actual_child_count'] = $actual_child_count;
            $update_data['last_modified_by'] = $last_modified_by;
            $update_data['last_modified_date'] = $last_modified_date;
            $update_data['modified'] = 'Yes';
            if($actual_child_count > 4)
            {
                $update_data['is_special'] = '1';
            }

            $n = $this->jobs->updateJob($job_id,$update_data);

            $this->insertLog($job_id,"Added Child",$job_info->actual_child_count,$actual_child_count);
            return $n;

        }

        return false;

    }

    /**
     * Get rate from system
     *
     * @param  integer $child_count,
     * @return object  rate object
     */
    public function getRate($child_count)
    {
        if(empty($child_count))
            $child_count = 1;

        if($child_count>4)
        {
            $child_count=4;
        }

        $select = $this->db->select()->where('child_count=?',$child_count)->from('rates');
        $stmt = $select->query();
        $info = $stmt->fetchObject();
        return $info;

    }

    /**
     * Update total client operation request , what done in system
     *
     * @param  string|integer $client_id, for which total open request going to update
     * @return boolean         true/false
     */
    public function updateToClientRequests($client_id)
    {
        if(empty($client_id))
            return false;

        $client_id = $this->db->quote($client_id);
        $sql="update clients_detail set open_requests=(select count(*) from jobs where client_user_id = $client_id and job_status in('new','pending','confirmed')) where userid= $client_id";
        $this->db->query($sql);

        return true;
    }

    /**
     * insert operation of log , what done in system
     *
     * @param  string|integer $job_id   OPTIONAL Currency value
     * @param  string         $log_message What type of log
     * @throws string         $initial  previous value
     * @return string         true|false
     */

    public function insertLog($job_id,$log_message= "",$initial,$modified)
    {
        if(empty($job_id))
            return false;

        $last_modified_date = date('Y-m-d H:i:s');
        $last_modified_by = Zend_Auth::getInstance()->getIdentity()->userid;

        $log_data = array('job_id'=>$job_id,
            'modified_by'=>$last_modified_by,
            'modified_date'=> $last_modified_date,
            'modification'=>$log_message,//'Added Child',
            'initial'=>$initial,
            'modified'=>$modified);

        $this->db->insert('job_logs',$log_data);

        return true;
    }



    public function miscRemoveAllSitter($job_id)
    {
        $url = SITE_URL . 'misc/removeallsitters';

        //print_r($url);die;
        $post = array(
            'job_id' => $job_id,
        );

        ignore_user_abort(true);
        set_time_limit(0);

        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $url);
        curl_setopt($c, CURLOPT_POST, 1);
        curl_setopt($c, CURLOPT_POSTFIELDS, $post);
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);  // Follow the redirects (needed for mod_rewrite)
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);  // Return from curl_exec rather than echoing
        curl_setopt($c, CURLOPT_FRESH_CONNECT, true);   // Always ensure the connection is fresh
        // Timeout super fast once connected, so it goes into async.
        curl_setopt($c, CURLOPT_TIMEOUT, 1);

        $output = curl_exec($c);
        curl_close($c);
    }

    public function miscAssignSitter($job_id,$client_user_id)
    {
        $url = SITE_URL . 'misc/assignallsitters';


        $post = array(
            'job_id' => $job_id,
            'userid' => $client_user_id,
            'jobno' => 1
        );

        ignore_user_abort(true);
        set_time_limit(0);

        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $url);
        curl_setopt($c, CURLOPT_POST, 1);
        curl_setopt($c, CURLOPT_POSTFIELDS, $post);
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);  // Follow the redirects (needed for mod_rewrite)
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);  // Return from curl_exec rather than echoing
        curl_setopt($c, CURLOPT_FRESH_CONNECT, true);   // Always ensure the connection is fresh
        // Timeout super fast once connected, so it goes into async.
        curl_setopt($c, CURLOPT_TIMEOUT, 1);

        $output = curl_exec($c);
        curl_close($c);
    }




}
