<?php 
require_once '../_config.php';  
require_once '../_include-v2.php';  

$security->removeCookiesLog();
setcookie(DOMAIN_COOKIES, null);  
 
if (isset($_COOKIE[DOMAIN_COOKIES])) unset($_COOKIE[DOMAIN_COOKIES]);    
if (isset ($_SESSION)) session_unset();   


header('location: /admin');
exit;
?>