<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  

includeClass(array('Item.class.php', 'DiscountScheme.class.php')); 
$item = new Item();
$brand = new Brand();
$discountScheme = new DiscountScheme();

// kalo perlu login

if($class->loadSetting('FEProductsPageNeedLogin') == 1){ 
if(!$security->isMemberLogin(false)) 
	header('location:/logout'); 
}
  
if(!IGNORE_QOH)  $warehouseCriteria = ' and iswebqoh = 1';

$pageUrlParam = array();

// ================== Kategori dr menu
$arrParentPath = array();
$catFromMenu = 0; 
if ( isset($_GET) && !empty($_GET['cat']) ){
	$catFromMenu = $_GET['cat'];
        
	$rsCat = $itemCategory->getDataRowById($catFromMenu);
	
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
}

$arrTwigVar['categoryPath'] = $arrParentPath;  
// ================== Kategori dr menu

    
$criteria = '';
 
// kategori sementara tampilin semua dulu, gk usah di intersect
$arrSelectedCategory = explode(',',$_GET['categorykey']);
if(!empty($catFromMenu)) array_push($arrSelectedCategory,$catFromMenu);
$arrSelectedCategory = array_unique($arrSelectedCategory);
$rsItemCategory = $itemCategory->searchData($itemCategory->tableName.'.statuskey',1);
foreach($rsItemCategory as $key=>$row) {  
    $_POST['chkCategory[]'] = (in_array($row['pkey'],$arrSelectedCategory)) ? 1 : '';
    $rsItemCategory[$key]['input'] = $class->inputCheckBox('chkCategory[]',array("etc" => 'attr-rel = '.$row['pkey']));
}

$arrTwigVar ['rsItemCategory'] = $rsItemCategory;

if(!empty($_GET['categorykey'])) { 
    // klao ada dari filter paramter, utamain dr filter
    $criteria .= ' and '.$item->tableName.'.categorykey in ('.$class->oDbCon->paramString($arrSelectedCategory,',').')';
}else{
    $arrChild  = $itemCategory->getChildren($catFromMenu);
    $catCriteria = (!empty($arrChild)) ? ' and categorykey in ('.implode(",",$arrChild).')' :  ' and categorykey = ' . $item->oDbCon->paramString($catFromMenu) ; 
    $criteria .= $catCriteria; 
}

if(!empty($arrSelectedCategory)) array_push($pageUrlParam,'categorykey=' . implode(',',$arrSelectedCategory) );

$criteria .= ' and ' .$item->tableName.'.statuskey = 1 and isvariant = 0';
     
/* ======================================================== SEARCH KEY ======================================================== */

$searchKey = ( isset($_GET) && !empty($_GET['key']) ) ? $_GET['key'] : '';

if($searchKey != ''){  
    // kalo diaktifin hasil search yg ad spasinya jd error
    //$searchKey = urlencode(urlencode($searchKey));  
    
    $arrTwigVar ['searchKey'] = $searchKey;
    array_push($pageUrlParam,"key=" . $searchKey);
    
    // harus dibawah agar gk terbawa parameter
    $searchKey = $class->oDbCon->paramString('%'.$searchKey .'%');   
    $criteria .= ' and ('.$item->tableName.'.name like '.$searchKey.' or '.$item->tableName.'.code like '.$searchKey.' or '.$item->tableName.'.tag like '.$searchKey.')';
}

/* ======================================================== ORDER CRITERIA ======================================================== */

// pastikan terdaftar

$arrOrderBy = array('name' => $class->lang['name'], 'price' => $class->lang['price']);
$arrOrderType = array('1' =>  $class->lang['asc'], '2' =>  $class->lang['desc']);

switch($_GET['orderby']){
    case 'name' : $orderField = $item->tableName.'.name';
                  break;
        
    case 'price' : $orderField = $item->tableName.'.sellingprice';
                    break;
        
    default : $orderField = $item->tableName.'.name';
}

$orderAsc = ($_GET['ordertype'] == 2) ? 'desc' : 'asc'; 

$orderBy = ' order by '.$orderField.' '.$orderAsc; 


// assign ulang 
if(isset($_GET['orderby'])) {
    $_POST['selOrderBy'] = $_GET['orderby'];
    array_push($pageUrlParam,"orderby=". $_GET['orderby']);  
}

if(isset($_GET['ordertype'])){
    $_POST['selOrderType'] = $_GET['ordertype'];
    array_push($pageUrlParam,"ordertype=". $_GET['ordertype']);  
} 

/* ======================================================== FILTER CRITERIA ======================================================== */

$groupCriteria = '';
$chkHideNoteAvailable = '';

if (isset($_GET) && !empty($_GET['hidHideNotAvailable']) && $_GET['hidHideNotAvailable'] == 1){  
    if(!IGNORE_QOH)
        $groupCriteria = ' having qtyonhand > 0 ';   
    $arrTwigVar ['hidHideNotAvailable'] =  1;
    
    array_push($pageUrlParam,"hidHideNotAvailable=1");
}   

 
if (isset($_GET) && !empty($_GET['pricerange'])){ 
	$arrPriceRange = explode(',',$_GET['pricerange']);
	 
	if (empty($arrPriceRange[0]) || !is_numeric($arrPriceRange[0]) || $arrPriceRange[0] < 0 )
		$arrPriceRange[0] = 0;
	
	if (empty($arrPriceRange[1]) || !is_numeric($arrPriceRange[1]) || $arrPriceRange[1] < 0 )
		$arrPriceRange[1] = 0;
		
	if ($arrPriceRange[1] < $arrPriceRange[0])
		$arrPriceRange[0] = 0;		 
	
    $_POST['priceFrom'] =  $class->formatNUmber($arrPriceRange[0]); 
    $_POST['priceTo'] =  $class->formatNUmber($arrPriceRange[1]); 
        
	if ($arrPriceRange[1] > 0){
		$criteria .= ' and sellingprice >= '.$class->oDbCon->paramString($arrPriceRange[0]).' and sellingprice <=  '.$class->oDbCon->paramString($arrPriceRange[1]);  
	    $arrTwigVar ['priceFrom'] = $arrPriceRange[0]; 
		$arrTwigVar ['priceTo'] =  $arrPriceRange[1]; 
        array_push($pageUrlParam,"pricerange=".$arrTwigVar ['priceFrom'].",".$arrTwigVar ['priceTo']); 
	}
	
}   

// kategori sementara tampilin semua dulu, gk usah di intersect
$arrSelectedBrand = explode(',',$_GET['brandkey']);
$arrSelectedBrand = array_unique($arrSelectedBrand);
$rsBrand = $brand->searchData($brand->tableName.'.statuskey',1);
foreach($rsBrand as $key=>$row)  { 
    $_POST['chkBrand[]'] = (in_array($row['pkey'],$arrSelectedBrand)) ? 1 : '';
    $rsBrand[$key]['input'] = $class->inputCheckBox('chkBrand[]',array("etc" => 'attr-rel = '.$row['pkey']));
}

if(!empty($_GET['brandkey'])){
    $criteria .= ' and '.$item->tableName.'.brandkey in ('.$class->oDbCon->paramString($arrSelectedBrand,',').')';   
    array_push($pageUrlParam,'brandkey=' . implode(',',$arrSelectedBrand) );
} 

$arrTwigVar ['rsBrand'] = $rsBrand;


/*$arrFilter = array();
if (isset($_GET) && !empty($_GET['filterkey'])){   
	 
	$arrFilter = explode(',',$_GET['filterkey']);
	for ($i=0;$i<count($arrFilter);$i++){
		if (!is_numeric($arrFilter[$i])) 
				unset($arrFilter[$i]);  
	}
	
	$arrTwigVar ['filterkey'] =  $arrFilter;  
    array_push($pageUrlParam,'filterkey=' . implode(',',$arrTwigVar['filterkey']) );
}*/

//available category

/*
// nanti dulu
if ( !empty($arrFilter)){
	
	//pisahkan per kategori
	$rsFilterCategory = $filterCategory->searchData($filterCategory->tableName.'.statuskey',1);
	 
	//$arrFilter = array(); 
	$arrIntersect = array();
	for ($i=0;$i<count($rsFilterCategory);$i++){
		$rsItemFilter = $itemFilter->searchData($itemFilter->tableName.'.statuskey',1,true, ' and categorykey = ' . $item->oDbCon->paramString($rsFilterCategory[$i]['pkey']));
	  	$rsItemFilter = array_column($rsItemFilter, 'pkey');
		$rsItemFilter = array_intersect($rsItemFilter,$arrFilter);
		
		if(empty($rsItemFilter))
			continue;
			
		$rsItem = $itemFilter->getItemInFilter($rsItemFilter); 
		$rsItem = array_column($rsItem, 'itemkey'); 
		 
		
		if (empty($arrIntersect)) 
			$arrIntersect = $rsItem;
		 else 
			$arrIntersect = array_intersect($arrIntersect,$rsItem);
		  
	}
	 
	 
	if (!empty($arrIntersect))
		$criteria .= ' and '.$item->tableName.'.pkey in ('.implode(',',$arrIntersect).')';
	else
		$criteria .= ' and false';
}

 
$rsFilterCategory = $filterCategory->searchData($filterCategory->tableName.'.statuskey',1,' order by name asc');
for($i=0;$i<count($rsFilterCategory);$i++){ 
	$rsFilter = $itemFilter->getRelatedFilterByItemCategory($rsFilterCategory[$i]['pkey'],$catFromMenu);
 	$rsFilterCategory[$i]['filter']	 = $rsFilter;
}
$arrTwigVar ['rsFilterCategory'] =  $rsFilterCategory;
 */

/* ======================================================== FILTER CRITERIA ======================================================== */


//$rsItem = $item->searchDataVariantGroup('','',true,$criteria,'order by '.$item->tableName.'.name asc','', $groupCriteria); 
$rsItem = $item->searchData('','',true,$criteria,$orderBy,'',$groupCriteria,$warehouseCriteria);
 
 /* BRAND FILTER */
//gk bisa search brand by category, karena akan bentrok ketika di page product search
/*$rsAvailableBrand = $item->getBrandFilter($rsItem);
$arrTwigVar['brandFilter'] = $rsAvailableBrand;

$arrBrandFilter = array();
$brandParam = '';
if (isset($_GET) && !empty($_GET['brandkey'])){   
	$arrBrandFilter = explode(',',$_GET['brandkey']);
	for ($i=0;$i<count($arrBrandFilter);$i++){
		if (!is_numeric($arrBrandFilter[$i])){
				unset($arrBrandFilter[$i]); 
		} 	
	}
	
	$arrTwigVar ['brandkey'] =  $arrBrandFilter; 
    $brandParam = implode(',',$arrBrandFilter);
}

if (!empty($brandParam))
$criteria .= ' and brandkey in ('.  $brandParam  .')'; */


/* ======================================================== PAGING ======================================================== */

 
if (isset($_POST) && !empty($_POST['hidTotalItemPerPage']))  
    $_SESSION['totalItemPerPage'] = $_POST['hidTotalItemPerPage']; 

if (empty($_SESSION['totalItemPerPage']))
	$_SESSION['totalItemPerPage'] = $class->loadSetting('productTotalItemPerPage'); 

$totalrowsperpage = $_SESSION['totalItemPerPage'];
$arrTwigVar ['totalItemPerPage'] = $totalrowsperpage;
  

$totalRows = count($rsItem);
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

$limit = (is_numeric($_SESSION['totalItemPerPage'])) ? ' limit ' . $now . ', ' . $totalrowsperpage : '';
  
//$rsItem = $item->searchDataVariantGroup('','',true,$criteria,'order by '.$item->tableName.'.name asc',$limit, $groupCriteria);
$rsItem = $item->searchData('','',true,$criteria,$orderBy,$limit,$groupCriteria,$warehouseCriteria);

$itemVariant = array();

for($i=0;$i<count($rsItem);$i++){
    $rsItemImage = $item->getItemImage($rsItem[$i]['pkey']); 

    if(!empty($rsItemImage)){ 
        $rsItem[$i]['mainimage'] = $rsItemImage[0]['file'];
        $rsItem[$i]['phpThumbHash'] = getPHPThumbHash($rsItemImage[0]['file']);	
        //$rsItem[$i]['linkname'] =  str_replace($class->arrSearch,$class->arrReplace,$rsItem[$i]['name']); 
    }

    $rsVariant = $item->getVariant($rsItem[$i]['pkey'],$warehouseCriteria);
    if (!empty($rsVariant)){ 
        $rsItem[$i]['variant'] = $rsVariant; 
        $rsItem[$i]['sellingprice'] = $rsVariant[0]['sellingprice']; 
    }
 
    if (IGNORE_QOH)
        $rsItem[$i]['qtyonhand'] = 99999;
}
  

/* ======================================================== PREPARE DATA ======================================================== */

/* ======================================================== APPLIED DISCOUNT ======================================================== */
 
$discountScheme->applyDiscountScheme($rsItem);

/*
foreach($rsItem as $key=>$itemRow) 
    $rsItem[$key]['promo'] = $voucher->checkHasPromo($itemRow);  
*/


/* ======================================================== APPLIED DISCOUNT ======================================================== */
 
$layout = (isset($_SESSION) && !empty($_SESSION['layout'])) ? $_SESSION['layout'] : $class->loadSetting('defaultLayout');  
$layout = (isset($_POST) && !empty($_POST['hidLayout'])) ? $_POST['hidLayout'] : $layout; 

// maslaah utk BKY, layoutnya normal, tp dibuat listing
$defaultQty = 0; // ($layout == 2) ? 0 : 1; 
  
for($i=0;$i<count($arrTwigVar['categoryPath']);$i++)  
    array_push($arrActive,$arrTwigVar ['SELF_PAGE'].'?'.$arrTwigVar['categoryPath'][$i]['pkey']);  


$arrTwigVar ['rsItem'] =  $rsItem;
$arrTwigVar ['totalPages'] =  $totalPages;   
$arrTwigVar ['hidLayout'] =  $class->inputHidden('hidLayout');  
$arrTwigVar ['layout'] = $layout; 
$arrTwigVar ['inputPriceFrom'] = $class->inputNumber('priceFrom', array('etc' => 'style="text-align:right"')); 
$arrTwigVar ['inputPriceTo'] = $class->inputNumber('priceTo', array('etc' => 'style="text-align:right"')); 
$arrTwigVar ['hidTotalItemPerPage'] =  $class->inputHidden('hidTotalItemPerPage');  
$arrTwigVar ['btnUpdateFilter'] =   $class->inputSubmit('btnUpdateFilter',$class->lang['updateSearchFilter'],array('add-class' => 'btnUpdate'));   
$arrTwigVar ['inputHidItemkey'] =  $class->inputHidden('hiditemkey[]');
$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action',array( 'value' => 'addToCart'));
$arrTwigVar ['inputQty'] = $class->inputNumber('orderQty[]',array( 'value' => $defaultQty)); 
$arrTwigVar ['inputDisabledQty'] = $class->inputNumber('orderQty[]',array( 'value' => 0, 'disabled' => true ));   
$arrTwigVar ['btnAddToCart'] =   $class->inputSubmit('btnSave', $class->lang['addToCart'], array( 'etc' => ' style="width:100%;"')); 
$arrTwigVar ['btnListSubmit'] =  $class->inputSubmit('btnListSubmit',$class->lang['addToCart']); 
$arrTwigVar ['pageUrlParam'] = (!empty($pageUrlParam)) ? '&'. implode('&',$pageUrlParam) : ''; // you can change it later in html / js, this is just a default variable

$arrTwigVar ['selOrderBy']  = $class->inputSelect('selOrderBy',$arrOrderBy);
$arrTwigVar ['selOrderType']  = $class->inputSelect('selOrderType',$arrOrderType);

$arrTwigVar ['ACTIVE_MENU'] =  $arrActive;

$arrTwigVar ['CANONICAL'] = rtrim($class->loadSetting('sitesName'),'/') . '/products' ;
$arrTwigVar ['STRUCTURE_DATA'] = $item->generateStructurData($rsItem);  
$arrTwigVar ['IN_STORE'] =  true; 
     
$_SESSION['layout'] = $layout;

echo $twig->render('products.html', $arrTwigVar);
?>