<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass(array('Voucher.class.php'));
$voucher= new Voucher();
$obj = $voucher;    

$fieldValue = $obj->tableName.'.code';

include 'ajax-general.php';
 
die;
  
?>