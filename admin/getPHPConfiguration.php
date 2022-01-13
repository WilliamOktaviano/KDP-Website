<?php 
// lebih utk template frontend 
// admin sudah tdk ad yg pake

require_once '../_config.php'; 
require_once '../_include-v2.php'; 

$arrConfig = array();
$arrConfig["uploadTempDoc"] = $class->uploadTempDoc;
$arrConfig["uploadTempDocShort"] = $class->uploadTempDocShort;
$arrConfig["uploadTempURL"] = $class->uploadTempURL;
$arrConfig["defaultDocUploadPath"] = $class->defaultDocUploadPath;
$arrConfig["adminTotalRowsPerPage"] = $class->adminTotalRowsPerPage;
 
echo json_encode($arrConfig); 
die; 
  
?>