<?php
class TermOfPayment extends BaseClass{
     
   function __construct(){
		
		parent::__construct();
 
		$this->tableName = 'term_of_payment';  
		$this->securityObject = 'termOfPayment'; 
		$this->tableStatus = 'master_status';  
       
        $this->arrLockedTable = array();
        $defaultFieldName = 'termofpaymentkey'; 
        array_push($this->arrLockedTable, array('table'=>'ap_payment_header','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'ar_payment_header','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'billing_statement_header','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'customer','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'preorder_header','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'purchase_order_assets_header','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'purchase_order_header','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'purchase_receive_header','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'sales_delivery_header','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'sales_order_car_service_header','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'sales_order_header','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'service_order_header','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'supplier','field'=>$defaultFieldName));  
                
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['duedays'] = array('duedays','number');
        $this->arrData['statuskey'] = array('selStatus');   
		
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'payment','title' => 'payment','dbfield' => 'name','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'maturity','title' => 'maturity','dbfield' => 'duedays','default'=>true, 'width' => 100 ,'align' => 'right', 'format'=>'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));

	   	$this->includeClassDependencies(array(
            
        ));

	   
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
		$duedays = $this->unFormatNumber($arr['duedays']);  
	 	  
	 	$rsItem = $this->isValueExisted($pkey,'name',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['top'][1]);
		}else if(count($rsItem) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['top'][2]);
		}
		 
		if (!is_numeric($duedays) || $duedays < 0){ 
			$this->addErrorList($arrayToJs,false,$this->errorMsg['duedays'][2]);
		}
		  	
		return $arrayToJs;
	 } 
	
	function normalizeParameter($arrParam, $trim=false){  
        $arrParam = parent::normalizeParameter($arrParam,true);   
        return $arrParam;
    }
	
	   
}
		
?>