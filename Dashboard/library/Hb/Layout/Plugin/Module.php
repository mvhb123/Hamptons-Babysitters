<?php
class Hb_Layout_Plugin_Module extends Zend_Layout_Controller_Plugin_Layout{
	
	
	
	
protected $_excludeAdminController=array('logout','error','authurizenet','register');
	
public function preDispatch(Zend_Controller_Request_Abstract $request)
{
	
	$this->request_handler = $request;
	$this->_controller=$this->getRequest()->getControllerName();
	$this->_action=$this->getRequest()->getActionName();
	$this->_module=$this->getRequest()->getModuleName();
	if(!in_array($this->_controller,array('error','authorizenet','misc','crone')))
	 $this->check();
	
	$layout = $this->getLayout();//->setLayoutPath();
	//print_r($layout);
	if($this->_module=='admin')
		$this->getLayout()->setLayoutPath(APPLICATION_PATH. "/layouts/default/scripts/");
	else{
		$this->getLayout()->setLayoutPath(APPLICATION_PATH. "/layouts/default/scripts/");
		
		$config=array('basePath'=>APPLICATION_PATH.'/views/default/');
		
        $view = new Zend_View($config);
        // more initialization...
        $viewRenderer =
                Zend_Controller_Action_HelperBroker::getStaticHelper(
                    'ViewRenderer'
                );
                
                $view->addHelperPath( APPLICATION_PATH.'/../library/Hb/View/Helper',
                      'Hb_View_Helper' );
                      $viewRenderer->setView($view);

                      $viewRenderer->view->globalMessages = parse_ini_file(SITE_ABS_PATH.'message.ini');
						//error_log(print_r($viewRenderer->view->globalMessages,true));
						//$viewRenderer->
	}
	
}
public function setLayoutActionHelper(){
	//var_dump($this->_helper);
	
}

protected function check(){
	
	switch($this->_module){
		case 'admin':
		if($this->getRequest()->getActionName()=='lostpassword')
		return true;
		if (Zend_Auth::getInstance()->hasIdentity()&&Zend_Auth::getInstance()->getIdentity()->usertype=='A') {
	   
       
	}else if(!in_array($this->_controller,$this->_excludeAdminController))
	{
		if(in_array($this->_action,array('logout')))
			Zend_Auth::getInstance()->clearIdentity();
			//Zend_Auth::getInstance()->clearIdentity();
		$this->getRequest()->setControllerName('login');
		$this->getRequest()->setActionName('index');
	}
		break;
		
		default:case 'default':
		
		if($this->getRequest()->getActionName()=='lostpassword')
		return true;
	if (Zend_Auth::getInstance()->hasIdentity()&&in_array(Zend_Auth::getInstance()->getIdentity()->usertype,array('P','S'))) {
	  if(Zend_Auth::getInstance()->getIdentity()->usertype=='P'&&in_array($this->_controller,array('admin','sitters'))){
		  header('location:'.SITE_URL.'client');
	  }else if(Zend_Auth::getInstance()->getIdentity()->usertype=='S'&&in_array($this->_controller,array('admin','client'))){
	 header('location:'.SITE_URL.'sitters');
	}
       
	}else if(!in_array($this->_controller,$this->_excludeAdminController))
	{
		if(in_array($this->_action,array('logout')))
			Zend_Auth::getInstance()->clearIdentity();
			//Zend_Auth::getInstance()->clearIdentity();
		$this->getRequest()->setControllerName('login');
		$this->getRequest()->setActionName('index');
	}
		break;
		
		
			
			
		
		
	}
	
}
}
