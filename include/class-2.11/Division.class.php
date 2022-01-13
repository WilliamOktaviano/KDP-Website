<?php

class Division extends Category{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'division';  
		$this->securityObject = 'Division'; 
	
        $this->arrLockedTable = array();
        $defaultFieldName = 'divisionkey'; 
        array_push($this->arrLockedTable, array('table'=>'item','field'=>$defaultFieldName)); 
        
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'division','title' => 'division','dbfield' => 'name','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
       
        $this->overwriteConfig();
   }
  
     
	function validateDelete($id){
		    
		$arrayToJs = array();
		$rs = $this->getDataRowById($id);
		
		if ($rs[0]['systemVariable'] == 1)  {
			$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['name'].'</strong>. ' . $this->errorMsg[211]);
			return $arrayToJs;
		}
		
	
		$item = new Item();
		$rsItem = $item->searchData($item->tableName.'.divisionkey', $id,true,' and '.$item->tableName.'.itemtype = '. $this->oDbCon->paramString(ITEM));  
		if(!empty($rsItem)){
			$rsDivision = $this->getDataRowById($id);
			$this->addErrorList($arrayToJs,false,'<strong>'.$rsDivision[0]['name']. '</strong>. '. $this->errorMsg[900] .' <strong>(' . $rsItem[0]['code'] . ' - ' . $rsItem[0]['name'] . ')</strong>.'); 
		}
	 
		return $arrayToJs;
	  
	 }
    
    
}

?>