<?php

class FilterCategory extends Category{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'filter_category';  
		$this->securityObject = 'FilterCategory';  
       	 
        $this->arrLockedTable = array();
        array_push($this->arrLockedTable, array('table'=>'filter_header','field'=>'categorykey')); 
		
   }
    function getQuery(){
	   
	   return '
				select
					'.$this->tableName. '.*,
					'.$this->tableStatus.'.status as statusname,
					parentcat.name as parentname
				from 
					'.$this->tableName . ' left join '.$this->tableName . ' parentcat on 	parentcat.pkey = '.$this->tableName . '.parentkey ,'.$this->tableStatus.' 
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
 		' .$this->criteria ; 
		 
    }
	
	 function validateForm($arr,$pkey = ''){
		    
		$arrayToJs = BaseClass::validateForm($arr,$pkey); 
		 
		$name = $arr['name'];  
	  
	 
	 	$rsCategory = $this->isValueExisted($pkey,'name',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['category'][1]);
		}else if(count($rsCategory) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['category'][2]);
		}
		  
		return $arrayToJs;
	 }

   
}

?>