<?php
require_once '../_config.php'; 
require_once '../_include-v2.php'; 
includeClass('Setting.class.php');
$setting = createObjAndAddToCol(new Setting());


	$obj= $setting; 
	
	if (empty($_POST['action']))
		die;
		
	foreach ($_POST as $k => $v) {
		
		if (!is_array($v))
			 $v = trim($v);  
		
		$arr[$k] = $v;    
	}  
	 
	
	$arrReturn = array(); 
	 
	if(!$security->isAdminLogin($obj->securityObject,11,false)) die;
	 $arrReturn = $obj->editData($arr);
	  
	echo json_encode($arrReturn);  
	die; 
	
?>