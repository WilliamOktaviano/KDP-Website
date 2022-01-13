<?php

class Banner extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'banner';  
		$this->tableNamePosition = 'banner_position';  
		$this->securityObject = 'Banner'; 
		$this->tableStatus = 'master_status';
		$this->uploadFolder = 'banner/'; 
		
   }
   
   function getQuery(){
	   
	   $sql = '
				select
					'.$this->tableName. '.*,
					'.$this->tableStatus.'.status as statusname ,
					'.$this->tableNamePosition.'.name as positionname
				from 
					'.$this->tableName . ','.$this->tableStatus.' , '.$this->tableNamePosition.'
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey and  
					'.$this->tableName . '.poskey = '.$this->tableNamePosition.'.pkey
 		' .$this->criteria ; 
	 
        return $sql;
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
			 
			$fileName = $this->updateImage($pkey, $arrParam['token-item-image-uploader'], $arrParam['item-image-uploader']);  
			
			$sql = '
					INSERT INTO		
					 '.$this->tableName .' (
						pkey, 
						code, 
						name, 
						file,
						poskey,
						statuskey,
						url, 
                        trdesc,
						orderlist ,
						createdby,
						createdon
					)
					VALUES	( 
						'.$pkey.', 
						'.$this->oDbCon->paramString($arrParam['code']).',
						'.$this->oDbCon->paramString($arrParam['name']).',  
						'.$this->oDbCon->paramString($fileName).' , 
						'.$this->oDbCon->paramString($arrParam['selPosition']).',  
						'.$this->oDbCon->paramString($arrParam['selStatus']).' , 
						'.$this->oDbCon->paramString($arrParam['url']).' , 
						\''.addslashes($arrParam['txtDetail']).'\',
						'.$this->oDbCon->paramString($this->unFormatNumber($arrParam['orderList'])).' , 
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
					
			
                $fileName = $this->updateImage($arrParam['hidId'], $arrParam['token-item-image-uploader'], $arrParam['item-image-uploader']);  

                $sql = '
                            UPDATE	
                             '.$this->tableName .'
                            SET	 
                             code ='.$this->oDbCon->paramString($arrParam['code']).', 
                             name ='.$this->oDbCon->paramString($arrParam['name']).',  
                             file = '.$this->oDbCon->paramString($fileName).',   
                             poskey = '.$this->oDbCon->paramString($arrParam['selPosition']).', 
                             url = '.$this->oDbCon->paramString($arrParam['url']).', 
                             orderlist = '.$this->oDbCon->paramString($this->unFormatNumber($arrParam['orderList'])).',  
                             trdesc = \''.addslashes($arrParam['txtDetail']).'\',
                             statuskey = '.$this->oDbCon->paramString($arrParam['selStatus']).',
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
         
        $name = $arr['name']; 
		$url = $arr['url']; 
		$order = $arr['orderList']; 
	 	      
        $arrImage = explode(",",$arr['item-image-uploader']);
        for($i=0;$i<count($arrImage);$i++){
            $path = $this->uploadTempDoc.$this->uploadFolder.$arr['token-item-image-uploader']; 
            if (filesize($path.'/'.$arrImage[$i]) >  (pow(1024,2) * PLAN_TYPE['maximagesize']) )
                $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][4] .' ('.$this->lang['max'].' '. $this->formatNumber(PLAN_TYPE['maximagesize']). ' MB)' );
        }
         
         
	 	$rs = $this->isValueExisted($pkey,'name',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['banner'][1]);
		}else if(count($rs) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['banner'][2]);
		}
		  
		if (!empty($url) && filter_var($url, FILTER_VALIDATE_URL) === false) {
			$this->addErrorList($arrayToJs,false,$this->errorMsg['url'][3]);
		} 
		
		if (!is_numeric($order)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['orderList'][2]);
		}
		
		return $arrayToJs;
	 }

     function updateImage($pkey,$token,$arrImage){		
		 
		$sourcePath = $this->uploadTempDoc.$this->uploadFolder.$token;
		$destinationPath = $this->defaultDocUploadPath.$this->uploadFolder;
		
		if(!is_dir($sourcePath)) 
			return;
	
			
		if(!is_dir($destinationPath)) 
			mkdir ($destinationPath,  0755, true);
			
		$destinationPath .= $pkey;  
 
 		//delete previous images	    
		$this->deleteAll($destinationPath);   
		 
		if (!empty($arrImage))	{
			$arrImage = explode(",",$arrImage); 
			$this->uploadImage($sourcePath, $destinationPath,$arrImage[0]); 
			return $arrImage[0]; 
		}
		
		return '';
		
	} 
	
	  function getAllPosition () { 
	 	 
		$sql = '
				select 
					pkey,
					name,
					concat('.$this->tableNamePosition.'.name," ", '.$this->tableNamePosition.'.bestfitsize) as namewithsize
				from 
					'.$this->tableNamePosition.'
				order by
					pkey asc
			';
			
		return $this->oDbCon->doQuery($sql); 
	 
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
	    
}

?>