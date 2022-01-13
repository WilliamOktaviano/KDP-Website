<?php
require_once '../_config.php'; 
require_once '../_include-v2.php';  
includeClass('Warehouse.class.php');
require_once DOC_ROOT. 'phpthumb/phpThumb.config.php'; 

if (empty($_SESSION[$class->loginAdminSession])){ 
	header("location: /admin"); 
}
 
$jqueryScript = '';
  
$avatarImg = '';

$employeeAvatar = $_SESSION[$class->loginAdminSession]['photofile']; 

if (!empty($employeeAvatar)){ 
     $avatarImg = 'style="background-image:url(\'../phpthumb/phpThumb.php?src='.PHPTHUMB_URL_PATH.$employee->uploadPhotoFolder.$class->userkey.'/'.$employeeAvatar.'&far=C&f=png&hash='.getPHPThumbHash($employeeAvatar).'\')"';  
}else{ 
    $profileImg = $class->loadSetting('companyLogo'); 
    if (!empty($profileImg)) 
        $avatarImg = 'style="background-image:url(\'../phpthumb/phpThumb.php?src='.PHPTHUMB_URL_PATH.'setting/companyLogo/'.$profileImg.'&far=C&f=png&hash='.getPHPThumbHash($profileImg).'\')"';  
}    

//temp
$arrKey = array_keys($GLOBALS['ObjCol']);
foreach($arrKey as $key){
    $obj = $GLOBALS['ObjCol'][$key];
    $obj->updateTablekeyStatus();
}

?>
<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">-->
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />  
<link rel=apple-touch-icon sizes=180x180 href="../include/img/ico/apple-touch-icon.png">
<link rel=icon type=image/png sizes=32x32 href="../include/img/ico/favicon-32x32.png">
<link rel=icon type=image/png sizes=16x16 href="../include/img/ico/favicon-16x16.png">
 
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>pace-theme-center-simple.css">   
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>pace.min.js"></script>   
<script>
    Pace.options.ajax = false; 
    Pace.on("done", function(){ $("#preloader-screen").hide(); });
    Pace.on("update", function(percent){  document.getElementById("pace-loading-percentage").innerHTML = Math.round(percent) +"%"; });  
</script>    
      
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>font-awesome-5.15.min.css">   
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>jquery-ui.min.css" />    
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>clock.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>fileuploader.css"> 
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>bootstrap.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>bootstrapValidator.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>jquery-ui-timepicker-addon.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>scrollToTop.css"/> 
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>sol.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>jquery.contextMenu-1.3.min.css"/>    
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath.ADMIN_CSS_VERSION; ?>">  
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>responsive.css" />  

     
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-3.3.1.min.js"></script>  
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>bootstrap.min.js"></script>   
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>bootstrapValidator.js"></script>          
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-ui.min.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-ui-timepicker-addon.min.js"></script>   
<script type='text/javascript' src='<?php echo $class->defaultJsPath; ?>clock.js'></script>
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery.formatCurrency-1.4.0.min.js" ></script> 
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>fileuploader.min.js"></script>  
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>ckeditor-4.11.3/ckeditor.js"></script>
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>ckeditor-4.11.3/adapters/jquery.js"></script>
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>ckfinder/ckfinder.js"></script>
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-scrollToTop.js"></script>   
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>sol.js"></script>
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery.contextMenu-1.1.min.js"></script>   
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>php-variables-1.2.min.js"></script> 
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>moment.min.js"></script>  
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>main-3.81.min.js"></script>  
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>formJS-1.139.min.js"></script>  

<?php if(DOMAIN_NAME == 'pstn.program-stok.com') { ?>
    <script type="text/javascript" src="//laz-g-cdn.alicdn.com/sj/securesdk/0.0.3/securesdk_lzd_v1.js" id="J_secure_sdk_v2" data-appkey="110359"></script>
<?php } ?>    

<title><?php echo  $class->loadSetting('companyName'); ?></title> 
 
</head> 
<body> 
<div id="preloader-screen">
    <div style=" height: 100%; width:100%; display:table">
        <div style="display:table-row">
            <div style="display:table-cell; vertical-align:middle;"> 
                <div class="loading-text">Loading <div id="pace-loading-percentage">0%</div></div> 
            </div>
        </div>
    </div>  
    <div class="pace"></div>
</div> 
      
<!-- The overlay and the box -->
<div id="popup-panel"><div class="div-table"><div class="div-table-row"><div class="div-table-col content-panel"></div></div></div></div>
<!-- The overlay and the box -->  
 
<!-- popup notification -->
<ul id="mnv-popup-notification" class="user-select-none"> 
    <li><div class="close-all"><?php echo $class->lang['closeAll']; ?></div><div style="clear:both"></div></li>
    <li class="list template">
        <i class="close-icon fas fa-times"></i>
        <div class="content"></div>
    </li>
</ul>
<!-- popup notification -->
    

<div id="dialog-message" title="Pesan"></div>
<!-- header -->
<div id="page-header" <?php echo (IS_DEVELOPMENT) ?  'class="bg-green-avocado"' : ''; ?>>
    <div class="show-left-menu-icon"><span class="fas fa-bars"></span></div>
    <div class="title"><?php echo  $class->loadSetting('companyName') ;?></div> 
    <!-- clock -->
    <div class="clock" style="margin-right:1em;">
       <div id="Date" style="margin-right:0.5em;"></div>
       <div id="hours"></div>
       <div class="point">:</div>
       <div id="min"></div>
       <div class="point">:</div>
       <div id="sec"></div> 
    </div>
    <!-- clock -->
    <div  style="clear:both"></div>
</div> 
<!-- header -->
    
<div class="div-table" style="width:100%; min-width:1200px; height:100vh; table-layout:fixed">
    <div class="div-table-row">
        <div class="div-table-col left-menu-col" >
            <!-- left menu --> 
                <div id="profile" >
                	<div  class="div-table" style="margin:0.5em; width: 95%; position:relative">
                       <div class="div-table-row"> 
                            <div class="div-table-col" style="width: 4.5em"><div class="avatar"><div class="img-panel" <?php echo $avatarImg; ?>></div></div></div>
                            <div class="div-table-col" style="padding-bottom:1.2em"> 
                                    <div class="name"><?php echo $_SESSION[$class->loginAdminSession]['name'];?></div> 
                                    <div class="link" style="position:absolute; bottom: 0"><a href="javascript: openTab('<?php echo $class->lang['profile'];?>','profile');"><?php echo $class->lang['profile']; ?></a> &sdot; <a href="logout">Logout</a></div>
                             </div>
                        </div>	
                    </div> 
                </div>
                
                <div style="clear:both;"></div>
                
                <div id="tabs-menu" class="user-select-none">
                    <ul>
                        <li><a href="#left-menu-panel"><div class="tab-menu-icon nav-icon"></div></a></li>
                        <li><a href="#left-status-panel"><div class="tab-menu-icon status-icon"></div></a></li> 
                        <li><a href="#left-notification-panel"><div class="tab-menu-icon notification-icon"><i class="warning-icon fas fa-exclamation-circle"></i></div></a></li> 
                    </ul> 
                    <div id="left-menu-panel" class="menu-navigation-tab" style="padding:0 !important">
                       <div id="main-menu"><?php include 'menu.php'; ?> </div>
                    </div>  
                    <div id="left-status-panel"></div>
                    <div id="tag-status-panel"></div>
                    <div id="left-notification-panel"> 
                        <?php include 'notification.php'; ?>
                    </div>
                </div> 
                         
            <!-- left menu -->
        </div> 
        <div class="div-table-col" style="vertical-align:top; "> 
        <!-- content --> 
            <div id="tabs" > 
                <ul> 
                </ul>   
                <div id="tabs-1">
                 
                </div> 
            </div>  
        <!-- content --> 
        </div>
    </div>
 </div>
 <div id="back-to-top"></div>
</body> 
</html>  
     
<script> CKFinder.setupCKEditor(null, '<?php echo $class->defaultJsPath; ?>ckfinder/'); </script>

<?php 
if (!empty($jqueryScript)) {
    echo '<script type="text/javascript">jQuery(document).ready(function(){'.$jqueryScript.'});</script>'; 
}

// matiin saja dulu biar lebih cepet
//echo $class->loadSetting('googleAnalytics');
?> 