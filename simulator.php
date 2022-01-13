<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  

includeClass(array('Item.class.php','DiscountScheme.class.php','OfferSimulator.class.php'));

$item = new Item();
$discountScheme = new DiscountScheme();
$offerSimulator  = new OfferSimulator();
$rsCartList = array(); 

// kalo terima GET, overwrite
if(isset($_GET) && !empty($_GET['id'])){ 
    $id = $_GET['id'];
    
    $rs = $offerSimulator->getDataRowById($id);
    if(!empty($rs)){
        $rsDetail = $offerSimulator->getDetailById($id);
        
        // updatenya nanti saja di ajax
        //$action = 'edit';
             
        $_SESSION[$class->loginSession]['simulator'] = array();
        $_SESSION[$class->loginSession]['simulator']['name'] = $rs[0]['name'];
        $_SESSION[$class->loginSession]['simulator']['hidId'] = $id;
        
        $arrDetail = array();
        $arrDetail['hidItemKey'] = array();
        $arrDetail['orderQty'] = array();
        foreach($rsDetail as $detailRow){
            array_push($arrDetail['hidItemKey'], $detailRow['itemkey']);
            array_push($arrDetail['orderQty'], $class->formatNumber($detailRow['qty']));
        } 
         
        $offerSimulator->addToCartSession($arrDetail);
        header("location: /simulator");
	    die;
    }
}

if (isset($_SESSION[$class->loginSession]) && !empty($_SESSION[$class->loginSession]['simulator'])) {
    
    $arrItemKey = array_column($_SESSION[$class->loginSession]['simulator']['detail'],'itemkey');
    
    $rsItemCol = $item->searchDataRow(array($item->tableName.'.pkey', $item->tableName.'.baseunitkey', $item->tableName.'.name', $item->tableName.'.sellingprice'),
                                ' and '.$item->tableName.'.pkey in ('.$class->oDbCon->paramString($arrItemKey,',').')'
                                );
    $discountScheme->applyDiscountScheme($rsItemCol);
    $rsItemCol = array_column($rsItemCol,null,'pkey');
     
    $_POST['name'] = $_SESSION[$class->loginSession]['simulator']['name'];
    $_POST['hidId'] = $_SESSION[$class->loginSession]['simulator']['hidId'];
    
    $subtotal = 0;
    for($i=0;$i<count($_SESSION[$class->loginSession]['simulator']['detail']); $i++){
        $itemkey = $_SESSION[$class->loginSession]['simulator']['detail'][$i]['itemkey'];
        $qty = $_SESSION[$class->loginSession]['simulator']['detail'][$i]['qty'];
        
        
        // $class->setLog($itemkey,true);
        //$class->setLog($rsItemCol[$itemkey],true);
        
        if(!isset($rsItemCol[$itemkey])) continue;
        
        $arrItem = $rsItemCol[$itemkey];  
        $sellingPrice = $arrItem['sellingprice']; 
        $rowSubtotal  = $qty * $sellingPrice;
        
        $arrImage = array();
        $rsImage = $item->getItemImage($itemkey); 
        if(!empty($rsImage))
            array_push($arrImage, array('file'=>  $rsImage[0]['file'], 'phpThumbHash' => getPHPThumbHash($rsImage[0]['file']))); 
        
        $arrItem ['image'] = $arrImage; 
        $arrItem ['inputHidItemKey'] =  $class->inputHidden('hidItemKey[]',array('value' =>  $arrItem['pkey']));
        $arrItem ['inputItemName'] =  $class->inputText('itemName[]',array('value' =>  $arrItem['name'], 'readonly'=>true));
        $arrItem ['inputQty'] =  $class->inputNumber('qty[]',array('value' =>  $qty));
        $arrItem ['inputPriceInUnit'] =  $class->inputNumber('priceInUnit[]',array('value' =>  $class->formatNumber($sellingPrice),'readonly' => true));
        $arrItem ['inputSubtotal'] =  $class->inputNumber('detailSubtotal[]',array('value' =>  $class->formatNumber($rowSubtotal),'readonly' => true));
        
        //mobile
        $arrItem ['inputQtyMobile'] =  $class->inputNumber('qtyMobile[]',array('value' =>  $qty));
        $arrItem ['inputPriceInUnitMobile'] =  $class->inputNumber('priceInUnitMobile[]',array('value' =>  $class->formatNumber($sellingPrice),'readonly' => true));
        $arrItem ['inputSubtotalMobile'] =  $class->inputNumber('detailSubtotalMobile[]',array('value' =>  $class->formatNumber($rowSubtotal),'readonly' => true));
        
        $subtotal += $rowSubtotal;
        
        array_push($rsCartList, $arrItem);
    } 
    
    $_POST['total'] = $class->formatNumber($subtotal);
    //$class->setLog($subtotal,true);
}

$arrTwigVar['cartList'] = $rsCartList;

$arrTwigVar ['inputHidDetailRowsToken'] =  $class->inputHidden('detailRowsToken[]'); // gk tau buat ap
$arrTwigVar ['inputItemName'] =  $class->inputText('itemName[]',array('disabled' => true, 'readonly'=>true)); 
$arrTwigVar ['inputQty'] =  $class->inputNumber('qty[]',array('disabled' => true)); 
$arrTwigVar ['inputPriceInUnit'] =  $class->inputNumber('priceInUnit[]',array('readonly' => true, 'disabled' => true)); 
$arrTwigVar ['inputSubtotal'] =  $class->inputNumber('detailSubtotal[]',array('readonly' => true, 'disabled' => true)); 

$arrTwigVar ['inputQtyMobile'] =  $class->inputNumber('qtyMobile[]',array('disabled' => true)); 
$arrTwigVar ['inputPriceInUnitMobile'] =  $class->inputNumber('priceInUnitMobile[]',array('readonly' => true, 'disabled' => true)); 
$arrTwigVar ['inputSubtotalMobile'] =  $class->inputNumber('detailSubtotalMobile[]',array('readonly' => true, 'disabled' => true)); 

$arrTwigVar ['inputHidItemKey'] =  $class->inputHidden('hidItemKey[]',array('disabled' => true)); 
$arrTwigVar ['inputName'] =  $class->inputText('name'); 

$arrTwigVar ['btnAddRows'] =  $class->inputLinkButton('btnAddItemRows' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button','etc' => 'attr-template="detail-row-template"'));
$arrTwigVar ['btnDeleteRows'] =  $class->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' =>  'tabIndex="-1" style="padding:6px 0;"'));
$arrTwigVar ['btnSubmit'] =   $class->inputSubmit('btnSave',$lang->lang['save']); 
$arrTwigVar ['inputTotal'] =  $class->inputNumber('total',array('readonly' => true, 'etc' => 'style="text-align:right"'));  

$arrTwigVar ['inputHidId'] =  $class->inputHidden('hidId'); 

// gk boleh ad, bentrok sama add session nanti
$_POST['action'] = (empty($_SESSION[$class->loginSession]['simulator']['hidId'])) ? 'add' : 'edit';   
$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action'); 


echo $twig->render('simulator.html', $arrTwigVar);  
 
?>