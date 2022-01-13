<?php 
class Gallery extends BaseClass{
 
   function __construct($galleryType = 1){
		
		parent::__construct();
       
		$this->tableName = 'gallery_header';  
		$this->tableCategory = 'gallery_category';  
        $this->tableImage = 'gallery_image';
		$this->tableStatus = 'master_status';  
		$this->uploadFolder = 'gallery/';
        $this->galleryType = $galleryType; 
        $this->securityObject = ($galleryType == 1) ? 'Gallery' : 'GalleryHumanResource'; 
       
        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataLang, 'tableName' => $this->tableLangValue));

        $this->arrData = array();  
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails)); 
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['categorykey'] = array('selCategory'); 
        $this->arrData['customerkey'] = array('hidCustomerKey'); 
        $this->arrData['featured'] = array('isFeatured'); 
        $this->arrData['trdesc'] = array('trDesc'); 
        $this->arrData['statuskey'] = array('selStatus');  
        $this->arrData['gallerytype'] = array('galleryType');  

        
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'category','title' => 'category','dbfield' => 'categoryname','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'shortDescription','title' => 'shortDescription','dbfield' => 'shortdescription', 'width' => 250 ));  
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70)); 

        $this->newLoad = true;
       
        $this->includeClassDependencies(array(
              'Category.class.php',
              'GalleryCategory.class.php',
              'Gallery.class.php',
        ));
        require_once DOC_ROOT. 'phpthumb/phpThumb.config.php';  
       
        $this->overwriteConfig(); 
   }
    function getQuery(){ 
	   
	   $sql = '
			SELECT '.$this->tableName.'.* , 
			   '.$this->tableCategory.'.name as categoryname,
			   '.$this->tableStatus.'.status as statusname,
			   '.$this->tableCustomer.'.name as customername 
			FROM '.$this->tableStatus.',
                 '.$this->tableCategory.',
                 '.$this->tableName.' 
					left join '.$this->tableCustomer.' on '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey 
			WHERE 
                '.$this->tableName.'.gallerytype = '.$this->galleryType.' and
                '.$this->tableName.'.categorykey = '.$this->tableCategory.'.pkey and
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey 
 	' .$this->criteria ; 
		 
      // $this->setLog($sql);
       return $sql;
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
		$sql = 'delete from '.$this->tableImage.' where refkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql);
		 
		if (!empty($arrImage))	{
			$arrImage = explode(",",$arrImage);
			for ($i=0;$i<count($arrImage);$i++){   
				$this->uploadImage($sourcePath, $destinationPath,$arrImage[$i]); 
				
				$sql = 'insert into '.$this->tableImage.' (refkey,file) values ('.$this->oDbCon->paramString($pkey).','.$this->oDbCon->paramString($arrImage[$i]).')';	
				$this->oDbCon->execute($sql);	 
				
			}		
		}
			
	} 
    
    function afterUpdateData($arrParam, $action){  
        $pkey = $arrParam['pkey'];
        
        if(isset($arrParam['token-item-image-uploader']))
            $this->updateImage($pkey, $arrParam['token-item-image-uploader'], $arrParam['item-image-uploader']);

    }

   function addData($arrParam){ 
        $arrParam['galleryType'] = $this->galleryType; 
		return parent::addData($arrParam); 	
	}
    
	
        
    function editData($arrParam){ 
        $arrParam['galleryType'] = $this->galleryType; 
        return parent::editData($arrParam);
	}
 
	
	function validateForm($arr,$pkey = ''){
		       
		$arrayToJs = parent::validateForm($arr,$pkey);  
		 
		$name = $arr['name'];   
		$categorykey = $arr['selCategory'];
		$customerKey = $arr['hidCustomerKey'];  
	  	 
        $arrImage = explode(",",$arr['item-image-uploader']);
        if(count($arrImage) > PLAN_TYPE['maxproductimage'])
            $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][2]); 
           
        $arrImage = explode(",",$arr['item-image-uploader']);
        for($i=0;$i<count($arrImage);$i++){
            $path = $this->uploadTempDoc.$this->uploadFolder.$arr['token-item-image-uploader']; 
            if (filesize($path.'/'.$arrImage[$i]) > (pow(1024,2) * PLAN_TYPE['maximagesize']))
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][4] .' ('.$this->lang['max'].' '. $this->formatNumber(PLAN_TYPE['maximagesize']/1024). ' KB)' );
        }
           
	 	$rs = $this->isValueExisted($pkey,'name',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['title'][1]);
		}else if(count($rs) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['title'][2]);
		}
		 
        
		if (empty($categorykey)){ 
				$this->addErrorList($arrayToJs,false,$this->errorMsg['category'][1]); 
		}
  
		   
		return $arrayToJs;
	 } 
	  

	 function getGalleryImage($pkey ){  
		$sql = 'select * from '.$this->tableImage.' where refkey = '.$this->oDbCon->paramString($pkey).' order by  pkey asc';	
	 	return $this->oDbCon->doQuery($sql);
    } 

    

    
	function delete($id, $forceDelete = false,$reason = ''){ 
		$arrayToJs =  array();
		 
		try{			 
				 
				$arrayToJs = $this->validateDelete($id);
				if (!empty($arrayToJs)) 
					return $arrayToJs;
		 		
				if (!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]);
			 
				
				$sql = 'delete from  '.$this->tableName.' where pkey = ' . $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);
		
				 
				$sql = 'delete from '.$this->tableImage.' where refkey = '. $this->oDbCon->paramString($id);
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
	 
	  function validateDelete($id){
		    
		$arrayToJs = array();
		$rs = $this->getDataRowById($id);
			$rs = $this->getDataRowById($id); 
		if ($rs[0]['systemVariable'] == 1)  {
			$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['name'].'</strong>. ' . $this->errorMsg[211]);
			return $arrayToJs;
		} 

      
		return $arrayToJs;
	 } 

    
     
    function normalizeParameter($arrParam, $trim=false){ 
         
        
        $arrParam = $this->updateOthersLangValue($arrParam, $this->arrData); 
    
        $arrParam = parent::normalizeParameter($arrParam,true);
 
        return $arrParam; 
    }
   
  

    
  } 

?>
