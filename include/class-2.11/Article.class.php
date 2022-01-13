<?php

class Article extends BaseClass{
  
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'article'; 
		$this->tableNameDetail = 'article_category_detail'; 
		$this->tableCategory = 'article_category';   
		$this->securityObject = 'Article'; 
		$this->tableStatus = 'master_status';
		$this->uploadFolder = 'article/'; 
        

        $this->arrDataDetail = array();  
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['categorykey'] = array('hidCategoryKey'); 
       
        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableNameDetail));
       
        $this->arrData = array();  
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['title'] = array('title');
       // $this->arrData['categorykey'] = array('hidCategoryKey');
        $this->arrData['shortdesc'] = array('txtShortDescription'); 
        $this->arrData['publishdate'] = array('publishDate','date');
        $this->arrData['detail'] = array('txtDetail','raw'); 
        $this->arrData['featured'] = array('isFeatured');  
        $this->arrData['file'] = array('fileName');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['tag'] = array('tag');
          
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'title','dbfield' => 'title','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'category','title' => 'title','dbfield' => 'categoryname','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'publishDate','title' => 'publishDate','dbfield' => 'publishdate','default'=>true, 'width' => 100, 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        
        $this->newLoad = true;
       
        $this->overwriteConfig();
       
   }
   
   function getQuery(){
	   
	   return '
			select
				'.$this->tableName. '.*,
				'.$this->tableStatus.'.status as statusname  
			from 
				'.$this->tableName . ' , '.$this->tableStatus.' 
			where  		 
				'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
 		' .$this->criteria ; 
		 
   }
   
   
    
	
	 function validateForm($arr,$pkey = ''){
		     
		$arrayToJs = parent::validateForm($arr,$pkey); 
         
		$name = $arr['title'];
        //$arrCategorykey = $arr['hidCategoryKey'];   
	 
        $arrImage = explode(",",$arr['item-image-uploader']); 
        for($i=0;$i<count($arrImage);$i++){
            if (empty($arrImage[$i]))
                continue;
            
            $path = $this->uploadTempDoc.$this->uploadFolder.$arr['token-item-image-uploader']; 
            if (filesize($path.'/'.$arrImage[$i]) > (pow(1024,2) * PLAN_TYPE['maximagesize']))
                $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][4] .' ('.$this->lang['max'].' '. $this->formatNumber(PLAN_TYPE['maximagesize']). ' MB)' );
        } 
		
	 	$rs = $this->isValueExisted($pkey,'title',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['article'][1]);
		}else if(count($rs) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['article'][2]);
		}
		
//		for($i=0;$i<count($arrCategorykey);$i++) { 
//			if (empty($arrCategorykey[$i]) ){ 
//				$this->addErrorList($arrayToJs,false, $this->errorMsg['category'][1]); 	
//			} 
//		}
           
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
    
    function getDetailWithRelatedInformation($pkey,$criteria='',$orderby =''){


        $sql = 'select
                '.$this->tableNameDetail .'.*, 
                '.$this->tableCategory.'.name as categoryname, 
                '.$this->tableCategory.'.code as categorycode 
           
              from
                '.$this->tableNameDetail .',
                '.$this->tableCategory.'
              where
                '.$this->tableNameDetail .'.categorykey = '.$this->tableCategory.'.pkey and
		'.$this->tableNameDetail .'.refkey in ('.$this->oDbCon->paramString($pkey,',') . ') ';

        $sql .= $criteria;

        $sql .= ' ' .$orderby;
        
        return $this->oDbCon->doQuery($sql);

    } 
        
    function normalizeParameter($arrParam, $trim = false){ 
                 
        $arrParam['fileName'] = $this->updateImages($arrParam['pkey'], $arrParam['token-item-image-uploader'], $arrParam['item-image-uploader']);    
        
        $arrParam = $this->updateOthersLangValue($arrParam, $this->arrData); 
        $arrParam = parent::normalizeParameter($arrParam,true); 
          
         return $arrParam; 
    }
    
    function searchDataWithCategory($fieldname='',$searchkey='',$mustmatch=true,$searchCriteria='',$orderCriteria='', $limit=''){

		$criteria = '';
		 
		if(!empty($fieldname)){
			
			$criteria .= ' and ' ;
			
			if($mustmatch)
				$criteria .=  $fieldname .' = '. $this->oDbCon->paramString($searchkey);
			else
				$criteria .=  $fieldname .' like '. $this->oDbCon->paramString('%'.$searchkey.'%');
		}
				
		if($searchCriteria <> '')
			$criteria .= ' ' .$searchCriteria;
	
		$this->setCriteria($criteria); 
		$sql = ' select '.$this->tableName.'.*
                 from '.$this->tableName.' 
                    left join '.$this->tableNameDetail.' on '.$this->tableNameDetail.'.refkey = '.$this->tableName.' .pkey 
                where 1=1 '.$criteria.' 
              group by '.$this->tableName.'.pkey ';
		
		if($orderCriteria <> '') 
			$sql .= ' ' .$orderCriteria;  
			
		if($limit <> '')
			$sql .= ' ' .$limit;
        
		return $this->oDbCon->doQuery($sql);	
	} 
    
    function getTotalRowsWithCategory($criteria){
		// fungsi ini sudah hampir gk kepake
          
        $sql = ' select '.$this->tableName.'.pkey
                 from '.$this->tableName.' 
                    left join '.$this->tableNameDetail.' on '.$this->tableNameDetail.'.refkey = '.$this->tableName.' .pkey 
                where 1=1 '.$criteria.' 
              group by '.$this->tableName.'.pkey ';
         
        
		$rs  = $this->oDbCon->doQuery($sql);
		return count($rs);
    
	} 
	
    function getDetailForAPI($arrKey, $arrIndex = array()){
        if(in_array('category_detail', $arrIndex)){
            $rsDetailsCol = array();
            $rsDetails = $this->getDetailWithRelatedInformation($arrKey); 
            $rsDetails = $this->reindexDetailCollections($rsDetails,'refkey'); 
            $rsDetailsCol['category_detail'] = $rsDetails;
        }
        
        return $rsDetailsCol;
    }
	    
}

?>
