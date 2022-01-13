<?php
require_once '../../_config.php';  
require_once '_include.php';


require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/MembershipSubscription.class.php';     
 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/SalesOrder.class.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TermOfPayment.class.php'; 
//require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Voucher.class.php'; 

function getNewObj(){ return  new MembershipSubscription(); }

$OBJ = getNewObj();
  
$customer = new Customer();
$termOfPayment = new TermOfPayment();  
//$voucher = new Voucher(); 

$detail = array(
    'pkey' => array('paramName' => 'key'),
    'itemkey' => array('paramName' => 'item_id', 'mandatory' => true, 'ref' => array('obj' => $item, 'field' => "code"), 'return' => array('paramName' => 'itemcode')), 
    'itemname' => array('paramName' => 'item_name', 'updatable' => false, 'return' => array('paramName' => 'itemname')), 
    'qty' =>  array('paramName' => 'qty', 'mandatory' => true),
    'priceinunit' => array('paramName' => 'price_in_unit', 'mandatory' => true),
 );

$API_FIELDS = array_merge($API_FIELDS,array(
    'code' =>   array('paramName' => 'code'),
    //'customcodekey' => array('paramName' => 'custom_code_key'),
    'trdate' => array('paramName' => 'date', 'mandatory' => true, 'return' => array('format' => 'mktime')),
    'customerkey' => array('paramName' => 'customer_id', 'mandatory' => true, 'search' => array('field' => $OBJ->tableCustomer.'.code'), 'ref' => array('obj' => $customer, 'field' => "code"), 'return' => array('paramName' => 'customercode')),
    'customername' => array('paramName' => 'customer_name',  'updatable' => false, 'return' => array('paramName' => 'customername')),
    'termofpaymentkey' => array('paramName' => 'term_of_payment_id', 'mandatory' => true, 'ref' => array('obj' => $termOfPayment, 'field' => "code"), 'return' => array('paramName' => 'termofpaymentcode') ),
    'termofpayment' => array('paramName' => 'term_of_payment', 'updatable' => false,  'ref' => array('obj' => $termOfPayment), 'return' => array('paramName' => 'termofpaymentname')),
    'trdesc' => array('paramName' => 'description'),
    'finaldiscounttype' => array('paramName' => 'final_discount_type'),
    'finaldiscount' => array('paramName' => 'final_discount'),
    'ispriceincludetax' => array('paramName' => 'price_include_tax'),
    'taxpercentage' => array('paramName' => 'tax_percentage'),
    'point' => array('paramName' => 'point'),
    'pointvalue' => array('paramName' => 'point_value', 'updatable' => false),
	'membershiplevelkey'  =>  array('paramName' => 'membership_level_key'),
	'membershiplevel'  =>  array('paramName' => 'membership_level_name', 'updatable' => false),
    'detail' => array('paramName' => 'detail', 'dataset' => $OBJ->arrDataDetail , 'tableName' => $OBJ->tableNameDetail, 'detail' =>  $detail)
));
         
require_once '_process.php';
     
?>