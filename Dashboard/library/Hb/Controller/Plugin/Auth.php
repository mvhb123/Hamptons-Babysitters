<?php 
class Hb_Controller_Plugin_Auth extends Zend_Controller_Plugin_Abstract
{
	
protected $_module;
protected $_controller;
protected $_action;
protected $_excludeAdminController=array('logout','error');
public $_session;
public $request_handler;
public function preDispatch(Zend_Controller_Request_Abstract $request)
{
	$this->request_handler = $request;
	$this->_controller=$this->getRequest()->getControllerName();
	$this->_action=$this->getRequest()->getActionName();
	$this->_module=$this->getRequest()->getModuleName();
	
}

protected function _actionExists(Zend_Controller_Request_Abstract $request)
    {
        $dispatcher = Zend_Controller_Front::getInstance()->getDispatcher();

        // Check controller
        if (!$dispatcher->isDispatchable($request)) {
            return false;
        }
return true;
       // $class  = $dispatcher->loadClass($dispatcher->getControllerClass($request));
        //$method = $dispatcher->formatActionName($request->getActionName());

        //return is_callable(array($class, $method));
    }

}
