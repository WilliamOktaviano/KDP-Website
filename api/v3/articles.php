<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Article.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ArticleCategory.class.php';

function getNewObj(){
    return new Article();
}

$OBJ = getNewObj();

$articleCategory = new ArticleCategory();

$imageUrl = array( 
    'pkey' => array('paramName' => 'key'),   
    'url' => array('paramName' => 'url'),
);

$categoryDetail = array(
    'pkey' => array('paramName' => 'key'),
    'categoryname' => array('paramName' => 'name', 'mandatory' => true, 'ref' => array('obj' => $articleCategory)),
);

$API_FIELDS = array_merge($API_FIELDS, array(
    'code' => array('paramName' => 'code'),
    'title' => array('paramName' => 'title', 'mandatory' => true),
    'category_detail' => array('paramName' => 'category_detail', 'updatable' => false, 'dataset' => $OBJ->arrDataDetail, 'tableName' => $OBJ->tableNameDetail, 'detail' =>  $categoryDetail),
    'shortdesc' => array('paramName' => 'short_description'),
    'publishdate' => array('paramName' => "publish_date", 'mandatory' => true, 'return' => array('format' => 'mktime')),
    'detail' => array('paramName' => 'detail'),
    'featured' => array('paramName' => 'is_featured'),
    'statuskey'  =>  array('paramName' => 'status_key'), 
    'image_url' => array('paramName' => 'image_url',  'updatable' => false, 'detail' =>  $imageUrl, 'return' => array('format' => 'file', 'paramName' => 'file', 'path' => 'article/'))
));

$IMAGE_SET = array(); // image set bisa lebih dr 1 jenis
array_push($IMAGE_SET,array(
    'paramName' => 'image_url',
    'paramImageList' => 'item-image-uploader',
    'paramToken' => 'token-item-image-uploader',
));

require_once '_process.php';

?>