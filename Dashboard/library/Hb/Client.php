<?php

class Hb_Client extends Hb_User
{

    public function __construct()
    {
        $this->db = Zend_Db_Table::getDefaultAdapter();
        parent::__construct();
        $this->children = new Hb_Client_Children();
        $this->clientId = 1;
    }

    public function buyPackage($data)
    {
        if(empty($data))
            return false;

        $result = $this->db->insert('clients_subscription',$data);
        if($result)
        {
            $st = $this->setClientSubscription($data['userid']);
            return $result;
        }
        else
            return false;

    }
    public function getClientPaymentInfo($client_id)
    {
        if(empty($client_id))
            return false;


        $select = $this->db->select()->where('user_id=?',$client_id)->from('client_payment_profile');
        $stmt = $select->query();
        $info = $stmt->fetchObject();
        return $info;
    }
    public function getClientPaymentInfoById($client_payment_profile_id)
    {
        if(empty($client_payment_profile_id))
            return false;


        $select = $this->db->select()->where('client_payment_profile_id=?',$client_payment_profile_id)->from('client_payment_profile');
        $stmt = $select->query();
        $info = $stmt->fetchObject();
        return $info;
    }


    public function UpdatePaymentInfo($client_payment_profile_id,$update_data)
    {
        $this->client_payment_profile = new Zend_Db_Table('client_payment_profile');
        $where = $this->db->quoteInto('client_payment_profile_id = ?', $client_payment_profile_id);
        $this->client_payment_profile->update($update_data, $where);

    }

    /*OLD code */


    public function create(array $client, $status = 'active')
    {
        //print_r($client);die;

        $client['added_by'] = Zend_Auth::getInstance()->getIdentity()->userid;
        $client['usertype'] = 'P';
        $oldUser = $this->checkUsername($client['username']);
        if (empty($oldUser)) {
            $userid = $this->addUser($client, $status);
            $userInfo = array('userid' => $userid);
            $images = array();
            if ($client['profile_image']['name'] != '') {

                if ($this->upload($client['profile_image'], $userid)) {
                    $path = PROFILE_IMAGE_ABS_PATH . 'orginal/' . $userid . '__' . $client['profile_image']['name'];
                    $thumb = PROFILE_IMAGE_ABS_PATH . 'thumb/' . $userid . '__' . $client['profile_image']['name'];
                    $full = PROFILE_IMAGE_ABS_PATH . 'full/' . $userid . '__' . $client['profile_image']['name'];
                    //added by namrata

                    $APP = PROFILE_IMAGE_ABS_PATH . 'app_thumb/' . $userid . '__' . $client['profile_image']['name'];


                    $path = PROFILE_IMAGES . 'orginal/' . $userid . '__' . $client['profile_image']['name'];
                    $thumb = PROFILE_IMAGES . 'thumb/' . $userid . '__' . $client['profile_image']['name'];
                    $full = PROFILE_IMAGES . 'full/' . $userid . '__' . $client['profile_image']['name'];

                    $images = array(
                        'main_image' => $full,
                        'thumb_image' => $thumb,
                        'orginal_image' => $path,
                        'orginal_image_spec' => '',
                    );
                }
            }
            $other_info = array(
                'local_phone' => $client['local_phone'],
                'work_phone' => $client['work_phone'],
                'emergency_contact' => $client['emergency_contact'],
                'emergency_phone' => $client['emergency_phone'],
            );
            $profile = array_merge($userInfo, $images, $other_info);
            $this->addProfile($profile);
            //$profileid = $this->addProfile($client);
            $this->setClientDetail($client, $userid);
        }
        return $userid;
    }

    public function modify(array $client, $userid)
    {

        //print_r($client['notes']);die;

        $client['added_by'] = Zend_Auth::getInstance()->getIdentity()->userid;
        $client['usertype'] = 'P';

        //added by namrata afeter app issue
        $client['status'] = 'active';

        if (isset($client['clientnotes'])) {
            $client['clientnotes'] = $client['clientnotes'];
        }


        $oldUser = $this->checkUsername($client['username'], $userid);
        if (empty($oldUser)) {
            $this->updateUser($client, $userid);
            $userInfo = array('userid' => $userid);
            $images = array();
            if ($client['profile_image']['name'] != '') {

                if ($this->upload($client['profile_image'], $userid)) {
                    $path = PROFILE_IMAGE_ABS_PATH . 'orginal/' . $userid . '__' . $client['profile_image']['name'];
                    $thumb = PROFILE_IMAGE_ABS_PATH . 'thumb/' . $userid . '__' . $client['profile_image']['name'];
                    $full = PROFILE_IMAGE_ABS_PATH . 'full/' . $userid . '__' . $client['profile_image']['name'];
//added by namrata for app image size		

                    $APP = PROFILE_IMAGE_ABS_PATH . 'app_thumb/' . $userid . '__' . $client['profile_image']['name'];

                    $path = PROFILE_IMAGES . 'orginal/' . $userid . '__' . $client['profile_image']['name'];
                    $thumb = PROFILE_IMAGES . 'thumb/' . $userid . '__' . $client['profile_image']['name'];
                    $full = PROFILE_IMAGES . 'full/' . $userid . '__' . $client['profile_image']['name'];
//added by namrata for app image size    
                    $APP = PROFILE_IMAGES . 'app_thumb/' . $userid . '__' . $client['profile_image']['name'];


                    $images = array(
                        'main_image' => $full,
                        'thumb_image' => $thumb,
                        'orginal_image' => $path,
                        'orginal_image_spec' => '',
                        'app_thumb' => $APP,
                    );
                }
            }
            $other_info = array(
                'local_phone' => $client['local_phone'],
                'work_phone' => $client['work_phone'],
                'emergency_contact' => $client['emergency_contact'],
                'emergency_phone' => $client['emergency_phone'],
            );
            $profile = array_merge($userInfo, $images, $other_info);

            $this->addProfile($profile);


            //$profileid = $this->addProfile($client);
            $this->setClientDetail($client, $userid, 'update');
        }
        return $userid;
    }

    public function delete($parent_id)
    {


        $this->deleteUser($parent_id);
    }

    public function update(array $parent, $parent_id)
    {

    }

    public function setClientDetail($detail, $userid, $action = 'insert')
    {

        // print_r($detail);die;
        if ($_SESSION['Zend_Auth']['storage']->usertype == 'A') {

            $insert = array(
                'userid' => $userid,
                'interests' => is_array($detail) ? @implode(',', $detail['interests']) : $detail['interests'],
                'sitter_fee' => $detail['sitter_fee'],
                'spouse_firstname' => $detail['spouse_firstname'],
                'spouse_lastname' => $detail['spouse_lastname'],
                'spouse_dob' => $detail['spouse_dob'],
                'clientnotes' => $detail['clientnotes'],
            );
        } else {
            $insert = array(
                'userid' => $userid,
                'interests' => is_array($detail) ? @implode(',', $detail['interests']) : $detail['interests'],
                'sitter_fee' => $detail['sitter_fee'],
                'spouse_firstname' => $detail['spouse_firstname'],
                'spouse_lastname' => $detail['spouse_lastname'],
                'spouse_dob' => $detail['spouse_dob'],
            );
        }

        $this->clientDetailTable = new Zend_Db_Table('clients_detail');

        if ($action == 'update') {
            unset($insert['userid']);
            $where = $this->db->quoteInto('userid = ?', $userid);

            $this->clientDetailTable->update($insert, $where);
        } else
            $this->clientDetailTable->insert($insert);
    }

    public function deleteClientDetail($delete_id)
    {
        $this->clientDetailTable = new Zend_Db_Table('clients_detail');
        $where = $this->db->quoteInto('userid = ?', $userid);

        $this->clientDetailTable->delete($where);
    }

    public function getDetail($client_id, $type = 'profile')
    {

        $sql = "select  *,u.userid as client_id from users u left join clients_detail cd on(u.userid=cd.userid) where u.usertype='P' and u.userid=$client_id ";
        $res = $this->db->query($sql);
        $clients = $res->fetchAll();
        //print_r($clients);
        $sql = "select  * from user_profile where userid=$client_id ";
        $res = $this->db->query($sql);
        $profile = $res->fetchAll();
        //var_dump($profile);
        //modify($profile);
        $return = array_merge($clients[0], is_array($profile[0]) ? $profile[0] : $profile);

        return $return;
    }

    public function search($search = array(), $filter = array(), $sort = array('page' => '1'))
    {

        $search['status'] = in_array($search['status'], array('active', 'inactive', 'deleted')) ? $search['status'] : '';

        $sort['rows'] = (int)$search['rows'] == 0 ? 10 : (int)$search['rows'];

        //$sort['page'] = (int)$search['page'] ==0 ? 1:(int)$search['page'];

        $filter['sort'] = (int)$search['sort'] == 0 ? 1 : $search['sort'];

        $orderby = array('key' => (in_array($search['key'], array('u.userid', 'events_compeleted', 'open_requests', 'available_credits', 'firstname', 'joining_date','active_alerts')) ? $search['key'] : 'lastname'), 'odr' => (in_array($search['odr'], array('asc', 'desc')) ? $search['odr'] : 'asc'));

        $search_query[] = trim($search['email']) != '' ? '  u.username like "%' . $search['email'] . '%"' : '';


        if (!empty($search['fullname'])) {
            //getting length of string
            $array = explode(" ", $search['fullname']);

            if (count($array) == 1) {
                $search_query[] = trim($search['fullname']) != '' ? ' (u.firstname like "%' . $array[0] . '%"' . ' or  u.lastname like "%' . $array[0] . '%")' : '';

            } else {
                $end = end($array);
                $search_query[] = trim($search['fullname']) != '' ? ' (u.firstname like "%' . $array[0] . '%"' . ' or  u.lastname like "%' . $end . '%")' : '';
            }

        }
         if (!empty($search['job_id']))
         {
         	if(is_numeric($search['job_id']))
         	{
         		$job_search_join= "join jobs ON (jobs.client_user_id = u.userid) ";
         		$job_search_query = " and job_id = ".$search['job_id'];
         	}
         	else
         	{
         		return array('total' => '', 'rows' => array());
         	}
         }

// $search_query[]= trim($search['fullname'])!='' ? ' u.lastname like "%'.$search['fullname'].'%"' :'';
        $search_query[] = trim($search['current_city']) != '' ? ' u.current_city like "%' . $search['current_city'] . '%"' : '';
        $search_query[] = trim($search['phone']) != '' ? ' u.phone like "%' . $search['phone'] . '%"' : '';


        if (trim($search['joining_start_date']) != '' && trim($search['joining_end_date']) == '') {
            $search['joining_start_date'] = str_replace("-", "/", $search['joining_start_date']);
            $search_query[] = ' date(joining_date)  = date("' . date('Y-m-d', strtotime($search['joining_start_date'])) . '")';
        } else if (trim($search['joining_start_date']) == '' && trim($search['joining_end_date']) != '') {
            $search['joining_end_date'] = str_replace("-", "/", $search['joining_end_date']);
            $search_query[] = ' date(joining_date)  = date("' . date('Y-m-d', strtotime($search['joining_end_date'])) . '")';
        } else if (trim($search['joining_start_date']) != '' && trim($search['joining_end_date']) != '') {
            $search['joining_start_date'] = str_replace("-", "/", $search['joining_start_date']);
            $search_query[] = ' date(joining_date)  between  date("' . date('Y-m-d', strtotime($search['joining_start_date'])) . '") and date("' . date('Y-m-d', strtotime($search['joining_end_date'])) . '")';
        }


        $search_query = array_diff($search_query, array(''));
        if (!empty($search_query)) {
            $search_query = ' and (' . implode(' and ', $search_query) . ' )';
        } else
            $search_query = '';


        if ($sort['page'] > 1) {
            //$start = ($sort['page'] - 1) * $sort['rows'] - 1;
            //rem -1 by nam
            $start = ($sort['page'] - 1) * $sort['rows'];
        } else
            if ($sort['page'] == 1 or $sort['page'] == 0) {
                $start = 0;
            }


        $search['miscinfo'] = $search['miscinfo'] != '' ? " and u.miscinfo like '%{$search['miscinfo']}%' " : '';
        //$sql = "select SQL_CALC_FOUND_ROWS *,concat(firstname,' ',lastname) as fullname,u.userid as client_id from users u left join clients_detail cd on(u.userid=cd.userid) where u.usertype='P' and u.status!='deleted' $search_query {$search['miscinfo']} order by {$orderby['key']} {$orderby['odr']} LIMIT  $start,{$sort['rows']} ";

        $sql = "select SQL_CALC_FOUND_ROWS *,concat(firstname,' ',lastname) as fullname,u.userid as client_id,ifnull(alerts.alert,0) as active_alerts from users u left join clients_detail cd on(u.userid=cd.userid) 
        		LEFT JOIN (
								SELECT count( jobs.job_id ) alert, client_user_id
								FROM job_logs
								LEFT JOIN jobs ON jobs.job_id = job_logs.job_id
								WHERE is_resolved =0
								GROUP BY client_user_id
						  ) AS alerts ON (alerts.client_user_id = u.userid) $job_search_join
        where u.usertype='P' and u.status!='deleted' $search_query $job_search_query {$search['miscinfo']} order by {$orderby['key']} {$orderby['odr']} LIMIT  $start,{$sort['rows']} ";
        

        //print_r($sql);die;
        $res = $this->db->query($sql);
//
        $clients = $res->fetchAll();
        $res = $this->db->query('SELECT FOUND_ROWS() as total');
        $total = $res->fetchAll();

        //  print_r($total[0]['total']);die;

        $results = array('total' => $total[0]['total'], 'rows' => $clients);
        return $results;
    }

    public function getSubscription(array $search)
    {

        $subscribeTable = new Zend_Db_Table('clients_subscription');
        $this->db = $subscribeTable->getAdapter();

        if ($search['sub_id']) {
            $query = ' and client_sub_id=' . $search['sub_id'];
        }

        $search['status'] = in_array($search['status'], array('active', 'inactive', 'deleted')) ? $search['status'] : '';

        $search['rows'] = (int)$search['rows'] == 0 ? 10 : (int)$search['rows'];

        //$sort['page'] = (int)$search['page'] ==0 ? 1:(int)$search['page'];

        $filter['sort'] = (int)$search['sort'] == 0 ? 1 : $search['sort'];

        $orderby = array('key' => (in_array($search['key'], array('userid', 'slots', 'price', 'start_date', 'end_date', 'status', 'last_modified_by', 'last_modified_date')) ? $search['key'] : 'last_modified_date'), 'odr' => (in_array($search['odr'], array('asc', 'desc')) ? $search['odr'] : 'desc'));

        if ($search['page'] > 1) {
            $start = ($search['page'] - 1) * $search['rows'] - 1;
        } else
            if ($search['page'] == 1 or $search['page'] == 0) {
                $start = 0;
            }


        $sql = 'select SQL_CALC_FOUND_ROWS * from clients_subscription where userid=' . $search['userid'] . ' ' . $query . " order by {$orderby['key']} {$orderby['odr']} LIMIT  $start,{$search['rows']}";

        $res = $this->db->query($sql);
        $subscription = $res->fetchAll();
        $res = $this->db->query('SELECT FOUND_ROWS() as total');

       	$sql = "select sum(slots) as credits, sum(total_credits) as purchased_credits from clients_subscription where end_date > now()  and userid=".$search['userid'];

		$res1 = $this->db->query($sql);
		$result = $res1->fetchAll();
		$total_avail_credits =(int) $result[0]['credits'];
		$purchased_credits =(int) $result[0]['purchased_credits'];
		
        $total = $res->fetchAll();
        $results = array('total' => $total[0]['total'], 'rows' => $subscription , 'total_avail_credits'=>$total_avail_credits,'purchased_credits'=>$purchased_credits );
        return $results;
    }

    public function authorizeNetPayment()
    {

    }

    public function getPackageIdByAmount($amount)
    {


        //return 
    }

    public function addSubscription(array $subs, $client_id = 0)
    {


        $insert = array(
            'userid' => $subs['userid'],
            'total_credits' => $subs['slots'],
            'slots' => $subs['slots'],
            'price' => $subs['price'],
            'start_date' => isset($subs['start_date']) ? date('Y-m-d H:i:s', strtotime($subs['start_date'])) : date('Y-m-d H:i:s'),
            'end_date' => isset($subs['end_date']) ? date('Y-m-d H:i:s', strtotime($subs['end_date'])) : date('Y-m-d H:i:s', strtotime('+1 year')),
            'status' => '1',
            // 'last_modified_by'=>Zend_Auth::getInstance()->getIdentity()->userid, 
            'last_modified_date' => date('Y-m-d H:i:s'),
            'notes' => (string)$subs['notes'] != '' ? $subs['notes'] : '',
            'package_id' => (int)$subs['package_id'],
            'transaction_id' => $subs['transaction_id'],
            'payment_gateway' => $subs['checkout_type'] == 'free' ? 'Free' : 'AuthorizeNet',
 	    'last_modified_by' => $subs['last_modified_by']?(int)$subs['last_modified_by']:'',
        );
        //print_r($insert);die();
        $subscribeTable = new Zend_Db_Table('clients_subscription');
        $subscribeTable->insert($insert);
        $this->subsId = $this->db->lastInsertId();
        $this->setClientSubscription($subs['userid']);

        $additional = array(
            '{transaction_id}' => $subs['transaction_id'],
            '{credits}' => $subs['slots'],
            '{amount}' => $subs['price'],
            '{notes}' => $subs['notes']
        );

        if ($subs['checkout_type'] != 'free') {
            $this->clientMail($subs['userid'], 'payment_confirmation', 'client', 'mail', $additional);
            return $this->subsId;
        }
        return $this->subsId;
    }

    public function updateSubscription(array $subs, $sub_id)
    {


        $insert = array(
            'userid' => $subs['userid'],
            'slots' => $subs['slots'],
            'price' => $subs['price'],
            'start_date' => isset($subs['start_date']) ? date('Y-m-d H:i:s', strtotime($subs['start_date'])) : date('Y-m-d H:i:s'),
            'end_date' => isset($subs['end_date']) ? date('Y-m-d H:i:s', strtotime($subs['end_date'])) : date('Y-m-d H:i:s', strtotime('+1 year')),
            'status' => '1',
            'last_modified_by' => Zend_Auth::getInstance()->getIdentity()->userid,
            'last_modified_date' => date('Y-m-d H:i:s'),
            'notes' => $subs['notes'],
            'package_id' => $subs['package_id'],
        );

        $where = $this->db->quoteInto('client_sub_id = ?', $sub_id);


        $subscribeTable = new Zend_Db_Table('clients_subscription');
        $subscribeTable->update($insert, $where);
        $this->setClientSubscription($subs['userid']);
        return true;
    }

    public function deleteSubscription($id)
    {
        $where = $this->db->quoteInto('client_sub_id = ?', $id);

        $this->clientSubscriptionTable = new Zend_Db_Table('clients_subscription');
        $this->clientSubscriptionTable->delete($where);
    }

    public function setClientSubscription($userid)
    {

        $sql = "update clients_detail set available_credits =(select sum(slots) from clients_subscription where end_date > now()  and userid=$userid) where  userid=$userid";

        $this->db = Zend_Db_Table::getDefaultAdapter();
        return $this->db->query($sql);
    }

    public function reimburse($job_id)
    {
        $this->jobs = new Hb_Jobs();
        $job = $this->jobs->search(array('job_id' => $job_id));
        $job = $job['rows'][$job_id];

        $sql = "update clients_subscription set slots =slots+{$job['credits_used']} where  userid='{$job['client_user_id']}' and client_sub_id = '{$job['subs_id']}'";

        $this->db = Zend_Db_Table::getDefaultAdapter();

        $this->db->query($sql);
        $this->setClientSubscription($job['client_user_id']);
    }

    public function clientMailString($client)
    {


        return array(
            '{client_email}' => $client['username'],
            '{client_firstname}' => ucwords($client['firstname']),
            '{client_lastname}' => ucwords($client['lastname']),
            '{client_phone}' => $client['phone'],
            '{sitter_currentcity}' => ucwords($sitter['current_city']),
        );
    }

    public function clientMail($client_id, $mail_name, $to = 'client', $from = 'mail', $additional = array())
    {

        $client = new Hb_Client();

        //if($client_id)
        $clientInfo = $client->getDetail($client_id);

        $this->hbSettings = new Hb_Settings();
        $mailTemplate = $this->hbSettings->getMailTemplates(array('mail_name' => $mail_name));
        $mailTemplate = $mailTemplate[0];
        $text = '';
        if ($from == 'mail')
            $from = $mailTemplate['from'];
        else if ($from == 'client')
            $from = $clientInfo['username'];

        if ($to == 'mail')
            $to = $mailTemplate['to'];

        else if ($to == 'client') {
            $to = $clientInfo['username'];
            $to_name = $clientInfo['firstname'] . ' ' . $clientInfo['lastname'];
        }

        $cc = explode(',', $mailTemplate['cc']);
        $bcc = explode(',', $mailTemplate['bcc']);


        $clientReplace = $client->clientMailString($clientInfo);

        $subject = str_ireplace(array_keys($clientReplace), $clientReplace, $mailTemplate['subject']);
        $subject = str_ireplace(array_keys($additional), $additional, $mailTemplate['subject']);


        $text = str_ireplace('{viewlink}', SITE_URL . 'clients/preview/job/' . $job_id, $mailTemplate['description']);
        $text = str_ireplace(array_keys($address), $address, $text);
        $text = str_ireplace(array_keys($clientReplace), $clientReplace, $text);
        $text = str_ireplace(array_keys($additional), $additional, $text);
//for job details

        $text = str_ireplace(array_keys($to_replace_job), $to_replace_job, $text);


// die();
        $mail = new Zend_Mail();
        $mail->setBodyText($text);
        $mail->setBodyHtml($text);
        $mail->setFrom($from);
        if (!empty($cc))
            foreach ($cc as $c) {
                $mail->addCc($c);
            };
        if (!empty($bcc))
            foreach ($bcc as $c) {
                $mail->addBcc($c);
            };


        $mail->addTo($to, $to_name);
        $mail->setSubject($subject);
        $mail->send();
    }

    /* ===============================PAYMENTS DETAILS SECTION OF THE CLIENT/PARENTS=========================================================== */

    /* -------------------------------------to insert payment details of the client--------------------------- */

    public function saveclientpaymentdetails($insert_data)
    {
        $this->client_payment_profile = new Zend_Db_Table('client_payment_profile');
        $this->client_payment_profile->insert($insert_data);
    }

    /* ------------------------function to get payment details of the client if exist---------------------------- */

    public function getPaymentDetail($user_id)
    {
        $sql = "select  * from client_payment_profile  where user_id=$user_id ";
        $res = $this->db->query($sql);
        $clients = $res->fetchAll();
        if (!empty($clients)) {
            return ($clients);
        } else {
            return false;
        }
    }


    /* ------------------------to update payments details of the client-------------------------------- */

    public function updateclientpaymentdetails($client_payment_profile_id, $update_data, $user_id)
    {
        $sql = " update client_payment_profile SET card_number='" . $update_data['card_number'] . "',exp_date='" . $update_data['exp_date'] . "',last_updated='" . $update_data['last_updated'] . "' where client_payment_profile_id=$client_payment_profile_id and user_id=$user_id ";
        $this->db->query($sql);
    }

//end of function


    /* -------------------------function to update payment details of the job--------------------------------------------------- */

    public function update_payment_status($user_id, $amount, $job_id)
    {
        $sql = " update jobs SET client_payment_status='paid',total_received=$amount,job_status = CASE 
            WHEN sitter_payment_status = 'paid' THEN 'closed'  WHEN sitter_payment_status = 'unpaid' THEN 'completed'  end  where client_user_id=$user_id and job_id=$job_id";
        $this->db->query($sql);
    }

    /* ------------------------function to inset transaction details------------------------------------------- */

    public function insert_transaction_details($insert_data)
    {
        $this->client_transaction = new Zend_Db_Table('client_transaction_details');
        $this->client_transaction->insert($insert_data);
    }

    /* --------------------------function to insert in to payment history--------------------------- */

    public function insert_transaction_history($transaction_data, $id)
    {
        $this->transaction_history = new Zend_Db_Table('transaction_history');
        $this->transaction_history->insert($transaction_data);

        //to send email to client from herer


        $additional = array(
            '{transaction_id}' => $id,
             '{job_id}' => $transaction_data['job_id'],
             '{amount}' => $transaction_data['amount'],
            '{notes}' => "Job booking amount charged"
        );

        $jobs = new Hb_Jobs();
        $sitter_id = "";

        $jobs->jobChargeMail($transaction_data['job_id'], 'payment-deduction-mail-client', 'client', 'mail', $sitter_id, $additional);


        //if ($subs['checkout_type'] != 'free')
        //    $this->clientMail($transaction_data['user_id'], 'payment-deduction-mail-client', 'client', 'mail', $additional);

    }

    /* --------------------------to update child count----------------------------- */

    public function update_child_count($job_id, $actual_child_count)
    {

        $last_modified_date = date('Y-m-d H:i:s');
        $last_modified_by = Zend_Auth::getInstance()->getIdentity()->userid;

        $job_id = $this->db->quote($job_id);
        $sql = "select child_count from jobs  where job_id=$job_id ";
        $res = $this->db->query($sql);
        $count = $res->fetchAll();
        $c_count = $count[0]['child_count'];
        if ($c_count != $actual_child_count) {

            $sql = "update jobs SET actual_child_count=$actual_child_count,last_modified_by=$last_modified_by,last_modified_date='" . $last_modified_date . "', modified='Yes' where job_id=$job_id";
            $this->db->query($sql);
        }
    }
    
    // for job billing by line item
    public function getSubscriptionnew(array $search)
    {
    
    	$subscribeTable = new Zend_Db_Table('clients_subscription');
    	$this->db = $subscribeTable->getAdapter();
    
    	if ($search['sub_id']) {
    		$query = ' and client_sub_id=' . $search['sub_id'];
    	}
    
    	$search['status'] = in_array($search['status'], array('active', 'inactive', 'deleted')) ? $search['status'] : '';
    
    	$search['rows'] = (int)$search['rows'] == 0 ? 10 : (int)$search['rows'];
    
    	//$sort['page'] = (int)$search['page'] ==0 ? 1:(int)$search['page'];
    
    	$filter['sort'] = (int)$search['sort'] == 0 ? 1 : $search['sort'];
    
    	$orderby = array('key' => (in_array($search['key'], array('userid','job_id', 'slots', 'price', 'start_date', 'end_date', 'status', 'last_modified_by', 'c.last_modified_date')) ? $search['key'] : 'c.last_modified_date'), 'odr' => (in_array($search['odr'], array('asc', 'desc')) ? $search['odr'] : 'desc'));
    
    	if ($search['page'] > 1) {
    		$start = ($search['page'] - 1) * $search['rows'] - 1;
    	} else
    		if ($search['page'] == 1 or $search['page'] == 0) {
    		$start = 0;
    	}
    
    
    	$sql = 'select SQL_CALC_FOUND_ROWS c.*, job_id from clients_subscription c join jobs on (jobs.subs_id=c.client_sub_id) where userid=' . $search['userid'] . ' ' . $query . " order by {$orderby['key']} {$orderby['odr']} LIMIT  $start,{$search['rows']}";
    
    	$res = $this->db->query($sql);
    	$subscription = $res->fetchAll();
    	$res = $this->db->query('SELECT FOUND_ROWS() as total');
    
    	$sql = "select sum(slots) as credits,sum(total_credits) as purchased_credits from clients_subscription where end_date > now()  and userid=".$search['userid'];
    
    	$res1 = $this->db->query($sql);
    	$result = $res1->fetchAll();
    	$total_avail_credits =(int) $result[0]['credits'];
    	$purchased_credits =(int) $result[0]['purchased_credits'];
    	 
    	$total = $res->fetchAll();
    	$results = array('total' => $total[0]['total'], 'rows' => $subscription , 'total_avail_credits'=>$total_avail_credits ,'purchased_credits'=>$purchased_credits);
    	return $results;
    }
    
    /* ----------------------------function to get payment history of sitters--------------------------- */
    
    public function client_transaction_history($search = array(), $filter = array(), $sort = array()) {
    
    
    
    	if ((!empty($search['userid'])) && ($search['userid'] != 0)) {
    		$query_clientId = 'and client_id = ' . $search['userid'];
    	} else {
    		$query_clientId = "";
    	}
    
    
    	if (trim($search['payment_start_date']) != '' && trim($search['payment_end_date']) == '') {
    
    
    
    		$search_query[] = ' date(date_added)  = date("' . date('Y-m-d', strtotime($search['payment_start_date'])) . '")';
    	} else if (trim($search['payment_start_date']) == '' && trim($search['payment_end_date']) != '') {
    		$search_query[] = ' date(date_added)  = date("' . date('Y-m-d', strtotime($search['payment_end_date'])) . '")';
    	}
    	// else '';
    	else if (trim($search['payment_start_date']) != '' && trim($search['payment_end_date']) != '') {
    		$search_query[] = ' date(date_added)  between  date("' . date('Y-m-d', strtotime($search['payment_start_date'])) . '") and date("' . date('Y-m-d', strtotime($search['payment_end_date'])) . '")';
    	}
    	// else '';
    
    
    	$search_query = array_diff($search_query, array(''));
    	if (!empty($search_query)) {
    		$search_query = ' and (' . implode(' or ', $search_query) . ' )';
    	} else
    		$search_query = '';
    
    
    	$orderby = array('key' => (in_array($search['key'], array('job_id','amount','date','client_name')) ? $search['key'] : 'date_added' ), 'odr' => (in_array($search['odr'], array('asc', 'desc')) ? $search['odr'] : 'desc' ));
    	
    	if ($orderby['key'] == 'client_name') {
    		$orderby['key'] = 'firstname '.$orderby['odr'].', lastname';
    	}
    
    	if ($orderby['key'] == 'date') {
    		$orderby['key'] = 'date_added ';
    	}
    	
    	$sort['rows'] = (int) $search['rows'] == 0 ? 10 : (int) $search['rows'];
    	$sort['page'] = (int) $search['page'] == 0 ? 1 : (int) $search['page'];
    	$filter['sort'] = (int) $search['sort'] == 0 ? 1 : $search['sort'];
    	if ($search['page'] > 1) {
    		$start = ($sort['page'] - 1) * $sort['rows'];
    	} else
    		if ($sort['page'] == 1 or $sort['page'] == 0) {
    		$start = 0;
    	}

    	$sql = "select * from client_transaction_details left join users on users.userid=client_transaction_details.client_id where users.status ='active' $query_clientId  $search_query  order by {$orderby['key']} {$orderby['odr']}";
    
    	$res = $this->db->query($sql);
    	$totalrecord = $res->fetchAll();
    	$total_count = count($totalrecord);
    
    	$sql = "select * from client_transaction_details left join users on users.userid=client_transaction_details.client_id where users.status ='active' $query_clientId  $search_query  order by {$orderby['key']} {$orderby['odr']} LIMIT  $start,{$sort['rows']} ";
    
    	$result = $this->db->query($sql);
    	$total = $result->fetchAll();
    
    	$results = array('total' => $total_count, 'rows' => $total, 'record' => $totalrecord);
    	return $results;
    }
    
    /* -----------------------------function to get payments details by id---------------------- */
    
    public function get_transaction_details($payment_id) {
    	$sql = "select firstname,lastname,client_id,job_id,amount,date_added,client_transaction_details_id,transaction_id,payment_mode from client_transaction_details left join users on client_transaction_details.client_id=users.userid where client_transaction_details_id=$payment_id";
    
    	$res = $this->db->query($sql);
    	$total = $res->fetchAll();
    	return($total);
    }
    
    
    // for job billing by line item transactions
    public function bookinghistory(array $search)
    {
    
    	$subscribeTable = new Zend_Db_Table('clients_subscription');
    	$this->db = $subscribeTable->getAdapter();
    
    	if ($search['sub_id']) {
    		$query = ' and client_sub_id=' . $search['sub_id'];
    	}
    
    	$search['status'] = in_array($search['status'], array('active', 'inactive', 'deleted')) ? $search['status'] : '';
    
    	$search['rows'] = (int)$search['rows'] == 0 ? 10 : (int)$search['rows'];
    
    	//$sort['page'] = (int)$search['page'] ==0 ? 1:(int)$search['page'];
    
    	$filter['sort'] = (int)$search['sort'] == 0 ? 1 : $search['sort'];
    
    	$orderby = array('key' => (in_array($search['key'], array('userid','client_name','job_id', 'slots', 'price', 'start_date', 'end_date', 'status', 'last_modified_by', 'c.last_modified_date')) ? $search['key'] : 'c.last_modified_date'), 'odr' => (in_array($search['odr'], array('asc', 'desc')) ? $search['odr'] : 'desc'));
    
    	if ($orderby['key'] == 'client_name') {
    		$orderby['key'] = 'firstname '.$orderby['odr'].', lastname';
    	}
    	
    	if ($search['page'] > 1) {
    		$start = ($search['page'] - 1) * $search['rows'] - 1;
    	} else
    		if ($search['page'] == 1 or $search['page'] == 0) {
    		$start = 0;
    	}
    
    
    	$sql = 'select SQL_CALC_FOUND_ROWS c.*,firstname,lastname,job_id from clients_subscription c join users join jobs where (c.userid=users.userid and jobs.subs_id=c.client_sub_id ) ' . $query . " order by {$orderby['key']} {$orderby['odr']} LIMIT  $start,{$search['rows']}";
    
    	$res = $this->db->query($sql);
    	$subscription = $res->fetchAll();
    	$res = $this->db->query('SELECT FOUND_ROWS() as total');
    
    	$sql = "select sum(slots) as credits,sum(total_credits) as purchased_credits from clients_subscription where end_date > now()";
    
    	$res1 = $this->db->query($sql);
    	$result = $res1->fetchAll();
    	$total_avail_credits =(int) $result[0]['credits'];
    	$purchased_credits =(int) $result[0]['purchased_credits'];
    
    	$total = $res->fetchAll();
    	$results = array('total' => $total[0]['total'], 'rows' => $subscription , 'total_avail_credits'=>$total_avail_credits ,'purchased_credits'=>$purchased_credits);
    	return $results;
    }

}
