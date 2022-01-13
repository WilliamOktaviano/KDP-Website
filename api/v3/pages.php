<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Page.class.php';

function getNewObj(){
    return new Page();
}

$OBJ = getNewObj();

$fileDetail = array( 
    'pkey' => array('paramName' => 'key'),  
    'file' => array('paramName' => 'file_url',  'return' => array('format' => 'file', 'paramName' => 'file', 'path' => 'page/'))
);

$API_FIELDS = array_merge($API_FIELDS, array(
    'code' => array('paramName' => 'code'),
    'pagename' => array('paramName' => 'page_name', 'mandatory' => true, 'search' => array('field' => $OBJ->tableName.'.pagename')),
    'title' => array('paramName' => 'title', 'mandatory' => true),
    'statuskey'  =>  array('paramName' => 'status_key'), 
    'detail' => array('paramName' => 'title'),
    'shortdesc' => array('paramName' => 'short_description'),
    'image_url' => array('paramName' => 'image_url',  'updatable' => false,  'return' => array('format' => 'file', 'paramName' => 'file', 'path' => 'page/')),
    'file_detail' =>  array('paramName' => 'file_detail', 'updatable' => false, 'detail' =>  $fileDetail),
              
));

require_once '_process.php';

?>