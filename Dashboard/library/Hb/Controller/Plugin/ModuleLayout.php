<?php
class Hb_Controller_Plugin_ModuleLayout extends Zend_Layout_Controller_Plugin_Layout{
	
	
	
	
protected $_excludeAdminController=array('logout','error');
	
public function preDispatch(Zend_Controller_Request_Abstract $request)
{
	$this->request_handler = $request;
	$this->_controller=$this->getRequest()->getControllerName();
	$this->_action=$this->getRequest()->getActionName();
	$this->_module=$this->getRequest()->getModuleName();
	if($this->_controller!='error')
	 $this->check();
	
	$layout = $this->getLayout();//->setLayoutPath();
	//print_r($layout);
	if($this->_module=='admin')
		$this->getLayout()->setLayoutPath(APPLICATION_PATH. "/layouts/scripts/admin/");
	else
		$this->getLayout()->setLayoutPath(APPLICATION_PATH. "/layouts/scripts/default/");
	
}
public function setLayoutActionHelper(){
	//var_dump($this->_helper);
	
}

protected function check(){
	
	switch($this->_module){
		case 'admin':
		if (Zend_Auth::getInstance()->hasIdentity()&&Zend_Auth::getInstance()->getIdentity()->usertype=='A') {
	  
     echo 'Welcome Administrator';
	   
	}else if(!in_array($this->_controller,$this->_excludeAdminController))
	{
		if(in_array($this->_action,array('logout')))
			Zend_Auth::getInstance()->clearIdentity();
		$this->getRequest()->setControllerName('login');
		$this->getRequest()->setActionName('index');
	}
		break;
		
	}
	
}
}
