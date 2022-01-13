<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('LoginLog.class.php');
$loginLog = new LoginLog();

if(!isset($_POST))
    die;
    

/* RECEIVE VALUE */
$userName=$_POST['loginID'];
$password=$_POST['loginPassword']; 
$token = (!empty($_POST['authcode'])) ? $_POST['authcode'] : '';
$deviceFingerprint = (!empty($_POST['df'])) ? $_POST['df'] : '';
//$rememberMe =  (isset($_POST['chkRemember']) && !empty($_POST['chkRemember'])) ? true : false; 

//if(DOMAIN_NAME == 'mvnet.wintera.co.id')  $rememberMe = false;
// matiin dulu
//$rememberMe = false;

$arrayToJs = array();
 
if ($loginLog->isLockout($userName,2)){ 
        $lockoutMinutes =  ceil($class->loadSetting('lockoutSecond') / 60); 
        $errorMsg = $class->errorMsg['login'][3];

        $patterns = array();
        $patterns[count($patterns)] = '/({{LOCKOUT_MINUTES}})/'; 

        $replacement = array();
        $replacement[count($replacement)] =$lockoutMinutes; 

        $errorMsg = preg_replace($patterns, $replacement, $errorMsg);  
    
        $arrayToJs['valid'] = false;
        $arrayToJs['message'] = $errorMsg; 
        
        echo json_encode($arrayToJs); 
        die;
} 


$arrLoginLog = array();
$arrLoginLog['logintype'] = 2;
$arrLoginLog['username'] = $userName;
$arrLoginLog['statuskey'] = 2;  
$arrLoginLog['userkey'] = 0 ;  
  
//if(DOMAIN_NAME == 'mvnet.wintera.co.id') $security->setLog(  date('d M Y H:i') .' => ' .$userName.'-'.$password.'-'.$token,true,'login-log');
    
$result = $security->adminLogin($userName,$password,$token, $deviceFingerprint);
 
if ( $result['valid']  == false){ 
    
	if (isset ($_SESSION[$class->loginAdminSession]))
		session_unset($_SESSION[$class->loginAdminSession]); 
    
    $loginLog->addData($arrLoginLog); 
    
}else{  
    // add ke session kalo gk pake OTP
    if (!$result['useOTP']){ 
        $_SESSION[$class->loginAdminSession]['id'] = base64_encode($result['data']['pkey']);
        $_SESSION[$class->loginAdminSession]['name'] = $result['data']['name']; 
        $_SESSION[$class->loginAdminSession]['username'] = $result['data']['username']; 
        $_SESSION[$class->loginAdminSession]['pass'] = $result['data']['password']; 
        $_SESSION[$class->loginAdminSession]['email'] = $result['data']['email'];  
        $_SESSION[$class->loginAdminSession]['photofile'] = $result['data']['photofile']; 
        $arrLoginLog['statuskey'] = 1 ;  
        $arrLoginLog['userkey'] = $result['data']['pkey'] ;  
        
        $loginLog->addData($arrLoginLog); 
        
        //if($rememberMe)
        //    $security->setCookiesLog($result['data']['pkey'],$deviceFingerprint);
        
    }
    
}		
	  

 echo json_encode($result); 
 die;
  
?>