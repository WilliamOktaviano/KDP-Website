<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  
 
includeClass("SalesOrder.class.php","Shipment.class.php");
$salesOrder = new SalesOrder();
$shipment = new Shipment();

$result = $class->lang['noDataFound'];
if (isset($_GET) && !empty($_GET['invoicekey']) && !empty($_GET['token']) ){ 
	
    // check token
    $key = $_GET['invoicekey'];
    $token = $_GET['token'];
   
    $rs = $salesOrder->getDataRowById($key);
	$rsDetail = $salesOrder->getDetailWithRelatedInformation($key); 
    $rsVoucher = $salesOrder->getVoucherDetail($key);
           
    if (md5($rs[0]['pkey'] . $rs[0]['grandtotal'] . $class->secretKey) <> $token)  die;
    
    // invoice template 
    for($i=0;$i<count($rsDetail);$i++){ 
        if ($rsDetail[$i]['discounttype'] == 2)
            $rsDetail[$i]['discount'] = $rsDetail[$i]['discount']/100 * $rsDetail[$i]['priceinunit']; 
    }
    
    $arrTemp['LANG'] = $class->lang;
    $arrTemp['settings'] = $arrTwigVar ['settings'];
    $arrTemp['HTTP_HOST'] = $arrTwigVar ['HTTP_HOST'];
    $arrTemp['rsDetail'] = $rsDetail;
    $arrTemp['rsVoucher'] = $rsVoucher;
    $arrTemp['code'] = $rs[0]['code'];
    $arrTemp['pkey'] = $rs[0]['pkey'];
    $arrTemp['hash'] = md5($rs[0]['pkey'] . $rs[0]['grandtotal'] . $class->secretKey);
    
    $arrTemp['companyLogoDocPath'] = 'setting/emailLogo/'.$class->loadSetting('emailLogo');
         
    $rsStatus = $salesOrder->getStatusById($rs[0]['statuskey']);
    $arrTemp['statuskey'] = $rsStatus[0]['pkey'];
    $arrTemp['statusname'] = $rsStatus[0]['status'];
    
    if ($rs[0]['finaldiscounttype'] == 2)
        $rs[0]['finaldiscount'] = $rs[0]['finaldiscount']/100 * $rs[0]['subtotal'];

    $finaldiscount = ($rs[0]['finaldiscount'] != 0) ? $rs[0]['finaldiscount'] * -1 : 0;    
    
    $arrTemp['trdate'] =  $rs[0]['trdate'];
    $arrTemp['grandSubtotal'] = $rs[0]['subtotal'];
    $arrTemp['shipmentFee'] = $rs[0]['shipmentfee'];
     
    $rsService = $shipment->getAllShipment('',$rs[0]['shipmentservicekey']);
    $recipientCourier = $rsService[0]['joinservicename'] ;
    if($rs[0]['useinsurance'] == 1)
        $recipientCourier .= ' (Asuransi)';
    $arrTemp['recipientCourier'] = $recipientCourier;

    $arrTemp['finalDiscountValue'] = $finaldiscount; 
    $arrTemp['subtotal'] = $rs[0]['subtotal'];  
    $arrTemp['beforeTax'] = $rs[0]['beforetaxtotal']; 
    $arrTemp['taxValue'] = $rs[0]['taxvalue']; 
    $arrTemp['grandTotal'] = $rs[0]['grandtotal']; 
    $arrTemp['etcCost'] = $rs[0]['etccost'];

    $arrTemp['recipientName'] = $rs[0]['recipientname'];
    $arrTemp['recipientPhone'] = $rs[0]['recipientphone']; 
    $arrTemp['recipientEmail'] = $rs[0]['recipientemail'];
    $arrTemp['recipientAddress'] = str_replace(chr(13),'<br>',$rs[0]['recipientaddress']);
  
    $arrTemp['dropshiperName'] = $rs[0]['dropshipername'];
    $arrTemp['dropshiperPhone'] = $rs[0]['dropshiperphone'];
   
} 

$arrTwigVar['footerInvoice']  = str_replace(chr(13),'<br>',$class->loadSetting('emailInvoiceFooter'));


if (isset($_GET) && !empty($_GET['forEmail'])){ 
    echo $twig->render('invoice-email-template.html', $arrTemp); 
} else{ 
    $arrTwigVar['invoice'] =  $twig->render('invoice-sales-template.html', $arrTemp);  
    echo $twig->render('invoice.html', $arrTwigVar);
}
?>