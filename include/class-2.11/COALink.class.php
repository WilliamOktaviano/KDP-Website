<?php 
class COALink extends BaseClass{
 
    function __construct(){
		
		parent::__construct();
     
		$this->tableName = 'coa_link';
		$this->tableCOA = 'chart_of_account';
		$this->securityObject = 'COALink';
		$this->tableStatus = 'master_status';
	  
		  
   }
    function getQuery(){
	   
	   return '
			select
					'.$this->tableName. '.*
				from 
					'.$this->tableName . '
 		' .$this->criteria ; 
	
    }  
        
    function editData($arrParam){ 
		
		$arrayToJs =  array();
			
		try{
 
			if (!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]); 

				for ($i=0;$i<count($arrParam['hidcoakey']);$i++){
		  			$this->updateCOALink($arrParam['hidcategorykey'][$i], $arrParam['hidcoakey'][$i],'','', $arrParam['hidrefkey'][$i]);
		  		}
								
			$this->oDbCon->endTrans();  
			$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']); 
			
		} catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false, $e->getMessage());
		}
		
		return $arrayToJs;
	}
	
	function updateCOALink ($categorykey, $coakey, $tablename="", $reftablekey="", $refkey=""){

		$sql = 'delete 
                    from 
                ' . $this->tableName .' 
                where 
                    categorykey = ' . $this->oDbCon->paramString($categorykey) . ' and 
                    reftable = ' . $this->oDbCon->paramString($tablename) . ' and 
                    reftablekey = ' . $this->oDbCon->paramString($reftablekey) . ' and 
                    refkey = ' . $this->oDbCon->paramString($refkey);
        
        //$this->setLog($sql,true);
		$this->oDbCon->execute($sql);			
        
        if(empty($coakey)) return;

         $sql = 'insert into 
            ' . $this->tableName .' (
                reftable,
                reftablekey,
                refkey,
                coakey,
                categorykey
            ) values (
                '.$this->oDbCon->paramString($tablename).',
                '.$this->oDbCon->paramString($reftablekey).',
                '.$this->oDbCon->paramString($refkey).',
                '.$this->oDbCon->paramString($coakey).' ,
                '.$this->oDbCon->paramString($categorykey).' 
            )'; 

          //$this->setLog($sql,true);
          $this->oDbCon->execute($sql);  
		 
	}
	
	function getCOALink ($categorykey, $tablename="", $reftablekey="", $refkey=""){
		$sql = 'select 
					coakey,
					'.$this->tableCOA.'.code,
					'.$this->tableCOA.'.name,
					concat('.$this->tableCOA. '.code," - ",'.$this->tableCOA.'.name) as value
				from 
					'. $this->tableName .',
					'.$this->tableCOA. '
				where
					'. $this->tableName .'.categorykey = ' . $this->oDbCon->paramString($categorykey).' and
					'. $this->tableName .'.coakey = '.$this->tableCOA.'.pkey and 
					'. $this->tableName .'.reftable = '.$this->oDbCon->paramString($tablename).' and
					'. $this->tableName .'.reftablekey = '.$this->oDbCon->paramString($reftablekey);
        
        if (!empty($refkey))
            $sql  .= ' and  '. $this->tableName .'.refkey = '.$this->oDbCon->paramString($refkey);
                         
		return 	$this->oDbCon->doQuery($sql); 
	}
	
}

?>