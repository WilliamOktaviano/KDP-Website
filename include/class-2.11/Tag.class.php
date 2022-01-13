<?php
class Tag extends BaseClass{
    
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'tag_detail';
		//$this->tableNameDetail = 'tag_detail';
       
		$this->tableStatus = 'master_status'; 
		$this->securityObject = 'Tag'; 
       
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');
        //$this->arrData['code'] = array('code');
        $this->arrData['tagname'] = array('name');
		
       	$this->arrLockedTable = array(); 
       
	}
	
	 function getQuery(){ } 
    
	function addData ($arrParam){ }
	
	function editData($arrParam){     
        
		$arrayToJs =  array();
	
		try{		
	  	 	
			if (!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
  
            $arrParam = $this->normalizeParameter($arrParam);
            
            $arrParam['pkey'] = $arrParam['hidId'];
            $arrParam['code'] = '';
            $this->updateData($arrParam,UPDATE_DATA, false);

            $this->oDbCon->endTrans(); 
            $this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   
			
		}catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false, $e->getMessage());  
		}			
		
		return $arrayToJs; 
	}
	 
    
  function normalizeParameter($arrParam, $trim = false){ 
         
	   $arrSearch = array("%","+","?","'","/","\\","#","<",">");
	   $arrReplace = array("","","","","","","","","");
      
      
	   $arrParam['name']  =  str_replace($arrSearch,$arrReplace,$arrParam['name']);  
      
        return $arrParam; 
    }
    
}
?>