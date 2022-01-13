<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  

includeClass(array('JobOpportunities.class.php'));  
$jobOpportunities = new JobOpportunities();
$careerCategory = new CareerCategory();
$careerField = new CareerField();
$city = new City();

$pageUrlParam = array();

// ================== Kategori dr menu
$arrParentPath = array();
$catFromMenu = 0; 
if ( isset($_GET) && !empty($_GET['cat']) ){
	$catFromMenu = $_GET['cat'];
        
	$rsCat = $careerCategory->getDataRowById($catFromMenu);
	
	$arrParentPath[0]['pkey'] = $rsCat[0]['pkey'];
	$arrParentPath[0]['name'] = $rsCat[0]['name']; 
	$parentkey = $rsCat[0]['parentkey'];
	 
	while($parentkey <> 0){ 
		$rsParent = $careerCategory->getDataRowById($parentkey); 
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
$rsCareerCategory = $careerCategory->searchData($careerCategory->tableName.'.statuskey',1);
foreach($rsCareerCategory as $key=>$row) {  
    $_POST['chkCategory[]'] = (in_array($row['pkey'],$arrSelectedCategory)) ? 1 : '';
    $rsCareerCategory[$key]['input'] = $class->inputCheckBox('chkCategory[]',array("etc" => 'attr-rel = '.$row['pkey']));
}

$arrTwigVar ['rsCareerCategory'] = $rsCareerCategory;

if(!empty($_GET['categorykey'])) { 
    // klao ada dari filter paramter, utamain dr filter
    $criteria .= ' and '.$jobOpportunities->tableName.'.categorykey in ('.$class->oDbCon->paramString($arrSelectedCategory,',').')';
}else{
    $arrChild  = $careerCategory->getChildren($catFromMenu);
    $catCriteria = (!empty($arrChild)) ? ' and '.$jobOpportunities->tableName.'.categorykey in ('.implode(",",$arrChild).')' :  ' and '.$jobOpportunities->tableName.'.categorykey = ' . $jobOpportunities->oDbCon->paramString($catFromMenu) ; 
    $criteria .= $catCriteria; 

}

if(!empty($arrSelectedCategory)) array_push($pageUrlParam,'categorykey=' . implode(',',$arrSelectedCategory) );


$orderby = 'order by title asc';
$criteria .=  ' and '.$jobOpportunities->tableName.'.statuskey = 1 ';

/* ===================== CITY ========================================== */  
$arrSelectedCity = explode(',',$_GET['citykey']);
$arrSelectedCity = array_unique($arrSelectedCity);
$rsCity = $city->searchData($city->tableName.'.statuskey',1);
foreach($rsCity as $key=>$row)  { 
    $_POST['chkCity[]'] = (in_array($row['pkey'],$arrSelectedCity)) ? 1 : '';
    $rsCity[$key]['input'] = $class->inputCheckBox('chkCity[]',array("etc" => 'attr-rel = '.$row['pkey']));
}

if(!empty($_GET['citykey'])){
    $criteria .= ' and '.$jobOpportunities->tableName.'.citykey in ('.$class->oDbCon->paramString($arrSelectedCity,',').')';   
    array_push($pageUrlParam,'citykey=' . implode(',',$arrSelectedCity) );
} 

$arrTwigVar ['rsCity'] = $rsCity;

/* ===================== JOB FIELD ========================================== */  
$arrSelectedJobField = explode(',',$_GET['jobfieldkey']);
$arrSelectedJobField = array_unique($arrSelectedJobField);
$rsJobField = $careerField->searchData($careerField->tableName.'.statuskey',1);
foreach($rsJobField as $key=>$row)  { 
    $_POST['chkJobField[]'] = (in_array($row['pkey'],$arrSelectedJobField)) ? 1 : '';
    $rsJobField[$key]['input'] = $class->inputCheckBox('chkJobField[]',array("etc" => 'attr-rel = '.$row['pkey']));
}

if(!empty($_GET['jobfieldkey'])){
    $criteria .= ' and '.$jobOpportunities->tableName.'.jobfieldkey in ('.$class->oDbCon->paramString($arrSelectedJobField,',').')';   
    array_push($pageUrlParam,'jobfieldkey=' . implode(',',$arrSelectedBrand) );
} 

$arrTwigVar ['rsJobField'] = $rsJobField;

/* ===================== JOB LIST ========================================== */  

$rsJobs = $jobOpportunities->searchData('','',true,$criteria,$orderby);

$totalrowsperpage = 5;

$totalRows = count($rsJobs);
$totalPages = ceil( $totalRows / $totalrowsperpage); 

$pageIndex = ( isset($_GET) && !empty($_GET['page']) ) ? $_GET['page'] : 0; 
$now = $pageIndex * $totalrowsperpage; 
	
if ($now > $totalRows){
	$now = 0; 
	$pageIndex = 0; 
}
 
$arrTwigVar ['pageIndex'] =  $pageIndex;  


$limit = ' limit ' . $now . ', ' . $totalrowsperpage;

$rsJobs = $jobOpportunities->searchData('','',true,$criteria,$orderby,$limit);

for($i=0;$i<count($arrTwigVar['categoryPath']);$i++)  
    array_push($arrActive,$arrTwigVar ['SELF_PAGE'].'?'.$arrTwigVar['categoryPath'][$i]['pkey']);  

$arrTwigVar ['rsJobs'] = $rsJobs;

$arrTwigVar ['totalPages'] =  $totalPages;
$arrTwigVar ['btnUpdateFilter'] =   $class->inputSubmit('btnUpdateFilter',$class->lang['updateSearchFilter'],array('add-class' => 'btnUpdate'));   
$arrTwigVar ['pageUrlParam'] = (!empty($pageUrlParam)) ? '&'. implode('&',$pageUrlParam) : ''; // you can change it later in html / js, this is just a default variable

$arrTwigVar ['ACTIVE_MENU'] =  $arrActive;

$arrTwigVar ['CANONICAL'] = rtrim($class->loadSetting('sitesName'),'/') . '/career' ;
//$arrTwigVar ['STRUCTURE_DATA'] = $jobOpportunities->generateStructurData($rsJobs);  


echo $twig->render('careers-list.html', $arrTwigVar);

?>