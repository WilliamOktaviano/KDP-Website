<?php
class Download extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'download';
		$this->tableFile = 'download_file';
        $this->tableCategory = 'download_category';
		$this->tableStatus = 'master_status';
		$this->securityObject = 'Download'; 
		$this->uploadImageFolder = 'download-image/';
		$this->uploadFileFolder = 'download/';
		 
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['useexternallink'] = array('chkExternal');  
        $this->arrData['externallink'] = array('externalLink');   
        $this->arrData['categorykey'] = array('hidCategoryKey'); 
        $this->arrData['shortdesc'] = array('shortDesc');   
        $this->arrData['statuskey'] = array('selStatus');   
        $this->arrData['file'] = array('fileName');
       
      
        $this->arrDataListAvailableColumn = array(); 
       
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true,'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        
        $this->includeClassDependencies(array( 
              'DownloadCategory.class.php'
        ));


		$this->overwriteConfig();
       
	}
	
	 function getQuery(){
	   
	   return '
			select
					'.$this->tableName. '.*,
					'.$this->tableStatus.'.status as statusname,
                    '.$this->tableCategory. '.name as categoryname
				from
					'.$this->tableName.'
                        left join '.$this->tableCategory.' on  '.$this->tableName.'.categorykey = '.$this->tableCategory.'.pkey ,
                    '.$this->tableStatus.'
                where
					'.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey
                   
 		' .$this->criteria ; 
		 
    }
    
	  
	function delete($id, $forceDelete = false,$reason = ''){ 
		 
		try{			
				  
				$arrayToJs =  array();
			 	
				if (!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]);
			
		 		 
				$sql = 'delete from  '.$this->tableName.' where pkey = ' . $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);  
				$this->deleteAll($this->defaultDocUploadPath.$this->uploadFileFolder.$id);
				$this->deleteAll($this->defaultDocUploadPath.$this->uploadImageFolder.$id);
			
                $this->setTransactionLog(DELETE_DATA,$id);
            
				$this->oDbCon->endTrans();
										 
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);    
			 
				
			}catch(Exception $e){
				$this->oDbCon->rollback();
				$this->addErrorList($arrayToJs,false, $e->getMessage()); 
		}			
			
		return $arrayToJs;	
	}
	    
    
	function validateForm($arr,$pkey = ''){
		   
		$arrayToJs = parent::validateForm($arr,$pkey); 
        
		$name = $arr['name'];  
		$categorykey = $arr['hidCategoryKey'];  
	 	 
	  	$rs = $this->isValueExisted($pkey,'name',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['download'][1]);
		}else if(count($rs) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['download'][2]);
		} 
		 
        /* if(empty($categorykey)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['category'][1]);
		} */
        
		return $arrayToJs;
	 }	 
    
    
    function normalizeParameter($arrParam, $trim=false){
  
        $arrParam['chkExternal'] = (!empty($arrParam['chkExternal'])) ? 1 : 0;  
	    if(!$arrParam['chkExternal']) $arrParam['externalLink'] = '';
        
        $arrParam['fileName'] = (isset( $arrParam['token-item-image-uploader'])) ? $this->updateImage($arrParam['pkey'], $arrParam['token-item-image-uploader'], $arrParam['item-image-uploader']) : '' ;
        
        $arrParam = parent::normalizeParameter($arrParam,true);  
        return $arrParam;
    }
     

    function afterUpdateData($arrParam, $action){  
		if(isset($arrParam['item-file-uploader'])) 
				$this->updateFile($arrParam['pkey'], $arrParam['token-item-file-uploader'], $arrParam['item-file-uploader']);  

		/*if(isset($arrParam['item-image-uploader']))
            $this->updateImage($arrParam['pkey'], $arrParam['token-item-image-uploader'], $arrParam['item-image-uploader']);  */

		if(isset($arrParam['chkExternal']) && $arrParam['chkExternal']){
			$this->deleteItemFile($arrParam['pkey']);
		}
	}
	
	function getItemFile($pkey){
		$sql = 'select * from '.$this->tableFile.' where refkey = '.$this->oDbCon->paramString($pkey).' order by pkey asc';	
		return $this->oDbCon->doQuery($sql);
    } 	
	
	function deleteItemFile($pkey){
			$sql = 'delete from '.$this->tableFile.' where refkey = '. $this->oDbCon->paramString($pkey);
			$this->oDbCon->execute($sql);
			$this->deleteAll($this->defaultDocUploadPath.$this->uploadFileFolder.$pkey);
    } 
	
	function updateFile($pkey,$token,$arrFile){		
        
        if(!empty($arrFile)) 
            $this->validateDiskUsage(); 
        
		$sourcePath = $this->uploadTempDoc.$this->uploadFileFolder.$token;
		$destinationPath = $this->defaultDocUploadPath.$this->uploadFileFolder;
		
			
		if(!is_dir($destinationPath)) 
			mkdir ($destinationPath,  0755, true);
			
		$destinationPath .= $pkey;  
		 
		
		//delete previous files	    
		$this->deleteAll($destinationPath);  
		$sql = 'delete from '.$this->tableFile.' where refkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql);
		 
		
		 
		if(!is_dir($sourcePath)) 
			return;
	
		if (!empty($arrFile))	{
			$arrFile = explode(",",$arrFile);
			for ($i=0;$i<count($arrFile);$i++){   
				$this->uploadImage($sourcePath, $destinationPath,$arrFile[$i]);
				
				$imagekey = $this->getNextKey($this->tableFile);  
				
				$sql = 'insert into '.$this->tableFile.' (pkey,refkey,file) values ('.$this->oDbCon->paramString($imagekey).','.$this->oDbCon->paramString($pkey).','.$this->oDbCon->paramString($arrFile[$i]).')';	
				$this->oDbCon->execute($sql);	 
				 
			}		
		} 
					
	} 		
	
	
     function updateImage($pkey,$token,$arrImage){		
		 
		$sourcePath = $this->uploadTempDoc.$this->uploadImageFolder.$token;
		$destinationPath = $this->defaultDocUploadPath.$this->uploadImageFolder;
		
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
    
    
    function getDetailForAPI($arrKey,$arrIndex = array()){
        $rsDetailsCol = array();
         
        if(in_array('file_detail',$arrIndex)){  
            $rsDetails = $this->getFileDetail($arrKey); 
            $rsDetails = $this->reindexDetailCollections($rsDetails,'refkey'); 
            $rsDetailsCol['file_detail'] = $rsDetails;
        }
         
        return $rsDetailsCol;
    }
    
    function getFileDetail($pkey){
       
      
        $sql = 'select
            '.$this->tableFile.'.pkey,     
            '.$this->tableFile.'.refkey,     
            '.$this->tableFile.'.file
          from 
           '.$this->tableFile.'
          where  
            '. $this->tableFile.'.refkey in ('.$this->oDbCon->paramString($pkey,',') .')';
 
        return $this->oDbCon->doQuery($sql);

    }
    
    
}
?>
