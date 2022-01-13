<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  
 
includeClass(array('Customer.class.php'));
$customer = new Customer();

if (isset($_GET) && !empty($_GET['pkey']) && !empty($_GET['activationhashkey'])){ 
	 $result =  $customer->resetPassword($_GET['pkey'],$_GET['activationhashkey']);  
	 if ($result[0]['valid'])
	 	$content = $class->lang['resetPasswordSuccessful'];
	 else
	 	$content = $result[0]['message'];
}
  
$arrTwigVar['title'] = $class->lang['accountRecovery']; 
$arrTwigVar['content'] =  $content  ; 

echo $twig->render('page.html', $arrTwigVar);

?>
