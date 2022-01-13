<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/LoginLog.class.php';

$OBJ = $class;
$customer = new Customer();
$loginLog =  new LoginLog();

require_once '_global.php';

if($ACTION != 'POST') endForRequestMethodError();

$hasSuccessValue = false;
$arrFailed = array();
$ARR_RETURN_VALUE = array();

for($i=0;$i<$totalPostVars;$i++){
    $postVars = $arrPostVars[$i];
    $username = $postVars['username'];
    $password = $postVars['password'];

    if ($loginLog->isLockout($username,2)){ 
        $lockoutMinutes =  ceil($class->loadSetting('lockoutSecond') / 60); 
        $errorMsg = $class->errorMsg['login'][3];
    
        $patterns = array();
        $patterns[count($patterns)] = '/({{LOCKOUT_MINUTES}})/'; 
    
        $replacement = array();
        $replacement[count($replacement)] =$lockoutMinutes; 
    
        $errorMsg = preg_replace($patterns, $replacement, $errorMsg);  

        addFailedRows($arrFailed, array(
                'username' => $username,
                'message' => $errorMsg,
            )
        );
        
        continue;
    }
    
    $arrLoginLog = array('logintype' => 2, 'username' => $username, 'statuskey' => 2, 'userkey' => 0);
    
    $result = $customer->memberLogin($username,$password);
    
    $errorMessage = "";
    if (count ($result) == 0) $errorMessage = $class->errorMsg[300];
    else if ($result[0]['statuskey'] == 1) $errorMessage = $class->errorMsg['login'][1];
    else if ($result[0]['statuskey'] == 3) $errorMessage =  $class->errorMsg['login'][2];
    else if ($result[0]['statuskey'] == 2){
        $arrLoginLog['userkey'] =  $result[0]['pkey'];
        $arrLoginLog['statuskey'] = 1;

        array_push($ARR_RETURN_VALUE,
            array(
                'data' => array(
                    'pkey' => $result[0]['pkey'],
                    'code' => $result[0]['code'],
                    'user_full_name' => $result[0]['name'],
                    'username' => $result[0]['username'] 
                )
            )
        );

        $hasSuccessValue = true;
    }
    
    if ($errorMessage != ""){
        addFailedRows($arrFailed, array(
            'username' => $username,
            'message' => $errorMessage
        ));
    }
    
    $loginLog->addData($arrLoginLog);
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