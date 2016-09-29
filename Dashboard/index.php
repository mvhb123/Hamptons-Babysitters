<?php

setlocale(LC_MONETARY, 'en_US.UTF-8');

//ini_set('zlib.output_compression', 1);
//ini_set('display_errors','On');   
date_default_timezone_set('America/New_York');

/* if(stripos($_SERVER['HTTP_ACCEPT_ENCODING'],'gzip')!==false){
  ob_start("ob_gzhandler");
  header('Content-Encoding:gzip');} */


if (isset($_GET['phpinfo']))
    phpinfo();
// Define path to application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/application'));

// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
//echo APPLICATION_PATH . '/configs/application.ini';
$application = new Zend_Application(
        APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini'
);

//var_dump($_SERVER);die;
ini_set('max_execution_time', 600);

    define('ValidationMode', "liveMode");

    date_default_timezone_set('America/New_York');
    define('ADMINMAIL', "admin@hamptonsbabysitters.com");
    define('SITE_URL', 'http://www.hamptonsbabysitters.com/a/');
    define('SITE_URL_HTTPS', 'https://www.hamptonsbabysitters.com/a/');
    define('SITE_ABS_PATH', $_SERVER["DOCUMENT_ROOT"] . '/a/'); 

    define('ADMIN_URL', SITE_URL . 'admin/');
    define('PROFILE_IMAGE_ABS_PATH', SITE_ABS_PATH . 'uploads/profile_images/');
    define('CHILDREN_IMAGE_ABS_PATH', SITE_ABS_PATH . 'uploads/children/');


    define('AUTHORIZENET_LOGIN_ID', '2z4fT9Ga');
    define('AUTHORIZENET_TRAN_KEY', '33328m3M2mEh2AyE');
  //  define('PAYMENT_TESTMODE', true);
define('PAYMENT_TESTMODE', false);
    define('AUTHORIZENET_MD5_SETTING', 'vicky');

    define('X_TEST', false);
    define('PAYMENT_URL', "https://secure.authorize.net/gateway/transact.dll");

    define('THUMB_SIZE_WIDTH', '100');
    define('THUMB_SIZE_HIEGHT', '90');

    define('LARGE_SIZE_WIDTH', '250');
    define('LARGE_SIZE_HIEGHT', '200');


    define('APPTHUMB_SIZE_WIDTH', '200');
    define('APPTHUMB_SIZE_HIEGHT', '200');

    define('PROFILE_IMAGES', 'uploads/profile_images/');
    define('CHILDREN_IMAGES', 'uploads/children/');


    define('DATE_FORMAT', 'm/d/Y');
    define('DATETIME_FORMAT', 'm/d/y h:i a');

    $application->bootstrap()
            ->run();






