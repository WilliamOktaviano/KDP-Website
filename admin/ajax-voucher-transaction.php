<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass(array('VoucherTransaction.class.php'));
$voucherTransaction = new VoucherTransaction();

$obj = $voucherTransaction;   
$fieldValue = $obj->tableName.'.code'; 

include 'ajax-general.php';

if (isset($_GET) && !empty($_GET['action'])) {
			switch ( $_GET['action']){  
                    
                case 'getAvailableVoucher' : 
                    
                    if (!isset($_GET) || empty($_GET['customerkey']))  die;
                    
                    $rs = $obj->getAvailableVoucher($_GET['customerkey']);
                    echo json_encode($rs); 
                    break;
                    
   				case 'calculateVoucherValue' : 
                    
                    if (!isset($_GET) || empty($_GET['voucherkey']))  die;
                    if (!isset($_GET) || empty($_GET['totalSales']))  die;
                    
                    $voucherValue = $obj->calculateVoucherValue($_GET['voucherkey'],$_GET['totalSales'] );
                    echo $voucherValue; 
                    break;
                    
 
            }
    
}
 
die;
  
?>