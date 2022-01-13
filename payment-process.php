<?php  
include '_config.php'; 
include '_include-fe-v2.php'; 
include '_global.php'; 
 
use Midtrans\Config;
use Midtrans\Snap;

includeClass(array('SalesOrder.class.php','Customer.class.php','MembershipSubscription.class.php'));
$customer = new Customer(); 
$city = new City();
$cityCategory = new CityCategory();
$salesOrder = new SalesOrder();
$shipment = new Shipment();
$membershipSubscription = new MembershipSubscription();

// kalo pake midtrans

if (!isset($_GET) || empty($_GET['id']) || empty($_GET['action']) )
    die($class->lang['noDataFound']);
 
$arrTwigVar['footerInvoice']  = str_replace(chr(13),'<br>',$class->loadSetting('emailInvoiceFooter'));

 switch ($_GET['action']) {
		case 'sales-order': 
							 $rs = $salesOrder->searchDataRow(array($salesOrder->tableName.'.pkey', $salesOrder->tableName.'.invoicesent'),
															 ' and '.$salesOrder->tableName.'.pkey = ' .$salesOrder->oDbCon->paramString($_GET['id']));
							if($rs[0]['invoicesent'] == 0)  $salesOrder->sendInvoice($_GET['id']);  
		 					break;
		 case 'membership': 
							 
							//if($rs[0]['invoicesent'] == 0)  $salesOrder->sendInvoice($_GET['id']);  
		 					break;
 }
  
// PAYMENT GATEWAY 
$paymentGatewayProduction = $class->loadSetting('PaymentGatewayIsProduction');
$paymentGatewayAPI = $class->loadSetting('paymentGatewayAPI');

$arrTwigVar['MIDTRANS_CLIENT_KEY'] = $class->loadSetting('PaymentGatewayClientKey');
$arrTwigVar['MIDTRANS_SERVER_KEY'] = $class->loadSetting('PaymentGatewayServerKey');
$arrTwigVar['IS_PRODUCTION'] = ($paymentGatewayProduction == 1) ? true : false;
$arrTwigVar['LIB_JS'] = $paymentGatewayAPI; //'https://app.midtrans.com/snap/snap.js';

if(!empty($arrTwigVar['MIDTRANS_SERVER_KEY'])){

    require_once(dirname(__FILE__) . '/assets/midtrans/Midtrans.php');    

    $totalAmount = 0; 
    $uniqueCode =  '-' . substr(time(),-4);
    $arrMidTrans = array();

    $arrTwigVar['partner'] = 'midtrans';
	
    switch ($_GET['action']) {

        case 'sales-order':  
            $arrTemp = compileSalesOrderInvoice($_GET['id']);

            $arrMidTrans['orderCode'] = $arrTemp['code'] . $uniqueCode;
            $arrMidTrans['totalAmount'] = $arrTemp['grandTotal'];
            $arrMidTrans['customerName'] = $arrTemp['customerName'];
            $arrMidTrans['customerEmail'] = $arrTemp['customerEmail'];
            $arrMidTrans['customerPhone'] = $arrTemp['customerPhone'];

            $arrTwigVar['pkey']  = $arrTemp['pkey'];
            $arrTwigVar['tokenkey']  = $arrTemp['tokenkey'];
            $arrTwigVar['statuskey']  = $arrTemp['statuskey'];
            $arrTwigVar['modulename'] = 'sales-order';
            $arrTwigVar['invoice'] =  $twig->render('invoice-sales-template.html', $arrTemp);
            break;
			
		 case 'membership':  
            $arrTemp = compileMembershipInvoice($_GET['id']);

            $arrMidTrans['orderCode'] = $arrTemp['code'] . $uniqueCode;
            $arrMidTrans['totalAmount'] = $arrTemp['grandTotal'];
            $arrMidTrans['customerName'] = $arrTemp['customerName'];
            $arrMidTrans['customerEmail'] = $arrTemp['customerEmail'];
            $arrMidTrans['customerPhone'] = $arrTemp['customerPhone'];

            $arrTwigVar['pkey']  = $arrTemp['pkey'];
            $arrTwigVar['tokenkey']  = $arrTemp['tokenkey'];
          	$arrTwigVar['statuskey']  = $arrTemp['statuskey'];
            $arrTwigVar['modulename'] = 'membership'; 
            $arrTwigVar['invoice'] =  $twig->render('invoice-membership-template.html', $arrTemp);
            break;
    }
 
    $arrTwigVar['snapToken'] =  midtransGetSnapToken($arrMidTrans);  
}    
// PAYMENT GATEWAY 
echo $twig->render('payment-process.html', $arrTwigVar); 

function midtransGetSnapToken($arr){
        global $class;
        global $arrTwigVar;
    
        // ================  ================  ================ MIDTRANS  ================  ================  ================ 
         
        Config::$serverKey = $arrTwigVar['MIDTRANS_SERVER_KEY']; 
        Config::$isProduction = $arrTwigVar['IS_PRODUCTION']; 
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

	
        $arrTwigVar ['btnPayment'] =  $class->input('btnPayment', array( 'type' => 'button', 'class' => 'btn btn-primary', 'value' => $class->lang['continueToPayment'])); 
 
        $snapToken = Snap::getSnapToken($transaction);  
        return $snapToken;
        // ================  ================  ================ MIDTRANS  ================  ================  ================ 

} 
 
function compileSalesOrderInvoice($id){  
    
    global $class;
    global $security;
    global $salesOrder;
    global $customer;
    global $item;
    global $shipment;
    global $city;
    global $cityCategory;
    
    // gk harus registrasi dulu utk transaksi
    
    /* if(!$security->isMemberLogin(false)) 
        header('location:/logout'); */

    $rs = $salesOrder->getDataRowById($id);
    if (empty($rs)) die($class->lang['noDataFound']);
    
    $rsDetail = $salesOrder->getDetailWithRelatedInformation($rs[0]['pkey']);  
    $rsVoucher = $salesOrder->getVoucherDetail($rs[0]['pkey']);
    //$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
     
    // pastikan sales order sama dengan pemiliknya...
    // perlu ganti pake token hashcode
    /*if ($rsCustomer[0]['pkey'] <> base64_decode($_SESSION[$class->loginSession]['id']))
        die($class->lang['noDataFound']);*/
    
    // INVOICE TEMPLATE 
    for($i=0;$i<count($rsDetail);$i++){ 
        // diskon
        if ($rsDetail[$i]['discounttype'] == 2)
            $rsDetail[$i]['discount'] = $rsDetail[$i]['discount'] /100 * $rsDetail[$i]['priceinunit'] * $rsDetail[$i]['qty'] ;
    }

    $arrTemp = array();
    $arrTemp['LANG'] = $class->lang;
    $arrTemp['rsDetail'] = $rsDetail;
    $arrTemp['rsVoucher'] = $rsVoucher;
    $arrTemp['pkey'] = $rs[0]['pkey'];
    $arrTemp['tokenkey'] = md5($rs[0]['pkey'].$rs[0]['code']);
    $arrTemp['code'] = $rs[0]['code']; 
    $arrTemp['trdate'] = date('Y-m-d') ;
    $arrTemp['subtotal'] = $rs[0]['subtotal'];
	
  
    $rsService = $shipment->getAllShipment('',$rs[0]['shipmentservicekey']);
    $recipientCourier = $rsService[0]['joinservicename'] ;
    if($rs[0]['useinsurance'] == 1)
        $recipientCourier .= ' (Asuransi)';
     
    $arrTemp['recipientCourier'] = $recipientCourier;
 
    $arrTemp['discountValue'] = $rs[0]['finaldiscount']; 
    $arrTemp['beforeTax'] = $rs[0]['beforetaxtotal']; 
    $arrTemp['taxValue'] = $rs[0]['taxvalue']; 
    $arrTemp['grandTotal'] = $rs[0]['grandtotal']; 

    $arrTemp['customerName'] = $rs[0]['recipientname'];; //$rsCustomer[0]['name'];
    $arrTemp['customerEmail'] = $rs[0]['recipientemail']; //(!empty($rsCustomer[0]['email'])) ? $rsCustomer[0]['email'] : $rs[0]['recipientemail'];
    
    $/*phone = $rsCustomer[0]['phone'];
    if(empty($phone))
        $phone = $rsCustomer[0]['mobile'];*/
    $arrTemp['customerPhone'] = $rs[0]['recipientphone'];;
    
    
    $arrTemp['recipientName'] = $rs[0]['recipientname'];
    $arrTemp['recipientPhone'] = $rs[0]['recipientphone'];
    $arrTemp['recipientMobile'] = $rs[0]['recipientmobile'];
    $arrTemp['recipientEmail'] = $rs[0]['recipientemail'];
    $arrTemp['recipientAddress'] = str_replace(chr(13),'<br>',$rs[0]['recipientaddress']);

    $rsCity = $city->getDataRowById($rs[0]['citykey']); 
    $rsCityCategory = $cityCategory->getDataRowById($rsCity[0]['categorykey']); 
    $arrTemp['recipientCity'] = $rsCity[0]['name']. ', '. $rsCityCategory[0]['name']; 

    $arrTemp['dropshiperName'] = $rs[0]['dropshipername'];
    $arrTemp['dropshiperPhone'] = $rs[0]['dropshiperphone'];  

    $rsStatus = $salesOrder->getStatusById($rs[0]['statuskey']);
    $arrTemp['statusname'] = $rsStatus[0]['status']; 
    $arrTemp['statuskey'] = $rsStatus[0]['pkey']; 

    return $arrTemp;
}

function compileMembershipInvoice($id){  
    
    global $class;
    global $security; 
	global $customer;
    global $membershipSubscription; 

    $rs = $membershipSubscription->searchData($membershipSubscription->tableName.'.pkey', $id);
    if (empty($rs)) die($class->lang['noDataFound']);
       
    $arrTemp = array();
    $arrTemp['LANG'] = $class->lang;  
    $arrTemp['pkey'] = $rs[0]['pkey'];
    $arrTemp['tokenkey'] = md5($rs[0]['pkey'].$rs[0]['code']);
    $arrTemp['code'] = $rs[0]['code']; 
    $arrTemp['trdate'] = $rs[0]['trdate'] ;
    $arrTemp['subtotal'] = $rs[0]['subtotal'];  
    $arrTemp['beforeTax'] = $rs[0]['beforetaxtotal'];  
    $arrTemp['taxValue'] = 0; 
    $arrTemp['grandTotal'] = $rs[0]['grandtotal'];  

    $arrTemp['customerName'] = $rs[0]['customername']; 
    $arrTemp['customerEmail'] = $rs[0]['customeremail'];  
    $arrTemp['customerPhone'] = $rs[0]['customerphone']; 
          
    $arrTemp['servicename'] = $rs[0]['membershiplevel']; 
	
    $rsStatus = $membershipSubscription->getStatusById($rs[0]['statuskey']);
    $arrTemp['statusname'] = $rsStatus[0]['status']; 
    $arrTemp['statuskey'] = $rsStatus[0]['pkey']; 
	
    return $arrTemp;
}
?>