<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Shipment.class.php';

function getNewObj(){
    return new Shipment();
}

$OBJ = getNewObj();

$detail = array(
    'pkey' => array('paramName' => 'key'),
    'servicecode' => array('paramName' => 'service_code'), 
    'servicename' => array('paramName' => 'service_name') 
);

$API_FIELDS = array_merge($API_FIELDS, array(
    'code' => array('paramName' => 'code'),
    'name' => array('paramName' => 'name', 'mandatory' => true),
    'insurance' => array('paramName' => 'insurance'),
    'adminfee' => array('paramName' => 'admin_fee'),
    'extcost' => array('paramName' => 'ext_cost'), 
    'maxweight' => array('paramName' => 'max_weight'),
    'minweight' => array('paramName' => 'min_weight'),
    'statuskey' => array('paramName' => 'status_key'),
	'services' => array('paramName' => 'services', 'dataset' => $OBJ->arrShipmentService , 'tableName' => $OBJ->tableShipmentService, 'detail' =>  $detail)
));


require_once '_process.php';

?>