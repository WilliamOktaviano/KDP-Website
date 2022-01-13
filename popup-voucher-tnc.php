<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  
 
includeClass(array("Voucher.class.php"));

$voucher = new Voucher();

$rsVoucher = $voucher->getDataRowById($_GET['id']);
$arrTwigVar ['rsVoucher'] =  $rsVoucher[0];  

echo $twig->render('popup-voucher-tnc.html', $arrTwigVar);  
 
?>