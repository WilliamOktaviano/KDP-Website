<?php
class AutoCode extends Baseclass{
 
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = '_code';
		$this->tableUserCode = '_user_code';	
		$this->tableStatus = 'master_status';
		$this->securityObject = 'AutoCode';
		
	}
	

	 function getQuery(){
	   
	   return '
			select
					'.$this->tableName. '.*,
					'.$this->tableUserCode.'.counter 
				from
					'.$this->tableName.','.$this->tableUserCode.' where
					'.$this->tableName.'.pkey = '.$this->tableUserCode.'.codekey
 		' .$this->criteria ; 
		 
    }
    
	function addData($arrParam){
		$arrayToJs =  array();
		
		try{	 
		
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
			 
		        $pkey = $this->getNextKey($this->tableName); 

				$sql = ' INSERT INTO	
							 '.$this->tableName .'( 
							 	pkey,
								code,
								label,
								prefix  
							 )
							VALUES (
						 	   '.$this->oDbCon->paramString($pkey).',
							   '.$this->oDbCon->paramString($arrParam['code'] ).',	
							   '.$this->oDbCon->paramString($arrParam['label'] ).',	
							   '.$this->oDbCon->paramString($arrParam['prefix'] ).' 
							)
									
				';    
				
				$this->oDbCon->execute($sql);
				
				
				$sql = 'INSERT INTO	
							 '.$this->tableUserCode .'( 
							 	codekey  
							 )
							VALUES (
							   '.$this->oDbCon->paramString($pkey).'
							)';
				
				$this->oDbCon->execute($sql);			
					 
				$this->oDbCon->endTrans();   
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']); 
					
				
		} catch(Exception $e){  
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false, $e->getMessage());  
		}		
				 
 		return $arrayToJs; 
	}
	
	function editData($arrParam) {
		
		$arrayToJs =  array();
		try {
			
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
			  
				$arrPrefix = $arrParam['prefix'];
				$arrDigit = $arrParam['digit'];
				$arrCounter = $arrParam['counter'];
				$arrHidId = $arrParam['hidId'];
			  	
				for ($i=0;$i<count($arrHidId);$i++){ 
					 
					$useautocode=0;
					if(!empty($arrParam['useautocode'.$arrHidId[$i]])) 
							$useautocode=1;  
			
					$digit=5;
					if(!empty($arrDigit[$i]) && is_numeric($arrDigit[$i]) && $arrDigit[$i] > 0) 
							$digit=$arrDigit[$i];  
			 
					$sql = '
						update
							' . $this->tableName . ', ' . $this->tableUserCode . '
						set
							useautocode ='.$this->oDbCon->paramString($useautocode).',
							prefix ='.$this->oDbCon->paramString($arrPrefix[$i]).',
							digit ='.$this->oDbCon->paramString($digit).',
							prefix ='.$this->oDbCon->paramString($arrPrefix[$i]).',
							counter ='.$this->oDbCon->paramString($arrCounter[$i]).'
						where
							'.$this->tableName.'.pkey = '.$this->tableUserCode.'.codekey and
							'. $this->tableName . '.pkey = '.$this->oDbCon->paramString($arrHidId[$i]).'
							
					';
					 
					$this->oDbCon->execute($sql);
			 		 
				}
			
			//$this->addTrail($sql);
			$this->oDbCon->endTrans();  
			$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']); 
			
		} catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false, $e->getMessage());
		}
		
		return $arrayToJs;
	}
	
	
	function editDataCode($arrParam){
		$arrayToJs =  array();
	 
		try {
			
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
			  
			 $sql = 'update 
			 			' . $this->tableName . ' 
					set 
						code = '.$this->oDbCon->paramString($arrParam['code']).',
						label = '.$this->oDbCon->paramString($arrParam['label']).',
						prefix = '.$this->oDbCon->paramString($arrParam['prefix']).'
					where  code = '.$this->oDbCon->paramString($arrParam['hidCode'])
					;
			  
			 $this->oDbCon->execute($sql);
			 	 
			  
			//$this->addTrail($sql);
			$this->oDbCon->endTrans();  
			$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']); 
			
		} catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false, $e->getMessage());
		}
		
		return $arrayToJs;
	}
	
	 function delete($id, $forceDelete = false,$reason = ''){ 
		
		$arrayToJs =  array();
		 
		try{			 
		
				$arrayToJs = $this->validateDelete($id);
				if (!empty($arrayToJs)) 
					return $arrayToJs;
					
					
				if (!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]);
			
		 		$sql = 'delete from  '.$this->tableName.' where pkey = ' . $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);  
				
				$sql = 'delete from  '.$this->tableUserCode.' where codekey = ' . $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);  
				
				 
                $this->setTransactionLog(DELETE_DATA,$id);
            
				$this->oDbCon->endTrans();
										 
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);    
			 
				
			}catch(Exception $e){
				$this->oDbCon->rollback();
				$this->addErrorList($arrayToJs,false, $e->getMessage()); 
		}			
			
		return $arrayToJs;	
	}
	 
	
}
?>