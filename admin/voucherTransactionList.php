<?php  
// ========================================================================== INITIALIZE ==========================================================================
include '../_config.php'; 
include '../_include-v2.php'; 

includeClass(array("VoucherTransaction.class.php"));
$voucherTransaction = new VoucherTransaction();
$obj = $voucherTransaction;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'voucherTransactionForm';
 
		
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Ref', $obj->tableName . '.refcode'));
array_push($arrSearchColumn, array('Pelanggan', $obj->tableCustomer. '.name'));

    
$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = ''; 

function generateQuickView($obj,$id){ 
	$detail = '';
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
