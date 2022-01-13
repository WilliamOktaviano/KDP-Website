<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Brand.class.php';

function getNewObj(){
    return new Brand();
}

$OBJ = getNewObj();

$API_FIELDS = array_merge($API_FIELDS, array(
    'code' => array('paramName' => 'code'),
    'name' => array('paramName' => 'name', 'mandatory' => true),
    'trdesc' => array('paramName' => 'description'),
    'statuskey'  =>  array('paramName' => 'status_key'), 
    'image_url' => array('paramName' => 'image_url',  'updatable' => false,  'return' => array('format' => 'file', 'paramName' => 'file', 'path' => 'page/')),
    'publish' => array('paramName' => 'is_publish', 'mandatory' => true)
));

require_once '_process.php';

?>