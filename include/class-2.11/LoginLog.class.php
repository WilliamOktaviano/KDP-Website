<?php

class LoginLog extends BaseClass{
 
   function __construct(){
		
		parent::__construct();

		$this->tableName = 'login_log';  
		$this->tableEmployee = 'employee';  
		$this->tableWarehouse = 'warehouse';  
		$this->tableStatus = 'login_log_status';  
		$this->securityObject = 'LoginLog';
		
   }
    
	function getQuery(){
	   
	   $sql = '
			select
					'.$this->tableName. '.* ,
					'.$this->tableEmployee. '.name as employeename,
					'.$this->tableStatus.'.status as statusname		
				from 
					'.$this->tableName . ' 
                        left join '.$this->tableEmployee. ' on  '.$this->tableName . '.userkey = '.$this->tableEmployee. '.pkey 
                        left join '.$this->tableWarehouse. ' on  '.$this->tableEmployee . '.warehousekey = '.$this->tableWarehouse. '.pkey ,
                    '.$this->tableStatus.' 
				where  		 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey  
 		' .$this->criteria ; 
		  
        return $sql;
    }
	
	function addData($arrParam){
		$arrayToJs =  array();
		
		try{ 
		  
		 	if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
		  
			
			$sql = '
					INSERT INTO		
					 '.$this->tableName .' (  
						logintype,   
                        userkey,
						username,
						ipaddress,
						statuskey,
						createdon 
					)
					VALUES	(  
						'.$this->oDbCon->paramString($arrParam['logintype']).',
						'.$this->oDbCon->paramString($arrParam['userkey']).', 
						'.$this->oDbCon->paramString($arrParam['username']).', 
						'.$this->oDbCon->paramString($_SERVER['REMOTE_ADDR']).', 
						'.$this->oDbCon->paramString($arrParam['statuskey']).', 
						now() 							
					)
			';
			 
			 
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
	
	  function isLockout($username,$logintype){
			 
        // ad problem kalo login nya disela sama yg berhasil, tetep gk ngepek
        $loginAttemptUntilLockout = $this->loadSetting('loginAttemptUntilLockout');
        if (empty($loginAttemptUntilLockout) || !is_numeric($loginAttemptUntilLockout))  
            $loginAttemptUntilLockout = 3;
              
		 $sql = 'SELECT 
                    * 
                FROM 
                    '.$this->tableName .' 
                WHERE 
                    username = '.$this->oDbCon->paramString($username).' and 
                    logintype = '.$logintype.' and   
                    createdon > DATE_SUB(now(), INTERVAL '.$this->loadSetting('lockoutSecond').' SECOND)
                order by createdon desc
                limit '. $loginAttemptUntilLockout .'   
                ';
           
		 $rs = $this->oDbCon->doQuery($sql);
   
           
          for($i=0;$i<count($rs);$i++){ 
              if ($rs[$i]['statuskey'] == 2)
                  $loginAttemptUntilLockout--;
          }
		    
		 if ($loginAttemptUntilLockout > 0)
             return false;
          
          return true;
		 
	 } 
		 
	 
  }

?>