<?php
class PaymentMethod extends BaseClass{ 
 
   function __construct(){
		
		parent::__construct(); 
		
		$this->tableName = 'payment_method';  
		$this->securityObject = 'PaymentMethod'; 
		$this->coaLink = 'coa_link'; 
		$this->tableStatus = 'master_status';
		 
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey'); 
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['useInPaymentConfirmation'] = array('useInPaymentConfirmation');
        $this->arrData['bankname'] = array('bankName');
        $this->arrData['bankaccountnumber'] = array('bankAccountNumber');
        $this->arrData['bankaccountname'] = array('bankAccountName');
        $this->arrData['bankaddress'] = array('bankAddress');
        $this->arrData['branch'] = array('branch');
        $this->arrData['swiftcode'] = array('swiftCode');
        $this->arrData['bankcode'] = array('bankCode');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['isvirtualaccount'] = array('chkVA');
       
        $this->arrLockedTable = array();
        $defaultFieldName = 'paymentkey'; 
        array_push($this->arrLockedTable, array('table'=>'service_order_payment','field'=>$defaultFieldName));  
        array_push($this->arrLockedTable, array('table'=>'ap_payment','field'=>$defaultFieldName));  
        array_push($this->arrLockedTable, array('table'=>'ar_payment','field'=>$defaultFieldName));  
        array_push($this->arrLockedTable, array('table'=>'billing_statement_payment','field'=>$defaultFieldName));  
        array_push($this->arrLockedTable, array('table'=>'purchase_order_assets_payment','field'=>$defaultFieldName));  
        array_push($this->arrLockedTable, array('table'=>'purchase_order_payment','field'=>$defaultFieldName));  
        array_push($this->arrLockedTable, array('table'=>'purchase_receive_payment','field'=>$defaultFieldName));  
        array_push($this->arrLockedTable, array('table'=>'sales_delivery_payment','field'=>$defaultFieldName));  
        array_push($this->arrLockedTable, array('table'=>'sales_order_payment','field'=>$defaultFieldName));    
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'bankName','title' => 'bankName','dbfield' => 'bankname','default'=>true, 'width' => 150 ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'bankAccountNumber','title' => 'bankAccountNumber','dbfield' => 'bankaccountnumber','default'=>true, 'width' => 150 ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'bankAccountName','title' => 'bankAccountName','dbfield' => 'bankaccountname','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'branch','title' => 'branch','dbfield' => 'branch','default'=>true, 'width' => 150 ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));

        $this->overwriteConfig();
       
	}
	
	function getQuery(){
	   
	   return '
				select
					'.$this->tableName. '.*,
					'.$this->tableStatus.'.status as statusname
				from 
					'.$this->tableName . ','.$this->tableStatus.' where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey  
 		' .$this->criteria ;  
		 
    }
	
	 
	
	function validateForm($arr,$pkey = ''){
		   
		$arrayToJs = parent::validateForm($arr,$pkey); 
		 
		$name = $arr['name']; 
		
	  	$rs = $this->isValueExisted($pkey,'name',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['paymentMethod'][1]);
		}else if(count($rs) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['paymentMethod'][2]);
		}
		  
		return $arrayToJs;
	 } 
	  
    	
    function delete($id,$forceDelete = false,$reason = ''){
		 
		$arrayToJs =  array();
		// tdk bisa didelete utk transaksi, tp ubah ke cancel
		if(isset( $this->tableNameDetail) &&!empty($this->tableNameDetail)){  
             $arrayToJs = $this->changeStatus($id, 4,'',false,$forceDelete);  
             return $arrayToJs; 
		} 
		
		try{ 
		
	 		$arrayToJs = $this->validateDelete($id);
			if (!empty($arrayToJs)) 
				return $arrayToJs;
					 
			 if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
				 
				$sql = 'delete from  '.$this->tableName.' where pkey = ' . $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);
			 	 
				$sql = 'delete from  '.$this->coaLink.' where reftable = \'warehouse\' and categorykey = \'payment\' and  refkey =  ' . $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);
            
                $this->setTransactionLog(DELETE_DATA,$id);
            
				$this->oDbCon->endTrans();
					 
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']); 
				 
		} catch(Exception $e){
			$this->oDbCon->rollback(); 
			$this->addErrorList($arrayToJs,false, $e->getMessage()); 
			
		}		 
			 	
 		return $arrayToJs; 
	} 
	 
}
		
?>
