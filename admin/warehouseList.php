<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array("Warehouse.class.php"));
$warehouse = createObjAndAddToCol(new Warehouse()); 

$obj = $warehouse;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'warehouseForm';
$quickView = false;
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Nama', $obj->tableName . '.name'));
array_push($arrSearchColumn, array('Nama', $obj->tableCompany . '.name'));
  

$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = ''; 

function generateQuickView($obj,$id){ 
	    
	$detail = '';
	   
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>