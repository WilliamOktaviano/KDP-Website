<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('Gallery.class.php')); 
$gallery = createObjAndAddToCol( new Gallery()); 

$obj = $gallery;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'galleryForm'; 

$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Nama', $obj->tableName . '.name'));
array_push($arrSearchColumn, array('Kategori', $obj->tableCategory . '.name'));
array_push($arrSearchColumn, array('Diposting oleh', $obj->tableCustomer . '.name'));
array_push($arrSearchColumn, array('Deskripsi', $obj->tableName . '.trdesc'));


function generateQuickView($obj,$id){  
	$rsItemImage = $obj->getGalleryImage($id);
	
	$image = '<div class="data-card no-border" style="margin:auto;">';
	$image .= '<div class="content"  style="height:25em !important">'; 
  	$image .= '<ul style="list-style:none; height:100px; margin-top:1em">';
	for($i=0;$i<count($rsItemImage);$i++){
		$image .= '<li style="margin:0.2em; border:1px solid #dedede; padding:0.2em; background-image:url(\'../phpthumb/phpThumb.php?src='.$obj->phpThumbURLSrc.$obj->uploadFolder.$id.'/'.$rsItemImage[$i]['file'].'&w=200&h=200&hash='.getPHPThumbHash($rsItemImage[$i]['file']).'\'); background-repeat:no-repeat; background-position:center; background-size:contain; height:10em; width:18%; float:left;">'; 
		$image .= '</li>';
	}
	$image .= '</ul>';
	$image .= '</div>';
	$image .= '</div>';
	
	$detail = '<div class="div-table" style="width:100%; ">
							<div class="div-table-row">
								<div class="div-table-col"  style="width:96%; text-align:center;"> 
									 '.$image.' 
								</div>  
							</div>
					</div>';
					
	$detail .= '<div style="clear:both;"></div>';	
		 
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>