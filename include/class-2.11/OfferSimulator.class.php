<?php

class OfferSimulator extends BaseClass{
	
    function __construct(){

    parent::__construct();

    $this->tableName = 'offer_simulator_header';
    $this->tableNameDetail = 'offer_simulator_detail';
    $this->tableWarehouse = 'warehouse';   
    $this->tableCustomer = 'customer';   
    $this->tableItem = 'item'; 	
    $this->tableItemCategory = 'item_category'; 	
    $this->tableItemUnit = 'item_unit'; 	
    $this->tableItemImage = 'item_image'; 	
    $this->tableBrand = 'brand'; 	
    $this->tableStatus = 'master_status';
    $this->securityObject = 'OfferSimulator'; 

        
    $this->arrDataDetail = array(); 
    $this->arrDataDetail['pkey'] = array('hidDetailKey');
    $this->arrDataDetail['refkey'] = array('pkey','ref');
    $this->arrDataDetail['itemkey'] = array('hidItemKey',array('mandatory' => true)); 
    $this->arrDataDetail['unitkey'] = array('selUnit'); 
    $this->arrDataDetail['qty'] = array('qty','number'); 
    $this->arrDataDetail['priceinunit'] = array('priceInUnit','number'); 
    $this->arrDataDetail['total'] = array('detailSubtotal','number');
        
    $arrDetails = array();
    array_push($arrDetails, array('dataset' => $this->arrDataDetail));
        
    $this->arrData = array(); 
    $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));   
    $this->arrData['code'] = array('code');
    $this->arrData['name'] = array('name');
    $this->arrData['warehousekey'] = array('selWarehouseKey');
    $this->arrData['customerkey'] = array('hidCustomerKey'); 
    $this->arrData['trdate'] = array('trDate','date');
    $this->arrData['grandtotal'] = array('total','number');
    $this->arrData['description'] = array('description');
    $this->arrData['statuskey'] = array('selStatus');


    $this->arrDataListAvailableColumn = array(); 
    array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
    array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 130));
    array_push($this->arrDataListAvailableColumn, array('code' => 'customername','title' => 'customer','dbfield' => 'customername','default'=>true, 'width' => 150));
    array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
         
         
    $this->includeClassDependencies(array(
           'Warehouse.class.php',   
           'Customer.class.php', 
           'Item.class.php', 
           'ItemMovement.class.php',
           'ItemUnit.class.php',
           'Brand.class.php',
           'Category.class.php',
           'ItemCategory.class.php' 
    ));  
        
    }

    function getQuery(){
        
        $sql = '
            SELECT
                '.$this->tableName.'.* ,  
                '.$this->tableWarehouse.'.name as warehousename,
                '.$this->tableCustomer.'.name as customername,
                '.$this->tableStatus.'.status as statusname
                
            FROM '.$this->tableStatus.',
                 '.$this->tableWarehouse.',
                 '.$this->tableName.' left join
                    '.$this->tableCustomer.' on '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey
            WHERE   
                  '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey 

            ' .$this->criteria ;
                                         
        return $sql;
    }

     function validateForm($arr,$pkey = ''){ 
        $item = new Item();
        $arrayToJs = parent::validateForm($arr,$pkey); 
            
        $customerkey = $arr['hidCustomerKey'];  
        $name = $arr['name'];   
        $arrItemkey = $arr['hidItemKey']; 
        $arrQty = $arr['qty']; 
        $arrPriceinunit = $arr['priceInUnit'];
         
	 	if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][1]);
		} 
        
        if(empty($arrItemkey)) 
                 $this->addErrorList($arrayToJs,false,  $this->errorMsg[501]);  
        
        if(empty($customerkey))
                $this->addErrorList($arrayToJs,false,$this->errorMsg['customer'][1]);


            $arrDetailKeys = array(); 

            for($i=0;$i<count($arrItemkey);$i++) { 
              /*  if (empty($arrItemkey[$i]) ){ 
                    $this->addErrorList($arrayToJs,false, $this->errorMsg['item'][1]); 	
                } */

                if (!empty($arrItemkey[$i])){
                    /*$rsItem = $item->getDataRowById($arrItemkey[$i]);
                    if ($this->unFormatNumber($arrQty[$i]) <= 0){ 
                        $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg[510]);  
                    }*/

                   /* $priceMandatory = $this->loadSetting('priceMandatory');
                    if ($priceMandatory == 1 && $this->unFormatNumber($arrPriceinunit[$i]) <= 0){  
                        $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg[511]);  
                    } */ 
                    
                    if (in_array($arrItemkey[$i],$arrDetailKeys)){  
                        $rsItem = $item->getDataRowById($arrItemkey[$i]);
                        $this->addErrorList($arrayToJs,false, $rsItem[0]['name'].'. '.$this->errorMsg[215]); 	 
                    }else{ 
                        array_push($arrDetailKeys, $arrItemkey[$i]);
                    } 

                }
            }
        return $arrayToJs;
    }

      function reCountSubtotal($arrParam){


            $subtotal = 0 ;
            $grandtotal = 0;

            $arrItemKey = $arrParam['hidItemKey'];

            $arrQty = $arrParam['qty']; 
            $arrPriceinunit = $arrParam['priceInUnit']; 
            $arrTransUnitKey = $arrParam['selUnit']; 

            $arrItemDetail = array();
            $item = new Item();
            $totalGramasi = 0;
        
            for ($i=0;$i<count($arrItemKey);$i++){

                if (empty($arrItemKey[$i]))  
                    continue; 

                    $rsItem = $item->getDataRowById($arrItemKey[$i]);
 
                    $itemkey = $arrItemKey[$i];
                    $transactionUnitKey = $arrTransUnitKey[$i];
                    $baseunitkey = $rsItem[0]['baseunitkey']; 
                    $qty =  $this->unFormatNumber($arrQty[$i]);
                    $conversionMultiplier = $item->getConvMultiplier($itemkey,$transactionUnitKey,$baseunitkey); 
                    $qtyinbaseunit = $qty * $conversionMultiplier;
                    $priceInUnit = $this->unFormatNumber($arrPriceinunit[$i]);

 
                    $gramasi = $rsItem[0]['gramasi'];
                    if ($rsItem[0]['weightunitkey'] == UNIT['kg'])
                        $gramasi *= 1000;
                
                    $arrItemDetail[$i]['baseUnitKey'] = $baseunitkey;
                    $arrItemDetail[$i]['unitConvMultiplier'] = $conversionMultiplier;
                    $arrItemDetail[$i]['qtyInBaseUnit'] = $qtyinbaseunit ; 
                    $arrItemDetail[$i]['priceInBaseUnit'] = $priceInUnit / $conversionMultiplier ;
                    $arrItemDetail[$i]['weight'] = $gramasi ; 
   
                    $detailSubtotal = $qty * $priceInUnit ;
                    $arrItemDetail[$i]['detailSubtotal'] = $detailSubtotal; 
				    $arrItemDetail[$i]['itemType'] = $rsItem[0]['itemtype']; 

                    $subtotal += $detailSubtotal ; 
                
                
                    $totalGramasi += ($qty * $gramasi);
            } 

            $grandtotal = $subtotal;
          
            $reCountResult = array();
            $reCountResult['detailCOGS'] = $arrItemDetail;
            $reCountResult['grandtotal'] = $grandtotal;


            return $reCountResult;

    } 

    
    
    function normalizeParameter($arrParam, $trim=false){
         
        
        $arrItemkey = $arrParam['hidItemKey'];
		
        $reCountResult = $this->reCountSubtotal($arrParam); 
		$arrParam['detailCOGS'] = $reCountResult['detailCOGS']; 
        $arrParam['total'] = $reCountResult['grandtotal'];

		 for ($i=0;$i<count($arrItemkey);$i++){  
                $qtyinbaseunit = $arrParam['detailCOGS'][$i]['qtyInBaseUnit'];  
                $arrParam['qtyInBaseUnit'][$i] = $qtyinbaseunit;  
                $arrParam['detailSubtotal'][$i] = $arrParam['detailCOGS'][$i]['detailSubtotal']; 
          }
		
        $arrParam = parent::normalizeParameter($arrParam,true);  
		
        return $arrParam;
    }



     function getDetailWithRelatedInformation($pkey,$criteria=''){
          $sql = 'select
                        '.$this->tableNameDetail .'.*, 
                        '.$this->tableItem.'.name as itemname, 
                        '.$this->tableItem.'.code as itemcode, 
                        '.$this->tableItem.'.shortdescription as itemshortdescription, 
                        '.$this->tableItem.'.brandkey, 
                        '.$this->tableItem.'.gramasi, 
                        '.$this->tableBrand.'.name as brandname ,
                        '.$this->tableItem.'.deftransunitkey,
                        '.$this->tableItemCategory.'.pkey as itemcategorykey,
                        '.$this->tableItemCategory.'.name as itemcategoryname,
                        '.$this->tableItemUnit.'.code as unitcode,
                        '.$this->tableItemUnit.'.name as unitname,
                         baseunit.name as baseunitname
                      from
                        '.$this->tableNameDetail .',
                        '.$this->tableItemUnit.',
                        '.$this->tableItemCategory.',
                        '.$this->tableItemUnit.' baseunit,
                        '.$this->tableItem.'
                            left join '.$this->tableBrand.' on 	' . $this->tableItem .'.brandkey = '.$this->tableBrand.'.pkey 
                      where
                        '.$this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and
                        '.$this->tableNameDetail.'.unitkey = '.$this->tableItemUnit.'.pkey and
                        '.$this->tableItem.'.baseunitkey = baseunit.pkey and
                        '.$this->tableItem.'.categorykey = '.$this->tableItemCategory.'.pkey and
                '.$this->tableNameDetail .'.refkey in ('.$this->oDbCon->paramString($pkey,',') . ') ';

                $sql .= $criteria;

                $sql .= ' ' .$orderby;

                return $this->oDbCon->doQuery($sql);

    }
      
     function addToCartSession($arr){   	 
 
         
        for($j=0;$j<count($arr['hidItemKey']);$j++){

             $qty = $this->unFormatNumber($arr['orderQty'][$j]);
             $itemkey = $arr['hidItemKey'][$j];
 
            if ($qty <= 0 || !is_numeric($qty)) continue;

            $ctr = isset($_SESSION[$this->loginSession]['simulator']['detail']) ? count($_SESSION[$this->loginSession]['simulator']['detail']) : 0;
            
            //cari apakah ad item yg sama
            $haveSameItem = false;
            
            // kalo dr product detail
            if(isset($arr['name']))
                $_SESSION[$this->loginSession]['simulator']['name'] = $arr['name'];
                
            for($i=0;$i<$ctr;$i++){ 
                if ($_SESSION[$this->loginSession]['simulator']['detail'][$i]['itemkey'] == $itemkey){
                     $_SESSION[$this->loginSession]['simulator']['detail'][$i]['qty'] += $qty;
                     //$this->addToTemporaryCart($itemkey,$_SESSION[$this->loginSession]['simulator'][$i]['qty']);
                     $haveSameItem = true; 
                 } 
            }

            if(!$haveSameItem){
                $_SESSION[$this->loginSession]['simulator']['detail'][$ctr]['itemkey'] = $itemkey;
                $_SESSION[$this->loginSession]['simulator']['detail'][$ctr]['qty'] = $qty;

                //$this->addToTemporaryCart($_SESSION[$this->loginSession]['simulator'][$ctr]['itemkey'],$_SESSION[$this->loginSession]['simulator'][$ctr]['qty']);
            }

        }

        return true;

    } 
    
    
    function getDetailForAPI($arrKey, $arrIndex = array()){
        if(in_array('detail', $arrIndex)){
            $rsDetailsCol = array();
            $rsDetails = $this->getDetailWithRelatedInformation($arrKey); 
            $rsDetails = $this->reindexDetailCollections($rsDetails,'refkey'); 
            $rsDetailsCol['detail'] = $rsDetails;
        }
        
		 if(in_array('image_url', $arrIndex)){ 
                $rsDetailsCol = array(); 
			 
                $rsDetails = $this->getItemImagesForAPI($arrKey);
                $rsDetails = $this->reindexDetailCollections($rsDetails,'detailkey');  
			 
			 	//$this->setLog($rsDetails,true);
                $rsDetailsCol['image_url'] = $rsDetails;
         }
		
        return $rsDetailsCol;
    }
	 
	function getItemImagesForAPI($arrPkey){
        // yg dibalikin harus pkey detailnya 
		
        $sql = 'select 
				'.$this->tableNameDetail.'.pkey as detailkey,
	   			'.$this->tableItemImage .'.pkey,
	   			'.$this->tableItemImage .'.refkey,
	   			'.$this->tableItemImage .'.file 
			  from 
			  	'.$this->tableNameDetail.',
				'.$this->tableItemImage.'  
			  where   
			  	'.$this->tableNameDetail.'.pkey in ('.$this->oDbCon->paramString($arrPkey,',').') and
				'.$this->tableNameDetail.'.itemkey = '.$this->tableItemImage.'.refkey';
    
        if (!empty($criteria))  
            $sql .=  ' ' .$criteria; 
  
		$rs = $this->oDbCon->doQuery($sql);
            
        $total = count($rs);    
        for($i=0;$i<count($rs);$i++)
            $rs[$i]['url'] = HTTP_HOST.'download/item/'.$rs[$i]['refkey'].'/'.$rs[$i]['file'];
        
        return $rs;
     
    }
    
  /* function addToTemporaryCart($arr){
         
        $userkey = (isset($_SESSION[$this->loginSession]['id']) && !empty( $_SESSION[$this->loginSession]['id'])) ? base64_decode($_SESSION[$this->loginSession]['id']) : '';
        if (empty($userkey)) return;

         try{

            if(!$this->oDbCon->startTrans())
                throw new Exception($this->errorMsg[100]);
 
            // harus cek user dan kepemilikannya ?
            // kalo gk ad pkey,
             
            if(!isset($arr['hidId']) || empty($arr['hidId'])){
                
            }
            
            $this->oDbCon->endTrans(); 
            $this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']); 


        } catch(Exception $e){
            $this->oDbCon->rollback();
            $this->addErrorList($arrayToJs,false,$e->getMessage());
        }		
       
        
        $sql  = 'select * from '.$this->tableCartTemp.'
                 where refkey = '.$this->oDbCon->paramString($userkey).' 
                 and itemkey = '.$this->oDbCon->paramString($itemkey);

        $rs =   $this->oDbCon->doQuery($sql);

        if (empty($rs)){
             $sql  = 'insert '.$this->tableCartTemp.' 
              (`refkey`,`itemkey`,`qty`) values 
              ('.$this->oDbCon->paramString($userkey).', '
                .$this->oDbCon->paramString($itemkey).','
                .$this->oDbCon->paramString($qty).')';
        }else{
            $sql  = 'update '.$this->tableCartTemp.' 
                     set qty = '.$this->oDbCon->paramString($qty).'
                     where refkey = '.$this->oDbCon->paramString($userkey).' 
                     and itemkey = '.$this->oDbCon->paramString($itemkey);

        }

        $this->oDbCon->execute($sql);
 
    }*/

}

?>