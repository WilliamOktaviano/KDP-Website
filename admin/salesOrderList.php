<?php   
require_once '../_config.php'; 
require_once '../_include-v2.php'; 
includeClass('SalesOrder.class.php');   
$salesOrder = createObjAndAddToCol( new SalesOrder()); 

$obj = $salesOrder;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'salesOrderForm';
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Kode Ref', $obj->tableName . '.refcode'));
array_push($arrSearchColumn, array('Tanggal', $obj->tableName . '.trdate'));
array_push($arrSearchColumn, array('Gudang', $obj->tableWarehouse. '.name'));
array_push($arrSearchColumn, array('Pelanggan', $obj->tableCustomer. '.name'));
array_push($arrSearchColumn, array('Sales', $obj->tableEmployee. '.name'));
array_push($arrSearchColumn, array('Nama Penerima','recipientname'));
array_push($arrSearchColumn, array('Nama Penerima','recipientphone'));
array_push($arrSearchColumn, array('Nama Penerima','recipientemail'));
array_push($arrSearchColumn, array('Nama Penerima','recipientaddress'));
array_push($arrSearchColumn, array('Total', $obj->tableName. '.grandtotal'));
array_push($arrSearchColumn, array('Catatan', $obj->tableName. '.trdesc'));

function generateQuickView($obj,$id){ 
	$item = new Item();
	    
	$detail = '';
	$rs = $obj->searchData($obj->tableName .'.pkey',$id,true);   
 	$rsDetail = $obj->getDetailWithRelatedInformation($id);
	
	if ($rs[0]['finaldiscounttype'] == 2)
		$rs[0]['finaldiscount'] = $rs[0]['finaldiscount']/100 * $rs[0]['subtotal'];
	  
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
								<div class="div-table-col">'.ucwords($obj->lang['subtotal']).'</div> 
								<div class="div-table-col">'.$obj->formatNumber($rs[0]['subtotal']).'</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['finalDiscount']).'</div> 
								<div class="div-table-col">('.$obj->formatNumber($rs[0]['finaldiscount']). ')</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['point']).'</div> 
								<div class="div-table-col">('.$obj->formatNumber($rs[0]['pointvalue']). ')</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['beforeTax']).'</div> 
								<div class="div-table-col">'.$obj->formatNumber($rs[0]['beforetaxtotal']).'</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['tax']).'</div> 
								<div class="div-table-col">'.$obj->formatNumber($rs[0]['taxvalue']).'</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['shippingFee']).'</div> 
								<div class="div-table-col">'.$obj->formatNumber($rs[0]['shipmentfee']).'</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['others']).'</div> 
								<div class="div-table-col">'.$obj->formatNumber($rs[0]['etccost']).'</div> 
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
                                <div class="div-table-col detail-col-header" style="width:100px; text-align:right;">'.ucwords($obj->lang['deliveredQty']).'</div> 
                                <div class="div-table-col detail-col-header" style="width:80px; text-align:right;">'.ucwords($obj->lang['price']).' @</div> 
                                <div class="div-table-col detail-col-header" style="width:70px; text-align:right;">'.ucwords($obj->lang['discount']).' @</div>
                                <div class="div-table-col detail-col-header" style="width:80px; text-align:right;">'.ucwords($obj->lang['subtotal']).'</div> 
								</div>';
								
		for ($i=0;$i<count($rsDetail);$i++){
			 
			if ($rsDetail[$i]['discounttype'] == 2)
				$rsDetail[$i]['discount'] = $rsDetail[$i]['discount']/100 * $rsDetail[$i]['priceinunit'];
		
            $deliveredQty = $item->splitQtyBaseOnUnit($rsDetail[$i]['itemkey'], $rsDetail[$i]['deliveredqtyinbaseunit']);
			$detailInformation  .= '
				<div class="div-table-row">  
					<div class="div-table-col">'.$rsDetail[$i]['itemcode'].'</div>
					<div class="div-table-col">'.$rsDetail[$i]['itemname'].'</div>
					<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['qty']).'</div>
                    <div class="div-table-col">'. $rsDetail[$i]['unitname'] .'</div> 
                    <div class="div-table-col" style="text-align:right;">'.$deliveredQty.'</div>
                    <div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['priceinunit']).'</div> 
					<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['discount']).'</div>
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
