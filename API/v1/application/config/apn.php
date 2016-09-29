<?php
/*
|||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
|| Apple Push Notification Configurations
|||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
*/


/*
|--------------------------------------------------------------------------
| APN Permission file
|--------------------------------------------------------------------------
|
| Contains the certificate and private key, will end with .pem
| Full server path to this file is required.
|
*/
$CI = & get_instance();

   
//$config['PermissionFile'] =  APPPATH.'../certificate/ck.pem'; // APPPATH.'../certificate/ck.pem';


	$config['PermissionFileSitter'] =  APPPATH.'../certificate/live/ck_SitterApp_prod.pem'; // APPPATH.'../certificate/ck.pem';
	$config['PermissionFileParent'] =  APPPATH.'../certificate/live/ck_ParentApp_prod.pem'; // APPPATH.'../certificate/ck.pem';
	$config['Sandbox'] = false;


/*
|--------------------------------------------------------------------------
| APN Private Key's Passphrase
|--------------------------------------------------------------------------
*/
$config['PassPhrase'] ='sofmen';//$CI->config->item('apn_PassPhrase');// 'sofmen';

/*
|--------------------------------------------------------------------------
| APN Services
|--------------------------------------------------------------------------
*/
//$CI->config->item('apn_sandbox');//true;
$config['PushGatewaySandbox'] = 'ssl://gateway.sandbox.push.apple.com:2195';
$config['PushGateway'] = 'ssl://gateway.push.apple.com:2195';

$config['FeedbackGatewaySandbox'] = 'ssl://feedback.sandbox.push.apple.com:2196';
$config['FeedbackGateway'] = 'ssl://feedback.push.apple.com:2196';


/*
|--------------------------------------------------------------------------
| APN Connection Timeout
|--------------------------------------------------------------------------
*/
$config['Timeout'] = 60;


/*
|--------------------------------------------------------------------------
| APN Notification Expiry (seconds)
|--------------------------------------------------------------------------
| default: 86400 - one day
*/
$config['Expiry'] = 86400;
