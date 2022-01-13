<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('Item.class.php',  
                   'ItemUnit.class.php',
                   'ItemMovement.class.php',
                  ));

$item = createObjAndAddToCol(new Item()); 
$itemMovement = createObjAndAddToCol(new ItemMovement()); 

$obj = $item;   

if (isset($_GET) && !empty($_GET['action'])) {
			switch ( $_GET['action']){ 
              
                case 'searchData' :  
                    
                            $order = 'order by item.name asc'; 
                            $term = ''; 
                            $criteria = ''; 
                    
                            $arrCriteria = array();

                            if (isset($_GET) && !empty($_GET['term'])){
                                $term = $_GET['term'];

                                if (isset($_GET)  && !empty($_GET['exact']) && $_GET['exact'] == 1)
                                     $criteria = $obj->tableName.'.name = '.$obj->oDbCon->paramString($term).' or '.$obj->tableName.'.code = '.$obj->oDbCon->paramString($term);
                                else
                                     $criteria = $obj->tableName.'.name like '.$obj->oDbCon->paramString('%'.$term.'%').' or '.$obj->tableName.'.code like '.$obj->oDbCon->paramString('%'.$term.'%'). ' or '.$obj->tableBrand.'.name like '.$obj->oDbCon->paramString('%'.$term.'%').' or '.$obj->tableName.'.tag like '.$obj->oDbCon->paramString('%'.$term.'%').' or '.$obj->tableName.'.shortdescription like '.$obj->oDbCon->paramString('%'.$term.'%');
 
                                array_push($arrCriteria,'('.$criteria.')') ;
                            }


                            if (isset($_GET) && !empty($_GET['isparent']) && $_GET['isparent'] == 1)
                                  array_push ($arrCriteria,$obj->tableName.'.parentkey = 0 ' );

                            // bedakan parameter kosong atau tdk pernah dikirim 
                            if (isset($_GET['pkey'])){ 
                                 // kalo kirim pkey tp nilainya kosong
                                 // gk boleh tampilkan semuai item, harus return empty result
                                 // kalo mau search semua data, jgn kirim pkey
                                 $_GET['pkey'] = (empty($_GET['pkey'])) ? 0 : $_GET['pkey'];
                                 array_push ($arrCriteria, $obj->tableName.'.pkey = ' . $obj->oDbCon->paramString($_GET['pkey']) );  
                            } 

                            if (isset($_GET['itemtype'])){  
                                 $_GET['itemtype'] = (empty($_GET['itemtype'])) ? 1 : $_GET['itemtype'];
                                 $itemtype = explode(',',$_GET['itemtype']);
                                 array_push ($arrCriteria, $obj->tableName.'.itemtype in (' . $obj->oDbCon->paramString($itemtype,',') .')' );  
                            } 
 
                            if (isset($_GET['serviceCost'])){  
                                 $_GET['serviceCost'] = (empty($_GET['serviceCost'])) ? 0 : 1;
                                 array_push ($arrCriteria, $obj->tableName.'.servicecost = ' . $obj->oDbCon->paramString($_GET['serviceCost']) );  
                            } 
                     
                            // sementara
                            array_push ($arrCriteria, $obj->tableName.'.pkey <> ' . DEFAULT_COST['outsourceDownpayment']);
                     
                            if (isset($_GET['moduleCost'])){  
                                 $module = explode(',',$_GET['moduleCost']);  
                                 $showInCriteria = array();
                                 
                                for($i=0;$i<count($module);$i++){
                                     
                                     switch ($module[$i]){ 
                                             /*case 'depot' : $field = 'showindepot';
                                                            break;
                                             case 'terminal' : $field = 'showinterminal';
                                                            break;*/
                                             case 'trucking' : $field = 'showintrucking';
                                                             // di SPK, harga fixed gk boleh muncul
                                                            //array_push ($arrCriteria, $obj->tableName.'.fixedcost = 0' );  
                                                            break;
                                             case 'costRate' : $field = 'showincostrate';
                                                            break;
                                             case 'shippingCompany' : $field = 'showinshippingcompany';
                                                            break;
                                             default :  $field = '';
                                     }
                                     
                                    array_push ($showInCriteria, $obj->tableName.'.'.$field.' = 1');
                                 }
                                
                                 $showInCriteria = implode(' or ', $showInCriteria);
                                 $showInCriteria = (!empty($showInCriteria)) ? '('.$showInCriteria.')' : '';
                                
                                
                                
                                 if (!empty($showInCriteria))
                                  array_push ($arrCriteria, $showInCriteria );  
                            } 

                          /*  if (isset($_GET) && !empty($_GET['chargePer']))
                                  array_push ($arrCriteria, $obj->tableName.'.chargetype = ' . $obj->oDbCon->paramString($_GET['chargePer']) );  */
 
                    
                            if (isset($_GET) && !empty($_GET['categorykey']))
                                  array_push ($arrCriteria, $obj->tableName.'.categorykey = ' . $obj->oDbCon->paramString($_GET['categorykey']) );  

                            // hanya ambil item yg aktif
                            array_push ($arrCriteria,  $obj->tableName.'.statuskey = 1 ' );  
                                           
                            $criteria = implode(' and ', $arrCriteria);
                            
                            $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';  
                                                  
                            $rsItem = array();
                            $rsItem = $obj->searchDataForAutoComplete('','',false,$criteria,$order );


                            for($i=0;$i<count($rsItem);$i++){ 
                                    if (isset($_GET) && !empty($_GET['getQOH'])){ 
                                            $warehousekey = '';
                                            if (isset($_GET) && !empty($_GET['warehousekey']))
                                                $warehousekey = $_GET['warehousekey'];

                                            $trdate = '';
                                            if (isset($_GET) && !empty($_GET['trdate']))
                                                $trdate = $_GET['trdate'];

                                            $qoh = $itemMovement->sumItemMovement($rsItem[$i]['pkey'],$warehousekey,$trdate);
                                            $rsItem[$i]['qoh'] = $qoh;
                                    } 

                                    $rsItem[$i]['value'] = htmlspecialchars_decode($rsItem[$i]['value']); 
                            }
                       
                        // jika add outsource trucking (utk report cost)
                        if (isset($_GET) && !empty($_GET['addOutsource'])){
                            array_push($rsItem,array('pkey' => 0, 'value' => $obj->lang['truckingFee']));
                        }
                         
                        echo json_encode($rsItem); 
                        break;
                    
                    
                case 'getAvailableConversion' : 
                         
                        if (!isset($_GET) ||  empty($_GET['itemkey']))  
                            die; 
                            
                        $rs = $obj->getItemUnitConversion($_GET['itemkey']);
                        
                        echo json_encode($rs); 
                        break;
                 
                case 'getUnitSellingPrice' : 
                         
                        if (!isset($_GET) || empty($_GET['itemkey']) || empty($_GET['unitkey']) )  die; 
                             
                        $itemkey = $_GET['itemkey'];
                        $unitkey = $_GET['unitkey'];
                         
                        $useLastSellingPrice = (!empty($_GET['lastsellingprice']) && $_GET['lastsellingprice'] == 1) ? true : false;
                    
                        $rs = $obj->getItemUnitConversion($itemkey, $unitkey);
                        $rs = array_column($rs,'sellingprice','conversionunitkey'); // karena by default dia balikin satuan base unit jg, jd 2 row
                    
                        $sellingPrice = $rs[$unitkey];
                     
                        if($useLastSellingPrice){
                            $lastSellingSetting = $obj->loadSetting('rememberLatestSellingPrice');
                            
                            if($lastSellingSetting == 1){ 
                                $customerkey = $_GET['customerkey'];

                                includeClass(array('SalesOrder.class.php'));
                                $salesOrder = new SalesOrder();
                                $latestPrice = $salesOrder->getlatestSellingPrice($itemkey,$unitkey,$customerkey);
                                if ($latestPrice > 0) $sellingPrice = $latestPrice;
                            }
                        }
                    
                        echo json_encode($sellingPrice); 
                        break;
			          
				case 'getTimeUnitSellingPrice' : 
                         $criteria = '';
                        if (!isset($_GET) || empty($_GET['itemkey']) || empty($_GET['timeunitkey']) )  die; 
						
					if (!empty($_GET['timeunitkey']))
            			$criteria = ' and timeunitkey = ' .$obj->oDbCon->paramString($_GET['timeunitkey']); 
                             
                        $rs = $obj->getTimeDetail($_GET['itemkey'], $criteria);
                        $rs = array_column($rs,'sellingprice','timeunitkey');
					
                        echo json_encode($rs[$_GET['timeunitkey']]); 
                        break;
                 
               case 'getAvailableUnit' : 
                         
                        if (!isset($_GET) ||  empty($_GET['itemkey']))  
                            die; 
                            
                        $rs = $obj->getAvailableUnit($_GET['itemkey']);
                        
                        echo json_encode($rs); 
                        break;
                 
		
				case 'getAvailableTimeUnit' : 
                         
                        if (!isset($_GET) ||  empty($_GET['itemkey']))  
                            die; 
                            
                        $rs = $obj->getTimeDetail($_GET['itemkey']);
                        
                        echo json_encode($rs); 
                        break;

                case 'getDataRowById' :
                     
                    if (!isset($_GET['pkey'])) die; 
                    $pkey = $_GET['pkey'];
                    
                    $arrCriteria = array();
                    array_push ($arrCriteria, $obj->tableName.'.pkey = ' .  $obj->oDbCon->paramString($pkey));   
                    $criteria = implode(' and ', $arrCriteria);  
                    $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';   
 
                    $rsData = $obj->searchData('','',true,$criteria); 
                    
                    echo json_encode($rsData); 
                    break;   
                    
                case 'getItemPackageOfContent'   :
                    
                    if (!isset($_GET['pkey'])) die; 
                    $pkey = $_GET['pkey'];
                    
                    $rsData = $obj->getItemPackageOfContent($pkey);   
                    
                    echo json_encode($rsData); 
                    break;   
                    
                /*case 'searchVendorPartNumber' : 
                    
                    //if (!isset($_GET['pkey'])) die; 
                    //$pkey = $_GET['pkey'];
                    
                    $arrCriteria = array();
                    if (isset($_GET) && !empty($_GET['term'])){
                        $term = $_GET['term'];

                        $criteria = $obj->tableVendorPartNumber.'.partnumber like '.$obj->oDbCon->paramString('%'.$term.'%');

                        array_push($arrCriteria,'('.$criteria.')') ;
                    }
                    
                    $criteria = implode(' and ', $arrCriteria);
                    $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';  
                    $rsItem = $obj->searchVendorPartNumberForAutoComplete('',$criteria);
                    
                    echo json_encode($rsItem); 
                    break;
                    
                case 'searchSerialNumberInMarket' : 
                    
                    if (!isset($_GET['sn'])) die; 
                    $sn = $_GET['sn'];
                    
                    $rsItem = $obj->searchSerialNumber('','', $sn, '', ' and warehousekey = 0 ' );
                    echo json_encode($rsItem); 
                    break;
                    
                case 'searchAvailableSerialNumber' : 
                    
                    if (!isset($_GET['sn'])) die; 
                    $sn = $_GET['sn'];
                    
                    $rsItem = $obj->searchSerialNumber('','', $sn, '', ' and warehousekey <> 0 ' );
                    echo json_encode($rsItem); 
                    break;*/
             
                case 'getMarketplaceCategoryAttributes' :
                      if (!isset($_GET) ||  empty($_GET['pkey'])) die; 
                    
                      $rs = $obj->getMarketplaceCategoryAttributes($_GET['pkey']);
                    
                      $arrAttributes = array();
                      foreach($rs as $row){
                          $marketplacekey = $row['marketplacekey'];
                          $attributekey = $row['attributekey']; 
                          $arrAttributes[$marketplacekey][$attributekey] = $row['value'];
                      }
                    
                      echo json_encode($arrAttributes); 
                      break;
                    
               /* case 'getCache' :
                    $sql = 'select pkey,name from item limit 100';
                    $rs = $obj->oDbCon->doQuery($sql);
                    $rs = array_column($rs,null,'name');
                    echo json_encode($rs); 
                    break;*/

                    
                                       
                case 'getTruckingCostDefaultPrice' : 
                    
                    if (empty($_GET['itemkey'])) die;
                    
                    $arrServiceDetail = json_decode($_GET['servicedetail'],true);
                         
                    $_GET['itemkey'] =  (empty($_GET['itemkey'])) ? '' : $_GET['itemkey']; 
                    
                    $arrCriteria = array();
                    
                    if(isset($_GET['jobcategorykey']) && !empty($_GET['jobcategorykey']))
                       $arrCriteria['jobcategorykey'] = $_GET['jobcategorykey'];
                
					if(!empty($arrServiceDetail))
                       $arrCriteria['servicedetail'] = $arrServiceDetail;
                    
                    if(isset($_GET['terminalkey']) && !empty($_GET['terminalkey']))
                       $arrCriteria['terminalkey'] = $_GET['terminalkey'];
                    
                    if(isset($_GET['depotkey']) && !empty($_GET['depotkey']))
                       $arrCriteria['depotkey'] = $_GET['depotkey'];
                
                    $rs = $obj->getTruckingCostDefaultPrice($_GET['itemkey'],$arrCriteria); 
                    
                    echo json_encode($rs); 
                    break; 
            }
     
}
 
die;
  
?>
