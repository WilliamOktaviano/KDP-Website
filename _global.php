<?php
require_once  $_SERVER ['DOCUMENT_ROOT'].'/Twig/Autoloader.php';
Twig_Autoloader::register();

$loader = new Twig_Loader_Filesystem($class->templateDocPath); 

$twig = new Twig_Environment($loader); 
$twig->addExtension(new Twig_Extension_Array());   

require_once  $_SERVER ['DOCUMENT_ROOT'].'/_twig-function.php';

$arrTwigVar = array();

$arrTwigVar['PAGE_ID'] = basename ($_SERVER['PHP_SELF'] ,".php");
$arrTwigVar ['DOMAIN_NAME'] = DOMAIN_NAME;
$arrTwigVar ['SELF_PAGE'] = $_SERVER['PHP_SELF'];  
$arrTwigVar ['HTTP_HOST'] = HTTP_HOST; 
$arrTwigVar ['REQUEST_URI'] = REQUEST_URI;

$arrActive = array($arrTwigVar ['SELF_PAGE']);
$arrTwigVar ['ACTIVE_MENU'] = $arrActive;  

$arrTwigVar ['TEMPLATE_CSS_PATH'] =  $class->templateCssPath;
$arrTwigVar ['TEMPLATE_JS_PATH'] =  $class->templateJsPath;
$arrTwigVar ['TEMPLATE_JS_PAGE_PATH'] =  $class->templateJsPath;
$arrTwigVar ['TEMPLATE_IMG_PATH'] =  $class->templateImgPath;
 
$arrTwigVar ['DEFAULT_URL_UPLOAD_PATH'] =  DEFAULT_URL_UPLOAD_PATH;
$arrTwigVar ['DEFAULT_DOC_UPLOAD_PATH'] =  DEFAULT_DOC_UPLOAD_PATH;
$arrTwigVar ['UPLOAD_TEMP_DOC_SHORT'] =  UPLOAD_TEMP_DOC_SHORT; 
$arrTwigVar ['UPLOAD_TEMP_URL'] =  UPLOAD_TEMP_URL;  
$arrTwigVar ['PHPTHUMB_URL_SRC'] =  PHPTHUMB_URL_PATH; 
 
$arrTwigVar ['META_URL'] = $class->loadSetting('metaURL') . $_SERVER['REQUEST_URI']; 
$arrTwigVar ['META_TYPE'] = $class->loadSetting('metaType');
$arrTwigVar ['META_TITLE'] = $class->loadSetting('metaTitle');
$arrTwigVar ['META_DESCRIPTION'] = $class->loadSetting('metaDescription');
$arrTwigVar ['META_IMAGE'] = $class->defaultURLUploadPath . 'setting/metaImage/'.$class->loadSetting('metaImage'); 
$arrTwigVar ['META_KEYWORDS'] = $class->loadSetting('metaKeywords');
$arrTwigVar ['CANONICAL'] = rtrim($class->loadSetting('sitesName'),'/') . rtrim(REQUEST_URI,'/');
  
$userkey = 0;
if (isset($_SESSION) && !empty($_SESSION[$class->loginSession]['id'])){ 
    $userkey = base64_decode($_SESSION[$class->loginSession]['id']);
    $arrTwigVar['loginName'] =  $_SESSION[$class->loginSession]['name'];
}
 
if(!isset($_SESSION['itemsToCompare']))
    $_SESSION['itemsToCompare'] = array();

define ('USERKEY', $userkey);
 
/* LANGUAGE */   
$rsLang = $lang->searchData($lang->tableName.'.statuskey',1);
for ($j=0;$j<count($rsLang);$j++) 
   $rsLang[$j]['phpThumbHash'] =  getPHPThumbHash($rsLang[$j]['file']);
  
$arrTwigVar['rslang'] = $rsLang;

if(!empty($rsLang)) {
    $_SESSION['lang'] = $rsLang[0]['code'];
    $class->setActiveLang();
}

$arrTwigVar ['LANG'] = $class->lang;
$arrTwigVar ['ERRORMSG'] = $class->errorMsg;
$arrTwigVar ['activeLangIndex'] = $class->langCode;
 
/* settings */
$rsSetting =  $setting->getSettingData();

for ($i=0;$i<count($rsSetting);$i++){
	$code = $rsSetting[$i]['code'];
	 
	if ($rsSetting[$i]['multivalue'] == 0){ 
			if ($rsSetting[$i]['type'] == 3 )
				$arrTwigVar ['settings'][$code] =str_replace(chr(13),'<br>',$rsSetting[$i]['value']);
			else
				$arrTwigVar ['settings'][$code] = $rsSetting[$i]['value'] ;
        
            if ($rsSetting[$i]['type'] == 6) 
                $arrTwigVar ['settings'][$code.'Hash'] =  getPHPThumbHash($rsSetting[$i]['value']);
            
	}else{ 
		$arrDetail = $setting->getDetailByCode($code);
		$arrTwigVar ['settings'][$code] = $arrDetail;
	} 
		 
}

$IGNOREQOH = ($class->loadSetting('ignoreQOH') == 1) ? true : false;
define ('IGNORE_QOH', $IGNOREQOH) ;
$arrTwigVar['ignoreQOH'] = IGNORE_QOH; 

/* KHUSUS WA */
$arrTwigVar['whatsapp'] = array();
foreach($arrTwigVar['settings']['companyMessenger'] as $row){
    if(in_array($row['label'], array('wa','whatsapp'))) 
        array_push($arrTwigVar['whatsapp'],$row);
}

/* ICON */
$favicon = (!empty($arrTwigVar ['settings']['webIcon'])) ? $class->defaultURLUploadPath.'setting/webIcon/'.$arrTwigVar ['settings']['webIcon'] : HTTP_HOST.'include/img/programstok.ico' ;
$arrTwigVar ['favicon'] = $favicon;

/* kalo under maintenance redirect ke under-maintenance */  

if ($arrTwigVar ['settings']['underMaintenance'] == 1)
   header('Location: /under-maintenance');
    
//$IS_ACTIVE_MODULE = $class->isActiveModule(array('banner', 'testimonial', 'item','service','course','article','youtube','page'));

/* ===================== BANNER ===================== */
if($IS_ACTIVE_MODULE['banner']){ 
	$rsBannerPosition = $banner->getAllPosition();
	for($i=0;$i<count($rsBannerPosition);$i++){
		$rsBanner = $banner->searchData($banner->tableNamePosition.'.name', $rsBannerPosition[$i]['name'],true,' and statuskey = 1',' order by orderlist asc'); 

		for ($j=0;$j<count($rsBanner);$j++){
		   $rsBanner[$j]['phpThumbHash'] =  getPHPThumbHash($rsBanner[$j]['file']); 
		}   

		$arrTwigVar['banner'][strtolower($rsBannerPosition[$i]['name'])] = $rsBanner; 
	}	
}


/* ===================== TESTIMONIAL ===================== */

if($IS_ACTIVE_MODULE['testimonial']){
	$rsRandTestimonial = $testimonial->getRandomData(10);
	foreach($rsRandTestimonial as $key=>$testimonialRow)
	  $rsRandTestimonial[$key]['phpThumbHash'] =  getPHPThumbHash($testimonialRow['file']); 
	
	$arrTwigVar['rsRandTestimonial'] = $testimonial->updateContentLang($rsRandTestimonial);  
}

/* ===================== ITEM CATEGORY ========================================== */
if($IS_ACTIVE_MODULE['item']){
	$arrTwigVar['compiledItemCategory'] = $itemCategory->compileChildArray(true);

	for($i=0;$i<count($arrTwigVar['compiledItemCategory']);$i++){
	  if (!isset($arrTwigVar['compiledItemCategory'][$i]))
		  continue;

	   for($j=0;$j<count($arrTwigVar['compiledItemCategory'][$i]['childnode']);$j++){
			 $arrTwigVar['compiledItemCategory'][$i]['childnode'][$j]['phpThumbHash'] =  getPHPThumbHash($arrTwigVar['compiledItemCategory'][$i]['childnode'][$j]['file']);
		}    
	} 

	//$class->setLog($arrTwigVar['compiledItemCategory'],true);

	$rsItemCategory= $itemCategory->searchData($itemCategory->tableName.'.statuskey',1,true,' and '.$itemCategory->tableName.'.isshow = 1 and '.$itemCategory->tableName.'.featured = 1'); 
	for($j=0;$j<count($rsItemCategory);$j++){   
		if( !empty($rsItemCategory[$j]['file'])) 
			$rsItemCategory[$j]['phpThumbHash'] = getPHPThumbHash($rsItemCategory[$j]['file']);	  
	}
	$arrTwigVar['itemCategory']  = $rsItemCategory;
	
	if ( isset($_GET) && !empty($_GET['categorykey']) ) $_POST['selQuickSearchCategory'] = $_GET['categorykey'];
	$arrTemp = array("0" => $class->lang['allCategories']);
	$arrCategory = $class->convertForCombobox($arrTwigVar['compiledItemCategory'][0]['childnode'], 'pkey','name');  
	$arrCategory = $arrTemp + $arrCategory;
	$arrTwigVar['selCategoryQuickSearch'] =  $class->inputSelect('selQuickSearchCategory',  $arrCategory  ); 


}

if($IS_ACTIVE_MODULE['service']){

	/* ===================== SERVICES ========================================== */
	$rsService = $service->searchData($service->tableName.'.statuskey',1,true, '',' order by name asc');
	for($j=0;$j<count($rsService);$j++){  

		$rsImage = $service->getItemImage($rsService[$j]['pkey']);

		if(!empty($rsImage)){ 
			$rsService[$j]['file'] = $rsImage[0]['file'];	
			$rsService[$j]['phpThumbHash'] = getPHPThumbHash($rsImage[0]['file']);	
		}

		if( !empty($rsService[$j]['iconimage']) ){ 
			$rsService[$j]['iconPhpThumbHash'] = getPHPThumbHash($rsService[$j]['iconimage']);	 
		}

	}

	$arrTwigVar['services'] = $rsService;

	/* ===================== SERVICE CATEGORY ========================================== */
	$arrTwigVar['compiledServiceCategory'] = $serviceCategory->compileChildArray(true);  
	for($i=0;$i<count($arrTwigVar['compiledServiceCategory']);$i++){
	  if (!isset($arrTwigVar['compiledServiceCategory'][$i]))
		  continue;

	   for($j=0;$j<count($arrTwigVar['compiledServiceCategory'][$i]['childnode']);$j++){
			 $arrTwigVar['compiledServiceCategory'][$i]['childnode'][$j]['phpThumbHash'] =  getPHPThumbHash($arrTwigVar['compiledServiceCategory'][$i]['childnode'][$j]['file']);
		}    
	} 
}
 
/* ===================== COURSE CATEGORY ========================================== */

if($IS_ACTIVE_MODULE['course']){ 
	$arrTwigVar['compiledCourseCategory'] = $courseCategory->compileChildArray(true);  
	for($i=0;$i<count($arrTwigVar['compiledCourseCategory']);$i++){
	  if (!isset($arrTwigVar['compiledCourseCategory'][$i]))
		  continue;

	   for($j=0;$j<count($arrTwigVar['compiledCourseCategory'][$i]['childnode']);$j++){
			 $arrTwigVar['compiledCourseCategory'][$i]['childnode'][$j]['phpThumbHash'] =  getPHPThumbHash($arrTwigVar['compiledCourseCategory'][$i]['childnode'][$j]['file']);
		}    
	} 

	$arrTwigVar['courseCategory'] = $courseCategory->searchData($courseCategory->tableName.'.statuskey',1,true,' and '.$courseCategory->tableName.'.isshow = 1'); 
} 

/* ===================== ARTICLE ===================== */ 

if($IS_ACTIVE_MODULE['article']){ 
	$rsRandArticle = $article->getRandomData(5);
	$arrTwigVar['rsRandArticle'] =  $article->updateContentLang($rsRandArticle);  
}
 
/* ===================== YOUTUBE ===================== */ 
if($IS_ACTIVE_MODULE['youtube']){
$rsRandYoutube = $youtube->getRandomData(5);
$arrTwigVar['rsRandYoutube'] = $rsRandYoutube; 
}
 
 
/* ===================== PAGE CONTENT ===================== */ 

if($IS_ACTIVE_MODULE['page']){ 
	$arrFreePage = array();
	$rsFreePage = $page->searchData($page->tableName.'.statuskey',1); 
	for($i=0;$i<count($rsFreePage);$i++){ 
		$nameindex = $rsFreePage[$i]['pagename'];
		$arrFreePage[$nameindex]['pkey'] = $rsFreePage[$i]['pkey'];
		$arrFreePage[$nameindex]['name'] = $nameindex;
		$arrFreePage[$nameindex]['title'] = $rsFreePage[$i]['title'];
		$arrFreePage[$nameindex]['shortdesc'] = $rsFreePage[$i]['shortdesc'];
		$arrFreePage[$nameindex]['detail'] = $rsFreePage[$i]['detail'];
		$arrFreePage[$nameindex]['file'] = $rsFreePage[$i]['file']; 
		$arrFreePage[$nameindex]['phpThumbHash'] =  getPHPThumbHash($rsFreePage[$i]['file']);
	}
	$arrTwigVar['page'] = $page->updateContentLang($arrFreePage); 
}

$arrTwigVar ['btnAddToCrt'] =   $class->inputSubmit('btnAddToCart', $class->lang['addToCart'], array('etc' => ' style="width:100%;"')); 
 
// nanti perlu dipindahan agar lebih fleksible
$arrTwigVar['btnGoogle'] = '
<div class="google">
<div class="button_container" widht="15%">
    <div id="g_id_onload"
        data-client_id="'.$class->loadSetting('googleOAuthId').'"
        data-auto_prompt="false"
        data-nonce="'.$class->generateStrongPassword().'"
        data-login_uri="'.HTTP_HOST.'api/sso/google-login"
        data-ux_mode="redirect"
        data-auto_prompt="false">
    </div>
    <div class="g_id_signin"
        data-type="standard"
        data-size="medium"
        data-width="274"
        data-theme="filled_blue"
        data-text="continue_with"
        data-shape="rectangular"
        data-logo_alignment="left"
        data-locale="id-ID">
    </div>
</div>
</div>';



 
 
/* QUICK SEARCH */
if ( isset($_GET) && !empty($_GET['key']) )
$_POST['quickSearch'] = $_GET['key'];


$arrTwigVar['inputQuickSearch'] = $class->inputText('quickSearch', array( 'etc' => 'placeholder="'.$class->lang['searchProduct'].'..."')); 
  
$arrTwigVar['inputSubscribeNewsletter'] =   $class->inputText('txtNewsletterEmail');
$arrTwigVar['btnSubmitSubscribeNewsletter'] =   $class->inputButton('btnSubscribeNewsletter', $class->lang['subscribe']);

/* ======== INPUT QUICK LOGIN ========== */ 
/*
// dipindah ke file popup login
$arrTwigVar['inputPopupUsername'] = $class->inputText('username'); 
$arrTwigVar['inputPopupPassword'] = $class->inputText('password'); 

$arrTwigVar['inputPopupUsernamePlaceholder'] = $class->inputText('username', array( 'etc' => 'placeholder="'.$class->lang['username'].'"')); 
$arrTwigVar['inputPopupPasswordPlaceholder'] = $class->inputText('password', array( 'etc' => 'placeholder="'.$class->lang['password'].'"')); 

$arrTwigVar['btnSubmitPopupLogin'] =   $class->inputSubmit('btnSave',$class->lang['login']); 
*/

/* ======== FOOTER CONTACT ========== */ 
$arrTwigVar['inputHidQuickContact'] =   $class->inputHidden('hidQuickContact', array('value' => 1));
$arrTwigVar['inputQuickContactFrom'] = $class->inputText('quickContactFrom', array( 'etc' => 'placeholder="'.$class->lang['name'].'"')); 
$arrTwigVar['inputQuickContactPhone'] = $class->inputText('quickContactPhone', array( 'etc' => 'placeholder="'.$class->lang['phone'].'"')); 
$arrTwigVar['inputQuickContactEmail'] = $class->inputText('quickContactEmail', array( 'etc' => 'placeholder="'.$class->lang['email'].'"')); 
$arrTwigVar['inputQuickContactMessage'] = $class->inputTextArea('quickContactMessage', array( 'etc' => 'placeholder="'.$class->lang['message'].'" class="btn btn-primary" style="height:8em"')); 
$arrTwigVar['inputQuickContactSubmit'] = $class->inputSubmit('btnQuickContactSubmit', $class->lang['submit']); 

// buat form contact
$arrTwigVar ['inputContactNamePlaceholder'] =  $class->inputText('name',array('etc' => 'placeholder="'.$class->lang['name'].'"')); 
$arrTwigVar ['inputContactPhonePlaceholder'] =  $class->inputText('phone',array('etc' => 'placeholder="'.$class->lang['phone'].'"')); 
$arrTwigVar ['inputContactEmailPlaceholder'] =  $class->inputText('email',array('etc' => 'placeholder="'.$class->lang['email'].'"')); 
$arrTwigVar ['inputContactSubjectPlaceholder'] =  $class->inputText('subject',array('etc' => 'placeholder="'.$class->lang['subject'].'"')); 
$arrTwigVar ['inputContactMessagePlaceholder'] =   $class->inputTextArea('message', array('etc' => 'placeholder="'.$class->lang['message'].'" style="height:10em"'));

?>