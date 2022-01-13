<?php 
require_once '_config.php'; 
require_once '_include-min.php';
require_once '_global.php';  

if(!$security->isMemberLogin(false)) 
	header('location:/logout'); 

$rs = $customer->getDataRowById(USERKEY);
    
$arrTwigVar ['inputHidId'] =  $class->input('hidden','hidId');

$id = $rs[0]['pkey'];

$_POST['action'] ='update-password';  
$arrTwigVar ['inputHidAction'] =  $class->input('hidden','action');   
 
 
$arrTwigVar ['inputCurrentPassword'] =  $class->input('password','currentPassword'); 
$arrTwigVar ['inputNewPassword'] =  $class->input('password','password'); 
$arrTwigVar ['inputPasswordConfirmation'] =  $class->input('password','passwordConfirmation');   
 
$arrTwigVar['btnSubmit'] =   $class->input('submit','btnSave',false,$class->lang['save']);  

echo $twig->render('update-password.html', $arrTwigVar);

?>