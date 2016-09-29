<?php
class Hb_Notification{

	public function __construct(){

            $this->notificationTable = new Zend_Db_Table('notification');
            $this->db = $this->notificationTable->getAdapter();
	}

        //function to insert data to notification table
        public function insert_notification_data($insert)
        {
            $this->notificationTable->insert($insert);
            $noti_id = $this->db->lastInsertId();
            return $noti_id;
        }

        //get unread notification coiunt

        public function get_unread_count($user_id)
        {
            $query="select count(notification_id) as noti_count from notification where userid=$user_id and is_read='0'";
            $res = $this->db->query($query);
            $result = $res->fetchAll();
          //  print_r((int)$result[0]['noti_count']);die;
            return (int)$result[0]['noti_count'];
        }

        public function getJobStatus($job_id)
        {
        	$query="select job_status,job_start_date,job_end_date from jobs where job_id=".$job_id;
        	$res = $this->db->query($query);
        	$result = $res->fetchAll();
        	$start_date =strtotime($result[0]['job_start_date']);
        	$lowerlimit =strtotime('-3 hours',strtotime($result[0]['job_start_date']));
        	$upperLimit= strtotime('+3 hours',strtotime($result[0]['job_end_date']));
    		if($result[0]['job_status']=='confirmed')
    		{
    			if( $lowerlimit <= time() && time() <= $upperLimit)
    			{
    				return 'active';
    			}
        		else
        		{
        			return 'inactive';
        		}

    		}
    		else
    			return $result[0]['job_status'];
        }

}
