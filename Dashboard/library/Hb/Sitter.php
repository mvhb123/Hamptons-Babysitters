<?php

class Hb_Sitter extends Hb_User {

    public function __construct() {
        $this->db = Zend_Db_Table::getDefaultAdapter();
        parent::__construct();
    }

    public function create(array $sitter, $status) {

        $sitter['added_by'] = Zend_Auth::getInstance()->getIdentity()->userid;
        $sitter['usertype'] = 'S';
        $oldUser = $this->checkUsername($sitter['username']);
        if (empty($oldUser)) {
            $userid = $this->addUser($sitter, $status);
            $userInfo = array('userid' => $userid);
            $images = array();
            if ($sitter['profile_image']['name'] != '') {

                if ($this->upload($sitter['profile_image'], $userid)) {
                    $path = PROFILE_IMAGE_ABS_PATH . 'orginal/' . $userid . '__' . $sitter['profile_image']['name'];
                    $thumb = PROFILE_IMAGE_ABS_PATH . 'thumb/' . $userid . '__' . $sitter['profile_image']['name'];
                    $full = PROFILE_IMAGE_ABS_PATH . 'full/' . $userid . '__' . $sitter['profile_image']['name'];

                    $APP = PROFILE_IMAGE_ABS_PATH . 'app_thumb/' . $userid . '__' . $sitter['profile_image']['name'];

                    $path = PROFILE_IMAGES . 'orginal/' . $userid . '__' . $sitter['profile_image']['name'];
                    $thumb = PROFILE_IMAGES . 'thumb/' . $userid . '__' . $sitter['profile_image']['name'];
                    $full = PROFILE_IMAGES . 'full/' . $userid . '__' . $sitter['profile_image']['name'];

                    $APP = PROFILE_IMAGES . 'app_path/' . $userid . '__' . $sitter['profile_image']['name'];
                    //$full = PROFILE_IMAGES . 'full/' . $userid . '__' . $sitter['profile_image']['name'];

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
                'local_phone' => $sitter['local_phone'],
                'work_phone' => $sitter['work_phone'],
            );
            $profile = array_merge($userInfo, $images, $other_info);
            $this->addProfile($profile);
            //$profileid = $this->addProfile($sitter);
            $this->setSitterDetail($sitter, $userid);
            if (is_array($sitter['prefer'])) {
                $this->addPreference($sitter['prefer'], $userid);
            }
        }
        $this->sitter_id = $userid;

        $this->addReference($sitter['reference']);
        $this->addEducation($sitter['education']);

        return $userid;
    }

    public function modify($sitter, $userid) {
        //echo "dfsddd";die;
        // print_r($sitter);die;
        $sitter['added_by'] = Zend_Auth::getInstance()->getIdentity()->userid;
        $sitter['usertype'] = 'S';
        $sitter['modified_date']=date('Y-m-d H:i:s');


        if (isset($sitter['sitternotes'])) {
            $sitter['sitternotes'] = $sitter['sitternotes'];
        }


        $oldUser = $this->checkUsername($sitter['username'], $userid);
        if (empty($oldUser)) {
            $this->updateUser($sitter, $userid);
            $userInfo = array('userid' => $userid);
            $images = array();
            if ($sitter['profile_image']['name'] != '') {

                if ($this->upload($sitter['profile_image'], $userid)) {
                    $path = PROFILE_IMAGE_ABS_PATH . 'orginal/' . $userid . '__' . $sitter['profile_image']['name'];
                    $thumb = PROFILE_IMAGE_ABS_PATH . 'thumb/' . $userid . '__' . $sitter['profile_image']['name'];
                    $full = PROFILE_IMAGE_ABS_PATH . 'full/' . $userid . '__' . $sitter['profile_image']['name'];

                    //by namrata for the sitter profile img for 200*200
                    $APP = PROFILE_IMAGE_ABS_PATH . 'app_thumb/' . $userid . '__' . $sitter['profile_image']['name'];


                    $path = PROFILE_IMAGES . 'orginal/' . $userid . '__' . $sitter['profile_image']['name'];
                    $thumb = PROFILE_IMAGES . 'thumb/' . $userid . '__' . $sitter['profile_image']['name'];
                    $full = PROFILE_IMAGES . 'full/' . $userid . '__' . $sitter['profile_image']['name'];

                    $APP = PROFILE_IMAGES . 'app_thumb/' . $userid . '__' . $sitter['profile_image']['name'];


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
                'local_phone' => $sitter['local_phone'],
                'work_phone' => $sitter['work_phone'],
            );
            $profile = array_merge($userInfo, $images, $other_info);
            $this->addProfile($profile);
            //$profileid = $this->addProfile($sitter);
            $this->setSitterDetail($sitter, $userid, 'update');
            if (is_array($sitter['prefer'])) {
                $this->addPreference($sitter['prefer'], $userid);
            }
        }
        $this->sitter_id = $userid;

        $this->addReference($sitter['reference']);
        $this->addEducation($sitter['education']);
        return $userid;
    }

    public function setSitterDetail($sitter, $userid, $action = 'insert') {

        if ($_SESSION['Zend_Auth']['storage']->usertype == 'A') {
            $insert = array('userid' => $userid,
                'about_me' => $sitter['about_me'],
                'traits' => $sitter['traits'],
                'exp_summary' => $sitter['exp_summary'],
                //'have_car' => $sitter['have_car'],
                'cpr_holder' => $sitter['cpr_holder'],
                'cpr_adult' => $sitter['cpr_adult'],
                'first_aid_cert' => $sitter['first_aid_cert'],
                //'clean_drive_record' => $sitter['clean_drive_record'],
                //'criminal_record' => $sitter['criminal_record'],
                //'have_child' => $sitter['have_child'],
                'otherpreference' => $sitter['otherpreference'],
                'sitternotes' => $sitter['sitternotes'],
            	//by anjali
            	'hampton_babysitter_training' => $sitter['hampton_babysitter_training'],
            	'water_certification' => $sitter['water_certification'],
            	'is_student' => ($sitter['is_student'])?$sitter['is_student']:0,
            	'highest_edu' => $sitter['highest_edu'],
            	'lifeguard' => ($sitter['lifeguard'])?$sitter['lifeguard']:'no',
            	'special_need_exp' => $sitter['special_need_exp'],//by anjali
            );
        } else {
            $insert = array('userid' => $userid,
                'about_me' => $sitter['about_me'],
                'traits' => $sitter['traits'],
                'exp_summary' => $sitter['exp_summary'],
                //'have_car' => $sitter['have_car'],
                'cpr_holder' => $sitter['cpr_holder'],
                'cpr_adult' => $sitter['cpr_adult'],
                'first_aid_cert' => $sitter['first_aid_cert'],
                //'clean_drive_record' => $sitter['clean_drive_record'],
                //'criminal_record' => $sitter['criminal_record'],
                //'have_child' => $sitter['have_child'],
                'otherpreference' => $sitter['otherpreference'],
            	//by anjali
            	'hampton_babysitter_training' => $sitter['hampton_babysitter_training'],
            	'water_certification' => $sitter['water_certification'],
            	'is_student' => ($sitter['is_student'])?$sitter['is_student']:0,
            	'highest_edu' => $sitter['highest_edu'],
            	'lifeguard' => ($sitter['lifeguard'])?$sitter['lifeguard']:'no',
            	'special_need_exp' => $sitter['special_need_exp'],//by anjali
            );
        }



        $this->sitterTable = new Zend_Db_Table('sitters');

        if ($action == 'update') {
            unset($insert['userid']);
            $where = $this->db->quoteInto('userid = ?', $userid);
            $this->sitterTable->update($insert, $where);
        } else
            $this->sitterTable->insert($insert);
    }

    public function getDetail($userid, $type = 'profile') {

        $sql = "select  *,u.userid as userid from users u left join sitters s on(u.userid=s.userid) where u.usertype='S' and u.userid=$userid ";
        $res = $this->db->query($sql);

        $sitters = $res->fetchAll();
        $sql = "select  * from user_profile where userid=$userid ";
        $res = $this->db->query($sql);
        $profile = $res->fetchAll();
        if (empty($profile))
            $profile[0] = array();
        //var_dump($profile);
        //print_r($profile);
        $return = array_merge($sitters[0], $profile[0]);
        return $return;
    }

    public function delete($sitter_id) {
        $this->deleteUser($sitter_id);
    }

    public function update(array $sitter, $sitter_id) {
        $this->sitterTable = new Zend_Db_Table('users');

        $sql = "select  profile_completed from users u where u.userid=$sitter_id ";
        $res = $this->db->query($sql);
        $sitters = $res->fetchAll();
        
        if($sitters[0]['profile_completed']!='1'&& $sitters[0]['profile_completed']!=1)
        	$sitter['status'] ='inactive';
        
        $where = $this->db->quoteInto('userid = ?', $sitter_id);
        $this->userTable->update($sitter, $where);
        
        // notification on profile approval
        $this->jobs = new Hb_Jobs();
        $this->notification = new Hb_Notification();
        $results = $this->jobs->get_device_token($sitter_id);
        
        if (!empty($results)) {
        	//to insert in to notifi table
        	$noti_data = array();
        	
        	$noti_data['job_id'] = '';
        	$noti_data['userid'] = $sitter_id;
        	$noti_data['date_added'] = date('Y-m-d H:i:s');
        	$noti_data['date_updated'] = date('Y-m-d H:i:s');
        
        	$notification_id = $this->notification->insert_notification_data($noti_data);
        
        	//get unread notification count
        	$count = $this->notification->get_unread_count($sitter_id);
        
        	$this->ipush = new Hb_Ipushnotification();
        	$message = "You profile has been approved by admin";
        
        	foreach ($results as $noti) {
        		$noti_type = 1;
        
        		if ((!empty($noti['userid'])) && (!empty($noti['deviceToken']))) {
        
        			$this->ipush->send_notification_sitter($noti['deviceToken'], $message,'', $noti['userid'], $noti_type, $count, $notification_id,"",$sitter['status']);
        
        		}
        	}
        }
        
        if ($sitter['status'] == 'active') {
        	$this->assign_open_job($sitter_id);
            $this->sitterMail($sitter_id, 'sitter_approved_mail');
        }
    }

    public function processJob($job_id, $sitter_id, $status) { //status as approve reject, etc
    }

    public function get($sitter_id) {
        
    }

    public function search($search = array(), $filter = array(), $sort = array()) {

        $search['status'] = in_array($search['status'], array('active', 'inactive', 'deleted', 'unapproved')) ? '("' . $search['status'] . '")' : '  ("active","unapproved")';

        $sort['rows'] = (int) $search['rows'] == 0 ? 10 : (int) $search['rows'];

        $sort['page'] = (int) $search['page'] == 0 ? 1 : (int) $search['page'];

        $filter['sort'] = (int) $search['sort'] == 0 ? 1 : $search['sort'];

        $search_query[] = trim($search['email']) != '' ? '  u.username like "%' . $search['email'] . '%"' : '';
        //   $search_query[] = trim($search['fullname']) != '' ? ' (u.firstname like "%' . $search['fullname'] . '%"' . ' or  u.lastname like "%' . $search['fullname'] . '%")' : '';


        if (!empty($search['fullname'])) {
            //getting length of string
            $array = explode(" ", $search['fullname']);

            if (count($array) == 1) {
                $search_query[] = trim($search['fullname']) != '' ? ' (u.firstname like "%' . $array[0] . '%"' . ' or  u.lastname like "%' . $array[0] . '%")' : '';
            } else {
                $end = end($array);
                $search_query[] = trim($search['fullname']) != '' ? ' (u.firstname like "%' . $array[0] . '%"' . ' or  u.lastname like "%' . $end . '%")' : '';
            }

            // print_r($length);die;
        }





        $search_query[] = trim($search['current_city']) != '' ? ' u.current_city like "%' . $search['current_city'] . '%"' : '';
        $search_query[] = trim($search['phone']) != '' ? ' u.phone like "%' . $search['phone'] . '%"' : '';



        if (trim($search['joining_start_date']) != '' && trim($search['joining_end_date']) == '') {
            $search['joining_start_date'] = str_replace("-", "/", $search['joining_start_date']);
            $search_query[] = ' date(joining_date)  = date("' . date('Y-m-d', strtotime($search['joining_start_date'])) . '")';
        }
        else if (trim($search['joining_start_date']) == '' && trim($search['joining_end_date']) != '') {
            $search['joining_end_date'] = str_replace("-", "/", $search['joining_end_date']);
            $search_query[] = ' date(joining_date)  = date("' . date('Y-m-d', strtotime($search['joining_end_date'])) . '")';
        }
        else if (trim($search['joining_start_date']) != '' && trim($search['joining_end_date']) != '') {
            $search['joining_start_date'] = str_replace("-", "/", $search['joining_start_date']);
            $search_query[] = ' date(joining_date)  between  date("' . date('Y-m-d', strtotime($search['joining_start_date'])) . '") and date("' . date('Y-m-d', strtotime($search['joining_end_date'])) . '")';
        }




        $orderby = array('key' => (in_array($search['key'], array('u.userid', 'firstname', 'joining_date')) ? $search['key'] : 'firstname' ), 'odr' => (in_array($search['odr'], array('asc', 'desc')) ? $search['odr'] : 'asc' ));

        $search_query = array_diff($search_query, array(''));
        if (!empty($search_query)) {
            $search_query = ' and (' . implode(' and ', $search_query) . ' )';
        } else
            $search_query = '';

        if ($search['page'] > 1) {
            $start = ($sort['page'] - 1) * $sort['rows'];
        } else
        if ($sort['page'] == 1 or $sort['page'] == 0) {
            $start = 0;
        }

        $prefer = implode(',', $search['prefer']);
        if ($prefer != '' && $search['filter'] == 'Search') {
            $prefer_query = ' and sp.prefer_id in(' . $prefer . ')';
            $prefer_join = ' join sitter_preference sp ';
            $prefer_join_on = ' and sp.sitter_user_id=u.userid  ';
        }

        $search['miscinfo'] = $search['miscinfo'] != '' ? " and u.miscinfo like '%{$search['miscinfo']}%' " : '';


        $sql = "select SQL_CALC_FOUND_ROWS *,u.userid as sitter_id from users u join sitters s $prefer_join on(u.userid=s.userid $prefer_join_on) where u.usertype='S' and status in {$search['status']} $search_query $prefer_query {$search['miscinfo']} group by u.userid  order by {$orderby['key']} {$orderby['odr']} LIMIT $start,{$sort['rows']} ";
        //die();
        $res = $this->db->query($sql);

        $sitters = $res->fetchAll();

        $res = $this->db->query('SELECT FOUND_ROWS() as total');

        $total = $res->fetchAll();
        $results = array('total' => $total[0]['total'], 'rows' => $sitters);
        return $results;
    }

    public function searchByPreference(array $prefer_array, $jobid) {
        $results = array();
        $prefer = implode(',', $prefer_array);
        if ($prefer != '') {
            $prefer_query = ' and sp.prefer_id in(' . $prefer . ')';
            $prefer_join_on = ' join sitter_preference sp join preference_master pm on(sp.sitter_user_id=u.userid and pm.prefer_id=sp.prefer_id ) ';
        }
        $sql = "select  *,u.userid as sitter_id from users u $prefer_join_on where u.usertype='S' and status='active' and u.userid not in(select sent_to from job_sent where job_id='$jobid') $prefer_query order by u.firstname";

        $res = $this->db->query($sql);
        // print_r($sql);die;
        $sitters = $res->fetchAll();
        foreach ($sitters as $sitter) {
            if (!isset($results[$sitter['userid']]))
                $results[$sitter['userid']] = $sitter;
            $results[$sitter['userid']]['prefer'][$sitter['prefer_id']] = $sitter['prefer_name'];
            //echo '<br>';
        }



        //print_r($results);
        return $results;
    }

    public function addPreference($prefer, $userid) {
        $this->db->query("Delete from sitter_preference where sitter_user_id=$userid ");

        foreach ($prefer as $ch) {
            $query[] = "($userid,$ch)";
        }
        $values = implode(',', $query);

        $sql = "insert ignore into sitter_preference values $values";
        $res = $this->db->query($sql);
    }

    public function getPreferences($sitter_id) {

        $ret = array();

        $sql = "SELECT * FROM `preference_group` g join `preference_master` m join `sitter_preference` s 
						on(g.group_id=m.group_id and s.prefer_id=m.prefer_id)
						where sitter_user_id=$sitter_id";

        $res = $this->db->query($sql);

        $prefers = $res->fetchAll();

        foreach ($prefers as $p)
            $ret[] = $p['prefer_id'];

        return $ret;
    }

    public function getEducation($userid) {
        $ret = array();

        $sql = "SELECT * FROM education 
						where user_id=$userid";

        $res = $this->db->query($sql);

        return $res->fetchAll();
    }

    public function getReference($userid) {
        $ret = array();

        $sql = "SELECT * FROM sitter_references 
						where sitter_user_id=$userid";

        $res = $this->db->query($sql);

        return $res->fetchAll();
    }

    public function addEducation($edu) {
        $this->db->query('delete from education where user_id=' . $this->sitter_id);
        for ($i = 0; $i <= count($edu['institution']); $i++) {
            if ($edu['institution'][$i] != '') {
                $query[] = "({$this->sitter_id},'" . addslashes($edu['institution'][$i]) . "','" . date('Y-m-d', strtotime($edu['start_date'][$i])) . "','" . date('Y-m-d', strtotime($edu['end_date'][$i])) . "','" . addslashes($edu['degree'][$i]) . "')";
            }
        }
        $values = implode(',', $query);
        if (!empty($query)) {
            $sql = "insert ignore into education ( `user_id`, `institution`, `start_date`, `end_date`, `degree`)values $values";

            $res = $this->db->query($sql);
        }
    }

    public function addReference($ref) {
        $this->db->query('delete from sitter_references where sitter_user_id=' . $this->sitter_id);
        for ($i = 0; $i <= count($ref['refered_by']); $i++) {
            if ($ref['refered_by'][$i] != '') {
                $query[] = "({$this->sitter_id},'" . addslashes($ref['refered_by'][$i]) . "','{$ref['phone'][$i]}','" . addslashes($ref['relationship'][$i]) . "')";
            }
        }
        $values = implode(',', $query);
        if (!empty($query)) {
            $sql = "insert ignore into sitter_references (`sitter_user_id`, `refered_by`, `phone`,`relationship`)values $values";
            $res = $this->db->query($sql);
        }
    }

    public function setAvailabileJobs() {
        /* $this->db->query("UPDATE sitters s SET available_jobs = 
							( SELECT count( * ) FROM job_sent WHERE sent_status = 'new' AND sent_to = s.userid )"); */
    	//optimised query
    	$this->db->query("UPDATE sitters s 
left join ( SELECT count( * ) as total_records,sent_to FROM job_sent WHERE sent_status = 'new' group by sent_to  ) as temp on temp.sent_to = s.userid  
SET available_jobs = temp.total_records");
    }

    public function setConfirmedJobs() {
        //echo "UPDATE sitters s SET confirmed_jobs = 
        //		( SELECT count( * ) FROM jobs WHERE job_status = 'confirmed' AND sitter_id = s.userid )";die();
        $this->db->query("UPDATE sitters s SET confirmed_jobs = 
							( SELECT count( * ) FROM jobs WHERE job_status = 'confirmed' AND sitter_user_id = s.userid )");
    }

    public function setCompletedJobs() {
        //echo $sql="UPDATE sitters s SET confirmed_jobs = 
        //				( SELECT count( * ) FROM jobs WHERE job_status = 'completed' AND sitter_user_id = s.userid )";
        $this->db->query("UPDATE sitters s SET completed_jobs = 
							( SELECT count( * ) FROM jobs WHERE job_status = 'completed' AND sitter_user_id = s.userid )");

        $this->db->query("UPDATE clients_detail c SET events_compeleted = 
							( SELECT count( * ) FROM jobs WHERE job_status = 'completed' AND client_user_id = c.userid )");
    }

    public function sitterMailString($sitter) {


        return array(
            '{sitter_email}' => $sitter['username'],
            '{sitter_firstname}' => ucwords($sitter['firstname']),
            '{sitter_lastname}' => ucwords($sitter['lastname']),
            '{sitter_phone}' => $sitter['phone'],
            '{sitter_currentcity}' => ucwords($sitter['current_city']),
                //'{sitter_currentcity}'=>$sitter['current_city'],
        );
    }

    public function sitterMail($sitter_id, $mail_name, $to = 'sitter', $from = 'mail') {


        $sitter = new Hb_Sitter();



        //if($sitter_id)
        $sitterInfo = $sitter->getDetail($sitter_id);

        $this->hbSettings = new Hb_Settings();
        $mailTemplate = $this->hbSettings->getMailTemplates(array('mail_name' => $mail_name));
        $mailTemplate = $mailTemplate[0];
        $text = '';
        if ($from == 'mail')
            $from = $mailTemplate['from'];
        else if ($from == 'sitter')
            $from = $sitterInfo['username'];

        if ($to == 'mail')
            $to = $mailTemplate['to'];

        else if ($to == 'sitter') {
            $to = $sitterInfo['username'];
            $to_name = $sitterInfo['firstname'] . ' ' . $sitterInfo['lastname'];
        }



        $cc = explode(',', $mailTemplate['cc']);
        $bcc = explode(',', $mailTemplate['bcc']);


        $sitterReplace = $sitter->sitterMailString($sitterInfo);

        $subject = str_ireplace(array_keys($sitterReplace), $sitterReplace, $mailTemplate['subject']);



        $text = str_ireplace('{viewlink}', SITE_URL . 'sitters/preview/job/' . $job_id, $mailTemplate['description']);
        $text = str_ireplace(array_keys($address), $address, $text);
        $text = str_ireplace(array_keys($sitterReplace), $sitterReplace, $text);
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
        $mail->send();
    }

    /* -------------------------functiom to get sitter earnings---------------------- */

    public function get_sitter_earnings($search = array(), $filter = array(), $sort = array()) {

        if (trim($search['job_start_date']) != '' && trim($search['job_end_date']) == '')
            $search_query[] = ' date(job_start_date)  = date("' . date('Y-m-d', strtotime($search['job_start_date'])) . '")';
        else if (trim($search['job_start_date']) == '' && trim($search['job_end_date']) != '')
            $search_query[] = ' date(job_end_date)  = date("' . date('Y-m-d', strtotime($search['job_end_date'])) . '")';
        // else '';
        else if (trim($search['job_start_date']) != '' && trim($search['job_end_date']) != '')
            $search_query[] = ' date(job_start_date)  between  date("' . date('Y-m-d', strtotime($search['job_start_date'])) . '") and date("' . date('Y-m-d', strtotime($search['job_end_date'])) . '")';
        // else '';
        //print_r($search_query);die;

        $search_query = array_diff($search_query, array(''));
        if (!empty($search_query)) {
            $search_query = ' and (' . implode(' or ', $search_query) . ' )';
        } else
            $search_query = '';



        //print_r($search);die;
        $orderby = array('key' => (in_array($search['key'], array('job_id', 'firstname', 'actual_child_count', 'hr', 'total_received', 'total_paid', 'company')) ? $search['key'] : 'job_id' ), 'odr' => (in_array($search['odr'], array('asc', 'desc')) ? $search['odr'] : 'asc' ));
        $sort['rows'] = (int) $search['rows'] == 0 ? 10 : (int) $search['rows'];

        $sort['page'] = (int) $search['page'] == 0 ? 1 : (int) $search['page'];

        $filter['sort'] = (int) $search['sort'] == 0 ? 1 : $search['sort'];

        if ($search['page'] > 1) {
            $start = ($sort['page'] - 1) * $sort['rows'];
        } else
        if ($sort['page'] == 1 or $sort['page'] == 0) {
            $start = 0;
        }

        $query_sitterId = 'and jobs.sitter_user_id = ' . $search['user'];
        
        /* commented for sitter earning fix 
         *  $sql = "select firstname,lastname,actual_child_count,job_start_date,job_end_date,total_received,total_paid,total_received-total_paid as company,actual_end_date-actual_start_date as hour from jobs left join job_master on jobs.master_job_id=job_master.master_job_id left join users on jobs.client_user_id=users.userid  where job_status='completed'  $query_sitterId $search_query ";
        $res = $this->db->query($sql);
        $total = $res->fetchAll();
        $sql = "select job_id,firstname,lastname,actual_child_count,job_start_date,completed_date,job_end_date,completed_date-job_start_date as hr,total_received,total_paid,total_received-total_paid as company,TIMEDIFF(completed_date,job_start_date) as hr from jobs left join job_master on jobs.master_job_id=job_master.master_job_id left join users on jobs.client_user_id=users.userid   where job_status='completed' $query_sitterId  $search_query order by {$orderby['key']} {$orderby['odr']} LIMIT  $start,{$sort['rows']} ";
        $res = $this->db->query($sql);
        $total_rec = $res->fetchAll();

        $sql1 = "select job_id,firstname,lastname,actual_child_count,job_start_date,job_end_date,completed_date-job_start_date as hr,total_received,total_paid,total_received-total_paid as company,TIMEDIFF(completed_date,job_start_date) as hr from jobs left join job_master on jobs.master_job_id=job_master.master_job_id left join users on jobs.client_user_id=users.userid   where job_status='completed' $query_sitterId  $search_query order by {$orderby['key']} {$orderby['odr']} ";
        $result = $this->db->query($sql1);
        $total_records = $result->fetchAll(); */
        
        //fix for sitters earnings
        $sql = "select job_id,firstname,lastname,ifnull(actual_child_count,child_count) as actual_child_count,job_start_date,completed_date,job_start_date as actual_start_date, if( completed_date=0 OR completed_date is null or completed_date < job_start_date   ,job_end_date, completed_date ) actual_end_date,job_end_date,
        if(   job_status = 'cancelled' and  immidiate_cancelled = 'Yes' and job_start_date > cancelled_date , 3, ( ((TIMESTAMPDIFF(MINUTE, job_start_date ,if( completed_date=0 OR completed_date is null or completed_date < job_start_date   ,job_end_date, completed_date ))/60) ) ))*ifnull( rate,sitter_rate_pre ) as total_paid,
        if(   job_status = 'cancelled' and  immidiate_cancelled = 'Yes' and job_start_date > cancelled_date , 3, ( ((TIMESTAMPDIFF(MINUTE, job_start_date , if( completed_date=0 OR completed_date is null or completed_date < job_start_date   ,job_end_date, completed_date ))/60 ) )  ))*ifnull( client_rate,client_updated_rate ) total_received,
		if(   job_status = 'cancelled' and  immidiate_cancelled = 'Yes' and job_start_date > cancelled_date , 3, ( ((TIMESTAMPDIFF(MINUTE, job_start_date , if( completed_date=0 OR completed_date is null or completed_date < job_start_date   ,job_end_date, completed_date ))/60 ) )  ))*ifnull( client_rate,client_updated_rate )
        	-
        if(   job_status = 'cancelled' and  immidiate_cancelled = 'Yes' and job_start_date > cancelled_date , 3, ( ((TIMESTAMPDIFF(MINUTE, job_start_date ,if( completed_date=0 OR completed_date is null or completed_date < job_start_date   ,job_end_date, completed_date ))/60) ) ))*ifnull( rate,sitter_rate_pre )  as company
        ,TIMEDIFF(if( completed_date=0 OR completed_date is null or completed_date < job_start_date   ,job_end_date, completed_date ),job_start_date) as hr 
        from jobs left join users on jobs.client_user_id=users.userid   
        where (job_status in ('completed','closed')  or ( job_status = 'cancelled' and  immidiate_cancelled = 'Yes')) $query_sitterId  $search_query order by {$orderby['key']} {$orderby['odr']} LIMIT  $start,{$sort['rows']} ";
        $res = $this->db->query($sql);
        $total_rec = $res->fetchAll();
        
        $sql1 = "select job_id,firstname,lastname,ifnull(actual_child_count,child_count) as actual_child_count,job_start_date,job_start_date as actual_start_date,completed_date,if( completed_date=0 OR completed_date is null or completed_date < job_start_date   ,job_end_date, completed_date ) actual_end_date,job_end_date,
        if(   job_status = 'cancelled' and  immidiate_cancelled = 'Yes' and job_start_date > cancelled_date , 3, ( ((TIMESTAMPDIFF(MINUTE, job_start_date ,if( completed_date=0 OR completed_date is null or completed_date < job_start_date   ,job_end_date, completed_date ))/60) ) ))*ifnull( rate,sitter_rate_pre ) as total_paid,
        if(   job_status = 'cancelled' and  immidiate_cancelled = 'Yes' and job_start_date > cancelled_date , 3, ( ((TIMESTAMPDIFF(MINUTE, job_start_date , if( completed_date=0 OR completed_date is null or completed_date < job_start_date   ,job_end_date, completed_date ))/60 ) )  ))*ifnull( client_rate,client_updated_rate ) total_received,
		if(   job_status = 'cancelled' and  immidiate_cancelled = 'Yes' and job_start_date > cancelled_date , 3, ( ((TIMESTAMPDIFF(MINUTE, job_start_date , if( completed_date=0 OR completed_date is null or completed_date < job_start_date   ,job_end_date, completed_date ))/60 ) )  ))*ifnull( client_rate,client_updated_rate )
        	-
        if(   job_status = 'cancelled' and  immidiate_cancelled = 'Yes' and job_start_date > cancelled_date , 3, ( ((TIMESTAMPDIFF(MINUTE, job_start_date ,if( completed_date=0 OR completed_date is null or completed_date < job_start_date   ,job_end_date, completed_date ))/60) ) ))*ifnull( rate,sitter_rate_pre )  as company
        ,TIMEDIFF(if( completed_date=0 OR completed_date is null or completed_date < job_start_date   ,job_end_date, completed_date ),job_start_date) as hr 
        from jobs left join users on jobs.client_user_id=users.userid   
        where (job_status in ('completed','closed')  or ( job_status = 'cancelled' and  immidiate_cancelled = 'Yes')) $query_sitterId  $search_query order by {$orderby['key']} {$orderby['odr']} ";
        $result = $this->db->query($sql1);
        $total_records = $result->fetchAll();

        $results = array('total' => count($total_records), 'rows' => $total_rec, 'records' => $total_records);
        return $results;
    }

    /* ----------------------------------function to grt list of unpaid ditters----------------- */

    public function get_unpaid_sitter_list($search = array(), $filter = array(), $sort = array()) {
       // $sql = "select firstname,lastname,job_id,sitter_user_id from jobs  left join users on jobs.sitter_user_id=users.userid  where job_status='completed' and sitter_payment_status='unpaid' and status='active' group by sitter_user_id ORDER BY firstname ASC ";
    	$sql = "select firstname,lastname,status,job_id,sitter_user_id from jobs join users on jobs.sitter_user_id=users.userid  where (job_status='completed'  OR ( job_status ='cancelled' AND immidiate_cancelled ='yes' ) ) and sitter_payment_status='unpaid' group by sitter_user_id ORDER BY firstname ASC ";
    	 
    	$res = $this->db->query($sql);
        $total = $res->fetchAll();
        return $total;
    }

    /* ----------------------------function to get details of unpaod siter details----------------- */

    public function get_unpaid_sitter_details($search = array(), $filter = array(), $sort = array()) {


//        if ((!empty($search['sitter_user_id'])) && ($search['sitter_user_id'] == '0')) {
//            $query_sitterId = 'and sitter_user_id = 0';
//        }
        //print_r($search);die;

        if ((!empty($search['sitter_user_id'])) && ($search['sitter_user_id'] != '0')) {
            $query_sitterId = 'and sitter_user_id = ' . $search['sitter_user_id'];
        } else {
            $query_sitterId = 'and sitter_user_id = 0';
        }


        // echo $query_sitterId;die;
        $sort['rows'] = (int) $search['rows'] == 0 ? 10 : (int) $search['rows'];
        $sort['page'] = (int) $search['page'] == 0 ? 1 : (int) $search['page'];
        $filter['sort'] = (int) $search['sort'] == 0 ? 1 : $search['sort'];
        if ($search['page'] > 1) {
            $start = ($sort['page'] - 1) * $sort['rows'];
        } else
        if ($sort['page'] == 1 or $sort['page'] == 0) {
            $start = 0;
        }
        
        //$sql = "select firstname,lastname,job_id,sitter_user_id,client_user_id from jobs  left join users on jobs.sitter_user_id=users.userid where job_status='completed' and sitter_payment_status='unpaid'  $query_sitterId ";
        $sql = "select firstname,lastname,job_id,sitter_user_id,client_user_id from jobs join users on jobs.sitter_user_id=users.userid where (job_status='completed'  OR ( job_status ='cancelled' AND immidiate_cancelled ='yes' )) and sitter_payment_status='unpaid'  $query_sitterId ";
        
        $res = $this->db->query($sql);


        // echo $sql;die;

        $total = $res->fetchAll();
        $total_count = count($total);

        //$sql = "select job_start_date,job_end_date,client_user_id,completed_date,firstname,sitter_payment_status,lastname,job_id,sitter_user_id,rate,sitter_rate_pre,TIMEDIFF(completed_date,job_start_date) as hr from jobs  left join users on jobs.sitter_user_id=users.userid    where job_status='completed' and sitter_payment_status='unpaid' $query_sitterId LIMIT  $start,{$sort['rows']} ";
        $sql = "select job_start_date,job_end_date,client_user_id,completed_date,immidiate_cancelled,cancelled_date,firstname,sitter_payment_status,lastname,job_id,sitter_user_id,rate,sitter_rate_pre,TIMEDIFF(completed_date,job_start_date) as hr from jobs join users on jobs.sitter_user_id=users.userid where (job_status='completed'  OR ( job_status ='cancelled' AND immidiate_cancelled ='yes' ) ) and sitter_payment_status='unpaid' $query_sitterId LIMIT  $start,{$sort['rows']} ";
        
        $result = $this->db->query($sql);
        $total = $result->fetchAll();

        $results = array('total' => $total_count, 'rows' => $total);
        return $results;
    }

    /* -------------------function to update total earned in ---------------- */

    public function update_sitter_total_earned($sitter_id, $amount) {
        $sql = "update sitters set earnings = earnings + $amount where userid=$sitter_id";


        $this->db->query($sql);
    }

    /* --------------------function to update sitter payment status------------------------------------- */

    public function update_sitter_payment_status($sitter_id, $job_id) {
        $sql = "select rate,sitter_rate_pre,job_id,job_start_date,job_end_date,TIMEDIFF(completed_date,job_start_date) as hr,TIMEDIFF(job_end_date,job_start_date) as hr_pre from jobs where job_id IN ($job_id)";

        $res = $this->db->query($sql);

        $payment_details = $res->fetchAll();



        foreach ($payment_details as $details) {

            $rate = $details['rate'];
            if ($rate == null) {
                $rate = $details['sitter_rate_pre'];
            }


            if ($details['hr'] <= 0) {
                $amount = $rate * $details['hr_pre'];
            } else {
                $amount = $rate * $details['hr'];
            }


            $j_id = $details['job_id'];

            $sql = "update jobs set sitter_payment_status='paid',total_paid=$amount,job_status = CASE 
            WHEN client_payment_status = 'paid' THEN 'closed'  WHEN client_payment_status = 'unpaid' THEN 'completed'  end where sitter_user_id=$sitter_id and job_id=$j_id";

            $res = $this->db->query($sql);

            $transaction_data = array();

            $transaction_data['user_id'] = $sitter_id;
            $transaction_data['job_id'] = $details['job_id'];
            $transaction_data['transaction_type'] = 'Dr.';
            $transaction_data['amount'] = $amount;
            $transaction_data['date_added'] = date('Y-m-d h:i:s');
            //function to insert in to payment history //after
            $this->transaction_history = new Zend_Db_Table('transaction_history');
            $this->transaction_history->insert($transaction_data);
        }
    }

    /* ----------------------------function to insert in to the sitetr_paymrnt details--------------------------- */

    public function insert_sitter_payment_details($insert_data) {

        $this->sitterPaymentTable = new Zend_Db_Table('sitter_payment_details');
        $this->sitterPaymentTable->insert($insert_data);

        $id = $this->sitterPaymentTable->getAdapter()->lastInsertId();

        return $id;
    }

    /* ----------------------------function to get payment history of sitters--------------------------- */

    public function sitter_payment_history($search = array(), $filter = array(), $sort = array()) {



        if ((!empty($search['userid'])) && ($search['userid'] != 0)) {
            $query_sitterId = 'and sitter_id = ' . $search['userid'];
        } else {
            $query_sitterId = "";
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



        $sort['rows'] = (int) $search['rows'] == 0 ? 10 : (int) $search['rows'];
        $sort['page'] = (int) $search['page'] == 0 ? 1 : (int) $search['page'];
        $filter['sort'] = (int) $search['sort'] == 0 ? 1 : $search['sort'];
        if ($search['page'] > 1) {
            $start = ($sort['page'] - 1) * $sort['rows'];
        } else
        if ($sort['page'] == 1 or $sort['page'] == 0) {
            $start = 0;
        }
        $sql = "select * from sitter_payment_details left join users on users.userid=sitter_payment_details.sitter_id where users.status ='active' $query_sitterId  $search_query";



        $res = $this->db->query($sql);
        $totalrecord = $res->fetchAll();
        $total_count = count($totalrecord);

        $sql = "select * from sitter_payment_details left join users on users.userid=sitter_payment_details.sitter_id where users.status ='active' $query_sitterId $search_query LIMIT  $start,{$sort['rows']} ";

        $result = $this->db->query($sql);
        $total = $result->fetchAll();

        //$sqla = "select * from sitter_payment_details left join users on users.userid=sitter_payment_details.sitter_id where users.status ='active' $query_sitterId $search_query ";
        // $results = $this->db->query($sqla);
        // $record = $results->fetchAll();




        $results = array('total' => $total_count, 'rows' => $total, 'record' => $totalrecord);
        return $results;
    }

    /* -----------------------------function to get payments details by id---------------------- */

    public function get_payment_details($payment_id) {
        $sql = "select firstname,lastname,sitter_id,job_ids,total_paid,date_added,sitter_payment_details_id,check_number,wire_number from sitter_payment_details left join users on sitter_payment_details.sitter_id=users.userid where sitter_payment_details_id=$payment_id";

        $res = $this->db->query($sql);
        $total = $res->fetchAll();
        return($total);
    }

    /* ------------------------public function to get list of all active sitters----------------------------------------- */

    public function get_all_sitters() {
        $sql = "select userid,username,password,firstname,lastname from users where status='active' and  usertype='S' ";

        $res = $this->db->query($sql);
        $total = $res->fetchAll();
        return $total;
    }

    /* ----------------------to send email/message to sitters--------------------------------- */

    public function send_message($sitter, $message) {
        if (!empty($sitter)) {
            foreach ($sitter as $user_id) {
                $res = $this->db->query('select firstname,lastname,email from users where userid=' . $user_id);
                $userInfo = $res->fetchAll();
                $userInfo = $userInfo[0];

                //print_r($userInfo);die();
                $this->hbSettings = new Hb_Settings();
                $mailTemplate = $this->hbSettings->getMailTemplates(array('mail_name' => 'reset_password'));
                $mailTemplate = $mailTemplate[0];

                $from = $mailTemplate['from'];
                $cc = explode(',', $mailTemplate['cc']);
                $Bcc = explode(',', $mailTemplate['Bcc']);

                $to = $userInfo['username'];
                $subject = $mailTemplate['subject'];

                $to_replace = array('{firstname}', '{lastname}', '{message}');
                $replace_with = array($userInfo['firstname'], $userInfo['lastname'], $message);

                $text = str_ireplace($to_replace, $replace_with, $mailTemplate['description']);

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

                $mail->addTo($to, "{$userInfo['firstname']} {$userInfo['lastname']}");

                $mail->setSubject($subject);


                $mail->send();
            }
        }
    }

    //added by namrata for paysitters section
    public function findkeyvalue($array, $key, $val) {
        foreach ($array as $item)
            if (isset($item[$key]) && $item[$key] == $val)
                return true;
        return false;
    }
    
    public function assign_open_job($sitter)
    {
    	$res = $this->db->query("select *,job_id,client_user_id as client_id from jobs where (job_status = 'pending' or job_status = 'new') and is_special = '0' and job_start_date >'".date('Y-m-d H:i:s')."' and job_id not in (select job_id from job_sent where sent_to=".$sitter.")");
    	$jobs = $res->fetchAll();
   
    	if(count($jobs)>0)
    	{	
    		$date=date('Y-m-d H:i:s');
    		foreach($jobs as $job)
	    	{
	    		$values[] = "(".$job['job_id'].",".$sitter.",".$job['client_id'].",'".$date."','new')";
	    		$sql = "update jobs set total_assigned = ifnull(total_assigned,0)+1, job_status='pending' where job_id =". $job['job_id'];
	    		$res =$this->db->query($sql);
	    	}
	    	
	    	$values = implode(',', $values);
	    	$sql1 = "insert ignore into job_sent (`job_id`, `sent_to`, `sent_by`,`sent_date`, `sent_status`) values $values";
	    	$this->db->query($sql1);
    	}
    }
    
    
    public function searchnew($search = array(), $filter = array(), $sort = array()) {
    
    	$search['status'] = in_array($search['status'], array('active', 'inactive', 'deleted', 'unapproved')) ? '("' . $search['status'] . '")' : '  ("active","unapproved","inactive")';
    
    	$sort['rows'] = (int) $search['rows'] == 0 ? 10 : (int) $search['rows'];
    
    	$sort['page'] = (int) $search['page'] == 0 ? 1 : (int) $search['page'];
    
    	$filter['sort'] = (int) $search['sort'] == 0 ? 1 : $search['sort'];
    
    	$search_skill='';
    	if (!empty($search['skills'])) {
    		//getting length of string
    		if($search['skills']=='special_need_exp')
    			$search_skill = "and (".$search['skills']."!= '' and ".$search['skills']." is not null)";
    		else
    			$search_skill = "and (".$search['skills']."= 'yes' or ".$search['skills']."='1')";
    		
    	}
    	
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
    	
    	$orderby = array('key' => (in_array($search['key'], array('u.userid', 'firstname', 'joining_date')) ? $search['key'] : 'firstname' ), 'odr' => (in_array($search['odr'], array('asc', 'desc')) ? $search['odr'] : 'asc' ));
    
    	$search_query = array_diff($search_query, array(''));
    	if (!empty($search_query)) {
    		$search_query = ' and (' . implode(' and ', $search_query) . ' )';
    	} else
    		$search_query = '';
    
    	if ($search['page'] > 1) {
    		$start = ($sort['page'] - 1) * $sort['rows'];
    	} else
    		if ($sort['page'] == 1 or $sort['page'] == 0) {
    		$start = 0;
    	}
    
    	if((isset($search['area_prefer']) && !empty($search['area_prefer']) )|| (isset($search['lang_prefer']) && !empty($search['lang_prefer'])))
    	{    		
    		if(!empty($search['area_prefer']))
    		{
    			$prefer_join = ' join sitter_preference sp ';
    			$prefer_join_on = ' and sp.sitter_user_id=u.userid  ';
    			$prefer_query = ' and sp.prefer_id =' . $search['area_prefer'] . ' ';
    		}
    		if(!empty($search['lang_prefer']))
    		{
    			$prefer_join1 = ' join sitter_preference sp2 ';
    			$prefer_join_on1 = ' and sp2.sitter_user_id=u.userid  ';
    			$prefer_query1 = ' and sp2.prefer_id =' . $search['lang_prefer'] . ' ';
    		}
    	}
    	
    	$sql = "select SQL_CALC_FOUND_ROWS *,u.userid as sitter_id, ifnull(area_preference,'') as area_preferences, ifnull(language_preference,'') as language_preferences from users u 
    	
    	left join ( SELECT sitter_user_id, GROUP_CONCAT( prefer_name ) area_preference
    	FROM sitter_preference sp
    	JOIN preference_master pm ON ( sp.prefer_id = pm.prefer_id )
    	WHERE pm.group_id =4
    	GROUP BY sitter_user_id ) as sa on sa.sitter_user_id = u.userid 
    	
    	left join ( SELECT sitter_user_id, GROUP_CONCAT( prefer_name ) language_preference
    	FROM sitter_preference spl
    	JOIN preference_master pml ON ( spl.prefer_id = pml.prefer_id )
    	WHERE pml.group_id =5
    	GROUP BY sitter_user_id ) as sl on sl.sitter_user_id = u.userid
    	
    	join sitters s $prefer_join $prefer_join1 on(u.userid=s.userid $prefer_join_on $prefer_join_on1 ) where u.usertype='S' and status in {$search['status']} $search_query $search_skill $prefer_query  $prefer_query1 group by u.userid  order by {$orderby['key']} {$orderby['odr']} LIMIT $start,{$sort['rows']} ";
   
    	$res = $this->db->query($sql);
    
    	$sitters = $res->fetchAll();
    	$res = $this->db->query('SELECT FOUND_ROWS() as total');
    
    	$total = $res->fetchAll();
    	$i=0;
    	foreach($sitters as $row)
    	{
    		$skills=array();
    		if($row['cpr_holder']=='yes')
    		{
    			$skills[]='CPR Certification-Infant/Toddler';
    		}
    		if($row['cpr_adult']=='yes')
    		{
    			$skills[]='CPR Certification- Adult';
    		}
    		if($row['first_aid_cert']=='yes')
    		{
    			$skills[]='First Aid Certification';
    		}
    		if($row['water_certification']=='yes')
    		{
    			$skills[]='Water Certification';
    		}
    		if($row['hampton_babysitter_training']=='yes')
    		{
    			$skills[]='Hamptons Babysitter Training';
    		}
    		if($row['special_need_exp']!='' && $row['special_need_exp']!=null)
    		{
    			$skills[]='Special Needs Experienced';
    		}
    		if($row['language_preferences']!='')
    		{
    			$skills[]=str_replace(',', ', ', $row['language_preferences']);
    		}
    		$sitters[$i]['skills']=implode(', ',$skills);
    		$sitters[$i]['area_preferences']=str_replace(',', '/ ', $row['area_preferences']);
    		$i++;
    	}
    	$results = array('total' => $total[0]['total'], 'rows' => $sitters);
    	return $results;
    }
    
    function sitterbooking($sitter_id)
    {
    	$sql="select job_id, job_id title,job_status, job_start_date start, job_end_date end, client_user_id, CONCAT(firstname,' ', lastname) client_name from jobs join users on jobs.client_user_id = users.userid where job_status='confirmed' and sitter_user_id = $sitter_id";
    	//print_r($sql);die;
    	$res = $this->db->query($sql);
    	$total = $res->fetchAll();
    	$i=0;
    	foreach($total as $row)
    	{
    		$total[$i]['url'] = ADMIN_URL."client/events/user/".$row['client_user_id']."/modify/".$row['job_id'];
    		//$total[$i]['color']="green";
    		
    		$lowerlimit1 =strtotime('-3 hours',strtotime($row['start']));
    		$upperLimit1= strtotime('+3 hours',strtotime($row['end']));
    		if( $lowerlimit1 <= time() && time() <= $upperLimit1)
    		{
    			$total[$i]['color']="green";
    		}
    		else
    		{
    			$total[$i]['color']='#337ab7';
    		}
    		$i++;
    	}
    	return $total;
    }
    
    public function searchByPreferencenew(array $prefer_array,$jobid , $skills , $sitter_name,$search_job) {
    	$results = array();
    	$prefer = implode(',', $prefer_array);
    	if ($prefer != '') {
    		$prefer_query = ' and sp.prefer_id in(' . $prefer . ')';
    		$prefer_join_on = ' join sitter_preference sp join preference_master pm on(sp.sitter_user_id=u.userid and pm.prefer_id=sp.prefer_id ) ';
    	}
    	
    	if($search_job )
    	{ 
    		if(is_numeric($search_job))
	    	{
	    		$job_join= " join jobs j on j.sitter_user_id=u.userid ";
	    		$search_job_query = " and job_id = $search_job ";
	    	}
	    	else{
	    		return array();
	    	}
    	}
    	
    	if(isset($sitter_name) && $sitter_name!='')
    		$search_name = " and (firstname like '%$sitter_name%' or lastname like '%$sitter_name%')";
    	if(isset($skills) && $skills!='')
    	{
    		$skills_join = ' join sitters s on s.userid=u.userid';
    		if($skills=='special_need_exp')
    			$search_skill = "and (".$skills."!= '' and ".$skills." is not null)";
    		else
    			$search_skill = " and (".$skills." = 'yes' or ".$skills." = '1' ) ";
    	}
    	
    	//$sql = "select  *,u.userid as sitter_id, if(sent_to is null,'Assign','Dispatched') as is_sent  from users u left join (select sent_to from job_sent where job_id='$jobid') as js on u.userid=js.sent_to  $prefer_join_on $skills_join $job_join where u.usertype='S'  and status='active' $prefer_query $search_name $search_skill  $search_job_query order by u.firstname";
    	
    	$sql = "select  *,u.userid as sitter_id from users u left join (select sent_to from job_sent where job_id='$jobid') as js on u.userid=js.sent_to  $prefer_join_on $skills_join $job_join where u.usertype='S'  and status='active' $prefer_query $search_name $search_skill  $search_job_query order by u.firstname";
    	$res = $this->db->query($sql);
    	
    	$sitters = $res->fetchAll();
    	foreach ($sitters as $sitter) {
    		if (!isset($results[$sitter['userid']]))
    			$results[$sitter['userid']] = $sitter;
    		$results[$sitter['userid']]['prefer'][$sitter['prefer_id']] = $sitter['prefer_name'];
    		$results[$sitter['userid']]['jobs']=$this->findbookedjobs($sitter['userid'],$jobid);// check for no of jobs of same day and in 4hr time frame.
    		//echo '<br>';
    	}
    
    	//print_r($results);
    	return $results;
    }
    
    //  to find no of job within 4 hrs timeframe and no of job of same day.
    function findbookedjobs($sitter_id,$job_id)
    {
    	$sql1= "Select j1.job_id from jobs j, jobs j1
    	where (
    	(j1.job_end_date
    	BETWEEN DATE_SUB( j.job_start_date, INTERVAL 239 MINUTE )
    	AND DATE_ADD( j.job_end_date, INTERVAL 239 MINUTE )
    	)
    	OR
    	(j1.job_start_date
    	BETWEEN DATE_SUB( j.job_start_date, INTERVAL 239 MINUTE )
    	AND DATE_ADD( j.job_end_date, INTERVAL 239 MINUTE )
    	)
    	)
    	AND j1.job_id != j.job_id
    	AND j1.job_id !=".$job_id."
    	AND j1.job_status =  'confirmed'
    	AND j.job_id =".$job_id."
    	AND j1.sitter_user_id=".$sitter_id;
    	$job_time = $this->db->query($sql1);
    	$job_time = $job_time->fetchAll();
    	
    	$data['jobsIn4hrs']=count($job_time);
    	
    	$sql = "SELECT j1.job_id, date_format( j1.job_start_date, '%Y-%m-%d' ) start_date
    	FROM jobs j, jobs j1
    	WHERE date_format( j.job_start_date, '%Y-%m-%d' ) = date_format( j1.job_start_date, '%Y-%m-%d' )
    	AND j1.job_id != j.job_id
    	AND j1.job_status =  'confirmed'
    	AND j.job_id =".$job_id."
    	AND j1.sitter_user_id=".$sitter_id;
    	$no_of_job = $this->db->query($sql);
    	$no_of_job = $no_of_job->fetchAll();
    	
    	$data['jobsOfday']=count($no_of_job);
    	
    	return $data;
    }

}
