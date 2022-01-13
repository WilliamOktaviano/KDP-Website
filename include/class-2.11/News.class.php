<?php

class News extends BaseClass{
  
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'news'; 
		$this->tableCategory = 'news_category';  
		$this->securityObject = 'News'; 
		$this->tableStatus = 'master_status';
		$this->uploadFolder = 'news/'; 
		
   }
   
   
	 function getQuery(){
	   
	   return '
				select
					'.$this->tableName. '.*,
					'.$this->tableStatus.'.status as statusname , 
					'.$this->tableCategory.'.name as categoryname		
				from 
					'.$this->tableName . ' left join '.$this->tableCategory.' on '.$this->tableName . '.categorykey = '.$this->tableCategory.'.pkey, '.$this->tableStatus.' 
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
			 
			$fileName = $this->updateImage($pkey, $arrParam['token-item-image-uploader'], $arrParam['item-image-uploader']);  
			 
			
			$sql = '
					INSERT INTO		
					 '.$this->tableName .' (
						pkey, 
						code, 
						title, 
						categorykey,
						shortdesc,
						publishdate,
						statuskey,
						file,
						detail,  
						createdby,
						createdon
					)
					VALUES	( 
						'.$pkey.', 
						'.$this->oDbCon->paramString($arrParam['code']).',
						'.$this->oDbCon->paramString($arrParam['title']).',  
						'.$this->oDbCon->paramString($arrParam['hidCategoryKey']).' , 
						'.$this->oDbCon->paramString($arrParam['txtShortDescription']).', 
						'.$this->oDbCon->paramDate($arrParam['publishDate'],' / ').',  
						'.$this->oDbCon->paramString($arrParam['selStatus']).' , 
						'.$this->oDbCon->paramString($fileName).' , 
						\''.addslashes($arrParam['txtDetail']).'\',
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
						 title ='.$this->oDbCon->paramString($arrParam['title']).', 
						 categorykey = 	'.$this->oDbCon->paramString($arrParam['hidCategoryKey']).' ,  
						 shortdesc = '.$this->oDbCon->paramString($arrParam['txtShortDescription']).', 
						 publishdate = '.$this->oDbCon->paramDate($arrParam['publishDate'],' / ').', 
                         statuskey = '.$this->oDbCon->paramString($arrParam['selStatus']).',
						 detail = \''.addslashes($arrParam['txtDetail']).'\', 
						 file = '.$this->oDbCon->paramString($fileName).',     
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
	 
          
        $arrImage = explode(",",$arr['item-image-uploader']);
        for($i=0;$i<count($arrImage);$i++){
            $path = $this->uploadTempDoc.$this->uploadFolder.$arr['token-item-image-uploader']; 
            if (filesize($path.'/'.$arrImage[$i]) > (pow(1024,2) * PLAN_TYPE['maximagesize']))
                $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][4] .' ('.$this->lang['max'].' '. $this->formatNumber(PLAN_TYPE['maximagesize']). ' MB)' );
        }
         
	 	$rs = $this->isValueExisted($pkey,'title',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,"Judul berita harus diisi.");
		}else if(count($rs) <> 0){
			$this->addErrorList($arrayToJs,false,"Judul berita telah terdaftar. Silahkan memilih judul berita lain.");
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