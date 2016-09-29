<?php

class Hb_Ipushnotification
{

    public function __construct()
    {


        $this->db = Zend_Db_Table::getDefaultAdapter();

    }

    /**
     * function to update logic entities as per child count increase or decrease
     *
     * @device_token  string $device_token,
     * @data_message  string $data_message,
     * @count  integer $count,
     * @custom_data  array $custom_data,
     *
     */

    public function sendIosNotification($device_token, $data_message,$count,$custom_data = array())
    {
        // echo "sdf";die;
        require_once APPLICATION_PATH . '/../library/Zend/Mobile/Push/Apns.php'; // The SDK
        require_once APPLICATION_PATH . '/../library/Zend/Mobile/Push/Message/Apns.php';


        $message = new Zend_Mobile_Push_Message_Apns();

        // var_dump($message);die;
        $message->setAlert($data_message);

        $message->setBadge($count);
        $message->setSound('default');
        $message->setId(time());

        //$message->addCustomData('data', array('job_id' =>$job_id));
        $message->addCustomData('data', $custom_data);

        $message->setToken($device_token);
        $apns = new Zend_Mobile_Push_Apns();


        $apns->setCertificate(APPLICATION_PATH . '/../certificate/live/ck_ParentApp_prod.pem');
        $apns->setCertificatePassphrase('sofmen');


        try {
            //$apns->connect(Zend_Mobile_Push_Apns::SERVER_SANDBOX_URI);
            $apns->connect(Zend_Mobile_Push_Apns::SERVER_PRODUCTION_URI);
        } catch (Zend_Mobile_Push_Exception_ServerUnavailable $e) {
            // you can either attempt to reconnect here or try again later
            exit(1);
        } catch (Zend_Mobile_Push_Exception $e) {
            echo 'APNS Connection Error:' . $e->getMessage();
            die;
            exit(1);
        }

        try {
            $apns->send($message);
            // var_dump($apns->send($message));die;

        } catch (Zend_Mobile_Push_Exception_InvalidToken $e) {
            // you would likely want to remove the token from being sent to again
            echo $e->getMessage();
        } catch (Zend_Mobile_Push_Exception $e) {
            // all other exceptions only require action to be sent
            echo $e->getMessage();
            //die;
        }
        $apns->close();
    }

    public function send_notification_parent($device_token, $data_message, $job_id, $user_id = "", $count, $notification_id)
    {
        // echo "sdf";die;
        require_once APPLICATION_PATH . '/../library/Zend/Mobile/Push/Apns.php'; // The SDK
        require_once APPLICATION_PATH . '/../library/Zend/Mobile/Push/Message/Apns.php';


        $message = new Zend_Mobile_Push_Message_Apns();

        // var_dump($message);die;
        $message->setAlert($data_message);

        $message->setBadge($count);
        $message->setSound('default');
        $message->setId(time());

        //$message->addCustomData('data', array('job_id' =>$job_id));
        $message->addCustomData('data', array('job_id' => $job_id, 'user_id' => $user_id, 'notification_id' => $notification_id));

        $message->setToken($device_token);
        $apns = new Zend_Mobile_Push_Apns();

        //$apns->setCertificate(APPLICATION_PATH . '/../certificate/dev/ck_ParentApp_dev.pem');
        $apns->setCertificate(APPLICATION_PATH . '/../certificate/live/ck_ParentApp_prod.pem');
        $apns->setCertificatePassphrase('sofmen');


        try {
            //$apns->connect(Zend_Mobile_Push_Apns::SERVER_SANDBOX_URI);
            $apns->connect(Zend_Mobile_Push_Apns::SERVER_PRODUCTION_URI);
        } catch (Zend_Mobile_Push_Exception_ServerUnavailable $e) {
            // you can either attempt to reconnect here or try again later
            exit(1);
        } catch (Zend_Mobile_Push_Exception $e) {
            echo 'APNS Connection Error:' . $e->getMessage();
            die;
            exit(1);
        }

        try {
            $apns->send($message);
            // var_dump($apns->send($message));die;

        } catch (Zend_Mobile_Push_Exception_InvalidToken $e) {
            // you would likely want to remove the token from being sent to again
            echo $e->getMessage();
        } catch (Zend_Mobile_Push_Exception $e) {
            // all other exceptions only require action to be sent
            echo $e->getMessage();
            //die;
        }
        $apns->close();
    }


    public function send_notification_sitter($device_token, $data_message, $job_id, $user_id = "", $type = "", $count = "", $notification_id = "", $job_status = "", $status = '')
    {
        // print_r($notification_id);die;

        require_once APPLICATION_PATH . '/../library/Zend/Mobile/Push/Apns.php'; // The SDK
        require_once APPLICATION_PATH . '/../library/Zend/Mobile/Push/Message/Apns.php';
        $message = new Zend_Mobile_Push_Message_Apns();


        $message->setAlert($data_message);

        $message->setBadge($count);
        $message->setSound('default');
        $message->setId(time());

        $message->addCustomData('data', array('job_id' => $job_id, 'user_id' => $user_id, 'notification_type' => $type, 'notification_id' => $notification_id, 'job_status' => $job_status, 'status' => $status));
        $message->setToken($device_token);
        $apns = new Zend_Mobile_Push_Apns();


        //by namrata
        // $apns->setCertificate(APPLICATION_PATH . '/../certificate/dev/ck_sitterApp_dev.pem');
        $apns->setCertificate(APPLICATION_PATH . '/../certificate/live/ck_SitterApp_prod.pem');
        $apns->setCertificatePassphrase('sofmen');


        try {
            //$apns->connect(Zend_Mobile_Push_Apns::SERVER_SANDBOX_URI);
            $apns->connect(Zend_Mobile_Push_Apns::SERVER_PRODUCTION_URI);
            //var_dump($apns->connect(Zend_Mobile_Push_Apns::SERVER_PRODUCTION_URI));die;
        } catch (Zend_Mobile_Push_Exception_ServerUnavailable $e) {
            // you can either attempt to reconnect here or try again later

            exit(1);
        } catch (Zend_Mobile_Push_Exception $e) {
            echo 'APNS Connection Error:' . $e->getMessage();
            die;
            exit(1);
        }

        try {
            // var_dump($apns->send($message));die;
            $apns->send($message);


        } catch (Zend_Mobile_Push_Exception_InvalidToken $e) {
            // you would likely want to remove the token from being sent to again
            echo $e->getMessage();
        } catch (Zend_Mobile_Push_Exception $e) {
            // all other exceptions only require action to be sent
            echo $e->getMessage();

        }
        $apns->close();
    }


}