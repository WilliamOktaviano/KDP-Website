<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  
 
includeClass(array("Customer.class.php", "CityCategory.class.php","City.class.php","Item.class.php","Shipment.class.php", "DiscountScheme.class.php", "VoucherTransaction.class.php"));

$customer = new Customer();
$city = new City();
$item = new Item();
$itemUnit = new ItemUnit();
$shipment = new Shipment();
$discountScheme = new DiscountScheme();
$voucherTransaction = new VoucherTransaction();

$_POST['action'] ='add';  
$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action'); 

$eligiblePoint = 0;

if (USERKEY != 0){     
    $rsUser = $customer->getDataRowById(USERKEY); 
    $_POST['recipientName'] = $rsUser[0]['name'];
    $_POST['recipientPhone'] = $rsUser[0]['phone'];
    $_POST['recipientEmail'] = $rsUser[0]['email'];
    $_POST['recipientAddress'] = $rsUser[0]['address'];
    $_POST['recipientZipcode'] = $rsUser[0]['zipcode'];
    
    $rsCity = $city->searchData($city->tableName.'.pkey',$rsUser[0]['citykey'],true);
    $_POST['hidRecipientCityKey'] = (!empty($rsCity)) ? $rsCity[0]['pkey'] : '';
    $_POST['hidRecipientCityName'] = (!empty($rsCity)) ? $rsCity[0]['citycategoryname'] : '';
    $_POST['mapAddress'] = $rsUser[0]['mapaddress'];
    $_POST['hidLatLng'] = $rsUser[0]['latlng'];
	
	$eligiblePoint = $rsUser[0]['point'];
	$pointUnitValue = $class->loadSetting('rewardsPointUnitValue');
	
	$arrTwigVar['rewardsPointUnitValue'] = $pointUnitValue;
	$arrTwigVar['rsCustomer'] = $rsUser[0]; 
}

$subtotal = 0;
$totalWeight = 0;
$rsCartList = array(); 
$arrTwigVar['hidItemKey'] = array(); 
$arrTwigVar['hidItemWeight'] = array(); 

// perlu update ulang di ajax
$voucherCriteria = array(); 
$voucherCriteria['brandkey'] = array();//$rsItem[0]['brandkey'];
$voucherCriteria['itemkey'] =  array(); //$rsItem[0]['pkey'];
$voucherCriteria['itemcategorykey'] = array();  //$rsItem[0]['categorykey'];

if (isset($_SESSION[$class->loginSession]) && !empty($_SESSION[$class->loginSession]['cart'])) {

    for($i=0;$i<count($_SESSION[$class->loginSession]['cart']); $i++){

        $rsItem = $item->getDataRowById($_SESSION[$class->loginSession]['cart'][$i]['itemkey']); 
        
        // bisa lemot gk kalo byk itemmnya
        $discountScheme->applyDiscountScheme($rsItem);
        
        $rsUnit = $itemUnit->getDataRowById($rsItem[0]['deftransunitkey']);
        
        if(empty($rsItem)) continue;

        $arrItem = $rsItem[0];
         
        $arrItem['image'] = $item->getItemImage($arrItem['pkey']);
        $arrItem['qty'] =  $_SESSION[$class->loginSession]['cart'][$i]['qty'] ;   
        $arrItem ['inputQty'] =  $class->inputNumber('qty[]',array('value' =>  $arrItem['qty']));
        
        $arrItem['unitname'] = $rsUnit[0]['name']; //temporary
        $rowSubtotal  = $arrItem['qty'] * $arrItem['sellingprice'];
        $arrItem['subtotal'] = $rowSubtotal;

        $subtotal += $rowSubtotal;
         
        $arrItem['gramasi'] = $arrItem['gramasi'];
        if ($arrItem['weightunitkey'] == UNIT['kg'])
            $arrItem['gramasi'] *= 1000;
        
        $arrItem['subtotalgramasi'] = $arrItem['qty'] * $arrItem['gramasi'];
        //$class->setLog($arrItem['qty'] * $arrItem['gramasi'],true);
        $totalWeight += $arrItem['subtotalgramasi'];
        
        array_push($rsCartList, $arrItem);
        
        array_push($arrTwigVar['hidItemKey'], $class->inputHidden('hidItemKey[]', array('value' => $arrItem['pkey'])));
        array_push($arrTwigVar['hidItemWeight'], $class->inputHidden('hidItemWeight[]', array('value' => $arrItem['gramasi'])));
  
        // voucher
        if(!in_array( $arrItem['brandkey'], $voucherCriteria['brandkey']))  array_push($voucherCriteria['brandkey'], $arrItem['brandkey']);
        if(!in_array( $arrItem['pkey'], $voucherCriteria['itemkey']))  array_push($voucherCriteria['itemkey'], $arrItem['pkey']);
        if(!in_array( $arrItem['categorykey'], $voucherCriteria['itemcategorykey']))  array_push($voucherCriteria['itemcategorykey'], $arrItem['categorykey']);
  
    } 
    

	// hitung point
	$pointNeeded = ceil($subtotal/$pointUnitValue);
	$pointNeeded = ($pointNeeded > $eligiblePoint) ? $eligiblePoint : $eligiblePoint;
	
	$_POST['point'] = $pointNeeded;
	
	$pointDisc = $pointNeeded * $pointUnitValue * -1;
	$grandtotal = $subtotal + $pointDisc; 
	
	$arrTwigVar['pointValue'] = $pointDisc;
    $voucherCriteria['totalsales'] = $subtotal;
}

$availableVoucher = array(); //$voucher->getAvailableVoucher(array(VOUCHER_CATEGORY['sales'],VOUCHER_CATEGORY['shipment']),VOUCHER_TYPE['regular'],CUSTOMER_TYPE['enduser'], $voucherCriteria );
$arrTwigVar['availableVoucher'] = $availableVoucher;

$totalWeight = $totalWeight / 1000;

$arrTwigVar['cartList'] = $rsCartList;
$arrTwigVar['subtotal'] = $subtotal;

// nanti perlu diupdate kalo settingannya include
$tax = $class->getFrontEndTax($subtotal)['taxValue'];  
$arrTwigVar['tax'] = $tax;

$arrTwigVar['grandtotal'] = $grandtotal + $tax;
$arrTwigVar['totalWeight'] =  $totalWeight;

	
//$arrTwigVar['btnDeleteRows'] = $class->inputLinkButton('btnCartDeleteRows' , '<i class="fas fa-times"></i>', array('etc'=>'tabIndex="-1"','class' => 'btn btn-link mnv-delete-cart-row remove-button')); 

/*$arrTwigVar ['inputQty'] =  $class->inputNumber('qty[]');*/
$arrTwigVar ['inputDays'] =  $class->inputNumber('totalDays'); // untuk rental quotation
$arrTwigVar ['inputDiscount'] =  $class->inputHidden('discount');
$arrTwigVar ['inputShippingCost'] =  $class->inputNumber('shippingCost', array('readonly' => 'true')); 
$arrTwigVar ['inputName'] =  $class->inputText('recipientName'); 
$arrTwigVar ['labelName'] =  (isset($_POST['recipientName']) && !empty($_POST['recipientName'])) ? $_POST['recipientName'] : ''; 
$arrTwigVar ['inputPhone'] =  $class->inputText('recipientPhone'); 
$arrTwigVar ['labelPhone'] =  (isset($_POST['recipientPhone']) && !empty($_POST['recipientPhone'])) ? $_POST['recipientPhone'] : ''; 
$arrTwigVar ['inputEmail'] =  $class->inputText('recipientEmail'); 
$arrTwigVar ['labelEmail'] =  (isset($_POST['recipientEmail']) && !empty($_POST['recipientEmail'])) ? $_POST['recipientEmail'] : ''; 
$arrTwigVar ['inputAddress'] =   $class->inputTextArea('recipientAddress', array( 'etc' => 'style="height:10em"')); 
$arrTwigVar ['labelAddress'] =  (isset($_POST['recipientAddress']) && !empty($_POST['recipientAddress'])) ? str_replace(chr(13),'<br>',$_POST['recipientAddress']) : '';  
$arrTwigVar ['inputZipcode'] =   $class->inputText('recipientZipcode'); 
$arrTwigVar ['labelZipcode'] =  (isset($_POST['recipientZipcode']) && !empty($_POST['recipientZipcode'])) ? $_POST['recipientZipcode'] : '';  
$arrTwigVar ['inputWeight'] =  $class->inputDecimal('totalWeight',array('value' => $class->formatNumber($totalWeight,2), 'readonly' => 'true'));
$arrTwigVar ['inputPoint'] =  $class->inputNumber('point',array('add-class'=>'label-style','etc' => 'style="text-align:right"')); 
$arrTwigVar ['btnSubmit'] =   $class->inputSubmit('btnSave',$lang->lang['checkOut']); // untuk checkout manual

$arrTwigVar ['btnNext'] =   $class->inputButton('btnNext',$lang->lang['next']);
$arrTwigVar ['inputHidVoucherKey'] =   $class->inputHidden('hidVoucherKey');
$arrTwigVar ['inputMapAddress'] =  $class->inputText('mapAddress', array('class' => 'form-control search-address', 'etc' => 'placeholder="'.$class->lang['searchLocation'].'"')); 
$arrTwigVar ['hidLatLng'] = $class->inputHidden('hidLatLng');  

$arrTwigVar ['hidVoucherKey'] = $class->inputHidden('hidVoucherKey[]');  

	
$autoCompleteCity =  $class->inputAutoComplete(array(  
                                                            'element' => array('value' => 'hidRecipientCityName',
                                                                               'key' => 'hidRecipientCityKey'),
                                                            'source' =>array(
                                                                                'url' => 'ajax-city.php',
                                                                                'data' => array(  'action' =>'searchData' )
                                                                            ) ,
                                                            //'callbackFunction' => 'onChangeCity()', // biteship gk pake kota, jd nonaktifkan dulu
                                                            'explodeScript' => true
    
                                                          )
                                                    );  
 
$arrTwigVar ['inputCity']  = $autoCompleteCity['input'];
$arrTwigVar ['labelCity'] =  (isset($_POST['hidRecipientCityKey']) && !empty($_POST['hidRecipientCityKey'])) ? $_POST['hidRecipientCityName'] : ''; 
  
$rsShipment = $shipment->getAllShipment();
$rsShipmentService = $class->reindexDetailCollections($rsShipment,'pkey');  
$arrTwigVar ['arrShipmentService'] =  json_encode($rsShipmentService);
$arrTwigVar ['showMapOnLoad'] = ($rsShipment[0]['needlocation']) ? true : false;

//$arrCourier = array_column($rsShipment,null,'pkey'); 
$rsCourier = $shipment->searchData($shipment->tableName.'.statuskey',1);

$arrCourier = $class->convertForCombobox($rsCourier,'pkey','name');   
$arrShipmentService = $class->convertForCombobox($rsShipmentService[$rsCourier[0]['pkey']],'servicekey','servicename');  

// asumsi bukan jenis voucher berdasarkan barang
//$useVoucherPoint = $class->loadSetting('transactionVoucherPoint');	
//if($useVoucherPoint == 1){
//	$rsVoucher = (!empty(USERKEY)) ? $voucherTransaction->getAvailableVoucher(USERKEY) : array();
//	$arrVoucher = $class->convertForCombobox($rsVoucher,'pkey','voucherlabel');   
//}

$arrTwigVar ['inputCourier'] =   $class->inputSelect('selCourier', $arrCourier);
$arrTwigVar ['inputShipment'] =   $class->inputSelect('selShipmentService', $arrShipmentService);
//$arrTwigVar ['inputVoucher'] =   $class->inputSelect('selVoucher', $arrVoucher);
$arrTwigVar ['inputInsurance'] =   $class->inputCheckBox('useInsurance');

$arrTwigVar ['JSScript']  = str_replace(array('<script type="text/javascript">','</script>'),array('',''),$autoCompleteCity['script']); 
 
echo $twig->render('cart.html', $arrTwigVar);  
 
?>