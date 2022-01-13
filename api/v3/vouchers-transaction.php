<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/VoucherTransaction.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';

function getNewObj(){
    return new VoucherTransaction();
}

$OBJ = getNewObj();
$voucher = new Voucher();
$customer = new Customer();
 
$API_FIELDS = array_merge($API_FIELDS, array(
    'code' => array('paramName' => 'code'),
 	'voucherlabel' => array('paramName' => 'name', 'updatable' => false ),  
 	'trdate' => array('paramName' => 'date',  'return' => array('format' => 'mktime' )), 
    'expdate' => array('paramName' => 'exp_date','updatable' => false,   'return' => array('format' => 'mktime')),
    'customerkey' => array('paramName' => 'customer_id', 'mandatory' => true, 'search' => array('field' => $OBJ->tableCustomer.'.code'), 'ref' => array('obj' => $customer, 'field' => "code"), 'return' => array('paramName' => 'customercode')),
    'customername' => array('paramName' => 'customer_name',  'updatable' => false, 'return' => array('paramName' => 'customername')),
 	'statuskey' => array('paramName' => 'status_key', 'updatable' => false), 
 	'vouchershortdesc' => array('paramName' => 'voucher_short_desc', 'updatable' => false ), 
 	'voucherdesc' => array('paramName' => 'voucher_desc', 'updatable' => false ), 
 	'value' => array('paramName' => 'value', 'updatable' => false ), 
 	'discounttype' => array('paramName' => 'discount_type', 'updatable' => false ), 
 	'minamount' => array('paramName' => 'min_amount_transaction', 'updatable' => false ), 
 	'maxdiscount' => array('paramName' => 'max_discount', 'updatable' => false ),  
 	 
 ));

require_once '_process.php';

?>