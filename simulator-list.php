<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php'; 
require_once '_global.php';  

includeClass('OfferSimulator.class.php');
$offerSimulator = new OfferSimulator();

if(!$security->isMemberLogin(false)) 
	header('location:/logout'); 
 
$pageIndex = 0;
if ( isset($_GET) && !empty($_GET['page']) ){
	$pageIndex = $_GET['page'];
}
$arrTwigVar ['pageIndex'] =  $pageIndex;

$totalrowsperpage = 999;

$now = $pageIndex * $totalrowsperpage;
$limit = ' limit ' . $now . ', ' . $totalrowsperpage;
      
$criteria = ' and customerkey = ' . $offerSimulator->oDbCon->paramString(USERKEY);

$rsSimulator = $offerSimulator->searchData('','',true,$criteria,'order by '.$offerSimulator->tableName.'.pkey desc',$limit);
for ($i=0;$i<count($rsSimulator);$i++) {
    $rsSimulator[$i]['token'] = md5($rsSimulator[$i]['pkey'] .$rsSimulator[$i]['grandtotal'] . $offerSimulator->secretKey);
}

$totalPages = ceil( $offerSimulator->getTotalRows($criteria) / $totalrowsperpage); 

$arrTwigVar ['rsSimulator'] =  $rsSimulator;
$arrTwigVar ['totalPages'] =  $totalPages;   
  
echo $twig->render('simulator-list.html', $arrTwigVar);

?>