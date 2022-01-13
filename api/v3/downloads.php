<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Download.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/DownloadCategory.class.php';

function getNewObj(){
    return new Download();
}

$OBJ = getNewObj();

$downloadCategory = new DownloadCategory();

$fileDetail = array( 
    'pkey' => array('paramName' => 'key'),  
    'file' => array('paramName' => 'file_url',  'return' => array('format' => 'file', 'paramName' => 'file', 'path' => 'download/'))
);

$API_FIELDS = array_merge($API_FIELDS, array(
    'code' => array('paramName' => 'code'),
    'name' => array('paramName' => 'name', 'mandatory' => true),
    'useexternallink' => array('paramName' => 'use_external_link'), 
    'externallink' => array('paramName' => 'external_link'), 
    'categorykey' => array('paramName' => 'category', 'ref' => array('obj' => $downloadCategory), 'return' => array('paramName' => 'categoryname')), 
    'shortdesc' => array('paramName' => 'short_description'),
    'statuskey'  =>  array('paramName' => 'status_key'), 
    'image_url' => array('paramName' => 'image_url',  'updatable' => false,  'return' => array('format' => 'file', 'paramName' => 'file', 'path' => 'download-image/')),
    'file_detail' =>  array('paramName' => 'file_detail', 'updatable' => false, 'detail' =>  $fileDetail),
              
));

require_once '_process.php';

?>