<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass(array('Tag.class.php'));
$tag = new Tag();

if (!isset($_POST)) die; 
 
if (isset($_POST) && !empty($_POST['action'])) {
			switch ( $_POST['action']){  
                case 'update' :  
                   
                    $hidTagDetailKey = $_POST['hidTagDetailKey'];
                    $arrName = $_POST['name'];
                      
                    for($i=0;$i<count($hidTagDetailKey);$i++){
                          
                        if (!isset($arrName[$i]) || empty($arrName[$i]))
                            continue; 
                        
                        $arrParam = array();
                        
                        $arrParam['hidId'] = $hidTagDetailKey[$i];
                        $arrParam['name'] = $arrName[$i];
                         
                        $tag->editData($arrParam); 
                    }
                    
                    //echo json_encode($rsData); 
                    break; 
                     
            }
} 
  
?>