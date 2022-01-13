<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/DiscountScheme.class.php';

$discountScheme = new DiscountScheme();
$OBJ = $discountScheme; 

require_once '_global.php';

if($ACTION != 'GET') endForRequestMethodError();

$hasSuccessValue = false;
$arrFailed = array();
$ARR_RETURN_VALUE = array();

$imageUrl = array( 
    'pkey' => array('paramName' => 'key'),   
    'url' => array('paramName' => 'url'),
);

$API_FIELDS = array(
    'pkey' => array('paramName' => 'pkey'), // ini harus itemkey, agar imagenya ketarik
    'code' => array('paramName' => 'code'),
    'name' => array('paramName' => 'name'),
    'sellingprice' => array('paramName' => 'selling_price'), 
    'discountedprice' => array('paramName' => 'discounted_price'), 
    'discounttype' => array('paramName' => 'discount_type'), 
    'discount' => array('paramName' => 'discount'),
	'image_url' => array('paramName' => 'image_url', 'updatable' => false, 'detail' =>  $imageUrl), // kalo jenis image harus diconvert ke token, dan image harus diupload ke _temp
);


$rs = $discountScheme->getAllDiscountedItem();
$ARR_RETURN_VALUE = $OBJ->compileAPIField($rs,$API_FIELDS,true);

$RETURN_VALUE['response_code'] = 200; //($hasSuccessValue) ? 200 : 409;
$RETURN_VALUE['success_rows'] = count($ARR_RETURN_VALUE);
$RETURN_VALUE['success_data'] = $ARR_RETURN_VALUE;
$RETURN_VALUE['failed_rows'] = count($arrFailed);
$RETURN_VALUE['failed_data'] = $arrFailed;

http_response_code($RETURN_VALUE['response_code']); 
echo json_encode($RETURN_VALUE);
die;
?>