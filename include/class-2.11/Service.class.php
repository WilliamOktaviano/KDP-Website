<?php 
class Service extends BaseClass{
 
   function __construct($itemType = 2, $serviceCost = 0){
		
		parent::__construct();
       
		$this->tableName = 'item';  
		$this->tableDetailTime = 'item_detail_time'; 
	   	$this->tableTimeUnit = 'time_unit';
		$this->tableCategory = 'service_category';  
		$this->tableStatus = 'master_status';  
		$this->tableDescription = 'item_description';
		$this->tableItemImage = 'item_image'; // biar gk bentrok sama isset(tableImage) di class updateImages
		$this->tableItemUnit = 'item_unit';
        $this->tableCostCOALink = 'item_coa_link';
		$this->tableUnitConversion = 'item_unit_conversion';
        $this->tableItemGroup = 'item_category_group';
	    $this->tableLangValue = 'item_lang';
		$this->uploadFolder = 'service/';
		$this->uploadIconFolder = 'service-icon/';
        $this->itemType = $itemType; 
        $this->serviceCost = $serviceCost;

        switch ($this->itemType){
            case TRUCKING_SERVICE :  $this->securityObject = (empty($serviceCost)) ?  'TruckingService' : 'TruckingCost';      
                                     break;    
            case SERVICE :  $this->securityObject = 'Service';      
                                     break;                               
        }
	   
	    $this->importUrl = 'import/service';
 
       
        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataLang, 'tableName' => $this->tableLangValue));
       
        // detail coa 
        $this->arrCOALink = array(); 
        $this->arrCOALink['pkey'] = array('hidCostCOADetailKey');
        $this->arrCOALink['refkey'] = array('pkey', 'ref');
        $this->arrCOALink['typekey'] = array('typeKeyCOA');
        $this->arrCOALink['eximkey'] = array('hidEximKey'); 
        $this->arrCOALink['categorykey'] = array('categoryKeyCOA');
        $this->arrCOALink['coakey'] = array('hidCostCOAKeyDetail');  
        
        array_push($arrDetails, array('dataset' => $this->arrCOALink, 'tableName' => $this->tableCostCOALink));
       
	   	$this->arrTimeConversion = array(); 
        $this->arrTimeConversion['pkey'] = array('hidTimeDetailKey');
        $this->arrTimeConversion['refkey'] = array('pkey', 'ref');
        $this->arrTimeConversion['timeunitkey'] = array('selTimeUnitKey',array('mandatory'=>true));
        $this->arrTimeConversion['sellingprice'] = array('unitSellingPrice','number');
       
	   	array_push($arrDetails, array('dataset' => $this->arrTimeConversion, 'tableName' => $this->tableDetailTime));
       
        $this->arrData = array();  
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails)); 
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['aliasname'] = array('aliasName');
        $this->arrData['categorykey'] = array('hidCategoryKey'); 
        $this->arrData['statuskey'] = array('selStatus');  
        $this->arrData['itemtype'] = array('itemType');  
        $this->arrData['servicecost'] = array('serviceCost');  
        $this->arrData['chargetype'] = array('rdbChargeType');    
        $this->arrData['fixedcost'] = array('chkIsFixedCost');  
        $this->arrData['reimburse'] = array('chkIsReimburse');  
        $this->arrData['iscontainer'] = array('chkIsContainer');  
        $this->arrData['istax23'] = array('chkIsTax23');  
        $this->arrData['costcoakey'] = array('hidCostCOAKey');  
        $this->arrData['revenuecoakey'] = array('hidRevenueCOAKey');  
        $this->arrData['prepaidexpensecoakey'] = array('hidPrepaidExpenseCOAKey');  
        $this->arrData['showintrucking'] = array('chkShowInTrucking');  
        $this->arrData['showincostrate'] = array('chkShowInCostRate');  
        //$this->arrData['showindepot'] = array('chkShowInDepot');  
        //$this->arrData['showinterminal'] = array('chkShowInTerminal');  
        //$this->arrData['showinshippingcompany'] = array('chkShowInShippingCompany');  
        $this->arrData['sellingprice'] = array('sellingPrice','number');
        $this->arrData['shortdescription'] = array('shortdescription');
        $this->arrData['commissiontype'] = array('selCommissionType');
        $this->arrData['commission'] = array('commissionValue','number');
        $this->arrData['shareprofit'] = array('chkIsShareProfit');
        $this->arrData['detail'] = array('txtDetail','raw');
	    $this->arrData['volume'] = array('volume','number');
	    $this->arrData['qty'] = array('qtyCombo','number');  
        $this->arrData['taxpercentage'] = array('taxPercentage');  
        $this->arrData['ispriceincludetax'] = array('chkIsPriceIncludeTax');  
        $this->arrData['iconimage'] = array('iconImage');
        
        
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'category','title' => 'category','dbfield' => 'categoryname','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'price','title' => 'price','dbfield' => 'sellingprice','default'=>true, 'width' => 100, 'align' =>'right','format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'shortDescription','title' => 'shortDescription','dbfield' => 'shortdescription', 'width' => 250 ));  
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70)); 
        
               
        if (PLAN_TYPE['categorykey'] == COMPANY_TYPE['trucking'])  
          array_push($this->arrDataListAvailableColumn, array('code' => 'iscontainericon','title' => 'container','dbfield' => 'iscontainericon','default'=>false, 'align' =>'center', 'width' => 80)); 
       
        $this->newLoad = true;
       
        $this->includeClassDependencies(array(
              'Category.class.php',
              'ServiceCategory.class.php',
              'Service.class.php',
              'ChartOfAccount.class.php',
              'TruckingServiceOrderCategory.class.php',
              'TimeUnit.class.php'
        ));
        require_once DOC_ROOT. 'phpthumb/phpThumb.config.php';  
       
        $this->overwriteConfig(); 
   }
   
    function getQuery(){ 
	   
	   $sql = '
				select
					'.$this->tableName. '.*, 
                    IF(reimburse=1, "<i class=\"fas fa-check text-green-avocado\"></i>", "") as reimburseicon,
					'.$this->tableCategory. '.name as categoryname,
					'.$this->tableStatus.'.status as statusname,
                     IF(iscontainer=1, "<i class=\"fas fa-check text-green-avocado\"></i>", "") as iscontainericon
				from 
					'.$this->tableName . ',
                    '.$this->tableStatus.' ,
                    '.$this->tableCategory. '
				where  		
                    itemtype = '.$this->itemType.' and
                    ispackage = 0 and
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey and
					'.$this->tableName . '.categorykey = '.$this->tableCategory.'.pkey and
                    '.$this->tableName . '.servicecost = '.$this->serviceCost.'
                    
 		' .$this->criteria ; 
		 
      // $this->setLog($sql);
       return $sql;
   }
    
    function afterUpdateData($arrParam, $action){  
        //$this->updateDescription($arrParam); 
        $pkey = $arrParam['pkey'];
        
        if(isset($arrParam['token-image-uploader']))
            $this->updateImages($pkey,$arrParam['token-image-uploader'], $arrParam['image-uploader'],$this->tableItemImage);
        
        $this->updateUnitConversion($pkey, $arrParam);   
    }

   function addData($arrParam){ 
        $arrParam['itemType'] = $this->itemType; 
        $arrParam['serviceCost'] = $this->serviceCost; 
		return parent::addData($arrParam); 	
	}
    
	
        
    function editData($arrParam){ 
        $arrParam['itemType'] = $this->itemType; 
        $arrParam['serviceCost'] = $this->serviceCost; 
        return parent::editData($arrParam);
	}
    
	
	/*function updateImage($pkey,$token,$arrImage){	
         
        //$pkey = $arrParam['pkey'];
		$sourcePath = $this->uploadTempDoc.$this->uploadFolder.$token;
		$destinationPath = $this->defaultDocUploadPath.$this->uploadFolder;
		
		 
		if(!is_dir($sourcePath)) 
			return;
			
		if(!is_dir($destinationPath)) 
			mkdir ($destinationPath,  0755, true);
			
		$destinationPath .= $pkey;  
		  
		//delete previous images	    
		$this->deleteAll($destinationPath);  
		$sql = 'delete from '.$this->tableItemImage.' where refkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql);
		  
		if (!empty($arrImage))	{
			$arrImage = explode(",",$arrImage);
			for ($i=0;$i<count($arrImage);$i++){   
                
                if ($i >= PLAN_TYPE['maxproductimage'])
                    continue;
				
				$newFileName = $this->hashFileName($sourcePath.$arrImage[$i]);
				$imagekey = $this->getNextKey($this->tableItemImage);  
 
				$this->uploadImage($sourcePath, $destinationPath,$arrImage[$i],$newFileName);
				
				$sql = 'insert into '.$this->tableItemImage.' (pkey,refkey,file) values ('.$this->oDbCon->paramString($imagekey).','.$this->oDbCon->paramString($pkey).', '.$this->oDbCon->paramString($newFileName).')';	
				$this->oDbCon->execute($sql);	 
				 
			}		
		}  
					
	} */
	 
	
	function validateForm($arr,$pkey = ''){
		       
		$arrayToJs = parent::validateForm($arr,$pkey);  
		 
		$name = $arr['name'];   
		$sellingPrice = $this->unFormatNumber($arr['sellingPrice']); 
		$categorykey = $arr['hidCategoryKey'];
        
        //$coaKey = $arr['hidCostCOAKey'];
	  
        if($this->checkTotalItemLimitation($this->tableName,PLAN_TYPE['maxproduct'],$pkey))
            $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][1] . ' ('.$this->lang['max'].' '. $this->formatNumber(PLAN_TYPE['maxproduct']). ' '. strtolower($this->lang['items']).')');  
        
        if (!empty($arr['image-uploader'])){
            $arrImage = explode(",",$arr['image-uploader']);
            if(count($arrImage) > PLAN_TYPE['maxproductimage'])
                $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][2] .' ('.$this->lang['max'].' '. $this->formatNumber(PLAN_TYPE['maxproductimage']). ' '. strtolower($this->lang['images']).')' );

            for($i=0;$i<count($arrImage);$i++){
                $path = $this->uploadTempDoc.$this->uploadFolder.$arr['token-image-uploader'];  
                if (filesize($path.'/'.$arrImage[$i]) >  (pow(1024,2) * PLAN_TYPE['maximagesize'])  )
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][4] .' ('.$this->lang['max'].' '. $this->formatNumber(PLAN_TYPE['maximagesize']). ' MB)' );
            }
            
        } 
        
		$rsItem = $this->isValueExisted($pkey,'name',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['service'][1]);
		}else if(count($rsItem) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['service'][2]);
		}
		 
		if (empty($categorykey)){ 
				$this->addErrorList($arrayToJs,false,$this->errorMsg['category'][1]); 
		}
        
        /*
        if (empty($coaKey)){ 
				$this->addErrorList($arrayToJs,false,$this->errorMsg['coa'][1]); 
		}
        */
         
		if (!is_numeric($sellingPrice) || $sellingPrice < 0){ 
			$this->addErrorList($arrayToJs,false, $this->errorMsg['sellingPrice'][2]);
		}
		   
		return $arrayToJs;
	 } 
	  
	function getItemDescription($pkey){
		$sql = 'select * from  '.$this->tableDescription.' where refkey = '.$this->oDbCon->paramString($pkey).' order by pkey asc';	
		return $this->oDbCon->doQuery($sql); 
	} 
	function getItemImage($pkey ){  
		$sql = 'select * from '.$this->tableItemImage.' where refkey = '.$this->oDbCon->paramString($pkey).' order by  pkey asc';	
	 	return $this->oDbCon->doQuery($sql);
    }  
	 
	  
	/*
     // deprecated
    function updateDescription($arrParam){
        $pkey = $arrParam['pkey'];
		
        if (!isset($arrParam['txtDescriptionLabel']))
            return;
        
	 	$itemDescriptionLabel = $arrParam['txtDescriptionLabel'];
		$itemDescription = $arrParam['txtDescription'];
		 
			$sql = 'delete from '.$this->tableDescription.' where refkey = '. $this->oDbCon->paramString($pkey);
			$this->oDbCon->execute($sql);
			
			for ($i=0;$i<count($itemDescription);$i++){
				
                if(!isset($itemDescriptionLabel[$i]))
                    continue;
                
                
				$label = trim($itemDescriptionLabel[$i]);
				$value = trim($itemDescription[$i]);
				 
			 	if (empty($label) && empty($value))  
					continue; 
			   
				$sql = 'insert into  '.$this->tableDescription.' (refkey,label,value) values ('.$this->oDbCon->paramString($pkey).','.$this->oDbCon->paramString($label).',  \''.addslashes($value).'\' )';	
		 
				$this->oDbCon->execute($sql);
				 
			}
					 
	}*/
	  
    
	 function updateUnitConversion($itemkey,$arrParam){ 
         
			$baseUnitKey = 1;
			$conversionUnitKey = 1;
			$conversionMultiplier = 1;
		    
            // untuk konversi default 1 pcs
            // cek utk base unit dan konversi unit yg sama udah ada blm (yg autoinsert sama islock)
            // kalo blm ad baru add
         
            $rsItemUnit = $this->getItemUnitConversion($itemkey);
         
            $found = false;
            for ($i=0;$i<count($rsItemUnit);$i++){
                if ($rsItemUnit[$i]['baseunitkey'] == $baseUnitKey  && $rsItemUnit[$i]['conversionunitkey'] == $baseUnitKey){
                    $found = true;
                    break;
                }
            } 
            if (!$found){ 
                $sql = 'insert into  '.$this->tableUnitConversion.' (refkey,baseunitkey,conversionunitkey,conversionmultiplier,isautoinsert) values ('.$this->oDbCon->paramString($itemkey).','.$this->oDbCon->paramString($baseUnitKey).','.$this->oDbCon->paramString($baseUnitKey).',1,1)';	
                $this->oDbCon->execute($sql);
            } 
			 
	 }
	  
    function getItemUnitConversion($pkey,$toUnitKey = '', $criteria = '', $orderby = ''){
     
		$sql = 'select 
                    '.$this->tableUnitConversion.'.* 
                from 
                    '.$this->tableUnitConversion.' 
                where  
                    refkey = '.$this->oDbCon->paramString($pkey);
        
        if (!empty($toUnitKey))
            $sql .= ' and conversionunitkey = ' .$this->oDbCon->paramString($toUnitKey); 
        
        $sql .= (!empty($criteria)) ? ' ' . $criteria : '';
        $sql .= (!empty($orderby)) ? ' ' . $orderby : ''; 
            
        $rs  = $this->oDbCon->doQuery($sql);  
          
        return $rs;
	}
    
	function delete($id, $forceDelete = false,$reason = ''){ 
		$arrayToJs =  array();
		 
		try{			 
				 
				$arrayToJs = $this->validateDelete($id);
				if (!empty($arrayToJs)) 
					return $arrayToJs;
		 		
				if (!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]);
			 
				
				$sql = 'delete from  '.$this->tableName.' where pkey = ' . $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);
				
				$sql = 'delete from  '.$this->tableDescription.' where refkey = '. $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);
				 
				$sql = 'delete from '.$this->tableItemImage.' where refkey = '. $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);
				$this->deleteAll($this->defaultDocUploadPath.$this->uploadFolder.$id);
            
				$this->deleteAll($this->defaultDocUploadPath.$this->uploadIconFolder.$id);
			 	
                $sql = 'delete from '.$this->tableUnitConversion.' where refkey = '. $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);	 
            
                $this->setTransactionLog(DELETE_DATA,$id);
            
				$this->oDbCon->endTrans();
										 
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);    
			 
				
			}catch(Exception $e){
				$this->oDbCon->rollback();
				$this->addErrorList($arrayToJs,false, $e->getMessage()); 
		}			
			
		return $arrayToJs;	
	}
	 
	  function validateDelete($id){
		    
		$arrayToJs = array();
		$rs = $this->getDataRowById($id);
			$rs = $this->getDataRowById($id); 
		if ($rs[0]['systemVariable'] == 1)  {
			$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['name'].'</strong>. ' . $this->errorMsg[211]);
			return $arrayToJs;
		} 
          
		//$salesOrder = new SalesOrder();
	 	//$rsItemMovement = $itemMovement->searchData($itemMovement->tableName.'.itemkey', $id,true);   
		
        //$this->addErrorList($arrayToJs,false,"<strong>" . $rs[0]['name'] . "</strong>. Harus cek sales order" ); 
          
      
		return $arrayToJs;
	 } 
	 
	  
	 function searchDataForAutoComplete($fieldname='',$searchkey='',$mustmatch=false,$searchCriteria='',$orderCriteria='', $limit=''){
		$sql = 'select
					'.$this->tableName. '.pkey,
                    '.$this->tableName. '.name as value, 
                    '.$this->tableName. '.code as code,
                    '.$this->tableName. '.sellingprice, 
                    '.$this->tableName. '.istax23 
				from 
					'.$this->tableName . ', 
                    '.$this->tableStatus.'
				where  		 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
			';
	
		if(!empty($fieldname)){
			
			$sql .= ' and ' ;
			
			if($mustmatch)
				$sql .=  $fieldname .' = '. $this->oDbCon->paramString($searchkey);
			else
				$sql .=  $fieldname .' like '. $this->oDbCon->paramString('%'.$searchkey.'%');
		}
				
		if($searchCriteria <> '')
			$sql .= ' ' .$searchCriteria;
	
		if($orderCriteria <> ''){
			$sql .= ' ' .$orderCriteria;
	 
	 	}
			
		if($limit <> '')
			$sql .= ' ' .$limit;
		    
		return $this->oDbCon->doQuery($sql);	
	} 
	   
    
     
    function normalizeParameter($arrParam, $trim=false){ 
         
        $arrParam['sellingPrice'] = (isset($arrParam['sellingPrice'])) ? $arrParam['sellingPrice'] : 0 ;  
        
        $arrParam = $this->updateOthersLangValue($arrParam, $this->arrData); 
        
        // kalo gk punya akses COA, di unset pas edit,
        // pas add gpp, utk memastikan diisi diawal 
        $security = new Security(); 
        if(!empty($arrParam['hidId']) && !$security->isAdminLogin('ChartOfAccount',10)){ 
            unset($this->arrData['costcoakey']);
            unset($this->arrData['revenuecoakey']);  
            unset($this->arrData['prepaidexpensecoakey']);  
        }
        
        if(isset($arrParam['token-icon-uploader'])){ 
            $file = $this->updateImages($arrParam['pkey'], $arrParam['token-icon-uploader'], $arrParam['icon-uploader'],'',$this->uploadIconFolder);   
            $arrParam['iconImage'] = $file; 
        }
            
        $arrParam = parent::normalizeParameter($arrParam,true);
 
        return $arrParam; 
    }
    
    function searchCategoryGroup($refname = '',$criteria = ''){
        
        $sql = 'select 
                    '.$this->tableItemGroup.'.* 
                from
                    '.$this->tableItemGroup.' 
                where
                    1=1'
            ;
        
        if (!empty($refname))
            $sql .= ' and ref = ' . $this->oDbCon->paramString($refname);
         
        
        if (!empty($criteria))
            $sql .= $criteria;
          
        return $this->oDbCon->doQuery($sql);
    }
    
    function searchItemByGroupCategory($ref){
        $rs = $this->searchCategoryGroup($ref);
        $arrCat = array_column($rs,'categorykey');
        
        $rs = array();
        
        if (!empty($arrCat)){ 
            $arrCat = implode(',',$arrCat); 
            //gk boleh pake searchdata karena kemungkinan bisa muncul dr service / paket
            //$sql = 'select * from '.$this->tableName.' where ' . $this->tableName.'.statuskey = 1 and itemtype = 2 and '.$this->tableName.'.categorykey in ('.$arrCat.')' ; 
            //$rs = $this->oDbCon->doQuery($sql);
            
            $rs = $this->searchData( $this->tableName.'.statuskey',1,true,' and '.$this->tableName.'.categorykey in ('.$arrCat.')');
        }
            
        return  $rs;
    }
	
	function getTimeDetail($pkey,$criteria=''){
        $sql = 'select
	   			'.$this->tableDetailTime .'.*,
                '.$this->tableTimeUnit.'.name as timename,
                '.$this->tableTimeUnit.'.minimaltime
			  from
			  	'. $this->tableDetailTime .',
                '.$this->tableTimeUnit.'
			  where
			  	' . $this->tableDetailTime .'.timeunitkey = '.$this->tableTimeUnit.'.pkey and
			  	'.$this->tableDetailTime .'.refkey = '.$this->oDbCon->paramString($pkey);
        
        $sql .= $criteria;
		return $this->oDbCon->doQuery($sql);
    }
    
	 function getRevenueCOAKey($itemkey,$warehousekey,$exportType = '',$containerType =''){ 
        $coaLink = new COALink();
        $warehouse = new Warehouse();
                
        $costByJobCategory = $this->loadSetting('splitCOAByJobCategory');
        $isCostByJob = ($costByJobCategory == 1) ? true : false;

        $rsItem = $this->getDataRowById($itemkey);
        
        $coakey = '';
        if($isCostByJob){ 
            $sql = 'select coakey from '.$this->tableCostCOALink.' 
					where refkey ='.$this->oDbCon->paramString($rsItem[0]['pkey']).'  and 
						   eximkey ='.$this->oDbCon->paramString($exportType).' and 
						   typekey = 1 and 
						   categorykey = '.$this->oDbCon->paramString($containerType);
            $rs = $this->oDbCon->doQuery($sql);
            $coakey = $rs[0]['coakey'];	
            
        }else{
            $coakey = $rsItem[0]['revenuecoakey'];
        }
         
         
        if (empty($coakey)){  
            $coa = ($rsItem[0]['itemtype'] == SERVICE) ? 'salesservice' : 'salesretail';
            $rsCOA = $coaLink->getCOALink ($coa, $warehouse->tableName,  $warehousekey);   
            $coakey = $rsCOA[0]['coakey'];
        }
        
        return $coakey;
    }
    
	
     function getRevenueCOAKeyByJobCategory($itemkey,$categorykey,$warehousekey = '', $defaultCOAOnEmpty = ''){ 
        // sementara utk TRUCKING dulu
        
        $rsItem = $this->getDataRowById($itemkey);
         
        // cek kalo tipenya per kategori (khususnya utk trucking)
        // 1 : normal, 2: kategori trucking, 3: bisa utk kategori sales order lainnya
         
        // item / service harusny sama.. sama2 di table item
         
        $coakey =  0;
        $truckingCostCOAType = $this->loadSetting('truckingCostCOAType');
         
        switch($truckingCostCOAType){
            
            case '2' :  $rsCOA = $this->getCostCOADetail($itemkey,' and  '.$this->tableCostCOALink.'.typekey = 2 and '.$this->tableCostCOALink.'.categorykey = '.  $this->oDbCon->paramString($categorykey));
                        $coakey =  ( !empty($rsCOA[0]['coakey']) ) ? $rsCOA[0]['coakey'] : 0 ;
                        break;
                
            default : $coakey = ( !empty($rsItem[0]['revenuecoakey']) ) ? $rsItem[0]['revenuecoakey'] : 0 ;
        }
          
        if($coakey == 0 ){   
                
            $coaLink = new COALink();
            $warehouse = new Warehouse();
    
            if(empty($warehousekey))
                $warehousekey = $warehouse->getDefaultData(); 
            
            
            // khusus trucking itemTtype <> SERVICE, nanti harus dicek
            
            if(!empty($defaultCOAOnEmpty)) 
                $coa = $defaultCOAOnEmpty;
            else
                $coa = ($rsItem[0]['itemtype'] == SERVICE) ? 'salesservice' : 'salesretail'; 
            
            $rsCOA = $coaLink->getCOALink ($coa, $warehouse->tableName,  $warehousekey);   
            $coakey = $rsCOA[0]['coakey'];
        }
          
        
        return $coakey;
    }
    
     function getCostCOAKey($itemkey,$warehousekey,$warehouseDefaultCOAKey ,$prepaidExpense = false,$exportType = '',$containerType =''){ 
  
        $coaLink = new COALink();
        $warehouse = new Warehouse();
        
        $costByJobCategory = $this->loadSetting('splitCOAByJobCategory');
        $isCostByJob = ($costByJobCategory == 1) ? true : false;
		$costCoaField = ($prepaidExpense) ? 'prepaidexpensecoakey' : 'costcoakey';
		 
        $coakey = '';
        if($isCostByJob == 1){
                    
            $typeKey = ($prepaidExpense) ? 2 : 3;
            
            $sql = 'select coakey 
					from '.$this->tableCostCOALink.' 
					where refkey ='.$this->oDbCon->paramString($itemkey).' and 
						  eximkey ='.$this->oDbCon->paramString($exportType).' and 
						  typekey ='.$this->oDbCon->paramString($typeKey).' and 
						  categorykey = '.$this->oDbCon->paramString($containerType);
			
            $rs = $this->oDbCon->doQuery($sql);
            $coakey = $rs[0]['coakey'];		             
			
        }else{
        	$rsItem = $this->getDataRowById($itemkey);
            $coakey = $rsItem[0][$costCoaField];
        }
          

        if (empty($coakey)){  

			// kalo prepaidexpense, overwrite
			if($prepaidExpense)
				$warehouseDefaultCOAKey = 'prepaidexpense';
				
            $rsCOA = $coaLink->getCOALink ($warehouseDefaultCOAKey, $warehouse->tableName,  $warehousekey);   
            $coakey = $rsCOA[0]['coakey'];
        }
        
                    

        return $coakey;
    }
	
	
    function getCostCOAKeyByJobCategory($itemkey,$categorykey,$warehousekey = ''){ 
        // sementara utk TRUCKING dulu
		
        $rsItem = $this->getDataRowById($itemkey);
           
        $coakey =  0;
        $truckingCostCOAType = $this->loadSetting('truckingCostCOAType');
         
        switch($truckingCostCOAType){
            
            case '2' :  $rsCOA = $this->getCostCOADetail($itemkey,' and  '.$this->tableCostCOALink.'.typekey = 1 and '.$this->tableCostCOALink.'.categorykey = '.  $this->oDbCon->paramString($categorykey));
                        $coakey =  ( !empty($rsCOA[0]['coakey']) ) ? $rsCOA[0]['coakey'] : 0 ;
                        break;
                
            default : $coakey = ( !empty($rsItem[0]['costcoakey']) ) ? $rsItem[0]['costcoakey'] : 0 ;
        }
         
        if($coakey == 0 ){   

            $coaLink = new COALink();
            $warehouse = new Warehouse();
    
            if(empty($warehousekey))
                $warehousekey = $warehouse->getDefaultData(); 
             
            $rsCOA = $coaLink->getCOALink ('operationalcost', $warehouse->tableName,  $warehousekey);   
            $coakey = $rsCOA[0]['coakey'];
        }
          
        
        return $coakey;
    }
    //costcoakey
    
    function getCostCOADetail($pkey,$criteria = ''){
        // sementara utk TRUCKING dulu
		
        $sql = 'select ' . $this->tableCostCOALink .'.*, 
                   		concat(' . $this->tableCostCOALink .'.categorykey, "-" , ' . $this->tableCostCOALink .'.typekey ) as categoryandtypekey
 				from '.$this->tableCostCOALink.' where refkey = ' .  $this->oDbCon->paramString($pkey);
		
		if(!empty($criteria))
        	$sql .= $criteria;
        
        return   $this->oDbCon->doQuery($sql);
    }
    
  } 

?>
