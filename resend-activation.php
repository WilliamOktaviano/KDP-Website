<?php 
include '_config.php';
include '_include-fe-v2.php'; 
include '_global.php'; 

$arrTwigVar ['inputEmail'] =  $class->inputText('email'); 
$arrTwigVar['inputEmailPlaceholder'] = $class->inputText('email', array( 'etc' => 'placeholder="'.$class->lang['email'].'"'));

$arrTwigVar ['btnSubmit'] =   $class->inputSubmit('btnSave',$class->lang['resendActivation']); 
$_POST['action'] ='resend-activation';  
$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action'); 
 
echo $twig->render('resend-activation.html', $arrTwigVar);

?>
