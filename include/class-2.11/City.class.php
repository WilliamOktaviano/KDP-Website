<?php  
class City extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'city';
		$this->tableCategory = 'city_category';
		$this->tableStatus = 'master_status'; 
	   
		$this->securityObject = 'City';
       
        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code'); 
        $this->arrData['name'] = array('cityname');
        $this->arrData['categorykey'] = array('hidCategoryKey');
        $this->arrData['isdefaultshipment'] = array('chkIsDefaultShipment');
        $this->arrData['statuskey'] = array('selStatus'); 
         
		$this->arrLockedTable = array();
        $defaultFieldName = 'citykey';
        array_push($this->arrLockedTable, array('table'=>'employee','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'customer','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'supplier','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'depo','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'service_order_header','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'testimonial','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'utc','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'warehouse','field'=>$defaultFieldName));
               
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true,'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'category','title' => 'category','dbfield' => 'categoryname','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
         
        $this->includeClassDependencies(array(
              'Category.class.php',
              'CityCategory.class.php'
        ));

		$this->overwriteConfig();
   }
	 
	 
	 
    function getQuery(){
	   
	   return '
				select
					'.$this->tableName. '.*,
					'.$this->tableStatus.'.status as statusname ,
					'.$this->tableCategory.'.name as categoryname,
                    concat ('.$this->tableName. '.name, ", ", '.$this->tableCategory.'.name) as citycategoryname
				from 
					'.$this->tableName . ','.$this->tableStatus.' , '.$this->tableCategory.'
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey and  
					'.$this->tableName . '.categorykey = '.$this->tableCategory.'.pkey
 		' .$this->criteria ; 
		 
    }
 
    function afterUpdateData($arrParam, $action){ 
			$this->updateIsDefaultShipment($arrParam['chkIsDefaultShipment'],$arrParam['pkey']);
    }
     
    function validateForm($arr,$pkey = ''){
		  
		$arrayToJs = parent::validateForm($arr,$pkey); 
	   
	 	$cityname = $arr['cityname'];   
	 	$categorykey = isset($arr['hidCategoryKey']) ? $arr['hidCategoryKey'] : 0;    
			 
		$rs = $this->isValueExisted($pkey,'name',$cityname); 
		if(empty($cityname)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['city'][1]);
		}else if (count($rs) <> 0){  
			$this->addErrorList($arrayToJs,false,$this->errorMsg['city'][2]);
		} 
		
        if(empty($categorykey)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['category'][1]);
		} 
          
		 return $arrayToJs;
	 }
    
	  function generateDefaultQueryForAutoComplete($returnField){ 
      
          $sql = 'select
					'.$returnField['key']. ',
                    concat('.$returnField['value']. ',", ",'.$this->tableCategory.'.name) as value 
				from 
					'.$this->tableName . ','.$this->tableStatus.' , '.$this->tableCategory.'
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey and  
					'.$this->tableName . '.categorykey = '.$this->tableCategory.'.pkey
			';
          
        
        return $sql;
        
    } 
     
	function updateIsDefaultShipment($isDefaultShipment,$id){ 
        if (!$isDefaultShipment)
            return;
        
		$sql = 'update  ' . $this->tableName . ' set isdefaultshipment = 0  where pkey <> '.$this->oDbCon->paramString($id).' order by pkey asc' ;
		$this->oDbCon->execute($sql); 
    }
 
          
    function normalizeParameter($arrParam, $trim = false){  
            
        $arrParam = parent::normalizeParameter($arrParam,true); 
        return $arrParam; 
    }   
    
    
  }

?>
