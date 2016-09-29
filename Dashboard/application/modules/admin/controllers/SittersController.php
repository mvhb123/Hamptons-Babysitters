<?php

class Admin_SittersController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        $this->sitter = new Hb_Sitter();
        $this->client = new Hb_Client();
        $this->searchParams = $this->_request->getParams();
        $this->view->searchParams = $this->searchParams;

        $this->view->actionName = $this->_request->getActionName();
    }

    public function indexAction() {
        // action body
        // action body
    	$this->hbSettings = new Hb_Settings();
        if ($this->searchParams['approve'] > 0) {
            $this->sitter->update(array('status' => 'active'), $this->searchParams['approve']);
            $this->_helper->redirector->gotoUrl($_SERVER['HTTP_REFERER']);
        } else if ($this->searchParams['deactivate'] > 0) {
            $this->sitter->update(array('status' => 'inactive'), $this->searchParams['deactivate']);
            $this->_helper->redirector->gotoUrl($_SERVER['HTTP_REFERER']);
        }
        $allprefer = $this->hbSettings->getPreferences();
        $this->view->area_prefer=$allprefer[4]['prefer'];
        $this->view->lang_prefer=$allprefer[5]['prefer'];
        $this->view->skills=$this->hbSettings->getskills();
        $this->view->sitters = $this->sitter->searchnew($this->searchParams, array(), array('rows' => $this->searchParams['rows'], 'page' => $this->searchParams['page']));
        $this->view->paginator = Zend_Paginator::factory(range(1, ((int) $this->view->sitters['total'])));

        $this->view->paginator->setCurrentPageNumber((int) $this->searchParams['page'] ? $this->searchParams['page'] : 1);
        $this->view->paginator->setItemCountPerPage((int) $this->searchParams['rows'] ? $this->searchParams['rows'] : 10 );
        $this->view->paginator->setPageRange(10);
        $this->view->paginator->searchParams = $this->view->searchParams;
        $this->hbSettings = new Hb_Settings();

        $this->view->preferences = $this->hbSettings->getPreferences();
    }

    function checkbirthdate($month, $day, $year) {
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
                (($month == $this_month) && ($day <= $this_day)))) {
            return true;
        } elseif (($year == $min_year) &&
                (($month > $this_month) ||
                (($month == $this_month && ($day > $this_day))))) {
            return true;
        } else {
            return false;
        }
    }

    public function profileAction() {
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
        $this->view->statusArray = array('active' => 'Active', 'inactive' => 'Inactive', 'unapproved' => 'New Applicant', 'deleted' => 'Deleted');
        if ($this->_request->getParam('modify') != '') {

            $this->view->userInfo = $sitterDetail = $this->sitter->getDetail($this->_request->getParam('modify'), 'profile');
            //echo '<pre>';print_r($sitterDetail);echo '</pre>';
            
            //print_r($sitterDetail);die;
            
            if ($sitterDetail) {
                $modify = true;
                $this->view->userid = $this->_request->getParam('modify');
                //print_r($sitterDetail);
                $this->view->sitterPreference = $this->sitter->getPreferences($this->view->userid);
                $this->userform->setAction(ADMIN_URL . 'sitters/profile/modify/' . $this->_request->getParam('modify'));
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

               // $this->userform->have_car->setValue($sitterDetail['have_car']);
                $this->userform->cpr_holder->setValue($sitterDetail['cpr_holder']);
                $this->userform->cpr_adult->setValue($sitterDetail['cpr_adult']);
                //$this->userform->have_child->setValue($sitterDetail['have_child']);
               // $this->userform->criminal_record->setValue($sitterDetail['criminal_record']);
                $this->userform->first_aid_cert->setValue($sitterDetail['first_aid_cert']);
               // $this->userform->clean_drive_record->setValue($sitterDetail['clean_drive_record']);
                $this->userform->otherpreference->setValue($sitterDetail['otherpreference']);
                $this->userform->miscinfo->setValue($sitterDetail['miscinfo']);
                
                //by anjali 
                $this->userform->water_certification->setValue($sitterDetail['water_certification']);
                $this->userform->hampton_babysitter_training->setValue($sitterDetail['hampton_babysitter_training']);
                $this->userform->is_student->setValue($sitterDetail['is_student']);
                $this->userform->highest_edu->setValue($sitterDetail['highest_edu']);
                $this->userform->lifeguard->setValue($sitterDetail['lifeguard']);
                if($sitterDetail['special_need_exp']==''||$sitterDetail['special_need_exp']==null)
                	$this->userform->sneed_exp->setValue(0);
                else
                	$this->userform->sneed_exp->setValue(1);
                $this->userform->special_need_exp->setValue($sitterDetail['special_need_exp']); //by anjali
 		
                //by namrata for sitters details
                $this->userform->sitternotes->setValue($sitterDetail['sitternotes']);


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
                $user = new Hb_User();
                $userAvailable = $user->checkUsername($values['username'], $this->_request->getParam('modify'));
                if (empty($userAvailable)) {


                    $datechecking = explode('/', $values['dob']);
                    if ($this->checkbirthdate($datechecking[0], $datechecking[1], $datechecking[2])) {


                        $_FILES['profile_image']['tmp_name'] = $this->userform->profile_image->getFileName();
                        $values['profile_image'] = $_FILES['profile_image'];
                        $values['status'] = $status;
                        $values['prefer'] = $_POST['prefer'];
                        $values['reference'] = $_POST['reference'];
                        $values['education'] = $_POST['education'];
                        //by anjali
                        $values['is_student']=$_POST['is_student'];
                        $values['highest_edu']=$_POST['highest_edu'];
                        $values['lifeguard']=$_POST['lifeguard'];
                        $values['water_certification']=$_POST['water_certification'];
                        $values['hampton_babysitter_training']=$_POST['hampton_babysitter_training'];
                        if($_POST['sneed_exp']==0)
                        	$_POST['special_need_exp']='';
                        $values['special_need_exp']=$_POST['special_need_exp']; //by anjali
                        if (!$modify) {
                            if ($this->sitter->create($values, $status)) {
                                // We're authenticated! Redirect to the home page
                                $flag = 'address';

                                //$this->_helper->redirector->gotoUrl(ADMIN_URL.'sitters/');
                            }
                        } else if ($this->sitter->modify($values, $this->_request->getParam('modify'))) {
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
                                $profile->successMessage = 'Basic information has been sucessfully modified';
                            else {
                                //Notification mail of registration
                                $this->sitter->sitterMail($values['userid'], 'sitter_added_by_admin');
                                $profile->successMessage = 'User information has been sucessfully created.';
                            }
                        }
                        $this->_helper->redirector->gotoUrl(ADMIN_URL . 'sitters/profile/modify/' . $this->sitter->sitter_id);
                    } else
                        $this->userform->dob->addError("You must be 18 years to register with HamptonsBabysitters.com");
                        //$this->userform->dob->addError($this->view->globalMessages['SitterAgeNotMatched']);
                }else {
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

    public function deleteAction() {

        $this->sitter->delete($this->searchParams['id']);
        $this->_helper->redirector->gotoUrl($_SERVER['HTTP_REFERER']);
    }

    public function userForm() {
        $form = new Zend_Form();

        $form->setName("userform");
        $form->setMethod('post');
        $form->setAttrib('enctype', 'multipart/form-data');
        $form->setAction(ADMIN_URL . 'sitters/profile/create/new/');
        
      	//$this->view->special_needs_exp_list =$this->client->getSpecialNeeds();
      
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
                array('StringLength', false, array(0, 10)),
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
        )); */
        $form->addElement('textarea', 'otherpreference', array(
            'required' => false,
            'label' => 'Other Preference',
        ));

        /* $form->addElement('radio', 'have_child', array(
            'MultiOptions' => array('yes', 'no'),
            'required' => false,
            'label' => 'Have Car?',
        )); */

        $form->addElement('text', 'miscinfo', array(
            'required' => false,
            'label' => 'Misc Info:',
        ));
        
         $form->addElement('textarea', 'sitternotes', array(
            'required' => false,
         
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
         		'MultiOptions' => array('yes','no'),
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

    public function addressAction() {
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

        $this->adForm->setAction(ADMIN_URL . 'sitters/address/userid/' . $userid);

        if ($this->_request->getParam('modify') != '') {
            $this->view->address = $this->sitter->getAddress(array('userid' => $this->view->userid, 'address_id' => $this->_request->getParam('modify')));
            $this->view->address = $this->view->address[0];
            if (isset($this->view->address)) {
                $address_id = $this->_request->getParam('modify');
                $this->adForm->setAction(ADMIN_URL . 'sitters/address/userid/' . $userid . '/modify/' . $address_id);
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

    public function addressForm($options = array('country' => 223)) {
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

    public function jobsAction() {

        ///$this->view->eventsForm = $this->eventsForm();

        // action body
        $this->jobs = new Hb_Jobs();
        
		/*commented by anjali
		$request = $this->getRequest();
        if ($request->isPost()) {
            $formValues = $request->getPost();
            if (is_array($formValues['jobaction'])) {

                if (current($formValues['jobaction']) == 'Accept') {
                    $job_id = array_shift(array_flip($formValues['jobaction']));
                    if ($this->jobs->confirmJob($job_id, $formValues['userid'])) {
                        $this->_helper->redirector->gotoUrl(ADMIN_URL . 'sitters/jobs/user/' . $formValues['userid'] . '/view/confirmed/');
                    }
                } else if (current($formValues['jobaction']) == 'Complete') {
                    $job_id = array_shift(array_flip($formValues['jobaction']));
                    $data = array('sitter_id' => $formValues['userid'],
                        'job_id' => $job_id,
                        'total_paid' => $formValues['total_paid'][$job_id],
                        'completed_date' => $formValues['completed_date'][$job_id],
                    );
                    if ($this->jobs->completeJob($data)) {
                        //$this->_helper->redirector->gotoUrl(ADMIN_URL.'sitters/jobs/user/'.$formValues['userid'].'/view/completed/');
                    }
                }
            }
        }*/

        $this->view->userid = $this->_request->getParam('user');
        //var_dump($this->_request->getParam('view'));
        $search = array_merge($this->searchParams, array('sitter_id' => $this->view->userid));
        $search['status'] = $this->_request->getParam('view');

        /* commented by anjali
		if ($this->_request->getParam('view') == 'pending') {
        
        	$this->view->jobs = $this->jobs->getSitterJobs(array('status' => array('pending'), 'sitter_id' => $this->view->userid));
        	 
        } else if ($this->_request->getParam('view') == 'confirmed') {
        
        	$this->view->jobs = $this->jobs->search($search);
        } else if ($this->_request->getParam('view') == 'completed') {
        	$this->view->jobs = $this->jobs->search($search);
        } else if ($this->_request->getParam('view') == 'cancelled') {
        	$this->view->jobs = $this->jobs->search($search);
        }*/
        
        if ($this->_request->getParam('view') == 'pending') {

            $this->view->jobs = $this->jobs->getSitterJobs(array('status' => array('pending'), 'sitter_id' => $this->view->userid ,'page'=> $this->searchParams['page'],'rows'=>$this->searchParams['rows']));
       
        } else{
            $this->view->jobs = $this->jobs->searchnew($search);
        }
//removed -1
        $this->view->paginator = Zend_Paginator::factory(range(1, ((int) $this->view->jobs['total'])));

        $this->view->paginator->setCurrentPageNumber((int) $this->searchParams['page'] ? $this->searchParams['page'] : 1);
        $this->view->paginator->setItemCountPerPage((int) $this->searchParams['rows'] ? $this->searchParams['rows'] : 10 );
        $this->view->paginator->setPageRange(10);
        $this->view->jobs = $this->view->jobs['rows'];
        $this->view->userInfo = $this->sitter->getDetail($this->view->userid, 'profile');
    }

    public function passwordAction() {
        $this->view->userid = $this->_request->getParam('user');

        if (isset($_POST['passwordreset'])) {
            $this->sitter->generatePassword($this->view->userid);
        }
        $this->view->userInfo = $this->sitter->getDetail($this->view->userid, 'profile');
    }

    public function sitterearningsAction() {


        $this->view->userid = $this->_request->getParam('user');
        $this->view->sitter_earning = $this->sitter->get_sitter_earnings($this->searchParams);
        $this->view->paginator = Zend_Paginator::factory(range(1, ((int) $this->view->sitter_earning['total'] )));

        $this->view->paginator->setCurrentPageNumber((int) $this->searchParams['page'] ? $this->searchParams['page'] : 1);
        $this->view->paginator->setItemCountPerPage((int) $this->searchParams['rows'] ? $this->searchParams['rows'] : 10 );
        $this->view->paginator->setPageRange(10);
        $this->view->earnings = $this->view->sitter_earning['rows'];

        $val = $this->view->sitter_earning['records'];
        $sum1 = 0;
        foreach ($val as $keyp => $subArray) {
            $sum1+=(float) $subArray['total_received'];
        }
        $sum2 = 0;
        foreach ($val as $keyp => $subArray) {
            $sum2+=(float) $subArray['total_paid'];
        }
        $sum3 = 0;
        foreach ($val as $keyp => $subArray) {
            $sum3+=(float) $subArray['company'];
        }
        
        $this->view->userInfo = $this->sitter->getDetail($this->view->userid, 'profile');
        $this->view->total_jobs=$this->view->sitter_earning['total'];
        $this->view->total_received = $sum1;
        $this->view->total_paid = $sum2;
        $this->view->company = $sum3;
    }

    /* -------------------------------function to get list of unpaid siytters list---------------------------------- */

    public function paysitterAction() {

        $update = new Zend_Session_Namespace('update');
          $id = new Zend_Session_Namespace('id');
        
        if ((isset($update->updatemessage)) && (!empty($update->updatemessage))) {
            $this->view->successMessage = $update->updatemessage;
        }
        if ((isset($id->sitterid)) && (!empty($id->sitterid))) {
                $sitter_id=$id->sitterid;
        }
        if(isset($_POST['sitter_id']))
        	$sitter_id=  $_POST['sitter_id'];
        $this->view->unpaid_sitter_list = $this->sitter->get_unpaid_sitter_list($this->searchParams);
        
         if((isset($sitter_id))&&(!empty($sitter_id)))
        {
             $array=$this->view->unpaid_sitter_list;
             $key='sitter_user_id';
             $val=$sitter_id;
           // $re=$this->findkeyvalue($array, $key, $val);
          $re=  $this->sitter->findkeyvalue($array, $key, $val);
            
         if($re==true)
         {
              $this->view->sitterid = $sitter_id;
             $this->searchParams['sitter_user_id']=$sitter_id;
         }
          else 
              {
                 $this->searchParams['sitter_user_id'] = '0';
              }
            
            
        }

//to get first sitter 
        if ((!isset($this->searchParams['sitter_user_id'])) || (empty($this->searchParams['sitter_user_id']))) {
          //  $sitter_user_id = $this->view->unpaid_sitter_list[0]['sitter_user_id'];
            //$this->searchParams['sitter_user_id'] = $sitter_user_id;
             $this->searchParams['sitter_user_id'] = '0';
        }
        
        $this->view->unpaid_sitter_details = $this->sitter->get_unpaid_sitter_details($this->searchParams);
        $this->view->paginator = Zend_Paginator::factory(range(1, ((int) $this->view->unpaid_sitter_details['total'])));
        $this->view->paginator->setCurrentPageNumber((int) $this->searchParams['page'] ? $this->searchParams['page'] : 1);
        $this->view->paginator->setItemCountPerPage((int) $this->searchParams['rows'] ? $this->searchParams['rows'] : 10 );
        $this->view->paginator->setPageRange(10);

        $this->view->unpaid_sitters_details = $this->view->unpaid_sitter_details['rows'];

        if ((isset($update->updatemessage)) && (!empty($update->updatemessage))) {

            unset($update->updatemessage);
        }
        
        
         if ((isset($id->sitterid)) && (!empty($id->sitterid))) {
           unset($id->sitterid);
        }
    }
    
    


    /* -----------------------function to update sitter payment status------------------------------------- */

    public function updatesitterpaymentstatusAction() {
        $job_ids = $_POST['job_id'];
        $sitter_id = $_POST['sitter_id'];
        $type = $_POST['type'];
        $number = $_POST['number'];
        $amount = $_POST['amount'];

        //to update earned amount in sitter user table
        $this->sitter->update_sitter_total_earned($sitter_id, $amount);
        //to update the status in jobs table
        $this->sitter->update_sitter_payment_status($sitter_id, $job_ids);
        //to insert the details to new sitter payment table
        $insert_data = array();

        $insert_data['sitter_id'] = $sitter_id;
        $insert_data['job_ids'] = $job_ids;

        if ($type == 'check') {
            $insert_data['check_number'] = $number;
        } else {
            $insert_data['wire_number'] = $number;
        }
        $insert_data['date_added'] = date('Y-m-d H:i:s');
        $insert_data['total_paid'] = $amount;
        $id = $this->sitter->insert_sitter_payment_details($insert_data);
        if (!empty($id)) {

            Zend_Session::start();
            $update = new Zend_Session_Namespace('update');
            $update->updatemessage = "Sitter Payment status has been updated Successfully";
           
             $id = new Zend_Session_Namespace('id');
            $id->sitterid = $sitter_id;
                                  
            $data = array();
            $data['status'] = "success";
            $data['message'] = "Sitter payment details saved successfully";
            echo json_encode($data);
            die;
        }
    }

    /* -----------------------function to get sitter payment history------------------------------------- */
    public function sitterpaymenthistoryAction() {

        $this->reports = new Hb_Reports();
        $this->view->sitter_payment_history = $this->sitter->sitter_payment_history($this->searchParams);

        $sum1 = 0;
        foreach ($this->view->sitter_payment_history['record'] as $keyp => $subArray) {
            $sum1+=(int) $subArray['total_paid'];
        }
        $this->view->paginator = Zend_Paginator::factory(range(1, ((int) $this->view->sitter_payment_history['total'] )));
        $this->view->paginator->setCurrentPageNumber((int) $this->searchParams['page'] ? $this->searchParams['page'] : 1);
        $this->view->paginator->setItemCountPerPage((int) $this->searchParams['rows'] ? $this->searchParams['rows'] : 10 );
        $this->view->paginator->setPageRange(10);
        $this->view->sitter_payment_history_details = $this->view->sitter_payment_history['rows'];
        $this->view->sitters = $this->reports->sitters();
        $this->view->total_amount = $sum1;
    }

    /* ------------------------function to get payment details--------------------------------- */

    public function paymentdetailsAction() {
        $payment_id = $_POST['payment_id'];
        $result = $this->sitter->get_payment_details($payment_id);
        $data = array();
        $data['name'] = $result[0]['firstname'] . " " . $result[0]['lastname'];
        $data['job_ids'] = $result[0]['job_ids'];
        
        $data['date_added'] = date('m/d/Y h:i:a', strtotime($result[0]['date_added']));
        $c_n = $result[0]['check_number'];
        $w_n = $result[0]['wire_number'];
        if ($c_n == NULL) {
            $c_n = "";
        }
        if ($w_n == NULL) {
            $w_n = "";
        }
        
        $data['total_paid'] = money_format('%.2n',$result[0]['total_paid']);
        $data['check_number'] = $c_n;
        $data['wire_number'] = $w_n;
        $data['status'] = "success";
        echo json_encode($data);
        die;
    }
    
    public function sendmessageAction()
    {
        if(!empty($_POST))
        {
            $message=$_POST['message'];
            $sitters=$_POST['my_multi_select3'];
            $this->sitter->send_message($sitters,$message);
        }
        $this->view->all_sitters=$this->sitter->get_all_sitters();
    }
    
    public function jobscalenderAction() {
    	$user_id = $_POST['userid'];
    	$result = $this->sitter->sitterbooking($user_id);
    	$this->view->userInfo = $this->sitter->getDetail($user_id, 'profile');
    	$data = array();
    	
    	$data['sitter_id'] =$user_id;
    	$data['name'] = $this->view->userInfo['firstname'] . " " . $this->view->userInfo['lastname'];
    	$data['events'] = $result;
    	$data['status'] = "success";
    	echo json_encode($data);
    	die;
    }
}
