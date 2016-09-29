<?php

class Hb_Reports
{

    public function __construct()
    {

        $this->jobTable = new Zend_Db_Table('jobs');
        $this->db = $this->jobTable->getAdapter();

        $this->db = Zend_Db_Table::getDefaultAdapter();
    }
    public function searchv2($count = FALSE, $search_data = array(), $limit = 10, $offset = 0, $order = "earnings", $order_str = 'DESC')
    {

        // sub query start here
        $query_sitterId = '';

        $pos = strpos($search_data['userid'],'15_most_productive');

        if ($pos !== FALSE) {
            $search_data['userid'] = str_replace('15_most_productive',"",$search_data['userid']);
            $sitters_id = $this->sittersv2(true);
        }
            $search_data['userid'] = explode(",",$search_data['userid']);
            if(!is_array($search_data['userid']))
            {
                $search_data['userid'] = array();
            }
            if(empty($sitters_id) || !is_array($sitters_id))
            {
                $sitters_id = array();
            }
            $search_data['userid'] =  array_merge($sitters_id,$search_data['userid']) ;
            //$query_sitterId = 'and sitters.userid = ' . $search_data['userid'];
            $search_data['userid'] =array_filter( $search_data['userid']);
            $sitterids = implode(',', $search_data['userid']);


        if(!empty($sitterids))
            $query_sitterId = 'and sitters.userid in (' . $sitterids . ')';


        $this->start_end_date_check($search_data['job_start_date'],$search_data['job_end_date']);
        if (trim($search_data['job_start_date']) != '' && trim($search_data['job_end_date']) == '') {
            $search_data['job_start_date'] = str_replace("-", "/", $search_data['job_start_date']);

            $search_data_query[] = ' date(job_start_date)  > date("' . date('Y-m-d', strtotime($search_data['job_start_date'])) . '")';
        } else if (trim($search_data['job_start_date']) == '' && trim($search_data['job_end_date']) != '') {
            $search_data['job_end_date'] = str_replace("-", "/", $search_data['job_end_date']);
            $search_data_query[] = ' date(job_end_date)  < date("' . date('Y-m-d', strtotime($search_data['job_end_date'])) . '")';
        } // else '';
        else if (trim($search_data['job_start_date']) != '' && trim($search_data['job_end_date']) != '') {
            $search_data['job_start_date'] = str_replace("-", "/", $search_data['job_start_date']);
            $search_data['job_end_date'] = str_replace("-", "/", $search_data['job_end_date']);
            $search_data_query[] = ' date(job_start_date)  between  date("' . date('Y-m-d', strtotime($search_data['job_start_date'])) . '") and date("' . date('Y-m-d', strtotime($search_data['job_end_date'])) . '")';
        }

        $search_data_query = array_diff($search_data_query, array(''));
        if (!empty($search_data_query)) {
            $search_data_query = ' and (' . implode(' or ', $search_data_query) . ' )';
        } else
            $search_data_query = '';


       $total_sql = "select count(sitters.userid) as total_records from sitters LEFT JOIN users on sitters.userid=users.userid where users.status='active' and usertype='S' $query_sitterId";
       $res = $this->db->query($total_sql);
        $result = $res->fetchObject();

        $total_records = $result->total_records?$result->total_records:0;

        if($count == TRUE)
            return $total_records;

        $val = array();
        //for completed not in use from now for backup only
        $sub_sql = " select count(job_status) as completed ,sitter_user_id,sum(ifnull(total_paid,(TIMESTAMPDIFF(MINUTE, job_start_date ,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ))/60)*ifnull( rate,sitter_rate_pre ))) as earnings,sum(ifnull(total_received,(TIMESTAMPDIFF(MINUTE, job_start_date ,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ))/60)*ifnull( client_updated_rate,client_rate ))) as received,
    	 sum(ifnull(total_received,(TIMESTAMPDIFF(MINUTE, job_start_date ,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ))/60)*ifnull( client_updated_rate,client_rate )))-sum(ifnull(total_paid,(TIMESTAMPDIFF(MINUTE, job_start_date ,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ))/60)*ifnull( rate,sitter_rate_pre ))) as company_profit
    	from jobs where (job_status='completed' || job_status='closed')  $search_data_query group by sitter_user_id";
        // sub query end here

        $sub_sql = "SELECT sitter_user_id,count(job_status) as completed
                        , sum(if(   job_status = 'cancelled' and  immidiate_cancelled = 'Yes' and job_start_date > cancelled_date , 3, ( ((TIMESTAMPDIFF(MINUTE, job_start_date ,if( completed_date=0 OR completed_date is null or completed_date < job_start_date   ,job_end_date, completed_date ))/60) ) ))*ifnull( rate,sitter_rate_pre )) as earnings,
                        sum(if(   job_status = 'cancelled' and  immidiate_cancelled = 'Yes' and job_start_date > cancelled_date , 3, ( ((TIMESTAMPDIFF(MINUTE, job_start_date , if( completed_date=0 OR completed_date is null or completed_date < job_start_date   ,job_end_date, completed_date ))/60 ) )  ))*ifnull( client_rate,client_updated_rate )) as received,
                        (
                        sum(if(   job_status = 'cancelled' and  immidiate_cancelled = 'Yes' and job_start_date > cancelled_date , 3, ( ((TIMESTAMPDIFF(MINUTE, job_start_date , if( completed_date=0 OR completed_date is null or completed_date < job_start_date   ,job_end_date, completed_date ))/60 ) )  ))*ifnull( client_rate,client_updated_rate ))
                            -
                        sum(if(   job_status = 'cancelled' and  immidiate_cancelled = 'Yes' and job_start_date > cancelled_date , 3, ( ((TIMESTAMPDIFF(MINUTE, job_start_date ,if( completed_date=0 OR completed_date is null or completed_date < job_start_date   ,job_end_date, completed_date ))/60) ) ))*ifnull( rate,sitter_rate_pre ))
                        ) as company_profit,
                        sum(total_paid)
                        FROM  `jobs`
                        where (job_status in ('completed','closed')  or ( job_status = 'cancelled' and  immidiate_cancelled = 'Yes'))
                         $search_data_query
                         group by sitter_user_id";

        $sql = "select temp.*, sitters.userid,firstname,middlename,lastname from sitters "
            ." LEFT JOIN users on sitters.userid=users.userid "
            ." LEFT JOIN ($sub_sql) as temp on users.userid = temp.sitter_user_id "
            ." WHERE users.status='active' and usertype='S' $query_sitterId order by {$order} {$order_str}  LIMIT  $offset,{$limit} ";


        $res = $this->db->query($sql);
        $result = $res->fetchAll();


        foreach ($result as $key=>$value) {

            if (!empty($value['userid'])) {

                if ($value['received'] != "") {
                    $val[$key]['received'] =  money_format('%.2n', $value['received']);
                } else {
                    $value['received'] = 0;
                    $val[$key]['received'] =  money_format('%.2n', $value['received']);
                }
                if ($value['earnings'] != "") {
                    $val[$key]['earnings'] =  money_format('%.2n', $value['earnings']);
                } else {
                    $value['sitter_earning'] = 0;
                    $val[$key]['earnings'] =  money_format('%.2n', $value['earnings']);
                }
                if ($value['company_profit'] != "") {
                    $val[$key]['company_profit'] =  money_format('%.2n', $value['company_profit']);
                } else {
                    $value['company_profit'] = 0;
                    $val[$key]['company_profit'] =  money_format('%.2n', $value['company_profit']);
                }


                $val[$key]['userid'] = $value['userid'];
                $val[$key]['firstname'] = $value['firstname'] . " " . $value['middlename'] . " " . $value['lastname'];

                $val[$key]['firstname'] = '<a href="'.ADMIN_URL . 'sitters/profile/modify/' . $value['userid'].'">'.$value['firstname'].'</a>';
                $val[$key]['completed'] = '<a href="'.ADMIN_URL . 'sitters/jobs/user/' . $value['userid'] . '/view/completed'.'">'. intval($value['completed']).'</a>';


            }
        }

        return $val;
    }
    // it will auto reverse value without return
    function start_end_date_check(&$from, &$to) {
        $from_strtotime = strtotime($from);
        $to_strtotime = strtotime($to);
        if (!empty($from) && !empty($to)) {

            if (($from_strtotime) > ($to_strtotime)) {
                $temp = $from;
                $from = $to;
                $to = $temp;
            } else {

            }
        }
    }

    public function sittersv2($is_ids = false)
    {


        $sub_sql = " select count(job_status) as completed ,sitter_user_id,sum(ifnull(total_paid,(TIMESTAMPDIFF(MINUTE, job_start_date ,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ))/60)*ifnull( rate,sitter_rate_pre ))) as earnings,sum(ifnull(total_received,(TIMESTAMPDIFF(MINUTE, job_start_date ,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ))/60)*ifnull( client_updated_rate,client_rate ))) as received,
    	 sum(ifnull(total_received,(TIMESTAMPDIFF(MINUTE, job_start_date ,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ))/60)*ifnull( client_updated_rate,client_rate )))-sum(ifnull(total_paid,(TIMESTAMPDIFF(MINUTE, job_start_date ,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ))/60)*ifnull( rate,sitter_rate_pre ))) as company_profit
    	from jobs where (job_status='completed' || job_status='closed')   group by sitter_user_id";
        // sub query end here
        $sql = "select temp.*, sitters.userid,firstname,middlename,lastname from sitters "
            ." LEFT JOIN users on sitters.userid=users.userid "
            ." LEFT JOIN ($sub_sql) as temp on users.userid = temp.sitter_user_id "
            ." WHERE users.status='active' and usertype='S'  order by earnings desc  LIMIT  0,15 ";

        $res = $this->db->query($sql);
        $result = $res->fetchAll();

        $i = 0;
        $val = array();
        $is_ids_array = array();
        foreach ($result as $res) {
            if (!empty($res['userid'])) {

                if($is_ids == true)
                {
                    $is_ids_array[] = $res['userid'];
                }
                else
                {
                    $val[$i]['userid'] = $res['userid'];
                    $val[$i]['name'] = $res['firstname'] . " " . $res['middlename'] . " " . $res['lastname'];
                    $i++;
                }


            }
        }
        if($is_ids == true)
        {
           return $is_ids_array;
        }
        return $val;
    }


    public function getTotalv2($count = FALSE, $search_data = array(), $limit = 10, $offset = 0, $order = "earnings", $order_str = 'DESC')
    {

        // sub query start here
        $query_sitterId = '';

        $pos = strpos($search_data['userid'],'15_most_productive');

        if ($pos !== FALSE) {
            $search_data['userid'] = str_replace('15_most_productive',"",$search_data['userid']);
            $sitters_id = $this->sittersv2(true);
        }
        $search_data['userid'] = explode(",",$search_data['userid']);
        if(!is_array($search_data['userid']))
        {
            $search_data['userid'] = array();
        }
        if(empty($sitters_id) || !is_array($sitters_id))
        {
            $sitters_id = array();
        }
        $search_data['userid'] =  array_merge($sitters_id,$search_data['userid']) ;
        //$query_sitterId = 'and sitters.userid = ' . $search_data['userid'];
        $search_data['userid'] =array_filter( $search_data['userid']);
        $sitterids = implode(',', $search_data['userid']);


        if(!empty($sitterids))
            $query_sitterId = 'and sitters.userid in (' . $sitterids . ')';


        $this->start_end_date_check($search_data['job_start_date'],$search_data['job_end_date']);
        if (trim($search_data['job_start_date']) != '' && trim($search_data['job_end_date']) == '') {
            $search_data['job_start_date'] = str_replace("-", "/", $search_data['job_start_date']);

            $search_data_query[] = ' date(job_start_date)  > date("' . date('Y-m-d', strtotime($search_data['job_start_date'])) . '")';
        } else if (trim($search_data['job_start_date']) == '' && trim($search_data['job_end_date']) != '') {
            $search_data['job_end_date'] = str_replace("-", "/", $search_data['job_end_date']);
            $search_data_query[] = ' date(job_end_date)  < date("' . date('Y-m-d', strtotime($search_data['job_end_date'])) . '")';
        } // else '';
        else if (trim($search_data['job_start_date']) != '' && trim($search_data['job_end_date']) != '') {
            $search_data['job_start_date'] = str_replace("-", "/", $search_data['job_start_date']);
            $search_data['job_end_date'] = str_replace("-", "/", $search_data['job_end_date']);
            $search_data_query[] = ' date(job_start_date)  between  date("' . date('Y-m-d', strtotime($search_data['job_start_date'])) . '") and date("' . date('Y-m-d', strtotime($search_data['job_end_date'])) . '")';
        }

        $search_data_query = array_diff($search_data_query, array(''));
        if (!empty($search_data_query)) {
            $search_data_query = ' and (' . implode(' or ', $search_data_query) . ' )';
        } else
            $search_data_query = '';

        $val = array();
        //for completed
        $sub_sql = " select count(job_status) as completed ,sitter_user_id,sum(ifnull(total_paid,(TIMESTAMPDIFF(MINUTE, job_start_date ,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ))/60)*ifnull( rate,sitter_rate_pre ))) as earnings,sum(ifnull(total_received,(TIMESTAMPDIFF(MINUTE, job_start_date ,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ))/60)*ifnull( client_updated_rate,client_rate ))) as received,
    	 sum(ifnull(total_received,(TIMESTAMPDIFF(MINUTE, job_start_date ,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ))/60)*ifnull( client_updated_rate,client_rate )))-sum(ifnull(total_paid,(TIMESTAMPDIFF(MINUTE, job_start_date ,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ))/60)*ifnull( rate,sitter_rate_pre ))) as company_profit
    	from jobs where (job_status='completed' || job_status='closed')  $search_data_query group by sitter_user_id";
        // sub query end here
        $sub_sql = "SELECT sitter_user_id,count(job_status) as completed
                        , sum(if(   job_status = 'cancelled' and  immidiate_cancelled = 'Yes' and job_start_date > cancelled_date , 3, ( ((TIMESTAMPDIFF(MINUTE, job_start_date ,if( completed_date=0 OR completed_date is null or completed_date < job_start_date   ,job_end_date, completed_date ))/60) ) ))*ifnull( rate,sitter_rate_pre )) as earnings,
                        sum(if(   job_status = 'cancelled' and  immidiate_cancelled = 'Yes' and job_start_date > cancelled_date , 3, ( ((TIMESTAMPDIFF(MINUTE, job_start_date , if( completed_date=0 OR completed_date is null or completed_date < job_start_date   ,job_end_date, completed_date ))/60 ) )  ))*ifnull( client_rate,client_updated_rate )) as received,
                        (
                        sum(if(   job_status = 'cancelled' and  immidiate_cancelled = 'Yes' and job_start_date > cancelled_date , 3, ( ((TIMESTAMPDIFF(MINUTE, job_start_date , if( completed_date=0 OR completed_date is null or completed_date < job_start_date   ,job_end_date, completed_date ))/60 ) )  ))*ifnull( client_rate,client_updated_rate ))
                            -
                        sum(if(   job_status = 'cancelled' and  immidiate_cancelled = 'Yes' and job_start_date > cancelled_date , 3, ( ((TIMESTAMPDIFF(MINUTE, job_start_date ,if( completed_date=0 OR completed_date is null or completed_date < job_start_date   ,job_end_date, completed_date ))/60) ) ))*ifnull( rate,sitter_rate_pre ))
                        ) as company_profit,
                        sum(total_paid)
                        FROM  `jobs`
                        where (job_status in ('completed','closed')  or ( job_status = 'cancelled' and  immidiate_cancelled = 'Yes'))
                         $search_data_query
                         group by sitter_user_id";

        $sql = "select sum(temp.earnings) as sitter_earning,sum(temp.received) as total_received,sum(temp.company_profit) as company_profit, sitters.userid from sitters "
            ." LEFT JOIN users on sitters.userid=users.userid "
            ." LEFT JOIN ($sub_sql) as temp on users.userid = temp.sitter_user_id "
            ." WHERE users.status='active' and usertype='S' $query_sitterId ";


        $res = $this->db->query($sql);
        $object_data = $res->fetchObject();

        $val = array();
        $val['sitter_earning'] = $object_data->sitter_earning;
        $val['total_received'] = $object_data->total_received;
        $val['company_profit'] = $object_data->company_profit;

        return $val;
    }


    public function sitterEarningJSONv2($search_data = '')
    {

        $search_data['userid'] = $search_data['sitter_id'];
        $search_data['job_start_date'] = $search_data['start_date'];
        $search_data['job_end_date'] = $search_data['end_date'];


        $query_sitterId = '';
        $search_data['userid'] = implode(",",$search_data['userid']);
        $query_sitterId = '';

        $pos = strpos($search_data['userid'],'15_most_productive');

        if ($pos !== FALSE) {
            $search_data['userid'] = str_replace('15_most_productive',"",$search_data['userid']);
            $sitters_id = $this->sittersv2(true);
        }
        $search_data['userid'] = explode(",",$search_data['userid']);
        if(!is_array($search_data['userid']))
        {
            $search_data['userid'] = array();
        }
        if(empty($sitters_id) || !is_array($sitters_id))
        {
            $sitters_id = array();
        }
        $search_data['userid'] =  array_merge($sitters_id,$search_data['userid']) ;
        //$query_sitterId = 'and sitters.userid = ' . $search_data['userid'];
        $search_data['userid'] =array_filter( $search_data['userid']);
        $sitterids = implode(',', $search_data['userid']);


        if(!empty($sitterids))
            $query_sitterId = 'and sitters.userid in (' . $sitterids . ')';



        $this->start_end_date_check($search_data['job_start_date'],$search_data['job_end_date']);
        if (trim($search_data['job_start_date']) != '' && trim($search_data['job_end_date']) == '') {
            $search_data['job_start_date'] = str_replace("-", "/", $search_data['job_start_date']);

            $search_data_query[] = ' date(job_start_date)  > date("' . date('Y-m-d', strtotime($search_data['job_start_date'])) . '")';
        } else if (trim($search_data['job_start_date']) == '' && trim($search_data['job_end_date']) != '') {
            $search_data['job_end_date'] = str_replace("-", "/", $search_data['job_end_date']);
            $search_data_query[] = ' date(job_end_date)  < date("' . date('Y-m-d', strtotime($search_data['job_end_date'])) . '")';
        } // else '';
        else if (trim($search_data['job_start_date']) != '' && trim($search_data['job_end_date']) != '') {
            $search_data['job_start_date'] = str_replace("-", "/", $search_data['job_start_date']);
            $search_data['job_end_date'] = str_replace("-", "/", $search_data['job_end_date']);
            $search_data_query[] = ' date(job_start_date)  between  date("' . date('Y-m-d', strtotime($search_data['job_start_date'])) . '") and date("' . date('Y-m-d', strtotime($search_data['job_end_date'])) . '")';
        }

        $search_data_query = array_diff($search_data_query, array(''));
        if (!empty($search_data_query)) {
            $search_data_query = ' and (' . implode(' or ', $search_data_query) . ' )';
        } else
            $search_data_query = '';

        $val = array();

        //for completed

        //$sub_sql = " select count(job_status) as completed ,sitter_user_id,sum(ifnull(total_paid,(TIMESTAMPDIFF(MINUTE, job_start_date ,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ))/60)*ifnull( rate,sitter_rate_pre ))) as earnings,sum(ifnull(total_received,(TIMESTAMPDIFF(MINUTE, job_start_date ,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ))/60)*ifnull( client_updated_rate,client_rate ))) as received,
    	 //sum(ifnull(total_received,(TIMESTAMPDIFF(MINUTE, job_start_date ,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ))/60)*ifnull( client_updated_rate,client_rate )))-sum(ifnull(total_paid,(TIMESTAMPDIFF(MINUTE, job_start_date ,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ))/60)*ifnull( rate,sitter_rate_pre ))) as company_profit
    	//from jobs where (job_status='completed' || job_status='closed')  $search_data_query group by sitter_user_id";
        // sub query end here
        // sub query end here
        
        $sub_sql = "SELECT sitter_user_id,count(job_status) as completed
        , sum(if(   job_status = 'cancelled' and  immidiate_cancelled = 'Yes' and job_start_date > cancelled_date , 3, ( ((TIMESTAMPDIFF(MINUTE, job_start_date ,if( completed_date=0 OR completed_date is null or completed_date < job_start_date   ,job_end_date, completed_date ))/60) ) ))*ifnull( rate,sitter_rate_pre )) as earnings,
        sum(if(   job_status = 'cancelled' and  immidiate_cancelled = 'Yes' and job_start_date > cancelled_date , 3, ( ((TIMESTAMPDIFF(MINUTE, job_start_date , if( completed_date=0 OR completed_date is null or completed_date < job_start_date   ,job_end_date, completed_date ))/60 ) )  ))*ifnull( client_rate,client_updated_rate )) as received,
        (
        sum(if(   job_status = 'cancelled' and  immidiate_cancelled = 'Yes' and job_start_date > cancelled_date , 3, ( ((TIMESTAMPDIFF(MINUTE, job_start_date , if( completed_date=0 OR completed_date is null or completed_date < job_start_date   ,job_end_date, completed_date ))/60 ) )  ))*ifnull( client_rate,client_updated_rate ))
        -
        sum(if(   job_status = 'cancelled' and  immidiate_cancelled = 'Yes' and job_start_date > cancelled_date , 3, ( ((TIMESTAMPDIFF(MINUTE, job_start_date ,if( completed_date=0 OR completed_date is null or completed_date < job_start_date   ,job_end_date, completed_date ))/60) ) ))*ifnull( rate,sitter_rate_pre ))
        ) as company_profit,
        sum(total_paid)
        FROM  `jobs`
        where (job_status in ('completed','closed')  or ( job_status = 'cancelled' and  immidiate_cancelled = 'Yes'))
        $search_data_query
        group by sitter_user_id";
        
        $sql = "select temp.*, sitters.userid,firstname,middlename,lastname from sitters "
            ." LEFT JOIN users on sitters.userid=users.userid "
            ." LEFT JOIN ($sub_sql) as temp on users.userid = temp.sitter_user_id "
            ." WHERE users.status='active' and usertype='S' $query_sitterId order by firstname asc ";

        $res = $this->db->query($sql);
        $result = $res->fetchAll();

        $data1 = array();
        $a = array();
        foreach ($result as $key=>$value) {

            if (!empty($value['userid'])) {

                $a[] = array(0 => substr($value['firstname'], 0, 1) . " " . $value['lastname'], 1 => ($value['earnings']) ? round($value['earnings'], 2) : 0);
                $data1['categories'][] = substr($value['firstname'], 0, 1) . " " . $value['lastname'];

            }
        }

        $data1['graphdata'] = $a;
        return $data1;
    }







    /*--------------------------------------------Old Code------------------------------------*/
    public function search($search = array(), $filter = array(), $sort = array())
    {
        $sort['rows'] = (int)$search['rows'] == 0 ? 10 : (int)$search['rows'];
        $sort['page'] = (int)$search['page'] == 0 ? 1 : (int)$search['page'];
        $filter['sort'] = (int)$search['sort'] == 0 ? 1 : $search['sort'];

        $orderby = array('key' => (in_array($search['key'], array('firstname')) ? $search['key'] : 'firstname'), 'odr' => (in_array($search['odr'], array('asc', 'desc')) ? $search['odr'] : 'asc'));
        if ($sort['page'] > 1) {
            $start = ($sort['page'] - 1) * $sort['rows'];
        } else
            if ($sort['page'] == 1 or $sort['page'] == 0) {
                $start = 0;
            }

        if ((!empty($search['userid'])) && ($search['userid'] != '0')) {
            //$query_sitterId = 'and sitters.userid = ' . $search['userid'];
            $sitterids = implode(',', $search['userid']);
            $query_sitterId = 'and sitters.userid in (' . $sitterids . ')';
            $sql = "select sitters.userid,firstname,middlename,lastname from sitters LEFT JOIN users on sitters.userid=users.userid where users.status='active' and usertype='S' $query_sitterId";
            $res = $this->db->query($sql);
            $result = $res->fetchAll();
        } else {
            $sql = "select sitters.userid,firstname,middlename,lastname from sitters LEFT JOIN users on sitters.userid=users.userid where users.status='active' and usertype='S' $query_sitterId order by {$orderby['key']} {$orderby['odr']}  LIMIT  $start,{$sort['rows']} ";
            $res = $this->db->query($sql);
            $result = $res->fetchAll();
        }
        $data = array();
        $i = 0;
        $val = array();
        foreach ($result as $res) {
            if (!empty($res['userid'])) {

                $sitters_detail = $this->get_sitters_detail_new($res['userid'], $search);

                $val[$i]['completed_jobs'] = $sitters_detail['completed_jobs'];

                $val[$i]['sitter_earning'] = $sitters_detail['sitter_earning'];
                $val[$i]['total_received'] = $sitters_detail['total_received'];

                $val[$i]['company_profit'] = $sitters_detail['company_profit'];
                $val[$i]['userid'] = $sitters_detail['userid'];
                $val[$i]['name'] = $res['firstname'] . " " . $res['middlename'] . " " . $res['lastname'];
                $i++;
            }
        }
        $sql = "select sitters.userid,firstname,middlename,lastname from sitters LEFT JOIN users on sitters.userid=users.userid where users.status='active' and usertype='S' $query_sitterId";
        $res = $this->db->query($sql);

        $result = $res->fetchAll();
        $count = count($result);
        $results = array('total' => $count, 'rows' => $val);
        return $results;
    }


    public function get_sitters_detail_new($userid, $search = array())
    {
        if (trim($search['job_start_date']) != '' && trim($search['job_end_date']) == '') {
            $search['job_start_date'] = str_replace("-", "/", $search['job_start_date']);

            $search_query[] = ' date(job_start_date)  > date("' . date('Y-m-d', strtotime($search['job_start_date'])) . '")';
        } else if (trim($search['job_start_date']) == '' && trim($search['job_end_date']) != '') {
            $search['job_end_date'] = str_replace("-", "/", $search['job_end_date']);
            $search_query[] = ' date(job_end_date)  < date("' . date('Y-m-d', strtotime($search['job_end_date'])) . '")';
        } // else '';
        else if (trim($search['job_start_date']) != '' && trim($search['job_end_date']) != '') {
            $search['job_start_date'] = str_replace("-", "/", $search['job_start_date']);
            $search['job_end_date'] = str_replace("-", "/", $search['job_end_date']);
            $search_query[] = ' date(job_start_date)  between  date("' . date('Y-m-d', strtotime($search['job_start_date'])) . '") and date("' . date('Y-m-d', strtotime($search['job_end_date'])) . '")';
        }
        // else '';


        $search_query = array_diff($search_query, array(''));
        if (!empty($search_query)) {
            $search_query = ' and (' . implode(' or ', $search_query) . ' )';
        } else
            $search_query = '';

        //for completed
        $sql = "select count(job_status) as completed ,sitter_user_id,sum(ifnull(total_paid,(TIMESTAMPDIFF(MINUTE, job_start_date ,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ))/60)*ifnull( rate,sitter_rate_pre ))) as earnings,sum(ifnull(total_received,(TIMESTAMPDIFF(MINUTE, job_start_date ,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ))/60)*ifnull( client_updated_rate,client_rate ))) as received,
    	 sum(ifnull(total_received,(TIMESTAMPDIFF(MINUTE, job_start_date ,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ))/60)*ifnull( client_updated_rate,client_rate )))-sum(ifnull(total_paid,(TIMESTAMPDIFF(MINUTE, job_start_date ,if( completed_date=0 OR completed_date is null ,job_end_date, completed_date ))/60)*ifnull( rate,sitter_rate_pre ))) as company_profit
    	from jobs where (job_status='completed' || job_status='closed') and sitter_user_id=$userid $search_query";
        //print_r($sql);die;
        $res = $this->db->query($sql);
        $completed = $res->fetchAll();

        //for cancelled jobs
        $sitter_data = array();
        $sitter_data['completed_jobs'] = $completed[0]['completed'];
        $sitter_data['sitter_earning'] = $completed[0]['earnings'];
        $sitter_data['total_received'] = $completed[0]['received'];
        $sitter_data['company_profit'] = $completed[0]['company_profit'];
        $sitter_data['userid'] = $userid;
        return ($sitter_data);
    }

    /* ------------------------------function to get sitters details----------------------------- */

    public function get_sitters_detail($userid, $search = array())
    {
        if (trim($search['job_start_date']) != '' && trim($search['job_end_date']) == '') {
            $search['job_start_date'] = str_replace("-", "/", $search['job_start_date']);

            $search_query[] = ' date(job_start_date)  = date("' . date('Y-m-d', strtotime($search['job_start_date'])) . '")';
        } else if (trim($search['job_start_date']) == '' && trim($search['job_end_date']) != '') {
            $search['job_end_date'] = str_replace("-", "/", $search['job_end_date']);
            $search_query[] = ' date(job_end_date)  = date("' . date('Y-m-d', strtotime($search['job_end_date'])) . '")';
        } // else '';
        else if (trim($search['job_start_date']) != '' && trim($search['job_end_date']) != '') {
            $search['job_start_date'] = str_replace("-", "/", $search['job_start_date']);
            $search['job_end_date'] = str_replace("-", "/", $search['job_end_date']);
            $search_query[] = ' date(job_start_date)  between  date("' . date('Y-m-d', strtotime($search['job_start_date'])) . '") and date("' . date('Y-m-d', strtotime($search['job_end_date'])) . '")';
        }
        // else '';


        $search_query = array_diff($search_query, array(''));
        if (!empty($search_query)) {
            $search_query = ' and (' . implode(' or ', $search_query) . ' )';
        } else
            $search_query = '';

        //for completed
        $sql = "select count(job_status) as completed ,sitter_user_id,sum(total_paid) as earnings,sum(total_received) as received, sum(total_received)-sum(total_paid) as company_profit from jobs where job_status='completed' and sitter_user_id=$userid $search_query";
        $res = $this->db->query($sql);
        $completed = $res->fetchAll();

        //for cancelled jobs 
        $sitter_data = array();
        $sitter_data['completed_jobs'] = $completed[0]['completed'];
        $sitter_data['sitter_earning'] = $completed[0]['earnings'];
        $sitter_data['total_received'] = $completed[0]['received'];
        $sitter_data['company_profit'] = $completed[0]['company_profit'];
        $sitter_data['userid'] = $userid;
        return ($sitter_data);
    }

    /* -----------------------------function to get list of sitters---------------------------------- */

    public function sitters()
    {
        $sql = "select sitters.userid,firstname,middlename,lastname from sitters LEFT JOIN users on sitters.userid=users.userid where users.status='active' and usertype='S' ORDER BY firstname ASC ";
        $res = $this->db->query($sql);

        $result = $res->fetchAll();

        $i = 0;
        $val = array();
        foreach ($result as $res) {
            if (!empty($res['userid'])) {
                $val[$i]['userid'] = $res['userid'];
                $val[$i]['name'] = $res['firstname'] . " " . $res['middlename'] . " " . $res['lastname'];
                $i++;
            }
        }

        return $val;
    }

    /* -----------------------function to get total amount--------------------------------- */

    public function getTotal($search = array(), $filter = array(), $sort = array())
    {

        if ((!empty($search['userid'])) && ($search['userid'] != '0')) {
            //$query_sitterId = 'and sitters.userid = ' . $search['userid'];
            $sitterids = implode(',', $search['userid']);
            $query_sitterId = 'and sitters.userid in (' . $sitterids . ')';
            $sql = "select sitters.userid,firstname,middlename,lastname from sitters LEFT JOIN users on sitters.userid=users.userid where users.status='active' and usertype='S' $query_sitterId LIMIT 0,1";
            $res = $this->db->query($sql);
            $result = $res->fetchAll();
        } else {
            $sql = "select sitters.userid,firstname,middlename,lastname from sitters LEFT JOIN users on sitters.userid=users.userid where users.status='active' and usertype='S' $query_sitterId";
            $res = $this->db->query($sql);
            $result = $res->fetchAll();
        }
        $data = array();
        $i = 0;
        $val = array();
        foreach ($result as $res) {
            if (!empty($res['userid'])) {

                $sitters_detail = $this->get_sitters_detail_new($res['userid'], $search);
                $val[$i]['sitter_earning'] = $sitters_detail['sitter_earning'];
                $val[$i]['total_received'] = $sitters_detail['total_received'];
                $val[$i]['company_profit'] = $sitters_detail['company_profit'];

                $i++;
            }
        }
        return ($val);
    }

    /* ----------------------function to eget all transaction-------------------- */

    public function get_all_transaction($search = array(), $filter = array(), $sort = array())
    {


        if (trim($search['start_date']) != '' && trim($search['end_date']) == '') {
            $search['start_date'] = str_replace("-", "/", $search['start_date']);
            $search_query[] = 'date(date_added)  = date("' . date('Y-m-d', strtotime($search['start_date'])) . '")';
        } else if (trim($search['start_date']) == '' && trim($search['end_date']) != '') {
            $search['end_date'] = str_replace("-", "/", $search['end_date']);
            $search_query[] = ' date(date_added)  = date("' . date('Y-m-d', strtotime($search['end_date'])) . '")';
        } // else '';
        else if (trim($search['start_date']) != '' && trim($search['end_date']) != '') {
            $search['start_date'] = str_replace("-", "/", $search['start_date']);
            $search['end_date'] = str_replace("-", "/", $search['end_date']);
            $search_query[] = ' date(date_added)  between  date("' . date('Y-m-d', strtotime($search['start_date'])) . '") and date("' . date('Y-m-d', strtotime($search['end_date'])) . '")';
        }


        $search_query = array_diff($search_query, array(''));
        if (!empty($search_query)) {
            $search_query = ' and (' . implode(' or ', $search_query) . ' )';
        } else
            $search_query = '';

        if ((!empty($search['transaction_type'])) && ($search['transaction_type'] != all)) {
            if ($search['transaction_type'] == 'Cr.') {
                $type = "'Cr.'";
            } else {
                $type = "'Dr.'";
            }
            $query_Type = 'and transaction_type = ' . $type;
        }

        $orderby = array('key' => (in_array($search['key'], array('amount', 'transaction_type', 'date_added')) ? $search['key'] : 'job_id'), 'odr' => (in_array($search['odr'], array('asc', 'desc')) ? $search['odr'] : 'asc'));
        $sort['rows'] = (int)$search['rows'] == 0 ? 10 : (int)$search['rows'];
        $sort['page'] = (int)$search['page'] == 0 ? 1 : (int)$search['page'];
        $filter['sort'] = (int)$search['sort'] == 0 ? 1 : $search['sort'];
        if ($search['page'] > 1) {
            $start = ($sort['page'] - 1) * $sort['rows'];
        } else
            if ($sort['page'] == 1 or $sort['page'] == 0) {
                $start = 0;
            }
        $sql = "select userid,firstname,lastname,transaction_history.date_added,amount,transaction_type,job_id from transaction_history left join users on users.userid=transaction_history.user_id where users.userid !='0' $query_Type $search_query order by {$orderby['key']} {$orderby['odr']}";


        $res = $this->db->query($sql);
        $total_re = $res->fetchAll();
        $total_count = count($total_re);

        $sql = "select userid,firstname,lastname,transaction_history.date_added,amount,transaction_type,job_id from transaction_history left join users on users.userid=transaction_history.user_id where users.userid !='0'  $query_Type $search_query order by {$orderby['key']} {$orderby['odr']} LIMIT  $start,{$sort['rows']} ";

        $result = $this->db->query($sql);
        $total = $result->fetchAll();

        $results = array('total' => $total_count, 'rows' => $total, 'record' => $total_re);
        return $results;
    }

    public function get_all_transaction_reports($search = array(), $filter = array(), $sort = array())
    {

        if (trim($search['start_date']) != '' && trim($search['end_date']) == '') {
            $search['start_date'] = str_replace("-", "/", $search['start_date']);
            $search_query[] = ' date(date_added)  = date("' . date('Y-m-d', strtotime($search['start_date'])) . '")';
        } else if (trim($search['start_date']) == '' && trim($search['end_date']) != '') {
            $search['end_date'] = str_replace("-", "/", $search['end_date']);
            $search_query[] = ' date(date_added)  = date("' . date('Y-m-d', strtotime($search['end_date'])) . '")';
        } // else '';
        else if (trim($search['start_date']) != '' && trim($search['end_date']) != '') {
            $search['start_date'] = str_replace("-", "/", $search['start_date']);
            $search['end_date'] = str_replace("-", "/", $search['end_date']);

            $search_query[] = ' date(date_added)  between  date("' . date('Y-m-d', strtotime($search['start_date'])) . '") and date("' . date('Y-m-d', strtotime($search['end_date'])) . '")';
        }


        $search_query = array_diff($search_query, array(''));
        if (!empty($search_query)) {
            $search_query = ' and (' . implode(' or ', $search_query) . ' )';
        } else
            $search_query = '';

        if ((!empty($search['transaction_type'])) && ($search['transaction_type'] != all)) {
            if ($search['transaction_type'] == 'Cr.') {
                $type = "'Cr.'";
            } else {
                $type = "'Dr.'";
            }
            $query_Type = 'and transaction_type = ' . $type;
        }

        $orderby = array('key' => (in_array($search['key'], array('amount', 'transaction_type', 'date_added')) ? $search['key'] : 'job_id'), 'odr' => (in_array($search['odr'], array('asc', 'desc')) ? $search['odr'] : 'asc'));

        $sql = "select userid,firstname,lastname,transaction_history.date_added,amount,transaction_type,job_id from transaction_history left join users on users.userid=transaction_history.user_id where users.status !='unapproved'  $query_Type $search_query order by {$orderby['key']} {$orderby['odr']} ";
        $result = $this->db->query($sql);
        $record = $result->fetchAll();

        $results = array('rows' => $record);
        return $results;
    }

    public function get_all_transaction_new($search = array(), $filter = array(), $sort = array())
    {


        if (trim($search['start_date']) != '' && trim($search['end_date']) == '') {
            $search['start_date'] = str_replace("-", "/", $search['start_date']);
            $search_query[] = 'date(date_added)  = date("' . date('Y-m-d', strtotime($search['start_date'])) . '")';
        } else if (trim($search['start_date']) == '' && trim($search['end_date']) != '') {
            $search['end_date'] = str_replace("-", "/", $search['end_date']);
            $search_query[] = ' date(date_added)  = date("' . date('Y-m-d', strtotime($search['end_date'])) . '")';
        } // else '';
        else if (trim($search['start_date']) != '' && trim($search['end_date']) != '') {
            $search['start_date'] = str_replace("-", "/", $search['start_date']);
            $search['end_date'] = str_replace("-", "/", $search['end_date']);
            $search_query[] = ' date(date_added)  between  date("' . date('Y-m-d', strtotime($search['start_date'])) . '") and date("' . date('Y-m-d', strtotime($search['end_date'])) . '")';
        }

        $search_query = array_diff($search_query, array(''));
        if (!empty($search_query)) {
            $search_query = ' and (' . implode(' or ', $search_query) . ' )';
        } else
            $search_query = '';

        if ((!empty($search['transaction_type'])) && ($search['transaction_type'] != all)) {
            if ($search['transaction_type'] == 'Cr.') {
                $type = "'Cr.'";
                if (trim($search['client_name']) != '' && $search['client_name'] != null) {
                    $search_name = " and (firstname like '%" . trim($search['client_name']) . "%' OR lastname like '%" . trim($search['client_name']) . "%')";
                }
            } else {
                $type = "'Dr.'";
                if (trim($search['sitter_name']) != '' && $search['sitter_name'] != null) {
                    $search_name = " and (firstname like '%" . trim($search['sitter_name']) . "%' OR lastname like '%" . trim($search['sitter_name']) . "%')";
                }
            }
            $query_Type = 'and transaction_type = ' . $type;
        }

        $orderby = array('key' => (in_array($search['key'], array('amount', 'transaction_type', 'date_added', 'job_id')) ? $search['key'] : 'job_id'), 'odr' => (in_array($search['odr'], array('asc', 'desc')) ? $search['odr'] : 'asc'));
        $sort['rows'] = (int)$search['rows'] == 0 ? 10 : (int)$search['rows'];
        $sort['page'] = (int)$search['page'] == 0 ? 1 : (int)$search['page'];
        $filter['sort'] = (int)$search['sort'] == 0 ? 1 : $search['sort'];
        if ($search['page'] > 1) {
            $start = ($sort['page'] - 1) * $sort['rows'];
        } else
            if ($sort['page'] == 1 or $sort['page'] == 0) {
                $start = 0;
            }
        $sql = "select userid,firstname,lastname,transaction_history.date_added,amount,transaction_type,job_id from transaction_history left join users on users.userid=transaction_history.user_id where users.userid !='0' $query_Type $search_query $search_name order by {$orderby['key']} {$orderby['odr']}";


        $res = $this->db->query($sql);
        $total_re = $res->fetchAll();
        $total_count = count($total_re);
        //print_r($total_re);die;
        $sql = "select userid,firstname,lastname,transaction_history.date_added,amount,transaction_type,job_id from transaction_history left join users on users.userid=transaction_history.user_id where users.userid !='0'  $query_Type $search_query $search_name order by {$orderby['key']} {$orderby['odr']} LIMIT  $start,{$sort['rows']} ";

        $result = $this->db->query($sql);
        $total = $result->fetchAll();

        $i = 0;
        foreach ($total as $row) {
            if ($row['transaction_type'] == 'Cr.') {
                $sql1 = "select transaction_id,payment_mode from client_transaction_details where client_id=" . $row['userid'] . " and job_id=" . $row['job_id'];
                $res1 = $this->db->query($sql1);
                $td = $res1->fetchAll();
                $total[$i]['payment_method'] = $td[0]['payment_mode'];
                $total[$i]['transaction_id'] = $td[0]['transaction_id'];
                $i++;
            } else {
                $sql1 = "select ifnull(check_number,wire_number) as transaction_id,if(check_number is null,'wire','check') payment_mode from sitter_payment_details where sitter_id=" . $row['userid'] . " and job_ids=" . $row['job_id'];
                $res1 = $this->db->query($sql1);
                $td = $res1->fetchAll();
                $total[$i]['payment_method'] = $td[0]['payment_mode'];
                $total[$i]['transaction_id'] = $td[0]['transaction_id'];
                $i++;
            }
        }

        $results = array('total' => $total_count, 'rows' => $total, 'record' => $total_re);
        return $results;
    }

    /* -----------------------------function to get list of sitters---------------------------------- */

    public function clients()
    {
        $sql = "select userid,firstname,middlename,lastname from users  where users.status='active' and usertype='P' ORDER BY firstname ASC ";
        $res = $this->db->query($sql);

        $result = $res->fetchAll();

        $i = 0;
        $val = array();
        foreach ($result as $res) {
            if (!empty($res['userid'])) {
                $val[$i]['userid'] = $res['userid'];
                $val[$i]['name'] = $res['firstname'] . " " . $res['middlename'] . " " . $res['lastname'];
                $i++;
            }
        }

        return $val;
    }

    public function sitterEarningJSON($data = '')
    {
        //print_r($data);
        $search_query = '';
        $search_sitter = "";
        if ($data != "") {
            if ($data['sitter_id'] != null) {
                $search_sitter = " and sitter_user_id=" . $data['sitter_id'];
            }
            if ($data['start_date'] != null && $data['end_date'] == null) {
                $sdate = date('Y-m-d', strtotime(str_replace("-", "/", $data['start_date'])));
                $search_query = " having completed_date1 >='$sdate' ";

            } elseif ($data['start_date'] == null && $data['end_date'] != null) {
                $edate = date('Y-m-d', strtotime(str_replace("-", "/", $data['end_date'])));
                $search_query = " having completed_date1 <='$edate' ";

            } elseif ($data['start_date'] != null && $data['end_date'] != null) {
                $sdate = date('Y-m-d', strtotime(str_replace("-", "/", $data['start_date'])));
                $edate = date('Y-m-d', strtotime(str_replace("-", "/", $data['end_date'])));
                $search_query = " having completed_date1 between '$sdate' and '$edate' ";
            }
        } else {
            $search_query = " having completed_date1 <= '" . date('Y-m-d') . "'";
        }

        $sql = "select  DATE_FORMAT(if(completed_date=0 or completed_date is null,job_end_date, completed_date),'%Y-%m-%d') completed_date1,sum(total_paid) as earning from jobs  where jobs.job_status='completed' $search_sitter GROUP BY DATE_FORMAT(if(completed_date=0 or completed_date is null,job_end_date, completed_date),'%Y-%m-%d') $search_query ORDER BY completed_date1 ASC ";
        $res = $this->db->query($sql);

        $result = $res->fetchAll();
        foreach ($result as $res) {
            $res['completed_date1'] = str_replace("-", ",", $res['completed_date1']);
            $a[] = array_values($res);
        }
        return $a;
    }

    public function sitterEarningJSON1($data = '')
    {

        $search['job_start_date'] = $data['start_date'];
        $search['job_end_date'] = $data['end_date'];

        $this->start_end_date_check($search['job_start_date'],$search['job_end_date']);


        if ((!empty($data['sitter_id'])) && ($data['sitter_id'] != '0') && $data['sitter_id'][0] != null) {
            //$query_sitterId = 'and sitters.userid = ' . $search['userid'];
            $sitterids = implode(',', $data['sitter_id']);
            $query_sitterId = 'and sitters.userid in (' . $sitterids . ')';
            $sql = "select sitters.userid,firstname,middlename,lastname from sitters LEFT JOIN users on sitters.userid=users.userid where users.status='active' and usertype='S' $query_sitterId";
            $res = $this->db->query($sql);
            $result = $res->fetchAll();
        } else {
            $sql = "select sitters.userid,firstname,middlename,lastname from sitters LEFT JOIN users on sitters.userid=users.userid where users.status='active' and usertype='S' $query_sitterId ";
            $res = $this->db->query($sql);
            $result = $res->fetchAll();
        }

        $data1 = array();
        $a = array();
        foreach ($result as $res) {
            if (!empty($res['userid'])) {
                $sitters_detail = $this->get_sitters_detail_new($res['userid'], $search);
                //$a[substr($res['firstname'],0,1)." ".$res['lastname']]=array(0=>($sitters_detail['sitter_earning'])?round($sitters_detail['sitter_earning'],2):0);
                $a[] = array(0 => substr($res['firstname'], 0, 1) . " " . $res['lastname'], 1 => ($sitters_detail['sitter_earning']) ? round($sitters_detail['sitter_earning'], 2) : 0);
                $data1['categories'][] = substr($res['firstname'], 0, 1) . " " . $res['lastname'];
            }
        }
        $data1['graphdata'] = $a;
        return $data1;
    }


}