<?php

class Admin_ReportsController extends Zend_Controller_Action {

    public function init() {

        /* Initialize action controller here */
        $this->reports = new Hb_Reports();
        if ($this->_request->getActionName() == 'view') {

            $this->_request->setActionName('index');
        }
        $this->searchParams = $this->_request->getParams();
        $get_post = $this->_request->getPost();

        if(!empty($get_post))
        $get_post['userid'] = $get_post['userid']?$get_post['userid']:array();

        $this->searchParams = $get_post + $this->searchParams;


        //print_r($this->searchParams);die;
        $this->view->searchParams = $this->searchParams;

        $this->view->actionName = $this->_request->getActionName();
    }

    public function indexAction() {


        $sitters = $this->reports->sitters();

        $sitters = array(array('userid'=>"15_most_productive","name"=>"15 most productive"))+$sitters;

        $this->view->sitters = $sitters;

        $this->view->sitter_earnings = $sum1;
        $this->view->total_received = $sum2;
        $this->view->company_profit = $sum3;
    }

    //for sitters earning graph.
    public function sitterproductivityAction() {


        $draw = $this->_request->get('sEcho');
        $start = intval($this->_request->get('iDisplayStart'));
        $length = intval($this->_request->get('iDisplayLength'));
        $search = $this->_request->get('sSearch');

        if (empty($search))
            $search = $this->_request->get('keywords');


        $job_start_date = $this->_request->get('job_start_date');
        $job_end_date = $this->_request->get('job_end_date');
        $user_ids = $this->_request->get('user_ids');


        $order_temp = $this->_request->get('iSortCol_0') ? $this->_request->get('iSortCol_0') : 0;
        $order = $this->_request->get('mDataProp_' . $order_temp);
        $order_by = $this->_request->get('sSortDir_0') ? $this->_request->get('sSortDir_0') : '';

        $search_data = array();
        $search_data['keyword'] = $search;
        $search_data['job_start_date'] = $job_start_date;
        $search_data['job_end_date'] = $job_end_date;
        $search_data['userid'] = $user_ids;

        $start = ($start >= 0) ? $start : 0;

        // to get filter result via search criteria
       $total = $this->reports->searchv2(TRUE, $search_data, $length, $start, $order, $order_by,$search_data);

        $all_users = array();
        if ($total > 0)
            $all_users =  $this->reports->searchv2(FALSE, $search_data, $length, $start, $order, $order_by);


        // to get total of all jobs at once
        $val = $this->reports->getTotalv2(FALSE, $search_data, $length, $start, $order, $order_by);

        $all_users =array_merge( $all_users ,
            array(array(
            "firstname"=> " ",
            "completed"=>"<span style='font-weight: bold;text-align:right'>Total Amount<span>",
            "earnings"=> "<span style='font-weight: bold;text-align:right'>". money_format('%.2n',  $val['sitter_earning'])."<span>",
            "received"=> "<span style='font-weight: bold;text-align:right'>". money_format('%.2n', $val['total_received'])."<span>",
            "company_profit"=> "<span style='font-weight: bold;text-align:right'>".money_format('%.2n', $val['company_profit'])."<span>",
            )
        ));

        $out = array('sEcho' => intval($draw)
        , 'iTotalRecords' => $total
        , 'iTotalDisplayRecords' => $total
        , 'aaData' => $all_users

        );

        echo json_encode($out);
        die;
        exit;

        if($_POST['ajaxcall'])
        {
            /*if($data['end_date']==null)
                $data['end_date']=date('m-d-Y');
            $cat=$data['start_date']." - ".$data['end_date']; */
            $res['categories']=$record['categories'];
            $res['status']="success";
            $res['data']=$record['graphdata'];
            echo json_encode($res,JSON_NUMERIC_CHECK); die;
        }
        else{
            $this->view->sitters=$this->reports->sitters();
            $this->view->earning=json_encode($record,JSON_NUMERIC_CHECK);
        }

    }

    /* ---------------------------to get all payment history------------------------------- */

    public function alltransactionsAction() {

        //print_r($this->searchParams);die;

        $this->view->transaction = $this->reports->get_all_transaction_new($this->searchParams);
        $this->view->paginator = Zend_Paginator::factory(range(1, ((int) $this->view->transaction['total'])));
        $this->view->paginator->setCurrentPageNumber((int) $this->searchParams['page'] ? $this->searchParams['page'] : 1);
        $this->view->paginator->setItemCountPerPage((int) $this->searchParams['rows'] ? $this->searchParams['rows'] : 10 );
        $this->view->paginator->setPageRange(10);

        $this->view->transactions = $this->view->transaction['rows'];
        
        $this->view->alltransaction=$this->view->transaction['record'];
        
        

        $val = $this->view->transaction['record'];
        $sum1 = 0;
        foreach ($val as $keyp => $subArray) {
            if ($subArray['transaction_type'] == 'Cr.')
                $sum1+=(float) $subArray['amount'];
        }

        $sum2 = 0;
        foreach ($val as $keyp => $subArray) {

            if ($subArray['transaction_type'] == 'Dr.')
                $sum2+=(float) $subArray['amount'];
        }

        
      //  echo $sum2;die;
        $this->view->transactions_credit = $sum1;
        $this->view->transactions_debit = $sum2;
        $this->view->transactions_amt = $sum1 - $sum2;
    }

    /* ------------------------------func----------------------------------- */

    public function createpdfAction() {



        $type = $this->_request->getParam('type');
        $start_date = $this->_request->getParam('start_date');
        $end_date = $this->_request->getParam('end_date');
        $key = $this->_request->getParam('key');
        $odr = $this->_request->getParam('odr');

        $data = array();

        if (!empty($key)) {
            $data['key'] = $key;
        }
        if (!empty($odr)) {
            $data['odr'] = $odr;
        }


        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['transaction_type='] = $type;
        $this->searchParams=$data;
        
        $record = $this->reports->get_all_transaction_reports($this->searchParams);
        
       // print_r($data['rows']);die;


        require_once APPLICATION_PATH . '/../library/Hb/mpdf/mpdf.php'; // The SDK
        $mpdf = new mPDF();

        //$mpdf->WriteHTML('<p>Your first taste of creating PDF from HTML</p>');

        $data=' <table style="border: 1px solid gray; border-collapse: collapse; font-size:14px;">';

        $data.='<thead><tr><th style="padding:5px; border: 1px solid gray; border-collapse: collapse;">Amount</th><th style="padding:5px; border: 1px solid gray; border-collapse: collapse;">Credit/Debit</th><th style="padding:5px; border: 1px solid gray; border-collapse: collapse;">Date</th><th style="padding:5px; border: 1px solid gray; border-collapse: collapse;">Client Name/Sitter Name</th><th style="padding:5px; border: 1px solid gray; border-collapse: collapse;">Job Id</th></tr></thead><tbody>';
        
      
        
        
        foreach($record['rows'] as $info)
        {
            $data.= '<tr>
                            
                            <td style="padding:3px;border: 1px solid gray; border-collapse: collapse;">'.$info['amount'].'</td>
                             <td style="padding:3px;border: 1px solid gray; border-collapse: collapse;">'.$info['transaction_type'].'</td>
                             <td style="padding:3px;border: 1px solid gray; border-collapse: collapse;">'.date('m/d/Y', strtotime($info['date_added'])).'</td>
                             <td style="padding:3px;border: 1px solid gray; border-collapse: collapse;">'. $info['firstname'] . " " . $info['lastname'].'</td>
                             <td style="padding:3px;border: 1px solid gray; border-collapse: collapse;">'.$info['job_id'].'</td>
                            
                   </tr></tbody>';
        }
        
          $data.='</table>';
        $mpdf->WriteHTML($data);

//$mpdf->Output();
        $mpdf->Output('BabysittersReport.pdf', 'D');

//$mpdf->Output('filename.pdf','F');
        exit;
    }
    
    //for sitters earning graph.
    public function graphAction() {
    	$data= $_POST;
    	//$record = $this->reports->sitterEarningJSON($data);
    	$record = $this->reports->sitterEarningJSONv2($data);

    	if($_POST['ajaxcall'])
    	{ 

    		$res['categories']=$record['categories'];
    		$res['status']="success";
    		$res['data']=$record['graphdata'];
    		echo json_encode($res,JSON_NUMERIC_CHECK); die;
    	}
    	else{
    		$this->view->sitters=$this->reports->sitters();
    		$this->view->earning=json_encode($record,JSON_NUMERIC_CHECK);
    	}
    		
    }

}
