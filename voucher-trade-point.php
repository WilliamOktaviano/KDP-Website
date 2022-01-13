<?php 
require_once '_config.php'; 
include '_include-fe-v2.php'; 
require_once '_global.php';  

includeClass(array('RewardsPoint.class.php','Voucher.class.php'));
$voucher = new Voucher();
$customer = new Customer();

if(!$security->isMemberLogin(false)) 
	header('location:/logout'); 

$_POST['action'] ='trade';  
$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action'); 


$rsVoucher = $voucher->searchDataRow(array($voucher->tableName.'.pkey',$voucher->tableName.'.name',$voucher->tableName.'.pointneeded',$voucher->tableName.'.shortdesc'),
									 ' and '.$voucher->tableName.'.statuskey in (2)'
									);
$rsCustomer = $customer->searchDataRow(array($customer->tableName.'.point',$customer->tableName.'.membershiplevel',$customer->tableName.'.canusepoint'),' and '.$customer->tableName.'.pkey = ' . $customer->oDbCon->paramString(USERKEY));

$minimumPoint = $class->loadSetting('minimumFirstPoint'); 

foreach($rsVoucher as $key=>$row){
	$rsVoucher[$key]['hidVoucherKey'] = $class->inputHidden('hidVoucherKey[]', array('value' => $row['pkey']));
	$rsVoucher[$key]['inputQty'] = $class->inputNumber('qty[]',array('etc' => 'style="width: 5em;"'));
	$rsVoucher[$key]['hidInputQty'] = $class->inputHidden('qty[]');
	
}

$arrTwigVar ['rsVoucher'] =  $rsVoucher;
$arrTwigVar ['eligiblePoint'] =  $rsCustomer[0]['point'];  
$arrTwigVar ['minimumPoint'] =  $minimumPoint;  
$arrTwigVar ['membershipLevel'] =   $rsCustomer[0]['membershiplevel'];  
$arrTwigVar ['canUsePoint'] =   $rsCustomer[0]['canusepoint'];  

$arrTwigVar ['btnSubmit'] =   $class->inputSubmit('btnSave',$lang->lang['trade']); // untuk checkout manual
$arrTwigVar ['ACTIVE_MENU'] =  array('/voucher.php');

echo $twig->render('voucher-trade-point.html', $arrTwigVar);

?>
