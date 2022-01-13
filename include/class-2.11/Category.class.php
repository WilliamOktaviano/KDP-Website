<?php 

class Category extends BaseClass{
  
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'item_category';
        $this->tableNameDetail = 'item_category_marketplace_detail';
		$this->tableStatus = 'master_status';  
		$this->uploadFolder = 'category/'; 
		$this->securityObject = 'ItemCategory';
       
       
        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code'); 
        $this->arrData['name'] = array('name');
        $this->arrData['orderlist'] = array('orderList', 'number');
        $this->arrData['parentkey'] = array('selCategory');
        $this->arrData['isleaf'] = array('isLeaf'); 
        $this->arrData['file'] = array('fileName');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['shortdescription'] = array('trShortDesc'); 
        $this->arrData['description'] = array('txtDescription','raw'); 
   }
   
   function getQuery(){
	   
	   $sql= '
			select
					'.$this->tableName. '.*,
					'.$this->tableStatus.'.status as statusname,
					parentcat.name as parentname
				from 
					'.$this->tableName . ' left join '.$this->tableName . ' parentcat on 	parentcat.pkey = '.$this->tableName . '.parentkey ,'.$this->tableStatus.' 
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
 		' .$this->criteria ; 
		 
       return $sql; 
    }
   
	   
	function addData($arrParam){  
		$arrParam['isLeaf'] = 1; 
        return parent::addData($arrParam);   
	}

	function editData($arrParam){  
		$arrParam['isLeaf'] = 1; 
        return parent::editData($arrParam);   
	} 
	
    function afterUpdateData($arrParam, $action){
        $this->updateOrder ($arrParam['orderList'],$arrParam['pkey']); 
        $this->updateLeaf();
    }

    function validateForm($arr,$pkey = ''){ 
				    
		$arrayToJs = parent::validateForm($arr,$pkey); 
          
		$name = $arr['name'];  
		$orderlist = (!empty($arr['orderList'])) ? $this->unformatNumber($arr['orderList']) : 0 ;  
        
        $pkeyCriteria = (!empty($pkey)) ? ' and '.$this->tableName.'.pkey <> ' . $this->oDbCon->paramString($pkey) : '';  
       
        $rsCategory = $this->searchData('','',true,' and '.$this->tableName.'.name = '.$this->oDbCon->paramString($name).' and '.$this->tableName.'.parentkey = '.$this->oDbCon->paramString($arr['selCategory']).' '.$pkeyCriteria);
    	if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['category'][1]);
		}else if(count($rsCategory) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['category'][2]);
		}
		
		if (!empty($orderlist)){
			if (!is_numeric($orderlist)){
				$this->addErrorList($arrayToJs,false,$this->errorMsg['orderList'][2]);
			}
		}
		   
		
		return $arrayToJs;
	 }

    function  afterStatusChanged($rs){
	 $this->updateLeaf(); 
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
		
		$arrayToJs =  array();
		 
		try{			 
		
				$arrayToJs = $this->validateDelete($id);
				if (!empty($arrayToJs)) 
					return $arrayToJs;
					
					
				if (!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]);
			
		 		 
				$sql = 'delete from  '.$this->tableName.' where pkey = ' . $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);  
				$this->deleteAll($this->defaultDocUploadPath.$this->uploadFolder.$id); 
				$this->updateLeaf(); 
                
                $this->deleteReference($id);
            
                /*
            	$autoCode = new AutoCode(); 
				$rsAutoCode = $autoCode->searchData('code', $rs[0]['code'],true);
				$autoCode->delete($rsAutoCode[0]['pkey']);
                */
             
                $this->setTransactionLog(DELETE_DATA,$id);	
				$this->oDbCon->endTrans();
										 
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);    
			 
				
			}catch(Exception $e){
				$this->oDbCon->rollback();
				$this->addErrorList($arrayToJs,false, $e->getMessage()); 
		}			
			
		return $arrayToJs;	
	}
	
	
	function compileChildArray($showWebstore = false){
		 
		$arrResult = array();
	 
        $orderby = 'order by orderlist asc, name asc';
             
        $webstoreCriteria = ($showWebstore) ? ' and '.$this->tableName . '.isshow = 1 '  : '';
        
		$rsChild = $this->searchData($this->tableName . '.statuskey',1,true,' and '.$this->tableName . '.parentkey = 0 ' .$webstoreCriteria,$orderby);
		$arrResult[0]['parentnode'] = -1; 
		$arrResult[0]['node'] = 0; 
		$arrResult[0]['childnode'] = $rsChild; 
		
		$rs = $this->searchData( $this->tableName . '.statuskey',1,true,$webstoreCriteria,$orderby);
		 
		for($i=0;$i<count($rs);$i++){ 
			$rsChild = $this->searchData($this->tableName . '.statuskey',1,true,' and '.$this->tableName . '.parentkey = ' . $this->oDbCon->paramString($rs[$i]['pkey']) .$webstoreCriteria,$orderby);
			$arrResult[$rs[$i]['pkey']]['parentnode'] = $rs[$i]['parentkey']; 
			$arrResult[$rs[$i]['pkey']]['node'] = $rs[$i]['pkey']; 
			$arrResult[$rs[$i]['pkey']]['childnode'] = $rsChild; 
		}
		
		return $arrResult;
	}
	
	function getLeafNodeWithPath($parentkey = 0, $pathSeparator = ' / ',$arrTemp =  array(), &$arrPath = array()){
		$sql = 'select * from '.$this->tableName.' where '.$this->tableName . '.parentkey = ' . $this->oDbCon->paramString($parentkey) .' and statuskey = 1 order by name asc';
		$rs = $this->oDbCon->doQuery($sql);  
		 
		for ($i=0;$i<count($rs);$i++){ 
		
			if ($rs[$i]['parentkey'] == 0){ 
				$arrTemp[$rs[$i]['pkey']] = array();
			}else{
				$arrTemp[$rs[$i]['pkey']] = $arrTemp[$rs[$i]['parentkey']];
			}
			
			array_push($arrTemp[$rs[$i]['pkey']],$rs[$i]['name']); 
			 
			if ($rs[$i]['isleaf'] == 1){   
				$arrResult = array();
				$arrResult['path'] = implode($pathSeparator,$arrTemp[$rs[$i]['pkey']]);
				$arrResult['pkey'] = $rs[$i]['pkey']; 
				array_push($arrPath,$arrResult);
			}else{  
				$this->getLeafNodeWithPath($rs[$i]['pkey'], $pathSeparator,$arrTemp, $arrPath);
			} 
				
		}
		
		return $arrPath;
		
	}
	
	function getCategoryTree($parentkey = 0, $pathSeparator = ' / ', $leafOnly = true){
		$sql = 'select * from '.$this->tableName.' where '.$this->tableName . '.parentkey = ' . $this->oDbCon->paramString($parentkey) .' and statuskey = 1 order by name asc';
		$rs = $this->oDbCon->doQuery($sql);  
		 
        $arrPath = array();
        
		for ($i=0;$i<count($rs);$i++){  
			
            $arrResult = array();
            $arrTemp = $this->getPath($rs[$i]['pkey'], $pathSeparator);
            $arrResult['path'] = $arrTemp[0]['path'];
            $arrResult['pkey'] = $rs[$i]['pkey']; 
            $arrResult['name'] = $rs[$i]['name'];  
            array_push($arrPath,$arrResult);

            $arr = $this->getCategoryTree($rs[$i]['pkey'], $pathSeparator,$leafOnly);
            $arrPath = array_merge($arrPath,$arr);
		}
		
		return $arrPath;
		
	}
	
	
	function getChildren($parentkey=0, &$arrChild=array()){
		// utk mencari semua node dibawah node $parentkey
		$rs = $this->searchData($this->tableName.'.statuskey',1,true,' and '.$this->tableName . '.parentkey = ' . $this->oDbCon->paramString($parentkey));
		 
		for ($i=0;$i<count($rs);$i++){ 
			 array_push($arrChild,$rs[$i]['pkey']);
			 if ($rs[$i]['isleaf'] == 0)
			 	$this->getChildren($rs[$i]['pkey'],$arrChild);
		}
		
		return $arrChild;
		 
	}
	
	
	function updateLeaf(){
        
		$sql = 'update ' . $this->tableName . ' set isleaf =  0';
		$this->oDbCon->execute($sql);
			
		$rs = array ();
		
		$sql = 'select * from ' . $this->tableName . ' where '.$this->tableName . '.parentkey =  0 and  ' . $this->tableName . '.statuskey = 1  order by orderlist asc';
		$rsTree = $this->oDbCon->doQuery($sql);	
		$this->updateLeafChild ($rsTree,$rs); 
		 
	}
	
	function updateLeafChild ($arrChild,&$rs) {
		 		
		for ($i=0;$i<count($arrChild);$i++) {   
			$sql = 'select  * from  ' . $this->tableName . ' where '.$this->tableName . '.parentkey = ' .$this->oDbCon->paramString($arrChild[$i]['pkey']) .  '  and  ' . $this->tableName . '.statuskey = 1 order by orderlist asc' ;  
			$rsTemp =  $this->oDbCon->doQuery($sql);
			if (empty($rsTemp)){
				$sql = 'update ' . $this->tableName . ' set isleaf =  1 where pkey = ' .$this->oDbCon->paramString($arrChild[$i]['pkey'])   ; 
				$this->oDbCon->execute($sql);	
			}else{		
				$this->updateLeafChild ($rsTemp,$rs);
			}
		}
	
	}
	
	function updateOrder($orderlist,$id){
		/*
		$sql = 'select pkey from ' . $this->tableName . ' where pkey <> '.$this->oDbCon->paramString($id).' and orderlist >= ' . $this->oDbCon->paramString($orderlist) .' order by orderlist asc' ;
		$rs =  $this->oDbCon->doQuery($sql);
		
		for ($i=0;$i<count($rs);$i++){
			$orderlist++;
			$sql = 'update ' . $this->tableName . ' set orderlist = '.$orderlist.' where pkey = ' . $rs[$i]['pkey'] ;			
			$this->oDbCon->execute($sql);	
		}
        */
	 }
    
    function getPath($categorykey, $pathSeparator = ' / '){
        $arrPath = array();
        $arrTempPath = array();
        
        $rsCat = $this->getDataRowById($categorykey);  
        array_unshift($arrTempPath, $rsCat[0]['name']);  
        
        $arrResult = array();
        $arrResult['name'] = $rsCat[0]['name'];
        $arrResult['pkey'] = $rsCat[0]['pkey'];
        $arrResult['path'] = implode($pathSeparator,$arrTempPath);
        array_unshift($arrPath, $arrResult);
        
        while($rsCat[0]['parentkey'] <> 0) { 
            $rsCat = $this->getDataRowById($rsCat[0]['parentkey']);  
            
            array_unshift($arrTempPath, $rsCat[0]['name']);  
            
            $arrResult = array();
            $arrResult['name'] = $rsCat[0]['name'];
            $arrResult['pkey'] = $rsCat[0]['pkey'];
            $arrResult['path'] = implode($pathSeparator,$arrTempPath); 
            array_unshift($arrPath, $arrResult); 
        }
 
        return $arrPath ;
    }
    
    function searchDataForAutoComplete($returnField, $searchOptions,$orderCriteria=''){ 
         
        $fieldname = $searchOptions['field'];
        $searchkey = $searchOptions['key'];
        $searchCriteria = (isset($searchOptions['criteria'])) ? $searchOptions['criteria'] : '';
            
		$sql = $this->generateDefaultQueryForAutoComplete($returnField);
	
		if(!empty($fieldname)){ 
			$sql .= ' and ' ; 
	        $sql .=  $fieldname .' like '. $this->oDbCon->paramString('%'.$searchkey.'%');
		}
				
		if($searchCriteria <> '')
			$sql .= ' ' .$searchCriteria;
	
		if($orderCriteria <> '') 
			$sql .= ' ' .$orderCriteria;
	     
         $rs = $this->oDbCon->doQuery($sql);	
         for($i=0;$i<count($rs);$i++) { 
            $rsPath = $this->getPath($rs[$i]['pkey']);
            $rs[$i]['name'] = $rs[$i]['value'] ; 
            $rs[$i]['value'] = htmlspecialchars_decode($rsPath[0]['path']); 
         }
        
         return $rs;
	} 

      function normalizeParameter($arrParam, $trim = false){  
           
        $arrParam['fileName'] = (isset( $arrParam['token-item-image-uploader'])) ? $this->updateImage($arrParam['pkey'], $arrParam['token-item-image-uploader'], $arrParam['item-image-uploader']) : '' ;
	   
        $arrParam = parent::normalizeParameter($arrParam,true); 
        return $arrParam; 
    }   
    
    function getMarketplaceCategory($id, $marketplacekey = '', $criteria = '') {
         
        $sql =  
            'select  
                '.$this->tableName.'.name as name ,
                '.$this->tableNameDetail.'.* 
                
            FROM 
                '.$this->tableName.',
                '.$this->tableNameDetail.' 
            WHERE
                '.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey and 
                '.$this->tableNameDetail.'.refkey = '.$this->oDbCon->paramString($id).'
                ';
        
        if (!empty($marketplacekey))
            $sql .= ' and ' .$this->tableNameDetail .'.marketplacekey = '.$this->oDbCon->paramString($marketplacekey);
            
        if (!empty($criteria))  
            $sql .=  ' ' .$criteria;
          
        return $this->oDbCon->doQuery($sql);
    }
      
}
?>