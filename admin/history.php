<?php   
require_once '../_config.php'; 
require_once '../_include.php'; 

if (empty($_SESSION[$class->loginAdminSession])){ 
	header("location: /admin"); 
}
  

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
   
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>jquery-ui.min.css" />    
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>bootstrap.css"/> 
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>jquery-ui-timepicker-addon.css"/>  
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath.ADMIN_CSS_VERSION; ?>">  
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>responsive.css" />  

     
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-3.3.1.min.js"></script>   
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-ui.min.js" charset="utf-8"></script> 
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery.formatCurrency-1.4.0.min.js" ></script>    
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>php-variables-1.2.min.js"></script>  
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>main-3.81.min.js"></script> 
    
<title><?php echo  $class->loadSetting('companyName'); ?></title> 

<script type="text/javascript">   

jQuery(document).ready(function(){  
    $("input").prop('readonly', true);
    $("select").prop('disabled', true);
});

</script>    
</head> 
<body> 
<div class="history-page">
<?php  
    $IS_HISTORY = true;
    include 'employeeCategoryForm.php'; 
?>
</div>
</body> 
</html>