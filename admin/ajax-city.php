<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('City.class.php');
$city = createObjAndAddToCol(new City());

$obj = $city;   

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');  

include 'ajax-general.php';
 
die;
  
?>