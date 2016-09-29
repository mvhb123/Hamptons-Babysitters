<?php

class LogoutController extends Zend_Controller_Action
{

    public function init()
    {
      Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::namespaceUnset('adminsession');
		//	header('location:/');
        //$this->_helper->redirector('','login'); // back to login page
    }

    public function indexAction()
    {
    	//print_r(ADMIN_URL);die;
    	$this->_helper->redirector->gotoUrl(SITE_URL); 
        // action body
    }


}

