<?php

class ErrorController extends Zend_Controller_Action
{

    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        $this->_helper->layout->disableLayout();
        if (!$errors) {
            $this->view->message = 'You have reached the error page';
            return;
        }

		//print_r($errors->exception->getMessage());
		//print_r($errors);
		


        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Page not found';
                break;
            default:
                // application error
                
                 // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Page not found';
                break;
               /* $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Application error';
                break;*/
        }
      //$err=  var_export($this->_request->getParams(), true);
        // Log exception, if logger available
        if ($log = $this->getLog()) {
            $log->crit($this->view->message, $errors->exception);
        }
        
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
           $this->view->exception= $errors->exception = $errors->exception;
            //$this->view->exception = true;
        }
       // print_r($_SERVER);die();
        if($_SERVER['REDIRECT_APPLICATION_ENV']!='development'){
       $err =  $this->view->request   = $errors->request->getParams();
       $err = var_export($err,true);
       $message = $errors->exception->getMessage() .'
  </p>

  <h3>Stack trace:</h3>
  <pre>'.$errors->exception->getTraceAsString().'
  </pre>

  <h3>Zend details :Request Parameters:</h3>
  <pre>'.$err.'
  </pre><br>
  
  <h3>Reuquest</h3>'.var_export($_REQUEST,true).'Post<Br>'.var_export($_POST,true).'
  <strong>Server Details</strong><pre>
  '.var_export($_SERVER,true).'</pre>
  
  ';
        
        
        
        $mail = new Zend_Mail();
        $mail->setFrom('errors@hamtptonsbabysitters.com')
     //->addTo('mail@shameemz.com');

        ->addTo('namrata.sharma@sofmen.com');
 //$mail->addCc('pg@prashantgeorge.com');
$writer = new Zend_Log_Writer_Mail($mail);
 
// Set subject text for use; summary of number of errors is appended to the
// subject line before sending the message.
$writer->setSubjectPrependText('Hamptons babysitters Errors');
 
// Only email warning level entries and higher.
$writer->addFilter(Zend_Log::WARN);
 
$zendlog = new Zend_Log();
$zendlog->addWriter($writer);
 
// Something bad happened!
$zendlog->log($message,Zend_Log::WARN);
        
       } 
        
    }

    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }


}

