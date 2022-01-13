<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemUnit.class.php';  

function getNewObj(){ return  new ItemUnit(); } 
$OBJ = getNewObj();

// INPUT QUERY    
$API_FIELDS = array_merge($API_FIELDS,array(
               'name'  =>  array('paramName' => 'name'),  
               'statuskey'  =>  array('paramName' => 'status', 'ref' => array('tableName' => $OBJ->tableStatus, 'field' => 'status') , 'return' => array('isReturn' => false)), 
            ));
       
require_once '_process.php';
     
?>