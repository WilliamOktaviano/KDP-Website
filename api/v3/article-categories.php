<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Category.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ArticleCategory.class.php'; 

function getNewObj(){
    return new ArticleCategory();
 }
 
$OBJ = getNewObj();


$imageUrl = array( 
    'pkey' => array('paramName' => 'key'),   
    'url' => array('paramName' => 'url'),
);

$API_FIELDS = array_merge($API_FIELDS,array(
    'code' => array('paramName' => 'code'), 
    'name' => array('paramName' => 'name', 'mandatory' => true),
    'shortdescription' =>  array('paramName' => 'short_description'),
    'description' => array('paramName' => 'description'),
    'parentkey' => array('paramName' => 'parent_key'),
    'statuskey'  =>  array('paramName' => 'status_key'), 
    'orderlist' => array('paramName' => 'order_list'),
    'isleaf' => array('paramName' => 'is_leaf'),
    'image_url' => array('paramName' => 'image_url',  'updatable' => false, 'detail' =>  $imageUrl, 'return' => array('format' => 'file', 'paramName' => 'file', 'path' => 'item-category/')),
));

$IMAGE_SET = array(); // image set bisa lebih dr 1 jenis
array_push($IMAGE_SET,array(
                                'paramName' => 'image_url',
                                'paramImageList' => 'item-image-uploader',
                                'paramToken' => 'token-item-image-uploader',
                            ));

require_once '_process.php';
?>