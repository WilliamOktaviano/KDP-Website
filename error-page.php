<?php 
include '_config.php';
include '_include-fe-v2.php'; 
include '_global.php';  

$errorId = 404;
if (isset($_GET) && !empty($_GET))
	$errorId = $_GET['id'];

$arrTwigVar['title'] =  $class->systemErrorMsg[$errorId];
$arrTwigVar['content'] = '<div style="text-align:center; margin-top:10%;">
							<div class="fa fa-exclamation-triangle" style="font-size:5em; color:#900;"></div>
							<div>'.$class->systemErrorMsg[$errorId].'</div>
						  </div>
						 ';

echo $twig->render('page.html', $arrTwigVar);

?>
