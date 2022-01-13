<?php
  
class DiscountScheme extends BaseClass{ 
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'discount_scheme_header';
		$this->tableNameDetail = 'discount_scheme_detail';
		$this->tableWarehouse = 'warehouse';
		$this->tableItem = 'item';
		$this->tableItemUnit = 'item_unit';
		$this->tableItemImage = 'item_image';
		$this->tableStatus = 'master_status';
		    
		$this->securityObject = 'DiscountScheme'; 
       
        $this->arrDataDetail = array();  
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['itemkey'] = array('hidItemKey');
        $this->arrDataDetail['discounttype'] = array('selDiscountType');
        $this->arrDataDetail['discount'] = array('discountValue','number');
         
       
        $this->arrData = array();        
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => array('dataset' => $this->arrDataDetail)));
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['trdatestart'] = array('trStartDate','date');
        $this->arrData['trdateend'] = array('trEndDate','date');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');
	          
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'startdate','title' => 'startDate','dbfield' => 'trdatestart','default'=>true, 'align' =>'center','format' => 'date', 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'enddate','title' => 'endDate','dbfield' => 'trdateend','default'=>true,  'align' =>'center','format' => 'date', 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'description','title' => 'note','dbfield' => 'trdesc',  'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        
        $this->printMenu = array();  
//        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/itemAdjustment'));
 
        $this->includeClassDependencies(array( 
              'Item.class.php', 
              'ItemCategory.class.php',
        ));
 
        $this->overwriteConfig();
   }
   
    function getQuery(){
	   
	   $sql =  '
			SELECT '.$this->tableName.'.* ,
			   '.$this->tableStatus.'.status as statusname
			FROM '.$this->tableStatus.', '.$this->tableName.'
			WHERE '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey 
 	      '.$this->criteria ;  
         
        return $sql;
    }
	  
    function afterStatusChanged($rsHeader){   

    }
     
    
    function validateForm($arr,$pkey = ''){
		  
		$arrayToJs = parent::validateForm($arr,$pkey); 
         
        $item = new Item();
         
        $warehouseke = $arr['selWarehouseKey'];
		$arrItemkey = $arr['hidItemKey'];  
		$arrDiscountValue = $arr['discountValue'];  

        $name = $arr['name'];   
			 
		$rs = $this->isValueExisted($pkey,'name',$name); 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][1]);
		}else if (count($rs) <> 0){  
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][2]);
		} 
        
        
        $arrDetailKeys = array(); 
		for($i=0;$i<count($arrItemkey);$i++) {
		 	if (empty($arrItemkey[$i]) ){ 
				$this->addErrorList($arrayToJs,false, $this->errorMsg['item'][1]); 	
			}else{
                // cek ada detail double gk 
                if (in_array($arrItemkey[$i],$arrDetailKeys)){  
                    $rsItem = $item->getDataRowById($arrItemkey[$i]);
                    $this->addErrorList($arrayToJs,false, $rsItem[0]['name'].'. '.$this->errorMsg[215]); 	 
                }else{ 
                    array_push($arrDetailKeys, $arrItemkey[$i]);
                }
                 
            
                $rsItem = $item->getDataRowById($arrItemkey[$i]);
                if ($this->unFormatNumber($arrDiscountValue[$i]) <= 0){ 
				    $this->addErrorList($arrayToJs,false, $this->errorMsg[603]); 	
                } 
            }   
		}
		  
		return $arrayToJs;
	 } 
      
 
    function getDetailWithRelatedInformation($pkey,$criteria = ''){
	   $sql = 'select
	   			'.$this->tableNameDetail .'.*,
                '.$this->tableItem.'.name as itemname, 
                '.$this->tableItem.'.code as itemcode, 
                '.$this->tableItem.'.sellingprice

			  from
			  	'. $this->tableNameDetail .',
                '.$this->tableItem.'
			  where
			  	' . $this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and
			  	refkey in ('.$this->oDbCon->paramString($pkey,',').')';
        
        $sql .= $criteria;
        
		return $this->oDbCon->doQuery($sql);
	
   }
    
    function getAllDiscountedItem($arrItemKey = array()){
        $today =  date('Y-m-d');
        
		 // ini harus itemkey, agar imagenya ketarik
		
        $sql = 'select  
                    '.$this->tableItem .'.pkey,
                    '.$this->tableItem .'.code,
                    '.$this->tableItem .'.name,
                    '.$this->tableItem .'.sellingprice,
                    '.$this->tableNameDetail .'.itemkey,
                    '.$this->tableNameDetail .'.discounttype,
                    '.$this->tableNameDetail .'.discount,
                    IF('.$this->tableNameDetail .'.discounttype=2,  '.$this->tableItem .'.sellingprice - ('.$this->tableItem .'.sellingprice * '.$this->tableNameDetail .'.discount / 100)  , '.$this->tableNameDetail .'.discount) as discountedprice
                from  
                    '.$this->tableName.',
                    '.$this->tableItem .',
                    '.$this->tableNameDetail .'
                where
                    '.$this->tableName.'.pkey = '.$this->tableNameDetail .'.refkey and
                    '.$this->tableNameDetail .'.itemkey = '.$this->tableItem .'.pkey and
                    '.$this->tableName .'.trdatestart <= '.$this->oDbCon->paramString($today).' and
                    '.$this->tableName .'.trdateend >= '.$this->oDbCon->paramString($today).' and
                    '.$this->tableName .'.statuskey = 1';
        
        if(!empty($arrItemKey))
            $sql .= ' and '.$this->tableNameDetail .'.itemkey in ('.$this->oDbCon->paramString($arrItemKey,',').') ';
            
        $sql .='  order by
                    '.$this->tableName.'.pkey desc
                ';
        
        
        $rs = $this->oDbCon->doQuery($sql);
        
        // loop jgn sampe itemnya double
        $returnArr = array();
        $arrItemKey = array();
        foreach($rs as $row){
            if(in_array($row['itemkey'], $arrItemKey)) continue; 
            array_push($returnArr,$row);
            array_push($arrItemKey,$row['itemkey']);
        }
        
		return $returnArr;
        
    }
    
    function applyDiscountScheme(&$rsItem){
        $arrItemKeys = array_column($rsItem,'pkey');
        $rsDiscount = $this->getAllDiscountedItem($arrItemKeys);
        
        $rsDiscount = array_column($rsDiscount,null,'itemkey');
        
        foreach($rsItem as $key=>$row){
            $itemkey = $row['pkey']; 
            if (isset($rsDiscount[$itemkey])){
                 $rsItem[$key]['hasdisc'] = true;
                 $rsItem[$key]['originalsellingprice'] = $rsItem[$key]['sellingprice'];
                 $rsItem[$key]['sellingprice'] = $rsDiscount[$itemkey]['discountedprice'];
                 $rsItem[$key]['discpercentage'] = 100 - ( $rsItem[$key]['sellingprice'] / $rsItem[$key]['originalsellingprice'] * 100);
            } else{ 
                 $rsItem[$key]['hasdisc'] = false;
                 $rsItem[$key]['originalsellingprice'] = $rsItem[$key]['sellingprice'];
                 $rsItem[$key]['discpercentage'] =0;
            }
        }  
    }
    
    function normalizeParameter($arrParam, $trim = false){
   /*     $item = new Item();
        
        $arrItemkey = $arrParam['hidItemKey'];
        for($i=0;$i<count($arrItemkey);$i++){ 
                $arrParam['discountValueInUnit'][$i] = (!isset($arrParam['discountValueInUnit'][$i])) ? 0 : $arrParam['discountValueInUnit'][$i];
                $arrParam['selDiscountType'][$i] = (!isset($arrParam['selDiscountType'][$i])) ? 1 : $arrParam['selDiscountType'][$i];
        } */
        
        $arrParam = parent::normalizeParameter($arrParam,true);  
        return $arrParam;
    }
	
	 function getDetailForAPI($arrKey, $arrIndex = array()){
          
		 if(in_array('image_url', $arrIndex)){ 
                $rsDetailsCol = array(); 
			 
                $rsDetails = $this->getItemImagesForAPI($arrKey);
                $rsDetails = $this->reindexDetailCollections($rsDetails,'itemkey');  
			 
                $rsDetailsCol['image_url'] = $rsDetails;
         }
		
        return $rsDetailsCol;
    }
	
	function getItemImagesForAPI($arrPkey){
        // yg dibalikin harus pkey detailnya 
		
        $sql = 'select  
				'.$this->tableNameDetail.'.itemkey,
	   			'.$this->tableItemImage .'.pkey,
	   			'.$this->tableItemImage .'.refkey,
	   			'.$this->tableItemImage .'.file 
			  from  
				'.$this->tableItemImage.',
				'.$this->tableNameDetail.'
			  where   
			    '.$this->tableNameDetail.'.itemkey = '.$this->tableItemImage.'.refkey and
			  	'.$this->tableItemImage.'.refkey in ('.$this->oDbCon->paramString($arrPkey,',').')';
    
        if (!empty($criteria))  
            $sql .=  ' ' .$criteria; 
  
		$rs = $this->oDbCon->doQuery($sql);
            
        $total = count($rs);    
        for($i=0;$i<count($rs);$i++)
            $rs[$i]['url'] = HTTP_HOST.'download/item/'.$rs[$i]['refkey'].'/'.$rs[$i]['file'];
        
        return $rs;
     
    }
    
}
?>