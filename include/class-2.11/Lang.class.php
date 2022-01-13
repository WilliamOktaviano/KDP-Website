<?php

class Lang extends BaseClass{
   
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'lang';   
		$this->securityObject = 'Lang'; 
		$this->tableStatus = 'master_status'; 
		$this->uploadFolder = 'lang/';
	   
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
	   
		return $arrayToJs;
    }
    
     
}

?>