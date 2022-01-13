<?php 
// personalized files 

$fileName = str_replace('/','',$_SERVER['PHP_SELF']);
$docPersonalizedFile = PERSONALIZED_DOC_PATH.$fileName;   
$urlPersonalizedFile = PERSONALIZED_URL_PATH.$fileName;   

//$file = (is_file($docPersonalizedFile)) ? $docPersonalizedFile : $fileName; 
  
if (is_file($docPersonalizedFile)){
    require_once $docPersonalizedFile;
    die;
}
 
?>