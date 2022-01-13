<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('WarehouseTransfer.class.php'));
$warehouseTransfer = new WarehouseTransfer();

$obj = $warehouseTransfer;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'warehouseTransferForm';
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Tanggal', $obj->tableName . '.trdate'));
array_push($arrSearchColumn, array('Gudang Asal', 'warehousefrom.name'));
array_push($arrSearchColumn, array('Gudang Tujuan', 'warehouseto.name'));
 
function generateQuickView($obj,$id){ 

	$rsDetail = $obj->getDetailWithRelatedInformation($id); 
	 
	$detail = '';
	
	$detailInformation  = ' <div class="data-card no-border">
					<h1>Detail Transfer</h1> 
					<div class="content">
					<div class="div-table  quick-view-table">
						  <div class="div-table-row"> 
								<div class="div-table-col detail-col-header"  style="width:300px;">Item</div>
								<div class="div-table-col detail-col-header" style="width:70px; text-align:right;">Jumlah</div> 
								<div class="div-table-col detail-col-header" style="width:70px;">Unit</div>
								<div class="div-table-col detail-col-header"></div>  
							</div>';
	
	for ($i=0;$i<count($rsDetail);$i++){ 
	
		$detailInformation  .= '
			<div class="div-table-row"> 
				<div class="div-table-col">'.$rsDetail[$i]['itemname'].'</div>
				<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['qty']).'</div> 
				<div class="div-table-col">'.$rsDetail[$i]['unitname'].'</div> 
				<div class="div-table-col"></div>  
			</div>
		';
	}
							
	$detailInformation  .= ' </div>
					</div>
				</div>  
	'; 	
		

	$detail .= $detailInformation;
			  
	$detail .= '<div style="clear:both;"></div>';							
							
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
