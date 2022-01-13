<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Voucher.class.php';

function getNewObj(){
    return new Voucher();
}

$OBJ = getNewObj();
 
$API_FIELDS = array_merge($API_FIELDS, array(
    'code' => array('paramName' => 'code'),
 	'name' => array('paramName' => 'name', 'mandatory' => true ),
 	'alias' => array('paramName' => 'alias' ), 
    'startdate' => array('paramName' => 'start_date',  'return' => array('format' => 'mktime')),
    'enddate' => array('paramName' => 'end_date',  'return' => array('format' => 'mktime')),
 	'value' => array('paramName' => 'value'),
 	'discounttype' => array('paramName' => 'discount_type'),
 	'minamount' => array('paramName' => 'min_transaction'),
 	'maxdiscount' => array('paramName' => 'max_discount'),
 	'pointneeded' => array('paramName' => 'point_needed'),
	'shortdesc' => array('paramName' => 'shortdesc'), 
	'trdesc' => array('paramName' => 'description'), 
 ));

require_once '_process.php';

?>