<?php
class CareerCategory extends Category{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'career_category';  
		$this->securityObject = 'CareerCategory'; 
		 
        $this->arrLockedTable = array();
        $defaultFieldName = 'categorykey'; 
        array_push($this->arrLockedTable, array('table'=>'job_opportunities','field'=>$defaultFieldName)); 
                   
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'category','title' => 'category','dbfield' => 'name','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'parent','title' => 'parent','dbfield' => 'parentname','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'description','title' => 'note','dbfield' => 'shortdescription',  'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
       
		$this->overwriteConfig();
        
       $this->includeClassDependencies(array(
              'JobOpportunities.class.php',  
        ));
	}
	  
	function validateDelete($id){
		    
		$arrayToJs = array();
		$rs = $this->getDataRowById($id);
		
		if ($rs[0]['systemVariable'] == 1)  {
			$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['name'].'</strong>. ' . $this->errorMsg[211]);
			return $arrayToJs;
		}
		
	
		$jobOpportunities = new JobOpportunities();
		$rsItem = $jobOpportunities->searchData($jobOpportunities->tableName.'.categorykey', $id,true);  
		if(!empty($rsItem)){
			$rsCategory = $this->getDataRowById($id);
			$this->addErrorList($arrayToJs,false,'<strong>'.$rsCategory[0]['name']. '</strong>. '. $this->errorMsg[900] .' <strong>(' . $rsItem[0]['code'] . ' - ' . $rsItem[0]['title'] . ')</strong>.'); 
		}
	 
		return $arrayToJs;
	  
	 }
    
	  
}
		
?>