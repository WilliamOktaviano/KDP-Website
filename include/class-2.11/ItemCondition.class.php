<?php
class ItemCondition extends BaseClass{
    
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'item_condition';  
		$this->tableStatus = 'master_status'; 
        $this->tableNameDetail = 'item_condition_detail';
		$this->securityObject = 'ItemCondition'; 
		
        $this->arrDataDetail = array(); 
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['marketplacekey'] = array('hidMarketplaceKey',array('mandatory'=>true));
        $this->arrDataDetail['marketplaceconditionkey'] = array('selCondition',array('mandatory'=>true));	
       
        $this->arrData = array();  
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => array('dataset' => $this->arrDataDetail))); 
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name'); 
        $this->arrData['statuskey'] = array('selStatus');   
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true,'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
    
		$this->overwriteConfig();
   }
	
	 function getQuery(){
	   
	   return '
			select
					'.$this->tableName. '.*,
					'.$this->tableStatus.'.status as statusname
				from
					'.$this->tableName.',
                    '.$this->tableStatus.' 
                where
					'.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey 
 		' .$this->criteria ; 
		 
    }
   
    
	function validateForm($arr,$pkey = ''){
		    
		$arrayToJs = parent::validateForm($arr,$pkey);

		$name = $arr['name'];   
        
	 	$rs = $this->isValueExisted($pkey,'name',$name);
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][1]);
		}else if(count($rs) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][2]);
		} 
        
		return $arrayToJs;
	 }	 
	   
    
    function getMarketplaceCondition($conditionkey,$marketplacekey){
        $rs = $this->getDetailByColumn($this->tableNameDetail.'.marketplacekey',$marketplacekey,true,' and '.$this->tableNameDetail.'.refkey = ' . $this->oDbCon->paramString($conditionkey));
        return (isset($rs)) ? $rs[0]['marketplaceconditionkey'] : 0;
    }
        
    
	
 
}
?>
