<?php

class Admin_JobsController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */

        $this->jobs = new Hb_Jobs();
        if ($this->_request->getActionName() == 'view') {

            $this->_request->setActionName('index');
        }

        $this->searchParams = $this->_request->getParams();
        $this->view->searchParams = $this->searchParams;

        $this->view->actionName = $this->_request->getActionName();
    }

    public function indexAction() {

        //print_r($this->searchParams);die;
    	$this->client = new Hb_Client();
    	
        //$this->view->jobs = $this->jobs->search($this->searchParams);
    	$this->view->jobs = $this->jobs->searchnew($this->searchParams);
    	
        //print_r($this->view->jobs);die;
        $this->view->paginator = Zend_Paginator::factory(range(1, ((int) $this->view->jobs['total'])));

        $this->view->paginator->setCurrentPageNumber((int) $this->searchParams['page'] ? $this->searchParams['page'] : 1);
        $this->view->paginator->setItemCountPerPage((int) $this->searchParams['rows'] ? $this->searchParams['rows'] : 10 );
        $this->view->paginator->setPageRange(10);
        $this->view->jobs = $this->view->jobs['rows'];

        //for client's job
        if($this->searchParams['client_id']>0)
        	$this->view->userInfo = $this->client->getDetail($this->searchParams['client_id'], 'profile');
        	 
        //print_r($this->view->jobs);
    }

    /* ----------------------function for billable jobs------------------------- */

    public function billablejobsAction() {
        $update = new Zend_Session_Namespace('update');


        if ((isset($update->updatemessage)) && (!empty($update->updatemessage))) {
            $this->view->successMessage = $update->updatemessage;
        }
        $this->view->billablejobs = $this->jobs->billable_jobs($this->searchParams);

        $this->view->paginator = Zend_Paginator::factory(range(1, ((int) $this->view->billablejobs['total'])));
        $this->view->paginator->setCurrentPageNumber((int) $this->searchParams['page'] ? $this->searchParams['page'] : 1);
        $this->view->paginator->setItemCountPerPage((int) $this->searchParams['rows'] ? $this->searchParams['rows'] : 10 );
        $this->view->paginator->setPageRange(10);
        $this->view->billablejobs = $this->view->billablejobs['rows'];
        if ((isset($update->updatemessage)) && (!empty($update->updatemessage))) {

            unset($update->updatemessage);
        }
    }

    /* ------------------------function to update client rate------------------------------------------------- */

    public function updateclientrateAction() {

        $job_id = $_POST['job_id'];
        $rate = $_POST['rate'];
        $this->jobs->update_client_rate($job_id, $rate);
        $data = array();
        $data['status'] = "success";
        $data['message'] = "Client Rate has been updated successfully";
        echo json_encode($data);
        die;
    }

    /* ------------------------function to update sitter rate------------------------------------------------- */

    public function updatesitterrateAction() {

        $job_id = $_POST['job_id'];
        $rate = $_POST['rate'];
        $this->jobs->update_sitter_rate($job_id, $rate);
        $data = array();
        $data['status'] = "success";
        $data['message'] = "Sitter Rate has been updated successfully";
        echo json_encode($data);
        die;
    }
    
    /*----------------------function to get all immidiate job------------------------------*/
    
     public function immediatespecialAction() {

        //print_r($this->searchParams);die;
        $this->searchParams['jobtype']='special';
        $this->searchParams['status']='immidiate';

         //print_r($this->jobs->search($this->searchParams));die;

        $this->view->immediate = $this->jobs->search($this->searchParams);

        // print_r($this->view->immediate);die;
        //print_r($this->view->immidiate_job);die;
        $this->view->paginator = Zend_Paginator::factory(range(1, ((int) $this->view->immediate['total'])));

        $this->view->paginator->setCurrentPageNumber((int) $this->searchParams['page'] ? $this->searchParams['page'] : 1);
        $this->view->paginator->setItemCountPerPage((int) $this->searchParams['rows'] ? $this->searchParams['rows'] : 10 );
        $this->view->paginator->setPageRange(10);
        $this->view->immediatespecial = $this->view->immediate['rows'];
        //echo '<pre>';
        //print_r($this->view->jobs);
    }
    
    
    
        /*----------------------function to get all immidiate job------------------------------*/
    
     public function immediateAction() {
 
         $this->searchParams['status']='immidiate';
        //print_r($this->searchParams);die;
        $this->searchParams['jobtype']='simple';
        $this->view->immediate = $this->jobs->search($this->searchParams);
        $this->view->paginator = Zend_Paginator::factory(range(1, ((int) $this->view->immediate['total'])));
        $this->view->paginator->setCurrentPageNumber((int) $this->searchParams['page'] ? $this->searchParams['page'] : 1);
        $this->view->paginator->setItemCountPerPage((int) $this->searchParams['rows'] ? $this->searchParams['rows'] : 10 );
        $this->view->paginator->setPageRange(10);
        $this->view->immediatejob = $this->view->immediate['rows'];
        //echo '<pre>';
        //print_r($this->view->jobs);
    }
    
    /*--------------------------function for job alert----------------------------------*/
    
    public function jobalertAction()
    {
          //$this->searchParams['status']='alert';
        $this->searchParams['status']='alert';
        $this->view->jobalert = $this->jobs->search($this->searchParams);
         //print_r($this->view->jobalert);die;
        $this->view->paginator = Zend_Paginator::factory(range(1, ((int) $this->view->jobalert['total'])));
        $this->view->paginator->setCurrentPageNumber((int) $this->searchParams['page'] ? $this->searchParams['page'] : 1);
        $this->view->paginator->setItemCountPerPage((int) $this->searchParams['rows'] ? $this->searchParams['rows'] : 10 );
        $this->view->paginator->setPageRange(10);
        //print_r($this->view->jobalert['rows']);die;
        
        $this->view->jobalert = $this->view->jobalert['rows'];
          
    }
    
    
       /*--------------------------function for special scheduled/pending jobs----------------------------------*/
    
    public function specialscheduledjobsAction()
    {
        
        //removed-1 from total
        $this->searchParams['jobtype']='special';
        $this->view->specialscheduledjobs = $this->jobs->scheduledjobs($this->searchParams);
        $this->view->paginator = Zend_Paginator::factory(range(1, ((int) $this->view->specialscheduledjobs['total'])));
        $this->view->paginator->setCurrentPageNumber((int) $this->searchParams['page'] ? $this->searchParams['page'] : 1);
        $this->view->paginator->setItemCountPerPage((int) $this->searchParams['rows'] ? $this->searchParams['rows'] : 10 );
        $this->view->paginator->setPageRange(10);
     //  print_r($this->view->paginator);die;
        
        $this->view->jobs = $this->view->specialscheduledjobs['rows'];
    }
            
      /*--------------------------function for scheduled/pending jobs----------------------------------*/
    
    public function scheduledjobsAction()
    {
                //removed-1 from total
        $this->searchParams['jobtype']='simple';
        $this->view->scheduledjobs = $this->jobs->scheduledjobs($this->searchParams);
        $this->view->paginator = Zend_Paginator::factory(range(1, ((int) $this->view->scheduledjobs['total'] )));
        $this->view->paginator->setCurrentPageNumber((int) $this->searchParams['page'] ? $this->searchParams['page'] : 1);
        $this->view->paginator->setItemCountPerPage((int) $this->searchParams['rows'] ? $this->searchParams['rows'] : 10 );
        $this->view->paginator->setPageRange(10);
      //  print_r($this->view->specialscheduledjobs);die;
        
        $this->view->jobs = $this->view->scheduledjobs['rows'];
    }
    
    /*------------------------function to make a job as completed---------------------*/
        public function closejobAction()
        {
          
            $job_id=$this->_request->getParam('job_id');
            $this->jobs->close_job($job_id);
                $this->_helper->redirector->gotoUrl($_SERVER['HTTP_REFERER']);
        }
        
        
        public function canceljobAction()
        {
          
            $job_id=$this->_request->getParam('job_id');
            
            $this->jobs->cancel_job($job_id);
          
            
                $this->_helper->redirector->gotoUrl($_SERVER['HTTP_REFERER']);
           
        }
        
        
          public function immidiatecancelledjobsAction()
    {
          $this->searchParams['type']='immidiate';
        
        $this->view->immidiatecancelledjobs = $this->jobs->cancelled_jobs_search($this->searchParams);
        $this->view->paginator = Zend_Paginator::factory(range(1, ((int) $this->view->immidiatecancelledjobs['total'])));
        $this->view->paginator->setCurrentPageNumber((int) $this->searchParams['page'] ? $this->searchParams['page'] : 1);
        $this->view->paginator->setItemCountPerPage((int) $this->searchParams['rows'] ? $this->searchParams['rows'] : 10 );
        $this->view->paginator->setPageRange(10);
        //print_r($this->view->jobalert['rows']);die;
        
        $this->view->immidiatecancelledjobs = $this->view->immidiatecancelledjobs['rows'];
          
    }
        
     public function cancelledjobsAction()
    {
          $this->searchParams['type']='cancelled';
        
        $this->view->cancelledjobs = $this->jobs->cancelled_jobs_search($this->searchParams);
        
        
       // print_r($this->view->cancelledjobs);die;
        $this->view->paginator = Zend_Paginator::factory(range(1, ((int) $this->view->cancelledjobs['total'])));
        $this->view->paginator->setCurrentPageNumber((int) $this->searchParams['page'] ? $this->searchParams['page'] : 1);
        $this->view->paginator->setItemCountPerPage((int) $this->searchParams['rows'] ? $this->searchParams['rows'] : 10 );
        $this->view->paginator->setPageRange(10);
        //print_r($this->view->jobalert['rows']);die;
        
        $this->view->cancelledjobs = $this->view->cancelledjobs['rows'];
          
    }
    
    public function specialconditionsAction() {
    	    	
    	$this->view->jobs = $this->jobs->searchSpecial($this->searchParams);
    	$this->view->paginator = Zend_Paginator::factory(range(1, ((int) $this->view->jobs['total'])));
    	$this->view->paginator->setCurrentPageNumber((int) $this->searchParams['page'] ? $this->searchParams['page'] : 1);
    	$this->view->paginator->setItemCountPerPage((int) $this->searchParams['rows'] ? $this->searchParams['rows'] : 10 );
    	$this->view->paginator->setPageRange(10);
    	$this->view->jobs = $this->view->jobs['rows'];
    }
    
    public function alertsAction() {
    
    	$this->view->jobs = $this->jobs->getadminalerts($this->searchParams);
    	$this->view->paginator = Zend_Paginator::factory(range(1, ((int) $this->view->jobs['total'])));
    	$this->view->paginator->setCurrentPageNumber((int) $this->searchParams['page'] ? $this->searchParams['page'] : 1);
    	$this->view->paginator->setItemCountPerPage((int) $this->searchParams['rows'] ? $this->searchParams['rows'] : 10 );
    	$this->view->paginator->setPageRange(10);
    	$this->view->jobs = $this->view->jobs['rows'];
    }
    
    /* ------------------------function to mark alert resolved------------------------------------------------- */
    
    public function markresolvedAction() {
    
    	$log_id = $_POST['log_id'];print_r();
    	$this->jobs->resolve($log_id);
    	$data = array();
    	$data['status'] = "success";
    	$data['message'] = "Alert resolved successfully.";
    	echo json_encode($data);
    	die;
    }

}
