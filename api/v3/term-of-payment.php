<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TermOfPayment.class.php';

function getNewObj(){
    return new TermOfPayment();
}

$OBJ = getNewObj();  
 
$API_FIELDS = array_merge($API_FIELDS,array(
    'code' =>   array('paramName' => 'code'),
    'name' => array('paramName' => 'name'), 
    'duedays' => array('paramName' => 'duedays'), 
));

require_once '_process.php';

?>