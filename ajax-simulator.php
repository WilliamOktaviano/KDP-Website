<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';

includeClass(array('OfferSimulator.class.php','Customer.class.php','Warehouse.class.php','Item.class.php','DiscountScheme.class.php'));

$offerSimulator = new OfferSimulator();
$customer = new Customer();
$warehouse = new Warehouse();
$item = new Item();
$discountScheme = new DiscountScheme();

if(!isset($_POST) || empty($_POST['action'])) die;

$arrayToJs = array();  


switch ($_POST['action']){ 
    
    case 'addToCart' : 
	 
				$arr = array(); 
				foreach ($_POST as $k => $v) $arr[$k] = $v; 
				
                // utk compability. bentrok dengna cart kalo camel
                if(isset( $arr['hiditemkey'])) $arr['hidItemKey'] = $arr['hiditemkey'];
        
				$arrayToJs = $offerSimulator->addToCartSession($arr);  
				break;
		 
    case 'updateCartList' :
                $_SESSION[$class->loginSession]['simulator']  = array();
        				
                $arr = array(); 
				foreach ($_POST as $k => $v) $arr[$k] = $v; 
				
                // utk compability. bentrok dengna cart kalo camel
                if(isset( $arr['hiditemkey'])) $arr['hidItemKey'] = $arr['hiditemkey'];
        
        
                $_SESSION[$class->loginSession]['simulator']['name'] = $arr['name'];
                $_SESSION[$class->loginSession]['simulator']['hidId'] = $arr['hidId'];
                $_SESSION[$class->loginSession]['simulator']['detail'] = array();
        
                $arrDetail = array();
                for($i=0;$i<count($arr['hidItemKey']);$i++){
                    $arrDetail[$i]['itemkey'] = $arr['hidItemKey'][$i];  
                    $arrDetail[$i]['qty'] = $arr['qty'][$i];          
                }
         
                $_SESSION[$class->loginSession]['simulator']['detail'] = $arrDetail;

                //$class->setLog($_SESSION[$class->loginSession]['simulator'],true);
                break;
        
    case 'add' :
                    $arr = array(); 
        
                    foreach ($_POST as $k => $v) {
                        $arr[$k] = $v; 
                    }
        
                    $arrReturn = array(); 
                    $customerkey = (!empty(USERKEY)) ? USERKEY : 1;

                    $arr['hidCustomerKey'] = $customerkey;

                    $arr['code'] = 'xxxxxx';    
                    $trDate = date('d / m / Y');
                    $arr['trDate'] = $trDate;  
                    $arr['selWarehouseKey'] =  $warehouse->getDefaultData();

                    $arr['trDesc'] = '';
                    $arr['name'] = (empty($arr['name'])) ? 'Project-'.$customerkey.'-'.mktime() : $arr['name'];
                    $arr['createdBy'] = 0;
                    $arr['fromFE'] = 1;

                    $rsItem = $item->searchDataRow(array($item->tableName.'.pkey', $item->tableName.'.baseunitkey',$item->tableName.'.sellingprice'),
                                          ' and '.$item->tableName.'.pkey in ('.$class->oDbCon->paramString($arr['hidItemKey'],',').')'
                                          );
                    $discountScheme->applyDiscountScheme($rsItem);
    
        
                    $arrItem = array_column($rsItem,null,'pkey');

                    for ($i=0;$i<=count($arr['hidItemKey']);$i++){
                        if(empty($arr['hidItemKey'][$i])) continue;

                        $rsItem =  $arrItem[$arr['hidItemKey'][$i]];

                        $arr['hidDetailKey'][$i] = 0;
                        $arr['selUnit'][$i] = $rsItem['baseunitkey'];
                        $arr['priceInUnit'][$i] = $rsItem['sellingprice'];  
                    }

                    $arrReturn = $offerSimulator->addData($arr); 
                    $_SESSION[$class->loginSession]['simulator']  = array();

                    echo json_encode($arrReturn);  

                    break;
        
         case 'edit' :
                    $arr = array(); 
        
                    foreach ($_POST as $k => $v) 
                        $arr[$k] = $v;
        
                    $arrReturn = array(); 
        
                    // refill ulang data default
                    $rs = $offerSimulator->getDataRowById($arr['hidId']);
                      
                    if(empty($rs)) die;
        
                    // cek ulang customernya sama gk
                    
                    $customerkey = (!empty(USERKEY)) ? USERKEY : 1;
                    if($rs[0]['customerkey'] <> $customerkey) die;
        

                    $arr['code'] = $rs[0]['code'] ;    
                    $arr['selWarehouseKey'] = $rs[0]['warehousekey'] ;    
                    $arr['hidCustomerKey'] = $rs[0]['customerkey'] ;  
                    $arr['hidCustomerKey'] = $rs[0]['customerkey'] ;
                    $arr['hidModifiedOn'] =  $rs[0]['modifiedon'] ;
         
                    $arr['name'] = (empty($arr['name'])) ? 'Project-'.$customerkey.'-'.mktime() : $arr['name'];
                    //$arr['createdBy'] = 0;
                    $arr['fromFE'] = 1;

                    $rsItem = $item->searchDataRow(array($item->tableName.'.pkey', $item->tableName.'.baseunitkey',$item->tableName.'.sellingprice'),
                                          ' and '.$item->tableName.'.pkey in ('.$class->oDbCon->paramString($arr['hidItemKey'],',').')'
                                          );
                    $discountScheme->applyDiscountScheme($rsItem);
    
        
                    $arrItem = array_column($rsItem,null,'pkey');

                    for ($i=0;$i<=count($arr['hidItemKey']);$i++){
                        if(empty($arr['hidItemKey'][$i])) continue;

                        $rsItem =  $arrItem[$arr['hidItemKey'][$i]];

                        $arr['hidDetailKey'][$i] = 0;
                        $arr['selUnit'][$i] = $rsItem['baseunitkey'];
                        $arr['priceInUnit'][$i] = $rsItem['sellingprice'];  
                    }

                    $arrReturn = $offerSimulator->editData($arr); 
                    $_SESSION[$class->loginSession]['simulator']  = array();

                    echo json_encode($arrReturn);  

                    break;
        
}


die; 

?>
