<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass("Contact.class.php");
$contact = new Contact();

$obj = $contact;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'contactUsForm';
 

$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Kategori', $obj->tableCategory . '.name'));
array_push($arrSearchColumn, array('Nama', $obj->tableName . '.name'));
array_push($arrSearchColumn, array('Telepon', $obj->tableName . '.phone'));
array_push($arrSearchColumn, array('Handphone', $obj->tableName . '.mobile'));
array_push($arrSearchColumn, array('Email', $obj->tableName . '.email'));
array_push($arrSearchColumn, array('Pesan', $obj->tableName . '.message'));
array_push($arrSearchColumn, array('Subjek', $obj->tableName . '.subject'));  
 
$arrColumn = array ();
array_push($arrColumn, array('Kode','code',120));
array_push($arrColumn, array('Category','categoryname',120));
array_push($arrColumn, array('Tgl. Kirim','createdon',130,'center','date'));
array_push($arrColumn, array('Nama','name'));
array_push($arrColumn, array('Telepon','phone',120));
array_push($arrColumn, array('Email','email',200));
array_push($arrColumn, array('Status','statusname',70));
            
function generateQuickView($obj,$id){ 
 
	$rs = $obj->getDataRowById($id);   
	
	$description  = '<div class="data-card no-border">
					<h1>Isi Pesan</h1>
					<div style="width:100%">'.str_replace(chr(13),'<br>',$rs[0]['message']).'</div> 
				</div>';
				
	$detail = $description; 
  
 	$detail .= '<div style="clear:both;"></div>';	 
  
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
