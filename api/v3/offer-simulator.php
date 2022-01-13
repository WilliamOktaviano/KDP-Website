<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/SalesOrder.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/OfferSimulator.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Employee.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Item.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemUnit.class.php';

function getNewObj(){
    return new OfferSimulator();
}

$OBJ = getNewObj();
$warehouse = new Warehouse();
$customer = new Customer();
$employee = new Employee();
$item = new Item();
$itemUnit = new ItemUnit();

$imageUrl = array( 
    'pkey' => array('paramName' => 'key'),   
    'url' => array('paramName' => 'url'),
);

$detail = array(
    'pkey' => array('paramName' => 'key'),
    'itemkey' => array('paramName' => 'item_id', 'mandatory' => true, 'ref' => array('obj' => $item, 'field' => "code"), 'return' => array('paramName' => 'itemcode')), 
    'itemname' => array('paramName' => 'item_name', 'updatable' => false, 'return' => array('paramName' => 'itemname')), 
    'qty' =>  array('paramName' => 'qty', 'mandatory' => true),
	'unitkey' => array('paramName' => 'unit_id', 'mandatory' => true, 'ref' => array('obj' => $itemUnit, 'field' => "code"), 'return' => array('paramName' => 'unitcode')),
    'unitname' => array('paramName' => 'unit_name', 'updatable' => false, 'return' => array('paramName' => 'unitname')),  
    'priceinunit' => array('paramName' => 'price_in_unit', 'mandatory' => true),
    'total' => array('paramName' => 'total','updatable' => false),
	'image_url' => array('paramName' => 'image_url', 'updatable' => false, 'detail' =>  $imageUrl), // kalo jenis image harus diconvert ke token, dan image harus diupload ke _temp
);

 
$API_FIELDS = array_merge($API_FIELDS,array(
    'code' =>   array('paramName' => 'code'), 
    'trdate' => array('paramName' => 'date', 'mandatory' => true, 'return' => array('format' => 'mktime')),
    'warehousekey' => array('paramName' => 'warehouse_id', 'mandatory' => true, 'ref' => array('obj' => $warehouse, 'field' => 'code'), 'return' => array('paramName' => 'warehousecode') ),
    'warehousename' => array('paramName' => 'warehouse_name', 'updatable' => false, 'return' => array('paramName' => 'warehousename')),
    'customerkey' => array('paramName' => 'customer_id', 'mandatory' => true, 'search' => array('field' => $OBJ->tableCustomer.'.code'), 'ref' => array('obj' => $customer, 'field' => "code"), 'return' => array('paramName' => 'customercode')),
    'customername' => array('paramName' => 'customer_name',  'updatable' => false, 'return' => array('paramName' => 'customername')),
    'description' => array('paramName' => 'description'),
    'total' => array('paramName' => 'total','updatable' => false),
    'name' => array('paramName' => 'name'),     
    'detail' => array('paramName' => 'detail', 'dataset' => $OBJ->arrDataDetail , 'tableName' => $OBJ->tableNameDetail, 'detail' =>  $detail)
));

require_once '_process.php';

?>