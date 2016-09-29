<?php

class Admin_PackagesController extends Zend_Controller_Action
{

    public function init()
    {
    	$this->flashMessenger = $this->_helper->FlashMessenger;
    	
    	
        /* Initialize action controller here */
        $this->packages =  new Hb_Packages();
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
    	$this->view->packages = $this->packages->get_all_package($this->searchParams);
    	$this->view->paginator = Zend_Paginator::factory(range(1,((int)$this->view->packages['total']-1)));
        $this->view->paginator->setCurrentPageNumber((int)$this->searchParams['page'] ? $this->searchParams['page'] :1);
        $this->view->paginator->setItemCountPerPage((int)$this->searchParams['rows'] ? $this->searchParams['rows'] : 10 );
        $this->view->paginator->setPageRange(10);
        
        //print_r($this->view->packages['rows']);die;
        
        $this->view->packages=$this->view->packages['rows'];
        
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
    
    public function editpackageAction()
    {
         $modify = new Zend_Session_Namespace('add');
         
         if($this->_request->getParam('modify')!='') 
          {
			$clientDetail = $this->packages->get_package_details($this->_request->getParam('modify'));
			$this->view->det=$clientDetail['rows'];
			unset($modify->successMessage);
    	  }
	}

	public function deleteAction()
	{
		$delete = new Zend_Session_Namespace('add');
		$this->packages->delete($this->searchParams['id']);
		$delete->addmessage = "Package has been deleted Successfully";
		$this->_helper->redirector->gotoUrl($_SERVER['HTTP_REFERER']);
	}
	
	/*-----------------------------function to update package details-------------------------*/
	public function updateAction()
	{
		$update_data['package_name']=$this->getRequest()->getPost('package_name', null);
		$update_data['credit']=$this->getRequest()->getPost('credit', null);
		$update_data['price']=$this->getRequest()->getPost('price', null);
		$package_id=$this->getRequest()->getPost('package_id', null);
		
                
		if((!empty($package_id))&&($package_id!=null))
		{
			$this->packages->update_package_details($update_data,$package_id);
		}
		Zend_Session::start();
		$update = new Zend_Session_Namespace('update');
		$update->updatemessage = "Package details has been updated Successfully";
		//$this->_helper->redirector->gotoUrl(ADMIN_URL.'packages/editpackage/modify/'.$package_id);
		$this->_helper->redirector->gotoUrl(ADMIN_URL.'packages/index');
	}

	/*----------------------to show add package view--------------------------------*/
	public function addpackageAction()
	{
		$this->view->data="";
	}
	
	/*-----------------------function to add package--------------------------------------*/
	public function addAction()
	{
		
		$insert_data['package_name']=$this->getRequest()->getPost('package_name', null);
		$insert_data['credit']=$this->getRequest()->getPost('credit', null);
		$insert_data['price']=$this->getRequest()->getPost('price', null);
		$insert_data['package_order']=$this->packages->get_max_package();
		$packa_id=$this->packages->add_package_details($insert_data);
		
		Zend_Session::start();
		$add = new Zend_Session_Namespace('add');
		$add->addmessage = "Package details has been saved Successfully";
		//$this->_helper->redirector->gotoUrl(ADMIN_URL.'packages/addpackage');
		$this->_helper->redirector->gotoUrl(ADMIN_URL.'packages/index');
	}
} //end of  class