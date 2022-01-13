<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  
 
$redirectUrl = $class->loadSetting('loginRedirect');

if($security->isMemberLogin(false)){
	$redirectUrlLogin = (!empty($redirectUrl)) ? $redirectUrl : '/';
	header('location:'.$redirectUrlLogin); 
}

$arrTwigVar ['inputUserName'] =  $class->inputText('loginID'); 
$arrTwigVar ['inputPassword'] =  $class->inputPassword('loginPassword'); 

$arrTwigVar['inputUserNamePlaceholder'] = $class->inputText('loginID', array( 'etc' => 'placeholder="'.$class->lang['username'].'"')); 
$arrTwigVar['inputPasswordPlaceholder'] = $class->inputPassword('loginPassword', array( 'etc' => 'placeholder="'.$class->lang['password'].'"')); 


$arrTwigVar ['btnSubmit'] =   $class->inputSubmit('btnSave',$class->lang['login']); 
$_POST['action'] ='login';  
$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action'); 
 
$fbAppID = $class->loadSetting('FBAppID');
$fbSecretKey = $class->loadSetting('FBSecretKey');
$arrTwigVar['btnFFBLogin'] = '';


$arrTwigVar['loginRedirect'] = $redirectUrl;

if(!empty($fbAppID)){ 
    require_once 'assets/vendor/autoload.php';

    $fb = new Facebook\Facebook([
        'app_id' => $fbAppID,
        'app_secret' => $fbSecretKey,
        'default_graph_version' => 'v11.0',
    ]);

    $helper = $fb->getRedirectLoginHelper();

    $permissions = ['email', 'public_profile']; // Optional permissions 

    $arrTwigVar['fbLoginLink'] = $helper->getLoginUrl(HTTP_HOST.'api/sso/fb-login', $permissions); 
}

echo $twig->render('login.html', $arrTwigVar);

?>
