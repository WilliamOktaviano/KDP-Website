<?php

class ArticleCategory extends Category{
   
   function __construct(){
		
		parent::__construct();

        $this->tableName = 'article_category';  
		$this->securityObject = 'ArticleCategory'; 
	  
        $this->arrLockedTable = array();
        $defaultFieldName = 'categorykey'; 
        array_push($this->arrLockedTable, array('table'=>'article','field'=>$defaultFieldName)); 
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


 
	function validateDelete($id){
		    
		$arrayToJs = array(); 
		$article = new Article();
		$rsData = $article->searchData($article->tableName.'.categorykey', $id,true); 
				
		if(!empty($rsData)){
			$rs = $this->getDataRowById($id);
			$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['name']. '</strong>. '. $this->errorMsg[900] .' <strong>(' . $rsData[0]['code'] . ' - ' . $rsData[0]['title'] . ')</strong>.');
		}
	 
		return $arrayToJs;
	 }
	    
}

?>