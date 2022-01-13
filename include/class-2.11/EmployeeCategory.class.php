<?php

class EmployeeCategory extends Category{
 
   function __construct(){
		 
		parent::__construct(); 
       
		$this->tableName = 'employee_category';  
		$this->securityObject = 'EmployeeCategory'; 
	 
       	$this->arrLockedTable = array();
        $defaultFieldName = 'categorykey';
        array_push($this->arrLockedTable, array('table'=>'employee','field'=>$defaultFieldName)); 
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'division','title' => 'division','dbfield' => 'name','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
       
        $this->overwriteConfig();
       
       
   }
 
	 function validateForm($arr,$pkey = ''){
		    
		$arrayToJs = BaseClass::validateForm($arr,$pkey); 
		 
		$name = $arr['name'];   
		
	 	$rsCategory = $this->isValueExisted($pkey,'name',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['division'][1]);
		}else if(count($rsCategory) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['division'][2]);
		}
		  
		return $arrayToJs;
	 }
 
	    
}

?>