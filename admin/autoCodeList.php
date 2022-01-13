<?php  
require_once '../_config.php'; 
require_once '../_include.php'; 


$obj = $autoCode;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
 
if(!$security->isAdminLogin($securityObject,10,true));
  
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>