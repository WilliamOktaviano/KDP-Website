<?php
class CareerField extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'career_field';  
		$this->securityObject = 'CareerField'; 
		$this->tableStatus = 'master_status';
        $this->uploadFolder = 'career-field/'; 
		 
        $this->arrLockedTable = array();
        $defaultFieldName = 'jobfieldkey';
        array_push($this->arrLockedTable, array('table'=>'job_opportunities','field'=>$defaultFieldName)); 
       
        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code'); 
        $this->arrData['name'] = array('name'); 
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['shortdescription'] = array('shortDesc');
        $this->arrData['file'] = array('fileName');
    
                   
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true,'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
    
		$this->overwriteConfig();
        
        $this->includeClassDependencies(array(
              'JobOpportunities.class.php',  
        ));
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
	  
    function delete($id, $forceDelete = false,$reason = ''){ 
		 
		try{			
				  
				$arrayToJs =  array();
			 	
				if (!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]);
			
		 		 
				$sql = 'delete from  '.$this->tableName.' where pkey = ' . $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);  
				$this->deleteAll($this->defaultDocUploadPath.$this->uploadFolder.$id);
			
        
                $this->setTransactionLog(DELETE_DATA,$id);
            
				$this->oDbCon->endTrans();
										 
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);    
			 
				
			}catch(Exception $e){
				$this->oDbCon->rollback();
				$this->addErrorList($arrayToJs,false, $e->getMessage()); 
		}			
			
		return $arrayToJs;	
	}

	function validateForm($arr,$pkey = ''){
		  				    
		$arrayToJs = parent::validateForm($arr,$pkey); 
        
		$name = $arr['name'];   
		  
	 	$rsItem = $this->isValueExisted($pkey,'name',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['careerField'][1]);
		}else if(count($rsItem) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['careerField'][2]);
		}
		  	
		return $arrayToJs;
	 } 

    function getTotalJobPosition(){
        $jobOpportunities = new JobOpportunities();
        $sql = 'select  
                    '.$this->tableName.'.pkey,
                    '.$this->tableName.'.name,
                    '.$this->tableName.'.file,
                    count( '.$jobOpportunities->tableName.'.pkey )  as totalposition
                from 
                    '.$this->tableName.'
                        left join '.$jobOpportunities->tableName.' on '.$this->tableName.'.pkey = '.$jobOpportunities->tableName.'.jobfieldkey
                where
                    '.$this->tableName.'.statuskey = 1 
                group by
                    '.$this->tableName.'.pkey
                order by 
                    '.$this->tableName.'.name asc';
         
        return $this->oDbCon->doQuery($sql);
        
        
    }    
    function normalizeParameter($arrParam, $trim = false){
        $arrParam['fileName'] = $this->updateImages($arrParam['pkey'], $arrParam['token-item-image-uploader'], $arrParam['item-image-uploader']);    
    
           $arrParam = parent::normalizeParameter($arrParam,true); 
        return $arrParam;
    }
    
	  
}
		
?>
