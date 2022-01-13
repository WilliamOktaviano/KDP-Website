<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 
includeClass('Page.class.php');
$page = createObjAndAddToCol(new Page());

$obj = $page;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'pageForm';
$quickView = false;
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Nama Page', $obj->tableName . '.pagename'));
array_push($arrSearchColumn, array('Judul', $obj->tableName . '.title')); 
 
$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = '';
$overwriteContextMenu['print'] = ''; 

function generateQuickView($obj,$id){
	return ''; 
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>