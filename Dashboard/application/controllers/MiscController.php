<?php

class MiscController extends Zend_Controller_Action {

    public function init() {
        
    }

    public function blogAction() {
        
    }

    public function stateoptionsAction() {

        $hbSettings = new Hb_Settings();
        $states = $hbSettings->getStates($this->_request->getParam('country_id'));

        foreach ($states as $state) {
            $t.='<option  value="' . $state['code'] . '">' . $state['name'] . '</option>';
        }

        echo $t;
        die();
    }

    public function stateoptionsidAction() {

        $hbSettings = new Hb_Settings();
        $states = $hbSettings->getStates($this->_request->getParam('country_id'));

        foreach ($states as $state) {
            $t.='<option  value="' . $state['zone_id'] . '">' . $state['name'] . '</option>';
        }

        echo $t;
        die();
    }

    public function contactAction() {
        $hb = new Hb_Packages();
        $hb->contactMail($_POST);
        $_SESSION['msg'] = "Your request recieved successfully.";


        if ($this->_request->getParam('page') == 'events') {

            header('location:' . SITE_URL . 'client/events/');
        } else {
            header('location:' . SITE_URL . 'client/buycredits/?msg=' . $_SESSION['msg']);
        }
        die();
    }
    
    
    
    
    //
    public function assignallsittersAction()
    {
        
                 $this->jobs = new Hb_Jobs();
                $job_id=$_POST['job_id'];
                $user_id=$_POST['userid'];
                $jobno=$_POST['jobno'];
                
                $this->jobs->assign_sitters($job_id,$user_id,$jobno);

    }
    
    public function removeallsittersAction()
    {
    	$this->jobs = new Hb_Jobs();
    	$job_id=$_POST['job_id'];
    	
    	$this->jobs->removeallsitters($job_id);
    
    }
}
