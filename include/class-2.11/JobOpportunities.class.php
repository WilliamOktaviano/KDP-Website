<?php

class JobOpportunities extends BaseClass{
   
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'job_opportunities'; 
	    $this->tableLangValue = 'job_opportunities_lang';
		$this->tableCategory = 'career_category'; 
		$this->tableJobField = 'career_field'; 
		$this->tableCity = 'city'; 
		$this->tableCityCategory = 'city_category'; 
		$this->securityObject = 'JobOpportunities'; 
        $this->tableFile = 'job_opportunities_file'; 
		$this->tableStatus = 'master_status';
        $this->uploadFolder = 'job-opportunities-image/';
        $this->uploadFileFolder = 'job-opportunities-file/';
       

        $this->arrLockedTable = array();
        $defaultFieldName = 'jobkey';
        array_push($this->arrLockedTable, array('table'=>'recruitment','field'=>$defaultFieldName)); 
        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataLang, 'tableName' => $this->tableLangValue));
       
        $this->arrData = array();  
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['title'] = array('title');
        $this->arrData['categorykey'] = array('hidCategoryKey');
        $this->arrData['jobfieldkey'] = array('hidJobFieldKey');
        $this->arrData['citykey'] = array('hidCityKey');
        $this->arrData['shortdesc'] = array('trDesc');  
        $this->arrData['jobdesc'] = array('jobDesc','raw');  
        $this->arrData['requirement'] = array('reqDesc','raw');  
        $this->arrData['file'] = array('fileName');
        $this->arrData['url'] = array('url');

        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'title','title' => 'title','dbfield' => 'title','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'category','title' => 'category','dbfield' => 'categoryname','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'jobfield','title' => 'careerField','dbfield' => 'jobfieldname','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'city','title' => 'city','dbfield' => 'cityandcategoryname', 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
       
        $this->newLoad = true;
       
        $this->includeClassDependencies(array(
            'Category.class.php','CareerCategory.class.php','CareerField.class.php','CityCategory.class.php','City.class.php'
        )); 
           
           
        $this->overwriteConfig();
   }
   
   
	 function getQuery(){
	   
	   return '
				select
					'.$this->tableName. '.*,
					'.$this->tableCity.'.name as cityname,
					'.$this->tableCategory.'.name as categoryname,
					'.$this->tableJobField.'.name as jobfieldname ,   
                    concat ('.$this->tableCity. '.name, ", ", '.$this->tableCityCategory.'.name) as citycatname,
					'.$this->tableStatus.'.status as statusname 
				from 
					'.$this->tableName . '
                        left join '.$this->tableJobField.' on '.$this->tableName.'.jobfieldkey = '.$this->tableJobField.'.pkey
                        left join '.$this->tableCity.' on '.$this->tableName.'.citykey = '.$this->tableCity.'.pkey
				        left join '.$this->tableCityCategory.' on '.$this->tableCity . '.categorykey = '.$this->tableCityCategory.'.pkey,
                    '.$this->tableCategory.', 
                    '.$this->tableStatus.' 
				where  		 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey and
					'.$this->tableName . '.categorykey = '.$this->tableCategory.'.pkey 
 		' .$this->criteria ; 
		 
    }
	 
	
	 function validateForm($arr,$pkey = ''){
		     
		$arrayToJs = parent::validateForm($arr,$pkey); 
         
		$name = $arr['title'];  
         $categorykey = $arr['hidCategoryKey'];
         $jobfieldkey = $arr['hidJobFieldKey'];
         //$locationkey = $arr['hidLocationKey'];

		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['jobOpportunities'][1]);
		}
         
        if ( empty($categorykey)){ 
            $this->addErrorList($arrayToJs,false,$this->errorMsg['category'][1]); 
		}
         
        if (empty($jobfieldkey)){ 
            $this->addErrorList($arrayToJs,false,$this->errorMsg['careerField'][1]); 
		}
         
        /*if (empty($locationkey)){ 
            $this->addErrorList($arrayToJs,false,$this->errorMsg['location'][1]); 
		}*/
        
        
         return $arrayToJs;
         
	 }
    
    function delete($id,$forceDelete = false,$reason = ''){
		 
		$arrayToJs =  array();
		// tdk bisa didelete utk transaksi, tp ubah ke cancel
		if(isset( $this->tableNameDetail) &&!empty($this->tableNameDetail)){  
             $arrayToJs = $this->changeStatus($id, 7,$reason,false,$forceDelete);  
             return $arrayToJs; 
		} 
		
		try{ 
		
	 		$arrayToJs = $this->validateDelete($id);
			if (!empty($arrayToJs)) 
				return $arrayToJs;
					 
			 if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
				 
				$sql = 'delete from  '.$this->tableName.' where pkey = ' . $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);
                $this->deleteAll($this->defaultDocUploadPath.$this->uploadFolder.$id);
                $this->deleteAll($this->defaultDocUploadPath.$this->uploadFileFolder.$id);

                $this->setTransactionLog(DELETE_DATA,$id);
            
				$this->oDbCon->endTrans();
					 
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']); 
				 
		} catch(Exception $e){
			$this->oDbCon->rollback(); 
			$this->addErrorList($arrayToJs,false, $e->getMessage()); 
			
		}		 
			 	
 		return $arrayToJs; 
	}
    
    function getItemFile($pkey){
		$sql = 'select * from '.$this->tableFile.' where refkey = '.$this->oDbCon->paramString($pkey).' order by pkey asc';	
		return $this->oDbCon->doQuery($sql);
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
  
        
    function normalizeParameter($arrParam, $trim = false){ 
                 
        $arrParam['fileName'] = $this->updateImages($arrParam['pkey'], $arrParam['token-item-image-uploader'], $arrParam['item-image-uploader']);    
               
        // harusnya boleh diupdate kalo sudah di save
            if(isset($arrParam['token-item-file-uploader']))
               $this->updateFile($arrParam['pkey'], $arrParam['token-item-file-uploader'], $arrParam['item-file-uploader']);    
            
        $arrParam = $this->updateOthersLangValue($arrParam, $this->arrData); 
        $arrParam = parent::normalizeParameter($arrParam,true); 
          
         return $arrParam; 
    }
		
    
}

?>
