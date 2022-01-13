<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/SalesOrder.class.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Item.class.php'; 

$customer = new Customer();
$salesOrder = new SalesOrder();
$item = new Item();

require_once '_global.php'; 

if(!in_array($ACTION,array('POST','GET'))) endForRequestMethodError();   
    
$RETURN_VALUE = array();

switch($ACTION){
    case 'POST' :
        
        if(empty($postVars['customer_id'])) endForRequestMethodError();
        
        $customerCode = $postVars['customer_id'];
        $itemRow = $postVars['detail'];
        $arrItemCode  = array_column($itemRow,'item_id');
             
        $rsCustomer = $customer->searchDataRow(array('pkey'), ' and '. $customer->tableName .'.code = '.$class->oDbCon->paramString($customerCode));
        $customerkey = $rsCustomer[0]['pkey'];
        
        $rsItem = $item->searchDataRow(array('pkey','code'), ' and '. $item->tableName .'.code in ('.$class->oDbCon->paramString($arrItemCode,',').')');
        $rsItemCol = array_column($rsItem,null,'code');
        
        // pake session
        $_SESSION[$class->loginSession]['id'] = base64_encode($customerkey);
        $_SESSION[$class->loginSession]['cart'] = array();
        
        foreach($itemRow as $row){
            if(!isset($rsItemCol[$row['item_id']])) continue;
            
            $itemkey = $rsItemCol[$row['item_id']]['pkey']; 
            
            array_push($_SESSION[$class->loginSession]['cart'], array('itemkey' => $itemkey, 'qty' => $row['qty']));
        }
        
        $salesOrder->updateTemporaryCart();
        
          $RETURN_VALUE['response_code'] = 200 ; //($hasSuccessValue) ? 200 : 409;
//        $RETURN_VALUE['success_rows'] = count($ARR_RETURN_VALUE);
//        $RETURN_VALUE['success_data'] = $ARR_RETURN_VALUE;
//        $RETURN_VALUE['failed_rows'] = count($arrFailed);
//        $RETURN_VALUE['failed_data'] = $arrFailed;


        break;
        
        
    case 'GET':
            $customerCode = $_GET['customer_id'];

            $rsCustomer = $customer->searchDataRow(array('pkey'), ' and '. $customer->tableName .'.code = '.$class->oDbCon->paramString($customerCode));
            $customerkey = $rsCustomer[0]['pkey'];

            $rs = $salesOrder->getAbandonedCart($customerkey);
        
            $rsItem = $item->searchDataRow(array($item->tableName.'.pkey',$item->tableName.'.sellingprice'),
                                            ' AND '.$item->tableName.'.pkey in ('.$class->oDbCon->paramString( array_column($rs,'itemkey') ,',').')'
                                          );
        
            $discountScheme = new DiscountScheme();  
            $discountScheme->applyDiscountScheme($rsItem);
            $rsItemCol = array_column($rsItem,null,'pkey');
        

            $rsDetails = $item->getItemImagesForAPI(array_column($rsItem,'pkey')); 
            $arrImagesCol = $item->reindexDetailCollections($rsDetails,'refkey'); 
 
        
            $ARR_RETURN_VALUE = array();
            foreach($rs as $rows){
                if(!isset($rsItemCol[$rows['itemkey']])) continue;
                
                $rsItem = $rsItemCol[$rows['itemkey']]; 
                $rsImage = $arrImagesCol[ $rows['itemkey'] ];
                
                $arrImages = array(); 
                foreach($rsImage as $imgRow)
                    array_push($arrImages, array('key' => $imgRow['pkey'], 'url' => $imgRow['url']));
                
                array_push($ARR_RETURN_VALUE, array(
                                                        'item_id' => $rows['itemcode'],
                                                        'item_name' => $rows['itemname'],
                                                        'item_weight' => $rows['itemweight'],
                                                        'item_weight_unit' => $rows['itemweightunit'],
                                                        'item_weight_unit_id' => $rows['itemweightunitcode'],
                                                        'qty' => $rows['qty'],
                                                        'selling_price' => $rsItem['sellingprice'],
                                                        'original_selling_price' => $rsItem['originalsellingprice'],
                                                        'has_disc' => $rsItem['hasdisc'],
                                                        'disc_percentage' => $rsItem['discpercentage'],
                                                        'images' => $arrImages
                                                )
                                        );
            }
        
            $RETURN_VALUE['response_code'] =  (!empty($ARR_RETURN_VALUE)) ? 200 : 409; 
            $RETURN_VALUE['data'] = $ARR_RETURN_VALUE;

            break;
        
}


http_response_code($RETURN_VALUE['response_code'] );
echo json_encode($RETURN_VALUE); 
 
die;
?>