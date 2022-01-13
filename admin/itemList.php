<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('Item.class.php');
$item = createObjAndAddToCol(new Item());

$warehouse = createObjAndAddToCol(new Warehouse());
$itemMovement = createObjAndAddToCol(new ItemMovement());

$obj = $item;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'itemForm';
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Nama', $obj->tableName . '.name'));
array_push($arrSearchColumn, array('Tag', $obj->tableName . '.tag'));
array_push($arrSearchColumn, array('Deskripsi Singkat', $obj->tableName . '.shortdescription'));
array_push($arrSearchColumn, array('Merk', $obj->tableBrand. '.name') );
array_push($arrSearchColumn, array('Kategori', $obj->tableCategory. '.name') );
array_push($arrSearchColumn, array('VendorPart Number', $obj->tableName. '.vendorpartnumbercache') );

function generateQuickView($obj,$id){ 
	global $warehouse;
	global $itemMovement;
	    
	$rs = $obj->getDataRowById($id);   
	
	$detail = '';
	 
 	$rsItemImage = $obj->getItemImage($id);
	$itemDescription =  str_replace(chr(13),'<br>',$rs[0]['shortdescription']);		 
 	 
    $img = (!empty($rsItemImage)) ? '<div class="image" style="background-image:url(\'../phpthumb/phpThumb.php?src='.$obj->phpThumbURLSrc .$obj->uploadFolder.$id.'/'.$rsItemImage[0]['file'].'&w=200&h=160&far=C&hash='.getPHPThumbHash($rsItemImage[0]['file']).'\'); "></div>' : '';
	$itemImage  = ' <div class="data-card border-blue">
								<h1>'.ucwords($obj->lang['description']).'</h1> 
								<div class="content">
									<div class="div-table" style="float:left; width:100%">
										<div class="div-table-row">
											<div class="div-table-col" style=" padding:0.3em; ">'.$itemDescription.'</div> 
											<div class="div-table-col image-panel">'.$img.'</div>  
										</div>
									</div>  
								</div>	
					</div>';			
	
	  $rsWarehouse = $warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'',' order by '.$warehouse->tableName.'.orderlist asc, name asc');
	  $stockRow = '';
	  for($i=0;$i<count($rsWarehouse);$i++){
			 
		 $qoh = 0;
		 $colorClass ="";
		 
		 $qoh = $obj->formatNumber($itemMovement->getItemQOH($id, $rsWarehouse[$i]['pkey']));
		 
		 if ($rsWarehouse[$i]['isqohcount']) 
			 $colorClass="text-green-avocado";
          
		 if ($qoh == 0)
			 $colorClass="text-red-cardinal";
			 
			 $stockRow .= '
			 <div class="div-table-row"> 
				 <div class="div-table-col-5 '.$colorClass.'" style="border-bottom:1px solid #dedede;" > 
					'.$rsWarehouse[$i]['name'].'
				 </div> 
				 <div class="div-table-col-5 '.$colorClass.'" style="border-bottom:1px solid #dedede; text-align:right;" > 
					'.$qoh.'
				 </div> 
			 </div> 
			 ';
	}
		 
	$stockInformation = '<div  class="data-card border-green"><h1>'.ucwords($obj->lang['stockInformation']).'</h1>
								<div class="content">
										<div class="div-table" style="width:100%">
											'.$stockRow.'
										</div>
								</div>
						</div>';	
	
	$detail .= '<div class="div-table" style="width:100%; ">
						<div class="div-table-row">
							<div class="div-table-col-5"  style="width:67%;">
							'.$itemImage.' 
							</div>  
							<div class="div-table-col-5">
							 '.$stockInformation.'
							</div> 
						</div>
				</div>';
			  
	$detail .= '<div style="clear:both;"></div>';	
	 
  
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
