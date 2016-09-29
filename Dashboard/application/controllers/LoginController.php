<?php

class LoginController extends Zend_Controller_Action
{

    public function init()
    {

        /* Initialize action controller here */

        if (Zend_Auth::getInstance()->hasIdentity() && in_array(Zend_Auth::getInstance()->getIdentity()->usertype, array('P', 'S'))) {
            if (Zend_Auth::getInstance()->getIdentity()->usertype == 'P') {
                //$this->_helper->redirector('','client');
                if ((int)Zend_Auth::getInstance()->getIdentity()->profile_completed <= 0)
                    header('location:' . SITE_URL . 'client/profile/');
                //$this->_helper->redirector('profile','client');
                else header('location:' . SITE_URL . 'client/');//$this->_helper->redirector('','client');

            } else if (Zend_Auth::getInstance()->getIdentity()->usertype == 'S') {

                if ((int)Zend_Auth::getInstance()->getIdentity()->profile_completed <= 0)
                    header('location:' . SITE_URL . 'sitters/profile/');//$this->_helper->redirector('profile','sitters');
                else

                    //    header('location:'.SITE_URL.'sitters/');

                    $this->_helper->redirector('', 'sitters');

            }
        }
        $this->view->searchParams = $this->_request->getParams();
    }

    public function indexAction()
    {


        //echo '<pre>';print_r($this->_request);
        $this->_helper->layout->disableLayout();
        $this->loginform = $this->getLoginForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($this->loginform->isValid($request->getPost())) {
                if ($this->_process($this->loginform->getValues())) {

                    // We're authenticated! Redirect to the home page
                    if (Zend_Auth::getInstance()->getIdentity()->usertype == 'P') {
                        //var_dump((int)Zend_Auth::getInstance()->getIdentity()->profile_completed);die;
                        if ((int)Zend_Auth::getInstance()->getIdentity()->profile_completed <= 0)
                            header('location:' . SITE_URL . 'client/profile/');
                        else if ((int)Zend_Auth::getInstance()->getIdentity()->password_reset == 1)
                            header('location:' . SITE_URL . 'client/password');//$this->_helper->redirector('','sitters');
                        //$this->_helper->redirector('profile','client');
                        else header('location:' . SITE_URL . 'client/');//$this->_helper->redirector('','client');
                        //$this->_helper->redirector('','client');
                    } else if (Zend_Auth::getInstance()->getIdentity()->usertype == 'S') {
                        if ((int)Zend_Auth::getInstance()->getIdentity()->profile_completed <= 0)
                            header('location:' . SITE_URL . 'sitters/profile/');//$this->_helper->redirector('profile','sitters');
                        else if ((int)Zend_Auth::getInstance()->getIdentity()->password_reset == 1)
                            header('location:' . SITE_URL . 'sitters/password');//$this->_helper->redirector('','sitters');
                        //nazmrata 2 lines next
                        else

                            $this->_helper->redirector('sitters');
                        //header('location:'.SITE_URL.'sitters/');

                    }

                }
            }
            //  $this->view->loginform = $this->loginform;
        }
        $this->view->loginform = $this->loginform;
    }

    public function getLoginForm()
    {

        $form = new Zend_Form();

        $form->setName("login");
        $form->setMethod('post');
        $form->setAction($this->view->url(array('controller' => 'login')));

        $form->addElement('text', 'email', array(
            'filters' => array('StringTrim', 'StringToLower'),
            'validators' => array(
                array('StringLength', false, array(0, 100)),
                // array('EmailAddress', true,'Email is not valid'),
            ),
            'required' => true,
            'label' => 'Username:',

        ));

        $form->addElement('password', 'password', array(
            'filters' => array('StringTrim'),
            'validators' => array(
                array('StringLength', false, array(0, 50)),
            ),
            'required' => true,
            'label' => 'Password:',
        ));

        $form->addElement('submit', 'login', array(
            'required' => false,
            'ignore' => true,
            'label' => 'Login',
        ));

        return $form;
    }

    protected function _process($values)
    {
        // Get our authentication adapter and check credentials
        $adapter = $this->_getAuthAdapter();
        $adapter->setIdentity($values['email']);
        $adapter->setCredential($values['password']);

        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($adapter);
        if ($result->isValid()) {

            $user = $adapter->getResultRowObject();
            if ($_REQUEST['env'] == 'dev') {
                print_r($user);
                die;
            }
            if ($user->status == 'active' || $user->status == 'unapproved' || $user->status == '') {

                $auth->getStorage()->write($user);
                $session = new Zend_Session_Namespace('usersession');
                $session->user = $user;

                return true;

            } else if ($user->status == 'inactive') {
                $this->view->error = 'Your account is inactive now. Please contact administrator at <a href="mailto:admin@hamptonsbabysitters.com">admin@hamptonsbabysitters.com</a> ';
            } else if ($user->status == 'deleted') {
                $this->view->error = 'Email/password was incorrect.';
            }
        } else {
            $this->view->error = 'Email/password was incorrect.';
        }

        return false;
    }

    protected function _getAuthAdapter()
    {
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);

        $authAdapter->setTableName('users')
            ->setIdentityColumn('username')
            ->setCredentialColumn('password');
        // ->setCredentialTreatment('SHA1(CONCAT(?,salt))');


        return $authAdapter;
    }

    public function lostpasswordAction()
    {

        $this->user = new Hb_User();
        $username = $this->_request->getParam('email');
        $user = $this->user->getUser(array('username' => $username));
        if (!empty($user)) {
            $user = $user[0];
            $this->user->generatePassword($user['userid']);
            $this->view->lostSuccess = 'New Password has sent to your email.';
            echo json_encode(array('msg' => $this->view->lostSuccess, 'err' => 'true'));
        } else {
            $this->view->lostError = 'User account not found';
            echo json_encode(array('msg' => $this->view->lostError, 'err' => 'false'));
        }
        die();
    }

    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::namespaceUnset('usersession');

        $this->_helper->redirector('', 'login'); // back to login page

    }


}

