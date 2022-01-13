<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('Employee.class.php');
$employee = createObjAndAddToCol(new Employee());

$obj = $employee;   

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 2');  

include 'ajax-general.php';

die;
  
?>