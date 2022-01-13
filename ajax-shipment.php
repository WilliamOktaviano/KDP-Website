<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';  
require_once '_global.php';  

includeClass(array("Shipment.class.php"));
$shipment = new Shipment();
$obj = $shipment;

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');  

include 'ajax-general.php';

switch ($_POST['action']){ 
	case 'getShippingInformation' : 
	        $serviceKey = $_POST['serviceKey'];
	        $fromCityKey = (!empty( $_POST['fromCityKey'])) ?  $_POST['fromCityKey'] : 0;
	        $toCityKey = (!empty( $_POST['toCityKey'])) ? $_POST['toCityKey'] : 0;
	        $useInsurance = $_POST['useInsurance'];
	        $totalValue = $_POST['totalValue'];
	        //$weight = (isset($_POST['weight']) && !empty($_POST['weight'])) ? $_POST['weight'] : 1; // weight harus per item
	        $arrItems =  $_POST['items'];
            $destination =  $_POST['destination'];
             
            // sementara isi yg penting dulu utk biteship
            $rs = $obj->getShippingInformation(array('serviceKey' => $serviceKey,
                                                     'destination' => $destination,
                                                     'items' => $arrItems
            ));
        
            //$class->setLog($rs,true);
            echo json_encode($rs); 
            break;

	case 'getAvailableShippingServices' : 
	     
	        $fromCityKey = $_POST['fromCityKey'];
	        $toCityKey = $_POST['toCityKey'];
        
            $rs = $obj->getAvailableShippingServices($fromCityKey ,$toCityKey);
            //$class->setLog($rs,true);
            echo json_encode($rs); 
            break;

}

die;
  
?>
