<?php

class Admin_AddressController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->user = new Hb_User();
    }

    public function indexAction()
    {
        // action body
    }
    
    public function addAction()
    {
        // action body
         $this->adForm = $this->addressForm();
                $request = $this->getRequest();
                if ($request->isPost()) {
                    if ($this->adForm->isValid($request->getPost())) {
						$this->user->addAddress($this->adForm->getValues());
						//print_r($request);
                       // if ($this->client->create($this->addressForm->getValues(),$status)) {
                            // We're authenticated! Redirect to the home page
                           // $this->_helper->redirector('index', 'index');
                        //}
                    }
                }
        $this->view->addressForm = $this->adForm;
    }

    public function addressForm()
    {
        $form  = new Zend_Form();
        		
        		$form->setName("addressform");
                $form->setMethod('post');
                $form->setAttrib('enctype', 'multipart/form-data');
                     
                $form->addElement('text', 'address_1', array(
                    'filters'    => array('StringTrim', 'StringToLower'),
                    'validators' => array(
                        array('StringLength', false, array(0, 150)),
                       
                        
                    ),
                    'required'   => false,
                    'label'      => 'Address 1:',
                ));
                $form->addElement('text', 'address_2', array(
                    'filters'    => array('StringTrim', 'StringToLower'),
                    'validators' => array(
                        array('StringLength', false, array(0, 150)),
                       
                        
                    ),
                    'required'   => false,
                    'label'      => 'Address 2:',
                ));
                $form->addElement('text', 'streat_address', array(
                    'filters'    => array('StringTrim', 'StringToLower'),
                    'validators' => array(
                        array('StringLength', false, array(0, 255)),
                       
                        
                    ),
                    'required'   => true,
                    'label'      => 'Streat Address:',
                ));
                $form->addElement('text', 'zipcode', array(
                    'filters'    => array('StringTrim', 'StringToLower'),
                    'validators' => array(
                        array('StringLength', false, array(0, 10)),
                       
                        
                    ),
                    'required'   => true,
                    'label'      => 'Zip Code:',
                ));
                $form->addElement('text', 'state', array(
                    'filters'    => array('StringTrim', 'StringToLower'),
                    'validators' => array(
                        array('StringLength', false, array(0, 30)),
                        
                        
                    ),
                    'required'   => true,
                    'label'      => 'State:',
                ));
                $form->addElement('select', 'country', array(
                    'filters'    => array('StringTrim', 'StringToLower'),
                    'MultiOptions'=>array('1'=>'USA', '2'=>'Canada'),
                      
                    'required'   => true,
                    'label'      => 'Country:',
                ));
                $form->addElement('select', 'address_type', array(
                    'filters'    => array('StringTrim', 'StringToLower'),
                    'MultiOptions'=>array('billing'=>'Billing', 'local'=>'Local','other'=>'Other'),
                      
                    'required'   => true,
                    'label'      => 'Address Type:',
                ));
        
                
                $form->addElement('submit', 'addressSubmit', array(
                    'required' => false,
                    'ignore'   => true,
                    'label'    => 'Add Address',
                ));       
        		
        		return $form;
    }

    


}



