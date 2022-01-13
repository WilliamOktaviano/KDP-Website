<?php   
require_once '../_config.php'; 
require_once '../_include-v2.php'; 
 
includeClass('NewsletterSubscription.class.php');
$subscribe = createObjAndAddToCol( new NewsletterSubscription()); 

$obj = $subscribe;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'subscribeForm';
$quickView = false; 

$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('email', $obj->tableName. '.email'));
array_push($arrSearchColumn, array('email', $obj->tableName. '.phone'));

$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = ''; 
 
function generateQuickView($obj,$id){ 
	    
	$detail = '';
	
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
