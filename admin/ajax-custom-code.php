<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php';  

$obj = $customCode;    

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');   

include 'ajax-general.php';

if (isset($_POST) && !empty($_POST['action'])) {
			switch ( $_POST['action']){ 
     
                case 'getRunningNumber' :  
                    if (!isset($_POST) || empty($_POST['pkey']) ){
                        $counter = 0;
                    } else{  
                        
                        switch ($_POST['resetTypeKey']) { 
                               
                                case '3': 
                                case '4':
                                    $trDate =  date('d / m / Y',strtotime($_POST['trDate'])); 
                                    break; 
                                
                                default : 
                                    $trDate =  $_POST['trDate']; 
                                    break;

                        } 

                        $warehousekey =  $_POST['warehousekey']; 
                        $counter = $customCode->getRunningNumber($_POST['pkey'],$_POST['resetTypeKey'],$trDate, $warehousekey);
                         
                    }
 
                    echo json_encode($counter);   
                    break;
                    
            }
}

die;
  
?>