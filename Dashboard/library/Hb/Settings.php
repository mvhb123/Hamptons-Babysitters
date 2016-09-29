<?php

class Hb_Settings
{

    public function __construct()
    {
        $this->db = Zend_Db_Table::getDefaultAdapter();


    }

    public function getSite()
    {
        $res = $this->db->query('select * from configuration ');
        $result = $res->fetchAll();

        foreach ($result as $r) {
            $data[$r['key']] = $r;
        }
        return $data;
    }

    public function updateSite($data)
    {

        $this->configTable = new Zend_Db_Table('configuration');

        foreach ($data as $key => $value) {
            $where = $this->configTable->getAdapter()->quoteInto('key = ?', $key);
            $update = array('value' => $value);
            $this->db->query("update configuration set value= '$value' where `key`='$key'");
            //$this->configTable->update($update,$where);

        }

    }

    public function addPreference($data)
    {

        $this->preferenceTable = new Zend_Db_Table('preference_master');
        $insert = array(
            'group_id' => $data['group_id'],
            'prefer_name' => $data['prefer_name'],
            'prefer_desc' => $data['prefer_desc'],
        );
        $this->preferenceTable->insert($insert);
        return true;

    }

    public function updatePreference($data, $prefer_id)
    {
        $this->preferenceTable = new Zend_Db_Table('preference_master');
        $update = array(

            'prefer_name' => $data,

        );
        $where = $this->db->quoteInto('prefer_id = ?', $prefer_id);
        $this->preferenceTable->update($update, $where);
        return true;
    }

    public function deletePreference($prefer_id)
    {
        $sql = "select * from `job_preference` where prefer_id =$prefer_id";

        $res = $this->db->query($sql);
        $result = $res->fetchAll();
        if (!empty($result)) {
            return false;
        } else {
            $this->db->query('delete from preference_master where prefer_id=' . $prefer_id);
            return true;
        }
    }

    public function getPreferences($client = false)
    {

        //$visible_to_client = 1;
        $visible_to_client = '';
        if ($client) {

            $visible_to_client = "where visible_to_client = 1 ";
        }
        $sql = "SELECT * FROM `preference_group` $visible_to_client ";

        $res = $this->db->query($sql);

        $groups = $res->fetchAll();

        $sql = "select * from preference_master ";
        $res = $this->db->query($sql);

        $preferences = $res->fetchAll();

        foreach ($groups as $group)
            $grp[$group['group_id']] = $group;
        foreach ($preferences as $p)
            if (in_array($p['group_id'], array_keys($grp)))
                $grp[$p['group_id']]['prefer'][$p['prefer_id']] = $p;

        return $grp;


    }

    public function updateMailTemplate($data, $mail_id)
    {

        $this->mailTable = new Zend_Db_Table('mail_templates');
        $where = $this->mailTable->getAdapter()->quoteInto('mail_id = ?', $mail_id);
        $content = $data['body'];
        $data = array(
            'label' => $data['label'],
            'description' => $data['body'],
            'to' => $data['to'],
            'from' => $data['from'],
            'from_name' => $data['from_name'],
            'cc' => $data['cc'],
            'bcc' => $data['bcc'],
            'subject' => $data['subject'],

        );

        $this->mailTable->update($data, $where);

        $mail = $this->getMailTemplates(array('mail_id' => $mail_id));
        $mail = $mail[0];
        //file_put_contents(SITE_ABS_PATH.$mail['path'],$content);
        //echo $data['body'];
        //print_r( $_POST);die;

    }

    public function getMailTemplates($search = array())
    {
        if ($search['mail_id'])
            $where = " and mail_id = {$search['mail_id']}";

        if ($search['mail_name'])
            $where = " and mail_name = '{$search['mail_name']}'";

        $sql = "SELECT *
FROM `mail_templates` where 1 $where order by label asc";
        $res = $this->db->query($sql);
        $results = $res->fetchAll();
        if ($where != '') {
            $results[0]['description'] = stripcslashes($results[0]['description']);
        }
        return $results;
    }

    public function getStates($country_id = 223)
    {

        if (!is_numeric($country_id)) {
            $res = $this->db->query('select * from country where iso_code_2="' . $country_id . '"');
            $country = $res->fetchAll();
            $country_id = $country[0]['country_id'];
        }
        $country_id = $country_id ? $country_id : 223;
        $res = $this->db->query('select * from states where country_id=' . (int)$country_id . ' ');
        $results = $res->fetchAll();
        foreach ($results as $result) {
            $data[$result['zone_id']] = $result;
        }

        return $data;
    }

    public function getCountries()
    {


        $res = $this->db->query('select * from country ');
        return $results = $res->fetchAll();

    }

    public function refreshCache()
    {
        $clients = new Hb_Client();
        $clientList = $clients->search(array('rows' => 20000));

        //print_r($clientList);
        foreach ($clientList['rows'] as $client) {
            $userid = (int)$client['userid'];
            $sql = "update `clients_detail` set open_requests=(select count(*) from `jobs`  where client_user_id = '" . (int)$client['userid'] . "' and `job_status` in ('new','pending','confirmed')) where `userid`= " . (int)$client['userid'];
            //die();
            $this->db->query($sql);

            $sql = "update clients_detail set available_credits =(select sum(slots) from clients_subscription where end_date > now()  and userid=$userid) where  userid=$userid";
            $this->db->query($sql);
            //die;
        }

        $this->db->query("delete from job_sent where job_id in
							( SELECT job_id FROM jobs WHERE job_status in('completed','cancelled') )");


        $this->db->query("UPDATE sitters s SET completed_jobs =
							( SELECT count( * ) FROM jobs WHERE job_status = 'completed' AND sitter_user_id = s.userid )");

        $this->db->query("UPDATE clients_detail c SET events_compeleted =
							( SELECT count( * ) FROM jobs WHERE job_status = 'completed' AND client_user_id = c.userid )");

        $this->db->query("UPDATE sitters s SET available_jobs =
							( SELECT count( * ) FROM job_sent WHERE sent_status = 'new' AND sent_to = s.userid )");


        //$this->db->query("UPDATE sitters s set earnings=
        //						(SELECT sum( total_paid ) as  FROM jobs WHERE sitter_user_id = s.userid");


    }

    public function getskills()
    {

        $skills = array('cpr_holder' => 'CPR Certification-Infant/Toddler',
            'cpr_adult' => 'CPR Certification- Adult',
            'first_aid_cert' => 'First Aid Certification',
            'water_certification' => 'Water Certification',
            'hampton_babysitter_training' => 'Hamptons Babysitter Training',
            'lifeguard' => 'Lifeguard',
            'special_need_exp' => 'Special Needs Experienced');
        return $skills;

    }

}
