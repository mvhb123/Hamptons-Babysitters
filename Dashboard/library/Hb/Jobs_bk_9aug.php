<?php

class Hb_Jobs
{

    public function __construct()
    {

        $this->jobTable = new Zend_Db_Table('jobs');
        $this->db = $this->jobTable->getAdapter();
    }

    /**
     * insert operation of log , what done in system
     *
     * @param  string|integer $job_id   OPTIONAL Currency value
     * @param string $key  but not in use for now
     * @return array         $modified modifieed value
     */

    public function isJobExists($job_id,$key="*")
    {
        if(empty($job_id))
            return false;

        $select = $this->db->select()->where('job_id=?',$job_id)->from('jobs');
        $stmt = $select->query();
        $info = $stmt->fetchObject();

        return $info;

    }


    /**
     * insert operation of log , what done in system
     *
     * @param  string|integer $job_id   OPTIONAL Currency value
     * @param boolean $cache  true/false
     * @return array         job array from cache or fresh one
     */
    public function getJobInfo($job_id,$cache = true)
    {
        if(empty($job_id))
            return false;


        if($cache==true && !empty($this->get_job_info[$job_id]))
            return $this->get_job_info[$job_id];


        $select = $this->db->select()->where('job_id=?',$job_id)->from('jobs');
        $stmt = $select->query();
        $info = $stmt->fetchObject();

        $this->get_job_info[$job_id] = $info;
        return $info;

    }

    /**
     * insert operation of log , what done in system
     *
     * @param  string|integer $job_id   OPTIONAL Currency value
     * @param array $update_data_temp data to be update in system
     * @return array job array from cache or fresh one
     */
    public function updateJob($job_id,$update_data_temp)
    {
        if(empty($job_id))
            return false;

        $update_data = $update_data_temp;

        $last_modified_date = date('Y-m-d H:i:s');
        $last_modified_by = Zend_Auth::getInstance()->getIdentity()->userid;

        $update_data['last_modified_by'] = $last_modified_by;
        $update_data['last_modified_date'] = $last_modified_date;
        $update_data['modified'] = 'Yes';

        $where = array();
        $where['job_id = ?'] = $job_id;
        $n = $this->db->update('jobs', $update_data,$where);

        return $n;

    }







    /*---------------------------------_Old Code----------------------*/
    public function getSpecialNeeds(){
        $sql="select * from special_needs";
        $res = $this->db->query($sql);
        return $res->fetchAll();
    }

    public function getPreferences($job_id)
    {

        $ret = array();

        $sql = "(SELECT g.*,m.* FROM `preference_group` g join `preference_master` m join `job_preference` s
						on(g.group_id=m.group_id and s.prefer_id=m.prefer_id)
						where job_id=$job_id ) union (SELECT *
FROM `preference_group` g
JOIN `preference_master` m ON ( g.group_id = m.group_id ) where for_manage_sitter=1)";

        $res = $this->db->query($sql);

        $prefers = $res->fetchAll();

        foreach ($prefers as $p) {
            $ret[$p['group_id']]['prefer'][$p['prefer_id']] = $p;
            if (isset($ret[$p['group_id']]))
                $ret[$p['group_id']]['label'] = $p['label'];
        }
        return $ret;
    }

    public function getChildren($job_id)
    {
        $job_id = $this->db->quote($job_id);
        $r = array();
        $sql = "select * from jobs_to_childs where job_id =$job_id ";

        $res = $this->db->query($sql);

        $result = $res->fetchAll();
        foreach ($result as $res) {
            $r[] = $res['child_id'];
        }
        return $r;
    }

    public function create(array $data, $parent_id)
    {

        //print_r($data);die;

        $start_date = $data['start_date'];
        $end_date = $data['end_date'];
        if ($start_date != '' && $end_date != '') {
            $days = (strtotime(date('Y-m-d', strtotime($end_date))) - strtotime(date('y-m-d', strtotime($start_date)))) / (24 * 60 * 60);

            $required_credits = $days;
        }//else $required_credits =1;

        $required_credits++;

        $start = strtotime($data['start_date']);

        $end_date = date('Y-m-d', $start);
        $end_time = date('H:i:s', strtotime($data['end_date']));

        $end = strtotime($end_date . ' ' . $end_time);
        //$end =strtotime($data['end_date']);
        $insertMaster = array('actual_start_date' => date('Y-m-d H:i:s', $start), 'actual_end_date' => date('Y-m-d H:i:s', strtotime($data['end_date'])));
        $master = new Zend_Db_Table('job_master');
        $master->insert($insertMaster);
        $master_job_id = $this->db->lastInsertId();
        for ($i = 1; $i <= $required_credits; $i++) {

            $sql = "select * from clients_subscription where end_date > now() and userid={$data['userid']} and slots>0 order by client_sub_id asc limit 0,1";
            $res = $this->db->query($sql);
            $result = $res->fetchAll();
            $result = $result[0];

            if (!empty($data['special_need'])) {
                $data['is_special'] = '1';
            } else {
                $data['is_special'] = '0';
            }

            //added for child count
            if ($data['actual_child_count'] > 4) {
                $data['is_special'] = '1';
            }

            if ($data['actual_child_count'] > 4) {
                $data['is_special'] = '1';
            }

            if (is_array($data['prefer'])) {
                $sql = 'select prefer_name from preference_master join preference_group on preference_group.group_id=preference_master.group_id where preference_group.group_id=5 and prefer_id in (' . implode(',', $data['prefer']) . ')';

                $prefer_name = $this->db->query($sql);
                $pf = $prefer_name->fetchAll();
                if (count($pf) > 0) {
                    $data['is_special'] = '1';
                }
            }

            /* if (is_array($data['children'])) {
                $sql = "select child_id from children where special_needs_status='Yes' and child_id in (" . implode(",", $data['children']) . ")";

                $prefer_name = $this->db->query($sql);
                $pf = $prefer_name->fetchAll();
                if (count($pf) > 0) {
                    $data['is_special'] = '1';
                }
            } */

            $insert = array(
                'master_job_id' => $master_job_id,
                'client_user_id' => $data['userid'],
                'jobs_posted_date' => date('Y-m-d H:i:s'),
                'job_start_date' => date('Y-m-d H:i:s', $start),
                'job_end_date' => date('Y-m-d H:i:s', $end),
                'sitter_user_id' => $data['sitter_id'],
                'job_added_by' => Zend_Auth::getInstance()->getIdentity()->userid,
                'last_modified_date' => date('Y-m-d H:i:s'),
                'last_modified_by' => Zend_Auth::getInstance()->getIdentity()->userid,
                'job_status' => $data['job_status'],
                'notes' => $data['notes'],
                'address_id' => $data['address_id'],
                'subs_id' => $result['client_sub_id'],
                'credits_used' => 1,
                'sitter_rate_pre' => $data['sitter_rate_pre'],
                'rate' => $data['rate'],
                'client_rate' => $data['client_rate'],
                'client_updated_rate' => $data['client_updated_rate'],
                'child_count' => $data['child_count'],
                'actual_child_count' => $data['actual_child_count'],
                'is_special' => $data['is_special'],
                'special_need' => $data['special_need']
            );
            // print_r($insert);die;


            if (empty($insert['job_added_by'])) {
                $insert['job_added_by'] = $data['userid'];
                $insert['last_modified_by'] = $data['userid'];
            }


            if ((isset($data['x_admin'])) && (!empty($data['x_admin']))) {
                $insert['job_added_by'] = $data['x_admin'];
                $insert['last_modified_by'] = $data['x_admin'];
            }

            $this->jobTable->insert($insert);
            $this->job_id = $this->db->lastInsertId();

            if ($data['is_special'] == '1') {
                $data_log = array('job_id' => $this->job_id,
                    'modified_by' => $insert['job_added_by'],
                    'modified_date' => date('Y-m-d H:i:s'),
                    'modification' => 'Special Condition',
                    'initial' => 0,
                    'modified' => 1
                );
                $this->db->insert('job_logs', $data_log);
            }

            //query if a job not special requset
            //var_dump($data['is_special']);die;
            if ($data['is_special'] == "0") {


                $url = SITE_URL . 'misc/assignallsitters';

                //print_r($url);die;
                $post = array(
                    'job_id' => $this->job_id,
                    'userid' => $data['userid'],
                    'jobno' => $i
                );

                ignore_user_abort(true);
                set_time_limit(0);

                $c = curl_init();
                curl_setopt($c, CURLOPT_URL, $url);
                curl_setopt($c, CURLOPT_POST, 1);
                curl_setopt($c, CURLOPT_POSTFIELDS, $post);
                curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);  // Follow the redirects (needed for mod_rewrite)
                curl_setopt($c, CURLOPT_RETURNTRANSFER, true);  // Return from curl_exec rather than echoing
                curl_setopt($c, CURLOPT_FRESH_CONNECT, true);   // Always ensure the connection is fresh
                // Timeout super fast once connected, so it goes into async.
                curl_setopt($c, CURLOPT_TIMEOUT, 1);

                $output = curl_exec($c);
                curl_close($c);
                //call using curl
                // $this->assign_sitters($this->job_id, $insert['job_added_by'],1);
            }


            $this->db->query("update clients_subscription set slots =slots-1 where client_sub_id = {$result['client_sub_id']}");

            $this->addChilds($this->job_id, $data['children']);
            $this->updateToClientRequests($data['userid']);
            $this->updateCredits($data['userid']);
            if (is_array($data['prefer'])) {
                $this->addPreference($data['prefer'], $this->job_id);
            }
            $start = $start + (60 * 60 * 24);
            $end = strtotime(date('Y-m-d', $start) . ' ' . $end_time);
            //$end =$end+(60*60*24);
            // Confirm job to client
            $this->jobInfo = $this->search(array('job_id' => $this->job_id));
            $this->jobInfo = $this->jobInfo['rows'][$this->job_id];
            $client = new Hb_Client();
            $userInfo = $clientInfo = $client->getDetail($data['userid']);
            //$this->hbSettings = new Hb_Settings();

            $this->jobMail($this->job_id, 'job_request_recieved', 'client', 'mail');
            if ($data['is_special'] == "1") {
                $this->jobMail($this->job_id, 'job_request_recieved_admin', ADMINMAIL, 'mail');
            }

        }
        return $this->job_id;
    }

    public function addChilds($job_id, $child_id = array())
    {
        $job_id = $this->db->quote($job_id);
        foreach ($child_id as $ch) {
            $ch = $this->db->quote($ch);
            $query[] = "($job_id,$ch)";
        }
        $values = implode(',', $query);

        $sql = "insert ignore into jobs_to_childs values $values";

        $res = $this->db->query($sql);
    }

    public function updateToClientRequests($userid)
    {
        $sql = "update clients_detail set open_requests=(select count(*) from jobs where client_user_id = $userid and job_status in('new','pending','confirmed')) where userid= $userid";
//die();
        $this->db->query($sql);
    }

    public function updateCredits($userid)
    {

        $this->client = new Hb_Client();
        $this->client->setClientSubscription($userid);
    }

    public function addPreference($prefer, $job_id)
    {
        $this->db->query("Delete from job_preference where job_id=$job_id ");

        foreach ($prefer as $ch) {
            $query[] = "($job_id,$ch)";
        }
        $values = implode(',', $query);

        $sql = "insert ignore into job_preference values $values";
        $res = $this->db->query($sql);
    }

    public function search($search = array(), $filter = array(), $sort = array())
    {

        //print_r($search);die;
        //$sort['page']=1;

        if ($search['job_id'] > 0)
            $query_jobId = ' and j.job_id=' . (int)$search['job_id'];

        if ($search['client_id'] > 0)
            $query_clientId = ' and client_user_id=' . $search['client_id'];


        if (is_array($search['status'])) {
            $query_status = ' and j.job_status in("' . implode('","', $search['status']) . '")';
        } else if (isset($search['status']) && $search['status'] != 'all')
            $query_status = ' and j.job_status="' . $search['status'] . '"';


        /* --------------------added by namrata for the immidiate jobs-------------------- */
        if (isset($search['status']) && $search['status'] == 'immidiate') {
            //$query_status="namrata";

            $immidiate_status = ' and TIMEDIFF(j.job_start_date,j.jobs_posted_date) <= "3" and TIMEDIFF(j.job_start_date,j.jobs_posted_date) > "0" ';
            //  $query_status = ' and j.job_status="new"';
            if (isset($search['jobtype']) && $search['jobtype'] == 'special') {

                $query_status = ' and j.job_status="new"';
            } else {
                $query_status = ' and j.job_status="pending"';
            }
        }
        /* ------------------------------------------------------------------------------------------ */

        /* --------------------added by namrata for the  jobs alert-------------------- */
        if (isset($search['status']) && $search['status'] == 'alert') {
            $alert_date = date("Y-m-d H:i:s", strtotime('+' . Alert . 'hours'));

            $current_date = date("Y-m-d H:i:s");
            $immidiate_status = ' and j.job_start_date <= "' . $alert_date . '" and j.job_start_date>="' . $current_date . '"';
            $query_status = ' and j.job_status!="cancelled" and j.job_status!="closed"';
            //print_r($current_date);die;
        }
        /* -------------------------------------------------------- */

        $sort['rows'] = (int)$search['rows'] == 0 ? 10 : (int)$search['rows'];

        $sort['page'] = (int)$search['page'] == 0 ? 1 : (int)$search['page'];

        $filter['sort'] = (int)$search['sort'] == 0 ? 1 : $search['sort'];

        $orderby = array('key' => (in_array($search['key'], array('j.job_id', 'client_user_id', 'jobs_posted_date', 'job_start_date', 'job_end_date', 'sitter_user_id', 'job_added_by', 'last_modified_date')) ? $search['key'] : 'j.job_start_date'), 'odr' => (in_array($search['odr'], array('asc', 'desc')) ? $search['odr'] : 'desc'));
        if ($search['key'] == '' && in_array($search['status'], array('completed'))) {
            $orderby['key'] = 'completed_date';
            $orderby['odr'] = 'desc';
        }
        //print_r($search);die;
        if ($sort['page'] > 1) {
            $start = ($sort['page'] - 1) * $sort['rows'];
        } else
            if ($sort['page'] == 1 or $sort['page'] == 0) {
                $start = 0;
            }
        if (!empty($search['job_id'])) {
            $start = 0;
        }

        $search_query[] = trim($search['client']) != '' ? '  u.username like "%' . $search['client'] . '%"' : '';
        $search_query[] = trim($search['client']) != '' ? ' u.firstname like "%' . $search['client'] . '%"' : '';
        $search_query[] = trim($search['client']) != '' ? ' u.lastname like "%' . $search['client'] . '%"' : '';

        if (trim($search['job_start_date']) != '' && trim($search['job_end_date']) == '') {
            $search['job_start_date'] = str_replace("-", "/", $search['job_start_date']);

            //$search_query[] = ' date(jm.actual_start_date)  = date("' . date('Y-m-d', strtotime($search['job_start_date'])) . '")';

            $search_query[] = ' date(j.job_start_date)  = date("' . date('Y-m-d', strtotime($search['job_start_date'])) . '")';
        } else if (trim($search['job_start_date']) == '' && trim($search['job_end_date']) != '') {
            $search['job_end_date'] = str_replace("-", "/", $search['job_end_date']);

            //$search_query[] = ' date(jm.actual_end_date)  = date("' . date('Y-m-d', strtotime($search['job_end_date'])) . '")';

            $search_query[] = ' date(j.job_end_date)  = date("' . date('Y-m-d', strtotime($search['job_end_date'])) . '")';
        } // else '';
        else if (trim($search['job_start_date']) != '' && trim($search['job_end_date']) != '') {
            $search['job_start_date'] = str_replace("-", "/", $search['job_start_date']);
            $search['job_end_date'] = str_replace("-", "/", $search['job_end_date']);

            //$search_query[] = ' date(jm.actual_start_date)  between  date("' . date('Y-m-d', strtotime($search['job_start_date'])) . '") and date("' . date('Y-m-d', strtotime($search['job_end_date'])) . '")';

            $search_query[] = ' date(j.job_start_date)  between  date("' . date('Y-m-d', strtotime($search['job_start_date'])) . '") and date("' . date('Y-m-d', strtotime($search['job_end_date'])) . '")';
        }
        // else '';


        $search_query = array_diff($search_query, array(''));
        if (!empty($search_query)) {
            $search_query = ' and (' . implode(' or ', $search_query) . ' )';
        } else
            $search_query = '';

        if (in_array($search['status'], array('completed', 'confirmed'))) {

        }
        if (isset($search['sitter_id']) && $search['sitter_id'] != '') {
            $query_sitterId = ' and j.sitter_user_id = ' . $search['sitter_id'];
        }
        //added after job differences
        if (in_array($search['status'], array('new'))) {
            $special_query = ' and j.is_special = ' . "'1'";
        }


        /* -------added by namrta for handling immidiate jobs----------------- */
        if (isset($search['jobtype']) && $search['jobtype'] == 'special') {
            $special_query = ' and j.is_special = ' . "'1'";
        }
        if (isset($search['jobtype']) && $search['jobtype'] == 'simple') {
            $special_query = ' and j.is_special = ' . "'0'";
        }

        /* -------------------------------------------------------- */

        $sql = "select TIMESTAMPDIFF(HOUR,j.jobs_posted_date,j.job_start_date) as hours,TIMESTAMPDIFF(HOUR,j.job_start_date,j.completed_date) as job_hour, j.*,u.*,jm.* $query_sitter_user_select from jobs j join job_master jm join users u $query_sitter_user
				on(j.client_user_id=u.userid and j.master_job_id = jm.master_job_id $query_sitter_user_on )
				where u.status!='deleted' $query_jobId  $query_clientId $query_status $query_sitterId $search_query $special_query $immidiate_status order by {$orderby['key']} {$orderby['odr']} LIMIT  $start,{$sort['rows']}";

        //print_r($sql);die;
        $res = $this->db->query($sql);
        $results = $res->fetchAll();

        // print_r($sql);die;

        $this->sitter = new Hb_Sitter();

        foreach ($results as $res) {
            if (!isset($data[$res['job_id']]))
                $data[$res['job_id']] = $res;
            $data[$res['job_id']]['children'] = $this->getChildrenDetails($res['job_id']); //[$res['child_id']]=$res;
            if ($res['sitter_user_id'] != null) {
                $sitter = $this->sitter->getDetail($res['sitter_user_id']);
                $data[$res['job_id']]['sitter_firstname'] = $sitter['firstname'];
                $data[$res['job_id']]['sitter_lastname'] = $sitter['lastname'];
                $data[$res['job_id']]['sitter_id'] = $sitter['userid'];
            } else {
                $data[$res['job_id']]['sitter_firstname'] = '-';
                $data[$res['job_id']]['sitter_lastname'] = '';
                $data[$res['job_id']]['sitter_id'] = 0;
            }
        }
        $sql = "select count(*) as total from jobs j join job_master jm join users u $query_sitter_user
				on(j.client_user_id=u.userid and j.master_job_id = jm.master_job_id $query_sitter_user_on )
				where u.status!='deleted' $query_jobId  $query_clientId $query_status $query_sitterId $search_query $special_query $immidiate_status";
        $res = $this->db->query($sql);

        $total = $res->fetchAll();
        $results = array('total' => $total[0]['total'], 'rows' => $data);
        return $results;

        //return $data;
    }

    public function getChildrenDetails($job_id)
    {

        $r = array();
        $sql = "select * from jobs_to_childs jc join children c on(c.child_id=jc.child_id)  where job_id =$job_id ";

        $res = $this->db->query($sql);

        $result = $res->fetchAll();
        foreach ($result as $res) {
            $r[$res['child_id']] = $res;
        }
        return $r;
    }

    public function jobMail($job_id, $mail_name, $to = 'mail', $from = 'mail', $sitter_id = false)
    {

        $client = new Hb_Client();
        $sitter = new Hb_Sitter();

        $this->jobInfo = $this->search(array('job_id' => $job_id));
        $this->jobInfo = $this->jobInfo['rows'][$job_id];


        $clientInfo = $client->getDetail($this->jobInfo['client_user_id']);
        if ($sitter_id)
            $sitterInfo = $sitter->getDetail($sitter_id);
        else if ($this->jobInfo['sitter_user_id'])
            $sitterInfo = $sitter->getDetail($this->jobInfo['sitter_user_id']);

        $this->hbSettings = new Hb_Settings();
        $mailTemplate = $this->hbSettings->getMailTemplates(array('mail_name' => $mail_name));
        $mailTemplate = $mailTemplate[0];
        $text = '';
        if ($from == 'mail')
            $from = $mailTemplate['from'];
        else if ($from == 'sitter')
            $from = $sitterInfo['username'];
        else if ($from == 'client')
            $from = $clientInfo['username'];

        if ($to == 'mail')
            $to = $mailTemplate['to'];
        else if ($to == 'client') {
            $to = $clientInfo['username'];
            $to_name = $clientInfo['firstname'] . ' ' . $clientInfo['lastname'];
        } else if ($to == 'sitter') {
            $to = $sitterInfo['username'];
            $to_name = $sitterInfo['firstname'] . ' ' . $sitterInfo['lastname'];
        }


        $cc = explode(',', $mailTemplate['cc']);
        $bcc = explode(',', $mailTemplate['bcc']);


        $clientReplace = $client->clientMailString($clientInfo);
        $sitterReplace = $sitter->sitterMailString($sitterInfo);

        $subject = str_replace(array_keys($sitterReplace), $sitterReplace, $mailTemplate['subject']);
        $subject = str_replace(array_keys($clientReplace), $clientReplace, $subject);


        $to_replace_job = $this->jobString($this->jobInfo);

        $text = str_ireplace('{viewlink}', SITE_URL . 'sitters/preview/job/' . $job_id, $mailTemplate['description']);
        $text = str_ireplace(array_keys($clientReplace), $clientReplace, $text);
        $address = $client->getAddress(array('userid' => $this->jobInfo['client_user_id'], 'address_id' => $this->jobInfo['address_id']));
        $address = $client->addressMailString($address[0]);
        $text = str_ireplace(array_keys($address), $address, $text);
        $text = str_ireplace(array_keys($sitterReplace), $sitterReplace, $text);
        $text = str_ireplace(array_keys($to_replace_job), $to_replace_job, $text);

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


        if (APPLICATION_ENV != 'development')
            $mail->send();
    }

    public function jobString($jobInfo)
    {

        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');


        $this->user = new Hb_User();
        foreach ($jobInfo['children'] as $child) {
            $c[] = ucwords(strtolower($child['child_name'])) . '(' . $viewRenderer->view->Age($child['dob']) . ')';
        }
        $children = implode(', ', $c);

        if ($jobInfo['client_updated_rate'] == null || empty($jobInfo['client_updated_rate'])) {
            $rate = $jobInfo['client_rate'];
        } else {
            $rate = $jobInfo['client_updated_rate'];
        }

        $t1 = StrToTime($jobInfo['completed_date']);
        $t2 = StrToTime($jobInfo['job_start_date']);
        $diff = $t1 - $t2;
        $hours = $diff / (60 * 60);

        $amount = $hours * $rate;
        $pamount = money_format('%.2n', $amount);

        return array(
            '{job_startdate}' => date(DATETIME_FORMAT, strtotime($jobInfo['job_start_date'])),
            '{job_enddate}' => date(DATETIME_FORMAT, strtotime($jobInfo['job_end_date'])),
            '{job_number}' => $jobInfo['job_id'],
            '{job_status}' => ucwords($jobInfo['job_status']),
            '{children}' => $children,
            '{Children}' => $children,
            '{job_completeddate}' => date(DATETIME_FORMAT, strtotime($jobInfo['completed_date'])),
            '{job_posteddate}' => date(DATETIME_FORMAT, strtotime($jobInfo['jobs_posted_date'])),
            '{job_modifieddate}' => date(DATETIME_FORMAT, strtotime($jobInfo['last_modified_date'])),
            '{job_notes}' => $jobInfo['notes'],
            '{job_totalassigned}' => $jobInfo['total_assigned'],
            '{job_totalpaid}' => '$' . $jobInfo['total_received'],
            '{job_totalpaybleamount}' => $pamount,
        );
    }

    public function update(array $data, $job_id)
    {
        $login_user = Zend_Auth::getInstance()->getIdentity()->userid;
        if (!empty($data['special_need'])) {
            $data['is_special'] = '1';
        } else {
            $data['is_special'] = '0';
        }

        if ($data['actual_child_count'] > 4) {
            $data['is_special'] = '1';
        }

        if (is_array($data['prefer'])) {
            $sql = 'select prefer_name from preference_master join preference_group on preference_group.group_id=preference_master.group_id where preference_group.group_id=5 and prefer_id in (' . implode(',', $data['prefer']) . ')';

            $prefer_name = $this->db->query($sql);
            $pf = $prefer_name->fetchAll();
            if (count($pf) > 0) {
                $data['is_special'] = '1';
            }
        }

        /* if (is_array($data['children'])) {
            $sql = "select child_id from children where special_needs_status='Yes' and child_id in (" . implode(",", $data['children']) . ")";

            $prefer_name = $this->db->query($sql);
            $pf = $prefer_name->fetchAll();
            if (count($pf) > 0) {
                $data['is_special'] = '1';
            }
        } */

        $update = array(
            'client_user_id' => $data['userid'],
            'jobs_posted_date' => date('Y-m-d H:i:s'),
            'job_start_date' => date('Y-m-d H:i:s', strtotime($data['start_date'])),
            //'job_end_date' => date('Y-m-d H:i:s', strtotime($data['end_date'])),
            'sitter_user_id' => $data['sitter_id'],
            'last_modified_date' => date('Y-m-d H:i:s'),
            'last_modified_by' => Zend_Auth::getInstance()->getIdentity()->userid,
            'job_status' => $data['job_status'],
            'notes' => $data['notes'],
            'address_id' => $data['address_id'],
            //by namrata for actual child count
            'actual_child_count' => $data['actual_child_count'],
            //'sitter_rate_pre' => $data['sitter_rate_pre'],
            //'rate' => $data['rate'],
            // 'client_rate' => $data['client_rate'],
            //'client_updated_rate' => $data['client_updated_rate'],
            'modified' => 'Yes',
            'is_special' => $data['is_special'],
            'special_need' => $data['special_need'],
        );

        if($data['job_status']=='completed')
        	$update['completed_date'] = date('Y-m-d H:i:s', strtotime($data['end_date']));
        else 
        	$update['job_end_date'] = date('Y-m-d H:i:s', strtotime($data['end_date']));

        if ($data['update'] == 1) {
            $update['sitter_rate_pre'] = $data['sitter_rate_pre'];
            $update['rate'] = $data['rate'];
            $update['client_rate'] = $data['client_rate'];
            $update['client_updated_rate'] = $data['client_updated_rate'];
        }

        // for job alert admin
        $pr_details = $this->searchnew(array('job_id' => $job_id));
        $pr_details = $pr_details['rows'][$job_id];

        $where = $this->jobTable->getAdapter()->quoteInto('job_id = ?', $job_id);
        $this->jobTable->update($update, $where);

        if ($pr_details['address_id'] != $update['address_id']) {
            $data_log = array('job_id' => $job_id,
                'modified_by' => $login_user,
                'modified_date' => date('Y-m-d H:i:s'),
                'modification' => 'Location Change',
                'initial' => $pr_details['address_id'],
                'modified' => $update['address_id']
            );

            $this->db->insert('job_logs', $data_log);
        }

        if ($pr_details['is_special'] != $update['is_special']) {
            if ($update['is_special'] == '1' || $update['is_special'] == 1) {
                $data_log = array('job_id' => $job_id,
                    'modified_by' => $login_user,
                    'modified_date' => date('Y-m-d H:i:s'),
                    'modification' => 'Special Condition',
                    'initial' => 0,
                    'modified' => 1
                );

                $this->db->insert('job_logs', $data_log);
            } else {
                $this->db->query("update job_logs set is_resolved=1 where modification='Special Condition' and job_id=" . $job_id);
            }
        }

        $pr_children = count($pr_details['children']);
        $up_chidren = count($data['children']);

        if ($pr_children != $up_chidren) {
            if ($pr_children > $up_chidren)
                $mod = "Removed Child";
            else
                $mod = "Added Child";
            $data_log = array('job_id' => $job_id,
                'modified_by' => $login_user,
                'modified_date' => date('Y-m-d H:i:s'),
                'modification' => $mod,
                'initial' => $pr_children,
                'modified' => $up_chidren
            );
            $this->db->insert('job_logs', $data_log);
        }

        if (strtotime($pr_details['job_start_date']) != strtotime($update['job_start_date']) || strtotime($pr_details['job_end_date']) != strtotime($update['job_end_date'])) {
            if (strtotime($pr_details['job_start_date']) != strtotime($update['job_start_date'])) {
                $inital_date = $pr_details['job_start_date'];
                $final_date = $update['job_start_date'];
            } else {
                $inital_date = $pr_details['job_end_date'];
                $final_date = ($update['job_end_date'])?$update['job_end_date']:$update['completed_date'];
            }
            $data_log = array('job_id' => $job_id,
                'modified_by' => $login_user,
                'modified_date' => date('Y-m-d H:i:s'),
                'modification' => 'Adjust Hours',
                'initial' => $inital_date,
                'modified' => $final_date
            );
            $this->db->insert('job_logs', $data_log);
        }

        $this->removeChilds($job_id);
        $this->addChilds($job_id, $data['children']);
        $this->updateToClientRequests($data['userid']);
        if (is_array($data['prefer'])) {
            $this->addPreference($data['prefer'], $job_id);
        }



        if (in_array($data['job_status'], array('pending', 'new'))) {
            $previously_special = $this->getSpecialStatus($job_id);
            if ($data['is_special'] == '1' && $previously_special != '1') {
                $url = SITE_URL . 'misc/removeallsitters';

                //print_r($url);die;
                $post = array(
                    'job_id' => $this->job_id,
                );

                ignore_user_abort(true);
                set_time_limit(0);

                $c = curl_init();
                curl_setopt($c, CURLOPT_URL, $url);
                curl_setopt($c, CURLOPT_POST, 1);
                curl_setopt($c, CURLOPT_POSTFIELDS, $post);
                curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);  // Follow the redirects (needed for mod_rewrite)
                curl_setopt($c, CURLOPT_RETURNTRANSFER, true);  // Return from curl_exec rather than echoing
                curl_setopt($c, CURLOPT_FRESH_CONNECT, true);   // Always ensure the connection is fresh
                // Timeout super fast once connected, so it goes into async.
                curl_setopt($c, CURLOPT_TIMEOUT, 1);

                $output = curl_exec($c);
                curl_close($c);
            } elseif ($data['is_special'] == '0' && $previously_special == '1') {
                $url = SITE_URL . 'misc/assignallsitters';

                //print_r($url);die;
                $post = array(
                    'job_id' => $this->job_id,
                    'userid' => $data['userid'],
                    'jobno' => 1
                );

                ignore_user_abort(true);
                set_time_limit(0);

                $c = curl_init();
                curl_setopt($c, CURLOPT_URL, $url);
                curl_setopt($c, CURLOPT_POST, 1);
                curl_setopt($c, CURLOPT_POSTFIELDS, $post);
                curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);  // Follow the redirects (needed for mod_rewrite)
                curl_setopt($c, CURLOPT_RETURNTRANSFER, true);  // Return from curl_exec rather than echoing
                curl_setopt($c, CURLOPT_FRESH_CONNECT, true);   // Always ensure the connection is fresh
                // Timeout super fast once connected, so it goes into async.
                curl_setopt($c, CURLOPT_TIMEOUT, 1);

                $output = curl_exec($c);
                curl_close($c);
            }

        }

        $this->jobMail($job_id, 'job_request_updated_to_client', 'client', 'mail');
        $this->jobMail($job_id, 'job_request_updated_to_admin', 'mail', 'client');
        if (!empty($data['sitter_id']) && $data['sitter_id'] != null) {
            //$this->jobMail($job_id, 'job_request_updated_to_sitter', 'mail', 'mail');
        }

    }

    public function searchnew($search = array(), $filter = array(), $sort = array())
    {


        //print_r($search);die;
        //$sort['page']=1;

        if ($search['job_id'] > 0)
            $query_jobId = ' and j.job_id=' . (int)$search['job_id'];

        if ($search['client_id'] > 0)
            $query_clientId = ' and client_user_id=' . $search['client_id'];


        if ($search['status'] != '') {
            if ($search['status'] == 'open') {
                $query_status = "and j.job_status in ('pending','new')";
            } else if ($search['status'] == 'scheduled') {
                $query_status = " and j.job_status ='confirmed' and ((date_format('" . date('Y-m-d H:i:s') . "', '%Y-%m-%d %H:%i' )  + INTERVAL 0 HOUR) not between (date_format( job_start_date, '%Y-%m-%d %H:%i' ) + INTERVAL -3 HOUR ) AND (date_format( job_end_date , '%Y-%m-%d %H:%i' )  + INTERVAL 3 HOUR))";
            } else if ($search['status'] == 'active') {
                $query_status = " and j.job_status ='confirmed' and ((date_format('" . date('Y-m-d H:i:s') . "', '%Y-%m-%d %H:%i' )  + INTERVAL 0 HOUR) between (date_format( job_start_date, '%Y-%m-%d %H:%i' ) + INTERVAL -3 HOUR ) AND (date_format( job_end_date , '%Y-%m-%d %H:%i' )  + INTERVAL 3 HOUR)) ";
            } else if ($search['status'] == 'completed') {
                $query_status = " and (j.job_status='completed' OR ( j.job_status ='cancelled' AND immidiate_cancelled ='yes' )) ";
            } else if ($search['status'] == 'closed') {
                $query_status = " and j.job_status ='closed' ";
            } else if ($search['status'] == 'cancelled') {
                $query_status = " and j.job_status ='cancelled' and j.immidiate_cancelled ='no' ";
            }
        }

        $sort['rows'] = (int)$search['rows'] == 0 ? 10 : (int)$search['rows'];

        $sort['page'] = (int)$search['page'] == 0 ? 1 : (int)$search['page'];

        $filter['sort'] = (int)$search['sort'] == 0 ? 1 : $search['sort'];

        $orderby = array('key' => (in_array($search['key'], array('j.job_id', 'is_special', 'completed_date', 'cancelled_date', 'client_user_id', 'jobs_posted_date', 'job_start_date', 'job_end_date', 'sitter_user_id', 'job_added_by', 'last_modified_date', 'client_name', 'sitter_name')) ? $search['key'] : 'j.job_start_date'), 'odr' => (in_array($search['odr'], array('asc', 'desc')) ? $search['odr'] : 'desc'));
        if ($search['key'] == '' && in_array($search['status'], array('completed'))) {
            $orderby['key'] = 'completed_date';
            $orderby['odr'] = 'desc';
        }

        if ($orderby['key'] == 'client_name') {
            $orderby['key'] = 'firstname ' . $orderby['odr'] . ', lastname';
        }

        if ($orderby['key'] == 'sitter_name') {
            $orderby['key'] = 'sitter_firstname ' . $orderby['odr'] . ', sitter_lastname';
        }

        //print_r($search);die;
        if ($sort['page'] > 1) {
            $start = ($sort['page'] - 1) * $sort['rows'];
        } else
            if ($sort['page'] == 1 or $sort['page'] == 0) {
                $start = 0;
            }
        if (!empty($search['job_id'])) {
            $start = 0;
        }

        $search_query[] = trim($search['client']) != '' ? '  u.username like "%' . $search['client'] . '%"' : '';
        $search_query[] = trim($search['client']) != '' ? ' u.firstname like "%' . $search['client'] . '%"' : '';
        $search_query[] = trim($search['client']) != '' ? ' u.lastname like "%' . $search['client'] . '%"' : '';

        $search_query[] = trim($search['sitter']) != '' ? ' s.sitter_firstname like "%' . $search['sitter'] . '%"' : '';
        $search_query[] = trim($search['sitter']) != '' ? ' s.sitter_lastname like "%' . $search['sitter'] . '%"' : '';

        if (trim($search['job_start_date']) != '' && trim($search['job_end_date']) == '') {
            $search['job_start_date'] = str_replace("-", "/", $search['job_start_date']);

            //$search_query[] = ' date(jm.actual_start_date)  = date("' . date('Y-m-d', strtotime($search['job_start_date'])) . '")';

            $search_query[] = ' date(j.job_start_date)  = date("' . date('Y-m-d', strtotime($search['job_start_date'])) . '")';
        } else if (trim($search['job_start_date']) == '' && trim($search['job_end_date']) != '') {
            $search['job_end_date'] = str_replace("-", "/", $search['job_end_date']);

            //$search_query[] = ' date(jm.actual_end_date)  = date("' . date('Y-m-d', strtotime($search['job_end_date'])) . '")';

            $search_query[] = ' date(j.job_end_date)  = date("' . date('Y-m-d', strtotime($search['job_end_date'])) . '")';
        } // else '';
        else if (trim($search['job_start_date']) != '' && trim($search['job_end_date']) != '') {
            $search['job_start_date'] = str_replace("-", "/", $search['job_start_date']);
            $search['job_end_date'] = str_replace("-", "/", $search['job_end_date']);

            //$search_query[] = ' date(jm.actual_start_date)  between  date("' . date('Y-m-d', strtotime($search['job_start_date'])) . '") and date("' . date('Y-m-d', strtotime($search['job_end_date'])) . '")';

            $search_query[] = ' date(j.job_start_date)  between  date("' . date('Y-m-d', strtotime($search['job_start_date'])) . '") and date("' . date('Y-m-d', strtotime($search['job_end_date'])) . '")';
        }
        // else '';


        $search_query = array_diff($search_query, array(''));
        if (!empty($search_query)) {
            $search_query = ' and (' . implode(' or ', $search_query) . ' )';
        } else
            $search_query = '';

        if (in_array($search['status'], array('completed', 'confirmed'))) {

        }
        if (isset($search['sitter_id']) && $search['sitter_id'] != '') {
            $query_sitterId = ' and j.sitter_user_id = ' . $search['sitter_id'];
        }

        /* -------------------------------------------------------- */
        $sql = "select u.status as client_status,ifnull(s.sitter_firstname,'') sitter_firstname,ifnull(s.sitter_lastname,'') sitter_lastname,ifnull(s.sitter_id,'') sitter_id, TIMESTAMPDIFF(HOUR,j.jobs_posted_date,j.job_start_date) as hours,
	TIMESTAMPDIFF(HOUR,j.job_start_date,j.completed_date) as job_hour, j.*,u.*,jm.*
	from jobs j left join (select users.userid as sitter_id, firstname sitter_firstname, lastname sitter_lastname from users where usertype='S') as s on j.sitter_user_id=s.sitter_id
	join job_master jm join users u on(j.client_user_id=u.userid and j.master_job_id = jm.master_job_id  )
	where 1 $query_jobId $query_clientId $query_status $query_sitterId $search_query  order by {$orderby['key']} {$orderby['odr']} LIMIT  $start,{$sort['rows']}";

//print_r($sql);die;
        $res = $this->db->query($sql);
        $results = $res->fetchAll();

        $this->sitter = new Hb_Sitter();

        foreach ($results as $res) {
            if (!isset($data[$res['job_id']]))
                $data[$res['job_id']] = $res;

            $js = $this->newjobstatus($res);
            $data[$res['job_id']]['alerts'] = $this->getAlerts($res['job_id']);
            $data[$res['job_id']]['jstatus'] = $js[1];
            $data[$res['job_id']]['children'] = $this->getChildrenDetails($res['job_id']); //[$res['child_id']]=$res;
        }
        $sql = "select count(*) as total from jobs j left join (select userid as sitter_id,firstname sitter_firstname, lastname sitter_lastname from users where usertype='S') as s on j.sitter_user_id=s.sitter_id join job_master jm join users u $query_sitter_user
	on(j.client_user_id=u.userid and j.master_job_id = jm.master_job_id $query_sitter_user_on )
	where 1 $query_jobId  $query_clientId $query_status $query_sitterId $search_query ";

        $res = $this->db->query($sql);

        $total = $res->fetchAll();
        $results = array('total' => $total[0]['total'], 'rows' => $data);
        return $results;

        //return $data;

    }

    function newjobstatus($job)
    {
        $status = array();

        if ($job['job_status'] == 'new' || $job['job_status'] == 'pending') {
            $status[0] = 'O';
            $status[1] = 'Open';
            return $status;
        } else if ($job['job_status'] == 'confirmed') {
            $lowerlimit1 = strtotime('-3 hours', strtotime($job['job_start_date']));
            $upperLimit1 = strtotime('+3 hours', strtotime($job['job_end_date']));
            if ($lowerlimit1 <= time() && time() <= $upperLimit1) {
                $status[0] = 'A';
                $status[1] = 'Active';
            } else {
                $status[0] = 'S';
                $status[1] = 'Scheduled';
            }
            return $status;
        } elseif (($job['job_status'] == 'cancelled' && $job['immidiate_cancelled'] == 'yes') || $job['job_status'] == 'completed') {
            $status[0] = 'C';
            $status[1] = 'Completed';
            return $status;
        } elseif ($job['job_status'] == 'cancelled' && $job['immidiate_cancelled'] == 'no') {
            $status[0] = 'CL';
            $status[1] = 'Cancelled';
            return $status;
        } elseif ($job['job_status'] == 'closed') {
            $status[0] = 'CO';
            $status[1] = 'Closed';
            return $status;
        } else {
            $status[0] = 'CI';
            $status[1] = 'Client Dispute';
            return $status;
        }
    }

    public function getAlerts($job_id)
    {
        $sql = "select count(*) as alert from job_logs where job_id=" . $job_id;
        $res = $this->db->query($sql);
        $total = $res->fetchAll();
        return $total[0]['total'];
    }

    public function removeChilds($job_id, $child_id = array())
    {

        foreach ($child_id as $ch) {
            $query[] = $child_id;
        }
        if (!empty($query)) {
            $values = ' and child_id in (' . implode(',', $query) . ')';
        }
        $sql = "delete from jobs_to_childs where job_id = $job_id $values";
        $res = $this->db->query($sql);
    }

    public function getSpecialStatus($job_id)
    {
        $sql = "select is_special from jobs where job_id=" . $job_id;
        $res = $this->db->query($sql);
        $total = $res->fetchAll();
        //print_r($total);die;
        return $total[0]['is_special'];
    }

    public function calculateCredits($userid, $start_date, $end_date, $job_id = 0)
    {
        $this->credits = $this->checkCredits($userid);
//var_dump($this->_request->getParam('action'));
        $required_credits = 0;
        $this->calculatedCredits = $this->checkCredits($userid, $start_date, $end_date);
        $days = (strtotime($end_date) - strtotime($start_date)) / (24 * 60 * 60);
//var_dump($days);
        $required_credits = round($days);
//if($required_credits==0)
        if ($job_id > 0)
            ++$this->calculatedCredits;
        $required_credits++;
        return $creditsArray = array('available_credits' => $this->credits, 'calculated_credits' => $this->calculatedCredits, 'required_credits' => $required_credits);
    }

    public function checkCredits($userid, $start_date = '', $end_date = '')
    {
        $required_credits = 0;
        if ($start_date != '' && $end_date != '') {
            $days = (strtotime(date('Y-m-d', strtotime($end_date))) - strtotime(date('y-m-d', strtotime($start_date)))) / (24 * 60 * 60);

            $required_credits = $days;
        }//else $required_credits =1;
//if($required_credits==0)
        $required_credits++;

        $sql = "select sum(slots) as credits from clients_subscription where end_date > now()  and userid=$userid group by userid having(sum(slots)>=$required_credits) ";

        $res = $this->db->query($sql);
        $result = $res->fetchAll();
        return (int)$result[0]['credits'];
    }

    public function delete($job_id)
    {
        $job = $this->search(array('job_id' => $job_id));
        $job = $job['rows'][$job_id];
 	$userType = Zend_Auth::getInstance()->getIdentity()->usertype;
        if ($userType != 'A') {

            $data_log = array('job_id' => $job_id,
                'modified_by' => Zend_Auth::getInstance()->getIdentity()->userid,
                'modified_date' => date('Y-m-d H:i:s'),
                'modification' => 'Client Cancellation',
                'initial' => 0,
                'modified' => 0
            );
            $this->db->insert('job_logs', $data_log);

            $this->jobMail($job_id, 'job_request_cancelled', 'client', 'mail');
            $this->jobMail($job_id, 'job_request_cancelled', 'sitter', 'mail');
        }
        if ($job['job_status'] == 'confirmed') {
        	if ((strtotime($job['job_start_date']) - time()) < 86400 && $userType != 'A') {
            	$update = array('job_status' => 'cancelled', 'cancelled_date' => date('Y-m-d H:i:s'), 'immidiate_cancelled'=>'yes');
        	}
        	else 
        		$update = array('job_status' => 'cancelled', 'cancelled_date' => date('Y-m-d H:i:s'));
            $where = $this->jobTable->getAdapter()->quoteInto('job_id = ?', $job_id);
            $this->jobTable->update($update, $where);
        } else {//if($job['job_status']=='pending'){
            if ((strtotime($job['job_start_date']) - time()) < 86400) {
                $update = array('job_status' => 'cancelled', 'cancelled_date' => date('Y-m-d H:i:s'));
   
                if($job['job_status']=='cancelled' && $userType=='A')
                	$update['immidiate_cancelled']='no';
                
                $where = $this->jobTable->getAdapter()->quoteInto('job_id = ?', $job_id);
                $this->jobTable->update($update, $where);
            } else {

                $client = new Hb_Client();
                $client->reimburse($job_id);
                $this->updateToClientRequests($job['client_user_id']);
                $where = $this->jobTable->getAdapter()->quoteInto('job_id = ?', $job_id);
                $this->jobTable->delete($where);
            }
        }
    }

    public function sendToSitters(array $jobs, array $sitters)
    {

        $this->jobSentTable = new Zend_Db_Table('job_sent');


        foreach ($jobs as $job_id) {
            $this->jobInfo = $this->search(array('job_id' => $job_id));
            $this->jobInfo = $this->jobInfo['rows'][$job_id];

            foreach ($sitters as $sitter_id) {
                $insert = array(
                    'job_id' => $job_id,
                    'sent_to' => $sitter_id,
                    'sent_by' => Zend_Auth::getInstance()->getIdentity()->userid,
                    'sent_date' => date('Y-m-d H:i:s'),
                    'sent_status' => 'new'
                );
                $sql = "select * from job_sent where job_id=$job_id and sent_to =$sitter_id";
                $res = $this->db->query($sql);
                $flag = $res->fetchAll();
                if (empty($flag)) {
                    $this->jobSentTable->insert($insert);
                } else {
                    $this->db->query("update job_sent set sent_by=" . Zend_Auth::getInstance()->getIdentity()->userid . " and sent_date=now() and sent_status='new'");
                }

                // Confirm job to admin
                $this->jobMail($job_id, 'send_to_sitter', 'sitter', 'mail', $sitter_id);
            }
            $update = array('job_status' => 'pending');
            $where = $this->jobTable->getAdapter()->quoteInto('job_id = ?', $job_id);
            $this->jobTable->update($update, $where);
            $this->sitter = new Hb_Sitter();
            $this->sitter->setAvailabileJobs();
//$this->sitter->setAvailabileJobs();
        }
        $this->db->query('update jobs set total_assigned = (select count(*) from job_sent where job_id =' . $job_id . ') where job_id =' . $job_id);
    }

    public function removeSendToSitters(array $jobs, array $sitters)
    {

        $this->jobSentTable = new Zend_Db_Table('job_sent');


        foreach ($jobs as $job_id) {
            foreach ($sitters as $sitter_id) {

                $this->db->query("delete from job_sent where job_id=$job_id and sent_to=$sitter_id");
            }
            $sql = "select * from job_sent where job_id=$job_id";
            $res = $this->db->query($sql);
            $flag = $res->fetchAll();
            if (!empty($flag))
                $update = array('job_status' => 'pending');
            else
                $update = array('job_status' => 'new');
            $where = $this->jobTable->getAdapter()->quoteInto('job_id = ?', $job_id);
            $this->jobTable->update($update, $where);
            $this->sitter = new Hb_Sitter();
            $this->sitter->setAvailabileJobs();
//$this->sitter->setAvailabileJobs();
        }
        $this->db->query('update jobs set total_assigned = (select count(*) from job_sent where job_id =' . $job_id . ') where job_id =' . $job_id);
    }

    public function getJobsSentSitters($job_id)
    {
        $res = $this->db->query("select * from job_sent join users u on(u.userid=sent_to) where job_id=$job_id");

        $results = $res->fetchAll();
        foreach ($results as $job) {
            $data[$job['sent_to']] = $job;
        }
        return $data;
    }

    public function confirmJob($job_id, $sitter_id, $assigned_by = "")
    {
    	
    	
    	if(empty($sitter_id))
    	{
    		return false;
    	}
    	
//echo "update jobs set job_status='confirmed', sitter_user_id=$sitter_id where job_id = $job_id";die();
        $this->db->query("update jobs set job_status='confirmed', sitter_user_id=$sitter_id where job_id = $job_id");
        $this->db->query("update job_sent set sent_status ='locked' where job_id=$job_id ");
        $this->db->query("update job_sent set sent_status ='accepted' where job_id=$job_id  and sent_to=$sitter_id ");
        $sitter = new Hb_Sitter();
        
       
        //$sitter->setConfirmedJobs(); //removed for optimizing by anjali
        $this->db->query("UPDATE sitters s SET confirmed_jobs = confirmed_jobs+1 where s.userid= $sitter_id "); //added for optimizing
        
        $sitter->setAvailabileJobs();

        $this->jobInfo = $this->search(array('job_id' => $job_id));
        $this->jobInfo = $this->jobInfo['rows'][$job_id];

//to mark alert resolved in job log
        $this->db->query("update job_logs set is_resolved=1 where job_id=$job_id and modification='Sitter Cancellation'");
// Confirm job to admin
// Confirm job to sitter


        $this->jobMail($job_id, 'jobs_confirmed_to_client', 'sitter', 'mail');

        $this->jobMail($job_id, 'jobs_confirmed_to_client', 'client', 'mail');
//$this->jobMail($job_id,'jobs_confirmed_to_admin','mail','sitter');
//code to select device token and send push nitifications


        return true;
    }


//by namrata

    public function cancelConfirm($job_id,$sitter_id)
    {

        $data_log = array('job_id' => $job_id,
            'modified_by' => Zend_Auth::getInstance()->getIdentity()->userid,
            'modified_date' => date('Y-m-d H:i:s'),
            'modification' => 'Sitter Cancellation',
            'initial' => 0,
            'modified' => 0
        );
        $this->db->insert('job_logs', $data_log);

//$this->jobMail($job_id, 'confirmed_job_cancelled_to_client', 'client', 'mail');

        $this->db->query("update jobs set job_status='pending', sitter_user_id=null where job_id = $job_id");
        $this->db->query("update job_sent set sent_status ='new' where job_id=$job_id ");
//$this->db->query("update job_sent set sent_status ='new' where job_id=$job_id  and sent_to=$sitter_id ");
        $sitter = new Hb_Sitter();
        
        //$sitter->setConfirmedJobs(); //by anjali
        $this->db->query("UPDATE sitters s SET confirmed_jobs = confirmed_jobs-1 where s.userid=$sitter_id ");
        
        $sitter->setAvailabileJobs();

        return true;
    }

    public function cancelJob($job_id, $sitter_id)
    {

        $this->db->query("update jobs set job_status='cancelled', sitter_user_id=$sitter_id where job_id = $job_id");
        $this->db->query("update job_sent set sent_status ='locked' where job_id=$job_id ");
//$this->db->query("update job_sent set sent_status ='accepted' where job_id=$job_id  and sent_to=$sitter_id ");
        $sitter = new Hb_Sitter();
       
        // $sitter->setConfirmedJobs(); //by anjali
        $this->db->query("UPDATE sitters s SET confirmed_jobs = confirmed_jobs-1 where s.userid=$sitter_id ");
        
        $sitter->setAvailabileJobs();
//if(Zend_Auth::getInstance()->getIdentity()->usertype!='A')
        $this->jobMail($job_id, 'job_request_cancelled', 'client', 'mail');
        $job = $this->search(array('job_id' => $job_id));
        $job = $job['rows'][$job_id];
        if ($job['sitter_user_id'] > 0)
//if(Zend_Auth::getInstance()->getIdentity()->usertype!='A')
            $this->jobMail($job_id, 'job_cancelled_to_sitter', 'sitter', 'mail');
        return true;
    }

    /* ---------------------function to get billable jobs---------------- */

    public function completeJob($data)
    {

//to get the details of the job

        $job_id = $data['job_id'];
        $query = $this->db->query("select job_start_date,job_end_date,sitter_user_id from jobs where job_id=$job_id");
        $result = $query->fetchAll();

        if (!empty($result[0]['job_start_date'])) {
            $start_time = date('h:i A', strtotime($result[0]['job_start_date']));

            if ($data['job_start_date'] != '')
                $result[0]['job_start_date'] = date('Y-m-d', strtotime($result[0]['job_start_date'])) . " " . date('H:i:s', strtotime($data['job_start_date']));

            $start = strtotime($start_time);
            $end = strtotime($data['completed_date']);

            $data_log = array('job_id' => $job_id,
                'modified_by' => Zend_Auth::getInstance()->getIdentity()->userid,
                'modified_date' => date('Y-m-d H:i:s'),
                'modification' => 'Adjust Hours',
                'initial' => $result[0]['job_end_date'],
                'modified' => date('Y-m-d', strtotime($result[0]['job_start_date'])) . " " . date('H:i:s', strtotime($data['completed_date']))
            );
            $this->db->insert('job_logs', $data_log);

//to check if the job is started or not

            /* $job_start_date_time=$result[0]['job_start_date'];
              $current_date_time =date('Y-m-d H:i:s');


              $job_start_date_timestamp=strtotime($job_start_date_time);
              $current_date_timestamp=strtotime($current_date_time);

              if($current_date_timestamp < $job_start_date_timestamp)
              {
              $error = new Zend_Session_Namespace('error');
              $error->errormessage = "You can not complete the job before it get started  ";
              return false;
              } */


            $job_start_date = $result[0]['job_start_date'];

            if ($end - $start > 0) {

                $completed_dat = date('Y-m-d', strtotime($result[0]['job_start_date']));


                $completed_time = date(' H:i:s', $end);
                // $completed_dat=date('Y-m-d',$start);
                $completed_date = $completed_dat . "" . $completed_time;


                //print_r($completed_date);die;
            } else {
                $error = new Zend_Session_Namespace('error');
                $error->errormessage = "Job completed time can not be less than job start time for job $job_id ";

                return false;
            }
        }

        if ($data['sitter_id'] > 0) {
            $sitter_id = $data['sitter_id'];
            $job_id = $data['job_id'];
            $total_paid = (float)$data['total_paid'];
// $completed_date = date('Y-m-d H:i:s', strtotime($data['completed_date']));


            $sql = "update jobs set job_status='completed',job_start_date='$job_start_date', completed_date='$completed_date' , total_paid='$total_paid', sitter_user_id=$sitter_id where job_id = $job_id"; //die();
            $this->db->query($sql);
            $this->db->query('update sitters set earnings=(select sum(total_paid) from jobs where sitter_user_id="' . $sitter_id . '" ) where userid="' . $sitter_id . '"');
            $this->db->query("update job_sent set sent_status ='locked' where job_id=$job_id ");
//$this->db->query("update job_sent set sent_status ='accepted' where job_id=$job_id  and sent_to=$sitter_id ");
            $sitter = new Hb_Sitter();
// $sitter->setConfirmedJobs();//by namrata
//$sitter->setAvailabileJobs();
//  $sitter->setCompletedJobs();//by namrata


            $this->db->query("UPDATE sitters s SET confirmed_jobs=confirmed_jobs-1,completed_jobs=completed_jobs+1 where userid=" . $sitter_id);


            $this->jobMail($job_id, 'job_completed_to_client', 'client', 'sitter');
//device notifiucations to client


            return true;
        }
    }

    /* ----------------------------function to update client rate---------------------------------- */

    public function getSitterJobs($search = array())
    {

//       print_r($search);die;

        if (isset($search['status']))
            $query_status = ' and j.job_status in ("' . implode('","', $search['status']) . '")';
//print_r($search['status']);die;

        if (isset($search['status']) && $search['status'][0] == 'pending')
            $query_status = ' and j.job_status in ("' . implode('","', $search['status']) . '") and job_start_date >='."'".date('Y-m-d H:i:s')."'";

        if (isset($search['sitter_id'])) {
            $query_sitter = ' and js.sent_to=' . $search['sitter_id'];
        }
        $sort['rows'] = (int)$search['rows'] == 0 ? 10 : (int)$search['rows'];

        $sort['page'] = (int)$search['page'] == 0 ? 1 : (int)$search['page'];

        $filter['sort'] = (int)$search['sort'] == 0 ? 1 : $search['sort'];

        if ($sort['page'] > 1) {

            $start = ($sort['page'] - 1) * $sort['rows'];
        } else
            if ($sort['page'] == 1 or $sort['page'] == 0) {
                $start = 0;
            }


//to get count
        $sqlcount = "select SQL_CALC_FOUND_ROWS * from jobs j join users u join children c join jobs_to_childs jc join job_sent js
				on(j.client_user_id=u.userid and jc.job_id=j.job_id and jc.child_id=c.child_id and js.job_id=j.job_id)
				where u.status!='deleted'   $query_sitter $query_status  GROUP BY j.job_id";


// print_r($sqlcount);die;
        $result = $this->db->query($sqlcount);
        $results = $result->fetchAll();
        $count = count($results);


        $sql = "select SQL_CALC_FOUND_ROWS * from jobs j join users u join children c join jobs_to_childs jc join job_sent js
				on(j.client_user_id=u.userid and jc.job_id=j.job_id and jc.child_id=c.child_id and js.job_id=j.job_id)
				where u.status!='deleted'   $query_sitter $query_status  GROUP BY j.job_id  LIMIT $start,{$sort['rows']} ";


        $res = $this->db->query($sql);
        $results = $res->fetchAll();

        foreach ($results as $res) {
            if (!isset($data[$res['job_id']]))
                $data[$res['job_id']] = $res;
            $data[$res['job_id']]['children'][$res['child_id']] = $res;
        }
//        $res = $this->db->query('SELECT FOUND_ROWS() as total');
//
//        $total = $res->fetchAll();

        $results = array('total' => $count, 'rows' => $data);

        return $results;
        return $data;
    }

    /* ----------------------------function to update sitter rate---------------------------------- */

    public function saveRate($job_id, $rate)
    {
        $where = $this->jobTable->getAdapter()->quoteInto('job_id = ?', $job_id);
        $update = array('rate' => $rate);
        $this->jobTable->update($update, $where);
    }

    /**/

    public function jobChargeMail($job_id, $mail_name, $to = 'mail', $from = 'mail', $sitter_id = false, $additional = array())
    {

        $client = new Hb_Client();
        $sitter = new Hb_Sitter();

        $this->jobInfo = $this->search(array('job_id' => $job_id));
        $this->jobInfo = $this->jobInfo['rows'][$job_id];


//print_r($this->jobInfo);die;
        $clientInfo = $client->getDetail($this->jobInfo['client_user_id']);
        if ($sitter_id)
            $sitterInfo = $sitter->getDetail($sitter_id);
        else if ($this->jobInfo['sitter_user_id'])
            $sitterInfo = $sitter->getDetail($this->jobInfo['sitter_user_id']);

        $this->hbSettings = new Hb_Settings();
        $mailTemplate = $this->hbSettings->getMailTemplates(array('mail_name' => $mail_name));
        $mailTemplate = $mailTemplate[0];
        $text = '';
        if ($from == 'mail')
            $from = $mailTemplate['from'];
        else if ($from == 'sitter')
            $from = $sitterInfo['username'];
        else if ($from == 'client')
            $from = $clientInfo['username'];

        if ($to == 'mail')
            $to = $mailTemplate['to'];
        else if ($to == 'client') {
            $to = $clientInfo['username'];
            $to_name = $clientInfo['firstname'] . ' ' . $clientInfo['lastname'];
        } else if ($to == 'sitter') {
            $to = $sitterInfo['username'];
            $to_name = $sitterInfo['firstname'] . ' ' . $sitterInfo['lastname'];
        }


        $cc = explode(',', $mailTemplate['cc']);
        $bcc = explode(',', $mailTemplate['bcc']);


        $clientReplace = $client->clientMailString($clientInfo);
        $sitterReplace = $sitter->sitterMailString($sitterInfo);

        $subject = str_replace(array_keys($sitterReplace), $sitterReplace, $mailTemplate['subject']);
        $subject = str_replace(array_keys($clientReplace), $clientReplace, $subject);


        $to_replace_job = $this->jobString($this->jobInfo);

        $text = str_ireplace('{viewlink}', SITE_URL . 'sitters/preview/job/' . $job_id, $mailTemplate['description']);
        $text = str_ireplace(array_keys($clientReplace), $clientReplace, $text);
        $address = $client->getAddress(array('userid' => $this->jobInfo['client_user_id'], 'address_id' => $this->jobInfo['address_id']));
        $address = $client->addressMailString($address[0]);
        $text = str_ireplace(array_keys($address), $address, $text);
        $text = str_ireplace(array_keys($sitterReplace), $sitterReplace, $text);
        $text = str_ireplace(array_keys($to_replace_job), $to_replace_job, $text);

//by namrata
        if (!empty($additional)) {
            $text = str_ireplace(array_keys($additional), $additional, $text);
        }

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

//echo $to;
//echo $to_name;die();
        $mail->addTo($to, $to_name);
        $mail->setSubject($subject);


        if (APPLICATION_ENV != 'development')
            $mail->send();
    }

    public function billable_jobs($search = array(), $filter = array(), $sort = array())
    {
        $sort['rows'] = (int)$search['rows'] == 0 ? 10 : (int)$search['rows'];

        $sort['page'] = (int)$search['page'] == 0 ? 1 : (int)$search['page'];

        $filter['sort'] = (int)$search['sort'] == 0 ? 1 : $search['sort'];

//print_r($search);
        if ($sort['page'] > 1) {
            $start = ($sort['page'] - 1) * $sort['rows'];
        } else
            if ($sort['page'] == 1 or $sort['page'] == 0) {
                $start = 0;
            }

//added by anjali for sorting
        $orderby = array('key' => (in_array($search['key'], array('job_id', 'job_start_date', 'job_end_date', 'client_name', 'sitter_name')) ? $search['key'] : 'job_start_date'), 'odr' => (in_array($search['odr'], array('asc', 'desc')) ? $search['odr'] : 'desc'));

        if ($orderby['key'] == 'client_name') {
            $orderby['key'] = 'p.firstname ' . $orderby['odr'] . ', p.lastname';
        }

        if ($orderby['key'] == 'sitter_name') {
            $orderby['key'] = 's.firstname ' . $orderby['odr'] . ', s.lastname';
        }

//added client and sitter payment status field in select.
        $sql = "select p.firstname,p.lastname,s.firstname,s.lastname,actual_child_count,child_count,modified,TIMEDIFF(completed_date,job_start_date) as ac_hr,job_start_date,job_end_date,TIMEDIFF(job_end_date,job_start_date) as hr,client_rate,client_updated_rate,client_payment_status,sitter_payment_status from jobs

			left join users p on jobs.client_user_id=p.userid

			left join users s on jobs.sitter_user_id=s.userid

			where job_status='completed' and client_payment_status='unpaid'";

        $res = $this->db->query($sql);
        $total_rec = $res->fetchAll();

        $sql = "select client_user_id as userid ,sitter_user_id as sitterid,job_id,p.firstname as client_first_name,p.lastname as client_last_name,s.firstname as sitter_first_name,s.lastname as sitter_last_name,actual_child_count,child_count,modified,job_start_date,job_end_date,completed_date,TIMEDIFF(completed_date,job_start_date) as ac_hr,TIMEDIFF(job_end_date,job_start_date) as hr,client_rate,client_updated_rate,client_payment_status,sitter_payment_status from jobs



			left join users p on p.userid=jobs.client_user_id

			left join users s on s.userid=jobs.sitter_user_id


			where job_status='completed' and client_payment_status='unpaid' order by {$orderby['key']} {$orderby['odr']} LIMIT  $start,{$sort['rows']} ";

        $res = $this->db->query($sql);
        $total = $res->fetchAll();
        $results = array('total' => count($total_rec), 'rows' => $total);
        return $results;
    }

    function update_client_rate($job_id, $rate)
    {
        $sql = " update jobs SET client_updated_rate=$rate where job_id=$job_id";
        $this->db->query($sql);
    }

    function update_sitter_rate($job_id, $rate)
    {
        $sql = " update jobs SET rate=$rate where job_id=$job_id";
        $this->db->query($sql);
    }

    public function get_device_token_parent($job_id)
    {
        $sql = "select client_user_id,job_id,deviceToken from user_token left join jobs on user_token.userid=jobs.client_user_id where job_id=$job_id ";
        $res = $this->db->query($sql);
        $results = $res->fetchAll();
        return $results;
    }

    public function get_device_token_sitter($job_id)
    {
        $sql = "select sitter_user_id,job_id,deviceToken from user_token left join jobs on user_token.userid=jobs.sitter_user_id left join users on users.userid=jobs.sitter_user_id where job_id=$job_id and users.notify='Yes'";


        $res = $this->db->query($sql);
        $results = $res->fetchAll();
        return $results;
    }

    /* -----replace sitter----------- */

    public function get_job_child_rate($child_count)
    {

        if ($child_count > 4) {
            $child_count = 4;
        }

        $sql = "select sitter_rate,client_rate from rates where child_count=$child_count";
        $res = $this->db->query($sql);
        $results = $res->fetchAll();
        return $results;
    }

    /* ----------------to get sitter job list---------------------------------------- */

    public function get_previos_child_count($job_id)
    {
        $sql = "select child_count,actual_child_count from jobs where job_id=$job_id";
        $res = $this->db->query($sql);
        $results = $res->fetchAll();

        if (empty($results[0]['actual_child_count'])) {
            $child_count = $results[0]['child_count'];
        } else {
            $child_count = $results[0]['actual_child_count'];
        }


        return $child_count;
    }

    /* --------------------function to get scheduled jobs--------------------- */

    public function get_jobs_sitter($job_id)
    {
        $sql = "select sitter_user_id from jobs where job_id=$job_id";
        $res = $this->db->query($sql);
        $results = $res->fetchAll();
        $id = $results[0]['sitter_user_id'];
        return $id;
    }

    public function remove_sitter($job_id,$sitter_id)
    {


        $sql = "update jobs SET sitter_user_id=NULL,job_status='new' where job_id=$job_id";
        $this->db->query($sql);

        $sql1 = "update job_sent SET sent_status='new' where job_id=$job_id";
        $this->db->query($sql1);

        $sitter = new Hb_Sitter();
        $sitter->setAvailabileJobs();
        $this->db->query("UPDATE sitters s SET confirmed_jobs = confirmed_jobs-1 where s.userid= $sitter_id ");
        
    }

    public function getsitterjob($job_id, $sitter_id)
    {

        $sql1 = "Select j1.job_id from jobs j, jobs j1
				where ( (j1.job_end_date
						BETWEEN DATE_SUB( j.job_start_date, INTERVAL 4 HOUR )
						AND DATE_ADD( j.job_end_date, INTERVAL 4 HOUR )) OR
						(j1.job_start_date
						BETWEEN DATE_SUB( j.job_start_date, INTERVAL 4 HOUR )
						AND DATE_ADD( j.job_end_date, INTERVAL 4 HOUR ))
					  )
				AND j1.job_id != j.job_id
				AND j1.job_id !=" . $job_id . "
				AND j1.job_status =  'confirmed'
				AND j.job_id =" . $job_id . "
				AND j1.sitter_user_id=" . $sitter_id;

        $job_time = $this->db->query($sql1);


        $total_rec = $job_time->fetchAll();

//print_r($total_rec);die;


        $sql = "SELECT j1.job_id, date_format( j1.job_start_date, '%Y-%m-%d' ) start_date
				FROM jobs j, jobs j1
				WHERE date_format( j.job_start_date, '%Y-%m-%d' ) = date_format( j1.job_start_date, '%Y-%m-%d' )
				AND j1.job_id != j.job_id
				AND j1.job_status =  'confirmed'
				AND j.job_id =" . $job_id . "
				AND j1.sitter_user_id=" . $sitter_id;
        $no_of_job = $this->db->query($sql);
        $all_jobs = $no_of_job->fetchAll();


        if ((count($all_jobs) >= 2)) {
            return '1';
        } else if (count($total_rec) > 0) {
            return '2';
        } else {
            return '3';
        }
    }

    /* public function assign_sitter_job($job_id,$user_id)
      {
      $url = SITE_URL . 'misc/assignallsitters';
      $i=1;
      //print_r($url);die;
      $post = array(
      'job_id' => $this->job_id,
      'userid' => $user_id,
      'jobno' => $i
      );

      ignore_user_abort(true);
      set_time_limit(0);

      $c = curl_init();
      curl_setopt($c, CURLOPT_URL, $url);
      curl_setopt($c, CURLOPT_POST, 1);
      curl_setopt($c, CURLOPT_POSTFIELDS, $post);
      curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);  // Follow the redirects (needed for mod_rewrite)
      curl_setopt($c, CURLOPT_RETURNTRANSFER, true);  // Return from curl_exec rather than echoing
      curl_setopt($c, CURLOPT_FRESH_CONNECT, true);   // Always ensure the connection is fresh
      // Timeout super fast once connected, so it goes into async.
      curl_setopt($c, CURLOPT_TIMEOUT, 1);

      $output = curl_exec($c);
      curl_close($c);
      } */

    public function scheduledjobs($search = array(), $filter = array(), $sort = array())
    {

        if ($search['job_id'] > 0)
            $query_jobId = ' and j.job_id=' . (int)$search['job_id'];

        if ($search['client_id'] > 0)
            $query_clientId = ' and client_user_id=' . $search['client_id'];

        $sort['rows'] = (int)$search['rows'] == 0 ? 10 : (int)$search['rows'];

        $sort['page'] = (int)$search['page'] == 0 ? 1 : (int)$search['page'];

        $filter['sort'] = (int)$search['sort'] == 0 ? 1 : $search['sort'];

        $orderby = array('key' => (in_array($search['key'], array('j.job_id', 'client_user_id', 'jobs_posted_date', 'job_start_date', 'job_end_date', 'sitter_user_id', 'job_added_by', 'last_modified_date')) ? $search['key'] : 'j.job_start_date'), 'odr' => (in_array($search['odr'], array('asc', 'desc')) ? $search['odr'] : 'asc'));
        if ($search['key'] == '' && in_array($search['status'], array('completed'))) {
            $orderby['key'] = 'completed_date';
            $orderby['odr'] = 'desc';
        }
        if ($sort['page'] > 1) {
            $start = ($sort['page'] - 1) * $sort['rows'];
        } else
            if ($sort['page'] == 1 or $sort['page'] == 0) {
                $start = 0;
            }

        $search_query[] = trim($search['client']) != '' ? '  u.username like "%' . $search['client'] . '%"' : '';
        $search_query[] = trim($search['client']) != '' ? ' u.firstname like "%' . $search['client'] . '%"' : '';
        $search_query[] = trim($search['client']) != '' ? ' u.lastname like "%' . $search['client'] . '%"' : '';

        if (trim($search['job_start_date']) != '' && trim($search['job_end_date']) == '') {
            $search['job_start_date'] = str_replace("-", "/", $search['job_start_date']);

            $search_query[] = ' date(jm.actual_start_date)  = date("' . date('Y-m-d', strtotime($search['job_start_date'])) . '")';
        } else if (trim($search['job_start_date']) == '' && trim($search['job_end_date']) != '') {
            $search['job_end_date'] = str_replace("-", "/", $search['job_end_date']);

            $search_query[] = ' date(jm.actual_end_date)  = date("' . date('Y-m-d', strtotime($search['job_end_date'])) . '")';
        } // else '';
        else if (trim($search['job_start_date']) != '' && trim($search['job_end_date']) != '') {
            $search['job_start_date'] = str_replace("-", "/", $search['job_start_date']);
            $search['job_end_date'] = str_replace("-", "/", $search['job_end_date']);

            $search_query[] = ' date(jm.actual_start_date)  between  date("' . date('Y-m-d', strtotime($search['job_start_date'])) . '") and date("' . date('Y-m-d', strtotime($search['job_end_date'])) . '")';
        }
// else '';


        $search_query = array_diff($search_query, array(''));
        if (!empty($search_query)) {
            $search_query = ' and (' . implode(' or ', $search_query) . ' )';
        } else
            $search_query = '';

        if (in_array($search['status'], array('completed', 'confirmed'))) {

        }
        if (isset($search['sitter_id']) && $search['sitter_id'] != '') {
            $query_sitterId = ' and j.sitter_user_id = ' . $search['sitter_id'];
        }


        /* -------added by namrta for handling pending jobs----------------- */
        if (isset($search['jobtype']) && $search['jobtype'] == 'special') {
            $special_query = ' and j.is_special = ' . "'1'";
        }
        if (isset($search['jobtype']) && $search['jobtype'] == 'simple') {
            $special_query = ' and j.is_special = ' . "'0'";
        }
        $query_status = ' and j.job_status="pending"';

        /* -------------------------------------------------------- */

        $sql = "select TIMESTAMPDIFF(SECOND,j.jobs_posted_date,j.job_start_date)as hours, j.*,u.*,jm.* $query_sitter_user_select from jobs j join job_master jm join users u $query_sitter_user
				on(j.client_user_id=u.userid and j.master_job_id = jm.master_job_id $query_sitter_user_on )
				where u.status!='deleted' $query_jobId  $query_clientId $query_status $query_sitterId $search_query $special_query  order by {$orderby['key']} {$orderby['odr']} LIMIT  $start,{$sort['rows']}";


        $res = $this->db->query($sql);
        $results = $res->fetchAll();
        $this->sitter = new Hb_Sitter();

        foreach ($results as $res) {
            if (!isset($data[$res['job_id']]))
                $data[$res['job_id']] = $res;
            $data[$res['job_id']]['children'] = $this->getChildrenDetails($res['job_id']); //[$res['child_id']]=$res;
            if ($res['sitter_user_id'] != null) {
                $sitter = $this->sitter->getDetail($res['sitter_user_id']);
                $data[$res['job_id']]['sitter_firstname'] = $sitter['firstname'];
                $data[$res['job_id']]['sitter_lastname'] = $sitter['lastname'];
                $data[$res['job_id']]['sitter_id'] = $sitter['userid'];
            } else {
                $data[$res['job_id']]['sitter_firstname'] = '-';
                $data[$res['job_id']]['sitter_lastname'] = '';
                $data[$res['job_id']]['sitter_id'] = 0;
            }
        }
        $sql = "select count(*) as total from jobs j join job_master jm join users u $query_sitter_user
				on(j.client_user_id=u.userid and j.master_job_id = jm.master_job_id $query_sitter_user_on )
				where u.status!='deleted' $query_jobId  $query_clientId $query_status $query_sitterId $search_query $special_query ";


// print_r($sql);die;
        $res = $this->db->query($sql);

        $total = $res->fetchAll();


        $results = array('total' => $total[0]['total'], 'rows' => $data);
        return $results;
    }

    public function close_job($job_id)
    {
        $update = array('job_status' => 'closed');
        $where = $this->jobTable->getAdapter()->quoteInto('job_id = ?', $job_id);
        $this->jobTable->update($update, $where);
    }

    public function cancel_job($job_id)
    {
//	$date=$this->db->query("select now() from jobs");
//	$results = $date->fetchAll();print_r($results);die;
        $sql = "select job_start_date from jobs where job_id=$job_id ";

        $res = $this->db->query($sql);
        $result = $res->fetchAll();
        $job_start_date = $result[0]['job_start_date'];
        $today_date = date('Y-m-d H:i:s');

        $diff = strtotime($job_start_date) - strtotime($today_date);
        $diff_in_hrs = $diff / 3600;


        if (($diff_in_hrs <= 3) && ($diff_in_hrs > 0)) {
            $update = array('job_status' => 'cancelled', 'cancelled_date' => date('Y-m-d H:i:s'), 'immidiate_cancelled' => 'yes');
        } else {
            $update = array('job_status' => 'cancelled', 'cancelled_date' => date('Y-m-d H:i:s'));
        }

        $where = $this->jobTable->getAdapter()->quoteInto('job_id = ?', $job_id);
        $this->jobTable->update($update, $where);

        $this->jobMail($job_id, 'job_request_cancelled', 'client', 'mail');
        $this->jobMail($job_id, 'job_request_cancelled', 'sitter', 'mail');
    }

    public function assign_sitters($job_id, $userid, $jobno)
    {
        $sql = "select distinct userid from users where usertype = 'S' and (status='active' or status='' )";
        $all_sitter = $this->db->query($sql);

        $all_sitter = $all_sitter->fetchAll();

        if (count($all_sitter) > 0) {

            foreach ($all_sitter as $row) {
                $sent_insert = array(
                    'job_id' => $job_id,
                    'sent_to' => $row['userid'],
                    'sent_by' => $userid,
                    'sent_date' => date('Y-m-d H:i:s'),
                    'sent_status' => 'new'
                );

                $sql1 = "select * from job_sent where job_id=" . $job_id . " and sent_to =" . $row['userid'];
                $res1 = $this->db->query($sql1);
                $flag = $res1->fetchAll();

                if (empty($flag)) {
                    $this->db->insert('job_sent', $sent_insert);
                    $this->db->query('update sitters set available_jobs=available_jobs+1 where userid=' . $row['userid']);

                    $msg = "New job has been posted.";

                    if ($jobno == 1) {

                        $this->send_job_notification_sitter($job_id, $row['userid'], $msg);
                    }


                    // $this->send_job_notification_sitter($job_id,$row['userid'],$msg);
                } else {
                    $this->db->query("update job_sent set sent_by=" . $userid . " and sent_date=now() and sent_status='new'");
                }

                // Confirm job to admin
                //jobMail($job_id,$job_details,'send_to_sitter','sitter','mail',$row['userid']);
            }

            $update = array('job_status' => 'pending');
            $where = $this->jobTable->getAdapter()->quoteInto('job_id = ?', $job_id);
            $this->jobTable->update($update, $where);
        }
        $this->db->query('update jobs set total_assigned = (select count(*) from job_sent where job_id =' . $job_id . ') where job_id =' . $job_id);
    }

    public function send_job_notification_sitter($job_id, $user_id, $msg)
    {

        $dt = $this->get_device_token($user_id);
        if (!empty($dt)) {
            $notiData = array(
                'userid' => $user_id,
                'job_id' => $job_id,
                'date_added' => date('Y-m-d H:i:s'),
                'date_updated' => date('Y-m-d H:i:s')
            );

            $this->db->insert('notification', $notiData);
            $lastNotiId = $this->db->lastInsertId();


            $query = "select count(notification_id) as noti_count from notification where userid=$user_id and is_read='0'";
            $res = $this->db->query($query);
            $result = $res->fetchAll();
            $notification_count = (int)$result[0]['noti_count'];

            foreach ($dt as $rowdt) {
                if (!empty($rowdt['deviceToken']) && $rowdt['deviceToken'] != '0' && $rowdt['deviceToken'] != '') {


                    $this->ipush = new Hb_Ipushnotification();

                    $this->ipush->send_notification_sitter($rowdt['deviceToken'], $msg, $job_id, $user_id, $noti_type, $notification_count, $lastNotiId);
                }
            }
        }
    }

    public function get_device_token($user_id)
    {
        $sql = "select user_token.userid,deviceToken from user_token left join users on users.userid=user_token.userid where users.userid=$user_id and users.notify='Yes'";

        $res = $this->db->query($sql);
        $results = $res->fetchAll();
        return $results;
    }

    /* to update cliet/sitter rate */

    public function cancelled_jobs_search($search = array(), $filter = array(), $sort = array())
    {

        if ($search['job_id'] > 0)
            $query_jobId = ' and j.job_id=' . (int)$search['job_id'];

        if ($search['client_id'] > 0)
            $query_clientId = ' and client_user_id=' . $search['client_id'];

        $sort['rows'] = (int)$search['rows'] == 0 ? 10 : (int)$search['rows'];

        $sort['page'] = (int)$search['page'] == 0 ? 1 : (int)$search['page'];

        $filter['sort'] = (int)$search['sort'] == 0 ? 1 : $search['sort'];

        $orderby = array('key' => (in_array($search['key'], array('j.job_id', 'client_user_id', 'jobs_posted_date', 'job_start_date', 'job_end_date', 'sitter_user_id', 'job_added_by', 'last_modified_date')) ? $search['key'] : 'j.job_start_date'), 'odr' => (in_array($search['odr'], array('asc', 'desc')) ? $search['odr'] : 'asc'));
        if ($search['key'] == '' && in_array($search['status'], array('completed'))) {
            $orderby['key'] = 'completed_date';
            $orderby['odr'] = 'desc';
        }
        if ($sort['page'] > 1) {
            $start = ($sort['page'] - 1) * $sort['rows'];
        } else
            if ($sort['page'] == 1 or $sort['page'] == 0) {
                $start = 0;
            }


//print_r($start);die;

        $search_query[] = trim($search['client']) != '' ? '  u.username like "%' . $search['client'] . '%"' : '';
        $search_query[] = trim($search['client']) != '' ? ' u.firstname like "%' . $search['client'] . '%"' : '';
        $search_query[] = trim($search['client']) != '' ? ' u.lastname like "%' . $search['client'] . '%"' : '';

        if (trim($search['job_start_date']) != '' && trim($search['job_end_date']) == '') {
            $search['job_start_date'] = str_replace("-", "/", $search['job_start_date']);

//$search_query[] = ' date(jm.actual_start_date)  = date("' . date('Y-m-d', strtotime($search['job_start_date'])) . '")';


            $search_query[] = ' date(j.job_start_date)  = date("' . date('Y-m-d', strtotime($search['job_start_date'])) . '")';
        } else if (trim($search['job_start_date']) == '' && trim($search['job_end_date']) != '') {
            $search['job_end_date'] = str_replace("-", "/", $search['job_end_date']);
            $search_query[] = ' date(j.job_end_date)  = date("' . date('Y-m-d', strtotime($search['job_end_date'])) . '")';

//$search_query[] = ' date(jm.actual_end_date)  = date("' . date('Y-m-d', strtotime($search['job_end_date'])) . '")';
        } // else '';
        else if (trim($search['job_start_date']) != '' && trim($search['job_end_date']) != '') {
            $search['job_start_date'] = str_replace("-", "/", $search['job_start_date']);
            $search['job_end_date'] = str_replace("-", "/", $search['job_end_date']);
            $search_query[] = ' date(j.job_start_date)  between  date("' . date('Y-m-d', strtotime($search['job_start_date'])) . '") and date("' . date('Y-m-d', strtotime($search['job_end_date'])) . '")';

//  $search_query[] = ' date(jm.actual_start_date)  between  date("' . date('Y-m-d', strtotime($search['job_start_date'])) . '") and date("' . date('Y-m-d', strtotime($search['job_end_date'])) . '")';
        }


        $search_query = array_diff($search_query, array(''));
        if (!empty($search_query)) {
            $search_query = ' and (' . implode(' or ', $search_query) . ' )';
        } else
            $search_query = '';


        if (isset($search['sitter_id']) && $search['sitter_id'] != '') {
            $query_sitterId = ' and j.sitter_user_id = ' . $search['sitter_id'];
        }

        if ($search['type'] == "immidiate") {


            $immidiate_query = ' and j.immidiate_cancelled="yes"';
        } else {
            $immidiate_query = ' and j.immidiate_cancelled="no"';
        }


        $sql = "select count(*) as total from jobs j join job_master jm join users u $query_sitter_user
				on(j.client_user_id=u.userid and j.master_job_id = jm.master_job_id $query_sitter_user_on )
				where u.status!='deleted' and j.job_status='cancelled' $query_jobId  $query_clientId  $query_sitterId $search_query $immidiate_query";
        $res = $this->db->query($sql);


        $total = $res->fetchAll();

        $sql = "select TIMESTAMPDIFF(SECOND,j.jobs_posted_date,j.job_start_date)as hours, j.*,u.*,jm.* $query_sitter_user_select from jobs j join job_master jm join users u $query_sitter_user
				on(j.client_user_id=u.userid and j.master_job_id = jm.master_job_id $query_sitter_user_on )
				where u.status!='deleted' and   j.job_status='cancelled' $query_jobId  $query_clientId  $query_sitterId $search_query $immidiate_query  order by {$orderby['key']} {$orderby['odr']} LIMIT  $start,{$sort['rows']}";


        $res = $this->db->query($sql);


//print_r($sql);die;
        $results = $res->fetchAll();
        $this->sitter = new Hb_Sitter();

        foreach ($results as $res) {
            if (!isset($data[$res['job_id']]))
                $data[$res['job_id']] = $res;
            $data[$res['job_id']]['children'] = $this->getChildrenDetails($res['job_id']); //[$res['child_id']]=$res;
            if ($res['sitter_user_id'] != null) {
                $sitter = $this->sitter->getDetail($res['sitter_user_id']);
                $data[$res['job_id']]['sitter_firstname'] = $sitter['firstname'];
                $data[$res['job_id']]['sitter_lastname'] = $sitter['lastname'];
                $data[$res['job_id']]['sitter_id'] = $sitter['userid'];
            } else {
                $data[$res['job_id']]['sitter_firstname'] = '-';
                $data[$res['job_id']]['sitter_lastname'] = '';
                $data[$res['job_id']]['sitter_id'] = 0;
            }
        }
        $count = $total['0']['total'];

//echo $count;die;
        $results = array('total' => $count, 'rows' => $data);

//print_r($results);die;

        return $results;
    }

    public function get_all_child_of_jobs($job_id)
    {
        $sql = "select * from jobs_to_childs

			left join children  on jobs_to_childs.child_id=children.child_id



			where job_id=$job_id and parent_user_id='0'";

        $res = $this->db->query($sql);
        $total_rec = $res->fetchAll();

        return ($total_rec);
    }

    public function get_job_status($job_id)
    {

        $sql = "select job_status from jobs where job_id=$job_id";

        $res = $this->db->query($sql);
        $total_rec = $res->fetchAll();

        if (!empty($total_rec)) {

            $count = count($total_rec);
        } else {
            $count = 0;
        }

        if ($count > 0) {

            $status = $total_rec[0]['job_status'];
            return ($status);
        } else {
            return false;
        }
    }

    public function add_job_log($job_id, $previos_child_count, $updated_child_count)
    {
        if ($previos_child_count < $updated_child_count)
            $modification = 'Added Child';
        else
            $modification = 'Removed Child';

        $jobData = array(
            'job_id' => $job_id,
            'modified_by' => 1,
            'modified_date' => date('Y-m-d H:i:s'),
            'modification' => $modification,
            'initial' => $previos_child_count,
            'modified' => $updated_child_count
        );

        $this->db->insert('job_logs', $jobData);
        $lastNotiId = $this->db->lastInsertId();
    }

    public function update_client_sitter_rate($job_id, $sitter_rate, $client_rate)
    {
        $sql = " update jobs SET sitter_rate_pre=$sitter_rate,rate=$sitter_rate,client_rate=$client_rate,client_updated_rate=$client_rate where client_payment_profile_id=$client_payment_profile_id and user_id=$user_id ";
        $this->db->query($sql);
    }

    public function removeallsitters($job_id)
    {
        $sql1 = "select sent_to as userid from job_sent where job_id=" . $job_id;
        $res1 = $this->db->query($sql1);
        $result = $res1->fetchAll();

        $sql = "delete from job_sent where job_id=" . $job_id;
        $this->db->query($sql);

        foreach ($result as $row) {
            $this->db->query('update sitters set available_jobs=available_jobs-1 where userid=' . $row['userid']);
            /* $msg = "Admin removed you from a job";
            $this->send_job_notification_sitter($job_id, $row['userid'], $msg); */
        }
    }

    public function job_logs_sitter($job_id, $sitter_id)
    {
        $sql1 = "select * from job_logs where modification not in ('Client Dispute','Payment Problem','New Sitter Application','Special Condition') and modified_by=" . $sitter_id . " and job_id=" . $job_id;
        $res1 = $this->db->query($sql1);
        $result = $res1->fetchAll();
        return $result;
    }


//for alerts tab

    public function searchSpecial($search = array(), $filter = array(), $sort = array())
    {


        //print_r($search);die;
        //$sort['page']=1;

        if ($search['job_id'] > 0)
            $query_jobId = ' and j.job_id=' . (int)$search['job_id'];

        if ($search['client_id'] > 0)
            $query_clientId = ' and client_user_id=' . $search['client_id'];

        $query_status = "and j.job_status in ('pending','new') and is_special='1'";


        $sort['rows'] = (int)$search['rows'] == 0 ? 10 : (int)$search['rows'];

        $sort['page'] = (int)$search['page'] == 0 ? 1 : (int)$search['page'];

        $filter['sort'] = (int)$search['sort'] == 0 ? 1 : $search['sort'];

        $orderby = array('key' => (in_array($search['key'], array('j.job_id', 'is_special', 'client_user_id', 'jobs_posted_date', 'job_start_date', 'job_end_date', 'sitter_user_id', 'job_added_by', 'last_modified_date', 'client_name', 'sitter_name')) ? $search['key'] : 'j.job_start_date'), 'odr' => (in_array($search['odr'], array('asc', 'desc')) ? $search['odr'] : 'desc'));
        if ($search['key'] == '' && in_array($search['status'], array('completed'))) {
            $orderby['key'] = 'completed_date';
            $orderby['odr'] = 'desc';
        }

        if ($orderby['key'] == 'client_name') {
            $orderby['key'] = 'firstname ' . $orderby['odr'] . ', lastname';
        }

        if ($orderby['key'] == 'sitter_name') {
            $orderby['key'] = 'sitter_firstname ' . $orderby['odr'] . ', sitter_lastname';
        }

        //print_r($search);die;
        if ($sort['page'] > 1) {
            $start = ($sort['page'] - 1) * $sort['rows'];
        } else
            if ($sort['page'] == 1 or $sort['page'] == 0) {
                $start = 0;
            }
        if (!empty($search['job_id'])) {
            $start = 0;
        }

        $search_query[] = trim($search['client']) != '' ? '  u.username like "%' . $search['client'] . '%"' : '';
        $search_query[] = trim($search['client']) != '' ? ' u.firstname like "%' . $search['client'] . '%"' : '';
        $search_query[] = trim($search['client']) != '' ? ' u.lastname like "%' . $search['client'] . '%"' : '';

        $search_query[] = trim($search['sitter']) != '' ? ' s.sitter_firstname like "%' . $search['sitter'] . '%"' : '';
        $search_query[] = trim($search['sitter']) != '' ? ' s.sitter_lastname like "%' . $search['sitter'] . '%"' : '';

        if (trim($search['job_start_date']) != '' && trim($search['job_end_date']) == '') {
            $search['job_start_date'] = str_replace("-", "/", $search['job_start_date']);

            //$search_query[] = ' date(jm.actual_start_date)  = date("' . date('Y-m-d', strtotime($search['job_start_date'])) . '")';

            $search_query[] = ' date(j.job_start_date)  = date("' . date('Y-m-d', strtotime($search['job_start_date'])) . '")';
        } else if (trim($search['job_start_date']) == '' && trim($search['job_end_date']) != '') {
            $search['job_end_date'] = str_replace("-", "/", $search['job_end_date']);

            //$search_query[] = ' date(jm.actual_end_date)  = date("' . date('Y-m-d', strtotime($search['job_end_date'])) . '")';

            $search_query[] = ' date(j.job_end_date)  = date("' . date('Y-m-d', strtotime($search['job_end_date'])) . '")';
        } // else '';
        else if (trim($search['job_start_date']) != '' && trim($search['job_end_date']) != '') {
            $search['job_start_date'] = str_replace("-", "/", $search['job_start_date']);
            $search['job_end_date'] = str_replace("-", "/", $search['job_end_date']);

            //$search_query[] = ' date(jm.actual_start_date)  between  date("' . date('Y-m-d', strtotime($search['job_start_date'])) . '") and date("' . date('Y-m-d', strtotime($search['job_end_date'])) . '")';

            $search_query[] = ' date(j.job_start_date)  between  date("' . date('Y-m-d', strtotime($search['job_start_date'])) . '") and date("' . date('Y-m-d', strtotime($search['job_end_date'])) . '")';
        }
        // else '';


        $search_query = array_diff($search_query, array(''));
        if (!empty($search_query)) {
            $search_query = ' and (' . implode(' or ', $search_query) . ' )';
        } else
            $search_query = '';

        if (in_array($search['status'], array('completed', 'confirmed'))) {

        }
        if (isset($search['sitter_id']) && $search['sitter_id'] != '') {
            $query_sitterId = ' and j.sitter_user_id = ' . $search['sitter_id'];
        }

        /* -------------------------------------------------------- */
        $special_query = '';
        $immidiate_status = '';
        $sql = "select ifnull(s.sitter_firstname,'') sitter_firstname,ifnull(s.sitter_lastname,'') sitter_lastname,ifnull(s.sitter_id,'') sitter_id, TIMESTAMPDIFF(HOUR,j.jobs_posted_date,j.job_start_date) as hours,
	TIMESTAMPDIFF(HOUR,j.job_start_date,j.completed_date) as job_hour, j.*,u.*,jm.* $query_sitter_user_select
	from jobs j left join (select users.userid as sitter_id, firstname sitter_firstname, lastname sitter_lastname from users where usertype='S') as s on j.sitter_user_id=s.sitter_id
	join job_master jm join users u  $query_sitter_user
	on(j.client_user_id=u.userid and j.master_job_id = jm.master_job_id $query_sitter_user_on )
	where u.status!='deleted' $query_jobId  $query_clientId $query_status $query_sitterId $search_query $special_query $immidiate_status order by {$orderby['key']} {$orderby['odr']} LIMIT  $start,{$sort['rows']}";

        //print_r($sql);die;
        $res = $this->db->query($sql);
        $results = $res->fetchAll();

        $this->sitter = new Hb_Sitter();

        foreach ($results as $res) {
            if (!isset($data[$res['job_id']]))
                $data[$res['job_id']] = $res;

            $data[$res['job_id']]['alerts'] = $this->getAlerts($res['job_id']);
            $data[$res['job_id']]['children'] = $this->getChildrenDetails($res['job_id']); //[$res['child_id']]=$res;
        }
        $sql = "select count(*) as total from jobs j left join (select userid as sitter_id,firstname sitter_firstname, lastname sitter_lastname from users where usertype='S') as s on j.sitter_user_id=s.sitter_id join job_master jm join users u $query_sitter_user
			on(j.client_user_id=u.userid and j.master_job_id = jm.master_job_id $query_sitter_user_on )
			where u.status!='deleted' $query_jobId  $query_clientId $query_status $query_sitterId $search_query $special_query $immidiate_status";

        $res = $this->db->query($sql);

        $total = $res->fetchAll();
        $results = array('total' => $total[0]['total'], 'rows' => $data);
        return $results;

        //return $data;

    }

    public function getadminalerts($search = array(), $filter = array(), $sort = array())
    {

        $orderby = array('key' => (in_array($search['key'], array('job_id', 'client_name', 'sitter_name')) ? $search['key'] : 'modified_date'), 'odr' => (in_array($search['odr'], array('asc', 'desc')) ? $search['odr'] : 'desc'));

        if ($orderby['key'] == 'job_id') {
            $orderby['key'] = 'jobs.job_id';
        }
        if ($orderby['key'] == 'client_name') {
            $orderby['key'] = 'firstname ' . $orderby['odr'] . ', lastname';
        }

        if ($orderby['key'] == 'sitter_name') {
            $orderby['key'] = 'sitter_firstname ' . $orderby['odr'] . ', sitter_lastname';
        }

        $sort['rows'] = (int)$search['rows'] == 0 ? 10 : (int)$search['rows'];

        $sort['page'] = (int)$search['page'] == 0 ? 1 : (int)$search['page'];

        $filter['sort'] = (int)$search['sort'] == 0 ? 1 : $search['sort'];

        if ($sort['page'] > 1) {
            $start = ($sort['page'] - 1) * $sort['rows'];
        } else
            if ($sort['page'] == 1 or $sort['page'] == 0) {
                $start = 0;
            }

        $sql = "select log_id from job_logs left join jobs on (jobs.job_id =job_logs.job_id) where is_resolved=0 and (job_status is null or job_status != 'closed')";
        $res = $this->db->query($sql);
        $total_count = count($res->fetchAll());

        $sql = "select job_logs.*,ifnull(mbyu.usertype,'J') as alert_source,job_status,users.status as client_status, client_user_id,users.firstname,users.lastname,ifnull(sitter_id,'') sitter_user_id ,ifnull(sitter_firstname,'') sitter_firstname,ifnull(sitter_lastname,'') sitter_lastname
	from job_logs left join jobs on (jobs.job_id =job_logs.job_id)
	left join users on (users.userid =jobs.client_user_id)
	left join (select userid as sitter_id, firstname as sitter_firstname, lastname as sitter_lastname from users where usertype='S') as sitters on (sitters.sitter_id=jobs.sitter_user_id)
	join users mbyu on (mbyu.userid =job_logs.modified_by)
	where is_resolved=0 and ((job_status is null and job_logs.job_id=0 ) or job_status != 'closed') order by {$orderby['key']} {$orderby['odr']} LIMIT  $start,{$sort['rows']}";


        $res = $this->db->query($sql);
        $total = $res->fetchAll();

        $i = 0;
        foreach ($total as $job) {
            $js = $this->newjobstatus($job);
            $total[$i]['job_status'] = $js[0];
            if ($job['alert_source'] == 'P')
                $total[$i]['alert_source'] = 'C';
            if($job['modification']=='Immediate Job')
            	$total[$i]['alert_source'] = 'J';
            if ($job['job_id'] == 0) {
                $total[$i]['job_id'] = '';
                $sql1 = "select firstname, lastname from users where userid=" . $job['initial'];
                $res1 = $this->db->query($sql1);
                $userinfo = $res1->fetchAll();
                $total[$i]['sitter_user_id'] = $job['initial'];
                $total[$i]['sitter_firstname'] = $userinfo[0]['firstname'];
                $total[$i]['sitter_lastname'] = $userinfo[0]['lastname'];
            }
            $i++;
        }
        $results = array('total' => $total_count, 'rows' => $total);
        return $results;

    }

    public function resolve($id)
    {
        $sql = 'update job_logs set is_resolved=1 where log_id=' . $id;
        $this->db->query($sql);
    }

// for language filter in assign sitter

    function languageprefer($job_id)
    {
        $ret = array();

        /*$sql = "(SELECT * FROM `preference_group` g
                JOIN `preference_master` m ON ( g.group_id = m.group_id ) where group_name='language')";*/

        $sql = "(SELECT g.group_id,g.group_name,g.label,g.visible_to_client,ifnull(for_manage_sitter,1) as for_manage_sitter,g.ordering,m.* FROM `preference_group` g
			JOIN `preference_master` m ON ( g.group_id = m.group_id ) where group_name='language') union (SELECT g.*,m.* FROM `preference_group` g join `preference_master` m join `job_preference` s
	on(g.group_id=m.group_id and s.prefer_id=m.prefer_id)
	where job_id=" . $job_id . " and group_name='language')";

        $res = $this->db->query($sql);
        $prefers = $res->fetchAll();

        foreach ($prefers as $p) {
            $ret['prefer'][$p['prefer_id']] = $p;
            if (isset($ret))
                $ret['label'] = $p['label'];
        }
        return $ret;
    }

// update status and total assign on removing all sitters
    function updatetotalassign($job_id)
    {
    	$sql = "update jobs set total_assigned = null , job_status='new' where job_id=" . $job_id;
    	$this->db->query($sql);
    }


}
