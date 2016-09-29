<?php

class Admin_PricingController extends Zend_Controller_Action
{

    public function init()
    {
    	$this->flashMessenger = $this->_helper->FlashMessenger;
    	
    	
        /* Initialize action controller here */
        $this->pricing =  new Hb_Pricing();
        if($this->_request->getActionName()=='view'){
			
			$this->_request->setActionName('index');
		}
		  $this->searchParams = $this->_request->getParams();
          $this->view->searchParams=$this->searchParams;
          $this->view->actionName=$this->_request->getActionName();
        
    }

    public function indexAction()
    {
    	$add = new Zend_Session_Namespace('add');

		$update = new Zend_Session_Namespace('update');
		
		if((isset($add->addmessage))&&(!empty($add->addmessage)))
		{
			$this->view->successMessage=$add->addmessage;
		}
    	if((isset($update->updatemessage))&&(!empty($update->updatemessage)))
		{
			$this->view->successMessage=$update->updatemessage;
		}
    	$this->view->pricing = $this->pricing->get_all_pricingrate($this->searchParams);
    	$this->view->paginator = Zend_Paginator::factory(range(1,((int)$this->view->pricing['total']-1)));
        $this->view->paginator->setCurrentPageNumber((int)$this->searchParams['page'] ? $this->searchParams['page'] :1);
        $this->view->paginator->setItemCountPerPage((int)$this->searchParams['rows'] ? $this->searchParams['rows'] : 10 );
        $this->view->paginator->setPageRange(10);
        
        //print_r($this->view->packages['rows']);die;
        
        $this->view->pricing=$this->view->pricing['rows'];
        
        if((isset($add->addmessage))&&(!empty($add->addmessage)))
        {
        	 unset($add->addmessage);
        }
        
        if((isset($update->updatemessage))&&(!empty($update->updatemessage)))
        {
        	 unset($update->updatemessage);
        }
    }
    
    /*-----------------------------function to view package details-------------------------*/
    
    public function editpricingAction()
    {
         $modify = new Zend_Session_Namespace('add');
         
         if($this->_request->getParam('modify')!='') 
          {
			$clientDetail = $this->pricing->get_pricing_details($this->_request->getParam('modify'));
			$this->view->det=$clientDetail['rows'];
			unset($modify->successMessage);
    	  }
	}

	public function deleteAction()
	{
		$delete = new Zend_Session_Namespace('add');
		$this->pricing->delete($this->searchParams['id']);
		$delete->addmessage = "Pricing details has been deleted Successfully";
		$this->_helper->redirector->gotoUrl($_SERVER['HTTP_REFERER']);
	}
	
	/*-----------------------------function to update rate details-------------------------*/
	public function updateAction()
	{
		$update_data['child_count']=$this->getRequest()->getPost('child_count', null);
		$update_data['client_rate']=$this->getRequest()->getPost('client_rate', null);
		$update_data['sitter_rate']=$this->getRequest()->getPost('sitter_rate', null);
		$rate_id=$this->getRequest()->getPost('rate_id', null);
		
                
		if((!empty($rate_id))&&($rate_id!=null))
		{
			$this->pricing->update_pricing_details($update_data,$rate_id);
		}
		Zend_Session::start();
		$update = new Zend_Session_Namespace('update');
		$update->updatemessage = "Pricing details has been updated Successfully";
		//$this->_helper->redirector->gotoUrl(ADMIN_URL.'packages/editpackage/modify/'.$package_id);
		$this->_helper->redirector->gotoUrl(ADMIN_URL.'pricing/index');
	}

	/*----------------------to show add rate view--------------------------------*/
	public function addpricingAction()
	{
		$this->view->data="";
	}
	
	/*-----------------------function to add rate--------------------------------------*/
	public function addAction()
	{
		
		$insert_data['child_count']=$this->getRequest()->getPost('child_count', null);
		$insert_data['client_rate']=$this->getRequest()->getPost('client_rate', null);
		$insert_data['sitter_rate']=$this->getRequest()->getPost('sitter_rate', null);
		$rate_id=$this->pricing->add_pricing_details($insert_data);
		
		Zend_Session::start();
		$add = new Zend_Session_Namespace('add');
		$add->addmessage = "Pricing details has been saved Successfully";
		$this->_helper->redirector->gotoUrl(ADMIN_URL.'pricing/index');
	}
} //end of  class