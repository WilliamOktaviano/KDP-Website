<?php 
require_once '_config.php'; 
include '_include-fe-v2.php'; 
require_once '_global.php';  
 
includeClass(array('Customer.class.php','MembershipLevel.class.php'));
$customer = new Customer();
$membershipLevel = new MembershipLevel();

if(!$security->isMemberLogin(false)) 
	header('location:/logout'); 

$_POST['action'] ='upgrade-membership';  
$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action'); 
$arrTwigVar ['inputHidUsePoint'] =  $class->inputHidden('hidUsePoint');

$rsCustomer = $customer->searchDataRow(array($customer->tableName.'.point',$customer->tableName.'.membershiplevel',$customer->tableName.'.canusepoint'),' and '.$customer->tableName.'.pkey = ' . $customer->oDbCon->paramString(USERKEY));
$rsMembership = $membershipLevel->searchDataRow(array($membershipLevel->tableName.'.pkey', $membershipLevel->tableName.'.sellingprice'),' and '.$membershipLevel->tableName.'.statuskey = 1',' order by '.$membershipLevel->tableName.'.pkey asc');					
$rsMembership = array_column($rsMembership,null,'pkey');

// next membership level
$nextMembershipLevel = array();
for($i=0;$i<count($rsMembership);$i++){ 
	if($rsMembership[$i]['pkey'] == $rsCustomer[0]['membershiplevel']){
		$nextMembershipLevel = (!empty($rsMembership[$i+1])) ? $rsMembership[$i+1] : array(); 
		break;
	}
}

if(empty($nextMembershipLevel))  header('location:/profile'); 

$rewardPointValue = $class->loadSetting('rewardsPointUnitValue');
$minimumPoint = $class->loadSetting('minimumFirstPoint'); 

$point = $rsCustomer[0]['point'];
$pointAmount = ceil($nextMembershipLevel['sellingprice'] / $rewardPointValue);
 
$arrTwigVar ['point'] = $point ;  
$arrTwigVar ['rsMembership'] = $rsMembership ;   
$arrTwigVar ['nextMembershipLevel'] = $nextMembershipLevel;   
$arrTwigVar ['upgradePointAmount'] = $pointAmount ;  
$arrTwigVar ['minimumPoint'] =  $minimumPoint;  
$arrTwigVar ['membershipLevel'] =   $rsCustomer[0]['membershiplevel'];  
$arrTwigVar ['canUsePoint'] =   $rsCustomer[0]['canusepoint']; 

$arrTwigVar ['btnTradePoint'] =   $class->inputSubmit('btnTradePoint',$lang->lang['trade'] .' '.$pointAmount.' '.$lang->lang['point'], array('etc' => 'rel-use-point="1"'  , 'disabled' => ($rsCustomer[0]['canusepoint'] == 0) ? true:false )); // untuk checkout manual
$arrTwigVar ['btnPurchase'] =   $class->inputSubmit('btnSave',$lang->lang['upgradeMembership'], array('etc' => 'rel-use-point="0"' , 'add-class' => 'btn-princeton-orange')); // untuk checkout manual

$arrTwigVar ['ACTIVE_MENU'] =  array('/profile.php');
  
echo $twig->render('upgrade-membership.html', $arrTwigVar);

?>