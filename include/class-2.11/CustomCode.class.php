<?php 

class CustomCode extends BaseClass{ 

   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'custom_code';
		$this->tableNameCounter = 'custom_code_counter';
		$this->tableResetType = 'custom_code_reset_type';
		$this->tableStatus = 'master_status';   
		$this->tableCode = '_code';    
		$this->tableTablekey = 'tablekey';     
		$this->securityObject = 'customCode'; 
       
        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code'); 
        $this->arrData['name'] = array('name');
        $this->arrData['title'] = array('title');
        $this->arrData['reftabletype'] = array('selModule');
        $this->arrData['codeformat'] = array('codeFormat'); 
        $this->arrData['digit'] = array('digit','number');
        $this->arrData['resettypekey'] = array('selResetType'); 
        $this->arrData['statuskey'] = array('selStatus'); 
        $this->arrData['useautocode'] = array('chkIsAutoCode'); 
        $this->arrData['resetwarehouse'] = array('chkResetWarehouse'); 
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true,'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'title','title' => 'title','dbfield' => 'title','default'=>true,'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'module','title' => 'module','dbfield' => 'categoryname','default'=>true,'width' => 200, 'align' => 'left'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'format','title' => 'format','dbfield' => 'codeformat','default'=>true,'width' => 100,'align' => 'left'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'resetType','title' => 'resetType','dbfield' => 'resettype','default'=>true,'width' => 100, 'align' => 'left'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'autoCode','title' => 'auto','dbfield' => 'useautocodeicon','default'=>true, 'align'=>'center', 'width' => 70));
    
		$this->overwriteConfig();
        
   }
   
   function getQuery(){
	   
	   $sql = '
			select
					'.$this->tableName. '.*,
                    '.$this->tableResetType .'.name as resettype,
					'.$this->tableStatus.'.status as statusname,
                    '.$this->tableCode.'.label as categoryname ,
                    IF('.$this->tableName. '.useautocode=1, "<i class=\"fas fa-check text-green-avocado\"></i>", "") as useautocodeicon
				from 
					'.$this->tableName . ',
					'.$this->tableResetType . ',
                    '.$this->tableStatus.', 
                    '.$this->tableCode.' , 
                    '.$this->tableTablekey.' 
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey and
					'.$this->tableName . '.resettypekey = '.$this->tableResetType.'.pkey  and 
					'.$this->tableName . '.reftabletype = '.$this->tableTablekey.'.pkey  and
                    '.$this->tableTablekey.'.tablename = '.$this->tableCode.'.code
                     
 		' .$this->criteria ; 
		  
       return $sql;
    }
        
    
    function afterUpdateData($arrParam, $action){
        $this->updateCounter($arrParam);
    }
    
    function updateCounter($arrParam){
        $pkey = $arrParam['pkey'];
        $resetType = $arrParam['selResetType'];
        $resetWarehouse = $arrParam['chkResetWarehouse'];
        $refCode =isset($arrParam['refCode']) ? $arrParam['refCode'] : '';
        $trDate = isset($arrParam['trDate']) ? $arrParam['trDate'] : DEFAULT_EMPTY_DATE;
        $counter = 0 ;
        
        // kalo gk pernah reset, overwrite,  set tgl jd empty date_create
        if ($resetType == 1)
            $trDate = DEFAULT_EMPTY_DATE;
        
        // kalo gk reset per warehouse, set 0 
        $warehousekey = (isset($arrParam['selWarehouseKey']) && !empty($arrParam['selWarehouseKey'])) ? $arrParam['selWarehouseKey'] : 0;
        
        $sql = 'select pkey, counter
                from  ' .$this->tableNameCounter. '  
                where
                    refkey = '.$this->oDbCon->paramString($pkey).' and 
                    resettypekey = '.$this->oDbCon->paramString($resetType).' and 
                    refcode = '.$this->oDbCon->paramString($refCode).' and 
                    trdate = ' .$this->oDbCon->paramDate($trDate,' / ',$format='Y-m-d') ;
        
        if (!empty($warehousekey))
            $sql .= ' and warehousekey = '. $this->oDbCon->paramString($warehousekey);
            
        
        $rs = $this->oDbCon->doQuery($sql);
        $counter = (!empty($rs)) ? $rs[0]['counter']  : 0;
            
        // jika dr form custom code
        $overwriteCounter = (isset($arrParam['incrementNumber'])) ? true : false; 
        
        if (empty($rs)){  
            $incrementNumber = $overwriteCounter ? $arrParam['incrementNumber'] : (++$counter + 1) ;   
            $sql = 'insert into 
                        ' .$this->tableNameCounter. ' (refkey,resettypekey,trdate,warehousekey,refcode, counter)
                     values (
                        '.$this->oDbCon->paramString($pkey).', 
                        '.$this->oDbCon->paramString($resetType).',
                        '.$this->oDbCon->paramDate($trDate,' / ').',
                        '.$this->oDbCon->paramString($warehousekey).',
                        '.$this->oDbCon->paramString($refCode).',
                        '.$this->oDbCon->paramString($this->unformatNumber($incrementNumber)).'
                    ) ';
        }else { 
           $incrementNumber = $overwriteCounter ? $arrParam['incrementNumber'] : ($counter + 1) ;
           $sql = 'update 
                        ' .$this->tableNameCounter. ' 
                    set 
                        counter = '.$this->oDbCon->paramString($this->unformatNumber($incrementNumber)).'
                    where 
                        pkey = ' . $this->oDbCon->paramString($rs[0]['pkey']) ;
             
        }
         
        $this->oDbCon->execute($sql);
        
        return $counter;
    }

    function validateForm($arr,$pkey = ''){
		     
		
		$arrayToJs = parent::validateForm($arr,$pkey);
		
		$name = $arr['name'];   
 
        if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][1]);
		}
          
		
		return $arrayToJs;
    }
    
    function getRunningNumber($pkey,$resetType = 0,$trDate,$warehousekey = 0){ 
         
          
        switch ($resetType) { 
				case '2': 
                    $criteria = ' and trdate = ' .$this->oDbCon->paramDate($trDate,' / '); 
					break;
                
				case '3':
					$criteria = ' and month(trdate) = ' .$this->oDbCon->paramDate($trDate,' / ','m') .' and year(trdate) = '. $this->oDbCon->paramDate($trDate,' / ','Y') ; 
					break; 
                
				case '4':
                    $criteria = ' and year(trdate) = ' .$this->oDbCon->paramDate($trDate,' / ','Y'); 
					break;
                
        }
        
        
        $sql = 'select counter 
                from '.$this->tableNameCounter.' where 
                refkey = ' . $this->oDbCon->paramString($pkey) .' and 
                resettypekey = ' .$this->oDbCon->paramString($resetType) .' and
                warehousekey = ' .$this->oDbCon->paramString($warehousekey) .'
                ';
          
        if (!empty($criteria))
            $sql .= $criteria;
         
        $rs = $this->oDbCon->doQuery($sql);
         
        return (!empty($rs)) ? $rs[0]['counter'] : 0 ;
        
    } 
    
     function normalizeParameter($arrParam, $trim = false){
          
        $arrParam['selWarehouseKey'] = (isset($arrParam['chkResetWarehouse']) && !empty($arrParam['chkResetWarehouse'])) ? $arrParam['selWarehouseKey'] : 0;
         
        $arrParam = parent::normalizeParameter($arrParam); 
          
        $resetType = $arrParam['selResetType'];
        switch ($resetType) {  
				case '2':
                    $arrParam['trDate'] =  $arrParam['selDailyPeriod']; 
				    $arrParam['incrementNumber']  = (!empty($arrParam['dailyIncrement'])) ? $arrParam['dailyIncrement'] : 0;   
                    break;
                case '3': 
                    $arrParam['trDate'] =  date('d / m / Y',strtotime($arrParam['selMonthlyPeriod']));  
                    $arrParam['incrementNumber'] = (!empty($arrParam['monthlyIncrement'])) ? $arrParam['monthlyIncrement'] : 0;   
                    break;
                case '4':
                    $arrParam['trDate'] =  date('01 / 01 / Y',strtotime($arrParam['selAnnuallyPeriod']));  
					$arrParam['incrementNumber'] = (!empty($arrParam['annuallyIncrement'])) ? $arrParam['annuallyIncrement'] : 0;  
                    break;  
                default : 
                    $arrParam['trDate'] = DEFAULT_EMPTY_DATE; 
				    $arrParam['incrementNumber']  = (!empty($arrParam['increment'])) ? $arrParam['increment'] : 0;   
                    break;
        } 
         
         $arrParam['incrementNumber'] = ( $arrParam['incrementNumber'] <= 0) ? 1 :  $arrParam['incrementNumber'];
  
          
        return $arrParam;
    }
    
 
    function getModulCategory($criteria=''){
	    $sql = 'select
                '.$this->tableTablekey.'.pkey, 
                '.$this->tableCode.'.label
			  from
                '.$this->tableTablekey.',
                '.$this->tableCode.'
			  where
			  	' . $this->tableTablekey .'.tablename = '.$this->tableCode.'.code';
        
        /*if(!empty($tablekey))
            $sql .= ' and '.$this->tableTablekey .'.pkey = '.$this->oDbCon->paramString($tablekey);*/
        
        $sql .= $criteria;
        
        $sql .= ' order by label asc';
         
		$rs =  $this->oDbCon->doQuery($sql); 
        
        // security object's access
        $security = new Security();
        $rsSecurity = $security->generateSecurityObject();
            
        foreach($rsSecurity as $module){
            
        }
        
        
        return $rs;
  
   }
} 
