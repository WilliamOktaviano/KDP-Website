<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';

$OBJ = $class;
$customer = new Customer();

require_once '_global.php';


// TWIG for email template
require_once  $_SERVER ['DOCUMENT_ROOT'].'/Twig/Autoloader.php';
Twig_Autoloader::register();
$loader = new Twig_Loader_Filesystem($class->templateDocPath); 
$twig = new Twig_Environment($loader); 
$twig->addExtension(new Twig_Extension_Array());   


if($ACTION != 'POST') endForRequestMethodError();

$hasSuccessValue = false;
$arrFailed = array();
$ARR_RETURN_VALUE = array();

    
for($i=0;$i<$totalPostVars;$i++){
    
    $arrPostVars[$i]['_mnv-api'] = 1;
    
    $postVars = $arrPostVars[$i];
    $email = $postVars['email'];
    
    $result = $customer->requestRecoverAccount($postVars);

    $temp_array = array();
    $temp_array['email'] = $email;
    $temp_array['messages'] = array_column($result, 'message');

    if ($result[0]['valid']){
        array_push($ARR_RETURN_VALUE, $temp_array);
        $hasSuccessValue = true;
    }
    else array_push($arrFailed, $temp_array);
}

$RETURN_VALUE['response_code'] = ($hasSuccessValue) ? 200 : 409;
$RETURN_VALUE['success_rows'] = count($ARR_RETURN_VALUE);
$RETURN_VALUE['success_data'] = $ARR_RETURN_VALUE;
$RETURN_VALUE['failed_rows'] = count($arrFailed);
$RETURN_VALUE['failed_data'] = $arrFailed;

http_response_code($RETURN_VALUE['response_code']); 
echo json_encode($RETURN_VALUE);
die;
?>