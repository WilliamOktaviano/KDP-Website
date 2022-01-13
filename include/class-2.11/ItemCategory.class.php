<?php

class ItemCategory extends Category{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'item_category';  
		$this->securityObject = 'ItemCategory'; 
        $this->tableMarketplaceCategory = 'item_category_marketplace_detail';
        $this->tableMarketplaceStorefront = 'item_category_storefront';
        $this->tableMarketplaceStorefrontDetail = 'item_category_storefront_detail';
		$this->uploadFolder = 'item-category/'; 
       
        $this->arrDataDetail = array(); 
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['marketplacekey'] = array('hidMarketplaceKey',array('mandatory'=>true)); 
        $this->arrDataDetail['marketplacecategorykey'] = array('hidMarketplaceCategoryKey');
       
        $this->arrStorefrontDetail = array(); 
        $this->arrStorefrontDetail['pkey'] = array('hidDetailStorefrontKey');
        $this->arrStorefrontDetail['refkey'] = array('pkey','ref'); 
        $this->arrStorefrontDetail['refstorefrontkey'] = array('hidStorefrontKey',array('datatype'=>'raw','mandatory'=>true));
       
               
        $arrDetails = array(); 
        array_push($arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableMarketplaceCategory));
        array_push($arrDetails, array('dataset' => $this->arrStorefrontDetail, 'tableName' => $this->tableMarketplaceStorefrontDetail));

       
        $this->arrData = array();  
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails)); 
        $this->arrData['code'] = array('code'); 
        $this->arrData['name'] = array('name');
        $this->arrData['orderlist'] = array('orderList', 'number');
        $this->arrData['parentkey'] = array('selCategory');
        $this->arrData['isleaf'] = array('isLeaf'); 
        $this->arrData['file'] = array('fileName');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['shortdescription'] = array('trShortDesc'); 
        $this->arrData['isshow'] = array('chkIsShow'); 
        $this->arrData['featured'] = array('chkIsFeatured'); 
        $this->arrData['description'] = array('txtDescription','raw'); 
         
             
        $this->arrDeleteTable = array(); 
        array_push($this->arrDeleteTable, array('table'=>$this->tableMarketplaceCategory,'field' => array('refkey'=>'{id}')));  
        array_push($this->arrDeleteTable, array('table'=>$this->tableMarketplaceStorefrontDetail,'field' => array('refkey'=>'{id}')));  
                      
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'category','title' => 'category','dbfield' => 'name','default'=>true,'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'parent','title' => 'parent','dbfield' => 'parentname','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'note','title' => 'note','dbfield' => 'shortdescription', 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'orderlist','title' => 'orderList','dbfield' => 'orderlist', 'align' => 'right', 'format' => 'integer', 'width' => 70));
       
        $this->includeClassDependencies(array( 
              'Marketplace.class.php',  
              'Item.class.php',
              'Storefront.class.php',  
        ));
       require_once DOC_ROOT. 'phpthumb/phpThumb.config.php';  


		$this->overwriteConfig();
	 /*
        GK BISA PAKE INI KARENA AD YG BEDA JENIS, BARANG DAN JASA.
        $this->arrLockedTable = array();
        $defaultFieldName = 'categorykey'; 
        array_push($this->arrLockedTable, array('table'=>'item','field'=>$defaultFieldName)); */
   } 
 
	function addData($arrParam){   
        $this->arrData['secondpercentage'] = array('secondPercentage','number');
        $this->arrData['sellpercentage'] = array('sellPercentage','number');  
        return parent::addData($arrParam);    
    }
     
                
	function editData($arrParam){   
        $this->arrData['secondpercentage'] = array('secondPercentage','number');
        $this->arrData['sellpercentage'] = array('sellPercentage','number');  
        return parent::editData($arrParam);    
    }
    
    function validateForm($arr,$pkey = ''){
		       
		$arrayToJs = parent::validateForm($arr,$pkey);  

		if($this->isActiveModule('marketplace')){
			$marketplace = new Marketplace();
                
        // VALIDASI KHUSUS KALO AD MARKETPLACE
        $marketplaceObjs = $marketplace->getMarketplaceObj();
        if(!empty($marketplaceObjs)){
            
            // semua field di kolom marketplace wajib diisi
            $hasEmptyField = false;
             
            foreach($arr['marketplaceCategoryName'] as $marketplaceCategoryName){
                if (empty($marketplaceCategoryName)){
                    $hasEmptyField = true;
                    break;
                }  
            }
            
            if($hasEmptyField)
                $this->addErrorList($arrayToJs,false,$this->errorMsg['marketplace'][5]); 
            
        }
         
		}
        
		return $arrayToJs;
	 } 
                   
	function validateDelete($id){
		    
		$arrayToJs = array();
		$rs = $this->getDataRowById($id);
		
		if ($rs[0]['systemVariable'] == 1)  {
			$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['name'].'</strong>. ' . $this->errorMsg[211]);
			return $arrayToJs;
		}
		
	
		$item = new Item();
		$rsItem = $item->searchData($item->tableName.'.categorykey', $id,true,' and '.$item->tableName.'.itemtype = '. $this->oDbCon->paramString(ITEM));  
		if(!empty($rsItem)){
			$rsCategory = $this->getDataRowById($id);
			$this->addErrorList($arrayToJs,false,'<strong>'.$rsCategory[0]['name']. '</strong>. '. $this->errorMsg[900] .' <strong>(' . $rsItem[0]['code'] . ' - ' . $rsItem[0]['name'] . ')</strong>.'); 
		}
	 
		return $arrayToJs;
	  
	 }
    
    
//    function getMarketplaceStorefront($pkey='',$marketplacekey =''){
//        $sql = 'select 
//                    '.$this->tableMarketplaceStorefrontDetail.'.*,
//                    '.$this->tableMarketplaceStorefront.'.name ,
//                    '.$this->tableMarketplaceStorefront.'.marketplacestorefrontkey ,
//                    '.$this->tableMarketplaceStorefront.'.marketplacekey 
//                from 
//                    '.$this->tableMarketplaceStorefrontDetail.',
//                    '.$this->tableMarketplaceStorefront.'
//                where '.$this->tableMarketplaceStorefrontDetail.'.refstorefrontkey = '.$this->tableMarketplaceStorefront.'.pkey';
//        
//        if(!empty($pkey))
//            $sql .= ' and '.$this->tableMarketplaceStorefrontDetail.'.refkey = ' .$this->oDbCon->paramString($pkey);
//        
//        if(!empty($marketplacekey))
//            $sql .= ' and '.$this->tableMarketplaceStorefront.'.marketplacekey = ' .$this->oDbCon->paramString($marketplacekey);
//        
//        return $this->oDbCon->doQuery($sql);
//    }

	 function getMarketplaceStorefront($pkey='',$marketplacekey =''){
        $sql = 'select 
                    '.$this->tableMarketplaceStorefrontDetail.'.*
                from 
                    '.$this->tableMarketplaceStorefrontDetail.' 
                where 
					1=1';
        
        if(!empty($pkey))
            $sql .= ' and '.$this->tableMarketplaceStorefrontDetail.'.refkey = ' .$this->oDbCon->paramString($pkey);
        
		$rs = $this->oDbCon->doQuery($sql);
		 
		$arrStorefrontKey = array();
		foreach($rs as $row){ 
			
			// utk compatibility 
			$arrKeys = json_decode($row['refstorefrontkey']);
			if(empty($arrKeys)) continue;
			
			if(!is_array($arrKeys)) $arrKeys = array($arrKeys);
			 
			$arrStorefrontKey = array_merge($arrStorefrontKey, $arrKeys);
		}
		 
		 
		// harus pisahin manual karena format storefrontkey ny JSON  
		 
			$sql = 'select pkey,marketplacekey,marketplacestorefrontkey from '.$this->tableMarketplaceStorefront.' where '.$this->tableMarketplaceStorefront.'.pkey in ('.$this->oDbCon->paramString($arrStorefrontKey,',').') ';
		   	
		 	if(!empty($marketplacekey))
			   $sql .= ' and '.$this->tableMarketplaceStorefront.'.marketplacekey = ' .$this->oDbCon->paramString($marketplacekey);
			
		 	$rsStorefront = $this->oDbCon->doQuery($sql); 
			$rsStorefrontKey = array_column($rsStorefront,'pkey'); 
			$rsStorefrontCol = array_column($rsStorefront,null,'pkey'); 
			 
			foreach($rs as $key=>$row){ 
				 
				// utk compatibility 
				$arrKeys = json_decode($row['refstorefrontkey']);
				if(!is_array($arrKeys)) $arrKeys = array($arrKeys);

				if(!in_array( $arrKeys[0] ,$rsStorefrontKey)) { 
					unset($rs[$key]); 
				}else{ 
					$rs[$key]['marketplacekey'] = $rsStorefrontCol[$arrKeys[0]]['marketplacekey'];
					
					$marketplacestorefrontkey = array();
					foreach($arrKeys as $refstorefrontkey)
						array_push($marketplacestorefrontkey,$rsStorefrontCol[$refstorefrontkey]['marketplacestorefrontkey']);
					
					$rs[$key]['marketplacestorefrontkey'] = (!empty($marketplacestorefrontkey)) ? json_encode($marketplacestorefrontkey) : '';
				}
			}

			$rs = array_values($rs);
	  
		//$this->setLog($rs,true);  
        return $rs;
    }
 
    function normalizeParameter($arrParam, $trim = false){
		
        $arrParam['secondPercentage'] = (isset($arrParam['secondPercentage'])) ? $arrParam['secondPercentage'] : 0 ;  
        $arrParam['sellPercentage'] = (isset($arrParam['sellPercentage'])) ? $arrParam['sellPercentage'] : 0 ;   
        $arrParam['chkIsShow'] = (!empty($arrParam['chkIsShow'])) ?  $arrParam['chkIsShow'] : 0 ;   
        //$arrParam['trDesc'] = (isset($arrParam['trDesc'])) ? $arrParam['trDesc'] : '' ;  

		
		// handling manual, karena nama variabelnya sama, jd harus dipisah manual
		if($this->isActiveModule('marketplace')){
			$arrStorefrontKey = $arrParam['hidStorefrontKey']; 
			$arrStorefrontMarketplaceKey = $arrParam['hidStoreFrontMarketplaceKey'];
			
			// cari relasi stoefrontkey dengan marketplacenya
			$storefront = new Storefront();
			$rsStorefront = $storefront->searchDataRow(array($storefront->tableName.'.pkey',$storefront->tableName.'.marketplacekey'),
													   ' and '.$storefront->tableName.'.pkey in ('.$this->oDbCon->paramString($arrStorefrontKey,',').')'
													  ); 
			
			$rsStorefront = $this->reindexDetailCollections($rsStorefront,'marketplacekey');   
			
						// reset 
			for($i=0;$i<count($arrStorefrontMarketplaceKey);$i++)  { 
				$arrStorefrontKey = array_column($rsStorefront[$arrStorefrontMarketplaceKey[$i]],'pkey'); 
				$arrParam['hidStorefrontKey'][$i] = !(empty($arrStorefrontKey)) ? json_encode($arrStorefrontKey) : '' ; 
			}

		} 
		
        $arrParam = parent::normalizeParameter($arrParam,true); 
        
        return $arrParam; 
    }   
	    
    
}

?>