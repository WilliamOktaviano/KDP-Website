<?php

class Testimonial extends BaseClass{ 
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'testimonial';   
		$this->tableStatus = 'master_status';   
		$this->uploadFolder = 'testimonial/'; 
		$this->securityObject = 'Testimonial'; 
        
        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataLang, 'tableName' => $this->tableLangValue));
       
        $this->arrData = array();  
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails)); 
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['company'] = array('company'); 
        $this->arrData['position'] = array('position');  
        $this->arrData['review'] = array('review');  
        $this->arrData['file'] = array('fileName');
        $this->arrData['statuskey'] = array('selStatus');   
        
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true,'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'company','title' => 'company','dbfield' => 'company','default'=>true,'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'review','title' => 'review','dbfield' => 'review','default'=>true, 'width' => 300));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
         
        $this->newLoad = true;
       
		$this->overwriteConfig();
       
  
   }
   
    
	 function getQuery(){
	   
	   return '
				select
					'.$this->tableName.'.*,
					'.$this->tableStatus.'.status as statusname 
				from 
					'.$this->tableName.' ,
					'.$this->tableStatus.' 
				where  		
					'.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey  
		' .$this->criteria ; 
		 
    }
	
	  
	
	 function validateForm($arr,$pkey = ''){
		    
		$arrayToJs = array();
		
		$name = $arr['name'];  
		$review = $arr['review'];   
         
        /*$captchaResponse = $arr['g-recaptcha-response'];  
		$request = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$this->loadSetting('reCaptchaSecretKey')."&response=".$captchaResponse);
		$captchaResult = json_decode($request);
		
 		$errorCaptcha= $captchaResult->{'error-codes'}; 
		 
		if (empty($captchaResponse)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['captcha'][1]);
		} else if(!$captchaResult->{'success'}){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['captcha'][1]);
		} */
		
        $arrImage = explode(",",$arr['item-image-uploader']); 
        for($i=0;$i<count($arrImage);$i++){
            if (empty($arrImage[$i]))
                continue;
            
            $path = $this->uploadTempDoc.$this->uploadFolder.$arr['token-item-image-uploader']; 
            if (filesize($path.'/'.$arrImage[$i]) > (pow(1024,2) * PLAN_TYPE['maximagesize']))
                $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][4] .' ('.$this->lang['max'].' '. $this->formatNumber(PLAN_TYPE['maximagesize']). ' MB)' );
        } 
         
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][1]);
		} 
        
		if(empty($review)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['review'][1]);
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