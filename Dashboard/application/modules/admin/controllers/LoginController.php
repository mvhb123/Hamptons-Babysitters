<?php

class Admin_LoginController extends Zend_Controller_Action
{

   public function init()
    {
        /* Initialize action controller here */
       
        if(Zend_Auth::getInstance()->hasIdentity()&&Zend_Auth::getInstance()->getIdentity()->usertype=='A')
			$this->_helper->redirector('','');
    }

    public function indexAction()
    {
        
         $this->_helper->layout->disableLayout();
        $this->loginform = $this->getLoginForm();
                $request = $this->getRequest();
                if ($request->isPost()) {
                    if ($this->loginform->isValid($request->getPost())) {
                        if ($this->_process($this->loginform->getValues())) {
                            // We're authenticated! Redirect to the home page
                           // $this->_helper->redirector('index', 'index');
                            $this->_helper->redirector( 'alerts','jobs');
                            
                        }
                    }
                }
        $this->view->loginform = $this->loginform;
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
                    
                    
                    $auth->getStorage()->write($user);
                    $session =  new Zend_Session_Namespace('adminsession');
                    $session->user = $user; 
                   
                    return true;
                }else {
					$this->view->error='Username/password was incorrect.';
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
	public function lostpasswordAction(){
		
		$this->user = new Hb_User();
		$username=$this->_request->getParam('email');
               
		$user = $this->user->getUser(array('username'=>$username));
		if(!empty($user)){
			$user = $user[0];
			$this->user->generatePassword($user['userid']);
			$this->view->lostSuccess='New Password has sent to your email.';
			echo json_encode(array('msg'=>$this->view->lostSuccess,'err'=>'true'));
		}else{
			$this->view->lostError='User account not found';
			echo json_encode(array('msg'=>$this->view->lostError,'err'=>'false'));
		}
		die();
	}
    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::namespaceUnset('adminsession');

        $this->_helper->redirector('','login'); // back to login page

    }
    public function getLoginForm(){
		
		$form  = new Zend_Form();
		
		$form->setName("login");
        $form->setMethod('post');
        $form->setAction($this->view->url(array('controller'=>'login','module'=>'admin')),null,true);
             
        $form->addElement('text', 'email', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                array('StringLength', false, array(0, 100)),
               // array('EmailAddress', true,'Email is not valid'),
            ),
            'required'   => true,
            'label'      => 'Username:',
            
        ));
		
        $form->addElement('password', 'password', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('StringLength', false, array(0, 50)),
            ),
            'required'   => true,
            'label'      => 'Password:',
        ));

        $form->addElement('submit', 'login', array(
            'required' => false,
            'ignore'   => true,
            'label'    => 'Login',
        ));       
		
		return $form;
	}


}

