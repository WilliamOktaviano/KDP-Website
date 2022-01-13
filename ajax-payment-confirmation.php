<?php
require_once '_config.php'; 
require_once '_include-min.php';
require_once '_global.php';  

if(!isset($_POST) || empty($_POST['action']))
                    die;
$arr = array();
foreach ($_POST as $k => $v) { 
    if (!is_array($v))
         $v = trim($v);  

    $arr[$k] = $v;     
}  

$arrayToJs = array(); 

switch ( $_POST['action']){ 
  case 'add' :    
        if (empty(USERKEY)){
            $code = $_POST['invoiceId'];
            $email = $_POST['email'];
            $rsCustomer = $customer->searchData('email',$email,true);
            $rsSalesOrder = $salesOrder->searchData($salesOrder->tableName.'.code',$code,true,' and recipientemail = ' .$salesOrder->oDbCon->paramString($email) );
        }else{
            //validasi ulang key sama login member id sama ap gk
            $rsSalesOrder = $salesOrder->searchData('customerkey',USERKEY,true,' and '.$salesOrder->tableName.'.pkey = '. $salesOrder->oDbCon->paramString($_POST['hidInvoiceKey']));
        
        }
        
        if (empty($rsSalesOrder)){  
			$salesOrder->addErrorList($arrayToJs,false,$salesOrder->lang['noDataFound']);
        }else{   
            $arr['code']  = 'xxxxx';
            $arr['hidSalesKey']  = $rsSalesOrder[0]['pkey'];
            //$arr['hidCustomerKey']  = USERKEY;
            $arr['amount']  = $rsSalesOrder[0]['grandtotal'];
            $arr['paymentDate']  = $_POST['trDate'];
            $arr['paymentAmount']  = $class->unFormatNumber($_POST['paymentAmount']);
            $arr['selPaymentMethod']  = $_POST['selPaymentMethodKey'];
            $arr['bankName']  = $_POST['bankName'];
            $arr['bankAccountName']  = $_POST['bankAccountName'];
            $arr['bankAccountNumber']  = $_POST['bankAccountNumber'];
            $arr['branch']  = $_POST['bankBranch'];
            $arr['createdBy'] = 0;
            $arr['selStatus'] = 1;
            $arr['fromFE'] = 1; 
            $arrayToJs = $paymentConfirmation->addData($arr);

        }
        

		break; 
		
	case 'generateInvoice' : 
	    
        $valid = true;
        $arrayToJs =  array();
     
        $captchaResponse = $arr['g-recaptcha-response'];  
        $request = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$salesOrder->loadSetting('reCaptchaSecretKey')."&response=".$captchaResponse);
        $captchaResult = json_decode($request);

        $errorCaptcha= $captchaResult->{'error-codes'}; 


        if (empty($captchaResponse)){ 
            $salesOrder->addErrorList($arrayToJs,false,$salesOrder->errorMsg['captcha'][1]);
        } else if(!$captchaResult->{'success'}){ 
            $salesOrder->addErrorList($arrayToJs,false,$salesOrder->errorMsg['captcha'][1]);
        } else{
                $code = $_POST['invoiceId'];
                $email = $_POST['email'];

                if (empty(USERKEY)){
                    $rsCustomer = $customer->searchData('email',$email,true);
                    $rsSalesOrder = $salesOrder->searchData($salesOrder->tableName.'.code',$code,true,' and '. $salesOrder->tableName.'.statuskey = 1 and recipientemail = ' .$salesOrder->oDbCon->paramString($email) );
                } 

                if (empty($rsSalesOrder)){  
                    $salesOrder->addErrorList($arrayToJs,false,$salesOrder->lang['noDataFound']);
                }else{   
                    $arrDetail = array(); 
                    $arrDetail['code'] = $rsSalesOrder[0]['code'];
                    $arrDetail['pkey'] = $rsSalesOrder[0]['pkey'];
                    $arrDetail['amount'] = $rsSalesOrder[0]['grandtotal']; 
                    $arrDetail['token'] = md5($rsSalesOrder[0]['pkey'] . $rsSalesOrder[0]['grandtotal'] . $class->secretKey);
                    $salesOrder->addErrorList($arrayToJs,true,$arrDetail);
                }

       }
 
	   
		break;
}

echo json_encode($arrayToJs);  

?>
