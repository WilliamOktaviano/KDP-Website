<?php  
// ========================================================================== INITIALIZE ==========================================================================
require_once '../_config.php'; 
require_once '../_include-v2.php';

includeClass('Voucher.class.php');
$voucher = new Voucher();

$obj = $voucher;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'voucherForm';

 // sementara     
$customFile = getPersonalizedFiles($FILE_NAME);   
if($customFile <> $FILE_NAME) include DOC_ROOT.$customFile;
		
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Nama', $obj->tableName . '.name'));
array_push($arrSearchColumn, array('Jenis Voucher', $obj->tableType . '.name'));
array_push($arrSearchColumn, array('Kategori Voucher', $obj->tableCategory . '.name'));

    
$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = ''; 
 
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
