<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  

includeClass(array('Gallery.class.php'));  
$gallery = new Gallery();

$pageIndex = 0;
if ( isset($_GET) && !empty($_GET['page']) ){
	$pageIndex = $_GET['page'];
}  

$criteriaPublisher = '';
$publisherkey = 0;
$publisherName = '';
if ( isset($_GET) && !empty($_GET['publisherkey']) ){
	$publisherkey = $_GET['publisherkey'];
	$rsCustomer = $customer->getDataRowById($publisherkey);
	if (!empty($rsCustomer)){
		$criteriaPublisher = ' and customerkey = ' . $publisherkey;
		$publisherName = $rsCustomer[0]['name'];
	}
}  
	   
$arrTwigVar ['pageIndex'] =  $pageIndex;
 
$totalrowsperpage = $class->loadSetting('gallerytotalitemperpage');
$now = $pageIndex * $totalrowsperpage;
$limit = ' limit ' . $now . ', ' . $totalrowsperpage;
     
$criteria = ' and ' .$gallery->tableName.'.statuskey = 1 ' . $criteriaPublisher;
  
$rsGallery = $gallery->searchData('','',true,$criteria,'order by '.$gallery->tableName.'.pkey desc',$limit); 
$totalPages = ceil( $gallery->getTotalRows($criteria) / $totalrowsperpage); 
  
for($i=0;$i<count($rsGallery);$i++){
		$rsImage = $gallery->getGalleryImage($rsGallery[$i]['pkey']); 
        for($j=0;$j<count($rsImage);$j++){
                $rsImage[$j]['phpThumbHash'] = getPHPThumbHash($rsImage[$j]['file']);	
        }
    
        $rsGallery[$i]['file'] = $rsImage[0]['file'];
        $rsGallery[$i]['phpThumbHash'] = getPHPThumbHash($rsImage[0]['file']);
        $rsGallery[$i]['linktitle'] =  str_replace($class->arrSearch,$class->arrReplace,$rsGallery[$i]['name']); 
        $rsGallery[$i]['rsGalleryImage'] = $rsImage;
    
        $arrTwigVar ['rsGalleryImage'][$rsGallery[$i]['pkey']] =  $rsImage; 
}


$arrTwigVar ['rsGallery'] =  $rsGallery;
$arrTwigVar ['totalPages'] =  $totalPages;  
$arrTwigVar ['activePubisherKey'] = $publisherkey; 
$arrTwigVar ['activePubisherName'] = $publisherName; 


/* ===================== All Galleries / Portfolio ========================================== */  
$rsGallery = $gallery->searchData($gallery->tableName.'.statuskey',1);
foreach($rsGallery as $key=>$galleryRow){ 
	$rsImage = $gallery->getGalleryImage($galleryRow['pkey']); 
    $rsGallery[$key]['mainimage'] = $rsImage[0]['file'];	
    $rsGallery[$key]['phpThumbHash'] = getPHPThumbHash($rsImage[0]['file']);
} 
$arrTwigVar['rsAllGallery'] = $rsGallery;  

echo $twig->render('gallery.html', $arrTwigVar);
?>