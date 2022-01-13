<?php

class Employee extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'employee';
		$this->tableCategory = 'employee_category'; 
		$this->tableStatus = 'employee_status';
		$this->tableCompany = 'company'; 
        $this->tableWarehouse = 'warehouse';
        $this->tableCustomer = 'customer';
        $this->tableCOA = 'chart_of_account';
		$this->tableEmployeeCompany = 'employee_detail_company';
		$this->tableEmployeeCustomer = 'employee_detail_customer';
		$this->tableEmployeeWarehouse = 'employee_detail_warehouse';
		$this->tableEmployeeSales = 'employee_detail_sales';
		$this->tableCity = 'city';
		$this->tableImageID = 'employee_image'; // gk boleh pake tableImage, akan otomatis narik jadiny nanti di baseclass
		$this->tableCityCategory = 'city_category'; 
		$this->tableSecurityAccess = 'security_access';	  
        $this->tableContact = 'contact_person';	  
        $this->tableCOAAccess = 'user_coa_access';
        $this->uploadFolder = 'employee/';
        $this->uploadPhotoFolder = 'employee-photo/';
        $this->uploadSignatureFolder = 'employee-signature/';
		$this->securityObject = 'Employee';	    
		$this->securityPrivilegesObject = 'SecurityPrivileges';	   
       
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code'); 
        $this->arrData['categorykey'] = array('selCategory');
        $this->arrData['warehousekey'] = array('selWarehouse');
        $this->arrData['name'] = array('memberName');
        $this->arrData['livingaddress1'] = array('livingAddress1');
        $this->arrData['livingaddress2'] = array('livingAddress2');
        $this->arrData['address1'] = array('memberAddress1');
        $this->arrData['address2'] = array('memberAddress2');
        $this->arrData['citykey'] = array('hidCityKey');
        $this->arrData['zipcode'] = array('memberZipCode');
        $this->arrData['phone'] = array('memberPhone');
        $this->arrData['mobile'] = array('memberMobile');
        $this->arrData['email'] = array('memberEmail');
        $this->arrData['isdriver'] = array('chkIsDriver');
        $this->arrData['issales'] = array('chkIsSales');
        $this->arrData['placeofbirth'] = array('hidPlaceOfBirthKey');
        $this->arrData['dateofbirth'] = array('dateOfBirth', 'date');
        $this->arrData['drivinglicense'] = array('drivingLicense');
        $this->arrData['drivinglicenseexpdate'] = array('drivingLicenseExpDate', 'date');
        $this->arrData['idnumber'] = array('IDNumber');
        $this->arrData['taxid'] = array('taxid');
        $this->arrData['religionkey'] = array('religion');
        $this->arrData['nationality'] = array('nationality');
        $this->arrData['maritalstatuskey'] = array('maritalStatus');
        $this->arrData['sexkey'] = array('sex');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['password'] = array('memberPassword');  
        $this->arrData['username'] = array('memberUserName');
        $this->arrData['secretAuth'] = array('secretAuth');
        $this->arrData['cashbankcoakey'] = array('hidCashBankCOAKey');
        $this->arrData['commissionapcoakey'] = array('hidCommissionAPCOAKey');
        $this->arrData['arcoakey'] = array('hidARCOAKey');
        $this->arrData['apcoakey'] = array('hidAPCOAKey');
        $this->arrData['photofile'] = array('photoFile');
        $this->arrData['signaturefile'] = array('signatureFile');
        $this->arrData['allwarehouseaccess'] = array('chkAllWarehouseAccess');
        $this->arrData['allcustomeraccess'] = array('chkAllCustomerAccess');
        $this->arrData['allcoaaccess'] = array('chkAllCOAAccess');
        $this->arrData['allsalesaccess'] = array('chkAllSalesAccess');
        $this->arrData['bankname'] = array('bankName');
        $this->arrData['bankaccountname'] = array('bankAccountName');
        $this->arrData['bankaccountnumber'] = array('bankAccountNumber');
        $this->arrData['position'] = array('position');
        $this->arrData['needrealization'] = array('chkNeedRealization');
        $this->arrData['commissionpercentage'] = array('commissionPercentage','number');
         
        $this->allowedStatusForEdit = array(1,2);
		
        $this->arrLockedTable = array(); 
        array_push($this->arrLockedTable, array('table'=>'transaction_log','field'=>'createdby'));
        array_push($this->arrLockedTable, array('table'=>'sales_order_header','field'=>'saleskey'));
        array_push($this->arrLockedTable, array('table'=>'service_order_header','field'=>'saleskey')); 
        array_push($this->arrLockedTable, array('table'=>'service_work_order','field'=>'driverkey'));  
        array_push($this->arrLockedTable, array('table'=>'ap_employee_commission','field'=>'employeekey'));  
        array_push($this->arrLockedTable, array('table'=>'ap_employee_commission_payment_header','field'=>'employeekey'));  
       
        $this->arrDeleteTable = array(); 
        array_push($this->arrDeleteTable, array('table'=>$this->tableContact,'field' => array('refkey'=>'{id}', 'reftable'=>$this->tableName)));   
        array_push($this->arrDeleteTable, array('table'=>$this->tableSecurityAccess,'field' => array('userkey'=>'{id}')));   
        array_push($this->arrDeleteTable, array('table'=>$this->tableImageID,'field' => array('refkey'=>'{id}')));   
        array_push($this->arrDeleteTable, array('table'=>$this->tableEmployeeCompany,'field' => array('refkey'=>'{id}')));   
        
        array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true,'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'division','title' => 'division','dbfield' => 'categoryname','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'phone','title' => 'phone','dbfield' => 'phone', 'default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'email','title' => 'email','dbfield' => 'email', 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'username','title' => 'username','dbfield' => 'username', 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
    
        $this->includeClassDependencies(array( 
              'AR.class.php',      
              'Category.class.php',      
              'ChartOfAccount.class.php', 
              'City.class.php',      
              'Company.class.php',
              'Customer.class.php',      
              'EmployeeCategory.class.php',      
              'PaymentMethod.class.php', 
              'RoleTemplate.class.php', 
              'TermOfPayment.class.php', 
              'Location.class.php', 
              'LoginLog.class.php'
        ));
       require_once DOC_ROOT. 'phpthumb/phpThumb.config.php';  
       
		$this->overwriteConfig();
       
   }
    
	function getQuery(){
	   
	   $sql = '
				select
					'.$this->tableName. '.*,
					'.$this->tableCategory.'.name as categoryname, 
					'.$this->tableStatus.'.status as statusname	, 
					'.$this->tableCity.'.name as cityname, 
					'.$this->tableCityCategory.'.name as citycategoryname	, 
					'.$this->tableWarehouse.'.name as warehousename			
				from 
					'.$this->tableName . ' 
						 left join '.$this->tableWarehouse.' on '.$this->tableName . '.warehousekey = '.$this->tableWarehouse.'.pkey 
						 left join '.$this->tableCity.' on '.$this->tableName . '.citykey = '.$this->tableCity.'.pkey 
						 left join '.$this->tableCityCategory.' on '.$this->tableCity . '.categorykey = '.$this->tableCityCategory.'.pkey 
                         inner join '.$this->tableStatus.' on '.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey    
                         inner join '.$this->tableCategory.' on '.$this->tableName . '.categorykey = '.$this->tableCategory.'.pkey     
				where  		
					1=1
					
 		' .$this->criteria ; 
        
        $sql .= $this->getCompanyCriteria()	; 
        $sql .= $this->getWarehouseCriteria() ; 
        
        //khusus employee, tambahkan owner (atau yg login mungkin ??)
        //$sql .= ' or '.$this->tableName. '.pkey  = ' .  $this->oDbCon->paramString($this->userkey);
        //$this->setLog($sql); 
        return $sql;
    }
	
	function addData($arrParam){
		
		$arrayToJs =  array();
		
		try{ 
            
		 	if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]); 
			 
			$code = $this->getNewCustomCode($arrParam);	
            
            $arrParam['code'] = (is_array($code)) ? $code[0] : $code;
                
            $pkey = $this->getNextKey($this->tableName);  
            $arrParam['pkey'] = $pkey;
			$arrParam['hidId'] = $pkey; // UTK UDPATE PRIVILEGES DETAIL
            
            $arrParam = $this->normalizeParameter($arrParam);
			$arrayToJs = $this->validateForm($arrParam);
			if (!empty($arrayToJs)) 
					return $arrayToJs;
            
			
            $arrParam['secretAuth'] = '';
            
            $arrParam['memberPassword'] = hash('sha256',md5($arrParam['memberPassword']));  
            
            if (!$this->hasSecurityPrivileges()){
               unset ($this->arrData['username']);
               unset ($this->arrData['password']); 
            }
			
            $this->updateData($arrParam,INSERT_DATA);   
            
            $this->updateImages($pkey, $arrParam['token-id-image-uploader'], $arrParam['id-image-uploader'], $this->tableImageID);  
            
            $this->updateDetail($pkey,$arrParam);   
            
			$this->oDbCon->endTrans();
					 
			$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   
            $rs = $this->searchData($this->tableName.'.pkey',$arrParam['pkey'],true);
            $arrayToJs[0]['data'] = $rs[0];
            
					 
		} catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false, $e->getMessage());   
		}
		
		return $arrayToJs; 	 	
		 
	}


	function editData($arrParam){  
		
		$arrayToJs =  array();
			
		try{ 
		
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
            
			$arrParam['pkey'] = $arrParam['hidId'];	
			$code = $this->getNewCustomCode($arrParam);	 
            $arrParam['code'] = (is_array($code)) ? $code[0] : $code;
            
            
            $arrParam = $this->normalizeParameter($arrParam);
			$arrayToJs = $this->validateForm($arrParam,$arrParam['hidId']);
			if (!empty($arrayToJs)) 
					return $arrayToJs; 
            
            $updatePassword = '';
            $password = '';
			if (!empty($arrParam['memberPassword'])){
			    $password = hash('sha256',md5($arrParam['memberPassword'])); 
                $arrParam['memberPassword'] = $password;
			}else{
                unset($this->arrData['password']); 
            } 
             
             
            if (!empty($arrParam['updateProfile'])){ 
                 unset($this->arrData['statuskey']);
                 unset($this->arrData['categorykey']);
                 unset($this->arrData['warehousekey']);
                 unset($this->arrData['isdriver']);
                 unset($this->arrData['issales']);
                 unset($this->arrData['drivinglicense']);
                 unset($this->arrData['drivinglicenseexpdate']);
                 unset($this->arrData['placeofbirth']);
                 unset($this->arrData['dateofbirth']);
                 unset($this->arrData['drivingLicenseExpDate']);
                 unset($this->arrData['religionkey']);
                 unset($this->arrData['nationality']);
                 unset($this->arrData['maritalstatuskey']);
                 unset($this->arrData['sexkey']);
                 unset($this->arrData['idnumber']);
                 unset($this->arrData['taxid']);
                 unset($this->arrData['address1']);
                 unset($this->arrData['address2']);
                 unset($this->arrData['citykey']);
                 unset($this->arrData['zipcode']);
                 unset($this->arrData['photofile']); 
                 unset($this->arrData['signaturefile']); 
            }else{
                 unset($this->arrData['secretAuth']); 
            }
            
            if (!$this->hasSecurityPrivileges())
                 unset($this->arrData['username']); 
            
            $this->updateData($arrParam,UPDATE_DATA);
             
            if (empty($arrParam['updateProfile']) && !isset($arrParam['_isImport_'])){  
                $this->updateDetail($arrParam['hidId'],$arrParam); 
                $this->updateImages($arrParam['hidId'], $arrParam['token-id-image-uploader'], $arrParam['id-image-uploader'], $this->tableImageID);   
            }
          
                
            /*
            if (!empty($updatePassword))
                $this->syncPass($arrParam['memberUserName'], $password);
            */
            	 
            
			$this->oDbCon->endTrans();  
			$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   
					
				
		} catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false, $e->getMessage());  
		}		
				 
 		return $arrayToJs; 
	}
	 
    function updateDetail($pkey, $arrParam){  
                
        $this->updatePrivilegesDetail($arrParam); 
         
        $this->updateCOAAccess($pkey, $arrParam);
        $rsOwnedCompany =  $this->getOwnedCompany($pkey);
        if(empty($rsOwnedCompany)) 
            $this->updateCompany($pkey, $arrParam);
         
        $this->updateWarehouseAccess($pkey, $arrParam);
        $this->updateCustomerAccess($pkey, $arrParam);
        $this->updateSalesAccess($pkey, $arrParam);
    }
    
    	  
    
  function updateCOAAccess($pkey,$arrParam){
        
	  	if(!USE_GL) return;
	  
        // filter hanya COA yg punya akses saja
        $arrCOA = $this->getCOAAccess($this->userkey);
        
		$sql = 'delete from '.$this->tableCOAAccess.' where refkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql);
       
        $hasAllCOAAccess = ($arrParam['chkAllCOAAccess'] == 1) ? true : false;
                
        if ($hasAllCOAAccess)  return; 
        if (!isset($arrParam['selCOAAccess']))  return;
      
		$arrCOAAccess = $arrParam['selCOAAccess'];

        for ($i=0;$i<count($arrCOAAccess);$i++){ 
            if (!in_array( $arrCOAAccess[$i],$arrCOA))
                continue;

            $sql = 'insert into  '.$this->tableCOAAccess.' (refkey,coakey) values ('.$this->oDbCon->paramString($pkey).', '.$this->oDbCon->paramString($arrCOAAccess[$i]).' )';	
            $this->oDbCon->execute($sql);

        }
					 
	}
    
    function updateCustomerAccess($pkey,$arrParam){ 
        if(!$this->isActiveModule('Customer')) return;
		
        // filter hanya Customer yg punya akses saja
        $arrCustomer = $this->getCustomerAccess($this->userkey);
        
		$sql = 'delete from '.$this->tableEmployeeCustomer.' where refkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql);
       
        $hasAllCustomerAccess = ($arrParam['chkAllCustomerAccess'] == 1) ? true : false;
        if ($hasAllCustomerAccess)  return; 
        if (!isset($arrParam['selCustomerAccess'])) return;

		$arrCustomerAccess = $arrParam['selCustomerAccess'];
        //$this->setLog($arrCustomerAccess,true);
        
        for ($i=0;$i<count($arrCustomerAccess);$i++){ 
            if (!in_array($arrCustomerAccess[$i],$arrCustomer))
                continue;

            $sql = 'insert into  '.$this->tableEmployeeCustomer.' (refkey,customerkey) values ('.$this->oDbCon->paramString($pkey).', '.$this->oDbCon->paramString($arrCustomerAccess[$i]).' )';
            $this->oDbCon->execute($sql);      

        }
					 
	}

     function updateSalesAccess($pkey,$arrParam){ 
        
        // filter hanya Customer yg punya akses saja
        $arrSales = $this->getSalesAccess($this->userkey);
        
		$sql = 'delete from '.$this->tableEmployeeSales.' where refkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql);
       
        $hasAllSalesAccess = ($arrParam['chkAllSalesAccess'] == 1) ? true : false;
        if ($hasAllSalesAccess)  return; 
        if (!isset($arrParam['selSalesAccess'])) return;

		$arrSalesAccess = $arrParam['selSalesAccess'];
        //$this->setLog($arrCustomerAccess,true);
        
        for ($i=0;$i<count($arrSalesAccess);$i++){ 
            if (!in_array($arrSalesAccess[$i],$arrSales))
                continue;

            $sql = 'insert into  '.$this->tableEmployeeSales.' (refkey,saleskey) values ('.$this->oDbCon->paramString($pkey).', '.$this->oDbCon->paramString($arrSalesAccess[$i]).' )';
            $this->oDbCon->execute($sql);      

        }
					 
	}

    function updateWarehouseAccess($pkey, $arrParam){
         
        if (!isset($arrParam['selWarehouseAccess'])) return;
         
        $sql = 'delete from '.$this->tableEmployeeWarehouse.' where refkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql); 
        
        $hasAllWarehouseAccess = ($arrParam['chkAllWarehouseAccess'] == 1) ? true : false;
        
        if ($hasAllWarehouseAccess) return;
        
        
        // filter hanya warehouse yg punya akses saja
        $arrWarehouse = $this->getWarehouseAccess($this->userkey);
        $arrWarehouseAccess = $arrParam['selWarehouseAccess'];   
        
        /*$this->setLog($arrWarehouse,true);
        $this->setLog($arrWarehouseAccess,true);*/
        
        for ($i=0;$i<count($arrWarehouseAccess);$i++){ 
            if (!in_array( $arrWarehouseAccess[$i],$arrWarehouse)) continue;
            
            $sql = 'insert into  '.$this->tableEmployeeWarehouse.' (refkey,warehousekey) values ('.$this->oDbCon->paramString($pkey).','.$this->oDbCon->paramString($arrWarehouseAccess[$i]).')';	
            $this->oDbCon->execute($sql); 
        }
        
    }
    
    function updateCompany($pkey, $arrParam) {
        $sql = 'delete from '.$this->tableEmployeeCompany.' where refkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql); 
          
		$arrCompany = $arrParam['selCompany'];
        //kalo company kosong, ambil company pertama 
        
        if (empty($arrCompany)){ 
            $rsCompany = array_column($this->getAccessCompany($this->userkey),'companykey');
            $arrCompany[0] = $rsCompany[0];
        }
            
        for ($i=0;$i<count($arrCompany);$i++){ 
            $sql = 'insert into  '.$this->tableEmployeeCompany.' (refkey,companykey) values ('.$this->oDbCon->paramString($pkey).','.$this->oDbCon->paramString($arrCompany[$i]).')';	
            $this->oDbCon->execute($sql); 
        }
    }
	
	function updatePrivilegesDetail($arrParam){
         
        // update contacts
        $this->updateContactPerson($arrParam['hidId'],$arrParam); 
        
        $hasSecurityPrivileges = $this->hasSecurityPrivileges();
        
        if(!$hasSecurityPrivileges) return;
        
		$sql = 'delete from security_access where userkey = ' . $arrParam['hidId'];
		$this->oDbCon->execute($sql);
		 
		
		$security = new Security();
		$rsSecurityObject  = $security->generateSecurityObject(); 
		
		for ($i=0;$i<count($rsSecurityObject);$i++){
		 	
			if (!isset($arrParam['chkList' . $rsSecurityObject[$i]['pkey']]))
				continue;
		
			for($j=0;$j<count($arrParam['chkList' . $rsSecurityObject[$i]['pkey']]);$j++){
				$sql = '
					INSERT INTO		
					security_access ( 
						userkey,
						objectkey,
						statuskey 
						)
						VALUES	(
						'.$arrParam['hidId'].', 
						'.$this->oDbCon->paramString($rsSecurityObject[$i]['pkey']).', 
						'.$this->oDbCon->paramString($arrParam['chkList' . $rsSecurityObject[$i]['pkey']][$j]).' 
					)
				';
				
				$this->oDbCon->execute($sql);
			} 
		}
	} 

    
    function validateForm($arr,$pkey = ''){
		  
		$arrayToJs = parent::validateForm($arr,$pkey);  
	    
	    $name =  $arr['memberName'];  
		$email = $arr['memberEmail'];  
		$citykey = $arr['hidCityKey'];   
	   
        $rsData = $this->searchData();
        
        if($this->checkTotalItemLimitation($this->tableName,PLAN_TYPE['maxuser'],$pkey)){  
          $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][1]. ' ('.$this->lang['max'].' '. $this->formatNumber(PLAN_TYPE['maxuser']). ' '. strtolower($this->lang['employees']).')');  
        }
                 
		$rsEmployee = $this->isValueExisted($pkey,'name',$name);	 
        if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['employee'][1]);
		}else if(count($rsEmployee ) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['employee'][2]);
		}
        
 
		$rsEmail = $this->isValueExisted($pkey,'email',$email);
		if(!empty($email)){ 
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) 
                $this->addErrorList($arrayToJs,false,$this->errorMsg['email'][3]);
            else if(count($rsEmail) <> 0) 	
                $this->addErrorList($arrayToJs,false,$this->errorMsg['email'][2]); 
        } 
        
        if (isset($arr['selCompany'])){
             $companyKey = $arr['selCompany'];
            for($i=0;$i<count($companyKey);$i++){ 
                $rsCompany = $this->getAccessCompany($this->userkey,$companyKey[$i]);
                if (empty($rsCompany)){
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['company'][3]);  
                    break;
                }
            }  
        }
       
        
        // jika bkn dr profile 
        if (empty($arr['updateProfile'])){ 
            $hasSecurityPrivileges = $this->hasSecurityPrivileges();
            if ($hasSecurityPrivileges){ 
                $username = $arr['memberUserName'];  
                $pass = $arr['memberPassword'];  
                $passConfirmation = $arr['memberPasswordConfirmation'];  

                $rsUsername = $this->isValueExisted($pkey,'username',$username);	 

                if(!empty($username)) { 
                     if (strlen($username) <  5 || strlen($username) > 30) 
                         $this->addErrorList($arrayToJs,false,$this->errorMsg['username'][3]);

                     if (count($rsUsername) <> 0) 
                        $this->addErrorList($arrayToJs,false,$this->errorMsg['username'][2]); 
                }


                if(!empty($pass)) {  
                    if($pass <> $passConfirmation) 
                        $this->addErrorList($arrayToJs,false,$this->errorMsg['password'][3]); 
                    
                    if(!$this->checkPasswordStrength($pass))
                        $this->addErrorList($arrayToJs,false,$this->errorMsg['password'][4]); 
                }
            }
        }
        
		return $arrayToJs;
	 }
	 
                   
	 function syncPass($username,$password){
	     return ;
         
	     // PERLU TAMBAH UTK SYNC KE DB LAIN YG MASI SATU USER 
	     
	    /* $dbCon = new Database(PS_DB['dbuser'],PS_DB['dbpass'],PS_DB['dbname'],'localhost'); 
         
	     try{
			   
		  	if(!$dbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
		     
		    $sql = 'update customer set password = '. $this->oDbCon->paramString($password).' where username = ' . $this->oDbCon->paramString($username);
	        $dbCon->execute($sql);
	        
			$dbCon->endTrans();
	 				 
		} catch(Exception $e){
		    $dbCon->rollback(); 
		}*/
		
	 }
	  
     
    function normalizeParameter($arrParam, $trim = false){
         
            if (!isset($arrParam['chkIsDriver']) || $arrParam['chkIsDriver'] == 0){ 
                $arrParam['drivingLicenseExpDate'] =  date('d / m / Y');
            }
        
            if (empty($arrParam['updateProfile']) && !isset($arrParam['_isImport_'])){ 
                
                if ($this->hasAllWarehouseAccess($this->userkey))
                    $arrParam['chkAllWarehouseAccess'] = (isset($arrParam['chkAllWarehouseAccess'])) ? $arrParam['chkAllWarehouseAccess'] : 0;
                else
                    $arrParam['chkAllWarehouseAccess']  = 0;

                if (!empty($this->hasAllCustomerAccess($this->userkey)))
                    $arrParam['chkAllCustomerAccess'] = (isset($arrParam['chkAllCustomerAccess'])) ? $arrParam['chkAllCustomerAccess'] : 0;
                else
                    $arrParam['chkAllCustomerAccess']  = 0;
                
                if ($this->hasAllCOAAccess($this->userkey))
                    $arrParam['chkAllCOAAccess'] = (isset($arrParam['chkAllCOAAccess'])) ? $arrParam['chkAllCOAAccess'] : 0;
                else
                    $arrParam['chkAllCOAAccess']  = 0; 
  
            
                if ($this->hasAllSalesAccess($this->userkey))
                    $arrParam['chkAllSalesAccess'] = (isset($arrParam['chkAllSalesAccess'])) ? $arrParam['chkAllSalesAccess'] : 0;
                else
                    $arrParam['chkAllSalesAccess']  = 0; 
  
                $arrParam['photoFile'] = $this->updateImages($arrParam['pkey'], $arrParam['token-photo-image-uploader'], $arrParam['photo-image-uploader'],'',$this->uploadPhotoFolder);  
                $arrParam['signatureFile'] = $this->updateImages($arrParam['pkey'], $arrParam['token-signature-image-uploader'], $arrParam['signature-image-uploader'],'',$this->uploadSignatureFolder);  
                
            }
        
        
        
            if (!isset($arrParam['selWarehouse']) || empty($arrParam['selWarehouse'])){ 
                $warehouse = new Warehouse();
                $arrParam['selWarehouse'] = $warehouse->getDefaultData();
            }
        
		    $arrParam = parent::normalizeParameter($arrParam,true);   
            
        return $arrParam;
    }
    
    function hasSecurityPrivileges(){
        $security = new Security();
        return $security->isAdminLogin($this->securityPrivilegesObject,10);   
    }
    
    
    function getCOAAccess($employeekey = ''){
        
	  	if(!USE_GL) return;
		
        $chartOfAccount = new ChartOfAccount();  
         
        $employeekey = (isset($employeekey) && !empty($employeekey)) ? $employeekey : $this->userkey ;
             
        if ($this->hasAllCOAAccess($employeekey)){
            $sql = 'select pkey as coakey from ' . $chartOfAccount->tableName;  
        }else{
            $sql = 'select 
                    '. $this->tableCOAAccess. '.*,
                    ' . $this->tableCOA .'.name as coaname
                from 
                    ' . $this->tableCOAAccess. ',
                    ' . $this->tableCOA. '
                where 
                    ' . $this->tableCOAAccess .'.coakey = ' . $this->tableCOA .'.pkey and
                    ' . $this->tableCOA .'.statuskey = 1 and
                    refkey = ' . $this->oDbCon->paramString($employeekey).'
                order by  ' . $this->tableCOA .'.pkey asc
                    ';
            }
            
        $rs = $this->oDbCon->doQuery($sql);
        $arrCOA = array_column($rs,'coakey'); 
          
        return $arrCOA;
    }
    
    function getWarehouseAccess($employeekey = ''){ 
        
        $employeekey = (isset($employeekey) && !empty($employeekey)) ? $employeekey : $this->userkey ; 
        
        if ($this->hasAllWarehouseAccess($employeekey)){
            // JGN PAKE SEARCH DATA, looping forever 
            $sql = 'select pkey as warehousekey from ' . $this->tableWarehouse;   
        } else{
            $sql = 'select 
                    ' . $this->tableEmployeeWarehouse .'.* ,
                    ' . $this->tableWarehouse .'.name as warehousename
                from 
                    ' . $this->tableEmployeeWarehouse .',
                    ' . $this->tableWarehouse .'
                where 
                    ' . $this->tableEmployeeWarehouse .'.warehousekey = ' . $this->tableWarehouse .'.pkey and
                    ' . $this->tableWarehouse .'.statuskey = 1 and
                    refkey = ' . $this->oDbCon->paramString($employeekey) ; // masalah gk kalo statuskeynya gk di masukin di criteria 
        } 
        

        $rs = $this->oDbCon->doQuery($sql);
        $arrWarehouse = array_column($rs,'warehousekey'); 

        return $arrWarehouse;
    }
    
    function getCustomerAccess($employeekey = ''){ 
            
        $employeekey = (isset($employeekey) && !empty($employeekey)) ? $employeekey : $this->userkey ;
         
        if ( $this->hasAllCustomerAccess($employeekey) ){ 
            $sql = 'select pkey as customerkey from ' . $this->tableCustomer;
        } else {
            $sql = 'select 
                    ' . $this->tableEmployeeCustomer .'.* ,
                    ' . $this->tableCustomer .'.name as customername
                from 
                    ' . $this->tableEmployeeCustomer .',
                    ' . $this->tableCustomer .'
                where 
                    ' . $this->tableEmployeeCustomer .'.customerkey = ' . $this->tableCustomer .'.pkey and
                    refkey = ' . $this->oDbCon->paramString($employeekey) ; 
        }
        
        $rs = $this->oDbCon->doQuery($sql);
        $arrCustomer = array_column($rs,'customerkey'); 
        
        return $arrCustomer;
         
    }

    
    function getSalesAccess($employeekey = ''){ 
            
        $employeekey = (isset($employeekey) && !empty($employeekey)) ? $employeekey : $this->userkey ;
         
        if ( $this->hasAllSalesAccess($employeekey) ){ 
            $sql = 'select pkey as saleskey from ' . $this->tableName;
        } else {
            $sql = 'select 
                    ' . $this->tableEmployeeSales .'.* ,
                    ' . $this->tableName .'.name as salesname
                from 
                    ' . $this->tableEmployeeSales .',
                    ' . $this->tableName .'
                where 
                    ' . $this->tableEmployeeSales .'.saleskey = ' . $this->tableName .'.pkey and
                    refkey = ' . $this->oDbCon->paramString($employeekey) ; 
        }
        
        $rs = $this->oDbCon->doQuery($sql);
        $arrSales = array_column($rs,'saleskey'); 
        
        return $arrSales;
         
    }

    function getAccessCompany($employeekey,$companykey = ''){
        
        $sql = 'select 
                    ' . $this->tableEmployeeCompany .'.* ,
                    ' . $this->tableCompany .'.name as companyname
                from 
                    ' . $this->tableEmployeeCompany .',
                    ' . $this->tableCompany .'
                where 
                    ' . $this->tableEmployeeCompany .'.companykey = ' . $this->tableCompany .'.pkey and
                    ' . $this->tableCompany .'.statuskey = 1 and
                    refkey = ' . $this->oDbCon->paramString($employeekey) ;
        
        if (!empty($companykey))
            $sql .= ' and companykey = ' . $this->oDbCon->paramString($companykey) ;
              
        return $this->oDbCon->doQuery($sql);
    }
    
    function getOwnedCompany($employeekey){
        $company = new Company();
        
        $rsCompany = $company->searchData($company->tableName.'.statuskey',1,true, ' and ' . $company->tableName.'.employeekey=' . $this->oDbCon->paramString($employeekey));
        return $rsCompany;
    }
    
    function checkIsUserFranchisee($employeekey){
        $sql = 'select * from ' . $this->tableCompany .' where employeekey = ' . $this->oDbCon->paramString($employeekey) ;
        $rs = $this->oDbCon->doQuery($sql);
        
        return (empty($rs)) ? false : true;
    }
    
    
    function delete($id,$forceDelete = false,$reason = ''){
		 
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
            
                $this->deleteAll($this->defaultDocUploadPath.$this->uploadFolder.$id);
                $this->deleteAll($this->defaultDocUploadPath.$this->uploadPhotoFolder.$id);
                $this->deleteAll($this->defaultDocUploadPath.$this->uploadSignatureFolder.$id);
            
                $this->setTransactionLog(DELETE_DATA,$id);
            
				$this->oDbCon->endTrans(); 

				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']); 
				 
		} catch(Exception $e){
			$this->oDbCon->rollback(); 
			$this->addErrorList($arrayToJs,false, $e->getMessage()); 
			
		}		 
			 	
 		return $arrayToJs; 
	}
    
    
    function updateAPCommissionOutstanding($employeekey){
        
    }
    
    function updateAROutstanding($customerkey){
         
		  $arrayToJs = array();
         
         try{	  
				if(!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]); 
			   
                $ar = new AREmployee();
                $outstanding = $ar->getAROutstanding($customerkey);

                $sql = 'update ' . $this->tableName .' set aroutstanding = ' .  $this->oDbCon->paramString($outstanding) .' where pkey = ' .  $this->oDbCon->paramString($customerkey);
                $this->oDbCon->execute($sql);
				
                $this->oDbCon->endTrans();  
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);  
			
			} catch(Exception $e){
				$this->oDbCon->rollback();
				$this->addErrorList($arrayToJs,false, $e->getMessage());  
			}	 
      
   } 
    
        
    function getAPCommissionCOAKey($employeekey,$warehousekey){ 
        $coaLink = new COALink();
        $warehouse = new Warehouse();
        
        $rsEmployee = $this->getDataRowById($employeekey);
        if (!empty($rsEmployee[0]['apcommissioncoakey'])){  
             $coakey = $rsEmployee[0]['apcommissioncoakey'];
        }else{ 
            $rsCOA = $coaLink->getCOALink ('commissionap', $warehouse->tableName,  $warehousekey);   
            $coakey = $rsCOA[0]['coakey'];
        }
        
        return $coakey;
    }
            
    function getARCOAKey($employeekey,$warehousekey){ 
        $coaLink = new COALink();
        $warehouse = new Warehouse();
        
        $rs = $this->getDataRowById($employeekey);
        if (!empty($rs[0]['arcoakey'])){  
             $coakey = $rs[0]['arcoakey'];
        }else{ 
            $rsCOA = $coaLink->getCOALink ('employeear', $warehouse->tableName,  $warehousekey);   
            $coakey = $rsCOA[0]['coakey'];
        }
        
        return $coakey;
    }
    
                
    function getCashAdvCOAKey($employeekey,$warehousekey){ 
        $coaLink = new COALink();
        $warehouse = new Warehouse();
        
        $rsEmployee = $this->getDataRowById($employeekey);
        if(!empty($rsEmployee[0]['cashbankcoakey'])){ 
            $coakey = $rsEmployee[0]['cashbankcoakey'];
        }else{ 
            $rsCOA = $coaLink->getCOALink ('cashbankdriver', $warehouse->tableName,$warehousekey, 0);  
            $coakey = $rsCOA[0]['coakey'];
        }
        
        return $coakey;
    }
    
    
     
  }

?>
