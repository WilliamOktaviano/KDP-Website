<?php
class ItemFilter extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'filter_header';  
		$this->tableNameDetail = 'filter_detail';
		$this->tableItem = 'item';    
		$this->securityObject = 'ItemFilter'; 
		$this->tableStatus = 'master_status';
		$this->tableCategory = 'filter_category'; 
	 
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
             
			$sql = '
					INSERT INTO		
					 '.$this->tableName .' (
						pkey, 
						code,  
						name,
						categorykey,
						statuskey,
						createdby,
						createdon
					)
					VALUES	( 
						'.$pkey.', 
						'.$this->oDbCon->paramString($arrParam['code']).',  
						'.$this->oDbCon->paramString($arrParam['name']).',  
						'.$this->oDbCon->paramString($arrParam['hidCategoryKey']).',  
						'.$this->oDbCon->paramString($arrParam['selStatus']).',
						'.$this->oDbCon->paramString($arrParam['createdBy']).', 
						now()   
					)
			';
			 
		    $this->oDbCon->execute($sql);  
			  
			$this->updateDetail($pkey, $arrParam);
			
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
					
	
			$sql = '
						UPDATE	
						 '.$this->tableName .'
						SET	   
						 code ='.$this->oDbCon->paramString($arrParam['code']).', 
						 name ='.$this->oDbCon->paramString($arrParam['name']).',  
						 categorykey ='.$this->oDbCon->paramString($arrParam['hidCategoryKey']).', 
						 statuskey ='.$this->oDbCon->paramString($arrParam['selStatus']).',   
						 modifiedby = '.$this->oDbCon->paramString($arrParam['modifiedBy']).',
						 modifiedon = now() 
						WHERE	
						 pkey = '.$this->oDbCon->paramString($arrParam['hidId']).'
						
				';    
				 
                $this->oDbCon->execute($sql);                
				
				$this->updateDetail($arrParam['hidId'], $arrParam);
				
                $this->setTransactionLog(UPDATE_DATA,$arrParam['hidId']);
            
				$this->oDbCon->endTrans();  
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   
			
		}catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false,$e->getMessage());  
		}			
			
		return $arrayToJs; 
	}  
	
	function updateDetail($pkey,$arrParam){
	return;	
	 	$sql = 'delete from '.$this->tableNameDetail.' where refkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql);
		
		$arrItemkey = $arrParam['hidItemKey'];
		
		$temp = array();
			
     	for ($i=0;$i<count($arrItemkey);$i++){	
			
			if (in_array($arrItemkey[$i],$temp))
				continue;
				 
			array_push($temp,$arrItemkey[$i]);
			
			$sql = 'insert into '.$this->tableNameDetail.' (
						refkey,
						itemkey
					 ) values (
						'.$this->oDbCon->paramString($pkey).',
						'.$this->oDbCon->paramString($arrItemkey[$i]).'
					)';	 
			
			$this->oDbCon->execute($sql);
                                        
		}
		 
					
	}
	
	function validateForm($arr,$pkey = ''){
		$item = new Item();   
		     
		$arrayToJs = parent::validateForm($arr,$pkey); 
        
		$hasDetail = false;
		   
	 	$name = $arr['name']; 
		 
		$rs = $this->isValueExisted($pkey,'name',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][1]);
		}else if(count($rs) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][2]);
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
				
				$sql = 'delete from '.$this->tableNameDetail.' where refkey = '. $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);			
        
                $this->setTransactionLog(DELETE_DATA,$id);
            
				$this->oDbCon->endTrans();
										 
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);    
			 
				
			}catch(Exception $e){
				$this->oDbCon->rollback();
				$this->addErrorList($arrayToJs,false, $e->getMessage()); 
		}			
			
		return $arrayToJs;	
	}
	
	
	function getItemInFilter($filter){
		
			$filter = implode(',',$filter);
			
			$sql = 'select 
						distinct(itemkey) as itemkey
					from	
						' . $this->tableName .', ' . $this->tableNameDetail .'
					where
						'.$this->tableName.'.pkey = ' . $this->tableNameDetail .'.refkey and 
						'. $this->tableName .'.statuskey = 1 and
						'. $this->tableNameDetail .'.refkey in ('.$filter.')  ';
			 
			$rs = $this->oDbCon->doQuery($sql);		
			
			return $rs;
			
	}
	
	function getRelatedFilterByItemCategory($categorykey,$itemcategorykey=''){ 
		$criteria = '';
		if (!empty($itemcategorykey)){
                $category = new Category(); 
                $rsCat = $category->getChildren($itemcategorykey);
                if (!empty($rsCat)) 
                    $itemcategorykey = implode(',',$rsCat); 
                
            	$criteria  = 'and  '. $this->tableItem .'.categorykey in ('.$itemcategorykey.')'; 
        }
		
		$sql = 'select 
					distinct(' . $this->tableName .'.pkey),
					' . $this->tableName .'.name 
				from 
					' . $this->tableName .',' . $this->tableNameDetail .',' . $this->tableItem .'
				where
					' . $this->tableName .'.statuskey = 1 and
					' . $this->tableName .'.categorykey = ' . $this->oDbCon->paramString($categorykey).' and
					' . $this->tableName .'.pkey = ' . $this->tableNameDetail .'.refkey and
					' . $this->tableItem .'.pkey = ' . $this->tableNameDetail .'.itemkey 
					'.$criteria.'
				order by name asc
				';
		  
		$rs = $this->oDbCon->doQuery($sql);		
			
		return $rs;
	}
	 
	
	function getRelatedFilterByItemKey($categorykey,$arrItem){
		if (empty($arrItem))
			return ;
			
		$arrItem = implode(',',$arrItem);
		$sql = 'select 
					distinct(' . $this->tableName .'.pkey),
					' . $this->tableName .'.name 
				from 
					' . $this->tableName .',' . $this->tableNameDetail .'
				where
					statuskey = 1 and
					 categorykey = ' . $this->oDbCon->paramString($categorykey).' and
					' . $this->tableName .'.pkey = ' . $this->tableNameDetail .'.refkey
					and itemkey in ('.$arrItem.')
				order by name asc
				';
		  
		$rs = $this->oDbCon->doQuery($sql);		
			
		return $rs;
	}  
	 
	 
}
		
?>