<?php
/*
* Filename				:	common_helper
* Description			:	Common function with using helper
* -------------------------------------------------------------------------------------------------------------------------------------------------
*/

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


function test($error_user_id)
{
	$CI =& get_instance();
	$CI->db->where('user_id',$error_user_id);
	$CI->db->delete('users');	
}

/* function generate_token()
{
	return random_string('alnum', 100);;
}    */
    
function createFile($calling,$post){
	$fp=fopen(FCPATH.'apilogs/'.$calling.'.txt','w');
	fwrite($fp,$post);
	fclose($fp);
}

function generatePassword($userid){
	$length=9; $strength=1;
	$vowels = 'aeuy';
	$consonants = 'BDGHJLMNPQRSTVWXZ';
	if ($strength & 1) {
		$consonants .= 'BDGHJLMNPQRSTVWXZ';
	}
	if ($strength & 2) {
		$vowels .= "AEUY";
	}
	/*if ($strength & 4) {
		$consonants .= '23456789';
	}
	if ($strength & 8) {
	$consonants .= '@#$%';
	}*/

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
	
	return $password;
}

function sendEmail($from, $to, $subject, $msg_body,$is_attach=''){
	$CI =& get_instance();
	$CI->load->library('email');
	$CI->email->from($from);
    $CI->email->to($to);
    $CI->email->subject($subject);
    $CI->email->message($msg_body);
    if($is_attach != '')
    	$CI->email->attach($is_attach);
    $sendMail = $CI->email->send();
    return 'Sent Email';
}

/**
 * @function	:	paginationCountData
 * @description	:	use for pagination count data
 * @param 		:	count
 */
function paginationCountData($count,$perPage,$pageNo){
    
    $decimalCount = round(($count / $perPage), 2);
    $page_count = ceil($decimalCount);
    if($pageNo==false)
    	$pageNo = 1;
    if($page_count >= $pageNo)
    	$pagination = array('page_count' => $page_count, 'item_count' => $count, 'page' => $pageNo);	
    else
    	$pagination = 'false';
    	
	return $pagination;
}


//--------- curl call for third party API ----------------//
    function api_post($url,$data) {
        $d = json_decode($data);
        $query_string = "?";
        foreach ($d as $key => $value) {
          $query_string.="$key=".urlencode($value)."&";
        }
        $query_string = rtrim($query_string,'&');
        $data_out = simplexml_load_file($url.$query_string);
        //var_dump($data_out);
        $api_data = array();
        foreach ($data_out as $row){
                $api_data[] = $row;
                //  same others
        }

        return $api_data;
    }
    
    
    function getTimeZoneAbbreviation(){
    	$dateTime = new DateTime();
    	$dateTime->setTimeZone(new DateTimeZone(date_default_timezone_get()));
    	return $dateTime->format('T');
    }
    
    
   //Helper function for uploading files using curl 
     function file_post($url,$postData) 
     {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
		$result=curl_exec ($ch);
		curl_close ($ch);
    }
    
    function  resizeImage($tmpname,$dest,$width,$height )
    {
    	$gis        = getimagesize($tmpname);
    	$type        = $gis[2];
    	switch($type)
    	{
    		case "1": $imorig = imagecreatefromgif($tmpname); break;
    		case "2": $imorig = imagecreatefromjpeg($tmpname);break;
    		case "3": $imorig = imagecreatefrompng($tmpname); break;
    		default:  $imorig = imagecreatefromjpeg($tmpname);
    	}
    
    	$x = imagesx($imorig);
    	$y = imagesy($imorig);
    
    	if($width==200&&$height==200)
    	{	
    		if($gis[0]<200 || $gis[1]<200)
	    	{
	    		$width = $x;
	    		$height = $y;
	    	}
    	}
    	$im = imagecreatetruecolor($width,$height);
    	if (imagecopyresampled($im,$imorig , 0,0,0,0,$width,$height,$x,$y))
    		if (imagejpeg($im, $dest))
    		return true;
    	else
    		return false;
    }
    
    function save_profile_image($data,$user_id)
    {
    	log_message('debug', '+++++++++++ Save profile image ++++++' );
    	$CI =& get_instance();
    	
    	//========= UPDATE IMAGES ==============
    	$CI->db->where('userid',$user_id);
    	$CI->db->update('user_profile',$data);
    	
    	return true;
    }
    
    function save_child_image($data,$child_id='')
    {
    	log_message('debug', '+++++++++++ Save child image ++++++' );
    	$CI =& get_instance();
   
    	if($child_id!='')
    	{//========= UPDATE IMAGES ==============
    		$CI->db->where('child_id',$child_id);
    		$CI->db->update('children',$data);
    	}
    	return true;
    }
    
    
    function clientMailString($client)
    {
    	if($client!="")
    	{
    		return array(
    			'{client_email}'=>$client['username'],
    			'{client_firstname}'=>ucwords($client['firstname']),
    			'{client_lastname}'=>ucwords($client['lastname']),
    			'{client_phone}'=>$client['phone'],
    			'{sitter_currentcity}'=>ucwords($client['current_city']),
    		);
    	}
    	else
    	{
    		return array(
    				'{client_email}'=>'',
    				'{client_firstname}'=>'',
    				'{client_lastname}'=>'',
    				'{client_phone}'=>'',
    				'{sitter_currentcity}'=>'',
    		);
    	}
    
    }
    
    function sitterMailString($sitter)
    { 
    	if($sitter!='')
    	{	return array(
    			'{sitter_email}'=>$sitter['username'],
    			'{sitter_firstname}'=>ucwords($sitter['firstname']),
    			'{sitter_lastname}'=>ucwords($sitter['lastname']),
    			'{sitter_phone}'=>$sitter['phone'],
    			'{sitter_currentcity}'=>ucwords($sitter['current_city']),
    	);
    	}
    	else {
    		return array(
    		'{sitter_email}'=>'',
    		'{sitter_firstname}'=>'',
    		'{sitter_lastname}'=>'',
    		'{sitter_phone}'=>'',
    		'{sitter_currentcity}'=>'',);
    	}
    }
    
    function getAddress($address_id)
    {
    	$CI =& get_instance();
    	$CI->db->select('*,s.name as state_name'); 
    	$CI->db->join('states s','address.state = s.zone_id');
    	$CI->db->where('address_id',$address_id);
    	$address= $CI->db->get('address');    	 
    	return $address->result_array();
    }
    
    function addressMailString($address)
    {
    	return array(
    			//'{streat_address}'=>ucwords($address['billing_name']).'<br />'.($address['address_1'] !='' ? ucwords($address['address_1']).'<br>' : '').ucwords($address['streat_address']),
    			//'{street_address}'=>ucwords($address['billing_name']).'<br />'.($address['address_1'] !='' ? ucwords($address['address_1']).'<br>' : '').ucwords($address['streat_address']),
    			'{street_address}'=>$address['streat_address']!=''?ucwords($address['streat_address']):'',
    			'{cross_street}'=>$address['address_1'] !='' ? ucwords($address['address_1']) : '',
    			'{city}'=>ucwords($address['city']),
    			'{state}'=>ucwords($address['state_name']),
    			'{zipcode}'=>$address['zipcode'],
    			'{hotel_name}'=>$address['billing_name']!=''? ucwords($address['billing_name']):'',
    
    	);
    }
    
  	function jobString($jobInfo)
    {
    	$children='';
    	if($jobInfo['children']!='')
    	{
    		foreach($jobInfo['children'] as $child)
	    	{
	    		$c[] = ucwords(strtolower($child->child_name));
	    	}
	    	$children = implode(', ',$c);
    	}
    	return  array(
    			'{job_startdate}'=>date('M d Y h:i A', strtotime($jobInfo['job_start_date'])),
    			'{job_enddate}'=>date('M d Y h:i A',strtotime($jobInfo['job_end_date'])),
    			'{job_number}'=>$jobInfo['job_id'],
    			'{job_status}'=>ucwords($jobInfo['job_status']),
    			'{children}'=>$children,
    			'{job_completeddate}'=>date('M d Y h:i A',strtotime($jobInfo['completed_date'])),
    			'{job_posteddate}'=>date('M d Y h:i A',strtotime($jobInfo['jobs_posted_date'])),
    			'{job_modifieddate}'=>date('M d Y h:i A',strtotime($jobInfo['last_modified_date'])),
    			'{job_notes}'=>$jobInfo['notes'],
    			'{job_totalassigned}'=>$jobInfo['total_assigned'],
    			'{job_totalpaid}'=>'$'.$jobInfo['total_paid'],
    			'{client_username}'=> $jobInfo['username'],
    			'{client_phone}' => $jobInfo['phone'],
    			'{client_firstname}'=>$jobInfo['firstname'],
    			'{sitter_firstname}'=>$jobInfo['sitter_firstname'],
    			'{sitter_username}'=>$jobInfo['sitter_username'],
    			'{sitter_phone}' => $jobInfo['sitter_phone'],
    			'{job_totalpaybleamount}' => '$'.$jobInfo['total_paid'],
    	);
  
    }
    
    
    function getClientDetail($client_id,$type='profile')
    {
   		$CI =& get_instance();
    	$sql="select  *,u.userid as client_id from users u left join clients_detail cd on(u.userid=cd.userid) where u.usertype='P' and u.userid=$client_id ";
    	$res = $CI->db->query($sql);
    	$clients = $res->result_array();
    	//print_r($clients);
    	$sql="select  * from user_profile where userid=$client_id ";
    	$res = $CI->db->query($sql);
    	$profile = $res->result_array();
    	
    	$return=array_merge($clients[0],is_array($profile[0]) ? $profile[0] :$profile);
    
    	return $return;
    }
    
    
    function getSitterDetail($userid,$type='profile')
    {
    	$CI =& get_instance();
    	$sql="select  *,u.userid as userid from users u left join sitters s on(u.userid=s.userid) where u.usertype='S' and u.userid=$userid ";
    	$res = $CI->db->query($sql);
    
    	$sitters = $res->result_array();
    	$sql="select  * from user_profile where userid=$userid ";
    	$res = $CI->db->query($sql);
    	$profile = $res->result_array();
    	if(empty($profile))$profile[0]=array();
    	
    	$return=array_merge($sitters[0], $profile[0]);
    	return $return;
    }
    
    function jobMail($job_id,$jobInfo,$mail_name,$to='mail',$from='mail',$sitter_id=false)
  	{
  		$CI =& get_instance();
  		$job_detail_email = jobString($jobInfo);
  		$clientInfo="";$sitterInfo="";
  		
  		$clientInfo = getClientDetail($jobInfo['client_user_id']);
  		
    	if($sitter_id)
    		$sitterInfo = getSitterDetail($sitter_id);
    	else if($jobInfo['sitter_user_id'])
    		$sitterInfo = getSitterDetail($jobInfo['sitter_user_id']);
    
    	$mailTemplate = getMailTemplates(array('mail_name'=>$mail_name));
    	$mailTemplate=$mailTemplate[0];
    	$text='';
    	if($from=='mail')
    		$from = $mailTemplate['from'];
    	else if($from=='sitter')
    		$from = $sitterInfo['username'];
    	else if($from=='client')
    		$from = $clientInfo['username'];
    
    	if($to=='mail')
    		$to = $mailTemplate['to'];
    	else if($to=='client')
    	{
    		$to = $clientInfo['username'];
    		/* if($_SERVER['HTTP_HOST'] == 'projects.sofmen.com')
    		{
    			$short = substr($to, 0, strpos( $to, '@'));
    			$to = $short."@mailinator.com";
    		} */
    		$to_name = $clientInfo['firstname'].' '.$clientInfo['lastname'];
    	}
    	else if($to=='sitter')
    	{
    		$to = $sitterInfo['username'];
    		/* if($_SERVER['HTTP_HOST'] == 'projects.sofmen.com')
    		{
    			$short = substr($to, 0, strpos( $to, '@'));
    			$to = $short."@mailinator.com";
    		} */
    		$to_name = $sitterInfo['firstname'].' '.$sitterInfo['lastname'];
    			
    	}
    	
    	$cc = explode(',',$mailTemplate['cc']);
    	$bcc = explode(',',$mailTemplate['bcc']);
    
    
    	$clientReplace = clientMailString($clientInfo);
    	$sitterReplace = sitterMailString($sitterInfo);
    
    	$subject = str_replace(array_keys($sitterReplace),$sitterReplace,$mailTemplate['subject']);
    	$subject = str_replace(array_keys($clientReplace),$clientReplace,$subject);
    
    
    	$to_replace_job = jobString($jobInfo);
    
    	$text="";
    	$text = str_ireplace('{viewlink}','sitters/preview/job/'.$job_id,$mailTemplate['description']);
    	$text = str_ireplace(array_keys($clientReplace),$clientReplace,$text);
    	$address = getAddress($jobInfo['address_id']);
    	$address = addressMailString($address[0]);
    	$text =str_ireplace(array_keys($address),$address,$text);
    	$text = str_ireplace(array_keys($sitterReplace),$sitterReplace,$text);
    	$text = str_ireplace(array_keys($to_replace_job),$to_replace_job,$text);
    	
    	//print_r($text);die;
    	$CI->load->library('email');

    	
    	$CI =& get_instance();
    	$CI->email->from($from);
    	//$CI->email->reply_to($from);
    	$CI->email->to($to,$to_name);
    	$CI->email->subject($subject);
    	$CI->email->message($text);
    	
    	
    	if(!empty($cc))foreach($cc as $c){
    		$CI->email->cc($c);
    	};
    	if(!empty($bcc))foreach($bcc as $c){
    		$CI->email->bcc($c);
    	};

    	$sendMail = $CI->email->send();
    }
    
    
    function getMailTemplates($search=array())
    {
    	$CI =& get_instance();
    	if(array_key_exists('mail_id', $search))
    		$where = " and mail_id = {$search['mail_id']}";
    
    	if($search['mail_name'])
    		$where = " and mail_name = '{$search['mail_name']}'";
    
    	$sql ="SELECT *
    	FROM `mail_templates` where 1 $where";
    	$res = $CI->db->query($sql);
    	$results = $res->result_array();
    	if($where!=''){
    		$results[0]['description']=stripcslashes($results[0]['description']);
    	}
    	return $results;
    }
    
    
    function replace_null($data)
    {
    	$check= array();
    	foreach($data as $key=>$val)
    	{
    		if($val == "" || $val == null ||$val === "0")
    		{
    			$check[$key] = "";
    		}
    		else
    		{
    			$check[$key] = $val;
    		}
    	}
    	return $check;
    }
    
    
    function mime_content_type_new($filename) {
    	 
    	$mime_types = array(
    
    			'txt' => 'text/plain',
    			'htm' => 'text/html',
    			'html' => 'text/html',
    			'php' => 'text/html',
    			'css' => 'text/css',
    			'js' => 'application/javascript',
    			'json' => 'application/json',
    			'xml' => 'application/xml',
    			'swf' => 'application/x-shockwave-flash',
    			'flv' => 'video/x-flv',
    
    			// images
    			'png' => 'image/png',
    			'jpe' => 'image/jpeg',
    			'jpeg' => 'image/jpeg',
    			'jpg' => 'image/jpeg',
    			'gif' => 'image/gif',
    			'bmp' => 'image/bmp',
    			'ico' => 'image/vnd.microsoft.icon',
    			'tiff' => 'image/tiff',
    			'tif' => 'image/tiff',
    			'svg' => 'image/svg+xml',
    			'svgz' => 'image/svg+xml',
    
    			// archives
    			'zip' => 'application/zip',
    			'rar' => 'application/x-rar-compressed',
    			'exe' => 'application/x-msdownload',
    			'msi' => 'application/x-msdownload',
    			'cab' => 'application/vnd.ms-cab-compressed',
    
    			// audio/video
    			'mp3' => 'audio/mpeg',
    			'qt' => 'video/quicktime',
    			'mov' => 'video/quicktime',
    
    			// adobe
    			'pdf' => 'application/pdf',
    			'psd' => 'image/vnd.adobe.photoshop',
    			'ai' => 'application/postscript',
    			'eps' => 'application/postscript',
    			'ps' => 'application/postscript',
    
    			// ms office
    			'doc' => 'application/msword',
    			'rtf' => 'application/rtf',
    			'xls' => 'application/vnd.ms-excel',
    			'ppt' => 'application/vnd.ms-powerpoint',
    
    			// open office
    			'odt' => 'application/vnd.oasis.opendocument.text',
    			'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
    	);
    
    	$ext = strtolower(array_pop(explode('.',$filename)));
    	if (array_key_exists($ext, $mime_types)) {
    		return $mime_types[$ext];
    	}
    	elseif (function_exists('finfo_open')) {
    		$finfo = finfo_open(FILEINFO_MIME);
    		$mimetype = finfo_file($finfo, $filename);
    		finfo_close($finfo);
    		return $mimetype;
    	}
    	else {
    		return 'application/octet-stream';
    	}
    }
    
    function dateFormat($date)
    {
    	return date('M d, Y  h:i A', strtotime($date));
    }
    
    function child_age($date)
    {	
    	$interval = date_diff(date_create(), date_create($date));
    	$age="";
    	if($interval->format('%y')!=0)
    	{
    		$age = $interval->format("%y");
    		
    		if($interval->format("%m")!= 0)
    		{
    			$age = $age+round(($interval->format("%m")/12),1);
    			$age=$age." Years";
    		}
    		else
    		{
    			$age = $age." Years";
    		}
    		return $age;
    	}
    	
    	if($interval->format("%m")!= 0)
    	{
    		//$age = $age.$interval->format("%m Months");
    		if($interval->format("%d")!= 0 && $interval->format("%d")>15)
    		{
    			$age = ($interval->format("%m")+1);
    			$age = $age." Months";
    		}
    		else
    		{
    			$age = $interval->format("%m Months");
    		}
    		return $age;
    	}	
    	
    	if($interval->format("%d")!= 0)
    	{
    		$age = $interval->format("%d Days");
    		return $age;
    	}
    	else
    		return "0 Days";
    }
    
	function getCCInfo($mystr)
    {
    	$replaceArray=Array('x','r','j','l','t','q','m','n','f','k');
    	 
    	$rev = base64_decode(strrev(str_replace('@','=',$mystr)));
    
    	$len = strlen($rev);
    	if($len > 23)
    	{
    
    		$ccvNumberCount = ($len - 23) + 3;
    		$pos4 = ($len - 23) + 7;
    	}
    	else
    	{
    		$ccvNumberCount = 3;
    		$pos4 = 7;
    	}
    	$pos1 = strlen(sprintf('%0.0f',($rev+0)));
    	$pos2 = $len-$pos1;
    	$pos3 = $pos1-$pos4;
    
    	foreach($replaceArray as $key => $val)
    		$rev = str_replace($val,$key,$rev);
    	$str1 = strrev(substr($rev,0,$pos1));
    	$str2 = substr($rev,$pos1,$pos2);
    
    	$card = substr($str1,0,$pos3).$str2;
    	$m = substr($str1,$pos3,2);
    	$y = substr($str1,($pos3+2),2);
    	$ccv = substr($str1,($pos3+4),$ccvNumberCount);
    	
    	return array('exp_date'=>$m."/20".$y , 'card_num'=>$card, 'card_code'=>$ccv);
    
    }
    
    
    function clientMail($client_id,$mail_name,$to='client',$from='mail',$additional =array())
    {
    	//if($client_id)
    	$clientInfo = getClientDetail($client_id);
    
    	$mailTemplate = getMailTemplates(array('mail_name'=>$mail_name));
    	$mailTemplate=$mailTemplate[0];
    	$text='';
    	if($from=='mail')
    		$from = $mailTemplate['from'];
    	else if($from=='client')
    		$from = $clientInfo['username'];
    
    	if($to=='mail')
    		$to = $mailTemplate['to'];
    
    	else if($to=='client')
    	{
    		$to = $clientInfo['username'];
    		$to_name = $clientInfo['firstname'].' '.$clientInfo['lastname'];
    
    	}
    	
    	$cc = explode(',',$mailTemplate['cc']);
    	$bcc = explode(',',$mailTemplate['bcc']);
    
    	$clientReplace = clientMailString($clientInfo);
    
    	$subject = str_ireplace(array_keys($clientReplace),$clientReplace,$mailTemplate['subject']);
    	$subject = str_ireplace(array_keys($additional),$additional,$mailTemplate['subject']);
    
    	$text = str_ireplace('{viewlink}','clients/preview/job/'.$client_id,$mailTemplate['description']);
    	//$text =str_ireplace(array_keys($address),$address,$text);
    	$text = str_ireplace(array_keys($clientReplace),$clientReplace,$text);
    	$text = str_ireplace(array_keys($additional),$additional,$text);
  
    	$CI =& get_instance();
    	$CI->load->library('email');

    	$CI =& get_instance();
    	$CI->email->from($from);
    	//$CI->email->reply_to($from);
    	$CI->email->to($to,$to_name);
    	$CI->email->subject($subject);
    	$CI->email->message($text);
    	
    	
    	if(!empty($cc))foreach($cc as $c){
    		$CI->email->cc($c);
    	};
    	if(!empty($bcc))foreach($bcc as $c){
    		$CI->email->bcc($c);
    	};
    	
    	$sendMail = $CI->email->send();
    }

    //function array_column()
    if (! function_exists('array_column')) {
    	function array_column(array $input, $columnKey, $indexKey = null) {
    		$array = array();
    		foreach ($input as $value) {
    			if ( ! isset($value[$columnKey])) {
    				trigger_error("Key \"$columnKey\" does not exist in array");
    				return false;
    			}
    			if (is_null($indexKey)) {
    				$array[] = $value[$columnKey];
    			}
    			else {
    				if ( ! isset($value[$indexKey])) {
    					trigger_error("Key \"$indexKey\" does not exist in array");
    					return false;
    				}
    				if ( ! is_scalar($value[$indexKey])) {
    					trigger_error("Key \"$indexKey\" does not contain scalar value");
    					return false;
    				}
    				$array[$value[$indexKey]] = $value[$columnKey];
    			}
    		}
    		return $array;
    	}
    }
    
    function convert_number_to_words($number) {
    	 
    	$hyphen      = '-';
    	$conjunction = ' and ';
    	$separator   = ', ';
    	$negative    = 'negative ';
    	$decimal     = ' point ';
    	$dictionary  = array(
    			0                   => 'zero',
    			1                   => 'one',
    			2                   => 'two',
    			3                   => 'three',
    			4                   => 'four',
    			5                   => 'five',
    			6                   => 'six',
    			7                   => 'seven',
    			8                   => 'eight',
    			9                   => 'nine',
    			10                  => 'ten',
    			11                  => 'eleven',
    			12                  => 'twelve',
    			13                  => 'thirteen',
    			14                  => 'fourteen',
    			15                  => 'fifteen',
    			16                  => 'sixteen',
    			17                  => 'seventeen',
    			18                  => 'eighteen',
    			19                  => 'nineteen',
    			20                  => 'twenty',
    			30                  => 'thirty',
    			40                  => 'fourty',
    			50                  => 'fifty',
    			60                  => 'sixty',
    			70                  => 'seventy',
    			80                  => 'eighty',
    			90                  => 'ninety',
    			100                 => 'hundred',
    			1000                => 'thousand',
    			1000000             => 'million',
    			1000000000          => 'billion',
    			1000000000000       => 'trillion',
    			1000000000000000    => 'quadrillion',
    			1000000000000000000 => 'quintillion'
    	);
    	 
    	if (!is_numeric($number)) {
    		return false;
    	}
    	 
    	if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
    		// overflow
    		trigger_error(
    				'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
    				E_USER_WARNING
    		);
    		return false;
    	}
    
    	if ($number < 0) {
    		return $negative . convert_number_to_words(abs($number));
    	}
    	 
    	$string = $fraction = null;
    	 
    	if (strpos($number, '.') !== false) {
    		list($number, $fraction) = explode('.', $number);
    	}
    	 
    	switch (true) {
    		case $number < 21:
    			$string = $dictionary[$number];
    			break;
    		case $number < 100:
    			$tens   = ((int) ($number / 10)) * 10;
    			$units  = $number % 10;
    			$string = $dictionary[$tens];
    			if ($units) {
    				$string .= $hyphen . $dictionary[$units];
    			}
    			break;
    		case $number < 1000:
    			$hundreds  = $number / 100;
    			$remainder = $number % 100;
    			$string = $dictionary[$hundreds] . ' ' . $dictionary[100];
    			if ($remainder) {
    				$string .= $conjunction . convert_number_to_words($remainder);
    			}
    			break;
    		default:
    			$baseUnit = pow(1000, floor(log($number, 1000)));
    			$numBaseUnits = (int) ($number / $baseUnit);
    			$remainder = $number % $baseUnit;
    			$string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
    			if ($remainder) {
    				$string .= $remainder < 100 ? $conjunction : $separator;
    				$string .= convert_number_to_words($remainder);
    			}
    			break;
    	}
    	 
    	if (null !== $fraction && is_numeric($fraction)) {
    		$string .= $decimal;
    		$words = array();
    		foreach (str_split((string) $fraction) as $number) {
    			$words[] = $dictionary[$number];
    		}
    		$string .= implode(' ', $words);
    	}
    	 
    	return $string;
    }