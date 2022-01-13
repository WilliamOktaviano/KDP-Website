<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';

includeClass(array('SalesOrder.class.php','Warehouse.class.php','Item.class.php','Shipment.class.php','DiscountScheme.class.php'));
$salesOrder = new SalesOrder();
$warehouse = new Warehouse();
$shipment = new Shipment();
$item = new Item();
$discountScheme = new DiscountScheme();

if(!isset($_POST) || empty($_POST['action'])) die;
	  
$arrayToJs = array();  

switch ( $_POST['action']){ 
	case 'addToCart' : 
	 
				$arr = array(); 
				foreach ($_POST as $k => $v) $arr[$k] = $v; 
				
				$arrayToJs = $salesOrder->addToCartSession($arr);  
				break;
		 
    case 'updateQty' :
                if(!isset($_POST['itemkey']) || empty($_POST['itemkey'])) die;
                if(!isset($_POST['qty']) || empty($_POST['qty'])) die;
          
                $qty = $class->unformatNumber($_POST['qty']);
                if ($qty <= 0) $qty = 1;
                  
                foreach($_SESSION[$class->loginSession]['cart'] as $key=>$row){  
                        if ($row['itemkey'] == $_POST['itemkey'])  
                           $_SESSION[$class->loginSession]['cart'][$key]['qty'] = $qty; 
                         
                }
                
                $salesOrder->updateTemporaryCart();   
                break;
                    
        case 'delete' :     

                foreach($_SESSION[$class->loginSession]['cart'] as $key=>$row){
                     if ($row['itemkey'] == $_POST['itemkey'])
                        unset($_SESSION[$class->loginSession]['cart'][$key]);
                }
        
                $_SESSION[$class->loginSession]['cart'] = array_values($_SESSION[$class->loginSession]['cart']);    
				
                $salesOrder->updateTemporaryCart();   
                break;
	 
				
        case 'cartStatus' :

            $qty = 0;	  

            if (isset($_SESSION) && !empty($_SESSION[$class->loginSession]['cart'])) { 
                for($i=0;$i<count($_SESSION[$class->loginSession]['cart']); $i++){
                    $qty += $_SESSION[$class->loginSession]['cart'][$i]['qty']; 
                }  
            }
 
            $arrayToJs['totalqty'] = number_format($qty,0,'.',','); 
          
            break; 
	  
							
		case 'add' :      
        
			$arr = array(); 

			foreach ($_POST as $k => $v) {
				$arr[$k] = $v; 
			}

			$customerkey = (!empty(USERKEY)) ? USERKEY : 1;

			$arr['hidCustomerKey'] = $customerkey;

			$arr['code'] = 'xxxxxx';    
			$arr['trDate'] = date('d / m / Y');

			$rsWarehouse = $warehouse->searchData($warehouse->tableName.'.statuskey',1,true,' order by pkey asc'); 
			$arr['selWarehouseKey'] =  $rsWarehouse[0]['pkey']; 

			// hitung ulang shipping cost

			$arrItems = array();
			for($i=0;$i<count($arr['hidItemKey']);$i++){ 
				array_push($arrItems,array(
											'itemkey' => $arr['hidItemKey'][$i],
											'qty' => $_SESSION[$class->loginSession]['cart'][$i]['qty']
											));
			}

			$latlng = explode(",",$_POST['hidLatLng']);
			$destination = array('latlng' => array('lat' => $latlng[0],'lng' => $latlng[1]), 'zipcode' => $_POST['recipientZipcode']); 

			$shipmentInformation = $shipment->getShippingInformation(array('serviceKey' => $_POST['selShipmentService'],
													 'destination' => $destination,
													 'items' => $arrItems
			));

			$shippingCost = (!empty($shipmentInformation['price'])) ? $shipmentInformation['price'] : 0;

			$arr['selTermOfPaymentKey'] = 1; 
			$arr['trNotes'] = '';
			$arr['selFinalDiscountType'] = '0';
			$arr['finalDiscount'] = $_POST['discount'];

			// tentuin tax
			$taxInformation = $class->getFrontEndTax(0);

			$arr['isPriceIncludeTax'] = $taxInformation['isPriceIncludeTax'];
			$arr['taxPercentage'] =  $taxInformation['taxPercentage'];
			$arr['taxValue'] = 0;  
			$arr['etcCost'] = '0';
			$arr['createdBy'] = 0;
			$arr['selSalesKey'] = '1';
			$arr['paymentMethodValue'] = 1;
			$arr['paymentMethodKey'] = 1; 
			$arr['pointValue'] = $_SESSION[$class->loginSession]['pointValue'];
			$arr['fromFE'] = 1;
			$arr['chkIsFullDeliver'] = 1;				
			$arr['selShipment'] = $_POST['selShipmentService'];							
			$arr['shipmentFee'] = $shippingCost;						
			$arr['useInsurance'] = $_POST['useInsurance'];						
			$arr['hidRecipientCityKey'] = $_POST['hidRecipientCityKey'];						
			$arr['hidVoucherKey'] = $_POST['hidVoucherKey'];									
			$arr['recipientmapaddress'] = $_POST['mapAddress'];									
			$arr['recipientlatlng'] = $_POST['hidLatLng'];									
			$arr['recipientzipcode'] = $_POST['recipientZipcode'];								

			$arrayToJs = array(); 
			$class->addErrorList($arrayToJs,false, $class->errorMsg['cart'][1]); 	

			$arr['hidVoucherKey'] =  $_POST['hidVoucherKey'];	
			$arr['hidDetailVoucherKey'] = array();
			$arr['voucherAmount'] = array();
			foreach($arr['hidVoucherKey'] as $row){
				// dummy aj, nanti akan dihitung ulang
				array_push($arr['hidDetailVoucherKey'],0);
				array_push($arr['voucherAmount'],1);
			}

			// voucher transaksi berubah cara penggunaannya
			/*if (isset($arr['hidVoucherKey']) && !empty($arr['hidVoucherKey'])){
				$arrayToJs = addVoucherTransaction($arr); 
				$rsVoucher = $arrayToJs[0]['data'];
				$arr['hidTransVoucherKey'] = $rsVoucher['pkey'];
			}*/

			if (isset($arr['hidItemKey']) && !empty($arr['hidItemKey']))
				$arrayToJs = addSO($arr); 

			/*if (isset($arr['POhidItemKey']) && !empty($arr['POhidItemKey']))
				$arrayToJs = addPO($arr); */

			$salesOrder->clearTemporaryCart();
			break;			
	
}


function addSO($arr){ 
	
	global $class;
	global $item;
	global $salesOrder; 
    global $discountScheme;
	  
    $arr['hidDetailKey'] = array();
    $arr['priceInUnit'] = array();
    $arr['discountValueInUnit'] = array();
    $arr['selDiscountType'] = array();
    $arr['selUnit'] = array();
    $arr['qty'] = array(); // set ulang karena dr UI ad post ['qty'] 
    
    $rsItem = $item->searchDataRow(array($item->tableName.'.pkey',$item->tableName.'.baseunitkey', $item->tableName.'.sellingprice'),
                                  ' and '.$item->tableName.'.pkey in ('.$class->oDbCon->paramString($arr['hidItemKey'],',').' )'
                                  );
    $discountScheme->applyDiscountScheme($rsItem);
    
    $rsItem = array_column($rsItem,null,'pkey');
     
    
    for($i=0;$i<count($arr['hidItemKey']);$i++){ 
        $itemRow = $rsItem[$arr['hidItemKey'][$i]];
        
        $arr['hidDetailKey'][$i] =0; 
        $arr['priceInUnit'][$i] = $itemRow['sellingprice'];  
        $arr['discountValueInUnit'][$i] = 0;
        $arr['selDiscountType'][$i] = 1;   
        $arr['selUnit'][$i] = $itemRow['baseunitkey'];
        $arr['qty'][$i] = $_SESSION[$class->loginSession]['cart'][$i]['qty']; 

	}
	 
	$arrayToJs = $salesOrder->addData($arr);
	
	return $arrayToJs;
}

// voucher transaksi berubah cara penggunaannya
//function addVoucherTransaction($arr){ 
//	 
//	global $voucherTransaction;  
//	global $salesOrder; 
//	
// 	//$salesOrder = new salesOrder();  
//	
//    $arrParam = array();
//    
//    $rsKey = $salesOrder->getTableKeyAndObj($salesOrder->tableName);
//    
//    $arrParam['trDate'] = $arr['trDate']; 
//    $arrParam['hidVoucherKey'] = $arr['hidVoucherKey']; 
//    $arrParam['hidCustomerKey'] = $arr['hidCustomerKey']; 
//    $arrParam['selWarehouse'] = $arr['selWarehouseKey']; 
//    $arrParam['refTableType'] = $rsKey['key']; 
//    $arrParam['selStatus'] = 1;  
//    $arrParam['code'] = 'xxxxxx'; 
//	
//    $arrayToJs = $voucherTransaction->addData($arrParam); 
//	
//	return $arrayToJs;
//}
echo json_encode($arrayToJs); 

?>