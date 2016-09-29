<?php

class AuthorizenetController extends Zend_Controller_Action {

    public function init() {

        $this->_helper->layout->disableLayout();
        $this->client = new Hb_Client();
        $this->jobs = new Hb_Jobs();

        if (!$this->_request->isPost())
            die('Direct Access is denied');

        //$this->handleResponse();
    }
    
       public function indexAction() {
           
     
        $this->handleResponse();

      
    }

    private function handleResponse() {
        
        
       

        require_once APPLICATION_PATH . '/../library/Hb/anet_php_sdk/AuthorizeNet.php'; // The SDK

        $api_login_id = AUTHORIZENET_LOGIN_ID;
        $md5_setting = AUTHORIZENET_MD5_SETTING; // Your MD5 Setting
        $response = new AuthorizeNetSIM($api_login_id, $md5_setting);
//print_r($response);die;
        if ($response->isAuthorizeNet()) {


            if ($response->response['x_transactiontype'] == "booking") {

                $this->handleResponseBooking($response);
            }

            if ($response->response['x_transactiontype'] == "credit") {
                if ($response->approved) {


                    // Do your processing here.
                    $this->packages = new Hb_Packages();
                    $package = $this->packages->searchByAmount(array('amount' => $_POST['x_amount']));

                    $data = array(
                        'userid' => $_POST['x_userid'],
                        'slots' => (int) $package['credits'] > 0 ? $package['credits'] : 0,
                        'price' => (float) $_POST['x_amount'],
                        'notes' => $package['package_name'],
                        'package_id' => $package['package_id'],
                        'transaction_id' => $response->transaction_id,
                        'payment_gateway' => 'AuthorizeNet',
                    );
                    
                    //print_r($data);die;

                    try {
                        
                       
                        $subsId = $this->client->addSubscription($data);
                                             
                       

                        if ($_POST['x_admin'] != '') {
                            $this->view->redirect_url.= ADMIN_URL . 'client/subscription/client/' . $_POST['x_userid'] . '/?success=payment&subs=' . $subsId;
                        } else {
                            
                          
                            $this->view->redirect_url.= SITE_URL_HTTPS . 'client/subscription/?success=payment&subs=' . $subsId;
                        }
                    } catch (Exception $e) {
                        
                        throw new Exception('Error while adding subscription');
                    }
                } else {
                    if ($_POST['x_admin'] != '') {
                        $this->view->redirect_url.= ADMIN_URL . 'client/checkout/client/' . $_POST['x_userid'] . '/?error=payment&msg=' . $response->response_reason_text;
                    } else {

                        $this->view->redirect_url = SITE_URL_HTTPS . 'client/checkout?error=payment&msg=' . $response->response_reason_text;
                    }
                }
            }//auth response
        } else {
            echo "Error. Check your MD5 Setting.";
        }
    }

 

    private function handleResponseBooking($response) {

        if ($response->approved) {

            //cut
            $data = array(
                'userid' => $_POST['x_userid'],
                'slots' => (int) $_POST['x_add_credit'],
                'price' => (float) $_POST['x_amount'],
                'notes' => "Booking amount charged",
                'package_id' => 0,
                'transaction_id' => $response->transaction_id,
                'payment_gateway' => 'AuthorizeNet',
            );

            try {

                // 
                $subsId = $this->client->addSubscription($data);



                $job = array();
                $job['userid'] = $_POST['x_userid'];
                $job['start_date'] = $_POST['x_start_date'];
                $job['end_date'] = $_POST['x_end_date'];
                $job['notes'] = $_POST['x_notes'];
                $job['children'] = unserialize($_POST['x_children']);
                $job['address_id'] = $_POST['x_address_id'];
                $job['prefer'] = unserialize($_POST['x_prefer']);
                $job['child_count'] = count(unserialize($_POST['x_children']));
                $job['actual_child_count'] = count(unserialize($_POST['x_children']));
                $job['job_status'] = 'new';


                $rate_data = $this->jobs->get_job_child_rate($job['actual_child_count']);

                if (!empty($rate_data)) {
                    $job['sitter_rate_pre'] = $rate_data[0]['sitter_rate'];
                    $job['rate'] = $rate_data[0]['sitter_rate'];
                    $job['client_rate'] = $rate_data[0]['client_rate'];
                    $job['client_updated_rate'] = $rate_data[0]['client_rate'];
                    ;
                }
                $admin = $_POST['x_admin'];

                if (($admin != '') && (isset($admin))) {
                    $job['x_admin'] = 1;
                }
                $job_id = $this->jobs->create($job, $job['userid']);


                if ($_POST['x_admin'] != '') {
                    //for admin
                    $this->view->redirect_url.= ADMIN_URL . 'client/subscription/client/' . $_POST['x_userid'] . '/?success=payment&subs=' . $subsId;
                } else {

                    $this->view->redirect_url.= SITE_URL_HTTPS . 'client/events/modify/' . $job_id . '/?success=payment&subs=' . $subsId;
                }
            } catch (Exception $e) {
                throw new Exception('Error while adding Job');
            }
        } else {
            if ($_POST['x_admin'] != '') {
                $this->view->redirect_url.= ADMIN_URL . 'client/checkout/client/' . $_POST['x_userid'] . '/?error=payment&msg=' . $response->response_reason_text;
            } else {

                $this->view->redirect_url = SITE_URL_HTTPS . 'client/events?error=payment&msg=' . $response->response_reason_text;
            }
        }
    }

}
