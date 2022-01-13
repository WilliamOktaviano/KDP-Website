<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  
  
includeClass(array('JobOpportunities.class.php'));  
$jobOpportunities = new JobOpportunities();

if(empty($_GET)){
	header("location: /");
	die;
} 

$id = $_GET['id']; 
$rsItem = $jobOpportunities->searchData($jobOpportunities->tableName.'.pkey',$id,true, ' and '.$jobOpportunities->tableName.'.statuskey = 1','','',''); 
if(empty($rsItem)){
	header("location: /");
	die;
} 
$_POST['hiditemkey[]'] = $rsItem[0]['pkey'];
 
$arrTwigVar['rsItem'] =  $rsItem;       
$title = (empty($rsItem[0]['title'])) ? $rsItem[0]['code'] : $rsItem[0]['title']; 
 
$descForMeta = $title;
 
$arrTwigVar ['META_TITLE'] = $title;
$arrTwigVar ['META_DESCRIPTION'] = $descForMeta; 
$arrTwigVar ['META_KEYWORDS'] = $title ;
 
$arrTwigVar ['CANONICAL'] = rtrim($class->loadSetting('sitesName'),'/') . '/career' ;
  
array_push($arrActive,'/career.php');
for($i=0;$i<count($arrTwigVar['categoryPath']);$i++)  
    array_push($arrActive,'/career.php?'.$arrTwigVar['categoryPath'][$i]['pkey']);  
   
$arrTwigVar ['ACTIVE_MENU'] =  $arrActive;  
echo $twig->render('career-detail.html', $arrTwigVar);
?>