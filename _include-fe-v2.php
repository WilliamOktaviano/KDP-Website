<?php

require_once DOC_ROOT. 'connections/_connection.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/BaseClass.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/AutoCode.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CustomCode.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Lang.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Mobile_Detect.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Employee.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Banner.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Testimonial.class.php';        
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Category.class.php';       
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemCategory.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ServiceCategory.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Service.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/News.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Article.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Youtube.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Page.class.php';    
require_once DOC_ROOT. 'phpthumb/phpThumb.config.php'; 
 
$GLOBALS['ObjCol'] = array();
$GLOBALS['oDbCon'] = new Database($rs[0]['dbusername'],$rs[0]['dbpass'],$rs[0]['dbname'],$host);

$class = new Baseclass();
$GLOBALS['class'] = $class;


$setting = new Setting(); 	   
$security = new Security();
$lang = new Lang(); 


// load settings
$TABLEKEY_SETTINGS = $class->loadTableKeySettings();
define('TABLENAME_SETTINGS', array_column($TABLEKEY_SETTINGS,null,'tablename'));
define('TABLEKEY_SETTINGS', array_column($TABLEKEY_SETTINGS,null,'pkey'));

// define plan configuration 
$PLAN_TYPE = $security->getUserPlanType();
$PLAN_TYPE = $PLAN_TYPE[0];   
define('PLAN_TYPE', $PLAN_TYPE);

if(!PLAN_TYPE['usefrontend']){  
   header('Location: /admin');
   die;
} 


$useGL = $class->loadSetting('useGL');
$useGL = ($useGL == 1) ? true : false;  
define('USE_GL', $useGL);

$banner = new Banner(); 
$testimonial = new Testimonial(); 
$itemCategory = new ItemCategory(); 
$serviceCategory = new ServiceCategory(); 
$service = new Service(SERVICE); 
$news = new News(); 
$article = new Article(); 
$youtube = new Youtube(); 
$page = new Page(); 

/*require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CourseCategory.class.php';    
$courseCategory = new CourseCategory(); */

function includeClass($classFile, $createObject = true){
    if (!is_array($classFile)) $classFile = array($classFile);
    
    foreach($classFile as $file){ 
         $filePath = DOC_ROOT. 'include/'.CLASS_VERSION.'/'.$file;
            if(is_file($filePath))
                require_once $filePath; 
    }
}

// sementara disini dulu biar ggk perlu udate setiap page,
// nanti kalo ad waktu baru pindahin ke setiap page
$IS_ACTIVE_MODULE = $class->isActiveModule(array('banner','item','partners','testimonial','youtube','page','portfolio','service','course','quiz','gallery','news','article'));

?>