<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('Customer.class.php');
$customer = createObjAndAddToCol(new Customer());

$obj = $customer;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'customerForm';
$quickView = false;
		
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('SID', $obj->tableName . '.sid'));
array_push($arrSearchColumn, array('Nama', $obj->tableName . '.name'));
array_push($arrSearchColumn, array('Kategori', $obj->tableCategory . '.name'));
array_push($arrSearchColumn, array('Email', $obj->tableName . '.email'));
  
$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = ''; 

function generateQuickView($obj,$id){ 
	    
	$detail = '';
	   
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>