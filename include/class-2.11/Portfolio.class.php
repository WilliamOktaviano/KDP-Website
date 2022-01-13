<?php

class Portfolio extends BaseClass{
    
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'portfolio'; 
		$this->tableCategory = 'portfolio_category';  
	    $this->tableLangValue = 'portfolio_lang';
		$this->securityObject = 'Portfolio'; 
		$this->tableStatus = 'portfolio_status';
        $this->tableImage = 'portfolio_image'; 
		$this->uploadFolder = 'portfolio/';
        $this->uploadFileFolder = 'portfolio-file/';  
       
        $this->allowedStatusForEdit = array(1,2);
        
        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataLang, 'tableName' => $this->tableLangValue));
       
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name'); 
        $this->arrData['categorykey'] = array('selCategory');
        $this->arrData['shortdesc'] = array('shortdescription');
        $this->arrData['companyname'] = array('companyName');
        $this->arrData['url'] = array('url'); 
        $this->arrData['maplocation'] = array('txtMapLocation');
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
      
        $this->newLoad = true;
       
        $this->includeClassDependencies(array(
           'PortfolioCategory.class.php'
        ));
       
        $this->overwriteConfig();
   }
   
   function getQuery(){
	   
	   return '
			select
				'.$this->tableName. '.*,
				'.$this->tableStatus.'.textcolor as statuscolor,
				'.$this->tableStatus.'.status as statusname , 
				'.$this->tableCategory.'.name as categoryname		
			from 
				'.$this->tableName . ' left join '.$this->tableCategory.' on '.$this->tableName . '.categorykey = '.$this->tableCategory.'.pkey, '.$this->tableStatus.' 
			where  		 
				'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
 		' .$this->criteria ; 
		 
   }
 
	
	 function validateForm($arr,$pkey = ''){
		    
		$arrayToJs = array();
		
		$code = $arr['code'];
		$name = $arr['name'];  
	 
	 	$rs = $this->isValueExisted($pkey,'code',$code);	 
		if(empty($code)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['code'][1]);
		}else if(count($rs) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['code'][2]);
		}
		
	 	$rs = $this->isValueExisted($pkey,'name',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['portfolio'][1]);
		}else if(count($rs) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['portfolio'][2]);
		}
		  
		return $arrayToJs;
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
    
        
    function normalizeParameter($arrParam, $trim = false){ 
                 
        $arrParam['fileName'] = $this->updateImages($arrParam['pkey'], $arrParam['token-item-image-uploader'], $arrParam['item-image-uploader']);    
        
        $arrParam = $this->updateOthersLangValue($arrParam, $this->arrData); 
        $arrParam = parent::normalizeParameter($arrParam,true); 
          
         return $arrParam; 
    }
	    
}

?>