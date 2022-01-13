<?php

class Page extends BaseClass{
  
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'page';   
		$this->securityObject = 'Page'; 
		$this->tableStatus = 'master_status'; 
	    $this->tableLangValue = 'page_lang';
		$this->uploadFolder = 'page/';
 
        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataLang, 'tableName' => $this->tableLangValue));
       
        $this->arrData = array();  
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['pagename'] = array('pagename');
        $this->arrData['title'] = array('title'); 
        $this->arrData['parentkey'] = array('selCategory');
        $this->arrData['detail'] = array('txtDetail','raw'); 
        $this->arrData['shortdesc'] = array('txtShortDescription');  
        $this->arrData['file'] = array('fileName');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['orderlist'] = array('orderList');
        
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'pagename','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'title','title' => 'title','dbfield' => 'title','default'=>true, 'width' => 300));
        array_push($this->arrDataListAvailableColumn, array('code' => 'parent','title' => 'parent','dbfield' => 'parentpagename',  'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        
        $this->newLoad = true;
        
        require_once DOC_ROOT. 'phpthumb/phpThumb.config.php';  

        $this->overwriteConfig();
	   
   }
   
   function getQuery(){
	   
	   return '
			select
					'.$this->tableName. '.*,
                    parenttable.pagename as parentpagename,
					'.$this->tableStatus.'.status as statusname 
				from 
                    '.$this->tableStatus.',
					'.$this->tableName .'
                        left join '.$this->tableName .' parenttable on '.$this->tableName .'.parentkey = parenttable.pkey
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
 		' .$this->criteria ; 
		 
    }
	 
	
	 function validateForm($arr,$pkey = ''){
		     
		$arrayToJs = parent::validateForm($arr,$pkey); 
		$pagename = $arr['pagename'];  
	  
	 	$rs = $this->isValueExisted($pkey,'pagename',$pagename);	 
		if(empty($pagename)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['page'][1]);
		}else if(count($rs) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['page'][2]);
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
    
    function getCategoryTree($parentkey = 0, $pathSeparator = ' / ', $leafOnly = true, $level = 0){
        $level++;
        
		$sql = 'select * from '.$this->tableName.' where '.$this->tableName . '.parentkey = ' . $this->oDbCon->paramString($parentkey) .' and statuskey = 1 order by orderlist asc';
		$rs = $this->oDbCon->doQuery($sql);  
		 
        $arrPath = array();
        
		for ($i=0;$i<count($rs);$i++){  
			
            $arrResult = array();
            //$arrTemp = $this->getPath($rs[$i]['pkey'], $pathSeparator);
            //$arrResult['path'] = $arrTemp[0]['path'];
            $arrResult['pkey'] = $rs[$i]['pkey']; 
            $arrResult['parentkey'] = $rs[$i]['parentkey'];  
            $arrResult['pagename'] = $rs[$i]['pagename'];  
            $arrResult['title'] = $rs[$i]['title'];   
            $arrResult['isleaf'] = $rs[$i]['isleaf'];   
            $arrResult['level'] = $level;  
            array_push($arrPath,$arrResult);

            $arr = $this->getCategoryTree($rs[$i]['pkey'], $pathSeparator,$leafOnly,$level);
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
    
    function afterUpdateData($arrParam, $action){ 
        $this->updateLeaf();
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
 
  
	function compileChildArray($pagekey, &$arrResult = array()){
		  
	 
        $orderby = 'order by orderlist asc';
              
		$rs = $this->searchDataRow(array( $this->tableName . '.pkey', $this->tableName . '.parentkey',$this->tableName . '.pagename', $this->tableName . '.title', $this->tableName . '.isleaf'),
                                        ' and '.$this->tableName . '.statuskey = 1 and '.$this->tableName . '.parentkey =  ' .$this->oDbCon->paramString($pagekey),
                                        $orderby);
        
        if(empty($rs)) return $arrResult;
         
        /*if(empty($arrResult)){ 
            $this->setLog("on",true);
            //$arrResult[0]['parentnode'] = $pagekey; 
            //$arrResult[0]['node'] = 0; 
            $arrResult[0]['childnode'] = $rs; 
        }*/
      
        //$arrResult[$pagekey]['parentnode'] = $pagekey; 
        //$arrResult[$rs[$i]['pkey']]['node'] = $rs[$i]['pkey']; 
        $arrResult[$pagekey]['childnode'] = $rs; 


		for($i=0;$i<count($rs);$i++)   
            $this->compileChildArray($rs[$i]['pkey'],$arrResult); 

		return $arrResult;
	} 
    
    function normalizeParameter($arrParam, $trim = false){ 
                 
        $arrParam['fileName'] = $this->updateImages($arrParam['pkey'], $arrParam['token-item-image-uploader'], $arrParam['item-image-uploader']);    
        
        $arrParam = $this->updateOthersLangValue($arrParam, $this->arrData); 
        $arrParam = parent::normalizeParameter($arrParam,true); 
          
         return $arrParam; 
    }
	    
}

?>
