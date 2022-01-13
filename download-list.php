<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  
  
includeClass(array('DownloadCategory.class.php', 'Download.class.php'));  
$download = new Download();
$downloadCategory = new DownloadCategory();

$pageUrlParam = array();
$arrParentPath = array();

$cat = 0; 
if ( isset($_GET) && !empty($_GET['cat']) ){
	$cat = $_GET['cat'];
        
	$rsCat = $downloadCategory->getDataRowById($cat);
	
	$arrParentPath[0]['pkey'] = $rsCat[0]['pkey'];
	$arrParentPath[0]['name'] = $rsCat[0]['name']; 
	$parentkey = $rsCat[0]['parentkey'];
	 
	while($parentkey <> 0){ 
		$rsParent = $downloadCategory->getDataRowById($parentkey); 
		$parentkey = $rsParent[0]['parentkey'];
		
		$ctr = count($arrParentPath);
		$arrParentPath[$ctr]['pkey'] =  $rsParent[0]['pkey'];
		$arrParentPath[$ctr]['name'] = $rsParent[0]['name']; 
	} 
}
	
$arrTwigVar['categoryPath'] = $arrParentPath;
    
$criteria = '';
$catCriteria = '';

$arrChild  = $downloadCategory->getChildren($cat);
if (!empty($arrChild)){ 
	$catCriteria = ' and categorykey in ('.implode(",",$arrChild).')';
}else{
    if($cat <> 0)
	   $catCriteria =  ' and categorykey = ' . $download->oDbCon->paramString($cat);
}
  
$criteria .= $catCriteria; 
$criteria .= ' and ' .$download->tableName.'.statuskey = 1';
      
$rsDownload = $download->searchData('','',true,$criteria,'order by '.$download->tableName.'.name asc');

/* ======================================================== PAGING ======================================================== */
 
$totalrowsperpage =  $class->loadSetting('productTotalItemPerPage'); 
$arrTwigVar ['totalItemPerPage'] = $totalrowsperpage;

$totalRows = count($rsDownload);
$totalPages = ceil( $totalRows / $totalrowsperpage); 

$pageIndex = ( isset($_GET) && !empty($_GET['page']) ) ? $_GET['page'] : 0; 
 
$now = $pageIndex * $totalrowsperpage; 
	
if ($now > $totalRows){
	$now = 0; 
	$pageIndex = 0; 
}
 
$arrTwigVar ['pageIndex'] =  $pageIndex;  

/* ======================================================== PAGING ======================================================== */

 
/* ======================================================== PREPARE DATA ======================================================== */

$limit =  ' limit ' . $now . ', ' . $totalrowsperpage ;  
$rsDownload = $download->searchData('','',true,$criteria,'order by '.$download->tableName.'.name asc',$limit);
   
/* ======================================================== PREPARE DATA ======================================================== */

for($i=0;$i<count($rsDownload);$i++) { 
    $rsDownload[$i]['phpThumbHash'] = getPHPThumbHash($rsDownload[$i]['file']); 
    
    $rsFile = array();
    
    if($rsDownload[$i]['useexternallink']){
        $rsFile[0]['filelink'] = $rsDownload[$i]['externallink'];
    }else{
        $rsFile = $download->getItemFile($rsDownload[$i]['pkey']);
        foreach($rsFile as $key=>$row){ 
            $rsFile[$key]['filelink'] =  HTTP_HOST.'download/download/'.$rsDownload[$i]['pkey'].'/'. $row['file'];
        }
    }
   
    
    $rsDownload[$i]['filelist'] = $rsFile;
        
}
   
$arrTwigVar ['rsDownload'] =  $rsDownload;
$arrTwigVar ['totalPages'] =  $totalPages;     
$arrTwigVar ['pageUrlParam'] = (!empty($pageUrlParam)) ? '&'. implode('&',$pageUrlParam) : ''; // you can change it later in html / js, this is just a default variable
 
$arrTwigVar ['CANONICAL'] = rtrim($class->loadSetting('sitesName'),'/') . '/download-list' ;
$arrTwigVar ['STRUCTURE_DATA'] = '';   

echo $twig->render('download-list.html', $arrTwigVar);
?>