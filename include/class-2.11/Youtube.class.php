<?php

class Youtube extends BaseClass{
   
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'youtube';  
		$this->securityObject = 'Youtube'; 
		$this->tableStatus = 'master_status'; 
	 
   }
   
   function getQuery(){
	   
	   return '
			select
					'.$this->tableName. '.*,
					'.$this->tableStatus.'.status as statusname 	
				from 
					'.$this->tableName . ' , '.$this->tableStatus.' 
				where  		 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
 		' .$this->criteria ; 
		 
    }  
	
    function addData($arrParam){   
		
		$arrayToJs =  array();
	
		try{		
			
            if (!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
            
			$code = $this->getNewCustomCode($arrParam);	 
            $arrParam['code'] = (is_array($code)) ? $code[0] : $code;
            
			$arrayToJs = $this->validateForm($arrParam);
				if (!empty($arrayToJs)) 
					return $arrayToJs;
					
			$pkey = $this->getNextKey($this->tableName);  
			 
		 
			$sql = '
					INSERT INTO		
					 '.$this->tableName .' (
						pkey, 
						code, 
						title,  
						shortdesc, 
						statuskey, 
						youtubeid,  
						createdby,
						createdon
					)
					VALUES	( 
						'.$pkey.', 
						'.$this->oDbCon->paramString($arrParam['code']).',
						'.$this->oDbCon->paramString($arrParam['title']).',   
						'.$this->oDbCon->paramString($arrParam['txtShortDescription']).',  
						'.$this->oDbCon->paramString($arrParam['selStatus']).' ,  
						'.$this->oDbCon->paramString($arrParam['youtubeid']).' ,  
						'.$this->oDbCon->paramString($arrParam['createdBy']).', 
						now()   
					)
			';
			 
		    $this->oDbCon->execute($sql);  
			  
            $this->setTransactionLog(INSERT_DATA,$pkey);
            
			$this->oDbCon->endTrans(); 
			$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   
	 
		}catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false,$e->getMessage());   
		}			
			
		return $arrayToJs; 
	} 
	
	
	function editData($arrParam){    
	  	
		$arrayToJs =  array();
	
		try{		
			
			if (!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]); 
            
			$code = $this->getNewCustomCode($arrParam);	 
            $arrParam['code'] = (is_array($code)) ? $code[0] : $code;
            
			$arrayToJs = $this->validateForm($arrParam,$arrParam['hidId']);
			if (!empty($arrayToJs)) 
					return $arrayToJs; 
			  
			
			$sql = '
						UPDATE	
						 '.$this->tableName .'
						SET	  
						 code ='.$this->oDbCon->paramString($arrParam['code']).',  
						 title ='.$this->oDbCon->paramString($arrParam['title']).',  
						 shortdesc = '.$this->oDbCon->paramString($arrParam['txtShortDescription']).',  
                         statuskey = '.$this->oDbCon->paramString($arrParam['selStatus']).',
						 youtubeid = '.$this->oDbCon->paramString($arrParam['youtubeid']).' ,  
						 modifiedby = '.$this->oDbCon->paramString($arrParam['modifiedBy']).',
						 modifiedon = now() 
						WHERE	
						 pkey = '.$this->oDbCon->paramString($arrParam['hidId']).'
						
				';    

                $this->oDbCon->execute($sql);       
                $this->setTransactionLog(UPDATE_DATA,$arrParam['hidId']);	  
  
				$this->oDbCon->endTrans();  
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   
			
		}catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false,$e->getMessage());  
		}			
			
		return $arrayToJs; 
	}  
	
	 function validateForm($arr,$pkey = ''){
		    
		
		$arrayToJs = parent::validateForm($arr,$pkey); 
         
		$name = $arr['title'];  
		$script = $arr['youtubeid'];  
	   
	 	$rs = $this->isValueExisted($pkey,'title',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['youtube'][1]);
		}else if(count($rs) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['youtube'][2]);
		}
		
		if(empty($script)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['youtube'][3]);
		}
		  
		return $arrayToJs;
	 } 
}

?>