<?php

class Supplier extends BaseClass{ 
    
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'supplier';  
		$this->tableStatus = 'supplier_status';
		$this->tableMasterPrice = 'supplier_master_price';
		$this->tableCity = 'city';
		$this->tableCityCategory = 'city_category'; 
		$this->tableTermOfPayment = 'term_of_payment';	   
        $this->tableContact = 'contact_person';
	   	$this->tableItem = 'item';
	    $this->tableContainer = 'container';
	    $this->tableLocation = 'location';
	    $this->tableCurrency = 'currency';
		$this->securityObject = 'Supplier';
	   
	    $this->importUrl = 'import/supplier';
                                 
        $this->arrContactPerson = array(); 
        $this->arrContactPerson['pkey'] = array('hidContactPersonDetailKey'); 
        $this->arrContactPerson['refkey'] = array('pkey', 'ref');
        $this->arrContactPerson['reftable'] = array('reftable',array('mandatory'=>true));
        $this->arrContactPerson['name'] = array('cpName',array('mandatory'=>true));
        $this->arrContactPerson['position'] = array('cpPosition');
        $this->arrContactPerson['phone'] = array('cpPhone');
	   
	   	$this->arrDataDetail = array(); 
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['servicekey'] = array('hidServiceKey',array('mandatory'=>true));
        $this->arrDataDetail['itemkey'] = array('hidContainerDetailKey');
        $this->arrDataDetail['locationkey'] = array('hidLocationDetailKey');
        $this->arrDataDetail['currencykey'] = array('selDetailCurrency');
        $this->arrDataDetail['price'] = array('price',array('datatype'=>'number','mandatory'=>true));
        
        
        $arrDetails = array(); 
        array_push($arrDetails, array('dataset' => $this->arrContactPerson, 'tableName' => $this->tableContact));
        array_push($arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableMasterPrice));
 
       
        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails)); 
        $this->arrData['code'] = array('code'); 
        $this->arrData['name'] = array('name');
        $this->arrData['address1'] = array('address1');
        $this->arrData['address2'] = array('address2');
        $this->arrData['citykey'] = array('hidCityKey');
        $this->arrData['zipcode'] = array('zipCode');
        $this->arrData['phone'] = array('phone');
        $this->arrData['mobile'] = array('mobile');
        $this->arrData['email'] = array('email');
        $this->arrData['fax'] = array('fax');
        $this->arrData['description'] = array('description');
        $this->arrData['termofpaymentkey'] = array('selTermOfPayment');
        $this->arrData['accountno'] = array('accountNo');
        $this->arrData['accountbank'] = array('accountBank');
        $this->arrData['accountname'] = array('accountName');
        $this->arrData['taxid'] = array('taxid');
        $this->arrData['taxregistrationname'] = array('taxRegistrationName');
        $this->arrData['taxregistrationaddress'] = array('taxRegistrationAddress');
        $this->arrData['apcoakey'] = array('hidAPCOAKey');
        $this->arrData['downpaymentcoakey'] = array('hidDownpaymentCOAKey'); 
        $this->arrData['autotax'] = array('chkAutoTax');
        //$this->arrData['categorykey'] = array('selCategoryKey');
        $this->arrData['currencypreference'] = array('selCurrencyPreference'); 
        $this->arrData['commissioncoakey'] = array('hidCommissionCOAKey'); 
        $this->arrData['statuskey'] = array('selStatus');  
        
       
        $this->arrLockedTable = array();
        $defaultFieldName = 'supplierkey'; 
        array_push($this->arrLockedTable, array('table'=>'ap','field'=>$defaultFieldName));  
        array_push($this->arrLockedTable, array('table'=>'ap_payment_header','field'=>$defaultFieldName));  
        array_push($this->arrLockedTable, array('table'=>'purchase_order_assets_header','field'=>$defaultFieldName));  
        array_push($this->arrLockedTable, array('table'=>'purchase_order_header','field'=>$defaultFieldName));  
        array_push($this->arrLockedTable, array('table'=>'purchase_receive_header','field'=>$defaultFieldName));  
        array_push($this->arrLockedTable, array('table'=>'purchase_return_header','field'=>$defaultFieldName));  
        array_push($this->arrLockedTable, array('table'=>'sales_delivery_header','field'=>$defaultFieldName));  
        array_push($this->arrLockedTable, array('table'=>'service_work_order','field'=>$defaultFieldName));   
        array_push($this->arrLockedTable, array('table'=>'trucking_service_work_order','field'=>$defaultFieldName));   
        array_push($this->arrLockedTable, array('table'=>'car_service_maintenance_header','field'=>$defaultFieldName));   
                
        $this->arrDeleteTable = array(); 
        array_push($this->arrDeleteTable, array('table'=>$this->tableContact,'field' => array('refkey'=>'{id}', 'reftable'=>$this->tableName)));  
   
               
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'phone','title' => 'phone','dbfield' => 'phone','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'mobilePhone','title' => 'mobilePhone','dbfield' => 'mobile','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'email','title' => 'email','dbfield' => 'email','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 90));
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'description',  'width' => 200));
     
        $this->overwriteConfig();
       
        $this->includeClassDependencies(array( 
                  'AP.class.php', 
                  'ChartOfAccount.class.php', 
                  'City.class.php', 
                  'Container.class.php',   
                  'COALink.class.php', 
                  'Currency.class.php', 
                  'Customer.class.php',
                  'GeneralJournal.class.php', 
                  'Item.class.php', 
                  'Location.class.php', 
                  'Service.class.php', 
                  'TermOfPayment.class.php', 
                  'Warehouse.class.php' 
        ));  

   }
   
	
   function getQuery(){
	    
	   return '
			select
					'.$this->tableName. '.*,
					'.$this->tableStatus.'.status as statusname	, 
					'.$this->tableCity.'.name as cityname , 
					'.$this->tableCityCategory.'.name as citycategoryname	, 
					coalesce('.$this->tableTermOfPayment.'.duedays,0) as duedays 		
				from 
					'.$this->tableName . ' 		 
                         left join '.$this->tableTermOfPayment.' on '.$this->tableName . '.termofpaymentkey = '.$this->tableTermOfPayment.'.pkey 
                         left join '.$this->tableCity.' on '.$this->tableName . '.citykey = '.$this->tableCity.'.pkey 
						 left join '.$this->tableCityCategory.' on '.$this->tableCity . '.categorykey = '.$this->tableCityCategory.'.pkey 
					,'.$this->tableStatus.' 
				where 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey
 		' .$this->criteria ; 
		 
    }  
	 
 
	function validateForm($arr,$pkey = ''){
		  
        $arrayToJs = parent::validateForm($arr,$pkey); 
          
	  	$name = $arr['name']; 
	 	$email = $arr['email'];  
       	$serviceDetailKey = $arr['hidServiceKey'];
		 
        $name = $arr['name'];
		$rsSupplier = $this->isValueExisted($pkey,'name',$name);	 
        if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['supplier'][1]);
		}else if(count($rsSupplier) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['supplier'][2]);
		}
		  
	 	if(!empty($email)){
			if(!filter_var($email, FILTER_VALIDATE_EMAIL)) 
				$this->addErrorList($arrayToJs,false,$this->errorMsg['email'][3]);
		} 
		
	 
		return $arrayToJs;
	 }   
    
	 function updateAPOutstanding($supplierkey){
         
		  $arrayToJs = array();
         
         try{	 
			
				if(!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]); 
			   
                $ap = new AP();
                $outstanding = $ap->getAPOutstanding($supplierkey);

                $sql = 'update ' . $this->tableName .' set apoutstanding = ' .  $this->oDbCon->paramString($outstanding) .' where pkey = ' .  $this->oDbCon->paramString($supplierkey);
                $this->oDbCon->execute($sql);
				
                $this->oDbCon->endTrans();  
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);  
			
			} catch(Exception $e){
				$this->oDbCon->rollback();
				$this->addErrorList($arrayToJs,false, $e->getMessage());  
			}	 
      
     } 
    
    function getAPCOAKey($supplierkey,$warehousekey,$apTypeKey = ''){ 
        $coaLink = new COALink();
        $warehouse = new Warehouse();
        
        $rsSupplier = $this->getDataRowById($supplierkey);
        if (!empty($rsSupplier[0]['apcoakey'])){  
             $coakey = $rsSupplier[0]['apcoakey'];
        }else{  
			switch ($apTypeKey){  
                case 4 : 
                    $coaName = 'commissionap'; 
                    break; 
                default :  
                    $coaName = 'ap';  
                    break;

			}
			
            $rsCOA = $coaLink->getCOALink ($coaName, $warehouse->tableName,  $warehousekey);   
            $coakey = $rsCOA[0]['coakey'];
        }
        
        return $coakey;
    }
    
	function getCommissionCOAKey($supplierkey,$warehousekey){ 
        $coaLink = new COALink();
        $warehouse = new Warehouse();
        
        $rsSupplier = $this->getDataRowById($supplierkey);
        if (!empty($rsSupplier[0]['commissioncoakey'])){  
             $coakey = $rsSupplier[0]['commissioncoakey'];
        }else{ 
            $rsCOA = $coaLink->getCOALink ('commissionap', $warehouse->tableName,  $warehousekey);   
            $coakey = $rsCOA[0]['coakey'];
        }
        
        return $coakey;
    }        
    
    function generateDefaultQueryForAutoComplete($returnField){ 
        $sql = 'select
                '.$returnField['key'].',
                '.$returnField['value'].' as value
            from 
                '.$this->tableName . ',
                '.$this->tableStatus.'  
            where  		
                '.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey  
        ';
        
        $sql .=  $this->getCompanyCriteria() ;
         
        return $sql;
        
    }
    
	function getDetailWithRelatedInformation($pkey,$criteria=''){ 
       
	   $sql = 'select
	   			'.$this->tableMasterPrice .'.*, 
	   			'.$this->tableItem.'.name as servicename,
	   			'.$this->tableName.'.categorykey,
	   			'.$this->tableCurrency.'.name as currencyname, 
                '.$this->tableContainer.'.name as containername,
	   			'.$this->tableLocation.'.name as locationname
                
              from
			  	'.$this->tableMasterPrice .' 
                    left join '.$this->tableLocation.' on '.$this->tableMasterPrice .'.locationkey = '.$this->tableLocation .'.pkey 
                    left join '.$this->tableCurrency.' on '.$this->tableMasterPrice .'.currencykey = '.$this->tableCurrency .'.pkey 
                    left join '.$this->tableContainer.' on '.$this->tableMasterPrice .'.itemkey = '.$this->tableContainer .'.pkey ,
                '.$this->tableName.',
			  	'.$this->tableItem .' 
			  where 
                '.$this->tableName .'.pkey = '.$this->tableMasterPrice .'.refkey and 
			  	'.$this->tableMasterPrice .'.servicekey = '.$this->tableItem .'.pkey and 
			  	'.$this->tableMasterPrice .'.refkey = '.$this->oDbCon->paramString($pkey) . ' ';
       
        $sql .= $criteria;
		 
           
		return $this->oDbCon->doQuery($sql);
	
   }
    
    function normalizeParameter($arrParam, $trim = false){  
          
            $arrParam = parent::normalizeParameter($arrParam,true); 
         
            if(isset($arrParam['hidContactPersonDetailKey'])){ 
                for($i=0;$i<count($arrParam['hidContactPersonDetailKey']);$i++){
                    $arrParam['reftable'][$i] = $this->tableName;
                }
            }
         
            return $arrParam; 
    }
    
    
    function getDownpaymentCOAKey($supplierkey,$warehousekey){
     
        $coaLink = new COALink();
        $warehouse = new Warehouse();
        
        $rsSupplier = $this->getDataRowById($supplierkey);
        if (!empty($rsSupplier[0]['downpaymentcoakey'])){  
             $coakey = $rsSupplier[0]['downpaymentcoakey'];
        }else{ 
            $rsCOA = $coaLink->getCOALink ('supplierdownpayment', $warehouse->tableName,  $warehousekey);   
            $coakey = $rsCOA[0]['coakey'];
        }
        
        return $coakey;
    }
    
    function getTaxInformation($pkey){
        
        $rs = $this->getDataRowById($pkey);
        
        if (empty($rs) || $rs[0]['autotax'] == 0)
            $rs[0]['taxpercentage'] = 0;
        else
            $rs[0]['taxpercentage'] = (!empty($rs[0]['taxid'])) ? 2 : 4;
         
             
        return $rs[0];
        
    }
    
    function getTermOfPayment($supplierkey){
        $sql = 'select 
                    '.$this->tableName.'.pkey, 
                    '.$this->tableName.'.termofpaymentkey, 
                    coalesce('.$this->tableTermOfPayment.'.duedays, 0) as duedays
                from 
                    '.$this->tableName.' 
                    left join '.$this->tableTermOfPayment.' on  '.$this->tableName.'.termofpaymentkey = '.$this->tableTermOfPayment.'.pkey
                where 
                    '.$this->tableName.'.pkey in('.$this->oDbCon->paramString($supplierkey,',').')
                ';
        
        return $this->oDbCon->doQuery($sql); 
    }
    
  }

?>
