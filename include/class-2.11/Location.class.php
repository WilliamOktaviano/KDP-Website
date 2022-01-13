<?php  
class Location extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'location';
        $this->tableCity = 'city';
        $this->tableCityCategory = 'city_category';
		$this->tableStatus = 'master_status'; 
	   
		$this->securityObject = 'Location'; 
        
        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code'); 
        $this->arrData['name'] = array('name');
        $this->arrData['citykey'] = array('hidCityKey'); 
        $this->arrData['statuskey'] = array('selStatus');  
       
		$this->arrLockedTable = array();
        $defaultFieldName = 'locationkey';
        array_push($this->arrLockedTable, array('table'=>'depot','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'trucking_service_order_header','field'=> 'stuffinglocationkey')); 
        array_push($this->arrLockedTable, array('table'=>'trucking_service_order_header','field'=> 'consigneelocationkey')); 
        array_push($this->arrLockedTable, array('table'=>'trucking_service_work_order','field'=> $defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'cost_rate_header','field'=> $defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'consignee','field'=> $defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'job_opportunities','field'=> $defaultFieldName)); 
        
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'location','title' => 'location','dbfield' => 'name','default'=>true,'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'city','title' => 'city','dbfield' => 'citycategoryname','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
            
        $this->includeClassDependencies(array( 
              'Category.class.php',
              'CityCategory.class.php',
              'City.class.php',  
        ));
       
		$this->overwriteConfig();
   }
	 
	 
	 
    function getQuery(){
	   
	   return '
				select
					'.$this->tableName. '.*,
                    '.$this->tableCity.'.name as cityname,
                    concat('.$this->tableCity.'.name, ", ",'.$this->tableCityCategory.'.name) as citycategoryname ,
					'.$this->tableStatus.'.status as statusname
				from 
					'.$this->tableName. ' 
                        left join  '.$this->tableCity. ' on  '.$this->tableName . '.citykey = '.$this->tableCity.'.pkey
						 left join '.$this->tableCityCategory.' on '.$this->tableCity . '.categorykey = '.$this->tableCityCategory.'.pkey ,
                    '.$this->tableStatus.'
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
 		         ' .$this->criteria ; 
		 
    } 
	
	  function validateForm($arr,$pkey = ''){
		  
		$arrayToJs = parent::validateForm($arr,$pkey); 
	   
	 	$locationname = $arr['name']; 
			 
		$rs = $this->isValueExisted($pkey,'name',$locationname); 
		if(empty($locationname)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['location'][1]);
		}else if (count($rs) <> 0){  
			$this->addErrorList($arrayToJs,false,$this->errorMsg['location'][2]);
		} 
           
		 return $arrayToJs;
	 }
    
	  function generateDefaultQueryForAutoComplete($returnField){ 
      
          $sql = 'select
					'.$returnField['key']. ',
                    '.$returnField['value'].' as value 
				from 
					'.$this->tableName . ','.$this->tableStatus.'
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey
			';
          
        return $sql;
    }
    
     
    function normalizeParameter($arrParam, $trim=false){ 
        
        $arrParam = parent::normalizeParameter($arrParam,true);   
        return $arrParam;
    }
        
    
  }

?>
