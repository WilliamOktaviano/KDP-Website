<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/LoginLog.class.php';

$customer = new Customer();
$loginLog =  new LoginLog();

require_once '_global.php';

if($ACTION != 'PUT') endForRequestMethodError();

$hasSuccessValue = false;
$arrFailed = array();
$ARR_RETURN_VALUE = array();

for($i=0;$i<$totalPostVars;$i++){
    $postVars = $arrPostVars[$i];
    
    $failedMsgs = array();
    if (!array_key_exists('username', $postVars)) array_push($failedMsgs, $class->lang['username'].". ".$class->errorMsg[603]);
    if (!array_key_exists('current_password', $postVars)) array_push($failedMsgs, $class->lang['currentPassword'].". ".$class->errorMsg[603]);
    if (!array_key_exists('password', $postVars)) array_push($failedMsgs, $class->lang['password'].". ".$class->errorMsg[603]);
    
    if (count($failedMsgs) > 0){
        $tempArray['message'] = $failedMsgs;
        array_push($arrFailed, $tempArray);
        continue;
    }
    $tempArray = array();
    $tempArray['username'] = $postVars['username'];
    $username = $postVars['username'];
    $currentPassword = $postVars['current_password'];
    $password = $postVars['password'];

    $rs = $customer->isValueExisted('', 'username', $username);
    $customerKey = 0;
    
    if (count($rs) == 0){
        // Username ga ketemu
        $tempArray['message'] = $class->errorMsg[213];
        array_push($arrFailed, $tempArray);
        continue;
    }
    
    $customerKey = $rs[0]['pkey']; 

    try{
        if(!$customer->oDbCon->startTrans(true))
            throw new Exception($class->errorMsg[100]);
        
        $arr = array(); 
       	$arr['hidUserKey'] = $customerKey;
       	$arr['currentPassword'] = $currentPassword;
       	$arr['password'] = $password;
        $customer->setLog($arr,true);
		$result = $customer->updatePassword($arr);
        
        $customer->oDbCon->endTrans();
    }
    catch(Exception $e){
        $customer->oDbCon->rollback();
        // harus pake format errorJS
        //$tempArray['message'] = $e->getMessage();
        //array_push($arrFailed, $tempArray);
        continue;
    }
    
     $response = getResponseValue($result,true);  

     if(empty($response['errorMessage'])){

        array_push($ARR_RETURN_VALUE,
                 array(
                        //'response_code' => 200,
                        'username' => $username,  
                        'message' => $response['successMessage'],
                        //'data' => $response['returnValue']['data'],
                    )
                ); 

        $hasSuccessValue = true;

    }else{ 
        addFailedRows($arrFailed, array(
                'username' => $username,
                'message' => $response['errorMessage'], 
        ));

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