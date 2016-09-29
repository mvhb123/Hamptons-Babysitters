<?php

class Admin_ClientController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->client = new Hb_Client();

        $this->notification = new Hb_Notification();
        $this->common = new Hb_Common();

        $this->searchParams = $this->_request->getParams();
        $this->view->searchParams = $this->searchParams;

        $this->actionName = $this->_request->getActionName();
        if ($this->actionName == 'events' && ($this->searchParams['listevents'] == 'jobs' || $this->searchParams['listevents'] == 'history'))
            $this->_request->setActionName('listevents');

        $this->view->actionName = $this->actionName;

        $this->view->flashMessenger  =$this->flashMessenger =  $this->_helper->getHelper('FlashMessenger');

    }

    public function indexAction()
    {

        $clients = new Hb_Client();
//echo "a";die;

        // print_r($this->searchParams);die;
        $this->view->searchParams = $this->searchParams;
        if(!isset($this->searchParams['key'])||empty($this->searchParams['key']))
        	$this->searchParams['key'] = 'firstname';
        $this->view->clients = $clients->search($this->searchParams, array(), array('rows' => $this->searchParams['rows'], 'page' => $this->searchParams['page']));
        $this->view->paginator = Zend_Paginator::factory(range(1, ((int)$this->view->clients['total'])));

        $this->view->paginator->setCurrentPageNumber((int)$this->searchParams['page'] ? $this->searchParams['page'] : 1);
        $this->view->paginator->setItemCountPerPage((int)$this->searchParams['rows'] ? $this->searchParams['rows'] : 10);
        $this->view->paginator->setPageRange(5);
    }

    public function profileAction()
    {

        // action body
        //$this->_request->getParams(); 
        $profile = new Zend_Session_Namespace('profile');

        $this->view->successMessage = $profile->successMessage;
        unset($profile->successMessage);
        $this->userform = $this->userForm();
        $this->view->billing = $this->client->getAddress(array('userid' => $this->view->userid, 'address_type' => 'billing'));
        $this->view->billing = $this->view->billing[0];
        $addroptions = array('country' => ($this->_request->isPost() && $this->searchParams['billing']['country'] > 0 ? $this->searchParams['billing']['country'] : $this->view->billing['country']));
        $this->userform->addSubForms(array('local' => $this->addressForm(), 'billing' => $this->addressForm($addroptions)));
        $this->userform->billing->setIsArray(true);
        $this->userform->local->setIsArray(true);
        $modify = false;


        if ($this->_request->getParam('modify') != '') {


            $clientDetail = $this->client->getDetail($this->_request->getParam('modify'), 'profile');

            //print_r($clientDetail);die;

            if ($clientDetail) {
                $modify = true;
                $this->view->userid = $this->_request->getParam('modify');
                $this->userform->setAction(ADMIN_URL . 'client/profile/modify/' . $this->_request->getParam('modify'));
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
                $this->userform->miscinfo->setValue($clientDetail['miscinfo']);

                //added for notes
                $this->userform->clientnotes->setValue($clientDetail['clientnotes']);

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

                $this->view->billing = $this->client->getAddress(array('userid' => $this->view->userid, 'address_type' => 'billing'));
                $this->view->billing = $this->view->billing[0];
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
                }
            }
        }
        if (!$modify) {

            $status = 'active';
        }


        //print_r($_FILES);
        $request = $this->getRequest();
        if ($request->isPost()) {

            //print_r($this->_request->getParam('modify'));die;
            //try{
            //print_r($this->userform->getValues());
            if (!isset($_POST['billing']['address_type'])) {
                $this->userform->removeSubForm('billing');
            }
            if ($this->userform->isValid($request->getPost())) {
                $values = $this->userform->getValues();

                //print_r($values);die;
                $user = new Hb_User();
                $userAvailable = $user->checkUsername($values['username'], $this->_request->getParam('modify'));
                if (empty($userAvailable)) {
                    //print_r($values['username']);die;
                    //by namrata code for updating iser profiles

                    $value = strcmp($clientDetail['username'], $values['username']);

                    if ($value != 0) {
                        //for only mdify
                        if ($clientDetail) {
                            // query to see in payment accoutndetails exist fr the client
                            $val = $this->client->getPaymentDetail($this->_request->getParam('modify'));
                            if ($val != false) {
                                //call api of update profile by email
                                require_once APPLICATION_PATH . '/../library/Hb/anet_php_sdk/AuthorizeNet.php'; // The SDK
                                $api_login_id = AUTHORIZENET_LOGIN_ID;
                                $transaction_key = AUTHORIZENET_TRAN_KEY;
                                $md5_setting = AUTHORIZENET_MD5_SETTING; // Your MD5 Setting
                                $var = new AuthorizeNetCIM();

                                $profile_id = intval($val[0]['authorizenet_profile_id']);
                                //$info=$var->getCustomerProfile($profile_id);
                                //call to update profile info/email
                                $info = $var->updateCustomerProfile($this->_request->getParam('modify'), $profile_id, $values['username']);
                            }
                        }
                    }

                    if (!$modify) {

                        //print_r($values);die;
                        if ($this->client->create($values, $status)) {
                            $flag = 'address';
                        }
                    } else if ($this->client->modify($values, $this->_request->getParam('modify'))) {
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
                        else {
                            //Notification mail send to the client
                            $this->client->clientMail($values['userid'], 'client_added_by_admin', $to = 'client', $from = 'mail', $additional = array());
                            $profile->successMessage = 'User have been sucessfully created.';
                        }
                        $this->_helper->redirector->gotoUrl(ADMIN_URL . 'client/profile/modify/' . $this->client->userid);
                    }
                } else {

                    $this->view->errorMessage = $this->userform->username->addError('Username is not available');
                }
            }
        }
        if (!isset($this->userform->billing))
            $this->userform->addSubForm($this->addressForm(), 'billing');
        $this->view->userform = $this->userform;

        //}
    }

    public function deleteAction()
    {

        $this->client->delete($this->searchParams['id']);
        $this->_helper->redirector->gotoUrl($_SERVER['HTTP_REFERER']);
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
        $form->setAction(ADMIN_URL . 'client/profile/create/new/');

        $form->addElement('text', 'firstname', array(
            //  
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
          )); */

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

        $form->addElement('text', 'miscinfo', array(
            'required' => false,
            'label' => 'Misc Info:',
        ));

        $form->addElement('submit', 'create', array(
            'required' => false,
            'ignore' => true,
            'label' => 'Create',
        ));

        $form->addElement('textarea', 'clientnotes', array(
            'required' => false,
        ));

        return $form;
    }

    public function childrenAction()
    {

        $parent_id = $_POST['parent_id'];
        $job_id = $_POST['job_id'];

        if ((isset($job_id)) && (!empty($job_id))) {

            $val = array();
            $val['child_name'] = $_POST['child_name'];

            $val['dob'] = $_POST['dob'];
            $val['sex'] = $_POST['sex'];
            $val['allergy_status'] = $_POST['allergy_status'];
            $val['allergies'] = $_POST['allergies'];
            $val['medicator_status'] = $_POST['medicator_status'];
            $val['medicator'] = $_POST['medicator'];
            $val['image'] = $_FILES['image'];
            $val['notes'] = $_POST['notes'];
            $val['job_child_added_by'] = 1;
            $val['special_needs'] = trim($_POST['special_needs']);
            $val['fav_food'] = trim($_POST['fav_food']);
            $val['fav_book'] = trim($_POST['fav_book']);
            $val['fav_cartoon'] = trim( $_POST['fav_cartoon']);
            if($_POST['special_needs']!=null ||$_POST['special_needs']!='')
            	$val['special_needs_status']= 'Yes';
            else
            	$val['special_needs_status']= 'No';
            
            $parent_id = '0';

            $child_id = $this->client->children->addChildNew($val, $parent_id);
            //to upadet child id in job
            $this->client->children->addChildToJob($child_id, $job_id);
            $this->jobs = new Hb_Jobs();
            $children = $this->jobs->getChildren($job_id);
            $updated_child_count = count($children);
            //to update child count acrual
            $this->client->update_child_count($job_id, $updated_child_count);
//get rate
            /* $rate_data=$this->jobs->get_job_child_rate($updated_child_count);
            if (!empty($rate_data)) {

                $this->jobs->update_client_sitter_rate($job_id,$rate_data[0]['sitter_rate'],$rate_data[0]['client_rate']);
            }*/

            //update rate

            //to upadet last job updated
            $data = array();
            $data['status'] = "success";
            $data['message'] = "Child has been added to job successfully";
            echo json_encode($data);
            die;
        }


        $this->cForm = $this->childForm();
        if ($this->_request->getParam('parent') != '')
            $parent_id = $this->_request->getParam('parent');

        if ($parent_id == '')
            $this->_helper->redirector('profile', 'client');
        else {
            $this->view->parent_id = $parent_id;
            $this->view->userid = $parent_id;
        }

        $this->cForm->setAction(ADMIN_URL . 'client/children/parent/' . $parent_id);
        if ($this->searchParams['delete'] > 0) {
            $this->client->children->delete($this->searchParams['delete']);
            $this->_helper->redirector->gotoUrl($_SERVER['HTTP_REFERER']);
        }

        if ($this->_request->getParam('modify') != '') {
            $this->view->child = $this->client->children->get($this->_request->getParam('modify'));

            if ($this->view->child) {
                $this->view->childId = $this->_request->getParam('modify');
                $child_id = $this->_request->getParam('modify');
                $this->cForm->setAction(ADMIN_URL . 'client/children/parent/' . $parent_id . '/modify/' . $child_id);
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
                if($values['special_needs']!=null ||$values['special_needs']!='')
                	$values['special_needs_status']= 'Yes';
                else
                	$values['special_needs_status']= 'No';
                //print_r($_FILES['image']);
                $values['image'] = $_FILES['image'];


                if ($this->view->child) {
                    if ($this->client->children->modify($values, $this->view->userid, $this->view->child['child_id'])) {
                        $this->cForm->reset();
                        $this->_helper->redirector->gotoUrl(ADMIN_URL . 'client/children/parent/' . $this->view->userid);
                    }
                } else
                    if ($this->client->children->add($values, $this->view->userid)) {
                        // We're authenticated! Redirect to the home page
                        $this->_helper->redirector->gotoUrl(ADMIN_URL . 'client/children/parent/' . $this->view->userid);
                        $this->cForm->reset();
                    }
            }
        }
        $this->view->children = $this->client->children->getAll($parent_id);
        $this->view->childForm = $this->cForm;
        $this->view->userInfo = $this->client->getDetail($this->view->userid, 'profile');
    }

    public function childForm()
    {
        $form = new Zend_Form();
        $form->setName("userform");
        $form->setMethod('post');
        $form->setAttrib('enctype', 'multipart/form-data');
        $this->view->special_needs_list =$this->client->getSpecialNeeds();

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
        		'multiOptions' => array('M'=>'Male','F'=>'Female'),
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

        $field=$form->getElement('special_needs');
        $field->setRegisterInArrayValidator(false);
        
        return $form;
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
        // echo $this->_request->getParam('user');die();
        if ($this->_request->getParam('userid') != '')
            $userid = $this->_request->getParam('userid');

        if ($userid == '')
            $this->_helper->redirector('profile', 'client');
        else {
            $this->view->userid = $userid;
        }

        $this->adForm->setAction(ADMIN_URL . 'client/address/userid/' . $userid);
        if ($this->_request->getParam('modify') != '') {
            $this->view->address = $this->client->getAddress(array('userid' => $this->view->userid, 'address_id' => $this->_request->getParam('modify')));
            $this->view->address = $this->view->address[0];
            if (isset($this->view->address)) {
                $address_id = $this->_request->getParam('modify');
                $this->adForm->setAction(ADMIN_URL . 'client/address/userid/' . $userid . '/modify/' . $address_id);
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

        if ($this->_request->getParam('client') != '')
            $client = $this->_request->getParam('client');

        if ($client == '')
            $this->_helper->redirector('profile', 'client');
        else {
            $this->view->userid = $client;
        }


        if ($this->_request->getParam('modify') != '') {
            $this->view->subscribe = $this->client->getSubscription(array('userid' => $this->view->userid, 'sub_id' => $this->_request->getParam('modify')));
            //print_r($this->view->subscribe);
            $this->view->subscribe = $this->view->subscribe['rows'][0];
            if (isset($this->view->subscribe)) {
                //	die();
                $sub_id = $this->_request->getParam('modify');
                $this->view->subscribeForm->setAction(ADMIN_URL . 'client/buycredits/client/' . $this->view->userid . '/modify/' . $sub_id);
                $this->view->subscribeForm->slots->setValue($this->view->subscribe['slots']);
                $this->view->subscribeForm->price->setValue($this->view->subscribe['price']);
                $this->view->subscribeForm->start_date->setValue(date(DATE_FORMAT, strtotime($this->view->subscribe['start_date'])));
                $this->view->subscribeForm->end_date->setValue(date(DATE_FORMAT, strtotime($this->view->subscribe['end_date'])));
                $this->view->subscribeForm->notes->setValue($this->view->subscribe['notes']);
                $this->view->selectedPackage = $this->view->subscribe['package_id'];
            }
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $values = $this->view->subscribeForm->getValues();

            if ($_POST['package_id'] != 'custom') {

				$values['slots'] = $this->view->packages[$_POST['package']]['credits'];
                $values['price'] = $this->view->packages[$_POST['package']]['price'];
                $values['package_id'] = $_POST['package'];
                
                if($_POST['checkout_type']=='free')
                {
                	$values['checkout_type']='free';
                	$values['transaction_id'] = 'No Payment';
                	$values['payment_gateway'] = 'Free';
                }
                
            } else
                $_POST['package'] = 0;
            $values['notes'] = $values['notes'] == '' ? $this->view->packages[$_POST['package']]['package_name'] : $values['notes'];
            if ($this->view->subscribeForm->isValid($values)) {


                $values['userid'] = $this->view->userid;
                if ($sub_id > 0) {
                    $this->client->updateSubscription($values, $sub_id);
                    $this->_helper->redirector->gotoUrl(ADMIN_URL . 'client/subscription/client/' . $this->view->userid);
                    //$this->view->subscribeForm->reset();
                } else {

                    $subsId=$this->client->addSubscription($values);
                    $this->_helper->redirector->gotoUrl(ADMIN_URL . 'client/subscription/client/' . $this->view->userid. '?success=free&subs=' . $subsId);
                }
            }
        }

        $this->view->userInfo = $this->client->getDetail($this->view->userid, 'profile');

        ##check whether profile is created on authorize.net
        $this->view->paymentProfile =   $this->client->getClientPaymentInfo($client);

    }

    public function subscriptionAction()
    {
    	$this->view->subscribeForm = $this->subscribeForm();
    	
    	if ($this->_request->getParam('client') != '')
    		$client = $this->_request->getParam('client');
    	
    	if ($client == '')
    		$this->_helper->redirector('index', 'client');
    	else {
    		$this->view->userid = $client;
    	}
    	if ($this->searchParams['delete'] > 0) {
    		$this->client->deleteSubscription($this->searchParams['delete']);
    		$this->_helper->redirector->gotoUrl($_SERVER['HTTP_REFERER']);
    	}
    	$this->view->subscriptions = $this->client->getSubscription(array_merge(array('userid' => $this->view->userid), $this->searchParams));
    	//by anjali
    	//$this->view->subscriptions = $this->client->getSubscriptionnew(array_merge(array('userid' => $this->view->userid), $this->searchParams));
    	$this->view->paginator = Zend_Paginator::factory(range(1, ((int)$this->view->subscriptions['total'] - 1)));
    	$this->view->total_avail_credits = $this->view->subscriptions['total_avail_credits'];
    	
    	//by anjali
    	$this->view->purchased_credits = $this->view->subscriptions['purchased_credits'];
    	
    	$this->view->subscriptions = $this->view->subscriptions['rows'];
    	$this->view->paginator->setCurrentPageNumber((int)$this->searchParams['page'] ? $this->searchParams['page'] : 1);
    	$this->view->paginator->setItemCountPerPage((int)$this->searchParams['rows'] ? $this->searchParams['rows'] : 10);
    	$this->view->paginator->setPageRange(10);
    	$this->view->userInfo = $this->client->getDetail($this->view->userid, 'profile');
    	
    	if ($this->searchParams['success'] == 'payment') {
    		$purchasedSub = $this->searchParams['subs'];
    		foreach ($this->view->subscriptions as $s) {
    			$sub[$s['client_sub_id']] = $s;
    		}
    		$purchasedSub = $sub[$purchasedSub];
    		$this->view->successMessage = 'You have successfully purchased ' . $purchasedSub['slots'] . ' credit(s) for ' . $purchasedSub['price'] . 'USD. Transction ID: ' . $purchasedSub['transaction_id'];
    		$checkout = new Zend_Session_Namespace('checkout');
    	
    		unset($checkout->selectedPackage);
    	}
    	if ($this->searchParams['success'] == 'free') {
    		$purchasedSub = $this->searchParams['subs'];
    		foreach ($this->view->subscriptions as $s) {
    			$sub[$s['client_sub_id']] = $s;
    		}
    		$purchasedSub = $sub[$purchasedSub];
    		$this->view->successMessage = 'You have successfully added ' . $purchasedSub['slots'] . ' credit(s) for Free';
    		$this->view->searchParams['success'] = 'payment';
    	}
    }

    public function passwordAction()
    {
        $this->view->userid = $client = $this->_request->getParam('client');

        if (isset($_POST['passwordreset'])) {
            $this->client->generatePassword($this->view->userid);
        }
        $this->view->userInfo = $this->client->getDetail($this->view->userid, 'profile');
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

    public function listeventsAction_old()
    {
        $this->jobs = new Hb_Jobs();
        $this->client = new Hb_Client();

        if ($this->_request->getParam('user') != '')
            $client = $this->_request->getParam('user');

        if ($client == '')
            $this->_helper->redirector('profile', 'client');
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
        range(1, $this->events['total'] - 1);
        $this->view->paginator = Zend_Paginator::factory(range(1, $this->events['total'] - 1));

        $this->view->paginator->setCurrentPageNumber((int)$this->searchParams['page'] ? $this->searchParams['page'] : 1);
        $this->view->paginator->setItemCountPerPage((int)$this->searchParams['rows'] ? $this->searchParams['rows'] : 10);
        $this->view->paginator->setPageRange(10);
        $this->view->userInfo = $this->client->getDetail($this->view->userid, 'profile');
    }

    public function eventsAction()
    {

        $child_flag = true;
        $this->jobs = new Hb_Jobs();
        $this->sitter = new Hb_Sitter();

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
        if ($this->_request->getParam('user') != '')
            $client = $this->_request->getParam('user');

        if ($client == '')
            $this->_helper->redirector('profile', 'client');
        else {

            $this->view->userid = $client;

            if ($this->searchParams['delete'] > 0) {
                $this->jobs->delete($this->searchParams['delete']);
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
        //var_dump($this->_request->getParam('modify'));
        $this->view->eventsForm->setAction(ADMIN_URL . 'client/events/user/' . $this->view->userid);

        $this->view->addresses = $this->client->getAddress(array('userid' => $this->view->userid, 'address_type' => array('local', 'other')));

        $this->view->children = $this->client->children->getAll($this->view->userid);
        //$this->view->special_needs_list =$this->client->getSpecialNeeds();

        if ($this->_request->getParam('modify') != '') {
            $this->view->event = $this->jobs->searchnew(array('job_id' => $this->_request->getParam('modify')));
            $this->view->jobPreference = $this->jobs->getPreferences($this->_request->getParam('modify'));

            $this->view->event = $this->view->event['rows'][$this->_request->getParam('modify')];
          
            
            //to pass all children with respect to the job

            $children_of_jobs = $this->jobs->get_all_child_of_jobs($this->_request->getParam('modify'));
            if (empty($children_of_jobs)) {
                $this->view->children = $this->client->children->getAll($this->view->userid);
            } else {
                $common_value = array_merge($this->view->children, $children_of_jobs);

                $this->view->children = $common_value;
            }

            if (isset($this->view->event)) {
            	
                $job_id = $this->_request->getParam('modify');
                $this->view->modify = $modify = true;
                $this->view->selectedChild = $this->jobs->getChildren($job_id);

                //to update child count
                //$updated_child_count=count($this->view->selectedChild);
                //to update child count acrual
                //$this->client->update_child_count($job_id,$updated_child_count);
                //var_dump($this->view->event['sitter_user_id']);
                if ($this->view->event['sitter_user_id'] > 0 )
					$this->view->logs =  $this->jobs->job_logs_sitter($job_id,$this->view->event['sitter_user_id']);
                if ($this->view->event['sitter_user_id'] >0)
                	$this->view->selectedSitter = $this->sitter->getDetail($this->view->event['sitter_user_id']);
                $this->view->selectedAddress = $this->view->event['address_id'];
                $this->view->eventsForm->setAction(ADMIN_URL . 'client/events/user/' . $this->view->userid . '/modify/' . $job_id);
                $this->view->eventsForm->start_date->setValue($this->view->event['job_start_date']);
                $this->view->eventsForm->end_date->setValue($this->view->event['job_end_date']);
                $this->view->eventsForm->notes->setValue($this->view->event['notes']);
                $this->view->eventsForm->special_need->setValue($this->view->event['special_need']);
            }
        } else
            $this->view->modify = $modify = false;


        $request = $this->getRequest();

        if ($request->isPost()) {
            if ($_POST['address_id'] != 'other') {
                $this->view->eventsForm->removeSubForm('other');
            }
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
                        $clild_flag = false;
                    }
                    if ($continue_flag && $clild_flag) {
                        $values['userid'] = $this->view->userid;

                        $values['children'] = $_POST['child'];
                        //echo '<pre>';print_r($values);die();
                        if ($_POST['address_id'] == 'other') {
                            $address_values = $this->view->eventsForm->other->getValues();
                            $address_values['userid'] = $this->view->userid;
                            $address_values['address_type'] = 'other';
                            $address_id = $this->client->addAddress($address_values);
                        } else
                            $address_id = $_POST['address_id'];


                        $values['address_id'] = $address_id;
                        $values['prefer'] = $_POST['prefer'];
                        $values['special_need'] =   $_POST['special_need'];
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
                                //bynamrata
                                $previos_child_count = $this->jobs->get_previos_child_count($job_id);

                                
                                $updated_child_count = count($values['children']);

                                if ($previos_child_count != $updated_child_count) {
                                    $this->jobs->add_job_log($job_id, $previos_child_count, $updated_child_count);
                                }

                                //to get previous child count

                                //

                                $values['actual_child_count'] = count($values['children']);

                                //chages for adding rate to the database
                                $rate_data = $this->jobs->get_job_child_rate($values['actual_child_count']);
                                if (!empty($rate_data)) {
                                    $values['sitter_rate_pre'] = $rate_data[0]['sitter_rate'];
                                    $values['rate'] = $rate_data[0]['sitter_rate'];
                                    $values['client_rate'] = $rate_data[0]['client_rate'];
                                    $values['client_updated_rate'] = $rate_data[0]['client_rate'];
                                }

                                //get id of the the sitter
                                //
                                $sitter_id = $this->jobs->get_jobs_sitter($job_id);
                                if ((!empty($sitter_id)) && ($sitter_id != null)) {
                                    $values['sitter_id'] = $sitter_id;
                                }

                                if ($previos_child_count == $updated_child_count) {
                                    $values['update'] = 0;
                                } else {
                                    $values['update'] = 1;
                                }

                                $this->jobs->update($values, $job_id);
//                                else
//                                {
//                                    $this->jobs->update($values, $job_id);
//                                }

                                //to send  notification to client that job has been modified by the admin
                                $results = $this->jobs->get_device_token_parent($job_id);


                                if (!empty($results[0][deviceToken])) {
                                    $noti_data = array();
                                    $noti_data['job_id'] = $job_id;
                                    $noti_data['userid'] = $results[0]['client_user_id'];
                                    $noti_data['date_added'] = date('Y-m-d H:i:s');
                                    $noti_data['date_updated'] = date('Y-m-d H:i:s');
                                    $notification_id = $this->notification->insert_notification_data($noti_data);
                                    //get unread notification count
                                    $count = $this->notification->get_unread_count($this->view->userid);
                                    $this->ipush = new Hb_Ipushnotification();
                                    $message = "Admin has updated details on a job posted by you";
                                    
                                    foreach ($results as $noti) {
                                    	if($noti['deviceToken']!='')
                                       		$this->ipush->send_notification_parent($noti['deviceToken'], $message, $job_id, $this->view->userid, $count, $notification_id);
                                    }
                                }
                                
                                //to send notification to sitter that job is modified by the cllient
                               
                                if ((!empty($sitter_id)) && ($sitter_id != null)) {

                                    $results = $this->jobs->get_device_token($sitter_id);

                                    // print_r($results);die;
                                    if (count($results) > 0) {

                                        $noti_data = array();
                                        $noti_data['job_id'] = $job_id;

                                        $noti_data['userid'] = $sitter_id;
                                        $noti_data['date_added'] = date('Y-m-d H:i:s');
                                        $noti_data['date_updated'] = date('Y-m-d H:i:s');

                                        $noti_id = $this->notification->insert_notification_data($noti_data);
                                        
                                        $jstaus=$this->notification->getJobStatus($job_id);
                                        //get unread notification count
                                        $count = $this->notification->get_unread_count($sitter_id);
                                        //print_r($count);die;

                                        $this->ipush = new Hb_Ipushnotification();
                                        $message = "Admin has updated details on a job confirmed by you";

                                        foreach ($results as $noti) {
                                            $type = 3;
                                            if($noti['deviceToken']!='')
                                           		$this->ipush->send_notification_sitter($noti['deviceToken'], $message, $job_id, $sitter_id, $type, $count, $noti_id,$jstaus);
                                        }
                                    }
                                }

                                //

                                $flag = true;
                                //$this->view->eventsForm->reset();
                            }
                        } else {


                            $time1 = date('H:i:s', strtotime($values['start_date']));
                            $time2 = date('H:i:s', strtotime($values['end_date']));

                            $time1 = strtotime("1980-01-01 $time1");
                            $time2 = strtotime("1980-01-01 $time2");

                            $hourdiff = date("H", strtotime("1980-01-01 00:00:00") + ($time2 - $time1));


                            if (($hourdiff < 3) && ($hourdiff >= 0)) {
                                $this->view->eventsForm->end_date->addError('Job should be of minimum 3 hours');
                                $this->view->eventsForm->end_date->setValue($end_date);
                            } else {


                                $updated_child_count = count($values['children']);

                                $values['child_count'] = $updated_child_count;
                                $values['actual_child_count'] = $updated_child_count;
                                $values['job_status'] = 'new';

                                $rate_data = $this->jobs->get_job_child_rate($updated_child_count);
                                if (!empty($rate_data)) {
                                    $values['sitter_rate_pre'] = $rate_data[0]['sitter_rate'];
                                    $values['rate'] = $rate_data[0]['sitter_rate'];
                                    $values['client_rate'] = $rate_data[0]['client_rate'];
                                    $values['client_updated_rate'] = $rate_data[0]['client_rate'];
                                }
                                $this->jobs->create($values, $this->view->userid);
                                $flag = true;


                                //end of else
                            }
                        }
                    } else {
                        if (!$clild_flag) {
                            $this->view->childError = 'Please select children to add Job';
                        } else {


                            $this->view->eventsForm->start_date->addError('Updating the date is not possible. Instead You can modify time');
                            $this->view->eventsForm->start_date->setValue($this->view->event['job_start_date']);
                            $this->view->eventsForm->end_date->addError('Updating the date is not possible. Instead You can modify time');
                            $this->view->eventsForm->end_date->setValue($this->view->event['job_end_date']);
                        }
                    }
                } else {
                    $flag = false;
                    $this->view->errorMessageCredits = '<p>You are going to book for <strong>' . $creditsArray['required_credits'] . ' days.</strong></p><p>You don\'t have enough credits to make this event</p>';
                    $this->view->eventsForm->end_date->addError('Not enough credits to book.');
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
            $this->_helper->redirector->gotoUrl(ADMIN_URL . 'client/events/user/' . $this->view->userid . '/modify/' . $job_id);
        }

        if (!isset($this->view->eventsForm->other))
            //$this->_helper->redirector('profile', 'client');
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
        
        /*$form->addElement('select', 'special_need', array(
        		'required' => false,
        		'label' => 'Special Needs:',
       		'multiOptions' => $this->view->special_needs_list
        ));
      	*/
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
        /*
        $field=$form->getElement('special_need');
        $field->setRegisterInArrayValidator(false);   */
        return $form;
    }

    public function assignAction()
    {


        $this->jobs = new Hb_Jobs();

        $this->job_id = $this->view->job_id = $this->job_id = $this->_request->getParam('modify');
        $this->view->userid = $this->_request->getParam('user');

        $request = $this->getRequest();


        if ($request->isPost() && $this->job_id > 0 && $this->searchParams['sendto'] == 'allsitter') {
            $admin = 1;

            $this->jobs->assign_sitters($this->job_id, $admin, 1);


            $this->view->sentJobs = $this->jobs->getJobsSentSitters($this->job_id);


            $this->_helper->layout->disableLayout();
        } //by namrata to auto confirm

        else if ($request->isPost() && $this->job_id > 0 && $this->searchParams['sendto'] == 'confirmsitter') {
            if (!empty($_POST['sitter_id'])) {
                $sitter = $_POST['sitter_id'];
                $assignee = 'admin';
                $this->jobs->confirmJob($this->job_id, $sitter, $assignee);
                
                $parent_id=$this->_request->getParam('user');
              
                //to insert in db
                //code to send email
                //
                //
                //to send notifications to sitter
                $results = $this->jobs->get_device_token($sitter);

                if (!empty($results)) {
                    //to insert in to notifi table 
                    $noti_data = array();
                    $noti_data['job_id'] = $this->job_id;

                    $noti_data['userid'] = $sitter;
                    $noti_data['date_added'] = date('Y-m-d H:i:s');
                    $noti_data['date_updated'] = date('Y-m-d H:i:s');

                    $notification_id = $this->notification->insert_notification_data($noti_data);

                    //get unread notification count
                    $count = $this->notification->get_unread_count($sitter);
                    $jstaus=$this->notification->getJobStatus($this->job_id);
                    
                    $this->ipush = new Hb_Ipushnotification();
                    $message = "You have a confirmed job assigned by admin.";

                    foreach ($results as $noti) {
                        $noti_type = 4; //4 for admin confirmed job , 3 for admin modification, 2 for removed from job , 1 for new job request

                        if ((!empty($noti['userid'])) && (!empty($noti['deviceToken']))) {

                            $this->ipush->send_notification_sitter($noti['deviceToken'], $message, $this->job_id, $noti['userid'], $noti_type, $count, $notification_id,$jstaus);
                            
                        }
                    }
                }
                
                //to send notifications to parent
                $results=array();
                $results = $this->jobs->get_device_token($parent_id);
                
                if (!empty($results)) {
                	//to insert in to notifi table
                	$noti_data = array();
                	$noti_data['job_id'] = $this->job_id;
                
                	$noti_data['userid'] = $parent_id;
                	$noti_data['date_added'] = date('Y-m-d H:i:s');
                	$noti_data['date_updated'] = date('Y-m-d H:i:s');
                
                	$notification_id = $this->notification->insert_notification_data($noti_data);
                
                	//get unread notification count
                	$count = $this->notification->get_unread_count($parent_id);
                
                	$this->ipush = new Hb_Ipushnotification();
                	$message = "Your job has been confirmed by admin";
                
                	foreach ($results as $noti) {
                
                		if ((!empty($noti['userid'])) && (!empty($noti['deviceToken']))) {
                
                			$this->ipush->send_notification_parent($noti['deviceToken'], $message, $this->job_id, $noti['userid'], $count, $notification_id);
                
                		}
                	}
                }
                
            }

            if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $this->searchParams['sendto'] == 'confirmsitter') {
                $this->_helper->layout->disableLayout();
            }

        } else if ($request->isPost() && $this->job_id > 0 && !empty($_POST['sitter']) && $this->searchParams['sendto'] == 'sitter') {

            $this->jobs->sendToSitters(array($this->job_id), $_POST['sitter']);
            $sitters = $_POST['sitter'];
            foreach ($sitters as $sitter) {

                $results = $this->jobs->get_device_token($sitter);

                if (!empty($results)) {

                    //to insert in to notifi table 
                    $noti_data = array();
                    $noti_data['job_id'] = $this->job_id;

                    $noti_data['userid'] = $sitter;
                    $noti_data['date_added'] = date('Y-m-d H:i:s');
                    $noti_data['date_updated'] = date('Y-m-d H:i:s');

                    $notification_id = $this->notification->insert_notification_data($noti_data);

                    //get unread notification count
                    $count = $this->notification->get_unread_count($sitter);
                          
                    $jstaus=$this->notification->getJobStatus($this->job_id);
                    
                    $this->ipush = new Hb_Ipushnotification();
                    $message = "You have a new job request by admin";

                    foreach ($results as $noti) {
                        $noti_type = 1;

                        if ((!empty($noti['userid'])) && (!empty($noti['deviceToken']))) {

                            $this->ipush->send_notification_sitter($noti['deviceToken'], $message, $this->job_id, $noti['userid'], $noti_type, $count, $notification_id,$jstaus);
                        }
                    }
                }
            }

            $this->view->sentJobs = $this->jobs->getJobsSentSitters($this->job_id);
            if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $this->searchParams['sendto'] == 'sitter') {
                $this->_helper->layout->disableLayout();
            }
        } else if ($request->isPost() && $this->job_id > 0 && !empty($_POST['sitter']) && $this->searchParams['remove'] == 'sitter') {
            $sitters = $_POST['sitter'];
            // echo $this->searchParams['remove'];die();
            $this->jobs->removeSendToSitters(array($this->job_id), $_POST['sitter']);
            //add notification for sitters by namrata

            foreach ($sitters as $sitter) {

                $results = $this->jobs->get_device_token($sitter);

                if (!empty($results)) {

                    $noti_data = array();
                    $noti_data['job_id'] = $this->job_id;
                    $noti_data['userid'] = $sitter;
                    $noti_data['date_added'] = date('Y-m-d H:i:s');
                    $noti_data['date_updated'] = date('Y-m-d H:i:s');

                    $notification_id = $this->notification->insert_notification_data($noti_data);

                    //get unread notification count
                    $count = $this->notification->get_unread_count($sitter);
                    
                    $jstaus=$this->notification->getJobStatus($this->job_id);
                    
                    $this->ipush = new Hb_Ipushnotification();
                    $message = "Admin denied you from previously assigned job";

                    foreach ($results as $noti) {

                        $noti_type = 2;
                        if ((!empty($noti['userid'])) && (!empty($noti['deviceToken']))) {

                            $this->ipush->send_notification_sitter($noti['deviceToken'], $message, $this->job_id, $noti['userid'], $noti_type, $count, $notification_id,$jstaus);
                        }
                    }
                }
            }

            $this->view->sentJobs = $this->jobs->getJobsSentSitters($this->job_id);
            if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $this->searchParams['remove'] == 'sitter') {
                $this->_helper->layout->disableLayout();
            }
        }
        $this->hbSettings = new Hb_Settings();
        $this->view->preferences = $this->hbSettings->getPreferences();

        if ((int)$this->job_id <= 0) {
            $this->_helper->redirector->gotoUrl(ADMIN_URL . 'client/events/user/' . $this->view->userid . '/');
        }
        $this->view->job_id = $this->job_id;
        $this->client = new Hb_Client();
        $this->sitter = new Hb_Sitter();
        $this->view->event = $this->jobs->search(array('job_id' => $this->job_id));
        $this->view->event = $this->view->event['rows'][$this->job_id];
        $this->view->userid = $this->view->event['userid'];

        $this->view->addresses = $this->client->getAddress(array('userid' => $this->view->userid, 'address_type' => array('local', 'other')));

        $this->view->children = $this->client->children->getAll($this->view->userid);

        //print_r($this->view->children );die;


        $this->view->selectedChild = $this->jobs->getChildren($this->job_id);
        $this->view->selectedAddress = $this->view->event['address_id'];
        //var_dump($this->view->event);die;
        if ($this->view->event['sitter_user_id'] > 0)
            $this->view->selectedSitter = $this->sitter->getDetail($this->view->event['sitter_user_id']);
        $this->view->sentJobs = $this->jobs->getJobsSentSitters($this->job_id);

       	$this->view->lp= $this->jobs->languageprefer($this->job_id); //for language filter by anjali
        $this->view->jobsPreferences = $this->jobs->getPreferences($this->job_id);
	
	// for language filter
	if(empty($this->view->jobsPreferences[5]))
        {
        	$pl[5]=array('prefer'=>array(),'label'=>'Language Spoken');
        	$this->view->jobsPreferences= $pl+$this->view->jobsPreferences;
        }
		// FOR skills filter
        $this->view->skills=$this->hbSettings->getskills();;
        //print_r($this->view->jobsPreferences);

        $prefer = array();
        if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $this->searchParams['search'] == 'sitter') {
            $this->_helper->layout->disableLayout();
            $search_flag = true;
        }


        if ($request->isPost() && is_array($this->searchParams['prefer'])) {
            $prefer = $this->searchParams['prefer'];
            $prefer = array_combine($prefer, $prefer);
        } else if (!$request->isPost())
            foreach ($this->view->jobsPreferences as $group)
                foreach ($group['prefer'] as $p) {
                    if ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' && $p['for_manage_sitter'] == 1)
                        continue;
                    $prefer[$p['prefer_id']] = $p['prefer_id'];
                }

        //for search and assign filter. by anjali
        $this->view->sitters = $this->sitter->searchByPreferencenew($prefer, $this->job_id,$this->searchParams['skills'],$this->searchParams['sitter_name'],$this->searchParams['seacrh_job']); //$this->sitter->search();
      //  print_r($this->view->sitters);
        foreach ($this->view->preferences as $group)
            foreach ($group['prefer'] as $p)
                $this->prefer[$p['prefer_id']] = $p['prefer_name'];
        //var_dump($this->prefer);
        $this->view->jobPreferSearch = array_intersect_key($this->prefer, $prefer);
    }

    public function cancelconfirmedAction()
    {
        $this->job = new Hb_Jobs();
        $this->job->cancelConfirm($this->searchParams['job_id']);
        $this->_helper->redirector->gotoUrl(ADMIN_URL . 'client/assign/user/' . $this->searchParams['user'] . '/modify/' . $this->searchParams['job_id']);

        die();
    }

    public function checkoutAction()
    {

        $checkout = new Zend_Session_Namespace('checkout');
        $this->view->userid = $this->_request->getParam('client');
        $this->view->subscribeForm = $this->subscribeForm();
        $this->view->actionName = 'subscription';
        $this->view->subscribeForm->start_date->setValue(date(DATE_FORMAT, strtotime('+1 day 9am')));
        $this->view->subscribeForm->end_date->setValue(date(DATE_FORMAT, strtotime('+1 year 6pm')));

        $this->packages = new Hb_Packages();
        $this->view->packages = $this->packages->search();

        //print_r($this->view->packages);
        $this->view->userInfo = $this->client->getDetail($this->view->userid);
        $this->view->addresses = $this->client->getAddress(array('userid' => $this->view->userid, 'address_type' => array('billing')));

        $this->view->addresses = $this->view->addresses[0];
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
                    $this->_helper->redirector->gotoUrl(ADMIN_URL . 'client/buycredits/client/' . $this->view->userid );
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
                    $packageData = $this->client->buyPackage($data);

                    $this->flashMessenger->addMessage(array('success'=> 'Credits purchased successfully.'));;
                    $this->_helper->redirector->gotoUrl(ADMIN_URL . 'client/subscription/client/' . $this->view->userid );

                }
                else
                {
                    $this->_helper->redirector->gotoUrl(ADMIN_URL . 'client/checkout/client/' . $this->view->userid );
                }

                ## check if any occured in payment

            }


            // echo '<pre>';
            //print_r($this->_request->getParams());
        } else if ($checkout->selectedPackage > 0) {
            $this->view->selectedPackage = $checkout->selectedPackage;
        }

        if ($this->searchParams['checkout_type'] == 'free') {
            $insert = array(
                'userid' => $this->view->userInfo['userid'],
                'slots' => $this->view->packages[$this->view->selectedPackage]['credits'],
                'price' => $this->view->packages[$this->view->selectedPackage]['price'],
                'package_id' => (int)$this->view->packages[$this->view->selectedPackage]['package_id'],
                'transaction_id' => 'No Payment',
                'checkout_type' => 'free',
                'notes' => $this->view->packages[$this->view->selectedPackage]['package_name'],
            );
            if ($subsId = $this->client->addSubscription($insert)) {
                $this->_helper->redirector->gotoUrl(ADMIN_URL . 'client/subscription/client/' . $this->view->userInfo['userid'] . '?success=free&subs=' . $subsId);
            }
        }
    }

    public function checkoutautoAction()
    {

        $checkout = new Zend_Session_Namespace('checkoutauto');
        $client_payment_profile_id = $this->_request->getParam('clientpayment');
        $client_id = $this->_request->getParam('client');

        if(empty($client_payment_profile_id))
        {
            $this->flashMessenger->addMessage(array('success'=> 'Invalid request,Please refrsh page and try again.'));

            if(!empty($client_id))
            $this->_helper->redirector->gotoUrl(ADMIN_URL . 'client/buycredits/client/' . $client_id );

            $this->_helper->redirector->gotoUrl(ADMIN_URL . 'client' );
        }

        ##check whether profile is created on authorize.net
        $payment_profile =   $this->client->getClientPaymentInfoById($client_payment_profile_id);

        if(empty($payment_profile))
        {
            $this->flashMessenger->addMessage(array('success'=> 'Invalid request,Please refrsh page and try again.'));
            if(!empty($client_id))
                $this->_helper->redirector->gotoUrl(ADMIN_URL . 'client/buycredits/client/' . $client_id );
            $this->_helper->redirector->gotoUrl(ADMIN_URL . 'client' );
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
                $this->_helper->redirector->gotoUrl(ADMIN_URL . 'client/buycredits/client/' .$payment_profile->user_id );
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
                $packageData = $this->client->addSubscription($data);

                $this->flashMessenger->addMessage(array('success'=> 'Credits purchased successfully.'));;
                $this->_helper->redirector->gotoUrl(ADMIN_URL . 'client/subscription/client/' . $payment_profile->user_id );

            }
            else
            {
                $this->_helper->redirector->gotoUrl(ADMIN_URL . 'client/buycredits/client/' . $payment_profile->user_id );
            }

        }

        $this->flashMessenger->addMessage(array('success'=> 'Invalid request,Please refrsh page and try again.'));
        $this->_helper->redirector->gotoUrl(ADMIN_URL . 'client' );
        die;



    }

    /* ----------function to store credit card info of the user-------------------------- */

    public function cardinfoAction()
    {

        $add = new Zend_Session_Namespace('Success');
        if (isset($add) && !empty($add)) {

            $this->view->successMessage = $add->message;
            unset($add->message);
        }
        $adds = new Zend_Session_Namespace('error');
        if (isset($adds) && !empty($adds)) {
            $this->view->errorMessage = $adds->message;
            unset($adds->message);
        }
        if ($this->_request->getParam('parent') != '')
            $parent_id = $this->_request->getParam('parent');

        if ($parent_id == '') {
            $this->_helper->redirector('profile', 'client');
        } else {
            $this->view->parent_id = $parent_id;
            $this->view->userid = $parent_id;

            $clientDetail = $this->client->getDetail($parent_id, 'profile');

            //to get payment details of the client
            $client_payment_details = $this->client->getPaymentDetail($parent_id);
            if ($client_payment_details != false) {
                $this->view->client_payment_profile_id = $client_payment_details[0]['client_payment_profile_id'];
                $this->view->authorizenet_profile_id = $client_payment_details[0]['authorizenet_profile_id'];
                $this->view->authorizenet_payment_profile_id = $client_payment_details[0]['authorizenet_payment_profile_id'];

                $this->view->expiry_date = $client_payment_details[0]['exp_date'];
                $this->view->card_number = $client_payment_details[0]['card_number'];
            }

            $this->view->parent_id = $parent_id;
            $this->view->first_name = $clientDetail['firstname'];
            $this->view->last_name = $clientDetail['lastname'];
        }
    }

    /* -----------------------------------------public function to store credit card info of the user------------------------------ */

    public function savecarddetailsAction()
    {

        $error = array();
        $data = array();
        require_once APPLICATION_PATH . '/../library/Hb/anet_php_sdk/AuthorizeNet.php'; // The SDK
        $api_login_id = AUTHORIZENET_LOGIN_ID;
        $transaction_key = AUTHORIZENET_TRAN_KEY;
        $md5_setting = AUTHORIZENET_MD5_SETTING; // Your MD5 Setting

        $var = new AuthorizeNetCIM();
        //to create cunstomer profile
        $parent_id = $this->_request->getParam('parent_id');
        $card_number = $this->_request->getParam('card_number');
        $exp_month = $this->_request->getParam('exp_month');
        $exp_year = $this->_request->getParam('exp_year');
        $cvv = $this->_request->getParam('cvv');

        //  echo $cvv;die;

        $clientDetail = $this->client->getDetail($parent_id, 'profile');
        $email = $clientDetail['username'];
        //$res=$var->deleteCustomerProfile(33116600);
        //code to update payment profile if need to update
        $authorizenet_profile_id = $this->_request->getParam('authorizenet_profile_id');
        $authorizenet_payment_profile_id = $this->_request->getParam('authorizenet_payment_profile_id');
        $client_payment_profile_id = $this->_request->getParam('client_payment_profile_id');
        $user_id = $this->_request->getParam('parent_id');
        //end of updation
        if ((isset($client_payment_profile_id)) && (!empty($client_payment_profile_id))) {
            if ((isset($authorizenet_profile_id)) && (!empty($authorizenet_profile_id))) {
                if ((isset($authorizenet_payment_profile_id)) && (!empty($authorizenet_payment_profile_id))) {

                    $payment_profile = array();
                    $payment_profile['cardNumber'] = $card_number;
                    $payment_profile['expirationDate'] = $exp_year . '-' . $exp_month;
                    $payment_profile['firstname'] = $clientDetail['firstname'];
                    $payment_profile['lastname'] = $clientDetail['lastname'];
                    $val = $var->updateCustomerPaymentProfile(intval($user_id), intval($authorizenet_profile_id), intval($authorizenet_payment_profile_id), $payment_profile, ValidationMode);

                    if ($val->xml->messages->resultCode == "Ok") {
                        $update_data = array();
                        $update_data['card_number'] = substr($card_number, -4);
                        $date = $this->cal_days_in_month(1, $exp_month, $exp_year); // 31
                        $exp_date = $exp_year . '-' . $exp_month . '-' . $date;

                        $update_data['exp_date'] = $exp_date;
                        $update_data['last_updated'] = date('Y-m-d H:i:s');
                        //function to update client details
                        $this->client->updateclientpaymentdetails($client_payment_profile_id, $update_data, $user_id);

                        Zend_Session::start();
                        $add = new Zend_Session_Namespace('Success');
                        $add->message = "Card details has been saved Successfully";
                        $this->_helper->redirector->gotoUrl(ADMIN_URL . 'client/cardinfo/parent/' . $parent_id);
                    } else {
                        Zend_Session::start();
                        $adds = new Zend_Session_Namespace('error');
                        $adds->message = "Please try to update again";
                        $this->_helper->redirector->gotoUrl(ADMIN_URL . 'client/cardinfo/parent/' . $parent_id);
                    }
                }
            }
        }


        //code to chek if the profile is created by client on 10 june 2015

        /* $p_details = $this->client->get_details_payment($user_id);
          if ($p_details != false) {
          $data['customer_profile_id'] = $p_details[0]['authorizenet_profile_id'];
          $payment_profile = array();
          $payment_profile['cardNumber'] = $card_number;
          $payment_profile['expirationDate'] = $exp_year . '-' . $exp_month;
          $payment_profile['firstname'] = $clientDetail['firstname'];
          $payment_profile['lastname'] = $clientDetail['lastname'];

          //function to crate customer payment profile
          $payment_data = $var->createCustomerPaymentProfile($data['customer_profile_id'], $payment_profile);

          if ($payment_data->xml->messages->resultCode == 'Ok') {
          $insert_data = array();
          $insert_data['user_id'] = $user_id;
          $insert_data['authorizenet_profile_id'] = $p_details[0]['authorizenet_profile_id'];
          $insert_data['authorizenet_payment_profile_id'] = (string) $payment_data->xml->customerPaymentProfileId;
          $insert_data['authorizenet_shipping_id'] = $p_details[0]['shipping_id'];
          $insert_data['card_number'] = substr($card_number, -4);
          $date = $this->cal_days_in_month(1, $exp_month, $exp_year); // 31
          $exp_date = $exp_year . '-' . $exp_month . '-' . $date;
          $insert_data['exp_date'] = $exp_date;

          $insert_data['date_added'] = date('Y-m-d H:i:s');
          $insert_data['last_updated'] = date('Y-m-d H:i:s');
          $insert_data['added_by'] = 'Admin';
          $this->client->saveclientpaymentdetails($insert_data);
          Zend_Session::start();
          $add = new Zend_Session_Namespace('Success');
          $add->message = "Payments details has been saved successfullyii";
          $this->_helper->redirector->gotoUrl(ADMIN_URL . 'client/cardinfo/parent/' . $parent_id);
          } else {
          Zend_Session::start();
          $adds = new Zend_Session_Namespace('error');
          $adds->message = "Error Creating profile. Plaese try again ";
          $this->_helper->redirector->gotoUrl(ADMIN_URL . 'client/cardinfo/parent/' . $parent_id);
          }
          } */


        $val = $var->createCustomerProfile($email, $parent_id);

        if ($val->xml->messages->resultCode == 'Ok') {

            $data['customer_profile_id'] = (string)$val->xml->customerProfileId;
            $payment_profile = array();
            $payment_profile['cardNumber'] = $card_number;
            $payment_profile['expirationDate'] = $exp_year . '-' . $exp_month;
            $payment_profile['firstname'] = $clientDetail['firstname'];
            $payment_profile['lastname'] = $clientDetail['lastname'];

            //function to crate customer payment profile
            $payment_profile = $var->createCustomerPaymentProfile($data['customer_profile_id'], $payment_profile);

            if ($payment_profile->xml->messages->resultCode == 'Ok') {
                $data['customer_payment_profile_id'] = (string)$payment_profile->xml->customerPaymentProfileId;

                $shipping_profile['firstname'] = $clientDetail['firstname'];
                $shipping_profile['lastname'] = $clientDetail['lastname'];;
                $shipping_profile['phonenumber'] = $clientDetail['phone'];

                //function create customer shipping address
                $shipping_profiles = $var->createCustomerShippingAddress($data['customer_profile_id'], $shipping_profile);

                if ($shipping_profiles->xml->messages->resultCode == 'Ok') {
                    $data['customer_address_id'] = (string)$shipping_profiles->xml->customerAddressId;
                    //api to vaerify the transaction details
                    $verify_details = $var->validateCustomerPaymentProfile($data['customer_profile_id'], $data['customer_payment_profile_id'], $data['customer_address_id'], '123');
                    if ($verify_details->xml->messages->resultCode == 'Ok') {
                        $payment_info = array();
                        $payment_info['user_id'] = $parent_id;
                        $payment_info['authorizenet_profile_id'] = $data['customer_profile_id'];
                        $payment_info['authorizenet_payment_profile_id'] = $data['customer_payment_profile_id'];
                        $payment_info['authorizenet_shipping_id'] = $data['customer_address_id'];
                        $payment_info['card_number'] = substr($card_number, -4);
                        $payment_info['date_added'] = date('Y-m-d H:i:s');
                        $payment_info['last_updated'] = date('Y-m-d H:i:s');
                        //$payment_info['added_by'] = 'Admin';

                        $date = $this->cal_days_in_month(1, $exp_month, $exp_year); // 31
                        $exp_date = $exp_year . '-' . $exp_month . '-' . $date;
                        $payment_info['exp_date'] = $exp_date;

                        $this->client->saveclientpaymentdetails($payment_info);
                        Zend_Session::start();
                        $add = new Zend_Session_Namespace('Success');
                        $add->message = "Payments details has been saved successfully";
                        $this->_helper->redirector->gotoUrl(ADMIN_URL . 'client/cardinfo/parent/' . $parent_id);
                    } else {
                        $in = $data['customer_profile_id'];
                        $inf = $this->deleteProfile($in);

                        Zend_Session::start();
                        $adds = new Zend_Session_Namespace('error');
                        $adds->message = "Please provide correct credit cards details";

                        $this->_helper->redirector->gotoUrl(ADMIN_URL . 'client/cardinfo/parent/' . $parent_id);
                        //delte customer profile and send an error regarding payment profile validations
                    }
                } else {
                    //error report here and delete profile of clientr
                    $in = $data['customer_profile_id'];
                    $inf = $this->deleteProfile($in);
                    Zend_Session::start();
                    $adds = new Zend_Session_Namespace('error');
                    $adds->message = "Please provide correct cards details";
                    $this->_helper->redirector->gotoUrl(ADMIN_URL . 'client/cardinfo/parent/' . $parent_id);
                    //print_r("shipoping details failed");die;	
                }
            } else {
                $in = $data['customer_profile_id'];
                $inf = $this->deleteProfile($in);

                Zend_Session::start();
                $adds = new Zend_Session_Namespace('error');
                $adds->message = "Please provide correct credit cards details";
                $this->_helper->redirector->gotoUrl(ADMIN_URL . 'client/cardinfo/parent/' . $parent_id);
                //print_r("customer credit card info failed");die;
                //error report for credit card info
            }
        }
        if ($val->xml->messages->resultCode == 'Error') {

            Zend_Session::start();
            $adds = new Zend_Session_Namespace('error');
            $adds->message = "User profile is already created ";
            $this->_helper->redirector->gotoUrl(ADMIN_URL . 'client/cardinfo/parent/' . $parent_id);
            //$error['profile_error']=(string)$val->xml->messages->message->text;
        }
    }

//end of function


    /* ----------------function to delete profile of user if got error----------------------------- */

    private function deleteProfile($id)
    {
        $vars = new AuthorizeNetCIM();
        $res = $vars->deleteCustomerProfile($id);
    }

    /* --------------------function to calculate the last date of a month------------------------ */

    private function cal_days_in_month($calendar, $month, $year)
    {
        return date('t', mktime(0, 0, 0, $month, 1, $year));
    }

    /*     * -----------function to charge a client------------- */

    public function chargeclientAction()
    {
        $job_id = $_POST['job_id'];

        $user_id = $_POST['user_id'];
        $charge = $_POST['charge'];

        //$user_id=773;
        $payment_profile = $this->client->getPaymentDetail($user_id);

        if ($payment_profile === false) {
            $data = array();
            $data['status'] = "failed";
            $data['message'] = "Client credit card details are not available";
            echo json_encode($data);
            die;
        }
        $today_date = date('m-d-Y');
        $ex_date = $payment_profile[0]['exp_date'];

        if ($ex_date < $today_date) {
            $data = array();
            $data['status'] = "failed";
            $data['message'] = "Client credit card  has been expired";
            echo json_encode($data);
            die;
        }

        $client_info = $this->client->getDetail($user_id, 'profile');

        require_once APPLICATION_PATH . '/../library/Hb/anet_php_sdk/AuthorizeNet.php'; // The SDK
        $api_login_id = AUTHORIZENET_LOGIN_ID;
        $transaction_key = AUTHORIZENET_TRAN_KEY;
        $md5_setting = AUTHORIZENET_MD5_SETTING; // Your MD5 Setting

        $var = new AuthorizeNetCIM();

        $payment_details = array();
        $payment_details['authorizenet_profile_id'] = $payment_profile[0]['authorizenet_profile_id'];
        $payment_details['authorizenet_payment_profile_id'] = $payment_profile[0]['authorizenet_payment_profile_id'];
        $payment_details['authorizenet_shipping_id'] = $payment_profile[0]['authorizenet_shipping_id'];
        $payment_details['amount'] = $charge;

        $response = $var->createCustomerProfileTransaction($payment_details);
        $transactionResponse = $response->getTransactionResponse();
        $id = $transactionResponse->transaction_id;

        if ($id != 0) {
            $this->client->update_payment_status($user_id, $charge, $job_id);
            //query to update in jobs table
            //to insert in the client_transaction_details
            $insert_data = array();
            $insert_data['job_id'] = $job_id;
            $insert_data['client_id'] = $user_id;

            $insert_data['transaction_id'] = $id;
            $insert_data['amount'] = $charge;
            $insert_data['payment_mode'] = $transactionResponse->card_type;
            $insert_data['date_added'] = date('Y-m-d h:i:s');
            $this->client->insert_transaction_details($insert_data);

//to insert in to transaction history

            $transaction_data = array();
            $transaction_data['user_id'] = $user_id;
            $transaction_data['job_id'] = $job_id;
            $transaction_data['transaction_type'] = 'Cr.';
            $transaction_data['amount'] = $charge;
            $transaction_data['date_added'] = date('Y-m-d h:i:s');
            $this->client->insert_transaction_history($transaction_data, $id);

//to insert in to transaction history
            Zend_Session::start();
            $update = new Zend_Session_Namespace('update');
            $update->updatemessage = "Client has been charged successfully";


            $data = array();

            $data['transaction_id'] = $id;
            $data['amount'] = money_format('%.2n', $charge);
            $data['job_id'] = $job_id;
            $data['client_name'] = $client_info['firstname'] . " " . $client_info['lastname'];

            $data['status'] = "success";
            $data['message'] = "Client has been charged successfully";
            echo json_encode($data);
            die;
        } else {
        	$errormsg=explode("Reason Text:",$transactionResponse->error_message);
        	//print_r($transactionResponse);die;
            if($errormsg[1]==null||$errormsg[1]=='')
        		$errormsg[1] = "Something went wrong, please try again later.";
            $data = array();
            $data['status'] = "failed";
            $data['message'] = $errormsg[1];//"A duplicate transaction has been occured";
            echo json_encode($data);
            die;
        }
    }

    /* ----------------------------function to load a page -------------------- */

    public function addchildrenAction()
    {
        $this->_helper->layout->disableLayout();
        $this->view->addchildren = "asd";
    }

    /* --------------------function to remove the sitter from confirmed job--------------------------------- */

    public function removeAction()
    {
        $job_id = $_POST['job_id'];
        $this->jobs = new Hb_Jobs();
       	$sitter=$this->jobs-> get_jobs_sitter($job_id);
       	
       	$this->jobs->jobMail($job_id, 'admin_denied_job_to_sitter', 'sitter', 'mail');
        $this->jobs->remove_sitter($job_id,$sitter);
        
        $results = $this->jobs->get_device_token($sitter);
        
        if (!empty($results)) {
        
        	$noti_data = array();
        	$noti_data['job_id'] = $job_id;
        	$noti_data['userid'] = $sitter;
        	$noti_data['date_added'] = date('Y-m-d H:i:s');
        	$noti_data['date_updated'] = date('Y-m-d H:i:s');
        
        	$notification_id = $this->notification->insert_notification_data($noti_data);
        
        	//get unread notification count
        	$count = $this->notification->get_unread_count($sitter);
        
        	$jstaus=$this->notification->getJobStatus($job_id);
        
        	$this->ipush = new Hb_Ipushnotification();
        	$message = "Admin denied you from previously booked job";
        
        	foreach ($results as $noti) {
        
        		$noti_type = 2;
        		if ((!empty($noti['userid'])) && (!empty($noti['deviceToken']))) {
        
        			$this->ipush->send_notification_sitter($noti['deviceToken'], $message, $job_id, $noti['userid'], $noti_type, $count, $notification_id,$jstaus);
        		}
        	}
        }
        $data = array();
        $data['status'] = "success";
        echo json_encode($data);
        die;
    }
    
    public function listeventsAction()
    {
    	$this->jobs = new Hb_Jobs();
    	$this->client = new Hb_Client();
    
    	if ($this->_request->getParam('user') != '')
    		$client = $this->_request->getParam('user');
    
    	if ($client == '')
    		$this->_helper->redirector('profile', 'client');
    	else {
    		$this->view->userid = $client;
    
    		$this->view->credits = $this->jobs->checkCredits($this->view->userid);
    	}
    	
    	$this->view->open = $this->jobs->searchnew(array('status'=>'open','rows'=>5,'client_id'=>$this->view->userid,'key'=>'jobs_posted_date','odr'=>'desc'));
       	$this->view->scheduled = $this->jobs->searchnew(array('status'=>'scheduled','rows'=>5,'client_id'=>$this->view->userid,'key'=>'job_start_date','odr'=>'asc'));
       	$this->view->active = $this->jobs->searchnew(array('status'=>'active','rows'=>5,'client_id'=>$this->view->userid,'key'=>'job_start_date','odr'=>'asc'));
        $this->view->completed = $this->jobs->searchnew(array('status'=>'completed','rows'=>5,'client_id'=>$this->view->userid,'key'=>'completed_date','odr'=>'desc'));
        $this->view->closed = $this->jobs->searchnew(array('status'=>'closed','rows'=>5,'client_id'=>$this->view->userid,'key'=>'completed_date','odr'=>'desc'));
        $this->view->cancelled = $this->jobs->searchnew(array('status'=>'cancelled','rows'=>5,'client_id'=>$this->view->userid,'key'=>'cancelled_date','odr'=>'desc'));
        
        
        $this->events['total'] = $this->view->events['total'];
    	$this->view->events = $this->view->events['rows'];
    	// echo count($this->view->events);
    	range(1, $this->events['total'] - 1);
    	$this->view->paginator = Zend_Paginator::factory(range(1, $this->events['total'] - 1));
    
    	$this->view->paginator->setCurrentPageNumber((int)$this->searchParams['page'] ? $this->searchParams['page'] : 1);
    	$this->view->paginator->setItemCountPerPage((int)$this->searchParams['rows'] ? $this->searchParams['rows'] : 10);
    	$this->view->paginator->setPageRange(10);
    	$this->view->userInfo = $this->client->getDetail($this->view->userid, 'profile');
    }

    /* client transaction history*/
    public function clienttransactionsAction()
    {
    	$this->jobs = new Hb_Jobs();
    	$this->client = new Hb_Client();
    
    	$this->reports = new Hb_Reports();
        $this->view->client_transaction_history = $this->client->client_transaction_history($this->searchParams);

        $sum1 = 0;
        foreach ($this->view->client_transaction_history['record'] as $keyp => $subArray) {
            $sum1+=(int) $subArray['amount'];
        }
        $this->view->paginator = Zend_Paginator::factory(range(1, ((int) $this->view->client_transaction_history['total'] )));
        $this->view->paginator->setCurrentPageNumber((int) $this->searchParams['page'] ? $this->searchParams['page'] : 1);
        $this->view->paginator->setItemCountPerPage((int) $this->searchParams['rows'] ? $this->searchParams['rows'] : 10 );
        $this->view->paginator->setPageRange(10);
        $this->view->client_transaction_history_details = $this->view->client_transaction_history['rows'];
        $this->view->clients = $this->reports->clients();
        $this->view->total_amount = $sum1;
    }
    
    public function transactiondetailsAction() {
    	$payment_id = $_POST['transaction_id'];
    	$result = $this->client->get_transaction_details($payment_id);
    	$data = array();
    	$data['client_id'] = $result[0]['client_id'];
    	$data['name'] = $result[0]['firstname'] . " " . $result[0]['lastname'];
    	$data['job_id'] = $result[0]['job_id'];
    
    	$data['date_added'] = date('m/d/Y h:i:a', strtotime($result[0]['date_added']));
    	$c_n = $result[0]['transaction_id'];
    
    	$data['total_amount'] = money_format('%.2n',$result[0]['amount']);
    	$data['transaction_id'] = $c_n;
    	$data['payment_mode'] = $result[0]['payment_mode'];
    	$data['status'] = "success";
    	echo json_encode($data);
    	die;
    }
    
    public function bookinghistoryAction()
    {
    
    	$this->view->subscribeForm = $this->subscribeForm();
    
    	if ($this->_request->getParam('client') != '')
    		$client = $this->_request->getParam('client');
    	
    	//$this->view->subscriptions = $this->client->getSubscription(array_merge(array('userid' => $this->view->userid), $this->searchParams));
    	//by anjali
    	$this->view->subscriptions = $this->client->bookinghistory(array_merge($this->searchParams));
    	$this->view->paginator = Zend_Paginator::factory(range(1, ((int)$this->view->subscriptions['total'] - 1)));
    	$this->view->total_avail_credits = $this->view->subscriptions['total_avail_credits'];
    
    	//by anjali
    	$this->view->purchased_credits = $this->view->subscriptions['purchased_credits'];
    
    	$this->view->subscriptions = $this->view->subscriptions['rows'];
    	$this->view->paginator->setCurrentPageNumber((int)$this->searchParams['page'] ? $this->searchParams['page'] : 1);
    	$this->view->paginator->setItemCountPerPage((int)$this->searchParams['rows'] ? $this->searchParams['rows'] : 10);
    	$this->view->paginator->setPageRange(10);
    }
    
    public function jobbookinghistoryAction()
    {
    	$this->view->subscribeForm = $this->subscribeForm();
    	
    	if ($this->_request->getParam('client') != '')
    		$client = $this->_request->getParam('client');
    	
    	if ($client == '')
    		$this->_helper->redirector('index', 'client');
    	else {
    		$this->view->userid = $client;
    	}
    	if ($this->searchParams['delete'] > 0) {
    		$this->client->deleteSubscription($this->searchParams['delete']);
    		$this->_helper->redirector->gotoUrl($_SERVER['HTTP_REFERER']);
    	}
    	//$this->view->subscriptions = $this->client->getSubscription(array_merge(array('userid' => $this->view->userid), $this->searchParams));
    	//by anjali
    	$this->view->subscriptions = $this->client->getSubscriptionnew(array_merge(array('userid' => $this->view->userid), $this->searchParams));
    	$this->view->paginator = Zend_Paginator::factory(range(1, ((int)$this->view->subscriptions['total'] - 1)));
    	$this->view->total_avail_credits = $this->view->subscriptions['total_avail_credits'];
    	
    	//by anjali
    	$this->view->purchased_credits = $this->view->subscriptions['purchased_credits'];
    	
    	$this->view->subscriptions = $this->view->subscriptions['rows'];
    	$this->view->paginator->setCurrentPageNumber((int)$this->searchParams['page'] ? $this->searchParams['page'] : 1);
    	$this->view->paginator->setItemCountPerPage((int)$this->searchParams['rows'] ? $this->searchParams['rows'] : 10);
    	$this->view->paginator->setPageRange(10);
    	$this->view->userInfo = $this->client->getDetail($this->view->userid, 'profile');
    	
    	if ($this->searchParams['success'] == 'payment') {
    		$purchasedSub = $this->searchParams['subs'];
    		foreach ($this->view->subscriptions as $s) {
    			$sub[$s['client_sub_id']] = $s;
    		}
    		$purchasedSub = $sub[$purchasedSub];
    		$this->view->successMessage = 'You have successfully purchased ' . $purchasedSub['slots'] . ' credit(s) for ' . $purchasedSub['price'] . 'USD. Transction ID: ' . $purchasedSub['transaction_id'];
    		$checkout = new Zend_Session_Namespace('checkout');
    	
    		unset($checkout->selectedPackage);
    	}
    	if ($this->searchParams['success'] == 'free') {
    		$purchasedSub = $this->searchParams['subs'];
    		foreach ($this->view->subscriptions as $s) {
    			$sub[$s['client_sub_id']] = $s;
    		}
    		$purchasedSub = $sub[$purchasedSub];
    		$this->view->successMessage = 'You have successfully added ' . $purchasedSub['slots'] . ' credit(s) for Free';
    		$this->view->searchParams['success'] = 'payment';
    	}
    }

//for removing all dispatched sitters
    public function removeallAction()
    {
    	$job_id = $_POST['job_id'];
    	
    	if($job_id!=null && $job_id!="")
    	{
    		$this->jobs = new Hb_Jobs();
    		$this->jobs->removeallsitters($job_id);
    		$this->jobs->updatetotalassign($job_id);
    	}
    	
    	$data = array();
    	$data['status'] = "success";
    	echo json_encode($data);
    	die;
    }
}

// end of class	
