<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  
  
includeClass(array("Item.class.php","Category.class.php","ItemCategory.class.php","DiscountScheme.class.php"));
$itemCategory = new ItemCategory();
$discountScheme = new DiscountScheme();
$item = new Item();
    
$warehouseCriteria = ' and iswebqoh = 1';

if(empty($_GET)){
	header("location: /");
	die;
} 

$id = $_GET['id']; 
$rsItem = $item->searchData($item->tableName.'.pkey',$id,true, ' and '.$item->tableName.'.statuskey = 1','','','',$warehouseCriteria); 
if(empty($rsItem)){
	header("location: /");
	die;
}
 
$discountScheme->applyDiscountScheme($rsItem);

$rsVariant = $item->getVariant($id,$warehouseCriteria);
if (!empty($rsVariant)){ 
	$rsItem[0]['sellingprice'] = $rsVariant[0]['sellingprice']; 
}

$rsImage = $item->getItemImage($id);
for ($i=0;$i<count($rsImage);$i++){  
    $rsImage[$i]['phpThumbHash'] = getPHPThumbHash($rsImage[$i]['file']);	
}

$rsVideo = $item->getDetailVideo($id);

$rsFiles = $item->getItemFile($id);
for ($i=0;$i<count($rsFiles);$i++){ 
    $ext = explode(".",$rsFiles[$i]['file']);
    $ext = strtolower($ext[count($ext) - 1]);
    
    $rsFiles[$i]['ext'] = $ext;
    
    if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'png')
        $rsFiles[$i]['phpThumbHash'] = getPHPThumbHash($rsFiles[$i]['file']);	
}

$rsDescription = $item->getItemDescription($id); 
$rsSpecification =  $item->getItemSpecification($id);

$_POST['hiditemkey[]'] = $rsItem[0]['pkey'];
$_POST['action'] ='addToCart';
$_POST['orderQty'] = '1';
$arrTwigVar ['inputHidItemkey'] =  $class->inputHidden('hiditemkey[]');
$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action');
$arrTwigVar ['inputQty'] = $class->inputNumber('orderQty[]', array('value' => 1,'etc' => 'style="text-align:center"')); 
$arrTwigVar ['btnSubmit'] =   $class->inputSubmit('btnSave', $class->lang['addToCart'], array('etc' => ' style="width:100%;"')); 
$arrTwigVar ['btnSubmitDisabled'] =   $class->inputButton('btnSaveDisabled', $class->lang['emptyStock'], array('disabled' => true,'etc' => ' style="width:100%;"')); 
  
$rsCat = $itemCategory->getDataRowById($rsItem[0]['categorykey']);

$arrParentPath[0]['pkey'] = $rsCat[0]['pkey'];
$arrParentPath[0]['name'] = $rsCat[0]['name']; 
$parentkey = $rsCat[0]['parentkey'];
 
while($parentkey <> 0){ 
	$rsParent = $itemCategory->getDataRowById($parentkey); 
	$parentkey = $rsParent[0]['parentkey'];
	
	$ctr = count($arrParentPath);
	$arrParentPath[$ctr]['pkey'] =  $rsParent[0]['pkey'];
	$arrParentPath[$ctr]['name'] = $rsParent[0]['name']; 
}   

if (IGNORE_QOH) $rsItem[0]['qtyonhand'] = 99999;
 
$rsItem[0]['shortdescription'] = str_replace(chr(13),'<br>',$rsItem[0]['shortdescription']);
$arrTwigVar['categoryPath'] = $arrParentPath;   
$arrTwigVar['rsVariant'] = $rsVariant;
$arrTwigVar['rsItem'] =  $rsItem;     
$arrTwigVar['rsItemImage'] =  $rsImage;  
$arrTwigVar['rsItemFiles'] =  $rsFiles;  
$arrTwigVar['rsItemVideo'] =  $rsVideo;  
$arrTwigVar['rsItemDescription'] =  $rsDescription;   
$arrTwigVar['rsSpecification'] =  $rsSpecification;   

$criteria = array(); 
$criteria['brandkey'] = $rsItem[0]['brandkey'];
$criteria['itemkey'] = $rsItem[0]['pkey'];
$criteria['itemcategorykey'] = $rsItem[0]['categorykey'];

$availableVoucher = array();
//$availableVoucher = $voucher->getAvailableVoucher(array(VOUCHER_CATEGORY['sales'],VOUCHER_CATEGORY['shipment']),VOUCHER_TYPE['regular'],CUSTOMER_TYPE['enduser'], $criteria );
$arrTwigVar['availableVoucher'] = $availableVoucher;

$title = (empty($rsItem[0]['name'])) ? $rsItem[0]['code'] : $rsItem[0]['name']; 
 
$descForMeta = $title;
if (!empty($rsItem[0]['shortdescription']))
    $descForMeta = $rsItem[0]['shortdescription'];
else if (!empty($rsDescription)) 
    $descForMeta = strip_tags($rsDescription[0]['value']); 

$arrTwigVar ['META_TITLE'] = $title;
$arrTwigVar ['META_DESCRIPTION'] = $descForMeta;

if (!empty($rsItem[0]['tag']))
    $rsItem[0]['tag'] .=  ', '. $rsItem[0]['tag'];

$arrTwigVar ['META_KEYWORDS'] = $title . ', '. $rsItem[0]['tag'];
$arrTwigVar ['META_IMAGE'] = $class->defaultURLUploadPath . 'item/'.$rsItem[0]['pkey'].'/'.$rsImage[0]['file']; 
 
$arrTwigVar ['CANONICAL'] = rtrim($class->loadSetting('sitesName'),'/') . '/products' ;
  
array_push($arrActive,'/products.php');
for($i=0;$i<count($arrTwigVar['categoryPath']);$i++)  
    array_push($arrActive,'/products.php?'.$arrTwigVar['categoryPath'][$i]['pkey']);  
  

$structureData =' 
<script type="application/ld+json">
{
    "@context": "http://schema.org/",
    "@type": "Product",
    "name": "'.$rsItem[0]['name'].'",
    "image" : ';

    //for ($i=0;$i<count($rsImage);$i++){  
       $structureData .='"'.HTTP_HOST.'phpthumb/phpThumb.php?src='. $class->phpThumbURLSrc.'item/'.$rsItem[0]['pkey'].'/'.$rsImage[0]['file'].'&hash='.$rsImage[0]['phpThumbHash'].'",'; 
    //}
 

$structureData .='"brand": {
        "@type": "Thing",
        "name":  "'.$rsItem[0]['brandname'].'"
    }, 
    "offers": {
        "@type": "Offer",
        "priceCurrency": "IDR",
        "price": "'.$rsItem[0]['sellingprice'].'",  
        "availability": "http://schema.org/InStock"
    }  
}
</script>
';
    
$arrTwigVar ['ACTIVE_MENU'] =  $arrActive; 
$arrTwigVar ['IN_STORE'] =  true; 
$arrTwigVar ['STRUCTURE_DATA'] = $structureData;   
 
echo $twig->render('products-detail.html', $arrTwigVar);
?>