<?php

class PortfolioCategory extends Category{ 
   
    function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'portfolio_category';  
		$this->securityObject = 'PortfolioCategory'; 
	 
		
   }
    /*function getQuery(){
	   
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
		 
   }*/
   
	
	 function validateForm($arr,$pkey = ''){
		    
		$arrayToJs = array();
		
		$code = $arr['code'];
		$name = $arr['name'];  
	 
	 	$rs = $this->isValueExisted($pkey,'code',$code);	 
		if(empty($code)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['code'][1]);
		}else if(count($rs) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['code'][2]);
		}
	 
	 	$rsCategory = $this->isValueExisted($pkey,'name',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['category'][1]);
		}else if(count($rsCategory) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['category'][2]);
		}
		  
		return $arrayToJs;
	 }


 
	function validateDelete($id){
		    
		$arrayToJs = array(); 
		$portfolio = new Portfolio();
		$rsData = $portfolio->searchData($portfolio->tableName.'.categorykey', $id,true); 
				
		if(!empty($rsData)){
			$rs = $this->getDataRowById($id);
			$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['name']. '</strong>. '. $this->errorMsg[900] .' <strong>(' . $rsData[0]['code'] . ' - ' . $rsData[0]['title'] . ')</strong>.');
		}
	 
		return $arrayToJs;
	 }
	    
}

?>