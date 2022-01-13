<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('Article.class.php'));
$article = createObjAndAddToCol( new Article()); 

$obj = $article;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'articleForm';
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Judul', $obj->tableName . '.title'));  
array_push($arrSearchColumn, array('Deskripsi', $obj->tableName . '.shortdesc'));  
 
   
function generateQuickView($obj,$id){ 
 
	$rs = $obj->getDataRowById($id);   
	
    $detail = '';
    
	$description  = '<div class="data-card no-border">
					<h1>'.ucwords($obj->lang['shortDescription']).'</h1>
					<div class="content" style="overflow:hidden;">
						<div class="div-table" style="float:left; width:100%">
							<div class="div-table-row">
								<div class="div-table-col" style="padding:0.3em; ">'.$rs[0]['shortdesc'].'</div> 
								<div class="div-table-col" style="width:1%"></div> 
                                <div class="div-table-col image-panel"><div class="image" style="background-image:url(\'../phpthumb/phpThumb.php?src='.$obj->phpThumbURLSrc .$obj->uploadFolder.$id.'/'.$rs[0]['file'].'&w=200&h=160&far=C&hash='.getPHPThumbHash($rs[0]['file']).'\'); "></div></div>  
                            </div>
						</div>  
					</div>
				</div>';
				
	$detail .= $description;
  
  
 	$detail .= '<div style="clear:both;"></div>';	
	 
  
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
