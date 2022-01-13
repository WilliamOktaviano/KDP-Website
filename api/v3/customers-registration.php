<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';

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
    $customer = new Customer();
    $customer->resetErrorLog();
    
    $postVars = $arrPostVars[$i];

    $failedMsgs = array();
    if (!array_key_exists('username', $postVars)) array_push($failedMsgs, $class->lang['username'].". ".$class->errorMsg[603]);
    if (!array_key_exists('password', $postVars)) array_push($failedMsgs, $class->lang['password'].". ".$class->errorMsg[603]);
    if (!array_key_exists('name', $postVars)) array_push($failedMsgs, $class->lang['name'].". ".$class->errorMsg[603]);
    if (!array_key_exists('email', $postVars)) array_push($failedMsgs, $class->lang['email'].". ".$class->errorMsg[603]);
    if (!array_key_exists('phone', $postVars)) array_push($failedMsgs, $class->lang['phone'].". ".$class->errorMsg[603]);
    
    if (count($failedMsgs) > 0){
        $tempArray['message'] = $failedMsgs;
        array_push($arrFailed, $tempArray);
        continue;
    }
    $tempArray = array();
    $tempArray['username'] = $postVars['username'];
    
    $arr['_mnv-api'] = 1;
    $arr['userName'] = $postVars['username'];
    $arr['password'] = $postVars['password'];
    $arr['name'] = $postVars['name'];
    $arr['email'] = $postVars['email'];
    $arr['phone'] = $postVars['phone'];

    $arr['code'] = 'XXXXX';
    $arr['createdBy'] = 0;
    $arr['selStatus'] = 1;   
    $arr['hidCityKey'] = 0; 
    $arr['address1'] = '';  
    $arr['address2'] = '';  
    $arr['zipCode'] = '';  
    $arr['mobile'] = '';  
    $arr['fax'] = '';  
    $arr['description'] = '';  
    $arr['selTermOfPayment'] = 0; 
    $arr['frontendRegistration'] = 1;  
    $arr['fromFE'] = 1;

     try{ 
        if(!$customer->oDbCon->startTrans(true))
            throw new Exception($customer->errorMsg[100]);
 
        $result = $customer->addData($arr);
        $response = getResponseValue($result,true);  
         
         if(empty($response['errorMessage'])){

            array_push($ARR_RETURN_VALUE,
                     array(
                            //'response_code' => 200,
                            'code' => $response['returnValue']['data']['code'],  
                            'message' => $response['successMessage'],
                            //'data' => $response['returnValue']['data'],
                        )
                    ); 

            $hasSuccessValue = true;

        }else{ 
            addFailedRows($arrFailed, array(
                    'code' => $PARAM['code'],
                    'message' => $response['errorMessage'], 
            ));

        } 
 
        $customer->oDbCon->endTrans();

    }catch(Exception $e){ 
        $customer->oDbCon->rollback(); 
    }	

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