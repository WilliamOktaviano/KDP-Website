<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('ItemIn.class.php');
$itemIn = createObjAndAddToCol(new ItemIn());
$item = createObjAndAddToCol(new Item());

$obj = $itemIn;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'itemInForm';
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Tanggal', $obj->tableName . '.trdate')); 
array_push($arrSearchColumn, array('Referensi', $obj->tableName . '.refcode'));
array_push($arrSearchColumn, array('Gudang', $obj->tableWarehouse . '.name') ); 
//array_push($arrSearchColumn, array('Pemasok', $obj->tableSupplier . '.name') ); 
array_push($arrSearchColumn, array('Kode Ref.', $obj->tableName. '.refcode') ); 

function generateQuickView($obj,$id){  
    $item = new Item();
    $showVendorPartNumber = $obj->loadSetting('showVendorPartNumber');
	
	$rs = $obj->searchData($obj->tableName .'.pkey',$id,true);   
    $rsDetail = $obj->getDetailWithRelatedInformation($id); 
    
	$detail = '';

     $vendorPartNumber = '';
     if ($showVendorPartNumber)  
         $vendorPartNumber = '<div class="div-table-col detail-col-header" style="width:150px;">Vendor Part Number</div>';
    
	$detailInformation  = ' <div class="data-card no-border">
					<h1>Detail Item</h1> 
					<div class="content">
					<div class="div-table quick-view-table" >
						  <div class="div-table-row"> 
                                '.$vendorPartNumber.' 
                                <div class="div-table-col detail-col-header" style="width:130px;">'.ucwords($obj->lang['itemCode']).'</div>
                                <div class="div-table-col detail-col-header" style="width:300px;">'.ucwords($obj->lang['itemName']).'</div>
								<div class="div-table-col detail-col-header" style="width:70px; text-align:right;">'.ucwords($obj->lang['qty']).'</div>
                                <div class="div-table-col detail-col-header" style="width:40px;">'.ucwords($obj->lang['unit']).'</div>
                                <div class="div-table-col detail-col-header" style="width:100px; text-align:right;">'.ucwords($obj->lang['receivedQty']).'</div>    
								<div class="div-table-col detail-col-header" style="width:70px; text-align:right;">'.ucwords($obj->lang['value']).'</div>  
								<div class="div-table-col detail-col-header"></div>  
							</div>';
							
	for ($i=0;$i<count($rsDetail);$i++){ 
        
        $vendorPartNumber = '';
        if ($showVendorPartNumber) 
             $vendorPartNumber = '<div class="div-table-col">'.$rsDetail[$i]['partnumber'].'</div>';
     
        $receivedQty = $item->splitQtyBaseOnUnit($rsDetail[$i]['itemkey'], $rsDetail[$i]['receivedqtyinbaseunit']);
        
		$detailInformation  .= '
			<div class="div-table-row"> 
                 '.$vendorPartNumber.' 
				<div class="div-table-col">'.$rsDetail[$i]['itemcode'].'</div>
				<div class="div-table-col">'.$rsDetail[$i]['itemname'].'</div>
				<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['qty']).'</div>
                <div class="div-table-col">'.$rsDetail[$i]['unitname'].'</div> 
                <div class="div-table-col" style="text-align:right;">'.$receivedQty.'</div>
				<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['costinbaseunit']).'</div>
				<div class="div-table-col"> <span class="text-muted"> / '.$rsDetail[$i]['baseunitname'].'</span></div>  
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
