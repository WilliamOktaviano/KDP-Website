<?php 
require_once '_config.php'; 
require_once '_include-min.php'; 


require_once  $_SERVER ['DOCUMENT_ROOT'].'/Twig/Autoloader.php';
Twig_Autoloader::register();

$loader = new Twig_Loader_Filesystem( $class->templateDocPath);
$twig = new Twig_Environment($loader);

$twig->addExtension(new Twig_Extension_Array());

$URLfilter = new Twig_SimpleFilter('urlfilter', function ($string) {
    global $class;
	$string = str_replace($class->arrSearch,$class->arrReplace,$string);
	$string = strtolower($string);
	
	return $string;
});
$twig->addFilter($URLfilter);


$url_decode = new Twig_SimpleFilter('url_decode', function ($string) {
	return urldecode($string);
});
$twig->addFilter($url_decode);
  
$arrTwigVar = array();
$arrTwigVar ['PAGE_TITLE'] = $class->lang['underMaintenance'];
$arrTwigVar ['SELF_PAGE'] = $_SERVER['PHP_SELF'];  
$arrTwigVar ['REQUEST_URI'] = $_SERVER['REQUEST_URI']; 

$arrTwigVar ['TEMPLATE_CSS_PATH'] =  $class->templateCssPath;
$arrTwigVar ['TEMPLATE_JS_PATH'] =  $class->templateJsPath;
$arrTwigVar ['TEMPLATE_IMG_PATH'] =  $class->templateImgPath;
$arrTwigVar ['DEFAULT_URL_UPLOAD_PATH'] =  $class->defaultURLUploadPath;
$arrTwigVar ['DEFAULT_DOC_UPLOAD_PATH'] =  $class->defaultDocUploadPath;
$arrTwigVar ['PHPTHUMB_URL_SRC'] =  $class->phpThumbURLSrc;
 
$arrTwigVar ['META_URL'] = $class->loadSetting('metaURL') . $_SERVER['REQUEST_URI']; 
$arrTwigVar ['META_TYPE'] = $class->loadSetting('metaType');
$arrTwigVar ['META_TITLE'] = $class->loadSetting('metaTitle');
$arrTwigVar ['META_DESCRIPTION'] = $class->loadSetting('metaDescription');
$arrTwigVar ['META_IMAGE'] = $class->defaultURLUploadPath . 'setting/metaImage/'.$class->loadSetting('metaImage'); 
$arrTwigVar ['META_KEYWORDS'] = $class->loadSetting('metaKeywords');

 
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

 
    
echo $twig->render('under-maintenance.html', $arrTwigVar);

?>