<?php

class NewsCategory extends Category{ 
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'news_category';  
		$this->securityObject = 'NewsCategory';  
		
        $this->arrLockedTable = array();
        $defaultFieldName = 'categorykey'; 
        array_push($this->arrLockedTable, array('table'=>'news','field'=>$defaultFieldName)); 
   }
     
 
	function validateDelete($id){
		    
		$arrayToJs = array();
		
		$news = new News();
		$rsData = $news->searchData($news->tableName.'.categorykey', $id,true); 
				
		if(!empty($rsData)){
			$rs = $this->getDataRowById($id);
			$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['name']. '</strong>. '. $this->errorMsg[900] .' <strong>(' . $rsData[0]['code'] . ' - ' . $rsData[0]['title'] . ')</strong>.');
		}
	 
		return $arrayToJs;
	 }
	    
}

?>