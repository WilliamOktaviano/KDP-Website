<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php'; 
require_once '_global.php';  

includeClass(array('Customer.class.php','VoucherTransaction.class.php'));
$voucherTransaction = new VoucherTransaction();
$customer = new Customer();

if(!$security->isMemberLogin(false)) 
	header('location:/logout'); 

$rsVoucherTrans = $voucherTransaction->searchData($voucherTransaction->tableName.'.customerkey',USERKEY,true,'and '.$voucherTransaction->tableName.'.statuskey in (2)', 'order by '.$voucherTransaction->tableName.'.trdate desc,'.$voucherTransaction->tableName.'.pkey desc');
$arrTwigVar ['rsActiveVoucher'] =  $rsVoucherTrans;

$rsVoucherTrans = $voucherTransaction->searchData($voucherTransaction->tableName.'.customerkey',USERKEY,true,'and '.$voucherTransaction->tableName.'.statuskey in (3)', 'order by '.$voucherTransaction->tableName.'.useddate desc,'.$voucherTransaction->tableName.'.pkey desc');
$arrTwigVar ['rsInactiveVoucher'] =  $rsVoucherTrans;

$minimumPoint = $class->loadSetting('minimumFirstPoint'); 

$rsCustomer = $customer->searchDataRow(array($customer->tableName.'.point',$customer->tableName.'.membershiplevel',$customer->tableName.'.canusepoint'),' and '.$customer->tableName.'.pkey = ' . $customer->oDbCon->paramString(USERKEY));
$arrTwigVar ['eligiblePoint'] =  $rsCustomer[0]['point'];  
$arrTwigVar ['minimumPoint'] =  $minimumPoint;  
$arrTwigVar ['membershipLevel'] =   $rsCustomer[0]['membershiplevel'];  
$arrTwigVar ['canUsePoint'] =   $rsCustomer[0]['canusepoint']; 
echo $twig->render('voucher.html', $arrTwigVar);

?>
