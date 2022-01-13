<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  
 
includeClass(array("VoucherTransaction.class.php"));

$voucherTransaction = new VoucherTransaction();

if(!$security->isMemberLogin(false)) 
	header('location:/logout'); 
  
$rsVoucher = (!empty(USERKEY)) ? $voucherTransaction->getAvailableVoucher(USERKEY) : array();

$arrTwigVar ['rsVoucher'] =  $rsVoucher;

echo $twig->render('popup-voucher.html', $arrTwigVar);  
 
?>