<?php

class RegisterController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */

        if (Zend_Auth::getInstance()->hasIdentity() && in_array(Zend_Auth::getInstance()->getIdentity()->usertype, array('P', 'S'))) {
            if (Zend_Auth::getInstance()->getIdentity()->usertype == 'P')
                $this->_helper->redirector('', 'client');
            else if (Zend_Auth::getInstance()->getIdentity()->usertype == 'S')
                $this->_helper->redirector('', 'sitters');
        }
    }

    public function indexAction() {
        
      //  print_r($_POST);die;

        $this->_helper->layout->disableLayout();
        $this->view->userform = $this->userForm();
        $this->user = new Hb_User();
        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($this->view->userform->isValid($request->getPost())) {
                $values = $this->view->userform->getValues();
                
                
                $userAvailable = $this->user->checkUsername($values['username']);
                //var_dump($userAvailable);die;
                if (empty($userAvailable)) {



                    $insert = array(
                        'username' => $values['username'],
                        'password' => $values['password'],
                        'firstname' => $values['firstname'],
                        'lastname' => $values['lastname'],
                        'usertype' => $values['usertype'],
                        'phone' => $values['phone']
                    );
                    $values['email'] = $values['username'];
                    if ($values['usertype'] == 'P')
                        $status = 1;
                    else
                        $status = 0;
                    if ($this->user->addUser($insert, $status)) {
                        // We're authenticated! Redirect to the home page

                        if ($values['usertype'] == 'P') {
                            $this->client = new Hb_Client();
                            $this->client->setClientDetail(array(), $this->user->userid, $action = 'insert');
                            //echo "wer";die;
                            //$this->client->clientMail($this->user->userid,'client_registration');
                        } else {
                            $this->sitter = new Hb_Sitter();
                            $this->sitter->setSitterDetail(array(), $this->user->userid, $action = 'insert');
                            //$this->sitter->sitterMail($this->user->userid,'sitter_registeration');
                        }
                        $this->_process($values);
                        //require_once('LoginController.php');
                        //$login =  new LoginController();
                        $this->_helper->redirector->gotoUrl(SITE_URL . '?msg=Your registration was successful.');
                        $this->view->userform->reset();
                    }
                } else {
                    $this->view->errorMessage = $this->view->userform->username->addError('Email/Username is already taken');
                }
            }
        }
    }

    protected function _process($values) {
        // Get our authentication adapter and check credentials
        $adapter = $this->_getAuthAdapter();
        $adapter->setIdentity($values['email']);
        $adapter->setCredential($values['password']);

        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($adapter);
        if ($result->isValid()) {
            $user = $adapter->getResultRowObject();


            $auth->getStorage()->write($user);
            $session = new Zend_Session_Namespace('usersession');
            $session->user = $user;

            return true;
        } else {
            $this->view->error = 'Email/password was incorrect.';
        }

        return false;
    }

    protected function _getAuthAdapter() {
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);

        $authAdapter->setTableName('users')
                ->setIdentityColumn('username')
                ->setCredentialColumn('password');
        // ->setCredentialTreatment('SHA1(CONCAT(?,salt))');


        return $authAdapter;
    }

    public function userForm() {
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
        $form->addElement('text', 'phone', array(
            'validators' => array(
                array('StringLength', false, array(0, 100)),
                array('NotEmpty', false, array('messages' => array('isEmpty' => 'Please fill Last Name.'))),
            ),
            'required' => false,
            'label' => 'Phone:',
        ));
        $form->addElement('text', 'username', array(
            'validators' => array(
                array('StringLength', false, array(0, 100)),
                array('EmailAddress', false),
            ),
            'required' => true,
            'label' => 'Username:',
        ));
        $form->addElement('password', 'password', array(
            'validators' => array(
                array('StringLength', false, array(0, 100))
            ),
            'required' => true,
            'label' => 'Password:',
        ));

        $form->addElement('password', 'confirm_password', array(
            'validators' => array(
                array('StringLength', false, array(0, 100))
            ),
            'required' => true,
            'label' => 'Password:',
        ));
        $form->addElement('radio', 'usertype', array(
            'MultiOptions' => Array('P', 'S'),
            'required' => true,
            'label' => 'Register as:',
        ));
        $form->addElement('checkbox', 'iagree', array(
            'MultiOptions' => Array('agreed'),
            'required' => true,
            'label' => 'I agreed to the terms and condition',
        ));




        $form->addElement('submit', 'register', array(
            'required' => false,
            'ignore' => true,
            'label' => 'Register',
        ));

        return $form;
    }
    
  

}
