<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 


$rs = array();
$sql = 'select * from tablekey where decimalnumber > 0';
$rs = $class->oDbCon->doQuery($sql);
$rs = array_column($rs,null,'pkey');
echo json_encode($rs); 
die; 
  
?>