<?php
class Brand extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'brand';
		$this->tableNameDetail = 'brand_marketplace_detail';
        $this->tableMarketplaceStorefrontDetail = 'brand_storefront_detail';
        $this->tableMarketplaceStorefront = 'item_category_storefront';
		$this->tableMarketplace = 'marketplace';
		$this->tableStatus = 'master_status';
		$this->securityObject = 'Brand'; 
		$this->uploadFolder = 'brand/';
	   
        $this->arrDataDetail = array(); 
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['marketplacekey'] = array('hidMarketplaceKey',array('mandatory'=>true));
        //$this->arrDataDetail['marketplacebrandkey'] = array('hidMarketplaceBrandKey',array('mandatory'=>true));
        $this->arrDataDetail['marketplacebrandname'] = array('marketplaceBrandName',array('mandatory'=>true));	
       
        $this->arrStorefrontDetail = array(); 
        $this->arrStorefrontDetail['pkey'] = array('hidDetailStorefrontKey');
        $this->arrStorefrontDetail['refkey'] = array('pkey','ref'); 
        $this->arrStorefrontDetail['refstorefrontkey'] = array('hidStorefrontKey',array('datatype'=>'raw','mandatory'=>true));
       
        $arrDetails = array(); 
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));
        array_push($arrDetails, array('dataset' => $this->arrStorefrontDetail, 'tableName' => $this->tableMarketplaceStorefrontDetail));

	   
        $this->arrData = array();  
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails)); 
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['publish'] = array('chkIsPublish'); 
        $this->arrData['file'] = array('fileName');
            
       	$this->arrLockedTable = array();
        $defaultFieldName = 'brandkey';
        array_push($this->arrLockedTable, array('table'=>'item','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'car','field'=>$defaultFieldName));
              
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
       
  	   $this->includeClassDependencies(array(
              'Marketplace.class.php',  
              'ItemCategory.class.php',  
              'Storefront.class.php'
        ));
       require_once DOC_ROOT. 'phpthumb/phpThumb.config.php';  

        $this->overwriteConfig();
	}
	
	 function getQuery(){
	   
	   return '
			select
					'.$this->tableName. '.*,
					'.$this->tableStatus.'.status as statusname
				from
					'.$this->tableName.','.$this->tableStatus.' where
					'.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey
 		' .$this->criteria ; 
		 
    }
    
	function validateForm($arr,$pkey = ''){
		   
		$arrayToJs = parent::validateForm($arr,$pkey); 
        
        
		$name = $arr['name'];  
	 	 
	  	$rs = $this->isValueExisted($pkey,'name',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['brand'][1]);
		}else if(count($rs) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['brand'][2]);
		} 
        
        // VALIDASI KHUSUS KALO AD MARKETPLACE
		// $marketplace = new Marketplace();
        
        /*$marketplaceObjs = $marketplace->getMarketplaceObj();
        if(!empty($marketplaceObjs)){ 
            
            $hasEmptyField = false;
            
            foreach($arr['marketplaceBrandName'] as $marketplaceBrandName){
                if (empty($marketplaceBrandName)){
                    $hasEmptyField = true;
                    break;
                } 
            } 
            if($hasEmptyField)
                $this->addErrorList($arrayToJs,false,$this->errorMsg['marketplace'][4]); 
            
        }*/
		 
		return $arrayToJs;
	 }	 
	   
    
     function updateImage($pkey,$token,$arrImage){		
		 
		$sourcePath = $this->uploadTempDoc.$this->uploadFolder.$token;
		$destinationPath = $this->defaultDocUploadPath.$this->uploadFolder;
		
		if(!is_dir($sourcePath)) 
			return;
	
			
		if(!is_dir($destinationPath)) 
			mkdir ($destinationPath,  0755, true);
			
		$destinationPath .= $pkey;  
 
 		//delete previous images	    
		$this->deleteAll($destinationPath);   
		 
		if (!empty($arrImage))	{
			$arrImage = explode(",",$arrImage); 
			$this->uploadImage($sourcePath, $destinationPath,$arrImage[0]); 
			return $arrImage[0]; 
		}
		
		return '';
		
	}  
    
    	 
	function delete($id, $forceDelete = false,$reason = ''){ 
		 
		try{			
				  
				$arrayToJs =  array();
			 	
				if (!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]);
			
		 		 
				$sql = 'delete from  '.$this->tableName.' where pkey = ' . $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);  
				$this->deleteAll($this->defaultDocUploadPath.$this->uploadFolder.$id);
			
                $this->setTransactionLog(DELETE_DATA,$id);
        
				$this->oDbCon->endTrans();
										 
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);    
			 
				
			}catch(Exception $e){
				$this->oDbCon->rollback();
				$this->addErrorList($arrayToJs,false, $e->getMessage()); 
		}			
			
		return $arrayToJs;	
	}
    
    
    function getMarketplaceBrand($id, $marketplacekey = '', $categorykey = '', $criteria = '') {
         
        $sql =  
            'select  
                '.$this->tableName.'.name as name ,
                '.$this->tableNameDetail.'.* 
                
            FROM 
                '.$this->tableName.',
                '.$this->tableNameDetail.' 
            WHERE
                '.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey and 
                '.$this->tableNameDetail.'.refkey = '.$this->oDbCon->paramString($id).'
                ';
        
        if (!empty($marketplacekey))
            $sql .= ' and ' .$this->tableNameDetail .'.marketplacekey = '.$this->oDbCon->paramString($marketplacekey);
            
        if (!empty($criteria))  
            $sql .=  ' ' .$criteria;
         
        
        $rsBrand =  $this->oDbCon->doQuery($sql);
        
        // kalo shopee, cocokin ulang ke kategori
        if( !empty($rsBrand) &&  !empty($categorykey) && $marketplacekey == MARKETPLACE['shopee']){ 
            // cari marketplacecategory
            $itemCategory = new ItemCategory();
            $rsMarketplaceCategory = $itemCategory->getMarketplaceCategory($categorykey, $marketplacekey);
            $marketplacecategorykey = (!empty($rsMarketplaceCategory)) ? $rsMarketplaceCategory[0]['marketplacecategorykey'] : 0;
                
            $dbCon = $this->masterConn(); 
            $sql = 'select lower(value) as value from marketplace_category_attributes where 
                    marketplacecategorykey = '.$this->oDbCon->paramString($marketplacecategorykey).' and label = \'Brand\' and marketplacekey = '.MARKETPLACE['shopee'] ;
            $rsMPBrand = $dbCon->doQuery($sql); 
             
            $arrBrand = (!empty($rsMPBrand)) ? json_decode($rsMPBrand[0]['value']) : array();
            $dbCon = null; 
              
            if(!in_array(strtolower($rsBrand[0]['marketplacebrandname']),$arrBrand))
                $rsBrand = array();
        }
         
        
        return $rsBrand;
    }

	
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
	   
        return $rs;
    }
		
    function normalizeParameter($arrParam, $trim = false){ 
        
         $arrParam['fileName'] = (isset( $arrParam['token-item-image-uploader'])) ? $this->updateImage($arrParam['pkey'], $arrParam['token-item-image-uploader'], $arrParam['item-image-uploader']) : '' ;

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