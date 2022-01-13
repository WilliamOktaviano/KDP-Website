<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Banner.class.php';

function getNewObj(){
    return new Banner();
}

$OBJ = getNewObj();

$imageUrl = array( 
    'pkey' => array('paramName' => 'key'),   
    'url' => array('paramName' => 'url'),
);

$API_FIELDS = array_merge($API_FIELDS, array(
    'code' => array('paramName' => 'code'),
    'name' => array('paramName' => 'name', 'mandatory' => true),
    'poskey' => array('paramName' => 'position', 'mandatory' => true,'search' => array('field' => $OBJ->tableNamePosition.'.name'), 'ref' => array('tableName' => $OBJ->tableNamePosition), 'return' => array('paramName' => 'positionname')),
    'statuskey'  =>  array('paramName' => 'status_key'),
    'trdesc' => array('paramName' => 'description'),
    'url' => array('paramName' => 'url'),
    'image_url' => array('paramName' => 'image_url',  'updatable' => false,  'return' => array('format' => 'file', 'paramName' => 'file', 'path' => 'banner/'))
));

$IMAGE_SET = array(); // image set bisa lebih dr 1 jenis
array_push($IMAGE_SET,array(
    'paramName' => 'image_url',
    'paramImageList' => 'item-image-uploader',
    'paramToken' => 'token-item-image-uploader',
));

require_once '_process.php';
?>