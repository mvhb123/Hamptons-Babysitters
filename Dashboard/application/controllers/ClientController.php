<?php

class ClientController extends Zend_Controller_Action
{

    public function init()
    {


        if ($_GET['env'] == 'dev') {
            var_dump($_SESSION['Zend_Auth']);
        }
        /* Initialize action controller here */
        $this->client = new Hb_Client();
        $this->common = new Hb_Common();

        $this->notification = new Hb_Notification();
        $this->searchParams = $this->_request->getParams();
        $this->view->searchParams = $this->searchParams;

        $this->actionName = $this->_request->getActionName();
        if ($this->actionName == 'events' && in_array($this->searchParams['listevents'], array('jobs', 'history')))
            $this->_request->setActionName('listevents');

        $this->view->actionName = $this->actionName;


        if (Zend_Auth::getInstance()->getIdentity()->userid != '')
            $client = Zend_Auth::getInstance()->getIdentity()->userid;


        if ($client == '')
            $this->_helper->redirector('index', 'index');
        else {
            $this->view->userid = $client;
            $this->view->userInfo = $this->client->getDetail($this->view->userid, 'profile');
            if ($this->actionName != 'profile')
                if ((int)$this->view->userInfo['profile_completed'] <= 0) {
                    $this->_helper->redirector('profile', 'client');
                }
        }

        $this->view->flashMessenger  =$this->flashMessenger =  $this->_helper->getHelper('FlashMessenger');
    }

    public function indexAction()
    {


        // action body

        $this->jobs = new Hb_Jobs();
        $this->client = new Hb_Client();


        $this->searchParams['client_id'] = $this->view->userid;
        $this->searchParams['rows'] = 5;
        $this->view->events = $this->jobs->search(array_merge($this->searchParams, array('status' => 'all')));

        $this->events['total'] = $this->view->events['total'];
        $this->view->events = $this->view->events['rows'];

        $this->view->subscriptions = $this->client->getSubscription(array_merge(array('userid' => $this->view->userid), $this->searchParams));
        $this->view->subscriptions = $this->view->subscriptions['rows'];

        $this->view->children = $this->client->children->getAll($this->view->userid);
    }

    public function profileAction()
    {
        // action body
        //$this->_request->getParams(); 

        $profile = new Zend_Session_Namespace('profile');

        $this->view->successMessage = $profile->successMessage;
        unset($profile->successMessage);

        //unset($_SESSION['successMessage']);
        $this->userform = $this->userForm();

        $this->view->billing = $this->client->getAddress(array('userid' => $this->view->userid, 'address_type' => 'billing'));
        $this->view->billing = $this->view->billing[0];


        $addroptions = array('country' => ($this->_request->isPost() && $this->searchParams['billing']['country'] > 0 ? $this->searchParams['billing']['country'] : $this->view->billing['country']));

        //  print_r($addroptions);die();
        $this->userform->addSubForms(array('local' => $this->addressForm(), 'billing' => $this->addressForm($addroptions)));
        $this->userform->local->setIsArray(true);


        $this->userform->billing->setIsArray(true);
        $modify = false;
        if ($this->view->userid != '') {

            $clientDetail = $this->client->getDetail($this->view->userid, 'profile');
            if ($clientDetail) {
                $modify = true;
                //$this->view->userid = $this->view->userid;
                $this->userform->setAction(SITE_URL . 'client/profile/');

                $this->userform->firstname->setValue($clientDetail['firstname']);
                $this->userform->middlename->setValue($clientDetail['middlename']);
                $this->userform->lastname->setValue($clientDetail['lastname']);
                $this->userform->username->setValue($clientDetail['username']);
                $this->userform->dob->setValue($clientDetail['dob']);
                $this->userform->phone->setValue($clientDetail['phone']);
                $this->view->profile_thumb_image = $clientDetail['thumb_image'];
                $this->userform->local_phone->setValue($clientDetail['local_phone']);
                $this->userform->work_phone->setValue($clientDetail['work_phone']);

                $this->userform->emergency_contact->setValue($clientDetail['emergency_contact']);
                $this->userform->emergency_phone->setValue($clientDetail['emergency_phone']);

                $this->userform->sitter_fee->setValue($clientDetail['sitter_fee']);
                $this->userform->spouse_firstname->setValue($clientDetail['spouse_firstname']);
                $this->userform->spouse_lastname->setValue($clientDetail['spouse_lastname']);
                $status = $clientDetail['status'];

                $this->view->local = $this->client->getAddress(array('userid' => $this->view->userid, 'address_type' => 'local'));
                $this->view->local = $this->view->local[0];

                if (isset($this->view->local)) {
                    $local_address_id = $this->view->local['address_id'];
                    $this->userform->local->billing_name->setValue($this->view->local['billing_name']);

                    $this->userform->local->address_1->setValue($this->view->local['address_1']);
                    $this->userform->local->address_2->setValue($this->view->local['address_2']);
                    $this->userform->local->streat_address->setValue($this->view->local['streat_address']);
                    $this->userform->local->zipcode->setValue($this->view->local['zipcode']);
                    $this->userform->local->city->setValue($this->view->local['city']);
                    $this->userform->local->state->setValue($this->view->local['state']);
                    $this->userform->local->country->setValue($this->view->local['country']);
                    $this->userform->local->address_type->setValue($this->view->local['address_type']);
                }

                if (isset($this->view->billing)) {
                    $billing_address_id = $this->view->billing['address_id'];
                    $this->userform->billing->address_1->setValue($this->view->billing['address_1']);
                    $this->userform->billing->address_2->setValue($this->view->billing['address_2']);
                    $this->userform->billing->streat_address->setValue($this->view->billing['streat_address']);
                    $this->userform->billing->zipcode->setValue($this->view->billing['zipcode']);
                    $this->userform->billing->city->setValue($this->view->billing['city']);
                    $this->userform->billing->state->setValue($this->view->billing['state']);
                    $this->userform->billing->country->setValue($this->view->billing['country']);
                    $this->userform->billing->address_type->setValue($this->view->billing['address_type']);
                    $this->view->billing_diff_local = $this->view->billing['different_from_local'];
                    //var_dump($this->view->billing_diff_local);
                    //$this->userform->billing->country->setValue($this->view->billing['country'] ? $this->view->billing['country'] :223);
                }
            }
        }
        if (!$modify) {

            $status = 'active';
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            //try{
            //print_r($this->userform->getValues());
            if (!isset($_POST['billing']['address_type'])) {
                $this->userform->removeSubForm('billing');
            }
            if ($this->userform->isValid($request->getPost())) {
                $values = $this->userform->getValues();
                $user = new Hb_User();
                $userAvailable = $user->checkUsername($values['username'], $this->view->userid);
                if (empty($userAvailable)) {


                    //to upadte user details to authorize.net api

                    if ($values['username'] != $clientDetail['username']) {

                        $re = $this->client->getPaymentDetail($this->view->userid);


                        if ($re != false) {
                            require_once APPLICATION_PATH . '/../library/Hb/anet_php_sdk/AuthorizeNet.php'; // The SDK
                            $api_login_id = AUTHORIZENET_LOGIN_ID;
                            $transaction_key = AUTHORIZENET_TRAN_KEY;
                            $md5_setting = AUTHORIZENET_MD5_SETTING; // Your MD5 Setting

                            $var = new AuthorizeNetCIM();
                            $details = array();
                            $p_id = (int)$re[0]['authorizenet_profile_id'];
                            $user_id = $this->view->userid;
                            $res = $var->updateCustomerProfile($user_id, $p_id, $values['username']);

                            if ($res->xml->messages->resultCode != 'OK') {
                                $this->view->errorMessage = "Error updation user profile ,Please try again";
                            }

                            //to chek if autho account is created for the user
                        }
                    }
//echo "sdf";die;
//print_r($values);die;
                    // $values['profile_image'] = $_FILES['profile_image'];
                    if (!$modify) {
                        //echo "dsdf";die;
                        if ($this->client->create($values, $status)) {


                            $flag = 'address';
                        }
                    } else if ($this->client->modify($values, $this->view->userid)) {
                        // We're authenticated! Redirect to the home page
                        $flag = 'address';
                    }
                    if ($flag == 'address') {
                        if ($local_address_id > 0) {
                            $values = $_POST['local'];
                            $values['userid'] = $this->client->userid;
                            $values['address_type'] = 'local';

                            $this->client->updateAddress($values, $local_address_id);
                        } else {
                            $values = $_POST['local'];
                            $values['userid'] = $this->client->userid;
                            $values['address_type'] = 'local';
                            $this->client->addAddress($values);
                        }
                        $values = $_POST['billing'];

                        if (!isset($values['address_type'])) {
                            $values = $_POST['local'];
                        } else
                            $values['different_from_local'] = true;
                        $values['userid'] = $this->client->userid;
                        if ($billing_address_id > 0) {
                            $values['address_type'] = 'billing';


                            $this->client->updateAddress($values, $billing_address_id);
                        } else {
                            $values['address_type'] = 'billing';

                            $this->client->addAddress($values);
                        }
                        if ($modify)
                            $profile->successMessage = 'User details have been sucessfully modified';
                        else
                            $profile->successMessage = 'User have been sucessfully created.';
                        $this->_helper->redirector->gotoUrl(SITE_URL . 'client/profile/');
                    }
                } else {

                    $this->view->errorMessage = $this->userform->username->addError('Username is not available');
                }
            }
        }
        if (!isset($this->userform->billing))
            $this->userform->addSubForm($this->addressForm(), 'billing');
        $this->view->userform = $this->userform;
    }

    public function passwordAction()
    {

        $user = $this->client->getDetail($this->view->userid);

        if ($this->_request->isPost()) {
            if ($_POST['oldpassword'] == $user['password']) {

                if ($_POST['newpassword'] == $_POST['repeatpassword']) {
                    $this->client->changePassword($this->view->userid, $_POST['newpassword']);
                    $this->view->successMsg = 'Your password has been successfully changed!';
                } else {
                    $this->view->errorMsg = 'Confirm your password!';
                }
            } else {
                $this->view->errorMsg = 'Current Password is incorrect!';
            }
        }
    }

    public function modifyAction()
    {
        // action body
    }

    public function userForm()
    {
        $form = new Zend_Form();

        $form->setName("userform");
        $form->setMethod('post');
        $form->setAttrib('enctype', 'multipart/form-data');
        $form->setAction(SITE_URL . 'client/profile/create/new/');

        $form->addElement('text', 'firstname', array(
            'validators' => array(
                array('StringLength', false, array(0, 50)),
                array('NotEmpty', false, array('messages' => array('isEmpty' => 'Please fill first name'))),
            ),
            'required' => true,
            'label' => 'First Name:',
        ));
        $form->addElement('text', 'middlename', array(
            'validators' => array(
                array('StringLength', false, array(0, 50)),
            ),
            'required' => false,
            'label' => 'Middle Name:',
        ));
        $form->addElement('text', 'lastname', array(
            'validators' => array(
                array('StringLength', false, array(0, 100)),
                array('NotEmpty', false, array('messages' => array('isEmpty' => 'Please fill Last Name.'))),
            ),
            'required' => true,
            'label' => 'Last Name:',
        ));
        $form->addElement('text', 'username', array(
            'validators' => array(
                array('StringLength', false, array(0, 100)),
                array('EmailAddress', false),
            ),
            'required' => true,
            'label' => 'Username:',
        ));

        /* $form->addElement('file', 'profile_image', array(
          //'filters'    => array('StringTrim'),
          'validators' => array(
          array('Extension', false, 'jpg,png,gif'),
          ),
          'required'   => false,
          'label'      => 'Profile Image:',
          ));
         */
        $form->addElement('text', 'dob', array(
            'validators' => array(
                array('StringLength', false, array(0, 10)),
            ),
            'required' => false,
            'label' => 'Date of Birth:',
        ));
        $form->addElement('text', 'phone', array(
            'validators' => array(
                array('StringLength', false, array(0, 50)),
            ),
            'required' => true,
            'label' => 'Phone:',
        ));

        $form->addElement('text', 'sitter_fee', array(
            'validators' => array(
                array('StringLength', false, array(0, 50)),
            ),
            'required' => false,
            'label' => 'Sitter Fee:',
        ));
        $form->addElement('text', 'spouse_firstname', array(
            'validators' => array(
                array('StringLength', false, array(0, 50)),
            ),
            'required' => false,
            'label' => 'Spouse First Name:',
        ));
        $form->addElement('text', 'spouse_lastname', array(
            'validators' => array(
                array('StringLength', false, array(0, 50)),
            ),
            'required' => false,
            'label' => 'Spouse Last Name:',
        ));
        $form->addElement('text', 'local_phone', array(
            'validators' => array(
                array('StringLength', false, array(0, 20)),
            ),
            'required' => false,
            'label' => 'Local Phone:',
        ));
        $form->addElement('text', 'work_phone', array(
            'validators' => array(
                array('StringLength', false, array(0, 20)),
            ),
            'required' => false,
            'label' => 'Local Phone:',
        ));
        $form->addElement('text', 'emergency_contact', array(
            'validators' => array(
                array('StringLength', false, array(0, 50)),
            ),
            'required' => false,
            'label' => 'Local Phone:',
        ));
        $form->addElement('text', 'emergency_phone', array(
            'validators' => array(
                array('StringLength', false, array(0, 20)),
            ),
            'required' => false,
            'label' => 'Local Phone:',
        ));

        $form->addElement('submit', 'create', array(
            'required' => false,
            'ignore' => true,
            'label' => 'Create',
        ));

        return $form;
    }

    public function childrenAction()
    {


        // print_r($_POST);die;
        $this->cForm = $this->childForm();
        // echo $this->_request->getParam('parent');die();

        $parent_id = $this->view->userid;

        if ($this->searchParams['delete'] > 0) {
            $this->client->children->delete($this->searchParams['delete']);
            $this->_helper->redirector->gotoUrl($_SERVER['HTTP_REFERER']);
        }

        $this->cForm->setAction(SITE_URL . 'client/children/');

        if ($this->_request->getParam('modify') != '') {
            $this->view->child = $this->client->children->get($this->_request->getParam('modify'));


            if ($this->view->child) {
                $this->view->childId = $this->_request->getParam('modify');
                $child_id = $this->_request->getParam('modify');
                $this->cForm->setAction(SITE_URL . 'client/children/modify/' . $child_id);
                $this->cForm->child_name->setValue($this->view->child['child_name']);
                $this->cForm->dob->setValue($this->view->child['dob']);
                $this->cForm->allergy_status->setValue($this->view->child['allergy_status']);
                $this->cForm->allergies->setValue($this->view->child['allergies']);
                $this->cForm->medicator_status->setValue($this->view->child['medicator_status']);
                $this->cForm->medicator->setValue($this->view->child['medicator']);
                $this->cForm->notes->setValue($this->view->child['notes']);
                $this->cForm->special_needs->setValue($this->view->child['special_needs']);
                $this->cForm->fav_food->setValue($this->view->child['fav_food']);
                $this->cForm->fav_book->setValue($this->view->child['fav_book']);
                $this->cForm->fav_cartoon->setValue($this->view->child['fav_cartoon']);
                $this->cForm->sex->setValue($this->view->child['sex']);
            }
        }


        $status = 1;

        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($this->cForm->isValid($request->getPost())) {

                $_FILES['image']['tmp_name'] = $this->cForm->image->getFileName();
                $values = $this->cForm->getValues();
                //print_r($_FILES['image']);
                $values['image'] = $_FILES['image'];

                if ($values['special_needs'] != null || $values['special_needs'] != '')
                    $values['special_needs_status'] = 'Yes';
                else
                    $values['special_needs_status'] = 'No';

                if ($this->view->child) {
                    if ($this->client->children->modify($values, $this->view->userid, $this->view->child['child_id'])) {
                        $this->cForm->reset();
                        $_SESSION['message'] = "You have successfully updated your child’s profile ";

                        $this->_helper->redirector->gotoUrl(SITE_URL . 'client/children');
                    }
                } else{
                	if($_POST['job_id']!='')
                		$child_id=$this->client->children->addChildNew($values, $this->view->userid);
                	else
                		$child_id=$this->client->children->add($values, $this->view->userid);
                    
                    if ($child_id) {
                        // We're authenticated! Redirect to the home page
                       
                        if($_POST['job_id']!='')
                        {	
                        	$job_id=$_POST['job_id'];
                        	if($job_id!=0)
                        	{
                        		$this->jobs = new Hb_Jobs();
                        		$this->common = new Hb_Common();
                        		
                        		$this->client->children->addChildToJob($child_id,$job_id);
	                        	
                        		$job_info = $this->jobs->getJobInfo($job_id,true);

               				 	if( $values['special_needs_status'] == 'Yes')
               				 	{
                    				$update_data = array();
                    				$update_data['is_special'] = '1';
                    				$this->jobs->updateJob($job_id,$update_data);
                				}

                			// sitters children logic to update other info of children
                				$this->common->updateJobChildQunatityInfo($job_id,'client',$job_info->is_special);
                        		
                        	}
                        	
                        	$data = array();
                        	$data['status'] = "success";
                        	$data['message'] = "Child has been added to job successfully";
                        	echo json_encode($data);
                        	die;
                        }
                        
                        $_SESSION['message'] = "You have successfully created your child’s profile ";
                        $this->_helper->redirector->gotoUrl(SITE_URL . 'client/children');
                        $this->cForm->reset();
                    }
            	}
            }
        }
        $this->view->children = $this->client->children->getAll($parent_id);
        $this->view->childForm = $this->cForm;
    }

    public function childForm()
    {
        $form = new Zend_Form();

        $form->setName("userform");
        $form->setMethod('post');
        $form->setAttrib('enctype', 'multipart/form-data');

        $this->view->special_needs_list = $this->client->getSpecialNeeds();

        $form->addElement('text', 'child_name', array(
            'validators' => array(
                array('StringLength', false, array(0, 50)),
            ),
            'required' => true,
            'label' => 'Full Name:',
        ));

        $form->addElement('select', 'sex', array(
            'required' => true,
            'label' => 'Gender:',
            'multiOptions' => array('M' => 'Male', 'F' => 'Female'),
        ));
        $form->addElement('text', 'dob', array(
            'validators' => array(
                array('StringLength', false, array(0, 10)),
            ),
            'required' => true,
            'label' => 'Date of Birth:',
        ));
        $form->addElement('textarea', 'allergy_status', array(
            'required' => false,
            'label' => 'Allergies:',
        ));
        $form->addElement('textarea', 'allergies', array(
            'required' => false,
            'label' => 'Allergies:',
        ));
        $form->addElement('text', 'medicator_status', array(
            'required' => false,
            'label' => 'Medication:',
        ));
        $form->addElement('text', 'medicator', array(
            'required' => false,
            'label' => 'Medication:',
        ));

        $form->addElement('select', 'special_needs', array(
            'required' => false,
            'label' => 'Special Needs:',
            'multiOptions' => $this->view->special_needs_list
        ));
        $form->addElement('text', 'fav_food', array(
            'required' => false,
            'label' => 'Favorite Food:',
        ));

        $form->addElement('text', 'fav_book', array(
            'required' => false,
            'label' => 'Favorite Book:',
        ));

        $form->addElement('text', 'fav_cartoon', array(
            'required' => false,
            'label' => 'Favorite Cartoon:',
        ));

        $form->addElement('file', 'image', array(
            //'filters'    => array('StringTrim'),
            'validators' => array(
                array('Extension', false, 'jpg,png,gif,jpeg'),
            ),
            'required' => false,
            'label' => 'Image:',
        ));

        $form->addElement('textarea', 'notes', array(
            'required' => false,
            'label' => 'Notes:',
        ));

        $form->addElement('submit', 'Create', array(
            'required' => false,
            'ignore' => true,
            'label' => 'Add',
        ));

        $field = $form->getElement('special_needs');
        $field->setRegisterInArrayValidator(false);

        return $form;
    }

    public function updatechildjobAction()
    {
        $this->jobs = new Hb_Jobs();
        $this->common = new Hb_Common();

        $request = $this->getRequest();
        if ($request->isPost()) {

            $job_id = $request->getPost('job_id');
            $child_id = $request->getPost('child_id');

            // check job exists or not
            $job_info = $this->jobs->isJobExists($job_id);
            if($job_info == false)
            {
                $data = array();
                $data['status'] = "error";
                $data['message'] = "Job not exists more.";
                echo json_encode($data);
                exit;
            }

            $job_info = $this->jobs->getJobInfo($job_id);
            if($job_info == false || empty($job_info))
            {
                $data = array();
                $data['status'] = "error";
                $data['message'] = "Job not exists more.";
                echo json_encode($data);
                exit;
            }

            if(in_array($job_info->job_status,array('cancelled','completed','closed')))
            {
                $data = array();
                $data['status'] = "error";
                $data['message'] = "Job is already ".$job_info->job_status.'.';
                echo json_encode($data);
                exit;
            }



            // check children exists or not
            $child_info = $this->client->children->isChildExists($child_id);
            if($child_info == false)
            {
                $data = array();
                $data['status'] = "error";
                $data['message'] = "Child not exists more.";
                echo json_encode($data);
                exit;
            }

            if( $child_info->special_needs_status == 'Yes')
            {
                $update_data = array();
                $update_data['is_special'] = '1';
                $this->jobs->updateJob($job_id,$update_data);
            }


            $this->jobs->addChilds($job_id,array("0"=>$child_id) );

            // sitters children logic to update other info of children
            $this->common->updateJobChildQunatityInfo($job_id,'client',$job_info->is_special);

            //$children = $this->jobs->getChildren($job_id);
            //$updated_child_count = count($children);
            //$this->client->update_child_count($job_id, $updated_child_count);


            $data = array();
            $data['status'] = "success";
            $data['message'] = "Child has been added to job successfully";
            echo json_encode($data);
            exit;
        }

        $data = array();
        $data['status'] = "error";
        $data['message'] = "Something went wrong, please refresh page and try again.";
        echo json_encode($data);
        exit;
    }
    public function checkuserAction()
    {
        // action body

        $this->_helper->layout->disableLayout();
        echo 'false';
    }

    public function detailAction()
    {
        // action body
    }

    public function addressAction()
    {
        // action body


        $this->adForm = $this->addressForm();
        // echo Zend_Auth::getInstance()->getIdentity()->userid;die();
        if ($this->_request->getParam('userid') != '')
            $userid = $this->_request->getParam('userid');

        if ($userid == '')
            $this->_helper->redirector('index', 'index');
        else {
            $this->view->userid = $userid;
        }

        $this->adForm->setAction(SITE_URL . 'client/address/userid/' . $userid);

        if ($this->_request->getParam('modify') != '') {
            $this->view->address = $this->client->getAddress(array('userid' => $this->view->userid, 'address_id' => $this->_request->getParam('modify')));
            $this->view->address = $this->view->address[0];
            if (isset($this->view->address)) {
                $address_id = $this->_request->getParam('modify');
                $this->adForm->setAction(SITE_URL . 'client/address/userid/' . $userid . '/modify/' . $address_id);
                $this->adForm->address_1->setValue($this->view->address['address_1']);
                $this->adForm->address_2->setValue($this->view->address['address_2']);
                $this->adForm->streat_address->setValue($this->view->address['streat_address']);
                $this->adForm->zipcode->setValue($this->view->address['zipcode']);
                $this->adForm->city->setValue($this->view->address['city']);
                $this->adForm->state->setValue($this->view->address['state']);
                $this->adForm->country->setValue($this->view->address['country']);
                $this->adForm->address_type->setValue($this->view->address['address_type']);
            }
        }


        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($this->adForm->isValid($request->getPost())) {
                $values = $this->adForm->getValues();
                $values['userid'] = $this->view->userid;
                if ($address_id > 0) {
                    $this->client->updateAddress($values, $address_id);
                } else {
                    $this->client->addAddress($values);
                }
            }
        }
        $this->view->addresses = $this->client->getAddress(array('userid' => $this->view->userid));
        $this->view->addressForm = $this->adForm;
    }

    public function addressForm($options = array('country' => 223))
    {
        $form = new Zend_Form();

        $form->setName("addressform");
        $form->setMethod('post');
        $form->setAttrib('enctype', 'multipart/form-data');

        $form->addElement('text', 'address_1', array(
            'validators' => array(
                array('StringLength', false, array(0, 150)),
            ),
            'required' => false,
            'label' => 'Address 1:',
            'class' => 'txt',
        ));
        $form->addElement('text', 'address_2', array(
            'validators' => array(
                array('StringLength', false, array(0, 150)),
            ),
            'required' => false,
            'label' => 'Address 2:',
            'class' => 'txt',
        ));
        $form->addElement('text', 'billing_name', array(
            'validators' => array(
                array('StringLength', false, array(0, 100)),
            ),
            'required' => false,
            'label' => 'Billing Name:',
            'class' => 'txt',
        ));
        $form->addElement('text', 'streat_address', array(
            'validators' => array(
                array('StringLength', false, array(0, 255)),
            ),
            'required' => true,
            'label' => 'Streat Address:',
            'class' => 'txt',
        ));
        $form->addElement('text', 'zipcode', array(
            'validators' => array(
                array('StringLength', false, array(0, 10)),
            ),
            'required' => true,
            'label' => 'Zip Code:',
            'class' => 'txt',
        ));
        $form->addElement('text', 'city', array(
            'validators' => array(
                array('StringLength', false, array(0, 100)),
            ),
            'required' => true,
            'label' => 'City:',
            'class' => 'txt',
        ));
        $this->hbSettings = new Hb_Settings();
        $this->view->states = $this->hbSettings->getStates($options['country']);
        foreach ($this->view->states as $state) {
            $states[$state['zone_id']] = $state['name'];
        }

        $this->view->countries = $this->hbSettings->getCountries();
        foreach ($this->view->countries as $country) {
            $countries[$country['country_id']] = $country['name'];
        }
        $form->addElement('select', 'state', array(
            'MultiOptions' => $states,
            'required' => true,
            'label' => 'State:',
        ));
        $form->addElement('select', 'country', array(
            'MultiOptions' => $countries,
            'required' => false,
            'label' => 'Country:',
        ));
        $form->addElement('checkbox', 'address_type', array(
            'MultiOptions' => array('billing' => 'Billing', 'other' => 'Other'),
            'required' => false,
            'label' => 'Address Type:',
        ));


        $form->addElement('submit', 'addressSubmit', array(
            'required' => false,
            'ignore' => true,
            'label' => 'Add Address',
        ));

        return $form;
    }

    public function buycreditsAction()
    {
        $this->view->subscribeForm = $this->subscribeForm();
        $this->view->actionName = 'subscription';
        $this->view->subscribeForm->start_date->setValue(date(DATE_FORMAT, strtotime('+1 day 9am')));
        $this->view->subscribeForm->end_date->setValue(date(DATE_FORMAT, strtotime('+1 year 6pm')));

        $this->packages = new Hb_Packages();
        $this->view->packages = $this->packages->search();
        //print_r($this->view->packages);


        if ($this->_request->getParam('modify') != '') {
            $this->view->subscribe = $this->client->getSubscription(array('userid' => $this->view->userid, 'sub_id' => $this->_request->getParam('modify')));
            //print_r($this->view->subscribe);
            $this->view->subscribe = $this->view->subscribe['rows'][0];
            if (isset($this->view->subscribe)) {
                //	die();
                $sub_id = $this->_request->getParam('modify');
                $this->view->subscribeForm->setAction(SITE_URL . 'client/buycredits/client/' . $this->view->userid . '/modify/' . $sub_id);
                $this->view->subscribeForm->slots->setValue($this->view->subscribe['slots']);
                $this->view->subscribeForm->price->setValue($this->view->subscribe['price']);
                $this->view->subscribeForm->start_date->setValue(date(DATE_FORMAT, strtotime($this->view->subscribe['start_date'])));
                $this->view->subscribeForm->end_date->setValue(date(DATE_FORMAT, strtotime($this->view->subscribe['end_date'])));
                $this->view->subscribeForm->notes->setValue($this->view->subscribe['notes']);
                $this->view->selectedPackage = $this->view->subscribe['package_id'];
            }
        }


        ##check whether profile is created on authorize.net
        $this->view->paymentProfile =   $this->client->getClientPaymentInfo($this->view->userid);

    }

    public function subscriptionAction()
    {


        $this->view->subscriptions = $this->client->getSubscription(array_merge(array('userid' => $this->view->userid), $this->searchParams));
        $this->view->paginator = Zend_Paginator::factory(range(1, ((int)$this->view->subscriptions['total'] - 1)));
        $this->view->subscriptions = $this->view->subscriptions['rows'];

        $this->view->paginator->setCurrentPageNumber((int)$this->searchParams['page'] ? $this->searchParams['page'] : 1);
        $this->view->paginator->setItemCountPerPage((int)$this->searchParams['rows'] ? $this->searchParams['rows'] : 10);
        $this->view->paginator->setPageRange(5);

        if ($this->searchParams['success'] == 'payment') {

            $purchasedSub = $this->searchParams['subs'];
            foreach ($this->view->subscriptions as $s) {
                $sub[$s['client_sub_id']] = $s;
            }
            $purchasedSub = $sub[$purchasedSub];
            $this->view->successMessage = 'You have successfully purchased ' . $purchasedSub['slots'] . ' credits for ' . $purchasedSub['price'] . 'USD. Transction ID: ' . $purchasedSub['transaction_id'];
            $checkout = new Zend_Session_Namespace('checkout');


            unset($checkout->selectedPackage);
        }
    }

    public function subscribeForm()
    {

        $form = new Zend_Form();

        $form->setName("subscribeform");
        $form->setMethod('post');
        $form->setAttrib('enctype', 'multipart/form-data');

        $form->addElement('text', 'slots', array(
            'required' => true,
            'label' => 'Credits',
        ));
        $form->addElement('text', 'price', array(
            'validators' => array(
                array('float', false),
            ),
            'required' => true,
            'label' => 'Amount',
        ));
        $form->addElement('text', 'start_date', array(
            'required' => false,
            'label' => 'Start From:',
        ));
        $form->addElement('text', 'end_date', array(
            'required' => false,
            'label' => 'Expire On:',
        ));
        $form->addElement('textarea', 'notes', array(
            'required' => false,
            'label' => 'Notes:',
        ));


        $form->addElement('submit', 'subscribesubmit', array(
            'required' => false,
            'ignore' => true,
            'label' => 'Subscribe',
        ));

        return $form;
    }

    public function listeventsAction()
    {
        // echo "d";die;
        $this->jobs = new Hb_Jobs();
        $this->client = new Hb_Client();

        if (Zend_Auth::getInstance()->getIdentity()->userid != '')
            $client = Zend_Auth::getInstance()->getIdentity()->userid;

        if ($client == '')
            $this->_helper->redirector('index', 'index');
        else {
            $this->view->userid = $client;

            $this->view->credits = $this->jobs->checkCredits($this->view->userid);
        }
        $this->searchParams['client_id'] = $this->view->userid;


        if ($this->searchParams['listevents'] == 'history') {
            $this->searchParams['status'] = array('completed', 'cancelled');
            $this->view->events = $this->jobs->search($this->searchParams);
        } else {

            $this->searchParams['status'] = array('new', 'pending', 'confirmed');
            $this->view->events = $this->jobs->search($this->searchParams);
        }

        $this->events['total'] = $this->view->events['total'];
        $this->view->events = $this->view->events['rows'];
        // echo count($this->view->events);
        //range(1, $this->events['total'] - 1);

        $this->view->paginator = Zend_Paginator::factory(range(1, $this->events['total']));

        $this->view->paginator->setCurrentPageNumber((int)$this->searchParams['page'] ? $this->searchParams['page'] : 1);
        $this->view->paginator->setItemCountPerPage((int)$this->searchParams['rows'] ? $this->searchParams['rows'] : 10);
        $this->view->paginator->setPageRange(10);
    }

    public function eventsAction()
    {


        $time = true;
        $payment_information = new Zend_Session_Namespace('payment_information');
        $payment = $payment_information->payment_information;

        $job_information = new Zend_Session_Namespace('job_information');
        $job = $job_information->job_information;

        if ((isset($payment_information->payment_information)) && (!empty($payment_information->payment_information))) {
            unset($payment_information->payment_information);
        }

        if ((isset($job_information->job_information)) && (!empty($job_information->job_information))) {
            unset($job_information->job_information);
        }
        //print_r($job_information->job_info);die;
        $child_flag = true;
        $this->jobs = new Hb_Jobs();
        if ($this->_request->getParam('saverate') == 'rate') {//echo 'here';die();
            $this->_helper->layout->disableLayout();
            $this->jobs->saveRate($this->_request->getParam('job_id'), $this->_request->getParam('rate'));
            echo json_encode(array('msg' => $msg));
            die();
        }
        $this->view->eventsForm = $this->eventsForm();
        $this->view->selectedChild = array();
        $this->view->eventsForm->addSubForm($this->addressForm(), 'other');

        $this->hbSettings = new Hb_Settings();
        $this->view->preferences = $this->hbSettings->getPreferences(true);
        $this->view->jobPreference = array();

        $this->view->eventsForm->start_date->setValue(date(DATETIME_FORMAT, strtotime('+1 day 9am')));
        $this->view->eventsForm->end_date->setValue(date(DATETIME_FORMAT, strtotime('+1 day 6pm')));
        if (Zend_Auth::getInstance()->getIdentity()->userid != '')
            $client = Zend_Auth::getInstance()->getIdentity()->userid;

        if ($client == '')
            $this->_helper->redirector('index', 'index');
        else {
            $this->view->userid = $client;

            if ($this->searchParams['delete'] > 0) {
                $this->jobs->delete($this->searchParams['delete']);
				$_SESSION['cancelJob']="Thanks for notifing us of your cancellation. Keep us in mind for your future needs.";
                $this->_helper->redirector->gotoUrl($_SERVER['HTTP_REFERER']);
            }

            $start_date = $this->_request->getParam('start_date');
            $end_date = $this->_request->getParam('end_date');

            $creditsArray = $this->jobs->calculateCredits($this->view->userid, $start_date, $end_date, $this->_request->getParam('modify'));
            //$creditsArray =array('available_credits'=>$this->view->credits,'calculated_credits'=>$this->view->calculatedCredits,'required_credits'=>$required_credits);
            $this->view->credits = $creditsArray['available_credits'];
            $this->view->calculatedCredits = $creditsArray['calculated_credits'];
            $required_credits = $creditsArray['required_credits'];

            if ($this->_request->getParam('check') == 'credits') {//echo 'here';die();
                $this->_helper->layout->disableLayout();

                echo json_encode($creditsArray);
                die();
            }
        }
        $this->view->eventsForm->setAction(SITE_URL . 'client/events/');

        $this->view->addresses = $this->client->getAddress(array('userid' => $this->view->userid, 'address_type' => array('local', 'other')));

        $this->view->children = $this->client->children->getAll($this->view->userid);


        if ($this->_request->getParam('modify') != '') {


            $this->view->event = $this->jobs->search(array('job_id' => $this->_request->getParam('modify')));
            $this->view->jobPreference = $this->jobs->getPreferences($this->_request->getParam('modify'));
            $this->view->event = $this->view->event['rows'][$this->_request->getParam('modify')];

            //to pass all children with respect to the job// by namrata

            $children_of_jobs = $this->jobs->get_all_child_of_jobs($this->_request->getParam('modify'));
            if (empty($children_of_jobs)) {
                $this->view->children = $this->client->children->getAll($this->view->userid);
            } else {
                $common_value = array_merge($this->view->children, $children_of_jobs);

                $this->view->children = $common_value;
            }

            /*--------------------------*/

            if (isset($this->view->event)) {
                $job_id = $this->_request->getParam('modify');
                $modify = true;
                $this->view->selectedChild = $this->jobs->getChildren($job_id);
                $this->view->selectedAddress = $this->view->event['address_id'];
                $this->view->eventsForm->setAction(SITE_URL . 'client/events/modify/' . $job_id);
                $this->view->eventsForm->start_date->setValue($this->view->event['job_start_date']);
                $this->view->eventsForm->end_date->setValue($this->view->event['job_end_date']);
                $this->view->eventsForm->notes->setValue($this->view->event['notes']);
                $this->view->eventsForm->special_need->setValue($this->view->event['special_need']);
            }
        } else
            $modify = false;

        $request = $this->getRequest();
        if ($request->isPost() ) {
            if ($_POST['address_id'] != 'other') {
                $this->view->eventsForm->removeSubForm('other');
            }

            //  print_r($creditsArray['calculated_credits']);die;
            if ($this->view->eventsForm->isValid($request->getPost())) {


                if ($creditsArray['calculated_credits'] > 0) {

                    $values = $this->view->eventsForm->getValues();

                    $modify_start_date = date('Y-m-d', strtotime($values['start_date'])) == date('Y-m-d', strtotime($this->view->event['job_start_date']));
                    $modify_end_date = date('Y-m-d', strtotime($values['end_date'])) == date('Y-m-d', strtotime($this->view->event['job_end_date']));
                    $continue_flag = false;
                    $clild_flag = true;
                    if (!$modify) {
                        $continue_flag = true;
                    } elseif ($modify_start_date && $modify_end_date) {
                        $continue_flag = true;
                    }
                    if (count($_POST['child']) <= 0) {

                        // $this->view->eventsForm->child_error->addError('Must add child’s profile to complete job request ');

                        $clild_flag = false;
                    }
                    if ($continue_flag && $clild_flag) {
                        $values['userid'] = $this->view->userid;

                        $values['children'] = $_POST['child'];


                        if ($_POST['address_id'] == 'other') {
                            $address_values = $this->view->eventsForm->other->getValues();
                            $address_values['userid'] = $this->view->userid;
                            $address_values['address_type'] = 'other';
                            $address_id = $this->client->addAddress($address_values);
                        } else
                            $address_id = $_POST['address_id'];


                        $values['address_id'] = $address_id;
                        $values['prefer'] = $_POST['prefer'];
                        if ($job_id > 0) {


                            $time1 = date('H:i:s', strtotime($values['start_date']));
                            $time2 = date('H:i:s', strtotime($values['end_date']));

                            $time1 = strtotime("1980-01-01 $time1");
                            $time2 = strtotime("1980-01-01 $time2");

                            $hourdiff = date("H", strtotime("1980-01-01 00:00:00") + ($time2 - $time1));


                            if (($hourdiff < 3) && ($hourdiff >= 0)) {
                                $this->view->eventsForm->end_date->addError('Job should be of minimum 3 hours');
                                $this->view->eventsForm->end_date->setValue($end_date);
                            } else {

                                $values['job_status'] = $this->view->event['job_status'];

                                $values['children'] = $_POST['child'];


                                $previos_child_count = $this->jobs->get_previos_child_count($job_id);

                                $updated_child_count = count($values['children']);

                                if ($previos_child_count != $updated_child_count) {
                                    $this->jobs->add_job_log($job_id, $previos_child_count, $updated_child_count);
                                    $values['update'] = 1;
                                } else
                                    $values['update'] = 0;


                                $values['actual_child_count'] = count($values['children']);

                                //to upate sitter rate/client rate if children countchangesd
                                $rate_data = $this->jobs->get_job_child_rate($values['actual_child_count']);


                                if (!empty($rate_data)) {
                                    $values['sitter_rate_pre'] = $rate_data[0]['sitter_rate'];
                                    $values['rate'] = $rate_data[0]['sitter_rate'];
                                    $values['client_rate'] = $rate_data[0]['client_rate'];
                                    $values['client_updated_rate'] = $rate_data[0]['client_rate'];;
                                }
                                $this->jobs->update($values, $job_id);
                                //to send notifiation to sitter if a job is modified

                                /*  $results = $this->jobs->get_device_token_sitter($job_id);

                                  if (!empty($results['sitter_user_id'])) {

                                  $noti_data=array();
                                  $noti_data['job_id']=$job_id;

                                  $noti_data['userid']=$results['sitter_user_id'];
                                  $noti_data['date_added']=date('Y-m-d H:i:s');
                                  $noti_data['date_updated']=date('Y-m-d H:i:s');

                                  $this->notification->insert_notification_data($noti_data);

                                  //get unread notification count
                                  $count=$this->notification->get_unread_count($results['sitter_user_id']);

                                  $this->ipush = new Hb_Ipushnotification();
                                  $message = "Job has been modified by the client";

                                  foreach ($results as $noti) {

                                  $this->ipush->send_notification_sitter($noti['deviceToken'], $message, $job_id,$results['sitter_user_id'],"",$count);
                                  }
                                  } */

                                $flag = true;
                            }
                        } else {


                            $date1 = $start_date;
                            $date2 = $end_date;

                            $time1 = date('H:i:s', strtotime($date1));
                            $time2 = date('H:i:s', strtotime($date2));

                            $time1 = strtotime("1980-01-01 $time1");
                            $time2 = strtotime("1980-01-01 $time2");


                            $hourdiff = date("H", strtotime("1980-01-01 00:00:00") + ($time2 - $time1));


                            if (($hourdiff < 3) && ($hourdiff >= 0)) {
                                $this->view->eventsForm->end_date->addError('Job should be of minimum 3 hours');
                                $this->view->eventsForm->end_date->setValue($end_date);
                            } else {////n
                                $updated_child_count = count($values['children']);

                                $values['child_count'] = $updated_child_count;
                                $values['actual_child_count'] = $updated_child_count;

                                $values['job_status'] = 'new';
                                $rate_data = $this->jobs->get_job_child_rate($updated_child_count);

                                if (!empty($rate_data)) {
                                    $values['sitter_rate_pre'] = $rate_data[0]['sitter_rate'];
                                    $values['rate'] = $rate_data[0]['sitter_rate'];
                                    $values['client_rate'] = $rate_data[0]['client_rate'];
                                    $values['client_updated_rate'] = $rate_data[0]['client_rate'];;
                                }

                                $val = $this->jobs->create($values, $this->view->userid);
                                $flag = true;
                            } //n
                        }
                    } else {


                        if ($modify == true) {

                            if (!$clild_flag) {
                                $this->view->childError = 'Please select children to add Job';
                            } else {


                                $this->view->eventsForm->start_date->addError('Updating the date is not possible. Instead You can modify time');
                                $this->view->eventsForm->start_date->setValue($this->view->event['job_start_date']);
                                $this->view->eventsForm->end_date->addError('Updating the date is not possible. Instead You can modify time');
                                $this->view->eventsForm->end_date->setValue($this->view->event['job_end_date']);
                            }
                        } else {
                            if (!$clild_flag) {

                                //echo "adsa";die;
                                $this->view->childError = 'Please select children to add Job';


                            }
                        }
                    }
                } else {


                    //namrata for purchase page
                    //case if new job is created
                    $values = $this->view->eventsForm->getValues();

                    $time1 = date('H:i:s', strtotime($values['start_date']));
                    $time2 = date('H:i:s', strtotime($values['end_date']));

                    $time1 = strtotime("1980-01-01 $time1");
                    $time2 = strtotime("1980-01-01 $time2");

                    $hourdiff = date("H", strtotime("1980-01-01 00:00:00") + ($time2 - $time1));

                    //   print_r($hourdiff);die;
                    if (($hourdiff < 3) && ($hourdiff > 0)) {


                        $this->view->eventsForm->end_date->addError('Job should be of minimum 3 hours');
                        $this->view->eventsForm->end_date->setValue($end_date);
                    } else {


                        $modify_start_date = date('Y-m-d', strtotime($values['start_date'])) == date('Y-m-d', strtotime($this->view->event['job_start_date']));
                        $modify_end_date = date('Y-m-d', strtotime($values['end_date'])) == date('Y-m-d', strtotime($this->view->event['job_end_date']));
                        $continue_flag = true;
                        $clild_flag = true;

                        if (count($_POST['child']) <= 0) {
                            $clild_flag = false;
                        }


                        if ($clild_flag) {
                            $values['userid'] = $this->view->userid;

                            $values['children'] = $_POST['child'];


                            if ($_POST['address_id'] == 'other') {
                                $address_values = $this->view->eventsForm->other->getValues();
                                $address_values['userid'] = $this->view->userid;
                                $address_values['address_type'] = 'other';
                                $address_id = $this->client->addAddress($address_values);
                            } else
                                $address_id = $_POST['address_id'];


                            $values['address_id'] = $address_id;
                            $values['prefer'] = $_POST['prefer'];
                            if ($job_id > 0) {
                                $values['job_status'] = $this->view->event['job_status'];

                                //print_r($values);die;
                                $values['children'] = $_POST['child'];


                                $values['actual_child_count'] = count($values['children']);

                                //to upate sitter rate/client rate if children countchangesd
                                $rate_data = $this->jobs->get_job_child_rate($values['actual_child_count']);


                                if (!empty($rate_data)) {
                                    $values['sitter_rate_pre'] = $rate_data[0]['sitter_rate'];
                                    $values['rate'] = $rate_data[0]['sitter_rate'];
                                    $values['client_rate'] = $rate_data[0]['client_rate'];
                                    $values['client_updated_rate'] = $rate_data[0]['client_rate'];;
                                }
                            }


                            $payment_info = array();

                            $today_date = date('m/d/y h:i a');
                            $hourdiff = round((strtotime($values['start_date']) - strtotime($today_date)) / 3600, 1);

                            //print_r($hourdiff);die;

                            $available_credits = $creditsArray['available_credits'];
                            $num_of_days = $creditsArray['required_credits'];

                            if ($available_credits <= 0) {
                                if ($hourdiff <= 3) {
                                    $payment_info['is_immidiate'] = 'yes';
                                    $payment_info['amount'] = 35 + (25 * ($num_of_days - 1));
                                    $payment_info['add_credit'] = $num_of_days;
                                } else {
                                    $payment_info['is_immidiate'] = 'no';
                                    $payment_info['amount'] = (25 * $num_of_days);
                                    $payment_info['add_credit'] = $num_of_days;
                                }
                            } else {

                                $payment_info['is_immidiate'] = 'no';
                                $less_credits = $creditsArray['required_credits'] - $available_credits;
                                $payment_info['amount'] = 25 * $less_credits;
                                $payment_info['add_credit'] = $less_credits;
                            }
                            $payment_info['days'] = $num_of_days;

                            $payment_info['credits'] = $available_credits;
                            $payment_info['credits'] = $available_credits;

                            Zend_Session::start();
                            $payment_information = new Zend_Session_Namespace('payment_information');
                            $payment_information->payment_information = $payment_info;

                            $job_information = new Zend_Session_Namespace('job_information');
                            $job_information->job_information = $values;
                            $this->_helper->redirector->gotoUrl(SITE_URL . 'client/jobbookingcharge');

                            $flag = false;
                            $this->view->errorMessageCredits = '<p>You are going to book for <strong>' . $creditsArray['required_credits'] . ' day(s).</strong></p><p>You don\'t have enough credits to book you will be charged $35 for immediate booking and $25 for normal booking per job</p>';
                        } else {

                            if (!$clild_flag) {
                                $this->view->childError = 'Please select children to add Job';
                            }
                        }


                        // $this->view->eventsForm->end_date->addError('Credits not matching with End date.');
                    }
                }
            }

        }
        if ($flag === true) {

            if ($modify) {
                $_SESSION['jobSuccess'] = $this->view->globalMessages['JobUpdatedSuccess'];
            } else {
                $_SESSION['jobSuccess'] = $this->view->globalMessages['JobCreatedSuccess'];
                $job_id = $this->jobs->job_id;
            }
            $this->_helper->redirector->gotoUrl(SITE_URL . 'client/events/modify/' . $job_id);
        }

        if (!isset($this->view->eventsForm->other))
            $this->view->eventsForm->addSubForm($this->addressForm(), 'other');
    }

    public function eventsForm()
    {
        $form = new Zend_Form();

        $form->setName("eventsform");
        $form->setMethod('post');
        $form->setAttrib('enctype', 'multipart/form-data');

        $this->view->special_needs_list =$this->client->getSpecialNeeds();
        
        $form->addElement('text', 'start_date', array(
            'required' => false,
            'label' => 'Start From:',
        ));
        $form->addElement('text', 'end_date', array(
            'required' => false,
            'label' => 'End Date:',
        ));
        $form->addElement('textarea', 'notes', array(
            'required' => false,
            'label' => 'Notes:',
        ));
       $form->addElement('text', 'special_need', array(
        		'required' => false,
        		'label' => 'Special Needs:',
        ));

        $form->addElement('submit', 'eventsubmit', array(
            'required' => false,
            'ignore' => true,
            'label' => 'Create Events',
        ));

        $form->addElement('select', 'special_needs', array(
        		'required' => false,
        		'label' => 'Special Needs:',
        		'multiOptions' => $this->view->special_needs_list
        ));
        
        $form->addElement('select', 'sex', array(
        		'required' => false,
        		'label' => 'Gender:',
        		'multiOptions' => array('M'=>'Male','F'=>'Female'),
        ));
        
        $field=$form->getElement('special_needs');
        $field->setRegisterInArrayValidator(false);
        return $form;
    }

    public function assignAction()
    {
        $this->jobs = new Hb_Jobs();

        $this->job_id = $this->view->job_id = $this->job_id = $this->_request->getParam('modify');
        $this->view->userid = Zend_Auth::getInstance()->getIdentity()->userid;


        $request = $this->getRequest();
        if ($request->isPost() && $this->job_id > 0 && !empty($_POST['sitter']) && $this->searchParams['sendto'] == 'sitter') {
            // print_r($_POST['sitter']);die();
            $this->jobs->sendToSitters(array($this->job_id), $_POST['sitter']);
            $this->view->sentJobs = $this->jobs->getJobsSentSitters($this->job_id);
            if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $this->searchParams['sendto'] == 'sitter') {
                $this->_helper->layout->disableLayout();
            }
        }
        $this->hbSettings = new Hb_Settings();
        $this->view->preferences = $this->hbSettings->getPreferences();

        if ((int)$this->job_id <= 0) {
            $this->_helper->redirector->gotoUrl(SITE_URL . 'client/events/');
        }
        $this->view->job_id = $this->job_id;
        $this->client = new Hb_Client();
        $this->sitter = new Hb_Sitter();
        $this->view->event = $this->jobs->search(array('job_id' => $this->job_id));
        $this->view->event = $this->view->event['rows'][$this->job_id];
        $this->view->userid = $this->view->event['userid'];

        $this->view->addresses = $this->client->getAddress(array('userid' => $this->view->userid, 'address_type' => array('local', 'other')));

        $this->view->children = $this->client->children->getAll($this->view->userid);
        $this->view->selectedChild = $this->jobs->getChildren($this->job_id);
        $this->view->selectedAddress = $this->view->event['address_id'];
        if ($this->view->event['sitter_user_id'] > 0)
            $this->view->selectedSitter = $this->sitter->getDetail($this->view->event['sitter_user_id']);
        $this->view->sentJobs = $this->jobs->getJobsSentSitters($this->job_id);

        $this->view->jobsPreferences = $this->jobs->getPreferences($this->job_id);
        //print_r($this->view->jobsPreferences);

        $prefer = array();
        if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $this->searchParams['search'] == 'sitter') {
            $this->_helper->layout->disableLayout();
        }
        if ($request->isPost() && is_array($this->searchParams['prefer'])) {
            $prefer = $this->searchParams['prefer'];
            $prefer = array_combine($prefer, $prefer);
        } else
            foreach ($this->view->jobsPreferences as $group)
                foreach ($group['prefer'] as $p)
                    $prefer[$p['prefer_id']] = $p['prefer_id'];
        //print_r($prefer);
        $this->view->sitters = $this->sitter->searchByPreference($prefer, $this->view->job_id); //$this->sitter->search();
        //print_r($this->view->preferences);
        foreach ($this->view->preferences as $group)
            foreach ($group['prefer'] as $p)
                $this->prefer[$p['prefer_id']] = $p['prefer_name'];
        //var_dump($this->prefer);
        $this->view->jobPreferSearch = array_intersect_key($this->prefer, $prefer);
        //print_r($this->view->jobPreferSearch);
        //$this->view->prefer =array_combine($prefer,$prefer);
        //  echo '<pre>'; print_r($this->view->sitters);
        // print_r($_SERVER);die();
    }

    public function checkoutAction()
    {


        $checkout = new Zend_Session_Namespace('checkout');


        $this->view->subscribeForm = $this->subscribeForm();
        $this->view->actionName = 'subscription';
        $this->view->subscribeForm->start_date->setValue(date(DATE_FORMAT, strtotime('+1 day 9am')));
        $this->view->subscribeForm->end_date->setValue(date(DATE_FORMAT, strtotime('+1 year 6pm')));

        $this->packages = new Hb_Packages();
        $this->view->packages = $this->packages->search();
        //print_r($this->view->packages);

        $this->view->addresses = $this->client->getAddress(array('userid' => $this->view->userid, 'address_type' => array('billing')));

        $this->view->addresses = $this->view->addresses[0];

##check whether profile is created on authorize.net
        $this->view->paymentProfile =   $this->client->getClientPaymentInfo($this->view->userid);
        
        //echo '<pre>';print_r($this->view->addresses);
        $request = $this->getRequest();
        if ($request->isPost()) {

            $this->view->selectedPackage = $this->searchParams['package'];
            $checkout->selectedPackage = $this->searchParams['package'];



            ## Added By rati to handle customer payment way
            if ($this->_request->getParam('buy') != '')
            {
                $post_data = $this->searchParams;

                $post_data['x_customer_ip'] = $this->getRequest()->getClientIp(true);
                $post_data['amount'] = $amount =  $this->view->packages[$this->searchParams['package']]['price'];

                if(empty($amount))
                {
                    $flashMessenger = $this->_helper->getHelper('FlashMessenger');
                    $flashMessenger->addMessage(array('error'=>'Invlaid package, Please select again.'));
                    $this->_helper->redirector->gotoUrl(SITE_URL . 'client/buycredits/' );
                }

                $transInfo = $this->common->processPayment($this->view->userid,$post_data);
                $is_bool = $this->common->parseResponse($transInfo);

                if($transInfo!= false && $is_bool == TRUE)
                {

                    $data = array(
                        'userid' => $this->view->userid,
                        'slots' => $this->view->packages[$this->searchParams['package']]['credits'],// $pack->credits,
                        'total_credits' => $this->view->packages[$this->searchParams['package']]['credits'],
                        'price' => $this->view->packages[$this->searchParams['package']]['price'],
                        'start_date' => date('Y-m-d H:i:s', strtotime('+1 day 9am')),
                        'end_date' => date('Y-m-d H:i:s', strtotime('+1 year 6pm')),
                        'last_modified_by' => $this->view->userid,
                        'last_modified_date' => date('Y-m-d H:i:s'),
                        'package_id' => $this->view->packages[$this->searchParams['package']]['package_id'],
                        'transaction_id' => $transInfo->transaction_id,
                        'payment_gateway' => 'Authorize.net',
                        'notes' => $this->view->packages[$this->searchParams['package']]['package_name']
                    );
                    $packageData = $this->client->addSubscription($data);

                    $this->flashMessenger->addMessage(array('success'=> 'Credits purchased successfully.'));;
                    $this->_helper->redirector->gotoUrl(SITE_URL . 'client/subscription/'  );

                }
                else
                {
                    $this->_helper->redirector->gotoUrl(SITE_URL . 'client/checkout/' );
                }

                ## check if any occured in payment

            }





        } else if ($checkout->selectedPackage > 0) {
            $this->view->selectedPackage = $checkout->selectedPackage;
        }
    }
    public function checkoutautoAction()
    {

        $checkout = new Zend_Session_Namespace('checkoutauto');
        $client_payment_profile_id = $this->_request->getParam('clientpayment');
        $client_id = $this->view->userid;

        if(empty($client_payment_profile_id))
        {
            $this->flashMessenger->addMessage(array('success'=> 'Invalid request,Please refrsh page and try again.'));

            if(!empty($client_id))
                $this->_helper->redirector->gotoUrl(SITE_URL . 'client/buycredits' );

            $this->_helper->redirector->gotoUrl(SITE_URL . 'client' );
        }

        ##check whether profile is created on authorize.net
        $payment_profile =   $this->client->getClientPaymentInfoById($client_payment_profile_id);

        if(empty($payment_profile))
        {
            $this->flashMessenger->addMessage(array('success'=> 'Invalid request,Please refrsh page and try again.'));
            if(!empty($client_id))
                $this->_helper->redirector->gotoUrl(SITE_URL . 'client/buycredits' );

            $this->_helper->redirector->gotoUrl(SITE_URL . 'client' );
        }


        if($payment_profile->user_id != $client_id)
        {
            $this->flashMessenger->addMessage(array('success'=> 'Received an unknown response from the payment gateway. Please try again.'));
            $this->_helper->redirector->gotoUrl(SITE_URL . 'client/buycredits' );
        }


        $request = $this->getRequest();
        if ($request->isPost()) {
            $selectedPackage = $this->searchParams['package'];
            $checkout->selectedPackage = $this->searchParams['package'];


            $this->packages = new Hb_Packages();
            $packages = $this->packages->search();

            $post_data = $this->searchParams;
            $amount =  $packages[$this->searchParams['package']]['price'];

            if(empty($amount))
            {
                $flashMessenger = $this->_helper->getHelper('FlashMessenger');
                $flashMessenger->addMessage(array('error'=>'Invlaid package, Please select again.'));
                $this->_helper->redirector->gotoUrl(SITE_URL . 'client/buycredits/' );
            }

            //create profile transaction
            $payment_pro = (array)$payment_profile;
            $transInfo = $this->common->profileTransaction( $amount,$payment_pro['authorizenet_profile_id'],$payment_pro['authorizenet_payment_profile_id'],$payment_pro['authorizenet_shipping_id']);
           
            $is_bool = $this->common->parseResponse($transInfo);

            if($transInfo!= false && $is_bool == TRUE)
            {

                $data = array(
                    'userid' => $payment_profile->user_id,
                    'slots' => $packages[$this->searchParams['package']]['credits'],// $pack->credits,
                    'total_credits' => $packages[$this->searchParams['package']]['credits'],
                    'price' => $packages[$this->searchParams['package']]['price'],
                    'start_date' => date('Y-m-d H:i:s', strtotime('+1 day 9am')),
                    'end_date' => date('Y-m-d H:i:s', strtotime('+1 year 6pm')),
                    'last_modified_by' => $payment_profile->user_id,
                    'last_modified_date' => date('Y-m-d H:i:s'),
                    'package_id' => $packages[$this->searchParams['package']]['package_id'],
                    'transaction_id' => $transInfo->transaction_id,
                    'payment_gateway' => 'Authorize.net',
                    'notes' => $packages[$this->searchParams['package']]['package_name']
                );
                $packageData = $this->client->buyPackage($data);

                $this->flashMessenger->addMessage(array('success'=> 'Credits purchased successfully.'));;
                $this->_helper->redirector->gotoUrl(SITE_URL . 'client/subscription'  );

            }
            else
            {
                $this->_helper->redirector->gotoUrl(SITE_URL . 'client/buycredits/'  );
            }

        }

        $this->flashMessenger->addMessage(array('success'=> 'Invalid request,Please refrsh page and try again.'));
        $this->_helper->redirector->gotoUrl(SITE_URL . 'client' );
        die;



    }

    public function sitterprofileAction()
    {
        $this->_helper->layout->disableLayout();
        $this->sitter = new Hb_Sitter();
        $this->view->sitterDetails = $this->sitter->getDetail($this->_request->getParam('sitter'));
        $this->view->sitterEducation = $this->sitter->getEducation($this->_request->getParam('sitter'));
        $this->view->sitterReference = $this->sitter->getReference($this->_request->getParam('sitter'));
        //$this->view->preferences = $this->sitter->getPreferences($this->_request->getParam('sitter'));
        //echo '<pre>';
        //print_r($this->view->sitterDetails);
        //print_r($this->view->sitterEducation);
        //print_r($this->view->sitterReference);
    }

    public function referafriendAction()
    {
        $userInfo = $this->view->userInfo;
        if ($this->_request->isPost() && trim($_POST['emails']) != '') {
            $this->hbSettings = new Hb_Settings();
            $mailTemplate = $this->hbSettings->getMailTemplates(array('mail_name' => 'refer_a_friend'));
            $mailTemplate = $mailTemplate[0];

            $from = $userInfo['username'];
            $cc = explode(',', $mailTemplate['cc']);
            $bcc = explode(',', $mailTemplate['bcc']);
            //$cc[] = $this->view->userInfo['username'];
            //$userInfo['username'];
            $subject = $mailTemplate['subject'];

            $to_replace = array('{firstname}', '{lastname}', '{message}');
            $replace_with = array($userInfo['firstname'], $userInfo['lastname'], $_POST['message']);

            $text = str_ireplace($to_replace, $replace_with, $mailTemplate['description']);
            $subject = str_ireplace($to_replace, $replace_with, $subject);

            $to_emails = explode(',', $_POST['emails']);

            foreach ($to_emails as $email) {
                $mail = new Zend_Mail();
                //$text = "Hello {$userInfo['firstname']} {$userInfo['lastname']}, <br> Your New Password:$password <br> Thanks,<br>HamptonsBabysitters.com Administrator";
                $mail->setBodyText($text);

                $mail->setBodyHtml($text);

                $mail->setFrom($from);

                if (!empty($cc))
                    foreach ($cc as $c)
                        $mail->addCc($c);
                if (!empty($bcc))
                    foreach ($bcc as $c)
                        $mail->addBcc($c);

                $mail->addTo(trim($email));

                $mail->setSubject($subject);

                $mail->send();
            }
            $this->view->successMsg = "Your message sent successfully.";
        }
    }

    public function testAction()
    {
        $this->user = new Hb_User();
        $this->user->send_noti();
    }

    public function jobbookingchargeAction()
    {

        $payment_information = new Zend_Session_Namespace('payment_information');
        $payment = $payment_information->payment_information;

        $job_information = new Zend_Session_Namespace('job_information');
        $job = $job_information->job_information;

     
        ##check whether profile is created on authorize.net
        $this->view->paymentProfile =   $this->client->getClientPaymentInfo($this->view->userid);
 
        //print_r($job);die;
        // print_r($job);die;
        if (((isset($payment)) && (!empty($payment))) && (isset($job)) && (!empty($job))) {


            //  print_r($job);die;

            $this->view->job_information = $job;
            $this->view->payment_info = $payment;


            $client = Zend_Auth::getInstance()->getIdentity()->userid;


            $this->view->addresses = $this->client->getAddress(array('userid' => $client, 'address_type' => array('billing')));

            $this->view->addresses = $this->view->addresses[0];
        } else {
            $this->_helper->redirector->gotoUrl(SITE_URL . 'client/events/');
        }
    }

    //function to get payment details of client
    public function getjobpaymentdetailsAction()
    {

        $job_id = $_POST['job_id'];
        $this->jobs = new Hb_Jobs();

        $this->view->event = $this->jobs->search(array('job_id' => $this->_request->getParam('modify')));
        $jobs_data = $this->jobs->search(array('job_id' => $job_id));
        $data = $jobs_data['rows'][$job_id];
        $data['status'] = "success";
        //echo json_encode($data); die;


        if ($data['client_updated_rate'] == null || $data['client_updated_rate'] == "" || empty($data['client_updated_rate']) || $data['client_updated_rate'] == false) {
            $data['client_updated_rate'] = $data['client_rate'];
        }


        if ($data['job_hour'] <= 0) {
            $data['job_hour'] = round((strtotime($data['job_end_date']) - strtotime($data['job_start_date'])) / 3600, 1);
            $data['completed_date'] = $data['job_end_date'];
        }

        echo json_encode($data);
        die;
    }

    // function for charging job booking fee from new credit card or saved credit card.
    public function chargebookingfeeAction()
    {
    	$post_data = $this->searchParams;

    	$post_data['x_customer_ip'] = $this->getRequest()->getClientIp(true);
    	$post_data['amount'] = (float) $_POST['x_amount'];// =  $this->view->packages[$this->searchParams['package']]['price'];
    	$client_payment_profile_id = $_POST['x_clientpayment'];

    	if(empty($post_data['amount']))
    	{
    		$flashMessenger = $this->_helper->getHelper('FlashMessenger');
    		$flashMessenger->addMessage(array('error'=>'Invlaid package, Please select again.'));
    		$this->_helper->redirector->gotoUrl(SITE_URL . 'client/buycredits/' );
    	}

    	if($client_payment_profile_id!='' && isset($client_payment_profile_id))
    	{
    		##check whether profile is created on authorize.net
    		$payment_profile =   $this->client->getClientPaymentInfoById($client_payment_profile_id);
    		$payment_pro = (array)$payment_profile;
    		$transInfo = $this->common->profileTransaction( $post_data['amount'],$payment_pro['authorizenet_profile_id'],$payment_pro['authorizenet_payment_profile_id'],$payment_pro['authorizenet_shipping_id']);
    		$is_bool = $this->common->parseResponse($transInfo);
    	}
    	else
    	{
    		$transInfo = $this->common->processPayment($this->view->userid,$post_data);
    		$is_bool = $this->common->parseResponse($transInfo);
    	}

    	//print_r($transInfo);die;
    	if($transInfo!= false && $is_bool == TRUE)
    	{
    		//cut
    		$data = array(
    				'userid' => $_POST['x_userid'],
    				'slots' => (int) $_POST['x_add_credit'],
    				'price' => (float) $_POST['x_amount'],
    				'notes' => "Booking amount charged",
    				'package_id' => 0,
    				'transaction_id' =>  $transInfo->transaction_id,
    				'payment_gateway' => 'AuthorizeNet',
    		);

    		try {
    			$this->jobs = new Hb_Jobs();
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

    			$this->_helper->redirector->gotoUrl( SITE_URL . 'client/events/modify/' . $job_id );

    		} catch (Exception $e) {
    			throw new Exception('Error while adding Job');
    		}
    	} else {
    		$this->_helper->redirector->gotoUrl(SITE_URL . 'client/jobbookingcharge');

    	}			 
    }
}
