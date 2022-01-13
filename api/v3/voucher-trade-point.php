<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Voucher.class.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/VoucherTradePoint.class.php';

function getNewObj(){
    return new VoucherTradePoint();
}

$OBJ = getNewObj();
$warehouse = new Warehouse();
$customer = new Customer(); 
$voucher = new Voucher(); 
$voucherTransaction = new VoucherTransaction();
  
$detail = array(
    'pkey' => array('paramName' => 'key'),
    'voucherkey' => array('paramName' => 'master_voucher_id', 'mandatory' => true, 'ref' => array('obj' => $voucher, 'field' => "code"), 'return' => array('paramName' => 'vouchercode')), 
    'vouchername' => array('paramName' => 'master_voucher_name', 'updatable' => false, 'return' => array('paramName' => 'vouchername')), 
    'qty' =>  array('paramName' => 'qty', 'mandatory' => true),
    'pointneeded' => array('paramName' => 'point_needed') 
);

$API_FIELDS = array_merge($API_FIELDS,array(
    'code' =>   array('paramName' => 'code'), 
	'trdate' => array('paramName' => 'date', 'mandatory' => true, 'return' => array('format' => 'mktime')),
    'warehousekey' => array('paramName' => 'warehouse_id', 'mandatory' => true, 'ref' => array('obj' => $warehouse, 'field' => 'code'), 'return' => array('paramName' => 'warehousecode') ),
    'warehousename' => array('paramName' => 'warehouse_name', 'updatable' => false, 'return' => array('paramName' => 'warehousename')),
    'customerkey' => array('paramName' => 'customer_id', 'mandatory' => true, 'search' => array('field' => $OBJ->tableCustomer.'.code'), 'ref' => array('obj' => $customer, 'field' => "code"), 'return' => array('paramName' => 'customercode')),
    'customername' => array('paramName' => 'customer_name',  'updatable' => false, 'return' => array('paramName' => 'customername')),
    'trdesc' => array('paramName' => 'description'),
	'changestatusto' => array('paramName' => 'change_status_to', 'forceParam' => true, 'return' => array('isReturn' => false)),
    'detail' => array('paramName' => 'detail', 'dataset' => $OBJ->arrDataDetail , 'tableName' => $OBJ->tableNameDetail, 'detail' =>  $detail), 
));

require_once '_process.php';

?>