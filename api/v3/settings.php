<?php
require_once '../../_config.php';  
require_once '_include.php';
 

require_once '_global.php'; 

if(!in_array($ACTION,array('GET'))) endForRequestMethodError();   
    
$RETURN_VALUE = array();

switch($ACTION){ 
        
    case 'GET':
            $code = $_GET['code'];
			$ARR_RETURN_VALUE = array();
			if(!empty($code)){ 
				array_push($ARR_RETURN_VALUE, $class->loadSetting($code)); 
			}
           
        
            $RETURN_VALUE['response_code'] =  (!empty($ARR_RETURN_VALUE)) ? 200 : 409; 
            $RETURN_VALUE['data'] = $ARR_RETURN_VALUE;

            break;
        
}


http_response_code($RETURN_VALUE['response_code'] );
echo json_encode($RETURN_VALUE); 
 
die;
?>