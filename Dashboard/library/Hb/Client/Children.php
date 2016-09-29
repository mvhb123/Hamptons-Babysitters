<?php

Class Hb_Client_Children
{

    public function __construct()
    {
        $this->childTable = new Zend_Db_Table('children');
        $this->db = $this->childTable->getAdapter();

    }


    public function isChildExists($child_id,$key="*")
    {

        if(empty($child_id))
            return false;

        $select = $this->db->select($key)->where('child_id=?',$child_id)->from('children');
        $stmt = $select->query();
        $info = $stmt->fetchObject();
        return $info;

    }


    public function add(array $child, $parent_id)
    {


        $insert = array(
            'parent_user_id' => $parent_id,
            'child_name' => $child['child_name'],
            'dob' => date('Y-m-d', strtotime($child['dob'])),
            'sex' => $child['sex'],
            'allergy_status' => $child['allergy_status'],
            'allergies' => $child['allergies'],
            'notes' => $child['notes'],
            'medicator' => $child['medicator'],
            'medicator_status' => $child['medicator_status'],
        	'special_needs'=>$child['special_needs'],
        	'special_needs_status'=>$child['special_needs_status'],
        	'fav_food'=>$child['fav_food'],
        	'fav_book'=>$child['fav_book'],
        	'fav_cartoon'=>$child['fav_cartoon']
        );

        $this->childTable->insert($insert);
        $child_id = $this->childTable->getAdapter()->lastInsertId();
        //echo '<pre>';print_r($child);
        if ($child['image']['name'] != '') {
            $orginal = CHILDREN_IMAGE_ABS_PATH . 'orginal/' . $parent_id . '__' . $child_id . '__' . $child['image']["name"];
            $thumb = CHILDREN_IMAGE_ABS_PATH . 'thumb/' . $parent_id . '__' . $child_id . '__' . $child['image']["name"];
            $full = CHILDREN_IMAGE_ABS_PATH . 'full/' . $parent_id . '__' . $child_id . '__' . $child['image']["name"];

            //added by namrata for 200
            $APP = CHILDREN_IMAGE_ABS_PATH . 'app_thumb/' . $parent_id . '__' . $child_id . '__' . $child['image']["name"];


            $this->upload($child['image'], array('orginal' => $orginal, 'thumb' => $thumb, 'full' => $full, 'app_thumb' => $APP));

            $orginal = CHILDREN_IMAGES . 'orginal/' . $parent_id . '__' . $child_id . '__' . $child['image']["name"];
            $thumb = CHILDREN_IMAGES . 'thumb/' . $parent_id . '__' . $child_id . '__' . $child['image']["name"];
            $full = CHILDREN_IMAGES . 'full/' . $parent_id . '__' . $child_id . '__' . $child['image']["name"];

            $APP = CHILDREN_IMAGES . 'app_thumb/' . $parent_id . '__' . $child_id . '__' . $child['image']["name"];


            $update = array('main_image' => $full,
                'thumb_image' => $thumb,
                'orginal_image' => $orginal,
                'orginal_image_spec' => '',
                'app_thumb' => $APP,

            );

            $where = $this->childTable->getAdapter()->quoteInto('child_id = ?', $child_id);

            $this->childTable->update($update, $where);
        }
        return true;

    }


    public function addChildNew(array $child, $parent_id)
    {


        $insert = array(
            'parent_user_id' => $parent_id,
            'child_name' => $child['child_name'],
            'dob' => date('Y-m-d', strtotime(str_replace('-', '/', $child['dob']))),
            'sex' => $child['sex'],
            'allergy_status' => $child['allergy_status'],
            'allergies' => $child['allergies'],
            'notes' => $child['notes'],
            'medicator' => $child['medicator'],
            'medicator_status' => $child['medicator_status'],
        	'special_needs'=>$child['special_needs'],
        	'special_needs_status'=>$child['special_needs_status'],
        	'fav_food'=>$child['fav_food'],
        	'fav_book'=>$child['fav_book'],
        	'fav_cartoon'=>$child['fav_cartoon']
        );
        
        $this->childTable->insert($insert);
        $child_id = $this->childTable->getAdapter()->lastInsertId();

        if ($child['image']['name'] != '') {


            $orginal = CHILDREN_IMAGE_ABS_PATH . 'orginal/' . $parent_id . '__' . $child_id . '__' . $child['image']["name"];
            $thumb = CHILDREN_IMAGE_ABS_PATH . 'thumb/' . $parent_id . '__' . $child_id . '__' . $child['image']["name"];
            $full = CHILDREN_IMAGE_ABS_PATH . 'full/' . $parent_id . '__' . $child_id . '__' . $child['image']["name"];

            $APP = CHILDREN_IMAGE_ABS_PATH . 'app_thumb/' . $parent_id . '__' . $child_id . '__' . $child['image']["name"];


            $this->upload($child['image'], array('orginal' => $orginal, 'thumb' => $thumb, 'full' => $full, 'app_thumb' => $APP));

            $orginal = CHILDREN_IMAGES . 'orginal/' . $parent_id . '__' . $child_id . '__' . $child['image']["name"];
            $thumb = CHILDREN_IMAGES . 'thumb/' . $parent_id . '__' . $child_id . '__' . $child['image']["name"];
            $full = CHILDREN_IMAGES . 'full/' . $parent_id . '__' . $child_id . '__' . $child['image']["name"];


            $APP = CHILDREN_IMAGES . 'app_thumb/' . $parent_id . '__' . $child_id . '__' . $child['image']["name"];


            $update = array('main_image' => $full,
                'thumb_image' => $thumb,
                'orginal_image' => $orginal,
                'orginal_image_spec' => '',
                'app_thumb' => $APP,


            );

            $where = $this->childTable->getAdapter()->quoteInto('child_id = ?', $child_id);

            $this->childTable->update($update, $where);
        }
        return $child_id;

    }


    public function modify(array $child, $parent_id, $child_id)
    {

        $update = array(
            'parent_user_id' => $parent_id,
            'child_name' => $child['child_name'],
            'dob' => date('Y-m-d', strtotime($child['dob'])),
            'sex' => $child['sex'],
            'allergy_status' => $child['allergy_status'],
            'allergies' => $child['allergies'],
            'notes' => $child['notes'],
            'medicator' => $child['medicator'],
            'medicator_status' => $child['medicator_status'],
        	'special_needs'=>$child['special_needs'],
        	'special_needs_status'=>$child['special_needs_status'],
        	'fav_food'=>$child['fav_food'],
        	'fav_book'=>$child['fav_book'],
        	'fav_cartoon'=>$child['fav_cartoon']
        		);
        $where = $this->db->quoteInto('child_id = ?', $child_id);
       
        $this->childTable->update($update, $where);


        if ($child['image']['name'] != '') {
            $orginal = CHILDREN_IMAGE_ABS_PATH . 'orginal/' . $parent_id . '__' . $child_id . '__' . $child['image']["name"];
            $thumb = CHILDREN_IMAGE_ABS_PATH . 'thumb/' . $parent_id . '__' . $child_id . '__' . $child['image']["name"];
            $full = CHILDREN_IMAGE_ABS_PATH . 'full/' . $parent_id . '__' . $child_id . '__' . $child['image']["name"];

            $APP = CHILDREN_IMAGE_ABS_PATH . 'app_thumb/' . $parent_id . '__' . $child_id . '__' . $child['image']["name"];
            //print_r($APP);die;
            //$this->upload($child['image'],array('orginal'=>$orginal,'thumb'=>$thumb,'full'=>$full));
            $this->upload($child['image'], array('orginal' => $orginal, 'thumb' => $thumb, 'full' => $full, 'app_thumb' => $APP));
            $orginal = CHILDREN_IMAGES . 'orginal/' . $parent_id . '__' . $child_id . '__' . $child['image']["name"];
            $thumb = CHILDREN_IMAGES . 'thumb/' . $parent_id . '__' . $child_id . '__' . $child['image']["name"];
            $full = CHILDREN_IMAGES . 'full/' . $parent_id . '__' . $child_id . '__' . $child['image']["name"];
            $APP = CHILDREN_IMAGES . 'app_thumb/' . $parent_id . '__' . $child_id . '__' . $child['image']["name"];

            $update = array('main_image' => $full,
                'thumb_image' => $thumb,
                'orginal_image' => $orginal,
                'orginal_image_spec' => '',
                'app_thumb' => $APP,

            );


            $where = $this->childTable->getAdapter()->quoteInto('child_id = ?', $child_id);

            $this->childTable->update($update, $where);
        }

        return true;
    }

    public function update(array $children, $parent_id)
    {

    }

    public function delete($child_id)
    {
    	$update = array(
    			'is_deleted' => 1);
        $this->db = $this->childTable->getAdapter();

        $where = $this->db->quoteInto('child_id = ?', $child_id);
        $this->childTable->update($update,$where);

    }

    public function getAll($client_id)
    {
        $this->db = $this->childTable->getAdapter();
        $res = $this->db->query('select * from children where is_deleted!=1 and parent_user_id=' . $client_id);
        return $res->fetchAll();

    }

    public function get($child_id)
    {
        $this->db = $this->childTable->getAdapter();
        $res = $this->db->query('select * from children where child_id=' . $child_id);
        $result = $res->fetchAll();
        if (empty($result))
            return false;
        else return $result[0];

    }

    public function upload($file, $path_array)
    {


        extract($path_array);


        copy($file["tmp_name"], $orginal);
        //move_uploaded_file($file["tmp_name"],$orginal);

        unlink($file["tmp_name"]);


        $this->resizeImage($orginal, $thumb, THUMB_SIZE_WIDTH, THUMB_SIZE_HIEGHT);
        //added
        $this->resizeImage($orginal, $app_thumb, APPTHUMB_SIZE_WIDTH, APPTHUMB_SIZE_HIEGHT);

        return $this->resizeImage($orginal, $full, LARGE_SIZE_WIDTH, LARGE_SIZE_WIDTH);


        // echo "Stored in: " . "upload/" . $file["name"];
    }

    public function resizeImage($tmpname, $dest, $width, $height)
    {


        // print_r($dest);die;
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


    /*-------------------------function to add child id to job--------------------------------*/
    public function addChildToJob($child_id, $job_id)
    {
        $values = array();
        $values['job_id'] = $job_id;
        $values['child_id'] = $child_id;

        $this->childJobTable = new Zend_Db_Table('jobs_to_childs');
        $this->childJobTable->insert($values);
        //function to update the last modified to hobs table


    }


}
