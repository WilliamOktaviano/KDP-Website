<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  

includeClass(array('JobOpportunities.class.php','Category.class.php','CareerCategory.class.php','CareerField.class.php','GalleryCategory.class.php'));  
$jobOpportunities = new JobOpportunities();
$careerCategory = new CareerCategory();
$careerField = new CareerField();
$galleryCategory = new GalleryCategory(2);

$pageIndex = 0;
if ( isset($_GET) && !empty($_GET['page']) ){
	$pageIndex = $_GET['page'];
}
$arrTwigVar ['pageIndex'] =  $pageIndex;
 

$totalrowsperpage = $class->loadSetting('newstotalrowsperpage');
$now = $pageIndex * $totalrowsperpage;
$limit = ' limit ' . $now . ', ' . $totalrowsperpage;
$orderby = 'order by createdon desc';
$criteria =  ' and '.$jobOpportunities->tableName.'.statuskey = 1 ';

/* ===================== JOB LIST ========================================== */  
$rsJobs = $jobOpportunities->searchData('','',true,$criteria,$orderby,$limit);

$arrTwigVar ['rsJobOpportunities'] = $jobOpportunities->updateContentLang($rsJobs);


/* ===================== CAREER CATEGORY ========================================== */  
$rsCategoryCareer = $careerCategory->searchData($careerCategory->tableName.'.statuskey',1);

for($i=0;$i<count($rsCategoryCareer);$i++){
        $rsCategoryCareer[$i]['phpThumbHash'] = getPHPThumbHash($rsCategoryCareer[$i]['file']);
}
$arrTwigVar ['rsCategoryCareer'] =  $rsCategoryCareer;



/* ===================== HR Gallery CATEGORY ========================================== */  
$rsGalleryHRCategory = $galleryCategory->searchData($galleryCategory->tableName.'.statuskey',1);

for($i=0;$i<count($rsGalleryHRCategory);$i++){
        $rsGalleryHRCategory[$i]['phpThumbHash'] = getPHPThumbHash($rsGalleryHRCategory[$i]['file']);
}
$arrTwigVar ['rsGalleryHRCategory'] =  $rsGalleryHRCategory;


/* ===================== JOB FIELD ========================================== */  
$rsJobField = $careerField->getTotalJobPosition();
for($i=0;$i<count($rsJobField);$i++) 
        $rsJobField[$i]['phpThumbHash'] = getPHPThumbHash($rsJobField[$i]['file']); 

$arrTwigVar ['rsJobField'] =  $rsJobField;

$totalPages = ceil( $jobOpportunities->getTotalRows($criteria) / $totalrowsperpage);
$arrTwigVar ['totalPages'] =  $totalPages;

echo $twig->render('job-opportunities.html', $arrTwigVar);

?>