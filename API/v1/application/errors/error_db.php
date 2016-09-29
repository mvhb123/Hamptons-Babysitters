<?php 
	
	$string = "post--".json_encode($_POST)."-get-".json_encode($_GET).'-files-'.json_encode($_FILES);
	log_message('error', 'Query POST: '.json_encode($_POST));
	log_message('error', 'Query GET: '.json_encode($_GET));
	log_message('error', 'Query FILE: '.json_encode($_FILES));
	log_message('error', 'Query error detail: '.$message.$heading);
	print_r($message);
	
	mail("anjali.jain@sofmen.com", "DB ERROR Babysitter api",$message.$string, $heading);
	
	global $error_user_id	;
	 $error_user_id;
	if(!empty($error_user_id))
	{
		$sql = 'DELETE FROM `users` WHERE `users`.`userid` = '.$error_user_id.'';
		mysql_query ( $sql);
		$sql = 'DELETE FROM `user_profile` WHERE `user_profile`.`userid` = '.$error_user_id.'';
		mysql_query ( $sql);
		$sql = 'DELETE FROM `clients_detail` WHERE `clients_detail`.`userid` = '.$error_user_id.'';
		mysql_query ( $sql);
		$sql = 'DELETE FROM `address` WHERE `address`.`userid` = '.$error_user_id.'';
		mysql_query ( $sql);
	}
	
	$data = array("errorCode"=>"IS1"
					,"errorMessage"=>"Internal server error, Please try after some time"
					,"errorDisplayMessage"=>"Internal server error, Please try after some time"
					,"status"=>'failed');

	echo json_encode($data);
?>