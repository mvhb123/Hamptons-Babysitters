<?php

class Admin_SettingsController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->hbSettings = new Hb_Settings();
         $this->searchParams = $this->_request->getParams();
           $this->view->searchParams= $this->searchParams;
            $this->view->actionName = $this->_request->getActionName();
       
		
    }

    public function indexAction()
    {
        // action body
        $this->view->site = $this->hbSettings->getSite();
       
       
       if($this->_request->isPost()){
		   $data = array_intersect_key($_POST,$this->view->site);
			$this->hbSettings->updateSite($data);
			$this->_helper->redirector->gotoUrl(ADMIN_URL.'settings');
		}
       
        $this->view->templates = scandir(SITE_ABS_PATH.'application/views/');
      
        $this->view->templates = array_diff($this->view->templates ,array('.','..','email','scripts'));
       
    }
    
    public function siteAction(){
		
		
	}
	public function mailAction(){
		
		$this->view->mailTemplates = $this->hbSettings->getMailTemplates();
		
	}
	public function mailmodifyAction(){
		if($this->_request->isPost()){
			$this->hbSettings->updateMailTemplate($_POST,$_POST['mail_id']);
			$this->_helper->redirector->gotoUrl(ADMIN_URL.'settings/mail');
		}
		$this->view->mailTemplate = $this->hbSettings->getMailTemplates(array('mail_id'=>$this->searchParams['id']));
		$this->view->mailTemplate=$this->view->mailTemplate[0];
	}
	
	public function preferenceAction(){
		
		$this->view->preferForm = $this->preferForm();
          // echo $this->_request->getParam('parent');die();
          
          if($this->searchParams['deleteprefer']>0){
			  $this->_helper->layout->disableLayout();
			  if($this->hbSettings->deletePreference($this->searchParams['deleteprefer']))echo 'true';else 'false';
			  die();
		  }
          if($this->searchParams['updateprefer']>0){
			  $this->_helper->layout->disableLayout();
			  if($this->hbSettings->updatePreference($this->searchParams['value'],$this->searchParams['updateprefer']))echo 'true';else 'false';
			  die();
		  }
            
            $this->view->preferForm->setAction(ADMIN_URL.'settings/preference/');
            
                                       $request = $this->getRequest();
                                        if ($request->isPost()) {
                                            if ($this->view->preferForm->isValid($request->getPost())) {
                                               
                                               
                                               $values = $this->view->preferForm->getValues();
        										 $values['group_id']=$_POST['group_id'];   
                                                if ($this->hbSettings->addPreference($values)) {
                                                     
                                                   // $this->_helper->redirector->gotoUrl(ADMIN_URL.'client/children/parent/'.$this->view->userid);
                                                   $this->view->preferForm->reset();
                                                }
                                            }
                                        }
                                      
                              
		$this->view->preferences = $this->hbSettings->getPreferences();
		
		
	}
	public function preferForm(){
		
						$form  = new Zend_Form();
                        		
                        		$form->setName("preferenceform");
                                $form->setMethod('post');
                                   
                                $form->addElement('text', 'prefer_name', array(
                                    'required'   => true,
                                    'label'      => 'Preference Name',
                                ));
                                
                               $form->addElement('submit', 'create', array(
                                    'required' => false,
                                    'ignore'   => true,
                                    'label'    => 'Create',
                                ));       
                        		
                               
                               
                        		
                        		return $form;
		
	}
	
	public function accountAction(){
		
	}


}

