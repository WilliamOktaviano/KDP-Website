<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('DiscountScheme.class.php');
$discountScheme = createObjAndAddToCol(new DiscountScheme()); 

$obj = $discountScheme;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'discountSchemeForm';
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Name', $obj->tableName . '.name'));
array_push($arrSearchColumn, array('Tgl Mulai', $obj->tableName . '.trdatestart')); 
array_push($arrSearchColumn, array('Tgl Berakhir', $obj->tableName . '.trdateend')); 
array_push($arrSearchColumn, array('Catatan', $obj->tableName. '.trdesc') );

 
function generateQuickView($obj,$id){ 
 $rsDetail = $obj->getDetailWithRelatedInformation($id);
	    
	$detail = '';
    
    

	$detailInformation  = ' <div class="data-card no-border">
					<h1>Detail Item</h1> 
					<div class="content">
					<div class="div-table quick-view-table" >
                      <div class="div-table-row"> 
                            <div class="div-table-col detail-col-header"  style="width:900px;">'.ucwords($obj->lang['itemName']).'</div>
                            <div class="div-table-col detail-col-header" style="width:70px; text-align:right;">'.ucwords($obj->lang['discount']).'</div> 
                            <div class="div-table-col detail-col-header"></div>
                        </div>';
							
	for ($i=0;$i<count($rsDetail);$i++){
		
        
                            $decimal = 0;
                            $typediscount = '';
                            
                            if ($rsDetail[$i]['discounttype']  == 2){ 
                                $decimal = 2;
                                $typediscount = '%';
                            } 
	
		$detailInformation  .= '
			<div class="div-table-row"> 
				<div class="div-table-col">'.$rsDetail[$i]['itemname'].'</div>
				<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['discount'],$decimal).' '.$typediscount.'</div> 
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
