<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Gallery.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';

function getNewObj(){
    return new Gallery();
}

$OBJ = getNewObj();

$customer = new Customer();

$fileDetail = array( 
    'pkey' => array('paramName' => 'key'),  
    'file' => array('paramName' => 'file_url',  'return' => array('format' => 'file', 'paramName' => 'file', 'path' => 'gallery/'))
);

$API_FIELDS = array_merge($API_FIELDS, array(
    'code' => array('paramName' => 'code'),
    'name' => array('paramName' => 'name', 'mandatory' => true), 
    'customerkey' => array('paramName' => 'customer', 'ref' => array('obj' => $customer), 'return' => array('paramName' => 'customername')), 
    'statuskey'  =>  array('paramName' => 'status_key'), 
    'trdesc' => array('paramName' => 'description'),
    'file_detail' =>  array('paramName' => 'file_detail', 'updatable' => false, 'detail' =>  $fileDetail),           
));

require_once '_process.php';

?>