<?php

class SittersController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->sitter = new Hb_Sitter();
        $this->notification = new Hb_Notification();
        $this->searchParams = $this->_request->getParams();
        $this->view->searchParams = $this->searchParams;

        $this->view->actionName = $this->_request->getActionName();

        if (Zend_Auth::getInstance()->getIdentity()->userid != '')
            $sitter = Zend_Auth::getInstance()->getIdentity()->userid;

        if ($sitter == '') {
            $this->_helper->redirector('index', 'index');
        } else {
            $this->view->userid = $sitter;
            $this->view->userInfo = $this->sitter->getDetail($this->view->userid, 'profile');
            if ($this->view->actionName != 'profile' && $this->_request->getControllerName() == 'sitters')
                if ((int)$this->view->userInfo['profile_completed'] <= 0) {
                    $this->_helper->redirector('profile', 'sitters');
                }
            //$this->view->credits = $this->jobs->checkCredits($this->view->userid);			 
        }
    }


    public function childrenAction() {

        $this->jobs = new Hb_Jobs();
        $this->client = new Hb_Client();
        $this->common = new Hb_Common();


        $this->cForm = $this->childForm();
        $request = $this->getRequest();


        if ($request->isPost()) {

            if ($this->cForm->isValid($request->getPost())) {


                $job_id = $request->getPost('job_id');;
                
                $val = array();
                $val['child_name'] = $request->getPost('child_name');

                $val['dob'] = $request->getPost('dob'); ;
                $val['sex'] =  $request->getPost('sex');;
                $val['allergy_status'] =  $request->getPost('allergy_status');;
                $val['allergies'] =  $request->getPost('allergies');;
                $val['medicator_status'] =  $request->getPost('medicator_status');;
                $val['medicator'] = $request->getPost('medicator'); ;
                $val['image'] = $_FILES['image'];
                $val['notes'] =  $request->getPost('notes');;
                $val['job_child_added_by'] = 1;
                $val['special_needs'] = trim( $request->getPost('special_needs'));
                $val['fav_food'] = trim( $request->getPost('fav_food'));
                $val['fav_book'] = trim( $request->getPost('fav_book'));
                $val['fav_cartoon'] = trim(  $request->getPost('fav_cartoon'));
                //$val['added_by_child'] = 'sitters';


                if($_POST['special_needs']!=null ||$_POST['special_needs']!='')
                    $val['special_needs_status']= 'Yes';
                else
                    $val['special_needs_status']= 'No';

                $parent_id = '0';


                $child_id = $this->client->children->addChildNew($val, $parent_id);
                $this->client->children->addChildToJob($child_id, $job_id);


                //don't chage this position of this call
                $job_info = $this->jobs->getJobInfo($job_id,true);

                if( $val['special_needs_status'] == 'Yes')
                {
                    $update_data = array();
                    $update_data['is_special'] = '1';
                    $this->jobs->updateJob($job_id,$update_data);
                }

                // sitters children logic to update other info of children
                $this->common->updateJobChildQunatityInfo($job_id,'sitter',$job_info->is_special);


                $data = array();
                $data['status'] = "success";
                $data['message'] = "Child has been added to job successfully";
                echo json_encode($data);
                die;
            }
            else
            {

                //$this->cForm->getMessages();

                $response = array();
                $response['status'] = 'error';
                $response['message'] = "Something went wrong, please refresh page and try again.";
                echo json_encode($response);
                exit;
                exit;


            }
        }

        $response = array();
        $response['status'] = 'error';
        $response['message'] = "Something went wrong, please refresh page and try again.";
        echo json_encode($response);
        exit;

        die;
    }


    public function passwordAction()
    {

        $user = $this->sitter->getDetail($this->view->userid);

        if ($this->_request->isPost()) {
            if ($_POST['oldpassword'] == $user['password']) {

                if ($_POST['newpassword'] == $_POST['repeatpassword']) {
                    $this->sitter->changePassword($this->view->userid, $_POST['newpassword']);
                    $this->view->successMsg = 'Your password has been successfully changed!';
                } else {
                    $this->view->errorMsg = 'Confirm your password!';
                }
            } else {
                $this->view->errorMsg = 'Current Password is incorrect!';
            }
        }
    }

    public function previewAction()
    {

        $sitter = Zend_Auth::getInstance()->getIdentity()->userid;

        $this->jobs = new Hb_Jobs();
        $this->view->job_id = $this->_request->getParam('job');
        $this->view->event = $this->jobs->searchnew(array('job_id' => $this->view->job_id));
        $this->view->event = $this->view->event['rows'][$this->view->job_id];
        $this->view->children = $this->view->event['children'];
        $this->view->jobPreferences = $this->jobs->getPreferences($this->view->job_id);

        $this->client = new Hb_Client();
        $this->view->address = $this->client->getAddress(array('address_id' => $this->view->event['address_id'], 'userid' => $this->view->event['client_user_id']));
        $this->view->address = $this->view->address[0];

        $this->hbSettings = new Hb_Settings();
        $this->view->states = $this->hbSettings->getStates(223);
        foreach ($this->view->states as $state) {
            $states[$state['zone_id']] = $state['name'];
        }

        $this->view->states = $states;
        $re = $this->jobs->getsitterjob($this->view->job_id, $sitter);
        $this->view->is_accept = $re;
    }

    public function indexAction()
    {

        $search = array_merge($this->searchParams, array('sitter_id' => $this->view->userid));
        //$search['status']=$this->_request->getParam('view');
        $this->jobs = new Hb_Jobs();
        //$this->view->pendingJobs = $this->jobs->getSitterJobs(array('status' => array('pending'), 'sitter_id' => $this->view->userid));
        //$this->view->confirmedJobs = $this->jobs->search(array('status' => array('confirmed'), 'sitter_id' => $this->view->userid));
        //$this->view->completedJobs = $this->jobs->search(array('status' => array('completed'), 'sitter_id' => $this->view->userid));

        //sitter dashboard changes by anjali
        $this->view->pendingJobs = $this->jobs->getSitterJobs(array('status' => array('pending'), 'rows' => 5, 'sitter_id' => $this->view->userid));
        $this->view->confirmedJobs = $this->jobs->searchnew(array('status' => 'scheduled', 'rows' => 5, 'sitter_id' => $this->view->userid));
        $this->view->activeJobs = $this->jobs->searchnew(array('status' => 'active', 'rows' => 5, 'sitter_id' => $this->view->userid));
        $this->view->cancelledJobs = $this->jobs->searchnew(array('status' => 'cancelled', 'rows' => 5, 'sitter_id' => $this->view->userid));
        $this->view->closedJobs = $this->jobs->searchnew(array('status' => 'closed', 'rows' => 5, 'sitter_id' => $this->view->userid));
        $this->view->completedJobs = $this->jobs->searchnew(array('status' => 'completed', 'rows' => 5, 'sitter_id' => $this->view->userid));
        //end sitter dashboard changes

    }

    function checkbirthdate($month, $day, $year)
    {
        $min_age = 18;
        $max_age = 122;

        if (!checkdate($month, $day, $year)) {
            return false;
        }

        list($this_year, $this_month, $this_day) = explode(',', date('Y,m,d'));

        $min_year = $this_year - $max_age;
        $max_year = $this_year - $min_age;

        // print "$min_year,$max_year,$month,$day,$year\n";

        if (($year > $min_year) && ($year < $max_year)) {
            return true;
        } elseif (($year == $max_year) &&
            (($month < $this_month) ||
                (($month == $this_month) && ($day <= $this_day)))
        ) {
            return true;
        } elseif (($year == $min_year) &&
            (($month > $this_month) ||
                (($month == $this_month && ($day > $this_day))))
        ) {
            return true;
        } else {
            return false;
        }
    }

    public function profileAction()
    {
        // action body
        //$this->_request->getParams(); 
        $profile = new Zend_Session_Namespace('profile');

        $this->view->successMessage = $profile->successMessage;
        unset($profile->successMessage);


        $this->hbSettings = new Hb_Settings();
        $this->view->preferences = $this->hbSettings->getPreferences();
        $this->view->sitterPreference = array();
        $this->userform = $this->userForm();
        $this->userform->addSubForms(array('local' => $this->addressForm()));
        $this->userform->local->setIsArray(true);

        $modify = false;
        if ($this->view->userid != '') {

            $sitterDetail = $this->sitter->getDetail($this->view->userid, 'profile');
            //echo '<pre>';print_r($sitterDetail);echo '</pre>';
            if ($sitterDetail) {
                $modify = true;
                $this->view->userid = $this->view->userid;
                //print_r($sitterDetail);
                $this->view->sitterPreference = $this->sitter->getPreferences($this->view->userid);
                $this->userform->setAction(SITE_URL . 'sitters/profile');
                $this->userform->firstname->setValue($sitterDetail['firstname']);
                $this->userform->middlename->setValue($sitterDetail['middlename']);
                $this->userform->lastname->setValue($sitterDetail['lastname']);
                $this->userform->username->setValue($sitterDetail['username']);
                $this->userform->traits->setValue($sitterDetail['traits']);
                $this->userform->about_me->setValue($sitterDetail['about_me']);
                $this->userform->exp_summary->setValue($sitterDetail['exp_summary']);
                $this->userform->local_phone->setValue($sitterDetail['local_phone']);
                $this->userform->work_phone->setValue($sitterDetail['work_phone']);
                $this->userform->dob->setValue($sitterDetail['dob']);
                $this->userform->phone->setValue($sitterDetail['phone']);
                //$this->userform->have_car->setValue($sitterDetail['have_car']);
                $this->userform->cpr_holder->setValue($sitterDetail['cpr_holder']);
                $this->userform->cpr_adult->setValue($sitterDetail['cpr_adult']);
                //$this->userform->have_child->setValue($sitterDetail['have_child']);
                //$this->userform->criminal_record->setValue($sitterDetail['criminal_record']);
                $this->userform->first_aid_cert->setValue($sitterDetail['first_aid_cert']);
                //$this->userform->clean_drive_record->setValue($sitterDetail['clean_drive_record']);
                $this->userform->otherpreference->setValue($sitterDetail['otherpreference']);
//by anjali
                $this->userform->water_certification->setValue($sitterDetail['water_certification']);
                $this->userform->hampton_babysitter_training->setValue($sitterDetail['hampton_babysitter_training']);
                $this->userform->is_student->setValue($sitterDetail['is_student']);
                $this->userform->highest_edu->setValue($sitterDetail['highest_edu']);
                $this->userform->lifeguard->setValue($sitterDetail['lifeguard']);
                if ($sitterDetail['special_need_exp'] == '' || $sitterDetail['special_need_exp'] == null)
                    $this->userform->sneed_exp->setValue(0);
                else
                    $this->userform->sneed_exp->setValue(1);
                $this->userform->special_need_exp->setValue($sitterDetail['special_need_exp']); //by anjali


                $this->view->profile_thumb_image = $sitterDetail['thumb_image'];
                unset($this->userform->password);
                $status = $sitterDetail['status'];
                $this->view->reference = $this->sitter->getReference($this->view->userid);
                $this->view->educations = $this->sitter->getEducation($this->view->userid);
            }
            $this->view->local = $this->sitter->getAddress(array('userid' => $this->view->userid, 'address_type' => 'local'));
            $this->view->local = $this->view->local[0];

            if (isset($this->view->local)) {
                $local_address_id = $this->view->local['address_id'];
                $this->userform->local->address_1->setValue($this->view->local['address_1']);
                $this->userform->local->streat_address->setValue($this->view->local['streat_address']);
                $this->userform->local->zipcode->setValue($this->view->local['zipcode']);
                $this->userform->local->city->setValue($this->view->local['city']);
                $this->userform->local->state->setValue($this->view->local['state']);
            }
        }
        if (!$modify) {

            $status = 'active';
        }


        //print_r($_FILES);
        $request = $this->getRequest();
        if ($request->isPost()) {


            //print_r($this->userform->getValues());

            if ($this->userform->isValid($request->getPost())) {
                $values = $this->userform->getValues();

// check December 3, 1974


                $values['dob'];

                $user = new Hb_User();
                $userAvailable = $user->checkUsername($values['username'], $this->view->userid);
                if (empty($userAvailable)) {
                    $datechecking = explode('/', $values['dob']);
                    if ($this->checkbirthdate($datechecking[0], $datechecking[1], $datechecking[2])) {


                        $_FILES['profile_image']['tmp_name'] = $this->userform->profile_image->getFileName();
                        $values['profile_image'] = $_FILES['profile_image'];
                        $values['status'] = $status;
                        $values['prefer'] = $_POST['prefer'];
                        $values['reference'] = $_POST['reference'];
                        $values['education'] = $_POST['education'];
                        if (!$modify) {
                            if ($this->sitter->create($values, $status)) {
                                // We're authenticated! Redirect to the home page
                                $flag = 'address';

                                //$this->_helper->redirector->gotoUrl(SITE_URL.'sitters/');
                            }
                        } else if ($this->sitter->modify($values, $this->view->userid)) {
                            $flag = 'address';
                        }


                        if ($flag == 'address') {
                            if ($local_address_id > 0) {
                                $values = $_POST['local'];
                                $values['userid'] = $this->sitter->sitter_id;
                                $values['address_type'] = 'local';

                                $this->sitter->updateAddress($values, $local_address_id);
                            } else {
                                $values = $_POST['local'];
                                $values['userid'] = $this->sitter->sitter_id;
                                //$values['address_id']=$this->sitter->id;
                                $values['address_type'] = 'local';
                                $this->sitter->addAddress($values);
                            }


                            if ($modify)
                                $profile->successMessage = $this->view->globalMessages['SitterProfileModifiedSuccess'];
                            else
                                $profile->successMessage = 'User have been sucessfully created.';
                            $this->_helper->redirector->gotoUrl(SITE_URL . 'sitters/profile/');
                        }
                        //$this->_helper->redirector->gotoUrl(SITE_URL.'sitters/');
                    } else
                        $this->userform->dob->addError($this->view->globalMessages['SitterAgeNotMatched']);
                } else {
                    $this->userform->username->addError('Username is not available');
                }
            }
            for ($i = 0; $i < count($_POST['education']['institution']); $i++) {
                $edu[$i] = array('institution' => $_POST['education']['institution'][$i], 'start_date' => $_POST['education']['start_date'][$i], 'end_date' => $_POST['education']['end_date'][$i], 'degree' => $_POST['education']['degree'][$i]);
            }

            for ($i = 0; $i < count($_POST['reference']['refered_by']); $i++) {
                $ref[] = array('refered_by' => $_POST['reference']['refered_by'][$i], 'phone' => $_POST['reference']['phone'][$i]);
            }
            $this->view->reference = $ref; //$this->sitter->getReference($this->view->userid);
            $this->view->educations = $edu; //$this->sitter->getEducation($this->view->userid);
        }
        $this->view->userform = $this->userform;

        //}
    }

    public function userForm()
    {
        $form = new Zend_Form();

        $form->setName("userform");
        $form->setMethod('post');
        $form->setAttrib('enctype', 'multipart/form-data');
        $form->setAction(SITE_URL . 'sitters/profile/create/new/');

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


        $form->addElement('file', 'profile_image', array(
            //'filters'    => array('StringTrim'),
            'validators' => array(
                array('Extension', false, 'jpg,png,gif,jpeg'),
            ),
            'required' => false,
            'label' => 'Profile Image:',
        ));

        $form->addElement('text', 'dob', array(
            'validators' => array(
                array('NotEmpty', false, array('messages' => array('isEmpty' => 'Please select Date of Birth.'))),
            ),
            'required' => true,
            'label' => 'Date of Birth:',
        ));
        $form->addElement('text', 'phone', array(
            'validators' => array(
                array('StringLength', false, array(0, 50)),
            ),
            'required' => true,
            'label' => 'Phone:',
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
        $form->addElement('textarea', 'about_me', array(
            'required' => false,
            'label' => 'about me',
        ));
        $form->addElement('textarea', 'traits', array(
            'required' => false,
            'label' => 'about me',
        ));
        $form->addElement('textarea', 'exp_summary', array(
            'required' => false,
            'label' => 'Experience Summary',
        ));

        /* $form->addElement('radio', 'have_car', array(
            'MultiOptions' => array('yes', 'no'),
            'required' => true,
            'label' => 'Have Car?',
        )); */
        $form->addElement('radio', 'cpr_adult', array(
            'MultiOptions' => array('yes', 'no'),
            'required' => true,
            'label' => 'Have Car?',
        ));
        $form->addElement('radio', 'cpr_holder', array(
            'MultiOptions' => array('yes', 'no'),
            'required' => true,
            'label' => 'Have Car?',
        ));
        $form->addElement('radio', 'first_aid_cert', array(
            'MultiOptions' => array('yes', 'no'),
            'required' => true,
            'label' => 'Have Car?',
        ));
        /* $form->addElement('radio', 'clean_drive_record', array(
            'MultiOptions' => array('yes', 'no'),
            'required' => true,
            'label' => 'Have Car?',
        ));
        $form->addElement('radio', 'criminal_record', array(
            'MultiOptions' => array('yes', 'no'),
            'required' => true,
            'label' => 'Have Car?',
        ));
        $form->addElement('radio', 'have_child', array(
            'MultiOptions' => array('yes', 'no'),
            'required' => false,
            'label' => 'Have Car?',
        )); */

        $form->addElement('textarea', 'otherpreference', array(
            'required' => false,
            'label' => 'Other Preference',
        ));

        $form->addElement('submit', 'create', array(
            'required' => false,
            'ignore' => true,
            'label' => 'Create',
        ));

        //by anjali
        $form->addElement('radio', 'water_certification', array(
            'MultiOptions' => array('yes', 'no'),
            'required' => true,
            'label' => 'Water Certification?',
        ));
        $form->addElement('radio', 'hampton_babysitter_training', array(
            'MultiOptions' => array(0, 1),
            'required' => true,
            'label' => 'Hamptons babysitter training?',
        ));
        $form->addElement('radio', 'is_student', array(
            'MultiOptions' => array(0, 1),
            'required' => true,
            'label' => 'Student?',
        ));
        $form->addElement('text', 'highest_edu', array(
            'required' => false,
            'label' => 'Highest Education Attainded',
        ));
        $form->addElement('radio', 'lifeguard', array(
            'MultiOptions' => array('yes', 'no'),
            'required' => true,
            'label' => 'Lifeguard',
        ));
        $form->addElement('radio', 'sneed_exp', array(
            'MultiOptions' => array(0, 1),
            'required' => true,
            'label' => 'Special Need Experienced',
        ));
        $form->addElement('text', 'special_need_exp', array(
            'required' => false,
            'label' => 'Special Need Experienced',
        ));
        /*$form->addElement('select', 'special_need_exp', array(
         'required' => false,
        		'label' => 'Special Need Experienced',
        		'multiOptions' => $this->view->special_needs_exp_list
        ));
        $field=$form->getElement('special_need_exp');
        $field->setRegisterInArrayValidator(false);*/ //by anjali

        return $form;
    }

    public function addressAction()
    {
        // action body


        $this->adForm = $this->addressForm();
        // echo $this->_request->getParam('user');die();
        if ($this->_request->getParam('userid') != '')
            $userid = $this->_request->getParam('userid');

        if ($userid == '')
            $this->_helper->redirector('profile', 'sitters');
        else {
            $this->view->userid = $userid;
        }

        $this->adForm->setAction(SITE_URL . 'sitters/address/userid/' . $userid);

        if ($this->view->userid != '') {
            $this->view->address = $this->sitter->getAddress(array('userid' => $this->view->userid, 'address_id' => $this->view->userid));
            $this->view->address = $this->view->address[0];
            if (isset($this->view->address)) {
                $address_id = $this->view->userid;
                $this->adForm->setAction(SITE_URL . 'sitters/address/userid/' . $userid . '/modify/' . $address_id);
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
                    $this->sitter->updateAddress($values, $address_id);
                } else {
                    $this->sitter->addAddress($values);
                }
            }
        }
        $this->view->addresses = $this->sitter->getAddress(array('userid' => $this->view->userid));
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
        ));

        $form->addElement('text', 'streat_address', array(
            'validators' => array(
                array('StringLength', false, array(0, 255)),
            ),
            'required' => true,
            'label' => 'Streat Address:',
        ));
        $form->addElement('text', 'zipcode', array(
            'validators' => array(
                array('StringLength', false, array(0, 10)),
            ),
            'required' => true,
            'label' => 'Zip Code:',
        ));
        $form->addElement('text', 'city', array(
            'validators' => array(
                array('StringLength', false, array(0, 100)),
            ),
            'required' => true,
            'label' => 'City:',
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


        $form->addElement('submit', 'addressSubmit', array(
            'required' => false,
            'ignore' => true,
            'label' => 'Add Address',
        ));

        return $form;
    }

    public function jobsAction()
    {
        // action body
        $this->jobs = new Hb_Jobs();

        $this->view->eventsForm = $this->eventsForm();

        $error = new Zend_Session_Namespace('error');

        if ((isset($error->errormessage)) && (!empty($error->errormessage))) {
            $this->view->errorMessage = $error->errormessage;
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $formValues = $request->getPost();
            if (is_array($formValues['jobaction'])) {

                if (current($formValues['jobaction']) == 'Accept') {

                    $job_id = array_shift(array_flip($formValues['jobaction']));

                    //need to chek if the status of job is changed after that implemet the code to accept the job by namrata
                    //9 september 2015

                    $status = $this->jobs->get_job_status($job_id);

                    if ($status == false) {
                        $error = new Zend_Session_Namespace('error');
                        $error->errormessage = "Job has been cancelled by the client";


                        $this->_helper->redirector->gotoUrl(SITE_URL . 'sitters/jobs');
                    }


                    if (($status == 'confirmed') || ($status == 'cancelled') || ($status == 'completed') || ($status == '')) {
                        if ($status == 'confirmed') {
                            $error = new Zend_Session_Namespace('error');
                            $error->errormessage = "Job already confirmed by another sitter";


                            $this->_helper->redirector->gotoUrl(SITE_URL . 'sitters/jobs');
                        }

                        if ($status == 'cancelled') {
                            $error = new Zend_Session_Namespace('error');
                            $error->errormessage = "Job has been cancelled ";


                            $this->_helper->redirector->gotoUrl(SITE_URL . 'sitters/jobs');
                        }


                        //  print_r($status);die;

                        $this->_helper->redirector->gotoUrl(SITE_URL . 'sitters/preview/job/' . $job_id);
                    }


                    if ($this->jobs->confirmJob($job_id, $this->view->userid)) {
                        //by namrata ipush notifications

                        $results = $this->jobs->get_device_token_parent($job_id);

                        if (!empty($results)) {

                            //  print_r($results[0]['client_user_id']);die;
                            //to insert in to notification table

                            $noti_data = array();
                            $noti_data['job_id'] = $job_id;

                            $noti_data['userid'] = $results[0]['client_user_id'];
                            $noti_data['date_added'] = date('Y-m-d H:i:s');
                            $noti_data['date_updated'] = date('Y-m-d H:i:s');

                            $notification_id = $this->notification->insert_notification_data($noti_data);

                            //get unread notification count
                            $count = $this->notification->get_unread_count($results[0]['client_user_id']);


                            $this->ipush = new Hb_Ipushnotification();
                            $message = "Your job request has been confirmed";

                            foreach ($results as $noti) {

                                $this->ipush->send_notification_parent($noti['deviceToken'], $message, $job_id, $results[0]['client_user_id'], $count, $notification_id);
                            }
                        }


                        $this->_helper->redirector->gotoUrl(SITE_URL . 'sitters/preview/job/' . $job_id);
                    }
                } else if (current($formValues['jobaction']) == 'Complete') {
                    $job_id = array_shift(array_flip($formValues['jobaction']));
                    $data = array('sitter_id' => $this->view->userid,
                        'job_id' => $job_id,
                        'total_paid' => $formValues['total_paid'][$job_id],
                        'job_start_date' => $formValues['completed_date'][$job_id]['start_date'],
                        'completed_date' => $formValues['completed_date'][$job_id]['end_date'],
                    );
                    if ($this->jobs->completeJob($data)) {

                        //by namrata
                        //
                        //
                        $results = $this->jobs->get_device_token_parent($job_id);
                        // print_r($results);die;
                        if (!empty($results)) {

                            $noti_data = array();
                            $noti_data['job_id'] = $job_id;

                            $noti_data['userid'] = $results[0]['client_user_id'];
                            $noti_data['date_added'] = date('Y-m-d H:i:s');
                            $noti_data['date_updated'] = date('Y-m-d H:i:s');

                            $notification_id = $this->notification->insert_notification_data($noti_data);

                            //get unread notification count
                            $count = $this->notification->get_unread_count($results[0]['client_user_id']);

                            $this->ipush = new Hb_Ipushnotification();
                            $message = "Your job  has been completed";
                            foreach ($results as $noti) {

                                $this->ipush->send_notification_parent($noti['deviceToken'], $message, $job_id, $results[0]['client_user_id'], $count, $notification_id);
                            }
                        }
                        //uncommented second line byn namrata
                        $this->_helper->redirector->gotoUrl(SITE_URL . 'sitters/jobs/view/completed/');
                    } else {
                        $this->_helper->redirector->gotoUrl(SITE_URL . 'sitters/jobs/view/scheduled/');
                    }
                }
            }
        }

        //$this->view->userid=$this->_request->getParam('user');
        //var_dump($this->_request->getParam('view'));
        $search = array_merge($this->searchParams, array('sitter_id' => $this->view->userid));
        if (!$this->searchParams['view'])
            $search['status'] = $this->searchParams['view'] = $this->view->searchParams['view'] = 'pending';
        else
            $search['status'] = $this->searchParams['view'];
        //$search['status']=$this->_request->getParam('view');

        if ($this->searchParams['view'] == 'pending') {

            $search['status'] = array('pending');
            $search['sitter_id'] = $this->view->userid;

            $this->view->jobs = $this->jobs->getSitterJobs($search);


            // print_r(($this->view->jobs));die;
        } else {
            //print_r($search);
            //$this->view->jobs = $this->jobs->search($search);  //commented for new job status
            $this->view->jobs = $this->jobs->searchnew($search);
        }

//removed -1
        $this->view->paginator = Zend_Paginator::factory(range(1, ((int)$this->view->jobs['total'])));

        $this->view->paginator->setCurrentPageNumber((int)$this->searchParams['page'] ? $this->searchParams['page'] : 1);
        $this->view->paginator->setItemCountPerPage((int)$this->searchParams['rows'] ? $this->searchParams['rows'] : 10);
        $this->view->paginator->setPageRange(10);
        $this->view->jobs = $this->view->jobs['rows'];


        if ((isset($error->errormessage)) && (!empty($error->errormessage))) {
            unset($error->errormessage);
        }
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

            $to_emails = explode(',', $_POST['emails']);;
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
            $this->view->successMsg = "Your message was sent successfully.";
        }
        //$this->setViewScript('../client/referafriend.phtml');	
    }

    public function cancelconfirmedAction()
    {
        $this->jobs = new Hb_Jobs();

        $this->view->job_id = $this->_request->getParam('job');
        $this->view->event = $this->jobs->search(array('job_id' => $this->view->job_id));
        //print_r($this->view->event);
        $this->view->event = $this->view->event['rows'][$this->view->job_id];
        if ((strtotime($this->view->event['job_start_date']) - time()) >= 86400) {
            if ($this->view->event['sitter_user_id'] == $this->view->userInfo['userid'] && $this->view->job_id != '') {
                $this->jobs->cancelConfirm($this->view->job_id,$this->view->event['sitter_user_id']);// for siter

                //notification for job cancel
                $job_id = $this->_request->getParam('job');
                /* $results = $this->jobs->get_device_token_parent($job_id); //commented for removing notification to client 
                // print_r($results);die;
                if (!empty($results)) {

                    //by namrata for notifications

                    $noti_data = array();
                    $noti_data['job_id'] = $job_id;

                    $noti_data['userid'] = $results[0]['client_user_id'];
                    $noti_data['date_added'] = date('Y-m-d H:i:s');
                    $noti_data['date_updated'] = date('Y-m-d H:i:s');

                    $notification_id = $this->notification->insert_notification_data($noti_data);

                    //get unread notification count
                    $count = $this->notification->get_unread_count($results[0]['client_user_id']);
                    $this->ipush = new Hb_Ipushnotification();
                    $message = "Your job request has been cancelled";
                    foreach ($results as $noti) {
                        $this->ipush->send_notification_parent($noti['deviceToken'], $message, $job_id, $results[0]['client_user_id'], $count, $notification_id);
                    }
                } */
                $_SESSION['jobMessage'] = "You have successfully cancelled this job";
            }
        } else {
            $_SESSION['jobErrorMessage'] = "You cannot cancel this job. Please contact administrator";
        }
        $this->_helper->redirector->gotoUrl(SITE_URL . 'sitters/preview/job/' . $this->view->job_id);

        die;
    }


    protected function eventsForm()
    {


        $form = new Zend_Form();

        $form->setName("eventsform");
        $form->setMethod('post');
        $form->setAttrib('enctype', 'multipart/form-data');

        $this->view->special_needs_list = $this->jobs->getSpecialNeeds();

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
            'multiOptions' => array('M' => 'Male', 'F' => 'Female'),
        ));

        $field = $form->getElement('special_needs');
        $field->setRegisterInArrayValidator(false);
        /*
        $field=$form->getElement('special_need');
        $field->setRegisterInArrayValidator(false);   */
        return $form;
    }
    protected  function childForm() {


        $form = new Zend_Form();

        $form->setName("userform");
        $form->setMethod('post');
        $form->setAttrib('enctype', 'multipart/form-data');

        $this->view->special_needs_list = $this->jobs->getSpecialNeeds();

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

}
