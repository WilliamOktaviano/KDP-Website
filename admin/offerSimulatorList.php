<?php   
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('OfferSimulator.class.php');
$offerSimulator = createObjAndAddToCol( new OfferSimulator()); 

$obj = $offerSimulator;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'offerSimulatorForm';
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Tanggal', $obj->tableName . '.trdate'));
array_push($arrSearchColumn, array('Gudang', $obj->tableWarehouse. '.name'));
array_push($arrSearchColumn, array('Pelanggan', $obj->tableCustomer. '.name'));

array_push($arrSearchColumn, array('Total', $obj->tableName. '.grandtotal'));
array_push($arrSearchColumn, array('Catatan', $obj->tableName. '.description'));

 
function generateQuickView($obj,$id){ 
	$item = new Item();
	    
	$detail = '';
	$rs = $obj->searchData($obj->tableName .'.pkey',$id,true);   
 	$rsDetail = $obj->getDetailWithRelatedInformation($id);

	  
	$basicInformation  = ' <div class="data-card border-orange">
						<h1>'.ucwords($obj->lang['generalInformation']).'</h1> 
						<div class="content">
						<div class="div-table general-information-table">
							<div class="div-table-row">
								<div class="div-table-col" style="width:40%">'.ucwords($obj->lang['status']).'</div> 
								<div class="div-table-col">'.$rs[0]['statusname'].'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['code']).'</div> 
								<div class="div-table-col">'.$rs[0]['code'].'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['date']).'</div> 
								<div class="div-table-col">'.$obj->formatDBDate($rs[0]['trdate']).'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['warehouse']).'</div> 
								<div class="div-table-col">'. $rs[0]['warehousename'].'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['customer']).'</div> 
								<div class="div-table-col">'.$rs[0]['customername'].'</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col" style="height:1em"></div> 
								<div class="div-table-col"></div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['total']).'</div> 
								<div class="div-table-col">'.$obj->formatNumber($rs[0]['grandtotal']).'</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col" style="height:1em"></div> 
								<div class="div-table-col"></div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['note']).'</div> 
								<div class="div-table-col">'.$rs[0]['trdesc'].'</div> 
							</div> 
						</div>
						</div>
					</div>  
		'; 	
		
		$detailInformation  = ' <div class="data-card border-green">
						<h1>'.ucwords($obj->lang['itemDetail']).'</h1> 
						<div class="content">
						<div class="div-table  quick-view-table">
							  <div class="div-table-row">  
                                <div class="div-table-col detail-col-header" style="width:80px;">'.ucwords($obj->lang['itemCode']).'</div>
                                <div class="div-table-col detail-col-header">'.ucwords($obj->lang['itemName']).'</div>
                                <div class="div-table-col detail-col-header" style="width:60px; text-align:right;">'.ucwords($obj->lang['qty']).'</div> 
                                <div class="div-table-col detail-col-header" style="width:50px;">'.ucwords($obj->lang['unit']).'</div> 
                                <div class="div-table-col detail-col-header" style="width:80px; text-align:right;">'.ucwords($obj->lang['price']).' @</div> 
                                <div class="div-table-col detail-col-header" style="width:80px; text-align:right;">'.ucwords($obj->lang['subtotal']).'</div> 
								</div>';
								
		for ($i=0;$i<count($rsDetail);$i++){
			 

			$detailInformation  .= '
				<div class="div-table-row">  
					<div class="div-table-col">'.$rsDetail[$i]['itemcode'].'</div>
					<div class="div-table-col">'.$rsDetail[$i]['itemname'].'</div>
					<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['qty']).'</div>
                    <div class="div-table-col">'. $rsDetail[$i]['unitname'] .'</div> 
                    <div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['priceinunit']).'</div> 
					<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['total']).'</div> 
				</div>
			';
		}
								
		$detailInformation  .= ' </div>
						</div>
					</div>  
		'; 	
		
		$detail .= '<div class="div-table" style="width:100%; ">
							<div class="div-table-row">
								<div class="div-table-col-5"  style="width:25%;">
								'.$basicInformation.'
								</div> 
								<div class="div-table-col-5">
								 '.$detailInformation.'
								</div>  
							</div>
					</div>';
				  
		$detail .= '<div style="clear:both;"></div>';	
		 
	 
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
