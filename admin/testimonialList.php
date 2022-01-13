<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('Testimonial.class.php'));
$testimonial = createObjAndAddToCol( new Testimonial()); 


$obj = $testimonial;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'testimonialForm';
  
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Nama', $obj->tableName . '.name'));   
array_push($arrSearchColumn, array('Perusahaan', $obj->tableName . '.company'));   
array_push($arrSearchColumn, array('Review', $obj->tableName . '.review'));   
 
   
$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = ''; 

function generateQuickView($obj,$id){
	return ''; 
}

 
include ('dataList.php');

?>