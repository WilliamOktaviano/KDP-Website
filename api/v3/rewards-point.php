<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/RewardsPoint.class.php';

function getNewObj(){
    return new RewardsPoint();
}

$OBJ = getNewObj();
 
$API_FIELDS = array_merge($API_FIELDS, array(
    'code' => array('paramName' => 'code'),
  	'trdate' => array('paramName' => 'date', 'mandatory' => true, 'return' => array('format' => 'mktime')),
 	'expdate' => array('paramName' => 'expdate',  'updatable' => false, 'return' => array('format' => 'mktime')),
   	'customerkey' => array('paramName' => 'customer_id', 'mandatory' => true, 'search' => array('field' => $OBJ->tableCustomer.'.code'), 'ref' => array('obj' => $customer, 'field' => "code"), 'return' => array('paramName' => 'customercode')),
    'customername' => array('paramName' => 'customer_name',  'updatable' => false, 'return' => array('paramName' => 'customername')),
	'warehousekey' => array('paramName' => 'warehouse_id', 'mandatory' => true, 'ref' => array('obj' => $warehouse, 'field' => 'code'), 'return' => array('paramName' => 'warehousecode') ),
    'warehousename' => array('paramName' => 'warehouse_name', 'updatable' => false, 'return' => array('paramName' => 'warehousename')), 
	'point' => array('paramName' => 'point', 'mandatory' => true ),
	'outstanding' => array('paramName' => 'outstanding', 'mandatory' => true , 'updatable' => false),
	'trdesc' => array('paramName' => 'description'), 
 ));

require_once '_process.php';

?>