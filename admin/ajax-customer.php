<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('Customer.class.php');
$customer = createObjAndAddToCol(new Customer());

$obj = $customer;   

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 2');  

include 'ajax-general.php';
  
if (isset($_GET) && !empty($_GET['action'])) {
            if (!isset($_GET) ||  empty($_GET['pkey']))   die;   
            $customerkey = $_GET['pkey'];
    
			switch ($_GET['action']){  
                case 'getDeliveryAddress' :    
                    $typekey = (isset($_GET['addresstype']) && !empty($_GET['addresstype'])) ? explode(',',$_GET['addresstype']) : array(0,1); 
                    $result = $obj->getAvailableAddress($customerkey,$typekey); 
                    echo json_encode($result); 
                    break; 
                    
                case 'getTaxInformation' :  
                    $result = $obj->getTaxInformation($customerkey);  
                    echo json_encode($result);  
                    break;
                     
                case 'getSalesman' :     
                    $result = $obj->getSalesman($customerkey);  
                    echo json_encode($result); 
                    break; 
             
                    
                 case 'getCustomersAge' :     
                    $result = $obj->getCustomersAge($customerkey);  
                    echo $result; 
                    break; 
            }
    
    
} 
die; 
?>
