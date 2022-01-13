<?php   
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('MembershipSubscription.class.php');
$membershipSubscription = createObjAndAddToCol( new MembershipSubscription()); 

$obj = $membershipSubscription;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'membershipSubscriptionForm';
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Tanggal', $obj->tableName . '.trdate'));
//array_push($arrSearchColumn, array('Gudang', $obj->tableWarehouse. '.name'));
array_push($arrSearchColumn, array('Pelanggan', $obj->tableCustomer. '.name'));
array_push($arrSearchColumn, array('Total', $obj->tableName. '.grandtotal'));
array_push($arrSearchColumn, array('Catatan', $obj->tableName. '.trdesc'));

 
$quickView = false; 
$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = '';  

function generateQuickView($obj,$id){ 
	$detail = '';
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
