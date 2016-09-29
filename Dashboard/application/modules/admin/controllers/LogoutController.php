<?php

class Admin_LogoutController extends Zend_Controller_Action
{

    public function init()
    {
      Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::namespaceUnset('adminsession');

        $this->_helper->redirector('','login'); // back to login page
    }

    public function indexAction()
    {
    	$this->_helper->redirector(ADMIN_URL,'login');
    	
        // action body
    }


}

