<?php

class Hb_User
{

    public $db;
    public $userTable;

    public function __construct()
    {

        $this->db = Zend_Db_Table::getDefaultAdapter();
        $this->userTable = new Zend_Db_Table('users');
        $this->userProfileTable = new Zend_Db_Table('user_profile');
        $this->addressTable = new Zend_Db_Table('address');
        //print_r($this->userTable);
    }


    /**
     * Ftech user info as per id
     *
     * @param  string|integer $user_id
     * @param boolean $cache  true/false
     * @return array         user array from cache or fresh one
     */
    public function getUserInfo($user_id,$cache = true)
    {
        if(empty($user_id))
            return false;


        if($cache==true && !empty($this->get_user_info[$user_id]))
            return $this->get_user_info[$user_id];


        $select = $this->db->select()->where('userid=?',$user_id)->from('users');
        $stmt = $select->query();
        $info = $stmt->fetchObject();

        $this->get_user_info[$user_id] = $info;
        return $info;

    }

    /****************** Old Code *******************/

    public function addUser(array $user, $status = 0)
    {

        $d = explode('/', $user['dob']);


        $insert = array(
            'username' => $user['username'],
            'password' => $user['password'],
            'firstname' => $user['firstname'],
            'middlename' => $user['middlename'],
            'lastname' => $user['lastname'],
            'dob' => $d[2] . '-' . $d[0] . '-' . $d[1],//date('Y-m-d',strtotime($user['dob'])),
            'phone' => $user['phone'],

            'usertype' => $user['usertype'],
            'status' => $status ? 'active' : 'unapproved',
            'joining_date' => date('Y-m-d H:i:s'),
            'modified_date' => date('Y-m-d H:i:s'),
            'added_by' => $user['added_by'] ? $user['added_by'] : 0,
        );

        if (Zend_Auth::getInstance()->getIdentity()->usertype == 'A')
            $insert['miscinfo'] = $user['miscinfo'];

        $this->userTable->insert($insert);
        $this->userid = $this->db->lastInsertId();
        if ($user['usertype'] == 'S') {
            $data_log = array('job_id' => '',
                'modified_by' => $this->userid,
                'modified_date' => date('Y-m-d H:i:s'),
                'modification' => 'New Sitter Application',
                'initial' => $this->userid,
                'modified' => 0
            );
            $this->db->insert('job_logs', $data_log);
        }
        return $this->userid;

    }

    public function checkUsername($username, $userid = '')
    {
        $user_query = '';
        if ((int)$userid > 0)
            $user_query = ' and userid!=' . $userid;
        //

        $sql = 'select * from users where username=' . $this->userTable->getAdapter()->quote($username) . $user_query;

        $res = $this->userTable->getAdapter()->query($sql);
        return $result = $res->fetchAll();

    }

    public function addProfile(array $profile)
    {

        $sql = "select * from user_profile where userid={$profile['userid']}";
        $res = $this->db->query($sql);
        $result = $res->fetchAll();
        if (empty($result))
            $this->userProfileTable->insert($profile);
        else {
            $where = $this->db->quoteInto('userid = ?', $profile['userid']);

            $this->userProfileTable->update($profile, $where);

        }

        return $this->db->lastInsertId();

    }

    public function deleteUser($userid)
    {

        try {
            $sql = "select * from users where userid='$userid'";
            $res = $this->db->query($sql);

            $userInfo = $res->fetchAll();
            $userInfo = $userInfo[0];

            $where = $this->db->quoteInto('userid = ?', $userid);

            if ($userInfo['status'] == 'deleted') {
                $this->userTable->delete($where);
            } else {
                $data = array('status' => 'deleted');
                $this->userTable->update($data, $where);


            }
        } catch (Exception $e) {


        }
    }

    public function updateUser(array $user, $userid)
    {
        $d = explode('/', $user['dob']);
        $insert = array(
            'username' => $user['username'],
            'firstname' => $user['firstname'],
            'middlename' => $user['middlename'],
            'lastname' => $user['lastname'],
            'dob' => $d[2] . '-' . $d[0] . '-' . $d[1],//date('Y-m-d',strtotime($user['dob'])),
            'phone' => $user['phone'],
            'usertype' => $user['usertype'],
            'status' => $user['status'],
            // 'joining_date'=>date('Y-m-d H:i:s'),
            'modified_date' => date('Y-m-d H:i:s'),
            'added_by' => $user['added_by'],
            'profile_completed' => 1
        );
        //print_r($insert);die();

        if (Zend_Auth::getInstance()->getIdentity()->usertype == 'A')
            $insert['miscinfo'] = $user['miscinfo'];

        $where = $this->db->quoteInto('userid = ?', $userid);

        $this->userTable->update($insert, $where);
        $this->userid = $userid;
        return true;

    }

    public function setCurrentCity($userid)
    {

        $addr = $this->getAddress(array('userid' => $userid, 'address_type' => 'local'));
        $addr = $addr[0];
        $update = array('current_city' => $addr['city']);

        $where = $this->db->quoteInto('userid = ?', $userid);

        $this->userTable->update($update, $where);
        $this->userid = $userid;
        return true;

    }

    public function addAddress(array $address)
    {
        $insert = array('userid' => $address['userid'] ? $address['userid'] : 1, //for modification
            'billing_name' => $address['billing_name'],
            'address_1' => $address['address_1'],
            'address_2' => $address['address_2'],
            'streat_address' => $address['streat_address'],
            'zipcode' => $address['zipcode'],
            'city' => $address['city'],
            'state' => $address['state'],
            'country' => $address['country'],
            'address_type' => $address['address_type'],
            'phone' => $address['phone'],
            'carrier_info' => $address['carrier_info'],
            'house_no' => $address['house_no'],
            'different_from_local' => (bool)$address['different_from_local'],
        );
        $this->addressTable->insert($insert);
        $addressId = $this->db->lastInsertId();
        $this->setCurrentCity($address['userid']);
        return $addressId;
    }

    public function updateAddress(array $address, $addressId)
    {

        $insert = array('userid' => $address['userid'], //for modification
            'billing_name' => $address['billing_name'],

            'address_1' => $address['address_1'],
            'address_2' => $address['address_2'],
            'streat_address' => $address['streat_address'],
            'zipcode' => $address['zipcode'],
            'city' => $address['city'],
            'state' => $address['state'],
            'country' => $address['country'],
            'address_type' => $address['address_type'],
            'phone' => $address['phone'],
            'carrier_info' => $address['carrier_info'],
            'house_no' => $address['house_no'],
            'different_from_local' => (bool)$address['different_from_local'],
        );

        $where = $this->db->quoteInto('address_id = ?', $addressId);

        $this->addressTable->update($insert, $where);
        $this->setCurrentCity($address['userid']);
    }

    public function getAddress(array $search)
    {
        $query = '';
        $types_query = '';
        $this->db = $this->addressTable->getAdapter();
        if ($search['address_id'] > 0) {
            $query = ' and address_id=' . $search['address_id'];
        }
        if (is_array($search['address_type'])) {
            $types_query = ' and address_type in("' . implode('","', $search['address_type']) . '") ';
        } else if ($search['address_type'] != '') {
            $types_query = ' and address_type in("' . $search['address_type'] . '") ';

        }

        $sql = "select *,s.name as state_name from address JOIN states s ON address.state = s.zone_id where userid='{$search['userid']}' $query $types_query";
        $res = $this->db->query($sql);

        return $res->fetchAll();
    }

    public function deleteAddress($addressId)
    {
    }

    public function getUser(array $search)
    {
        $sql = "select * from users where username='{$search['username']}'";
        $res = $this->db->query($sql);

        return $res->fetchAll();
    }

    public function getEducationTable()
    {
        $this->educationTable = new Zend_Db_Table('education');
    }

    public function addEducation($data)
    {
        $insert = array(
            'userid' => $data['userid'],
            'institution' => $data['institution'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'degree' => $data['degree'],
        );
        $this->educationTable->insert($insert);
    }

    public function upload($file, $user_id)
    {


        $path = PROFILE_IMAGE_ABS_PATH . 'orginal/' . $user_id . '__' . $file["name"];
        $thumb = PROFILE_IMAGE_ABS_PATH . 'thumb/' . $user_id . '__' . $file["name"];
        $full = PROFILE_IMAGE_ABS_PATH . 'full/' . $user_id . '__' . $file["name"];
        //bynamratra
        $appthumb = PROFILE_IMAGE_ABS_PATH . 'app_thumb/' . $user_id . '__' . $file["name"];
        // print_r($path);
        //print_r($file);die;

        //ADDED BY ANJALI TO FIX orientation
        $image = imagecreatefromstring(file_get_contents($file["tmp_name"]));
        $exif = exif_read_data($file["tmp_name"]);
        if (!empty($exif['Orientation'])) {
            switch ($exif['Orientation']) {
                case 8:
                    $image = imagerotate($image, 90, 0);
                    break;
                case 3:
                    $image = imagerotate($image, 180, 0);
                    break;
                case 6:
                    $image = imagerotate($image, -90, 0);
                    break;
            }
        }
        imagejpeg($image, $file["tmp_name"], 90);
        //end ADDED BY ANJALI TO FIX orientation

        copy($file["tmp_name"], $path) or die('cannot copy files');
        unlink($file["tmp_name"]);

        $this->resizeImage($path, $thumb, THUMB_SIZE_WIDTH, THUMB_SIZE_HIEGHT);
        $this->resizeImage($path, $appthumb, APPTHUMB_SIZE_WIDTH, APPTHUMB_SIZE_HIEGHT);
        return $this->resizeImage($path, $full, LARGE_SIZE_WIDTH, LARGE_SIZE_HIEGHT);


        // echo "Stored in: " . "upload/" . $file["name"];
    }

    public function resizeImage($tmpname, $dest, $width, $height)
    {
        $gis = getimagesize($tmpname);
        $type = $gis[2];
        switch ($type) {
            case "1":
                $imorig = imagecreatefromgif($tmpname);
                break;
            case "2":
                $imorig = imagecreatefromjpeg($tmpname);
                break;
            case "3":
                $imorig = imagecreatefrompng($tmpname);
                break;
            default:
                $imorig = imagecreatefromjpeg($tmpname);
        }

        $x = imagesx($imorig);
        $y = imagesy($imorig);


        $im = imagecreatetruecolor($width, $height);
        if (imagecopyresampled($im, $imorig, 0, 0, 0, 0, $width, $height, $x, $y))
            if (imagejpeg($im, $dest))
                return true;
            else
                return false;
    }

    public function generatePassword($userid)
    {
        $length = 9;
        $strength = 1;
        $vowels = 'aeuy';
        $consonants = 'BDGHJLMNPQRSTVWXZ';
        if ($strength & 1) {
            $consonants .= 'BDGHJLMNPQRSTVWXZ';
        }
        if ($strength & 2) {
            $vowels .= "AEUY";
        }

        $password = '';
        $alt = time() % 2;
        for ($i = 0; $i < $length; $i++) {
            if ($alt == 1) {
                $password .= $consonants[(rand() % strlen($consonants))];
                $alt = 0;
            } else {
                $password .= $vowels[(rand() % strlen($vowels))];
                $alt = 1;
            }
        }
        //$password='142536';//sha1($password);
        $res = $this->db->query('select * from users where userid=' . $userid);
        $userInfo = $res->fetchAll();
        $userInfo = $userInfo[0];

        $where = $this->db->quoteInto('userid = ?', $userid);

        $this->userTable->update(array('password' => $password, 'password_reset' => 1), $where);
        $this->hbSettings = new Hb_Settings();
        $mailTemplate = $this->hbSettings->getMailTemplates(array('mail_name' => 'reset_password'));
        $mailTemplate = $mailTemplate[0];

        $from = $mailTemplate['from'];

        $cc = explode(',', $mailTemplate['cc']);
        $Bcc = explode(',', $mailTemplate['Bcc']);

        $to = $userInfo['username'];
        $subject = $mailTemplate['subject'];

        $to_replace = array('{firstname}', '{lastname}', '{password}');
        $replace_with = array($userInfo['firstname'], $userInfo['lastname'], $password);

        $text = str_ireplace($to_replace, $replace_with, $mailTemplate['description']);

        $mail = new Zend_Mail();
        //$text = "Hello {$userInfo['firstname']} {$userInfo['lastname']}, <br> Your New Password:$password <br> Thanks,<br>HamptonsBabysitters.com Administrator";
        $mail->setBodyText($text);

        $mail->setBodyHtml($text);

        $mail->setFrom($from);

        if (!empty($cc)) foreach ($cc as $c) $mail->addCc($c);
        if (!empty($bcc)) foreach ($bcc as $c) $mail->addBcc($c);

        $mail->addTo($to, "{$userInfo['firstname']} {$userInfo['lastname']}");

        $mail->setSubject($subject);
        $mail->send();

    }

    public function changePassword($userid, $password)
    {
        try {
            $this->db->query('update users set password = "' . $password . '", password_reset=0 where userid="' . $userid . '"');
        } catch (Execption $e) {
            die('Error changing password..');
        }
    }

    public function addressMailString($address)
    {
        return array(
            //'{streat_address}'=>ucwords($address['billing_name']).'<br />'.($address['address_1'] !='' ? ucwords($address['address_1']).'<br>' : '').ucwords($address['streat_address']),
            //'{street_address}'=>ucwords($address['billing_name']).'<br />'.($address['address_1'] !='' ? ucwords($address['address_1']).'<br>' : '').ucwords($address['streat_address']),
            //'{cross_street}'=>$address['address_1'] !='' ? ucwords($address['address_1']).'<br>' : '',
            //'{city}'=>ucwords($address['city']),
            //'{state}'=>ucwords($address['state']),
            //'{zipcode}'=>$address['zipcode'],
            '{street_address}' => ucwords($address['streat_address']),
            '{cross_street}' => $address['address_1'] != '' ? ucwords($address['address_1']) : '',
            '{city}' => ucwords($address['city']),
            '{state}' => ucwords($address['state_name']),
            '{zipcode}' => $address['zipcode'],
            '{hotel_name}' => $address['billing_name'] != '' ? ucwords($address['billing_name']) : '',
        );
    }

    public function age($dob)
    {


        $diff = abs(strtotime(date('Y-m-d')) - strtotime($dob));


        $years = floor($diff / (365 * 60 * 60 * 24));
        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
        $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

        if ($years > 0)
            return $ret = $years . 'yrs ' . (($month > 0) ? '' . $month . ' months' : '');
        else return ($month > 0) ? '' . $month . ' months ' : '';

    }

    public function getSpecialNeeds()
    {
        $sql = "select * from special_needs";
        $res = $this->db->query($sql);
        return $res->fetchAll();
    }

}
