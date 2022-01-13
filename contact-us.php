<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  
   
$arrTwigVar ['inputName'] =  $class->inputText('name'); 
$arrTwigVar ['inputPhone'] =  $class->inputText('phone'); 
$arrTwigVar ['inputEmail'] =  $class->inputText('email'); 
$arrTwigVar ['inputSubject'] =  $class->inputText('subject'); 
$arrTwigVar ['inputMessage'] =   $class->inputTextArea('message', array('etc' => 'style="height:10em"')); 
$arrTwigVar ['btnSubmit'] =   $class->inputSubmit('btnSave',$class->lang['send']); 
    
echo $twig->render('contact-us.html', $arrTwigVar);

?>
