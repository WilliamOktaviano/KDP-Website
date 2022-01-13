<?php

class RoleTemplate extends BaseClass{ 
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'template_role'; 
		$this->tableStatus = 'master_status';
		$this->tableSecurityRole = 'security_role';	
		$this->tableSecurityObject = 'security_object';	  
		$this->securityAccess = 'security_access';
		$this->securityObject = 'RoleTemplate';
		   
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true,'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
    
		$this->overwriteConfig();
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
	
	function addData($arrParam){
		
		$arrayToJs =  array();
		
		try{
		
            if(!$this->oDbCon->startTrans())
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
						name, 
						statuskey, 
						createdon,
						createdby
					)
					VALUES	( 
						'.$pkey .',  
						'.$this->oDbCon->paramString($arrParam['code']).',
						'.$this->oDbCon->paramString($arrParam['name']).',
						'.$this->oDbCon->paramString($arrParam['selStatus']).', 
						now(),
						'.$this->oDbCon->paramString($arrParam['createdBy']).'								
					)
			';
			 
			 
			$this->oDbCon->execute($sql);
			
			$arrParam['hidId'] = $pkey;
			$this->createDetail($arrParam);
			 
            $this->setTransactionLog(INSERT_DATA,$pkey);	
            
			$this->oDbCon->endTrans();
					 
			$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   
					 
		} catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false, $e->getMessage());   
		}
		
		return $arrayToJs; 	 	
		 
	}


	function editData($arrParam){  
		
		$arrayToJs =  array();
			
		try{	   
            
			if(!$this->oDbCon->startTrans())
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
					 name ='.$this->oDbCon->paramString($arrParam['name']).',	 
					 modifiedby = '.$this->oDbCon->paramString($arrParam['modifiedBy']).',
					 modifiedon = now()
					WHERE	
					 pkey = '.$this->oDbCon->paramString($arrParam['hidId']).' 
					
			';    
			 
			$this->oDbCon->execute($sql);
            
            $this->createDetail($arrParam);
           
            $this->setTransactionLog(UPDATE_DATA,$arrParam['hidId']);	
            
			$this->oDbCon->endTrans();  
			$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   
					
				
		} catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false, $e->getMessage());  
		}		
				 
 		return $arrayToJs; 
	}
	 
	
	function createDetail($arrParam){
		 
		$sql = 'delete from '.$this->tableSecurityRole.'  where rolekey = ' . $arrParam['hidId'];
		$this->oDbCon->execute($sql);
		 
		
		$security = new Security();
		$rsSecurityObject  = $security->generateSecurityObject(); 
		
		for ($i=0;$i<count($rsSecurityObject);$i++){
		 	
			if (!isset($arrParam['chkList' . $rsSecurityObject[$i]['pkey']]))
				continue;
		
			for($j=0;$j<count($arrParam['chkList' . $rsSecurityObject[$i]['pkey']]);$j++){
				$sql = '
					INSERT INTO		
					'.$this->tableSecurityRole.' ( 
						rolekey,
						objectkey,
						statuskey 
						)
						VALUES	(
						'.$arrParam['hidId'].', 
						'.$this->oDbCon->paramString($rsSecurityObject[$i]['pkey']).', 
						'.$this->oDbCon->paramString($arrParam['chkList' . $rsSecurityObject[$i]['pkey']][$j]).' 
					)
				';
				
				$this->oDbCon->execute($sql);
			} 
		}
	} 
	 
	function validateForm($arr,$pkey = ''){
		  
		  
		$arrayToJs = array();
	   
	    $code =  $arr['code']; 
	    $name =  $arr['name']; 
		
		$rs = $this->isValueExisted($pkey,'code',$code);	 
		if(empty($code)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['code'][1]);
		}else if(count($rs) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['code'][2]);
		}
		  
	 	$rsName = $this->isValueExisted($pkey,'name',$name);	

		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][1]);
		}else if(count($rsName) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][2]);
		}
	
		return $arrayToJs;
	 }
	  
	 
	function delete($id, $forceDelete = false,$reason = ''){ 
		 
		$arrayToJs =  array();
			
		try{
			
			$arrayToJs = $this->validateDelete($id);
				if (!empty($arrayToJs)) 
					return $arrayToJs;
		 	
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
			  		  
			$sql = 'delete from  '.$this->tableName.' where pkey = ' . $this->oDbCon->paramString($id);
			$this->oDbCon->execute($sql);
		
			$sql = 'delete from  '.$this->tableSecurityRole.' where rolekey = ' . $this->oDbCon->paramString($id);
			$this->oDbCon->execute($sql);
             
            $this->setTransactionLog(DELETE_DATA,$id);	
		 
			$this->oDbCon->endTrans();
				 
			$arrayToJs[0]['valid'] = true;
			$arrayToJs[0]['message'] = $this->lang['dataHasBeenSuccessfullyUpdated'];
			 
		} catch(Exception $e){
			$this->oDbCon->rollback(); 
			 
			$arrayToJs[0]['valid'] = false;
			$arrayToJs[0]['message'] = $e->getMessage();
			
		}		
				
 		return $arrayToJs; 
	}

	  function validateDelete($id){
		    
		$arrayToJs = array();
		$rs = $this->getDataRowById($id);
		
		if ($rs[0]['systemVariable'] == 1)  {
			$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['name'].'</strong>. ' . $this->errorMsg[211]); 
		} 
		 
		return $arrayToJs;
	 } 

	 function getSecurityRole($rolekey){
		$sql = 'select 
                    '.$this->tableSecurityRole.'.*, 
                    '.$this->tableSecurityObject.'.modulename
                from 
                    '.$this->tableSecurityRole.', 
                    '.$this->tableName.' , 
                    '.$this->tableSecurityObject.'
                where
			         '.$this->tableSecurityRole.'.rolekey =  '.$this->tableName.' .pkey and
	                 '.$this->tableSecurityRole.' .objectkey =  '.$this->tableSecurityObject.'.pkey and
			         rolekey = '.$this->oDbCon->paramString($rolekey);

        $rs =  $this->oDbCon->doQuery($sql);
		 
		return $rs;
	}
    
    function getEmployeeSecurityTemplate($userkey){
       $sql = 'select 
                    '.$this->securityAccess.'.* 
                from 
                    '.$this->securityAccess.' 
                where
			         '.$this->securityAccess.'.userkey =  '.$this->oDbCon->paramString($userkey);

        $rs =  $this->oDbCon->doQuery($sql);
		 
        //$this->setLog($sql);
		return $rs;
    }
     
	 
  }

?>