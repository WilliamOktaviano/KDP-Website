<?php 
class Warehouse extends BaseClass{
  
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'warehouse';
		$this->tableItemMovement = 'item_movement'; 
		$this->tableCOA = 'chart_of_account';
        $this->tableCOALink = 'coa_link';
		$this->securityObject = 'Warehouse';
		$this->tableStatus = 'master_status';  
       
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['address'] = array('address');
        $this->arrData['citykey'] = array('hidCityKey'); 
        $this->arrData['zip'] = array('zip');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['isqohcount'] = array('qohcount');
        $this->arrData['iswebqoh'] = array('webqoh');
        $this->arrData['isrma'] = array('isrma');
        $this->arrData['isbus'] = array('isbus');
        $this->arrData['isvendor'] = array('isvendor');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['orderlist'] = array('orderlist');
        $this->arrData['location'] = array('location');
        $this->arrData['phone'] = array('phone');
 
        $this->arrLockedTable = array();
        $defaultFieldName = 'warehousekey'; 
        array_push($this->arrLockedTable, array('table'=>'item_movement','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'ap','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'ap_payment_header','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'ar','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'ar_payment_header','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'assets','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'billing_statement_header','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'cash_movement','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'employee','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'item_adjustment_header','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'item_in_header','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'item_in_warehouse','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'item_out_header','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'preorder_header','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'purchase_order_assets_header','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'purchase_order_header','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'purchase_receive_header','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'purchase_return_header','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'sales_delivery_header','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'sales_order_car_service_header','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'sales_order_header','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'sales_return_header','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'service_order_header','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'service_work_order','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'trucking_selling_rate_header','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'warehouse_transfer_header','field'=>'fromwarehousekey'));
        array_push($this->arrLockedTable, array('table'=>'warehouse_transfer_header','field'=>'towarehousekey')); 
           
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'company','title' => 'company','dbfield' => 'companyname','default'=>true, 'width' => 300));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc',  'width' => 200));
       
            
        $this->includeClassDependencies(array(
            'ChartOfAccount.class.php',
            'COALink.class.php',
            'PaymentMethod.class.php',
            'City.class.php',
            'Company.class.php',
            'Location.class.php'
        ));   

       
        $this->overwriteConfig();
   }
    
    function getQuery(){
	   
	   $sql = '
			select
					'.$this->tableName. '.*,
					'.$this->tableCompany. '.name as companyname,
					'.$this->tableStatus.'.status as statusname
				from 
					'.$this->tableName . ' , 
					'.$this->tableCompany. ' , 
                    '.$this->tableStatus.'
				where  		
					'.$this->tableName . '.companykey = '.$this->tableCompany.'.pkey and
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey   
 		'.$this->criteria ; 
 
        $sql .=  $this->getCompanyCriteria() ;
        $sql .=  $this->getWarehouseCriteria() ;
             
        return $sql;
    }  
	
    	
    function afterUpdateData($arrParam, $action){
        $pkey = $arrParam['pkey']; 
        $this->updateCOALink($pkey,$arrParam); 	     
    }

  
	function updateCOALink($warehouseKey, $arrParam){ 
        
        /*
        // gk boleh return, kalo gk, user gk bisa hapus coa link
        if (empty($arrParam['coalink']))
            return;*/
        
        //$this->setLog($arrParam,true);
        
        $coaLink = new COALink();	 
		for ($i=0;$i<count($arrParam['coalink']);$i++)
            $coaLink->updateCOALink($arrParam['hidcategorykey'][$i],$arrParam['hidcoakey'][$i], $this->tableName,$warehouseKey, $arrParam['hidrefkey'][$i]);
   
	} 
	
	function validateForm($arr,$pkey = ''){ 
		
		$arrayToJs = parent::validateForm($arr,$pkey);  
        
		$warehousename = $arr['name']; 
		//$companykey = $arr['selCompany']; 
        
        if($this->checkTotalItemLimitation($this->tableName,PLAN_TYPE['maxwarehouse'],$pkey)){  
          $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][1]);  
        }
		
		$rsWarehouse = $this->isValueExisted($pkey,'name',$warehousename);	 
		if(empty($warehousename)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['warehouse'][1]);
		}else if(count($rsWarehouse) <> 0){ 
			$this->addErrorList($arrayToJs,false,$this->errorMsg['warehouse'][2]);
		}
      
        // check if warehouse owned by company 
        /*
        if(empty($companykey)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['company'][1]);
		}else{ 
             $employee = new Employee();
             $userkey =  (isset($arr['userkey']) && !empty($arr['userkey'])) ? $arr['userkey'] : base64_decode($_SESSION[$this->loginAdminSession]['id']); 
             $rsOwnedCompany = array_column($employee->getOwnedCompany($userkey),'pkey');
             
             if(!in_array($companykey, $rsOwnedCompany))
			     $this->addErrorList($arrayToJs,false,$this->errorMsg['company'][3]);
                 
        }*/
        
		return $arrayToJs;
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
				 
                // delete table untuk coalink
                $sql = 'delete from ' . $this->tableCOALink .' where reftable = ' . $this->oDbCon->paramString($this->tableName) . ' and reftablekey = ' . $this->oDbCon->paramString($id);
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
	  
    function getCompanyWarehouse(){
        $arrWarehouse = array();
        
        $rsWarehouse = $this->searchData();
        if (!empty($rsWarehouse)){ 
		    $arrWarehouse = array_column($rsWarehouse,'pkey'); 
        }
        
        return $arrWarehouse;
        
    }

	// overwrite karena ad warehouse Criteria
	function generateComboboxOpt($opt = array(),$queryOpt = array(),$preselected='',$relOpt = array()){
		// nanti dilihat perlu isset gk, atau selalu ditambahkan saja
		if(isset($queryOpt['criteria'])) $queryOpt['criteria'] .=    $this->getCompanyCriteria() . $this->getWarehouseCriteria() ; 
		return parent::generateComboboxOpt($opt,$queryOpt,$preselected ,$relOpt );
	}
}

?>