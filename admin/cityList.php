<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('City.class.php');
$city = createObjAndAddToCol(new City()); 


$obj = $city;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
										
if(!$security->isAdminLogin($securityObject,10,true));

$addDataFile = 'cityForm';
$quickView = false;
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Nama Lokasi', $obj->tableName . '.name'));
array_push($arrSearchColumn, array('Kategori', $obj->tableCategory . '.name'));
   
$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = ''; 

function generateQuickView($obj,$id){
	return ''; 
}

 
include ('dataList.php');

?>
