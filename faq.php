<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php'; 
    
includeClass("FAQ.class.php");
$faq = new FAQ();

$rsFAQ = $faq->searchData( $faq->tableName.'.statuskey','1',true,'',' order by orderlist asc');
 
$arrTwigVar ['rsFAQ'] =  $rsFAQ;

echo $twig->render('faq.html', $arrTwigVar);

?>