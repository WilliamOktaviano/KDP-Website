<?php
require_once '../../_config.php';  
require_once '_include.php';

use Midtrans\Config;
use Midtrans\Snap;

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/SalesOrder.class.php';

require_once '_global.php';

if($ACTION != 'GET') endForRequestMethodError();

$hasSuccessValue = false;
$arrFailed = array();
$ARR_RETURN_VALUE = array();
 

require_once( $_SERVER ['DOCUMENT_ROOT']. '/assets/midtrans/Midtrans.php');    

$uniqueCode =  '-' . substr(time(),-4);

$arrMidTrans = array();
$arrMidTrans['orderCode'] = $_GET['order_id'] . $uniqueCode;
$arrMidTrans['totalAmount'] = $_GET['total'];
$arrMidTrans['customerName'] = $_GET['customer_name'];
$arrMidTrans['customerEmail'] = $_GET['customer_email'];
$arrMidTrans['customerPhone'] = $_GET['customer_phone'];

$responseCode = 200;
$message = '';
$snapToken = '';

try{
    $snapToken =  midtransGetSnapToken($arrMidTrans);      
}catch(Exception $e){
    $responseCode = 401;
    $message = $e->getMessage();
}


$RETURN_VALUE['response_code'] = $responseCode;
$RETURN_VALUE['data'] =  $snapToken;
$RETURN_VALUE['message'] = $message;

http_response_code($RETURN_VALUE['response_code']); 
echo json_encode($RETURN_VALUE);
die;


function midtransGetSnapToken($arr){
        global $class; 
    
        $paymentGatewayClientKey = $class->loadSetting('PaymentGatewayClientKey');
        $paymentGatewayServerKey = $class->loadSetting('PaymentGatewayServerKey');
        $paymentGatewayProduction = $class->loadSetting('PaymentGatewayIsProduction');
    
        // ================  ================  ================ MIDTRANS  ================  ================  ================ 
         
        Config::$serverKey = $paymentGatewayServerKey; 
        Config::$isProduction = ($paymentGatewayProduction == 1) ? true : false; 
        Config::$isSanitized = true; 
        Config::$is3ds = true;
  
        // Required
        $transaction_details = array(
          'order_id' => $arr['orderCode'],
          'gross_amount' => round($arr['totalAmount']), // no decimal allowed for creditcard
        );
    
        $customer_details = array(
          'first_name'    => $arr['customerName'], 
          'email'         => $arr['customerEmail'], 
          'phone'         => $arr['customerPhone'],
        );


        // Optional, remove this to display all available payment methods
        //$enable_payments = array('credit_card','cimb_clicks','mandiri_clickpay','echannel');

        // Fill transaction details
        $transaction = array(
          //'enabled_payments' => $enable_payments,
          'transaction_details' => $transaction_details, 
          'customer_details' => $customer_details,
        );

        //$arrTwigVar ['btnPayment'] =  $class->input('btnPayment', array( 'type' => 'button', 'class' => 'btn btn-primary', 'value' => $class->lang['continueToPayment'])); 

        $snapToken = Snap::getSnapToken($transaction); 
        return $snapToken;
        // ================  ================  ================ MIDTRANS  ================  ================  ================ 

} 

?>