<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass(array('Banner.class.php'));
$banner = createObjAndAddToCol(new Banner());


$obj = $banner;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'bannerForm';
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Nama', $obj->tableName . '.name'));
array_push($arrSearchColumn, array('URL', $obj->tableName . '.url') );
array_push($arrSearchColumn, array('Posisi', $obj->tableNamePosition . '.name'));
 
$arrColumn = array ();
array_push($arrColumn, array(ucwords($obj->lang['code']),'code',120));
array_push($arrColumn, array(ucwords($obj->lang['name']),'name'));
array_push($arrColumn, array(strtoupper($obj->lang['url']),'url',250));
array_push($arrColumn, array(ucwords($obj->lang['position']),'positionname',200));
array_push($arrColumn, array(ucwords($obj->lang['status']),'statusname',70));
 
function generateQuickView($obj,$id){ 
 
	$rs = $obj->getDataRowById($id);   
    $detail = '<div class="data-card" style="border:0; margin:auto; padding-top:1em"><div class="image-panel" style="width:100%;"><div class="image" style="background-image:url(\'../phpthumb/phpThumb.php?src='.$obj->phpThumbURLSrc .$obj->uploadFolder.$id.'/'.$rs[0]['file'].'&w=600&h=160&far=C&hash='.getPHPThumbHash($rs[0]['file']).'\'); "></div></div></div>';
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>