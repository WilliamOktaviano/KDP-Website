<?php
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  

includeClass(array("Contact.class.php","Category.class.php","ContactCategory.class.php"));
$contact = new Contact();
$contactCategory = new ContactCategory();

	foreach ($_POST as $k => $v) {
		
		if (!is_array($v))
			 $v = trim($v);  
		
		$arr[$k] = $v;     
	}  

    // handling dr footer contact
    if(!empty($arr['hidQuickContact'])) {
        $arr['name'] = $arr['quickContactFrom']; 
        $arr['message'] = $arr['quickContactMessage']; 
        $arr['phone'] = $arr['quickContactPhone']; 
        $arr['email'] = $arr['quickContactEmail']; 
        $arr['subject'] = ''; 
    }
    
    
   
	$arrReturn = array(); 
	$arr['code'] = 'XXXXX';
	$arr['createdBy'] = 0;
	$arr['selStatus'] = 1;
	$arr['selCategory'] = (!isset($_POST['hidCategoryKey'])) ? $contactCategory->getDefaultData() : $_POST['hidCategoryKey'];
	$arrReturn = $contact->addData($arr);
 	  
	echo json_encode($arrReturn);  
	die; 
	
?>
