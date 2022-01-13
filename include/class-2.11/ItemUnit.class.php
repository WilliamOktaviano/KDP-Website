<?php
class ItemUnit extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'item_unit';  
		$this->securityObject = 'ItemUnit'; 
		$this->tableStatus = 'master_status';
		 
       
        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code'); 
        $this->arrData['name'] = array('name'); 
        $this->arrData['statuskey'] = array('selStatus');
       
        $this->arrLockedTable = array();
        $defaultFieldName = 'unitkey'; 
        array_push($this->arrLockedTable, array('table'=>'item','field'=>'deftransunitkey')); 
        array_push($this->arrLockedTable, array('table'=>'item','field'=>'baseunitkey')); 
        array_push($this->arrLockedTable, array('table'=>'item_in_detail','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'item_out_detail','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'item_unit_conversion','field'=>'baseunitkey')); 
        array_push($this->arrLockedTable, array('table'=>'item_unit_conversion','field'=>'conversionunitkey')); 
        array_push($this->arrLockedTable, array('table'=>'preorder_detail','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'purchase_order_assets_detail','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'purchase_order_detail','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'purchase_receive_detail','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'purchase_return_detail','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'sales_delivery_detail','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'sales_order_car_service_detail','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'sales_order_detail','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'sales_return_detail','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'warehouse_transfer_detail','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'billing_statement_detail','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'item_adjustment_detail','field'=>$defaultFieldName));  
                   
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true,'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
    
		$this->overwriteConfig();
           
	}
	
	 function getQuery(){
	   
	   return '
				select
					'.$this->tableName. '.*,
					'.$this->tableStatus.'.status as statusname
				from 
					'.$this->tableName . ','.$this->tableStatus.'	
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey  
 		' .$this->criteria ; 
		 
    }
	  
	function validateForm($arr,$pkey = ''){
		  				    
		$arrayToJs = parent::validateForm($arr,$pkey); 
        
		$name = $arr['name'];   
		  
	 	$rsItem = $this->isValueExisted($pkey,'name',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['itemUnit'][1]);
		}else if(count($rsItem) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['itemUnit'][2]);
		}
		  	
		return $arrayToJs;
	 } 
    
    function normalizeParameter($arrParam, $trim = false){
        $arrParam = parent::normalizeParameter($arrParam,true); 
        return $arrParam;
    }
    
	  
}
		
?>