<?php

class CityCategory extends Category{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'city_category';  
		$this->securityObject = 'CityCategory'; 
	 
        $this->arrLockedTable = array();
        $defaultFieldName = 'categorykey';
        array_push($this->arrLockedTable, array('table'=>'city','field'=>$defaultFieldName)); 
             
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true,'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
             
		$this->overwriteConfig(); 
   }
    
   function getQuery($count=''){
	     
	    $select = 'select
					'.$this->tableName. '.*,
					'.$this->tableStatus.'.status as statusname,
					parentcat.name as parentname';

		if (!empty($count))
			$select = 'select count('.$this->tableName . '.pkey) as totalrows';
	   
	    return $select.' from
					'.$this->tableName . ' left join '.$this->tableName . ' parentcat on 	parentcat.pkey = '.$this->tableName . '.parentkey ,'.$this->tableStatus.' 
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
 		' .$this->criteria ; 
		 
    }
 
	    
}

?>