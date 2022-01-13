<?php

class Customer extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'customer'; 
		$this->tableStatus = 'customer_status';
		$this->tableCity = 'city';
		$this->tableCategory = 'customer_category'; 
		$this->tableCityCategory = 'city_category'; 
		$this->tableCustomerSocialMedia = 'customer_social_media'; 
		$this->tableSocialMedia = 'social_media'; 
		$this->tableTermOfPayment = 'term_of_payment';	  
		$this->tableCurrency = 'currency'; 
        $this->tableContact = 'contact_person';	     
	    $this->tableBusinessCategory = 'business_category';
	    $this->tableCustomerBusiness = 'customer_business_category_detail';
	    $this->tableCustomerFeaturesDetail = 'customer_features_detail';
	    $this->tableMembershipSubscription = 'membership_subscription';

        $this->tableMembershipLevel= 'membership_level';	  
        $this->tablePaymentMethod = 'payment_method';	  
        $this->tableMultipleAddress = 'multiple_address_detail';
	   	$this->tableEmployeeCustomer = 'employee_detail_customer';	  
       	$this->tableCustomerSocialMedia = 'customer_social_media';
       	$this->tableMedia = 'media';
       	$this->tableLocation = 'location';
        $this->tableSex = '_sex';	  
	    $this->tableFile = 'customer_file';
	   
	   	$this->uploadFolder = 'customer/';
	    $this->uploadFileFolder = 'customer-file/';
		$this->securityObject = 'Customer';
        $this->creditLimitSecurityObject = 'creditLimitApproval';
		
        $this->importUrl = 'import/customer';
       
        $this->arrContactPerson = array(); 
        $this->arrContactPerson['pkey'] = array('hidContactPersonDetailKey'); 
        $this->arrContactPerson['refkey'] = array('pkey', 'ref');
        $this->arrContactPerson['reftable'] = array('reftable',array('mandatory'=>true));
        $this->arrContactPerson['name'] = array('cpName',array('mandatory'=>true));
        $this->arrContactPerson['position'] = array('cpPosition');
        $this->arrContactPerson['phone'] = array('cpPhone');
        
        $this->arrBusinessCategory = array(); 
        $this->arrBusinessCategory['pkey'] = array('hidDetailKey'); 
        $this->arrBusinessCategory['refkey'] = array('pkey', 'ref'); 
        $this->arrBusinessCategory['refbusinesskey'] = array('selBusinessDetailKey',array('mandatory'=>true));
        
        $arrDetails = array(); 
        array_push($arrDetails, array('dataset' => $this->arrContactPerson, 'tableName' => $this->tableContact));
        array_push($arrDetails, array('dataset' => $this->arrBusinessCategory, 'tableName' => $this->tableCustomerBusiness));
       
        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails)); 
        $this->arrData['code'] = array('code'); 
        $this->arrData['sid'] = array('sid'); 
        $this->arrData['name'] = array('name');
        $this->arrData['alias'] = array('alias');
        $this->arrData['categorykey'] = array('selCategory');
        $this->arrData['username'] = array('userName');
        $this->arrData['password'] = array('password');
        $this->arrData['address'] = array('address');
        $this->arrData['sexkey'] = array('sex');
        $this->arrData['citykey'] = array('hidCityKey');
        $this->arrData['referralkey'] = array('hidReferralKey');
        $this->arrData['zipcode'] = array('zipCode');
        $this->arrData['phone'] = array('phone');
        $this->arrData['mobile'] = array('mobile');
        $this->arrData['email'] = array('email');
        $this->arrData['fax'] = array('fax'); 
        $this->arrData['idnumber'] = array('IDNumber');
        $this->arrData['dateofbirth'] = array('dob', 'date');
        $this->arrData['placeofbirth'] = array('hidPlaceOfBirthKey');
        $this->arrData['weight'] = array('weight', 'number');
        $this->arrData['height'] = array('height', 'number');
        $this->arrData['occupation'] = array('occupation');
        $this->arrData['fbaccount'] = array('FBAccount');
        $this->arrData['igaccount'] = array('IGAccount');
                     
        $this->arrData['taxid'] = array('taxid');
        $this->arrData['taxregistrationname'] = array('taxRegistrationName');
        $this->arrData['taxregistrationaddress'] = array('taxRegistrationAddress');
        $this->arrData['description'] = array('description');
        $this->arrData['termofpaymentkey'] = array('selTermOfPayment');
        $this->arrData['companybankkey'] = array('selBank');
        $this->arrData['activationhashkey'] = array('activationhashkey');
        $this->arrData['creditlimit'] = array('creditlimit','number');
        
        $this->arrData['arcoakey'] = array('hidARCOAKey');
        $this->arrData['downpaymentcoakey'] = array('hidDownpaymentCOAKey'); 
        $this->arrData['currencypreference'] = array('selCurrencyPreference'); 
        $this->arrData['statuskey'] = array('selStatus'); 
        $this->arrData['saleskey'] = array('hidSalesKey'); 
        $this->arrData['mediakey'] = array('selMedia'); 
        $this->arrData['ismainaccount'] = array('chkIsMainAccount'); 
        $this->arrData['locationkey'] = array('hidLocationKey'); 
        $this->arrData['attention'] = array('attention');
        $this->arrData['parentkey'] = array('hidParentKey');
        $this->arrData['subscriptionstatuskey'] = array('selSubscriptionStatus');
        $this->arrData['activationdate'] = array('activationDate', 'date');
	    $this->arrData['photofile'] = array('photoFile');
        $this->arrData['ssotypekey'] = array('hidSSOTypeKey'); 
        $this->arrData['latlng'] = array('hidLatLng'); 
        $this->arrData['mapaddress'] = array('mapAddress'); 
        $this->arrData['membershiplevel'] = array('selMembership'); 
        $this->arrData['companyname'] = array('companyName'); 
        $this->arrData['mainbusinesskey'] = array('selBusiness'); 


        $this->allowedStatusForEdit = array(1,2);
       
        $this->arrLockedTable = array();
        $defaultFieldName = 'customerkey'; 
        array_push($this->arrLockedTable, array('table'=>'ar','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'ar_payment_header','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'billing_statement_header','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'email_blast','field'=>$defaultFieldName));  
        array_push($this->arrLockedTable, array('table'=>'gallery_header','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'preorder_header','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'rewards_point','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'sales_order_car_service_header','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'sales_order_header','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'sales_return_header','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'service_order_header','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'trucking_selling_rate_header','field'=>$defaultFieldName));  
        array_push($this->arrLockedTable, array('table'=>'trucking_service_order_header','field'=>$defaultFieldName));   
        array_push($this->arrLockedTable, array('table'=>'emkl_job_order_detail','field'=>$defaultFieldName));  
         
       
        $this->arrDeleteTable = array(); 
        array_push($this->arrDeleteTable, array('table'=>$this->tableContact,'field' => array('refkey'=>'{id}', 'reftable'=>$this->tableName)));  
        array_push($this->arrDeleteTable, array('table'=>$this->tableMultipleAddress,'field' => array('refkey'=>'{id}', 'reftable'=>$this->tableName)));  
        array_push($this->arrDeleteTable, array('table'=>$this->tableCustomerSocialMedia,'field' => array('refkey'=>'{id}')));  
                      
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'category','title' => 'category','dbfield' => 'categoryname','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'phone','title' => 'phone','dbfield' => 'phone','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'mobilePhone','title' => 'mobilePhone','dbfield' => 'mobile','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'email','title' => 'email','dbfield' => 'email', 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'address','title' => 'address','dbfield' => 'address', 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'arOutstanding','title' => 'outstanding','dbfield' => 'aroutstanding', 'width' => 80, 'align' => 'right', 'format' => 'integer'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'dob','title' => 'dateOfBirth','dbfield' => 'dateofbirth',  'width' => 150, 'align' =>'center', 'format' => 'date'));
        
	    if(PLAN_TYPE['categorykey'] == 1)
	   		array_push($this->arrDataListAvailableColumn, array('code' => 'point','title' => 'point','dbfield' => 'point',  'width' => 80, 'align' => 'right', 'format' => 'integer'));
        
	   
        $this->includeClassDependencies(array(
              'ChartOfAccount.class.php',  
              'AR.class.php',  
              'PaymentMethod.class.php', 
              'TermOfPayment.class.php', 
              'Category.class.php', 
              'CustomerCategory.class.php',
              'City.class.php',  
              'Location.class.php',  
              'Currency.class.php',  
              'ItemMovement.class.php',  
              'Location.class.php',  
              'Media.class.php',  
              'MembershipSubscription.class.php',  
			  'BusinessCategory.class.php',
			  'CustomerFeatures.class.php',
			  'MembershipLevel.class.php',
			  'Warehouse.class.php'
        ));
       
       if($this->isActiveModule('truckingServiceOrder')){ 
           $this->includeClassDependencies(array(
                'TruckingServiceOrder.class.php',  
            ));
       }
       
       
        $this->overwriteConfig();
   }
   function getQuery(){
	   
	   $sql = '
			select
					'.$this->tableName. '.*, 
                    '.$this->tableCategory.'.name as categoryname,
                    '.$this->tableMedia.'.name as medianame,
                    '.$this->tableLocation.'.name as locationname,
					'.$this->tableStatus.'.status as statusname,	
                    IF(subscriptionstatuskey=1, "Aktif", "Tidak Aktif") as subscriptionstatus,
                    IF(ismainaccount=1, "<i class=\"fas fa-check text-green-avocado\"></i>", "") as mainaccounticon,
					'.$this->tableCity.'.name as cityname , 
					'.$this->tableCurrency.'.name as currencyname,
                    '.$this->tableTermOfPayment.'.name as termofpayment, 
					'.$this->tableCityCategory.'.name as citycategoryname,
                    '.$this->tablePaymentMethod.'.name as paymentmethodname,
                    concat ('.$this->tableCity. '.name, ", ", '.$this->tableCityCategory.'.name) as cityandcategoryname,
                    '.$this->tableSex.'.name as sexname,
					'.$this->tableMembershipLevel.'.name as membershiplevelname
				from 
					'.$this->tableName . ' 
						 left join '. $this->tableCategory.' on '.$this->tableName . '.categorykey = '.$this->tableCategory.'.pkey  
						 left join '.$this->tableCity.' on '.$this->tableName . '.citykey = '.$this->tableCity.'.pkey 
						 left join '.$this->tableLocation.' on '.$this->tableName . '.locationkey = '.$this->tableLocation.'.pkey 
						 left join '.$this->tableMedia.' on '.$this->tableName . '.mediakey = '.$this->tableMedia.'.pkey 
						 left join '.$this->tableCityCategory.' on '.$this->tableCity . '.categorykey = '.$this->tableCityCategory.'.pkey 
						 left join '.$this->tableCurrency.' on '.$this->tableName . '.currencypreference = '.$this->tableCurrency.'.pkey
                         left join '.$this->tableTermOfPayment.' on '.$this->tableName . '.termofpaymentkey = '.$this->tableTermOfPayment.'.pkey
                         left join '.$this->tablePaymentMethod.' on '.$this->tableName . '.companybankkey = '.$this->tablePaymentMethod.'.pkey 
                         left join '.$this->tableSex.' on '.$this->tableName . '.sexkey = '.$this->tableSex.'.pkey
						 left join '.$this->tableMembershipLevel.' on '.$this->tableName.'.membershiplevel = '.$this->tableMembershipLevel.'.pkey,
					'.$this->tableStatus.' 
				where  		 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey   
					
 		' .$this->criteria ; 
         
        
        $sql .=  $this->getCustomerCriteria() ;
        return $sql;
    }
	
	
	
	function resetReferralKeyIfQuotaExceed($pkey){
		// $pkey => customerkey
		$customerFeatures = new CustomerFeatures();
		
		// hanya hapus jika ada quota dan melebih quota
		$rsCustomer = $this->searchDataRow( array($this->tableName.'.referralkey'),
										  	' and '.$this->tableName.'.pkey = ' . $this->oDbCon->paramString($pkey));
 		
		if(empty($rsCustomer) || $rsCustomer[0]['referralkey'] == 0) return false;
		
		$referralkey = $rsCustomer[0]['referralkey'] ;
			
		// cek batasan quota
		$arrReturn = $customerFeatures->getFeaturesQuota($referralkey,'','referral');
		if(empty($arrReturn)) return false;
		
		$featurekey = $arrReturn[0]['featurekey'];
		$quota = $arrReturn[0]['quota'];
		$quotaUsed = $arrReturn[0]['quotaused'];
		
		if($quotaUsed >= $quota){
			$sql = 'update '.$this->tableName.'  set '.$this->tableName.'.referralkey = 0 where '.$this->tableName.'.pkey = ' . $this->oDbCon->paramString($pkey);
			$this->oDbCon->execute($sql); 
			return true;
		}
		
		return false;
		
	}
	
    function afterUpdateData($arrParam, $action){ 
        
        $pkey = $arrParam['pkey'];
        $this->updateQuestionnair($pkey, $arrParam);
        //$this->updateCustomerMembership($pkey, $arrParam);
        $this->updateCustomerSocialMedia($pkey, $arrParam);
        
        // kalo add user baru
        if ($action == INSERT_DATA && !empty($arrParam['fromFE'])){ 
            $this->sendActivationEmail($pkey);
			
			// kalo ada keangggotaan 
			if( $arrParam['selMembership'] <> $arrParam['_mnv-joined-membership']){
				$newLevel = $arrParam['_mnv-joined-membership'];
				
				$membershipSubscription = new MembershipSubscription();
				$membershipLevel = new MembershipLevel();
				$warehouse = new Warehouse();
				$termOfPayment = new TermOfPayment();
				$rsLevel = $membershipLevel->getDataRowById($newLevel);
				
				$arrMembership = array();
				
           		$arrMembership['code'] = 'xxxxx';
           		$arrMembership['trDate'] = date('d / m / Y');
           		$arrMembership['selWarehouseKey'] = $warehouse->getDefaultData();
           		$arrMembership['selTermOfPaymentKey'] = $termOfPayment->getDefaultData();
				$arrMembership['hidCustomerKey'] =$pkey;
				$arrMembership['subtotal'] = $rsLevel[0]['sellingprice'];
				$arrMembership['grandtotal'] = $rsLevel[0]['sellingprice'];
				$arrMembership['selStatus'] = 1; 
				$arrMembership['selMembershipLevel'] = $newLevel;  
				 
				$membershipSubscription->addData($arrMembership);
			}
			
			// kalo ad referralkey dan quota
			// table customer_features_detail harusnya selalu ada  
			// sementara patokannya per bulan dulu 
			// counter sudah dihitung pada saat customer register
			
			if(!empty($arrParam['hidReferralKey'])){  
				
                // reset referral key kalo usdha lebih
				$isReset = $this->resetReferralKeyIfQuotaExceed($pkey);
				
				// kalo masih ad quota
				if(!$isReset)
					$this->updateMembershipFeaturesCounter($arrParam['hidReferralKey'],'referral',array('date' => date('01 / m / Y'))); 
			}
			
		}
             
        $this->addContactsToThirdParty($arrParam['email']);
        
        if (!empty($arrParam['fromFE']) || isset($arrParam['_isImport_'])) return;
        
         
        $arrParam['maTypeKey'] = 1;
        $this->updateMultipleAddres($pkey, $arrParam);
        $this->updateCustomerAccess($pkey);

		if(isset($arrParam['item-file-uploader'])) 
            $this->updateFile($arrParam['pkey'], $arrParam['token-item-file-uploader'], $arrParam['item-file-uploader']); 
          
    } 
    
	function updateMembershipFeaturesCounter($customerkey,$funckey,$opt = array()){
		
		$customerFeatures = new CustomerFeatures();
		
		$arrReturn = $customerFeatures->getFeaturesQuota($customerkey,'',$funckey);
		if(empty($arrReturn)) return;
		
		$featurekey = $arrReturn[0]['featurekey'];
		$featurequota = $arrReturn[0]['quota'];
			
		// kalo ad periode, isi
		// sementar patokannya per bulan dulu
		$arrCriteria = array(); 
		if(!empty($opt['date']))
			array_push($arrCriteria,'dateperiod = ' .$this->oDbCon->paramDate($opt['date'],' / ')); 
		
		$criteria = (!empty($arrCriteria)) ? ' and '. implode(' and ', $arrCriteria) : '';
		
		// cari dulu sudah terdaftar blm, kalo blm ad, add new row
		$sql = 'select  pkey from  '.$this->tableCustomerFeaturesDetail.' 
				where  
					refkey = '.$this->oDbCon->paramString($customerkey) .' and
					featurekey = '. $this->oDbCon->paramString($featurekey); 
		$sql .= $criteria;
		
		$rs = $this->oDbCon->doQuery($sql);
		 
		if(empty($rs)){
			$dateperiod = !empty($opt['date']) ? $this->oDbCon->paramDate($opt['date'],' / ') : '\'\'';
			$sql = 'insert into '.$this->tableCustomerFeaturesDetail.' (refkey,featurekey,quota,quotaused,dateperiod) 
					values ('.$this->oDbCon->paramString($customerkey).','.$this->oDbCon->paramString($featurekey).','.$this->oDbCon->paramString($featurequota).',1,'.$dateperiod.') ';
			$this->oDbCon->execute($sql);
		}else{
		    $sql = 'update '.$this->tableCustomerFeaturesDetail.'
					set quotaused = quotaused  + 1  
					where
						refkey = '.$this->oDbCon->paramString($customerkey) .' and
						featurekey = '. $this->oDbCon->paramString($featurekey);
			
			$arrCriteria = array(); 
			if(!empty($opt['date']))
			array_push($arrCriteria,'dateperiod = ' .$this->oDbCon->paramDate($opt['date'],' / ')); 
			$criteria = (!empty($arrCriteria)) ? ' and '. implode(' and ', $arrCriteria) : '';
			$sql .= $criteria;
			
			$this->oDbCon->execute($sql);
		}
		
		
	}
	
    function addContactsToThirdParty($email){
       //nanti perlu cek ulang kalo sudah terdaftar lebih baik gk perlu nembak lg
        $smtpAgent = $this->loadSetting('SMTPAgent');
        $folderId = $this->loadSetting('sendInBlueDefaultFolderId');  
        switch($smtpAgent){
            case 'sendinblue' : 
                    $this->includeClassDependencies(array("SendInBlue.class.php"));
                    $sendinblue = new SendInblue();
                    $sendinblue->createContact($folderId,$email); 
                    break;
            default : break;
        }
    }
    
    function updateQuestionnair($pkey, $arrParam){
         
      /*  if(isset($arrParam['hidQuestionKey']) && !empty($arrParam['hidQuestionKey'])){
            
            $questionnaireResponse = new QuestionnaireResponse();
            
            $arrQuestionnaire = array();
            $arrQuestionnaire['code'] = 'xxxxxx';
            $arrQuestionnaire['userkey'] = $pkey;
            $arrQuestionnaire['hidRefKey'] = $arrParam['hidQuestionnaireKey'];
            $arrQuestionnaire['trDate'] = date('d / m / Y');
            $arrQuestionnaire['selStatus'] = 1;

            // response
            for($i=0;$i<count($arrParam['hidQuestionKey']);$i++){ 
                $questionkey = $arrParam['hidQuestionKey'][$i];
                $arrQuestionnaire['hidDetailKey'][$i] = 0;
                $arrQuestionnaire['hidQuestionKey'][$i] = $questionkey;
                $arrQuestionnaire['answer'][$i] = $arrParam['questionnaireAnswer'.$questionkey]; 
                $arrQuestionnaire['trDesc'][$i] = (isset($arrParam['questionnaireAnswerDescription'.$questionkey]) && !empty($arrParam['questionnaireAnswerDescription'.$questionkey])) ? $arrParam['questionnaireAnswerDescription'.$questionkey] : '';
            }

             $questionnaireResponse->addData($arrQuestionnaire);
        }
*/
    }
	
//	function updateCustomerMembership($pkey, $arrParam){
//         
//        if(isset($arrParam['membership']) && !empty($arrParam['membership'])){
//            
//            $customerMembership = new CustomerMembership();
//            $arrMembership = array();
//            $arrMembership['code'] = 'xxxxxx';
//            $arrMembership['hidCustomerKey'] = $pkey;
//            $arrMembership['hidReferralKey'] = $arrParam['hidReferralKey'];
//            $arrMembership['selMembership'] = $arrParam['membership'];
//            $arrMembership['trDate'] = date('d / m / Y'); 
//            $arrMembership['selStatus'] = 1;
//
//            $customerMembership->addData($arrMembership);
//        }
//
//    }

    
    function updateCustomerSocialMedia($pkey, $arrParam){
        if(!isset($arrParam['hidSocialKey'])) return;
        
        $socialMedia = new SocialMedia();
         
        $sql = 'delete from '.$this->tableCustomerSocialMedia.' where refkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql); 
         
         
        foreach($arrParam['hidSocialKey'] as $row){  
            $sql = 'insert into '.$this->tableCustomerSocialMedia.' (refkey,socialkey,value) values ('.$this->oDbCon->paramString($pkey).','.$this->oDbCon->paramString($row).','.$this->oDbCon->paramString($arrParam['socialMedia'.$row]).')';	
            $this->oDbCon->execute($sql); 
        }
                                     
    }

	function updateCustomerAccess($pkey){
		$userkey = $this->userkey;
		$sql = 'insert into  '.$this->tableEmployeeCustomer.' (refkey,customerkey) values ('.$this->oDbCon->paramString($userkey).', '.$this->oDbCon->paramString($pkey).' )';
        $this->oDbCon->execute($sql);
	}
  
    function delete($id,$forceDelete = false, $reason = ''){
		
        //$questionnaireResponse = new QuestionnaireResponse();
        
		$arrayToJs =  array();
	   
		try{ 
		
	 		$arrayToJs = $this->validateDelete($id);
			if (!empty($arrayToJs)) 
				return $arrayToJs;
					 
			 if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
				 
                $rs = $this->getDataRowById($id);
            
				$sql = 'delete from  '.$this->tableName.' where pkey = ' . $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);
				$this->deleteAll($this->defaultDocUploadPath.$this->uploadFolder.$id);
			
         
                // delete quittionare 
                /*$rsQuiz = $questionnaireResponse->searchData($questionnaireResponse->tableName.'.userkey',$id);
            
            	$sql = 'delete from  '.$questionnaireResponse->tableNameDetail.' where refkey = ' . $this->oDbCon->paramString($rsQuiz[0]['pkey']);
				$this->oDbCon->execute($sql);
             
            	$sql = 'delete from  '.$questionnaireResponse->tableName.' where pkey = ' . $this->oDbCon->paramString($rsQuiz[0]['pkey']);
				$this->oDbCon->execute($sql); */
            
            
                $this->deleteReference($id);
            
                $this->setTransactionLog(DELETE_DATA,$id);
            
				$this->oDbCon->endTrans(); 

				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyDeleted']); 
				 
                $this->afterCommitDelete($rs); 
            
		} catch(Exception $e){
			$this->oDbCon->rollback(); 
			$this->addErrorList($arrayToJs,false, $e->getMessage()); 
			
		}		 
			 	
 		return $arrayToJs; 
	}
    
	function sendActivationEmail($userkey){
		
        global $twig;
        
        $rsCust = $this->getDataRowById($userkey);
        $activationLink =  HTTP_HOST.'activation/'.$rsCust[0]['pkey'].'/'.$rsCust[0]['activationhashkey'];
	 
        // nanti jadikan default variable
        $arrTwigVar = array();
        $arrTwigVar = $this->getDefaultEmailVariable();
         
        $arrTwigVar['CUSTOMER_NAME'] = $rsCust[0]['name']; 
        $arrTwigVar['ACTIVATION_LINK'] = $activationLink;
         
        $twig->render('email-template.html');  
        $content = $twig->render('email-activation.html', $arrTwigVar);
 
        $this->sendMail(array(), $this->lang['activationEmail'] . ' - ' . DOMAIN_NAME,$content,array('name' => $rsCust[0]['name'], 'email'=>$rsCust[0]['email'])); 
        
	}
	
	function requestRecoverAccount($arr){
        
		global $twig;
        
		$arrayToJs =  array();
		 
		$arrayToJs = $this->validateRequestRecoverAccount($arr);
        
        if (!empty($arrayToJs))  return $arrayToJs;
				 
        
        $rsCust = $this->searchDataRow(array($this->tableName.'.pkey', $this->tableName.'.activationhashkey', $this->tableName.'.name', $this->tableName.'.email'),
                           ' and '.$this->tableName.'.email = '.$this->oDbCon->paramString($arr['email']).'
                             and '.$this->tableName.'.statuskey = 2'); // cuma yg aktif yg boleh reset password
        
		//$rsCust = $this->searchData('','',true,' and '.$this->tableName.'.email = ' . $this->oDbCon->paramString($arr['email']) );
				  
         
		$resetLink =  HTTP_HOST.'account-recovery/'.$rsCust[0]['pkey'].'/'.$rsCust[0]['activationhashkey'];
	 
        $arrTwigVar = array();
        $arrTwigVar = $this->getDefaultEmailVariable();
           
        $arrTwigVar['CUSTOMER_NAME'] = $rsCust[0]['name'];
        $arrTwigVar['RESET_PASSWORD_LINK'] = $resetLink;
           
        $twig->render('email-template.html');  
        $content = $twig->render('email-reset-password.html', $arrTwigVar);
        
        $this->sendMail(array(), $this->lang['accountRecovery'] . ' - ' . DOMAIN_NAME,$content,array('email'=>$rsCust[0]['email'])); 
		
		$this->addErrorList($arrayToJs,true,$this->lang['emailSentSuccessful']);  
		return 	$arrayToJs; 
		
	}
	 
	function resendActivationEmail($arr){ 
		 	
		$arrayToJs =  array();
		$arrayToJs = $this->validateResendActivation($arr);
		if (!empty($arrayToJs)) 
				return $arrayToJs;
					
		$rsCust = $this->searchDataRow(array($this->tableName.'.pkey'),
                                       ' and '.$this->tableName.'.email = '.$this->oDbCon->paramString($arr['email']).'
                                         and '.$this->tableName.'.statuskey = 1');
        
        
		$this->sendActivationEmail($rsCust[0]['pkey']);
		$this->addErrorList($arrayToJs,true,$this->lang['emailSentSuccessful']);
		return 	$arrayToJs;
			
	}
	
	function activateMember($userkey,$activationhashkey){
		
		$arrayToJs =  array();
				
        $rsCust = $this->searchDataRow(array($this->tableName.'.pkey'),
                           ' and '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($userkey).'
                             and '.$this->tableName.'.activationhashkey = '.$this->oDbCon->paramString($activationhashkey).' 
                             and '.$this->tableName.'.statuskey = 1'); 

		//$rsCust = $this->searchData('','',true,' and '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($userkey).' and '.$this->tableName.'.activationhashkey = '.$this->oDbCon->paramString($activationhashkey).' and '.$this->tableName.'.statuskey = 1');
		
        if (empty($rsCust))	{
  				$this->addErrorList($arrayToJs,false, $this->errorMsg[302]); 
	 	}else{
			try{	 
			
				if(!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]);
			  
				$sql = 'update ' . $this->tableName .' set statuskey = 2, activationdate = now() where pkey = ' . $this->oDbCon->paramString($userkey);
				$this->oDbCon->execute($sql);
				
				$this->oDbCon->endTrans();  
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);  
			
			} catch(Exception $e){
				$this->oDbCon->rollback();
				$this->addErrorList($arrayToJs,false, $e->getMessage());  
			}	 	  
		} 
		
 		return $arrayToJs; 
			
	}
	
	function resetPassword($userkey,$activationhashkey){
		
	 	global $twig;
        
		$arrayToJs =  array();
		 
        $rsCust = $this->searchDataRow(array($this->tableName.'.pkey',$this->tableName.'.name',$this->tableName.'.email'),
                           ' and '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($userkey).'
                             and '.$this->tableName.'.activationhashkey = '.$this->oDbCon->paramString($activationhashkey).' 
                             and '.$this->tableName.'.statuskey = 2'); 
         
        
		if (empty($rsCust))	{
				$this->addErrorList($arrayToJs,false, $this->errorMsg[303]); 
		}else{
			try{	  
				
				$newPassword = $this->generateStrongPassword();
				
				if(!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]);
			  
				//randon hashkey baru untuk mencegah reset password 2x
                
                    
				$sql = 'update ' . $this->tableName .' set password ='.$this->oDbCon->paramString(hash('sha256',md5($newPassword))).', activationhashkey = md5(now() + md5(email)) where pkey = ' . $this->oDbCon->paramString($userkey);
				$this->oDbCon->execute($sql);
				 
                $arrTemplate = array();

                // nanti jadikan default variable
                $companyName = $this->loadSetting('companyName');
                $arrTemplate['COMPANY_NAME'] = $companyName;
                $arrTemplate['HTTP_HOST'] = HTTP_HOST;

                $arrTwigVar = array();
                $arrTwigVar['CUSTOMER_NAME'] = $rsCust[0]['name'];
                $arrTwigVar['COMPANY_NAME'] = $companyName;
                $arrTwigVar['NEW_PASSWORD'] = $newPassword;

                $twig->render('email-template.html', $arrTemplate);  
                $content = $twig->render('email-new-password.html', $arrTwigVar);

                $this->sendMail(array(), $this->lang['resetPassword'] . ' - ' . DOMAIN_NAME,$content,array('email'=>$rsCust[0]['email'])); 
  
				$this->oDbCon->endTrans();  
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);  
			
			} catch(Exception $e){
				$this->oDbCon->rollback();
				$this->addErrorList($arrayToJs,false, $e->getMessage());  
			}	 	  
		} 
			  	    
 		return $arrayToJs;  
				
	}
	
	
	function validateRequestRecoverAccount($arr){
		  
		$arrayToJs = array();
	     
        if (!IS_DEVELOPMENT){ 
            if(!isset($arr['_mnv-api'])){ 
                $captchaResponse = $arr['g-recaptcha-response'];  
                $request = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$this->loadSetting('reCaptchaSecretKey')."&response=".$captchaResponse);
                $captchaResult = json_decode($request);

                $errorCaptcha= $captchaResult->{'error-codes'};  

                if (empty($captchaResponse)){
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['captcha'][1]);
                } else if(!$captchaResult->{'success'}){
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['captcha'][1]);
                }  
            }
        }
				
        $rsCust = $this->searchDataRow(array($this->tableName.'.pkey'),
                               ' and '.$this->tableName.'.email = '.$this->oDbCon->paramString($arr['email']).'
                                 and '.$this->tableName.'.statuskey = 2'); 
         
		if (empty($rsCust)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['email'][4]); 
			return $arrayToJs; 
		} 
		
		return $arrayToJs;
	 }  
	 
	
	function validateResendActivation($arr){
		  
		  
		$arrayToJs = array();
	     
        if (!IS_DEVELOPMENT){ 
            if(!isset($arr['_mnv-api'])){ 
                $captchaResponse = $arr['g-recaptcha-response'];  
                $request = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$this->loadSetting('reCaptchaSecretKey')."&response=".$captchaResponse);
                $captchaResult = json_decode($request);

                $errorCaptcha= $captchaResult->{'error-codes'};  

                if (empty($captchaResponse)){
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['captcha'][1]);
                } else if(!$captchaResult->{'success'}){
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['captcha'][1]);
                }  
            }
        }
        
        
        $rsCust = $this->searchDataRow(array($this->tableName.'.pkey'),
                               ' and '.$this->tableName.'.email = '.$this->oDbCon->paramString($arr['email']).'
                                 and '.$this->tableName.'.statuskey = 1'); 
		if (empty($rsCust))   
			$this->addErrorList($arrayToJs,false,$this->errorMsg['email'][4]);   	
		 
		return $arrayToJs;
	 }  
	 
	function validateForm($arr,$pkey = ''){ 
        
        $arrayToJs = parent::validateForm($arr,$pkey);  
	    
	 	$username = isset($arr['userName']) ? $arr['userName'] : '';   
 
        if (!IS_DEVELOPMENT){ 
			
			// hanya jika add data
            if ($pkey == '' && isset($arr['fromFE']) && !empty($arr['fromFE'])){ 
                // kalo bukan dr login SSO gk perlu validasi ini
                if(!isset($arr['mnv-OAuth']) || $arr['mnv-OAuth'] == 0){ 

                    if(empty($username)){
                        $this->addErrorList($arrayToJs,false,$this->errorMsg['username'][1]);
                    } 

                    // khusus edit
                    if(!empty($pkey) && empty($arr['chkAgree'])){
                        $this->addErrorList($arrayToJs,false,$this->errorMsg['registration'][1]);
                    }

                     if(!isset($arr['_mnv-api'])){  
                        $captchaResponse = $arr['g-recaptcha-response'];  
                        $request = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$this->loadSetting('reCaptchaSecretKey')."&response=".$captchaResponse);
                        $captchaResult = json_decode($request);

                        $errorCaptcha= $captchaResult->{'error-codes'};  
                        if (empty($captchaResponse)){
                            $this->addErrorList($arrayToJs,false,$this->errorMsg['captcha'][1]);
                        } else if(!$captchaResult->{'success'}){
                            $this->addErrorList($arrayToJs,false,$this->errorMsg['captcha'][1]);
                        } 
                     }
                }
            } 
        } 
	 
        if(isset($arr['item-file-uploader']) && !empty($arr['item-file-uploader'])){ 
            $arrFile = explode(",",$arr['item-file-uploader']);
            if(count($arrFile) > PLAN_TYPE['maxproductfile'])
                $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][3] .' ('.$this->lang['max'].' '. $this->formatNumber(PLAN_TYPE['maxproductfile']). ' '. strtolower($this->lang['files']).')' );

            for($i=0;$i<count($arrFile);$i++){
                if (empty($arrFile[$i]))
                    continue;

                $path = $this->uploadTempDoc.$this->uploadFileFolder.$arr['token-item-file-uploader']; 
                if (filesize($path.'/'.$arrFile[$i]) > (pow(1024,2) * PLAN_TYPE['maxfilesize']) )
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][5] .' ('.$this->lang['max'].' '. $this->formatNumber(PLAN_TYPE['maxfilesize']). ' MB)' );
            }
        }	  
        
        $name = $arr['name'];  
        
        if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['customer'][1]);
		}else{
			 
		 if( $this->loadSetting('uniqueCustomerName') == 1){    
			$rsCustomer = $this->isValueExisted($pkey,'name',$name);	
			if(count($rsCustomer) <> 0) 
				$this->addErrorList($arrayToJs,false,$this->errorMsg['customer'][2]);
		 }
			
		}
        
	 	if(isset($arr['email']) && !empty($arr['email'])){
            $email = $arr['email'];
  
			if(!filter_var($email, FILTER_VALIDATE_EMAIL)) 
				$this->addErrorList($arrayToJs,false,$this->errorMsg['email'][3]);
			
			 if( $this->loadSetting('uniqueCustomerEmail') == 1){    
				$rsCust = $this->isValueExisted($pkey,'email',$email);	
				if(count($rsCust) <> 0) 
					$this->addErrorList($arrayToJs,false,$this->errorMsg['email'][2]);
			 }
		}  
		
		if(!empty($username)){
            
            // kalo dr SSO baru validasi
            // sementara nilainya ambil dr ajax aj dulu
            if(!isset($arr['mnv-OAuth'])){ 
                $rsCust = $this->isValueExisted($pkey,'username',$username);	 
                if(count($rsCust) <> 0){
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['username'][2]);
                }

                if (!empty($arr['fromFE'])){
                    $strlen = strlen($username);
                    if ($strlen < 5 || $strlen > 30){
                        $this->addErrorList($arrayToJs,false,$this->errorMsg['username'][3]);
                    }
                }

                if ( !preg_match('/^[a-zA-Z0-9_\.]+$/', $username) ){
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['username'][4]); 
                }
            }
		}
        
        // kalo ad kuisioner
        if(isset($arr['hidQuestionKey']) && !empty($arr['hidQuestionKey'])){
            $hasBlankAnswer = false;
            // harus kasi paramter baru, mandatory atau tdk
            foreach($arr['hidQuestionKey'] as $questionkey){ 
                if (empty($arr['questionnaireAnswer'.$questionkey])){
                    $hasBlankAnswer = true;
                    break;
                }
            }
            
            if($hasBlankAnswer)
                 $this->addErrorList($arrayToJs,false,$this->errorMsg['questionnaire'][2]); 
        }
        
        // agreement 
        if (isset($arr['chkAgree'])){
            if(empty($arr['chkAgree']))
                 $this->addErrorList($arrayToJs,false,$this->errorMsg['registration'][1]); 
        }
		 
		return $arrayToJs;
	 }  
	  
    function generateDefaultQueryForAutoComplete($returnField){  
        
            $sql = 'select
					'.$returnField['key']. ',  
                    '.$returnField['value']. ' as value,
                    '.$this->tableName. '.name as name,
                    '.$this->tableName. '.address, 
                    '.$this->tableName. '.phone,
                    '.$this->tableName. '.email,
                    '.$this->tableName. '.creditlimit ,
                    '.$this->tableName. '.taxid ,
                    '.$this->tableName. '.termofpaymentkey,
                    '.$this->tableName. '.currencypreference,
                    '.$this->tableName. '.companybankkey
                    
				from 
					'.$this->tableName . ' ,'.$this->tableStatus.' 
				where  		 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey  
			'; 
        
            $sql .=  $this->getCustomerCriteria() ; 
        
            return $sql;
        
    } 
       
    function updateAROutstanding($customerkey){
         
		  $arrayToJs = array();
         
         try{	  
				if(!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]); 
			   
                $ar = new AR();
                $outstanding = $ar->getAROutstanding($customerkey);

                $salesAsAROutstanding = $this->loadSetting('salesAsAROutstanding');
                if($salesAsAROutstanding == 1){ 
                    // kalo trucking harus tambah JO blm diinvoie
                    // sum selisih total JO dengan total invoiced
                    if($this->isActiveModule('truckingServiceOrder')){ 
                        $truckingServiceOrder = new TruckingServiceOrder();
                        $totalUninvoiced = $truckingServiceOrder->getCustomerUninvoicedAmount($customerkey);
                        $outstanding += $totalUninvoiced;
                    }
                    
                    // nanti tambahkan juga utk sales order
                }
             
                $sql = 'update ' . $this->tableName .' set aroutstanding = ' .  $this->oDbCon->paramString($outstanding) .' where pkey = ' .  $this->oDbCon->paramString($customerkey);
                $this->oDbCon->execute($sql);
				
                $this->oDbCon->endTrans();  
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);  
			
			} catch(Exception $e){
				$this->oDbCon->rollback();
				$this->addErrorList($arrayToJs,false, $e->getMessage());  
			}	 
      
     } 
    
    function getCustomerCreditLimitSummary($customerkey,$overdue = false, $orderBy=''){
        
        // status non atkif tetpe harus dimunculin
        //$criteria = ' and creditlimit > 0 '; // jgn ditambahin biar keliatan kalo ad yg lupa diset
        
        $criteria = '';
        
        if($overdue)
            $criteria .= ' and '.$this->tableName.'.creditlimit < '.$this->tableName.'.aroutstanding';
            
        $rsCustomer = $this->searchDataRow(
            array($this->tableName.'.pkey',$this->tableName.'.code',$this->tableName.'.name',$this->tableName.'.creditlimit',$this->tableName.'.aroutstanding'),
            $criteria,
            $orderBy
        );
        
        return $rsCustomer;
    }
    
    function willExceedCreditLimit($customerkey,$amount){
        
        $amount = $this->unFormatNumber($amount);
        
        $rs = $this->getDataRowById($customerkey);
        if ($rs[0]['creditlimit'] <= 0 )
            return false;
        
        if ($rs[0]['aroutstanding'] + $amount  > $rs[0]['creditlimit'])
            return true;
        else
            return false;
            
    }
    
//    function getMembershipLevel($pkey = array()){
//
//		$sql = 'select 
//					'.$this->tableMembershipLevel.'.*
//				from 
//					'.$this->tableMembershipLevel.'
//				where
//				   1 =1
//				';
//
//		if(!empty($pkey))
//			$sql .=  ' and '.$this->tableMembershipLevel.'.pkey in ('.$this->oDbCon->paramString($pkey,',').')';
//		 
//        return  $this->oDbCon->doQuery($sql); 
//          
//    }
    
     function updatePassword($arrParam){
        
		$arrayToJs =  array();
			
		try{
			
            if (empty($arrParam['hidUserKey']))
                return;
            
			$id = $arrParam['hidUserKey'];
            
            $rs = $this->getDataRowById($id);
            
			$username = $rs[0]['username'];
            $currentPassword = $arrParam['currentPassword'];
            $password = $arrParam['password'];
            
            if(!$this->checkPassword($id,$username,$currentPassword)){
                $this->addErrorList($arrayToJs,false,$this->errorMsg['username'][5]);  
                return $arrayToJs;
            }
            
            if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]); 
            
            
            $sql = 'update customer set password = \''.hash('sha256',md5($password)).'\' where pkey = ' . $this->oDbCon->paramString($id);
            $this->oDbCon->execute($sql);
      
            $this->setTransactionLog(UPDATE_DATA,$id);
            
			$this->oDbCon->endTrans();  
            
            //send email
            /*
            $companyName = $this->loadSetting('companyName');

            $content =  $this->lang['updatePasswordContent'];
            $emailTemplate = $this->getEmailTemplate(); 

            $patterns = array();
            $patterns[0] = '/({{CONTENT}})/';
            $patterns[1] = '/({{CUSTOMER_NAME}})/'; 
            $patterns[2] = '/({{COMPANY_NAME}})/';

            $replacement = array();
            $replacement[0] = $content;
            $replacement[1] = $rs[0]['name']; 
            $replacement[2] = $companyName;  

            $email = preg_replace($patterns, $replacement, $emailTemplate); 

            $this->sendMail(array(),$this->lang['updatePassword'] .' - ' . $companyName,$email,array('email'=>$rs[0]['email']));	 
            */
            
			$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']); 
		
	    } catch(Exception $e){
			$this->oDbCon->rollback(); 
			$this->addErrorList($arrayToJs,false,$e->getMessage()); 
		}		
				 
 		return $arrayToJs;  
	   
    }
    
        
    function getARCOAKey($customerkey,$warehousekey){ 
        $coaLink = new COALink();
        $warehouse = new Warehouse();
        
        $rsCustomer = $this->getDataRowById($customerkey);
        if (!empty($rsCustomer[0]['arcoakey'])){  
             $coakey = $rsCustomer[0]['arcoakey'];
        }else{ 
            $rsCOA = $coaLink->getCOALink ('ar', $warehouse->tableName,  $warehousekey);   
            $coakey = $rsCOA[0]['coakey'];
        }
        
        return $coakey;
    }
    
    function getDownpaymentCOAKey($customerkey,$warehousekey){ 
        $coaLink = new COALink();
        $warehouse = new Warehouse();
        
        $rsCustomer = $this->getDataRowById($customerkey);
        if (!empty($rsCustomer[0]['downpaymentcoakey'])){  
             $coakey = $rsCustomer[0]['downpaymentcoakey'];
        }else{ 
            $rsCOA = $coaLink->getCOALink ('customerdownpayment', $warehouse->tableName,  $warehousekey);   
            $coakey = $rsCOA[0]['coakey'];
        }
        
        return $coakey;
    }
    
    function normalizeParameter($arrParam, $trim = false){  
        
        if(!empty($arrParam['_mnv-api'])){
            unset($arrParam['password']);  // klo gk di unset, keupdate terus md5nya
        }
        
        //$this->setLog($arrParam,true);
        
        // sementara sampe baseclass bisa mengakomodir
       /* if (!empty($arrParam['fromFE']) || isset($arrParam['_isImport_'])){   
            $this->arrData['pkey'] = array('pkey'); 
        }*/
        
        if (empty($arrParam['password'])){
            unset($this->arrData['password']);
        }else{
            $arrParam['password'] = hash('sha256',md5($arrParam['password']));
        }
        
        // selalu timpa
        $arrParam['activationhashkey'] =  md5($arrParam['pkey'].$arrParam['code'].time().$this->secretKey) ;
            
        
        if(!empty($arrParam['referralCode'])){ 
            $rsCustomer = $this->searchDataRow(array($this->tableName.'.pkey '),
											   'and '.$this->tableName.'.code = ' .$this->oDbCon->paramString( $arrParam['referralCode'])
											  ); 
            $arrParam['hidReferralKey'] = (!empty($rsCustomer)) ? $rsCustomer[0]['pkey'] : 0; 
        }
        
        if (isset($arrParam['chkIsMainAccount']) && $arrParam['chkIsMainAccount'] == 1)
            $arrParam['hidParentKey'] = 0;

        if(isset($arrParam['hidContactPersonDetailKey'])){ 
            for($i=0;$i<count($arrParam['hidContactPersonDetailKey']);$i++) 
                    $arrParam['reftable'][$i] = $this->tableName; 
        }
        
		// overwrite ulang level membership terakhir, kalo edit saja
		if($this->isActiveModule('MembershipSubscription')){
		 
			if (empty($arrParam['hidId'])){
				$membershipLevel = new MembershipLevel();
				$lastLevel = $membershipLevel->getDefaultData();
			}else{
				$arrActivePeriod = $this->calculateActiveSubscriptionPeriod($arrParam['hidId']);  
				$lastLevel = $arrActivePeriod['level'];  
			} 
			$arrParam['selMembership'] = $lastLevel;

		}
		
		 if(isset($arrParam['selBusinessDetailKey'])){
            for($i=0;$i<count($arrParam['selBusinessDetailKey']);$i++) 
                    $arrParam['hidDetailKey'][$i] = '';  // harus di set agar kesave

        }
		
        $arrParam = parent::normalizeParameter($arrParam,true); 
        
        return $arrParam;
        
    }
    function getCustomerFile($pkey){
		$sql = 'select * from '.$this->tableFile.' where refkey = '.$this->oDbCon->paramString($pkey).' order by pkey asc';	
		return $this->oDbCon->doQuery($sql);
    } 
	
	function updateFile($pkey,$token,$arrFile){		
	 /*   if (isset($arrParam['_ignore_']) && $arrParam['_ignore_']['itemFile'])
            return;*/
        
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
        
    function getTaxInformation($pkey){
        
        $rs = $this->getDataRowById($pkey);
        
        if (empty($rs) || $rs[0]['autotax'] == 0)
            $rs[0]['taxpercentage'] = 0;
        else
            $rs[0]['taxpercentage'] = (!empty($rs[0]['taxid'])) ? 2 : 4;
         
             
        return $rs[0];
        
    }
    
    function getCustomerSocialMedia($pkey){
        $sql = 'select
                    '.$this->tableCustomerSocialMedia.'.*,
                    '.$this->tableSocialMedia.'.name as socialmedianame 
                from
                    '.$this->tableCustomerSocialMedia.'
				        join '.$this->tableSocialMedia.' on '.$this->tableCustomerSocialMedia . '.socialkey = '.$this->tableSocialMedia.'.pkey 
                where
                    '.$this->tableCustomerSocialMedia.'.refkey = '.$this->oDbCon->paramString($pkey);
        
        
        $rs = $this->oDbCon->doQuery($sql);
        
        return $rs;
    }
    
    function getCustomersAge($pkey){ 
        $rs = $this->getDataRowById($pkey);
        
        $today = date('Y-m-d');
        $dateOfBirth = $rs[0]['dateofbirth'];
        $diff = date_diff(date_create($dateOfBirth), date_create($today));
        
        $age = $diff->format('%y');

        return $age;
        
    }

    function getSalesman($pkey){
        $employee = new Employee();
        $rs = $this->getDataRowById($pkey); 
        
        $rs = $employee->getDataRowById($rs[0]['saleskey']);
        return (empty($rs)) ? array() : $rs[0];
        
    }
	 
	function calculateActiveSubscriptionPeriod($pkey){
		$membershipSubscription = new MembershipSubscription();
		
		// utk data membership level harus query terpisah
		$rs = $membershipSubscription->searchDataRow(array($this->tableMembershipSubscription.'.membershiplevelkey'),
								  ' and '.$this->tableMembershipSubscription.'.customerkey =  ' .$this->oDbCon->paramString($pkey).' 
								    and '.$this->tableMembershipSubscription.'.statuskey in (2,3)',
								  ' order by '.$this->tableMembershipSubscription.'.trdate desc, '.$this->tableMembershipSubscription.'.pkey desc limit 1'
								  ); 
		$membershipLevelkey = (!empty($rs)) ? $rs[0]['membershiplevelkey'] : 0;
		
		$sql = 'select 
					coalesce( sum('.$this->tableMembershipSubscription.'.activeperiodmonth) ,0) as totalactiveperiod, 
					count('.$this->tableMembershipSubscription.'.pkey) as totalsubscription,
					min('.$this->tableMembershipSubscription.'.trdate) as firstsubscriptiondate
				from '.$this->tableMembershipSubscription.' 
				where 
					'.$this->tableMembershipSubscription.'.customerkey =  ' .$this->oDbCon->paramString($pkey) .' and
					'.$this->tableMembershipSubscription.'.statuskey in (2,3)
				order by trdate desc';
		 
		$rs = $this->oDbCon->doQuery($sql);
		
		return array('activeperiodmonth' => $rs[0]['totalactiveperiod'],
					 'level' => $membershipLevelkey,
					 'totalsubscription' => $rs[0]['totalsubscription'],
					 'firstsubscriptiondate' => $rs[0]['firstsubscriptiondate']
					);
	}
	
	function updateExpDate($pkey){
	
		try{ 
		 
			 if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
 
				$membershipSubscription = new MembershipSubscription();
				$membershipLevel = new MembershipLevel();
			
				$rsCustomer = $this->getDataRowById($pkey);
			
				// ambil semua transaksi karena perlu hitung ulang total brp thn user perpanjang ?
			 	$arrActivePeriod = $this->calculateActiveSubscriptionPeriod($pkey);
 				//$this->setLog($arrActivePeriod,true);
			
				$totalSubscription = $arrActivePeriod['totalsubscription'];
				$totalActivePeriod = $arrActivePeriod['activeperiodmonth'];
				$lastLevel = $arrActivePeriod['level'];
				$firstSubsDate  = $arrActivePeriod['firstsubscriptiondate'];
					
				// cari pernah ad subscription tdk, atau semua dicancel
				if($totalSubscription <=0 ){
 					$expDate = '\'0000-00-00\'';
 					$firstSubsDate = '\'0000-00-00\'';
					$lastLevel = $membershipLevel->getDefaultData(); //reset jd awal
				}else{  
					$date = new DateTime($firstSubsDate); 
					$date->add(new DateInterval('P'.$totalActivePeriod.'M'));    
					$expDate = $this->oDbCon->paramString($date->format('Y-m-d')); 
					$firstSubsDate = $this->oDbCon->paramString($firstSubsDate);
				}
			
				$sql = 'update 
					'.$this->tableName.' 
				set  
					'.$this->tableName.'.expdate = '.$expDate.',
					'.$this->tableName.'.subscriptionactivationdate = '.$firstSubsDate.', 
					'.$this->tableName.'.membershiplevel = '.$this->oDbCon->paramString($lastLevel).'
				where  
					'.$this->tableName.'.pkey = ' . $this->oDbCon->paramString($pkey);  
 				//$this->setLog($sql,true);
				$this->oDbCon->execute($sql);
 
				$this->oDbCon->endTrans();  
            
		} catch(Exception $e){
			$this->oDbCon->rollback(); 
			$this->addErrorList($arrayToJs,false, $e->getMessage()); 
			
		}		 
		
	}
	    
    function getFeaturesDetail($pkey){ 
		
		$membershipLevel = new MembershipLevel();
		
		$rsCustomer = $this->searchDataRow( array($this->tableName.'.pkey', $this->tableName.'.membershiplevel'), 
										   ' and '. $this->tableName.'.pkey = '. $this->oDbCon->paramString($pkey)
										   );
			
		$rsFeaturesDetail = (!empty($rsCustomer[0]['membershiplevel'])) ? $membershipLevel->getFeaturesDetail($rsCustomer[0]['membershiplevel']) : array();
		
		
		// utk ambil detail dan quota terpakai
        $sql = 'select * from ' . $this->tableCustomerFeaturesDetail . ' where refkey = ' . $this->oDbCon->paramString($pkey); 
		$rs = $this->oDbCon->doQuery($sql);
		$rs = array_column($rs,null,'featurekey');
		
		// cocokin dengan $rsFeaturesDetail
		foreach($rsFeaturesDetail as $key=>$row){
			
			if(!isset($rs[$row['featurekey']])){
				$rsFeaturesDetail[$key]['quotaused'] = 0;
				continue;
			}
			
			$arrFeatures = $rs[$row['featurekey']]; 
			$rsFeaturesDetail[$key]['quotaused'] = (!empty($arrFeatures['quotaused'])) ? $arrFeatures['quotaused'] : 0; 
		}
		
		return $rsFeaturesDetail;
    }


    function getCustomerBusinessDetail($pkey){
        $sql = 'select
                    '.$this->tableCustomerBusiness.'.*,
                    '.$this->tableBusinessCategory.'.name as businessname
                from
                    '.$this->tableCustomerBusiness.',
                    '.$this->tableBusinessCategory.'
                where
                    '.$this->tableCustomerBusiness.'.refbusinesskey = '.$this->tableBusinessCategory.'.pkey and
                    '.$this->tableCustomerBusiness.'.refkey = '.$this->oDbCon->paramString($pkey);
         
        $rs = $this->oDbCon->doQuery($sql);
        
        return $rs;
    }
	
  
	 // overwrite karena ad warehouse Criteria
	function generateComboboxOpt($opt = array(),$queryOpt = array(),$preselected='',$relOpt = array()){
		// nanti dilihat perlu isset gk, atau selalu ditambahkan saja
		if(isset($queryOpt['criteria'])) $queryOpt['criteria'] .=  $this->getCustomerCriteria() ; 
		return parent::generateComboboxOpt($opt,$queryOpt,$preselected ,$relOpt );
	}
  }

?>