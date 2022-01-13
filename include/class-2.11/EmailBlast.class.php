<?php
class EmailBlast extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'email_blast';
		$this->securityObject = 'EmailBlast';
		
	}
	
	function getQuery(){
		
		return '
			select
				'.$this->tableName. '.* 
			from 
				'.$this->tableName . ','.$this->tableStatus.' where  		
				1=1
	 		' .$this->criteria ;
		
	}
	
	function addData ($arrParam){
		$arrayToJs = array();
		
		try{
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
            
			$arrayToJs = $this->validateForm($arrParam);
				if (!empty($arrayToJs))
					return $arrayToJs;
			
			
			$pkey = $this->getNextKey($this->tableName);
			
			$sql = '
					INSERT INTO
					'.$this->tableName.'(
						pkey, 
						itemkey,
						customerkey 
					)
					VALUES (
						'.$pkey.', 
						'.$this->oDbCon->paramString($arrParam['itemKey']).',
						'.$this->oDbCon->paramString($arrParam['customerKey']).' 
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
	
	function validateForm($arr,$pkey = ''){
		  
		$arrayToJs = array(); 
			 
		if(empty($_SESSION[$this->loginSession]['id'])){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['session'][1]);
		}
		 
		return $arrayToJs;
	 }	 
	 
	function isNotificationExisted($customerkey,$itemkey){ 
		$sql = 'select * from notification where customerkey = '.$this->oDbCon->paramString($customerkey).' and itemkey = '.$this->oDbCon->paramString($itemkey).' and statuskey = 1';
 		return  $this->oDbCon->doQuery($sql);
	}
}
?>