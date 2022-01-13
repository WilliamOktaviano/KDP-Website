<?php
  
include '../../assets/vendor/autoload.php'; 
   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Excel.class.php';         
 
require_once  $_SERVER ['DOCUMENT_ROOT'].'/Twig/Autoloader.php';
Twig_Autoloader::register();
 
$templatePath = $class->defaultDocAdminPath.'/report/template'; 

// cek file personalized      
$docPersonalizedFile = PERSONALIZED_DOC_PATH.'admin/report/'.basename($_SERVER['PHP_SELF']);  

$templateFile = str_replace('.php','',basename($_SERVER['PHP_SELF']));
$personalizedTemplateFile = PERSONALIZED_DOC_PATH.'admin/report/template/'.$templateFile.'.html';
$templatePath = (is_file($personalizedTemplateFile)) ? PERSONALIZED_DOC_PATH.'admin/report/template' : $templatePath; 

  
$loader = new Twig_Loader_Filesystem($templatePath);
$twig = new Twig_Environment($loader);
  
require_once  $_SERVER ['DOCUMENT_ROOT'].'/_twig-function.php';
 
$arrTwigVar ['TEMPLATE_CSS_PATH'] =  $class->adminCssPath;
$arrTwigVar ['ADMIN_CSS_VERSION'] =  ADMIN_CSS_VERSION; 
$arrTwigVar ['TEMPLATE_JS_PATH'] =  $class->defaultJsPath; 
$arrTwigVar ['SELF_PAGE'] = $_SERVER['PHP_SELF'];

$arrTwigVar['INPUT_HID_FILE_DATA'] = $class->inputHidden('hidFileData');
$arrTwigVar['INPUT_HID_ORDER_BY'] = $class->inputHidden('hidOrderBy');
$arrTwigVar['INPUT_HID_ORDER_TYPE'] = $class->inputHidden('hidOrderType');
$arrTwigVar['INPUT_HID_ONSUBMIT'] = $class->inputHidden('hidAction', array('overwritePost' => false, 'value' => 1));
$arrTwigVar['INPUT_HID_EXPORT'] = $class->inputHidden('hidExportExcel');
$arrTwigVar['INPUT_SUBMIT'] = $class->inputSubmit('btnSubmit',$class->lang['submit'], array('etc' => 'style="width:100%;"' ));
$arrTwigVar['INPUT_HID_RS'] = $class->inputHidden('hidRs');

/* LANGUAGE */
$arrTwigVar ['LANG'] = $class->lang;
$arrTwigVar ['ERRORMSG'] = $class->errorMsg;


/* settings */
$rsSetting =  $setting->getSettingData();
for ($i=0;$i<count($rsSetting);$i++){
	$code = $rsSetting[$i]['code'];
	 
	if ($rsSetting[$i]['multivalue'] == 0){ 
			if ($rsSetting[$i]['type'] == 3 )
				$arrTwigVar ['settings'][$code] =str_replace(chr(13),'<br>',$rsSetting[$i]['value']);
			else
				$arrTwigVar ['settings'][$code] = $rsSetting[$i]['value'] ;
	}else{ 
		$arrDetail = $setting->getDetailByCode($code);
		$arrTwigVar ['settings'][$code] = $arrDetail;
	} 
		 
}


$EXPORT_TYPE = ((isset($_POST['hidExportExcel']) && !empty($_POST['hidExportExcel']))) ? $_POST['hidExportExcel'] : 0; 


// TEST
// kalo sudah ad buffer file langsung export saja
// sementara utk yg export excel dulu, utk template gk perlu
if($EXPORT_TYPE == 1 && isset($_POST['hidFileData']) && !empty($_POST['hidFileData'])){
    //$start_time = microtime(TRUE);
    
    $excel = new Excel();
    
    $fileIndexName = $_POST['hidFileData'];
    
    $path = $class->uploadTempDoc.'export-data/';  
    $fileName = $path.$fileIndexName;
    
    $handle = fopen($fileName, "r");
    $contents = fread($handle,filesize($fileName));
    //$contents = gzuncompress($contents);
    
    $dataFromFile = json_decode($contents,true);
    
    $arrTemplate = $dataFromFile['arrTemplate'];
    $moduleName = $dataFromFile['module'];
     
    
    //$class->setLog($class->getPerformanceLog($start_time),true);
    
    $arrExportParam = array();
    $arrExportParam['exportType'] = $EXPORT_TYPE;
    $arrExportParam['module'] = $moduleName;
    $excel->exportToSave($arrTemplate,$arrExportParam,array(),$fileIndexName);  
    
    
}
//===================
  
$filePath = PERSONALIZED_DOC_PATH.'admin/report/'. basename($_SERVER['PHP_SELF']);      
if (is_file($filePath)){ 
    include $filePath; 
    die;
}

?>