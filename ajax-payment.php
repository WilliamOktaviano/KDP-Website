<?php 
include '_config.php'; 
require_once '_include-fe-v2.php';
include '_global.php';

if(!isset($_POST) || empty($_POST['action'])) die;
if(!isset($_POST) || empty($_POST['partner'])) die;

$arrayToJs = array(); 
$obj = '';

$arr = array();
foreach ($_POST as $k => $v) { 
	if (!is_array($v)) $v = trim($v);   
	$arr[$k] = $v;   
}  
 

// biar kedepan bisa utk pembayaran jenis registrasi, deposit, dsb
  
switch($_POST['action']){
	case 'sales-order' : 	includeClass(array("SalesOrder.class.php"));
							$obj = new SalesOrder();
							break;
	case 'membership' : 	includeClass(array("MembershipSubscription.class.php"));
							$obj = new MembershipSubscription();
							break;
		
}

if ( !empty($obj)){ 
    
    $pkey = $_POST['pkey'];
    
    $arr = array(); 
    
    $arr['invoiceURL'] = (isset($_POST['invoiceurl'])) ? $_POST['invoiceurl'] : ''; 
 
    $obj->updatePaymentGatewwayResponse($pkey,$arr);  
    
    // kalo transaksi sukses
    if($_POST['result'] == 200)
        $obj->paymentGatewaySuccess($pkey);
        
        
    if(!empty($_POST['invoiceurl']) && $_POST['result'] != 200)
        $obj->sendPaymentInstruction($pkey);
        
} 

echo json_encode($arrayToJs);  

?>