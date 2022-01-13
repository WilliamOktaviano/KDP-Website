<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/SalesOrder.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TermOfPayment.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Shipment.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Employee.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/City.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Voucher.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Item.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/PaymentMethod.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemUnit.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/VoucherTransaction.class.php';

function getNewObj(){
    return new SalesOrder();
}

$OBJ = getNewObj();
$warehouse = new Warehouse();
$customer = new Customer();
$termOfPayment = new TermOfPayment();
$paymentMethod = new PaymentMethod();
$shipment = new Shipment();
$employee = new Employee();  
$city = new City();
$voucher = new Voucher();
$item = new Item();
$itemUnit = new ItemUnit();
$voucherTransaction = new VoucherTransaction();

$imageUrl = array( 
    'pkey' => array('paramName' => 'key'),   
    'url' => array('paramName' => 'url'),
);

$voucherDetail = array(
    'pkey' => array('paramName' => 'key'),
    'amount' => array('paramName' => 'amount', 'mandatory' => true), 
    'voucherkey' => array('paramName' => 'voucher_id',  'ref' => array('obj' => $voucherTransaction, 'field' => "code"),  'return' => array('paramName' => 'code'))
);

$paymentDetail = array(
    'pkey' => array('paramName' => 'key'),
    'amount' => array('paramName' => 'amount', 'mandatory' => true), 
    'paymentkey' => array('paramName' => 'payment_method_id', 'ref' => array('obj' => $paymentMethod, 'field' => "code"),  'mandatory' => true, 'return' => array('paramName' => 'paymentmethodcode')),  
    'paymentname' => array('paramName' => 'payment_method_name', 'updatable' => false,   'return' => array('paramName' => 'paymentmethodname')) 
);

$detail = array(
    'pkey' => array('paramName' => 'key'),
    'itemkey' => array('paramName' => 'item_id', 'mandatory' => true, 'ref' => array('obj' => $item, 'field' => "code"), 'return' => array('paramName' => 'itemcode')), 
    'itemname' => array('paramName' => 'item_name', 'updatable' => false, 'return' => array('paramName' => 'itemname')), 
    //'item_main_image_url' => array('paramName' => 'item_main_image_url', 'updatable' => false, 'return' => array('paramName' => 'itemmainimageurl')), 
    'qty' =>  array('paramName' => 'qty', 'mandatory' => true),
    'unitkey' => array('paramName' => 'unit_id', 'mandatory' => true, 'ref' => array('obj' => $itemUnit, 'field' => "code"), 'return' => array('paramName' => 'unitcode')),
    'unitname' => array('paramName' => 'unit_name', 'updatable' => false, 'return' => array('paramName' => 'unitname')), 
	'priceinunit' => array('paramName' => 'price_in_unit', 'mandatory' => true),
    'unitconvmultiplier' => array('paramName' => 'unit_conv_multiplier', 'mandatory' => true),
    'deliveredqtyinbaseunit' =>  array('paramName' => 'delivered_qty_in_base_unit', 'mandatory' => true),
    'qtyinbaseunit' =>  array('paramName' => 'qty_in_base_unit', 'mandatory' => true),
    'priceinbaseunit' =>  array('paramName' => 'price_in_base_unit', 'mandatory' => true),
    'discounttype' =>  array('paramName' => 'discount_type'),
    'discount' =>  array('paramName' => 'discount'),
    'costinbaseunit' =>  array('paramName' => 'cost_in_base_unit', 'mandatory' => true),
    'itemtype' =>  array('paramName' => 'item_type'),
	'image_url' => array('paramName' => 'image_url', 'updatable' => false, 'detail' =>  $imageUrl), // kalo jenis image harus diconvert ke token, dan image harus diupload ke _temp
);

$API_FIELDS = array_merge($API_FIELDS,array(
    'code' =>   array('paramName' => 'code'),
    'marketplaceorderid' => array('paramName' => 'marketplace_order_id'),
    'marketplaceinvoiceurl' => array('paramName' => 'marketplace_invoice_url'),
    'customcodekey' => array('paramName' => 'custom_code_key'),
    'trdate' => array('paramName' => 'date', 'mandatory' => true, 'return' => array('format' => 'mktime')),
    'warehousekey' => array('paramName' => 'warehouse_id', 'mandatory' => true, 'ref' => array('obj' => $warehouse, 'field' => 'code'), 'return' => array('paramName' => 'warehousecode') ),
    'warehousename' => array('paramName' => 'warehouse_name', 'updatable' => false, 'return' => array('paramName' => 'warehousename')),
    'customerkey' => array('paramName' => 'customer_id', 'mandatory' => true, 'search' => array('field' => $OBJ->tableCustomer.'.code'), 'ref' => array('obj' => $customer, 'field' => "code"), 'return' => array('paramName' => 'customercode')),
    'customername' => array('paramName' => 'customer_name',  'updatable' => false, 'return' => array('paramName' => 'customername')),
    'termofpaymentkey' => array('paramName' => 'term_of_payment_id', 'mandatory' => true, 'ref' => array('obj' => $termOfPayment, 'field' => "code"), 'return' => array('paramName' => 'termofpaymentcode') ),
    'termofpayment' => array('paramName' => 'term_of_payment', 'updatable' => false,  'ref' => array('obj' => $termOfPayment), 'return' => array('paramName' => 'termofpaymentname')),
    'trdesc' => array('paramName' => 'description'),
    'finaldiscounttype' => array('paramName' => 'final_discount_type'),
    'finaldiscount' => array('paramName' => 'final_discount'),
    'ispriceincludetax' => array('paramName' => 'price_include_tax'),
    'taxpercentage' => array('paramName' => 'tax_percentage'),
    'shipmentkey' => array('paramName' => 'shipment', 'ref' => array('obj' => $shipment), 'return' => array('paramName' => 'shipmentname')),
    'shipmentservicekey' => array('paramName' => 'shipment_service_key'),
    'shipmentfee' => array('paramName' => 'shipping_cost'),
    'useinsurance' => array('paramName' => 'use_insurance'),
    'etccost' => array('paramName' => 'etc_cost'),
    'isfulldeliver' => array('paramName' => 'is_full_deliver'),
    'saleskey' => array('paramName' => 'sales', 'ref' => array('obj' => $employee, 'field' => "username"), 'return' => array('paramName' => 'salesusername')),
    'recipientname' => array('paramName' => 'recipient_name'),
    'recipientphone' => array('paramName' => 'recipient_phone'),
    'recipientemail' => array('paramName' => 'recipient_email'),
    'recipientaddress' => array('paramName' => 'recipient_address'),
    'recipientcitykey' => array('paramName' => 'recipient_city', 'ref' => array('obj' => $city), 'return' => array('paramName' => 'recipientcityname')),
    'recipientmapaddress' => array('paramName' => 'recipient_map_address'),
    'isdropship' => array('paramName' => 'is_dropship'),
    'dropshipername' => array('paramName' => 'dropshiper_name'),
    'dropshiperphone' => array('paramName' => 'dropshiper_phone'),
    'dropshiperaddress' => array('paramName' => 'dropshiper_address'), 
    'detail' => array('paramName' => 'detail', 'dataset' => $OBJ->arrDataDetail , 'tableName' => $OBJ->tableNameDetail, 'detail' =>  $detail),
	'payment_method_detail' => array('paramName' => 'payment_method_detail', 'dataset' => $OBJ->arrPaymentDetail, 'tableName' => $OBJ->tablePayment, 'detail' =>  $paymentDetail),
	'voucher_detail' => array('paramName' => 'voucher_detail', 'dataset' => $OBJ->arrVoucherDetail , 'tableName' => $OBJ->tableVoucherDetail, 'detail' => $voucherDetail )
));

require_once '_process.php';

?>