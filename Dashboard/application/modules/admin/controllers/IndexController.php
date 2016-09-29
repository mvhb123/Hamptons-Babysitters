<?php

class Admin_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
          
        $this->jobs =  new Hb_Jobs();
        if($this->_request->getActionName()=='view'){
			
			$this->_request->setActionName('index');
		}
		
		 $this->searchParams = $this->_request->getParams();
          $this->view->searchParams=$this->searchParams;
          
          $this->view->actionName=$this->_request->getActionName();
    }

    public function indexAction()
    {
    	// action body
    	/* change default home page
    	 * $this->view->newJobs = $this->jobs->search(array('status'=>'new','rows'=>5));
    	$this->view->pendingJobs = $this->jobs->search(array('status'=>'pending','rows'=>5));
    	$this->view->confirmedJobs = $this->jobs->search(array('status'=>'confirmed','rows'=>5));
    	$this->view->completedJobs = $this->jobs->search(array('status'=>'completed','rows'=>5)); */
    	
    	$this->_helper->redirector->gotoUrl(ADMIN_URL . 'jobs/alerts');
    }
    public function refreshcacheAction(){
		
		$this->settings = new Hb_Settings();
		$this->settings->refreshCache();
		
		header("location:".$_SERVER['HTTP_REFERER']);
		die;
	}


}

