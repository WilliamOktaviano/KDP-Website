<?php
require_once '../_config.php'; 
require_once '../_include-v2.php';
require_once DOC_ROOT. 'phpthumb/phpThumb.config.php';  

// kalo gk ad session dan ad cookies
// gk boleh ad !isset $_SESSION, karena bisa lg login
//if(isset($_COOKIE[DOMAIN_COOKIES]) && !empty($_COOKIE[DOMAIN_COOKIES])){ 
//      
//    $token = $security->getCookiesOTP(DOMAIN_COOKIES);
//    
//    $result = $security->AuthCookies($token);
//    if (!empty($result)){ 
//        $result['data'] = $result[0];
//        
//        // harus pake $claas, jd gk bisa ditaro di dalam class Security
//        $_SESSION[$class->loginAdminSession]['id'] = base64_encode($result['data']['pkey']);
//        $_SESSION[$class->loginAdminSession]['name'] = $result['data']['name']; 
//        $_SESSION[$class->loginAdminSession]['username'] = $result['data']['username']; 
//        $_SESSION[$class->loginAdminSession]['pass'] = $result['data']['password']; 
//        $_SESSION[$class->loginAdminSession]['email'] = $result['data']['email'];  
//        $_SESSION[$class->loginAdminSession]['photofile'] = $result['data']['photofile']; 
//         
//       /* echo $class->loginAdminSession.'<br>';
//        print_r($_SESSION);
//        die;*/
//        
//        header('location: /admin/list');
//        die;
//    }
//        
//}
    
//cek hak akses mana yg bisa direfer
$actionTarget = 'member.php';
 
if (isset ($_SESSION[$class->loginAdminSession])){
    // ntah kenapa beberapa subdomain selalu diarahin ke index dulu, akhirnya pd ke logout
    header('location: /admin/list');
    die;
    //$class->setLog($_SERVER['HTTP_REFERER'],true);
    //$class->setLog("in",true);
	//unset($_SESSION[$class->loginAdminSession]);
}

$loginId = '';
$loginPassword = '';
$token = '';

// ini lupa untuk apa
/*if (isset($_GET)){
    
    if (!empty($_GET['username']))
        $loginId = $_GET['username'];
        
    if (!empty($_GET['token'])){
        $loginPassword = $class->generateStrongPassword();
        $token = $_GET['token'];
    }    
}   
    
$_POST['loginID'] = $loginId;
$_POST['loginPassword'] = $loginPassword;
$_POST['token'] = $token;*/

//$security->updateAvailableSecurityObject($security->oDbCon,3); 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1"> 
<link rel=apple-touch-icon sizes=180x180 href="../include/img/ico/apple-touch-icon.png">
<link rel=icon type=image/png sizes=32x32 href="../include/img/ico/favicon-32x32.png">
<link rel=icon type=image/png sizes=16x16 href="../include/img/ico/favicon-16x16.png">
    
<title><?php echo  $class->loadSetting('companyName');  ?></title>  

<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>font-awesome-5.15.min.css">  
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>jquery-ui.min.css" />    
<link rel="stylesheet" href="<?php echo $class->adminCssPath; ?>bootstrap.css"/>
<link rel="stylesheet" href="<?php echo $class->adminCssPath; ?>bootstrapValidator.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath.ADMIN_CSS_VERSION; ?>">  

<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-3.3.1.min.js"></script>   
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-ui.min.js" charset="utf-8"></script> 
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>bootstrapValidator.js"></script>
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>php-variables-1.2.min.js"></script>
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>imprint.min.js"></script> 
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>login.min.js"></script>
	 
<?php if(DOMAIN_NAME == 'pstn.program-stok.com') { ?>
    <script type="text/javascript" src="//laz-g-cdn.alicdn.com/sj/securesdk/0.0.3/securesdk_lzd_v1.js" id="J_secure_sdk_v2" data-appkey="110359"></script>
<?php } ?>
</head>  
       
<?php
$profileImg = $class->loadSetting('companyLogo');
$bgImage = $class->loadSetting('adminBackgroundImage');

$avatarImg = (!empty($profileImg)) ? 'style="background-image:url(\'../phpthumb/phpThumb.php?src='.PHPTHUMB_URL_PATH .'setting/companyLogo/'.$profileImg.'&far=C&f=png&hash='.getPHPThumbHash($profileImg).'\')"' : '' ;
$bgImage = (empty($bgImage)) ? '../include/img/admin-bg.jpg' : '../phpthumb/phpThumb.php?src='.PHPTHUMB_URL_PATH .'setting/adminBackgroundImage/'.$bgImage.'&far=C&hash='.getPHPThumbHash($bgImage) ;
 
?>
    
<body style="background-color:#dedede; background-size:cover; background-repeat: no-repeat;  background-position: center;" background="<?php echo $bgImage; ?>"> 
<div id="body-login"> 
    <div class="login-panel-background">
        <div class="avatar"><div class="img-panel" <?php echo $avatarImg; ?>></div></div>  
        <div style="text-align:center; line-height:2em; margin-bottom:1em"><?php echo strtoupper(DOMAIN_NAME); ?></div> 
        <form id="defaultForm" method="post" class="form-horizontal" style="overflow:hidden" action="ajax-login.php">
            <?php echo $class->inputHidden('action'); ?>   
            <?php echo $class->inputHidden('df'); ?>   
            <div class="notification-msg" style="text-align:center; margin:auto; margin-top:0.5em; margin-bottom:0.5em"></div>  
            <div class="login-slide-panel div-table" style="width:640px;  margin-top:15px; position:relative"> 
                <div class="div-table-row">
                    <div class="div-table-col" style="width:50%;"> 
                       <div class="div-table"  style="width:100%;">
                             <div class="div-table-row form-group"> 
                                <div class="div-table-col-5" style="padding:0" >
                                    <div class="col-lg-12" style="padding:0"> 
                                          <?php echo $class->inputText('loginID', array('etc'=> 'placeholder="'.$class->lang['username'].'"') );   ?> 
                                    </div>
                                </div> 
                            </div> 
                             <div class="div-table-row form-group"> 
                                <div class="div-table-col-5" style="padding:0; padding-top:10px" >
                                    <div class="col-lg-12" style="padding:0" > 
                                          <?php echo $class->inputPassword('loginPassword', array('etc'=> 'placeholder="'.$class->lang['password'].'"') );   ?>  
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </div>
                    <div class="div-table-col">
                        <div class="div-table" style="width:100%;">
                            <div class="div-table-row form-group"> 
                                <div class="div-table-col-5" >
                                    <div class="col-lg-12" style="padding:0"> 
                                        <?php echo $class->inputText('authcode', array('etc'=>'style="text-align:center"  tabindex="-1" placeholder="'.ucwords($class->lang['authenticationCode']).'"')); ?>
                                        <div class="icon-back"><i class="fas fa-chevron-left"></i></div>
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </div>
                </div> 
            </div> 
            <div style="padding-top:10px; width: 320px"><?php echo $class->inputSubmit('btnLogin', $class->lang['login'], array('etc' => 'style="width:100%"') ); ?></div>
        </form>  
        <div style="clear:both;"></div>
    </div>
</div> 
  
</body>
</html>