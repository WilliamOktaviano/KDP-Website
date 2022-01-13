<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass(array('Category.class.php','ArticleCategory.class.php'));
$articleCategory = createObjAndAddToCol(new ArticleCategory());

$obj = $articleCategory;    

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');   

include 'ajax-general.php';
 
die;
  
?>