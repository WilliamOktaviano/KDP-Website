<?php 
require_once '_config.php'; 
include '_include-fe-v2.php'; 
require_once '_global.php';  

includeClass('SalesOrder.class.php');
$salesOrder = new SalesOrder();

if(!$security->isMemberLogin(false)) 
	header('location:/logout'); 
 
$pageIndex = ( isset($_GET) && !empty($_GET['page']) ) ? $_GET['page'] : 0; 
$arrTwigVar ['pageIndex'] =  $pageIndex;

$totalrowsperpage = $class->loadSetting('historyTransactionTotalPerPage');
if (empty($totalrowsperpage))
    $totalrowsperpage = 10;
 

$now = $pageIndex * $totalrowsperpage;
$limit = ' limit ' . $now . ', ' . $totalrowsperpage;
      
$criteria = ' and customerkey = ' . $salesOrder->oDbCon->paramString(USERKEY);

$rsSalesOrder = $salesOrder->searchData('','',true,$criteria,'order by '.$salesOrder->tableName.'.pkey desc',$limit);
for ($i=0;$i<count($rsSalesOrder);$i++) {
    $rsSalesOrder[$i]['token'] = md5($rsSalesOrder[$i]['pkey'] .$rsSalesOrder[$i]['grandtotal'] . $salesOrder->secretKey);

    $rsSalesOrder[$i]['paymentInformation'] = '';
    
    // set informasi pembayaran
    if($rsSalesOrder[$i]['statuskey'] == 1){ 
        if(!empty($rsSalesOrder[$i]['paymentgatewayinvoiceurl']))
            $rsSalesOrder[$i]['paymentInformation'] = '<a href="'.$rsSalesOrder[$i]['paymentgatewayinvoiceurl'].'" target="_blank">'.$class->lang['paymentInstruction'].'</a>';
    }
}



$totalPages = ceil( $salesOrder->getTotalRows($criteria) / $totalrowsperpage); 

$arrTwigVar ['rsSalesOrder'] =  $rsSalesOrder;
$arrTwigVar ['totalPages'] =  $totalPages;   
  
echo $twig->render('transaction-history.html', $arrTwigVar);

?>