<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php'; 
require_once '_global.php';  

includeClass('RewardsPoint.class.php');
$rewardsPoint = new RewardsPoint();
$customer = new Customer();

if(!$security->isMemberLogin(false)) 
	header('location:/logout'); 
 
$pageIndex = ( isset($_GET) && !empty($_GET['page']) ) ? $_GET['page'] : 0; 
$arrTwigVar ['pageIndex'] =  $pageIndex;
 
$totalrowsperpage = $class->loadSetting('historyTransactionTotalPerPage');
if (empty($totalrowsperpage))
    $totalrowsperpage = 10;

$now = $pageIndex * $totalrowsperpage;
$limit = ' limit ' . $now . ', ' . $totalrowsperpage;
      
$criteria = ' and '.$rewardsPoint->tableName.'.customerkey = ' . $rewardsPoint->oDbCon->paramString(USERKEY);
$criteria .= ' and '.$rewardsPoint->tableName.'.statuskey in (2,3)';

$rsPoint = $rewardsPoint->searchData('','',true,$criteria,'order by '.$rewardsPoint->tableName.'.pkey desc',$limit);

$totalPages = ceil($rewardsPoint->getTotalRows($criteria) / $totalrowsperpage); 

$minimumPoint = $class->loadSetting('minimumFirstPoint'); 
$rsCustomer = $customer->searchDataRow(array($customer->tableName.'.point',$customer->tableName.'.membershiplevel',$customer->tableName.'.canusepoint'),' and '.$customer->tableName.'.pkey = ' . $customer->oDbCon->paramString(USERKEY));
$arrTwigVar ['eligiblePoint'] =  $rsCustomer[0]['point'];  
$arrTwigVar ['minimumPoint'] =  $minimumPoint;  
$arrTwigVar ['membershipLevel'] =   $rsCustomer[0]['membershiplevel'];  
$arrTwigVar ['canUsePoint'] =   $rsCustomer[0]['canusepoint'];  
$arrTwigVar ['rsPoint'] =  $rsPoint;
$arrTwigVar ['totalPages'] =  $totalPages;      
  
echo $twig->render('point-history.html', $arrTwigVar);

?>