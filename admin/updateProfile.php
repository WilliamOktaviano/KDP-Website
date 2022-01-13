<?php
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('Employee.class.php');
$employee = createObjAndAddToCol(new Employee());

$obj= $employee; 
	 
$id = base64_decode($_SESSION[$class->loginAdminSession]['id']);
$username = $_SESSION[$class->loginAdminSession]['username'];
$password = $_POST['currentPassword'];

	if (isset($_POST) && !empty($_POST['type'])) {
		$isAvailable = true;
		if ( $_POST['type'] == 'check' ){
			if ( $_POST['fieldtype'] == 'checkPassword' ){  
				$isAvailable  = $obj->checkPassword($id,$username,$password);
			}
			echo json_encode(array(
				'valid' => $isAvailable,
			)); 
			die;
		}
	}
	
	if (isset($_POST) && !empty($_POST['action'])) {
		
		foreach ($_POST as $k => $v) { 
			if (!is_array($v))
				 $v = trim($v);  
			
			$arr[$k] = $v;     
		}
		
		$arrReturn = array(); 
		
		if ( $_POST['action'] == 'edit' ){ 
			 
			$rs = $obj->getDataRowById($id ); 
			$arr['code'] = $rs[0]['code'];
			$arr['updateProfile'] = 1;
			$arr['modifiedBy'] = $id;
			 
			if($obj->checkPassword($id,$username,$password))
				$arrReturn = $obj->editData($arr);
			else
				$class->addErrorList($arrReturn,false,$class->errorMsg[302]);
								
		}
		
		echo json_encode($arrReturn);  
		die;
	}
	
?>