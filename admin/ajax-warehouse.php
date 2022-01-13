<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('Warehouse.class.php');
$warehouse = createObjAndAddToCol(new Warehouse());

$obj = $warehouse;    

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');   

include 'ajax-general.php';
 
die;
  
?>