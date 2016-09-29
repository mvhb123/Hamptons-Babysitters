<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->redirector->gotoUrl(SITE_URL.'client');
        die();
    }

    public function indexAction()
    {
        // action body
        
      
    }


}

