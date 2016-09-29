<?php

class CroneController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */

        $this->db = Zend_Db_Table::getDefaultAdapter();
    }
    
    
    
    public function indexAction()
    {
        $t1 = StrToTime ( '2006-04-14 11:45:00' );
		$t2 = StrToTime ( '2006-04-14 11:30:00' );
		$diff = $t1 - $t2;
		$hours = $diff / ( 60 * 60 );
		echo $hours;die;
    }

    public function notifyAction() {


        $current_date = date('Y-m-d H:i');

        $new_date1 = date("Y-m-d H:i:s", strtotime($current_date . "+15 minutes"));

        $new_date2 = date("Y-m-d H:i:s", strtotime($current_date . "+30 minutes"));

        $sql = "select job_start_date,job_id from jobs where (job_status='new'or job_status='pending') and job_start_date BETWEEN '$new_date1' and '$new_date2' ";

       // $sql = "select job_start_date,job_id from jobs where job_status='new' LIMIT 10 ";

        $res = $this->db->query($sql);

        $result = $res->fetchAll();
        
      

        if (!empty($result)) {

            $uids = Array();
            foreach ($result as $u) {
                $uids[] = $u['job_id'];
                $list = implode(",", $uids);
            }
            $this->hbSettings = new Hb_Settings();

            $mail_name = 'pending_job_notification_admin';
            $mailTemplate = $this->hbSettings->getMailTemplates(array('mail_name' => $mail_name));
            $mailTemplate = $mailTemplate[0];

            $from = $mailTemplate['from'];
            $to = ADMINMAIL;
          //      $to = 'namrata.sharma@sofmen.com';


            $cc = explode(',', $mailTemplate['cc']);
            $bcc = explode(',', $mailTemplate['bcc']);

            $subject = $mailTemplate['subject'];

            $to_replace = array('{job_number}');
            $replace_with = array($list);

            $text = str_ireplace($to_replace, $replace_with, $mailTemplate['description']);
            $subject = str_ireplace($to_replace, $replace_with, $subject);

            $mail = new Zend_Mail();
            //$text = "Hello {$userInfo['firstname']} {$userInfo['lastname']}, <br> Your New Password:$password <br> Thanks,<br>HamptonsBabysitters.com Administrator";
            $mail->setBodyText($text);

            $mail->setBodyHtml($text);
            
            $email=$to;
           

            $mail->setFrom($from);

            if (!empty($cc))
                foreach ($cc as $c)
                    $mail->addCc($c);
            if (!empty($bcc))
                foreach ($bcc as $c)
                    $mail->addBcc($c);

            $mail->addTo(trim($email));

            $mail->setSubject($subject);
            $mail->send();
        }


        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        // $this->_redirect() ;
    }

    
    
    //for notifications prior 3 hours //15 min
    public function jobnotifyadminAction() {
        $current_date = date('Y-m-d H:i');

        $new_date1 = date("Y-m-d H:i:s", strtotime($current_date . "+180 minutes"));

        $new_date2 = date("Y-m-d H:i:s", strtotime($current_date . "-175 minutes"));

        //$sql = "select job_start_date,job_id from jobs left join job_sent on  job_sent.job_id=jobs.job_id where sent_by=1  where job_status='pending' and sent_date  = $new_date1";
        $sql = "select job_start_date,job_id from jobs where job_status in ('new','pending') and job_start_date  = '$new_date1'";
        $res = $this->db->query($sql);

        $result = $res->fetchAll();
        
        print_r($sql);
       	print_r($result);
        if (!empty($result)) {

            $uids = Array();
            foreach ($result as $u) {
            	
            	$data_log = array('job_id' => $u['job_id'],
            			'modified_by' => 1,
            			'modified_date' => date('Y-m-d H:i:s'),
            			'modification' => 'Immediate Job',
            			'initial' => 0,
            			'modified' => 1
            	);
            	$this->db->insert('job_logs', $data_log);
            	
                $uids[] = $u['job_id'];
                $list = implode(",", $uids);
            }
            $this->hbSettings = new Hb_Settings();

                $mail_name = 'sitters_pending_job_notification_admin';
            $mailTemplate = $this->hbSettings->getMailTemplates(array('mail_name' => $mail_name));
            $mailTemplate = $mailTemplate[0];
			
            $from = $mailTemplate['from'];
            $to = ADMINMAIL;

            print_r(ADMINMAIL);

            $cc = explode(',', $mailTemplate['cc']);
            $bcc = explode(',', $mailTemplate['bcc']);

            $subject = $mailTemplate['subject'];

            $to_replace = array('{job_number}');
            $replace_with = array($list);

            $text = str_ireplace($to_replace, $replace_with, $mailTemplate['description']);
            $subject = str_ireplace($to_replace, $replace_with, $subject);

            $mail = new Zend_Mail();
           
            //$text = "Hello {$userInfo['firstname']} {$userInfo['lastname']}, <br> Your New Password:$password <br> Thanks,<br>HamptonsBabysitters.com Administrator";
            $mail->setBodyText($text);

            $mail->setBodyHtml($text);

            $mail->setFrom($from);

            if (!empty($cc))
                foreach ($cc as $c)
                    $mail->addCc($c);
            if (!empty($bcc))
                foreach ($bcc as $c)
                    $mail->addBcc($c);

            $mail->addTo(trim($to));
            
            $mail->setSubject($subject);
            $mail->send();
        }


        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        
        
        
        
    }
    
    
    public function testemailsAction()
    {
        //use Zend\Mail;
        
         require_once APPLICATION_PATH . '/../library/Zend/Mail/Message.php'; // The

$mail = new essage();
$mail->setBody('This is the text of the email.');
$mail->setFrom('projects@sofmen.com', 'Dolf');
$mail->addTo('namrata.sharma@sofmen.com', 'Matthew');
$mail->setSubject('TestSubject');

$transport = new Mail\Transport\Sendmail('-freturn_to_me@example.com');

print_r($transport->send($mail));die;
$transport->send($mail);
        

$this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();

    }

    
}


