<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('DownloadCategory.class.php','Download.class.php'));
$download = createObjAndAddToCol(new Download()); 

$obj = $download;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'downloadForm';
$quickView = false;
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Nama', $obj->tableName . '.name'));
array_push($arrSearchColumn, array('Kategori', $obj->tableCategory . '.name')); 

		   
$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = '';
$overwriteContextMenu['print'] = ''; 

function generateQuickView($obj,$id){ 
	    
	$detail = '';
	   
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
