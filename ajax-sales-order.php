<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';  
require_once '_global.php';  

includeClass(array('SalesOrder.class.php'));
$salesOrder = new SalesOrder();
$obj = $salesOrder;   
 
include 'ajax-general.php';

switch ($_POST['action']){ 
	case 'getLatestTransaction' : 
	        
            $rs = $obj->getLatestTransaction();
            //$class->setLog($rs,true);
            echo json_encode($rs); 
            break;
 
}

die;
  
?>
