<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  
 
includeClass(array('Customer.class.php'));
$customer = new Customer();

if (isset($_GET) && !empty($_GET['pkey']) && !empty($_GET['activationhashkey'])){ 
	 $result =  $customer->activateMember($_GET['pkey'],$_GET['activationhashkey']); 
	 if ($result[0]['valid'])
	 	$content = $class->lang['accountActivationSuccessful'];
	 else
	 	$content = $result[0]['message'];
}
  
$arrTwigVar['title'] = $class->lang['accountActivation']; 
$arrTwigVar['content'] =  $content ; 

echo $twig->render('page.html', $arrTwigVar);

?>
