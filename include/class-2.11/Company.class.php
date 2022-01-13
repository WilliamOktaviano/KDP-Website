<?php
class Company extends BaseClass{
    
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'company';
		$this->tableCity = 'city';
		$this->tableEmployee = 'employee';
		$this->tableEmployeeCompany = 'employee_detail_company';
		$this->tableStatus = 'master_status'; 
		$this->securityObject = 'company'; 
       
        $this->arrDeleteTable = array();  
        array_push($this->arrDeleteTable, array('table'=>$this->tableEmployeeCompany,'field' => 'companykey'));  
       
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['employeekey'] = array('hidEmployeeKey'); 
        $this->arrData['citykey'] = array('hidCityKey');
        $this->arrData['isservice'] = array('isService');
        $this->arrData['isretail'] = array('isRetail');
        $this->arrData['address'] = array('address');
        $this->arrData['statuskey'] = array('selStatus');  
        
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'businessPartner','title' => 'businessPartner','dbfield' => 'employeename','default'=>true, 'width' => 300)); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'address','title' => 'address','dbfield' => 'address',  'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'city','title' => 'city','dbfield' => 'cityname',  'width' => 200));
    
        $this->overwriteConfig(); 
 
	}
	
	 function getQuery(){
	   
	   $sql = '
			select
					'.$this->tableName. '.*,
                    '.$this->tableCity.'.name as cityname,
                    '.$this->tableEmployee.'.name as employeename,
					'.$this->tableStatus.'.status as statusname
				from
					'.$this->tableName.' 
                        left join '.$this->tableCity.' on
                          '.$this->tableName.'.citykey = '.$this->tableCity.' .pkey
                        ,
                    '.$this->tableEmployee.', 
                    '.$this->tableStatus.' 
                where
					'.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
					'.$this->tableName.'.employeekey = '.$this->tableEmployee.'.pkey 
                  
 		' .$this->criteria ; 
		  
        return $sql;
    }
   
    
    
    function afterUpdateData($arrParam, $action){
        $this->updateCompanySettings($arrParam);
    }
    
   
	function addData ($arrParam){
         $arrParam['oldEmployeeKey'] = 0 ;
         return parent::addData($arrParam);    
			
	}
	
	function editData($arrParam){    
        $rs = $this->getDataRowById($arrParam['hidId']);
        $arrParam['oldEmployeeKey'] = $rs[0]['employeekey'];

		return parent::editData($arrParam);    
	}
	
	function validateForm($arr,$pkey = ''){
		   
		$arrayToJs = parent::validateForm($arr,$pkey);

		$name = $arr['name'];   
		$employee = $arr['hidEmployeeKey'];   
        
	 	$rs = $this->isValueExisted($pkey,'name',$name);
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['company'][1]);
		}else if(count($rs) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['company'][2]);
		} 
        
        if(empty($employee)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['businessPartner'][1]);
		}
        
		return $arrayToJs;
	 }	 
	   
    
    function updateCompanySettings($companyParam){
        
        $pkey = $companyParam['pkey'];
        
        $warehouse = new Warehouse();
        $employee = new Employee();
        
        $warehouse->filterDataByCompany = false;
        
        $employeekey = $companyParam['hidEmployeeKey'];
        $companykey = $pkey;
            
        // if did not have a default warehouse
        // add new default warehouse
        
        $rsWarehouse = $warehouse->searchData($warehouse->tableName.'.companykey',$pkey,true);
        if (empty($rsWarehouse)){
            $arrParam = array();

            $arrParam['code'] = 'xxxxx';
            $arrParam['name'] = $this->lang['warehouse'] . ' ' . $companyParam['name'];
            $arrParam['selStatus'] = 1;
            $arrParam['qohcount'] = 1;
            $arrParam['webqoh'] = 1;
            $arrParam['address'] = '';
            $arrParam['hidCityKey'] = '';
            $arrParam['zip'] = '';
            $arrParam['trDesc'] = '';
            $arrParam['companykey'] = $companykey;
            $arrParam['userkey'] = $employeekey;
            $arrParam['createdBy'] = 0;
 
            $warehouse->arrData['companykey'] =  array('companykey'); 
            $rsReturn = $warehouse->addData($arrParam);

            $sql = 'update ' . $warehouse->tableName.' set systemVariable = 1  where pkey = ' .$this->oDbCon->paramString($rsReturn[0]['data']['pkey']); 
            $this->oDbCon->execute($sql); 
        }
     
        //cek masi sama gk karyawannya, kalo gk sama, delete
        if ($companyParam['oldEmployeeKey'] <> $companyParam['hidEmployeeKey']){
            $sql = 'delete from ' . $this->tableEmployeeCompany .' where refkey = '.$this->oDbCon->paramString($companyParam['oldEmployeeKey']).'and companykey = ' . $this->oDbCon->paramString($companykey);
            $this->oDbCon->execute($sql);  
        }
            
        //cek ad akses gk
        $rsAccess = $employee->getAccessCompany($employeekey, $pkey);
        if (empty($rsAccess)){ 
            // add new employee_company
            $sql = 'insert into ' . $this->tableEmployeeCompany .' (refkey,companykey) values ('.$this->oDbCon->paramString($employeekey).','.$this->oDbCon->paramString($companykey).' ) ';
            $this->oDbCon->execute($sql);  
        }
         
    }
    
    function delete($id,$forceDelete = false,$reason = ''){
		 
		$warehouse = new Warehouse();
		$arrayToJs =  array(); 

		try{ 
		
	 		$arrayToJs = $this->validateDelete($id);
			if (!empty($arrayToJs)) 
				return $arrayToJs;
					 
			 if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
				 
				$sql = 'delete from  '.$this->tableName.' where pkey = ' . $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);
			 
                $this->deleteReference($id);
            
                // delete warehouse
                // gk boleh pake search data, karena nanti akan include criteriacompany
                $sql = 'select * from '.$warehouse->tableName.' where companykey = ' . $this->oDbCon->paramString($id);
        
                $rsWarehouse = $this->oDbCon->doQuery($sql);
                    
                for($i=0;$i<count($rsWarehouse);$i++){
                      
                    if ($rsWarehouse[$i]['islocked']){ 
                        $warehouse->changeStatus($rsWarehouse[$i]['pkey'],2);
                    }else{ 			 
                        $sql = 'delete from  '.$warehouse->tableName.' where pkey = ' . $this->oDbCon->paramString($rsWarehouse[$i]['pkey']);
                        $this->oDbCon->execute($sql);
                    } 
                }
            
                $this->setTransactionLog(DELETE_DATA,$id);
            
				$this->oDbCon->endTrans(); 

				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']); 
				 
		} catch(Exception $e){
			$this->oDbCon->rollback(); 
			$this->addErrorList($arrayToJs,false, $e->getMessage()); 
			
		}		 
			 	
 		return $arrayToJs; 
	}
     
 
}
?>