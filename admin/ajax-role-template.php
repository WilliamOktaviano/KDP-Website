<?php 
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj = $roleTemplate;  
 
if (isset($_GET) && !empty($_GET['action'])) {
			switch ( $_GET['action']){  
                case 'searchData' :   
 
                if (isset($_GET) && !empty($_GET['pkey'])){
                    
                    $importKey = $_GET['pkey'];
                    
                    if(isset($_GET['importType']) && ($_GET['importType'] == 2) ) 
                       // dr employee
                       $rs = $obj->getEmployeeSecurityTemplate($importKey);
                    else 
                      // default dr role template
                       $rs = $obj->getSecurityRole($importKey);
                        
                }
	                

                echo json_encode($rs); 
                break;
                    
            }
}

die;
  
?>