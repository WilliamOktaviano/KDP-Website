<?php 
require_once '_config.php'; 
require_once '_include-min.php';
require_once '_global.php';  
 
if(isset($_GET) && !empty($_GET['page']))	
	$pagename = $_GET['page'];
else
	$pagename = ''; 
 
 
$rsPage = $page->searchData('pagename',$pagename,true);
$rsPage = $page->updateContentLang($rsPage);  

$arrTwigVar ['title'] =  $rsPage[0]['title']; 
$arrTwigVar ['content'] =  $rsPage[0]['detail']; 
$arrTwigVar ['pageKey'] =  $rsPage[0]['pkey']; 
$arrTwigVar ['pageImage'] =  $rsPage[0]['file'];  

echo $twig->render('page-content.html', $arrTwigVar);  
?>
