<?php
require_once '../../_config.php';  
require_once '_include.php';  

function endForRequestMethodError(){ 
    global $class;
    $RETURN_VALUE = array();
    $RETURN_VALUE['response_code'] = 400;
    $RETURN_VALUE['message'] = $class->errorMsg[213];
    http_response_code($RETURN_VALUE['response_code']); 
    echo json_encode($RETURN_VALUE); 
    die;   
}

if(!isset($_GET) && empty($_GET['userkey'])) endForRequestMethodError();

$hasSuccessValue = false;
$arrFailed = array();
$ARR_RETURN_VALUE = array();

$responseCode = 200;
$message = ''; 

$RETURN_VALUE['response_code'] = $responseCode;
$RETURN_VALUE['data'] =  $class->getUserOTP($_GET['userkey']);       
$RETURN_VALUE['message'] = $message;

http_response_code($RETURN_VALUE['response_code']); 
echo json_encode($RETURN_VALUE);
die;
 
?>