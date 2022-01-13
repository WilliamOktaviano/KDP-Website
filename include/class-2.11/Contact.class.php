<?php

class Contact extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'contact_us';   
		$this->tableCategory = 'contact_category';   
		$this->securityObject = 'Contact'; 
		$this->tableStatus = 'master_status';
       
   	    $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey'); 
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['phone'] = array('phone');
        $this->arrData['mobile'] = array('mobile');
        $this->arrData['email'] = array('email');
        $this->arrData['message'] = array('message'); 
        $this->arrData['categorykey'] = array('selCategory');
        $this->arrData['statuskey'] = array('selStatus');
	   
	      
	   	$this->newLoad=true;
	   
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'category','title' => 'category','dbfield' => 'categoryname',  'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'createdon', 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name', 'width' => 100));
      	array_push($this->arrDataListAvailableColumn, array('code' => 'phone','title' => 'phone','dbfield' => 'phone', 'width' => 100));
      	array_push($this->arrDataListAvailableColumn, array('code' => 'email','title' => 'email','dbfield' => 'email', 'width' => 100));
      	array_push($this->arrDataListAvailableColumn, array('code' => 'statusname','title' => 'status','dbfield' => 'statusname', 'width' => 100)); 
	   
	   
   }
   
    
	 function getQuery(){
	   
	   return '
				select
					'.$this->tableName. '.*,
					'.$this->tableCategory.'.name as categoryname, 
					'.$this->tableStatus.'.status as statusname 
				from 
					'.$this->tableName . '
							left join '.$this->tableCategory.' on '.$this->tableName . '.categorykey = '.$this->tableCategory.'.pkey,
					'.$this->tableStatus.'  
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
 		' .$this->criteria ; 
		 
    }
	 
	
	function editData($arrParam){    
	    return ''; 
	}  
	
	 function validateForm($arr,$pkey = ''){
		    
		$arrayToJs = parent::validateForm($arr,$pkey); 
         
		$name = $arr['name'];  
		$message = $arr['message'];  
		$email = $arr['email'];   
		$phone = $arr['phone'];   
		$subject = $arr['subject'];  
	     
        if (!IS_DEVELOPMENT){ 
            $captchaResponse = $arr['g-recaptcha-response'];  
            $secretkey = $this->loadSetting('reCaptchaSecretKey');

            // post request to server
            $url = 'https://www.google.com/recaptcha/api/siteverify';
            $data = array('secret' => $secretkey, 'response' => $captchaResponse);

            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($data)
                    )
            );

            $context  = stream_context_create($options);
            $response = file_get_contents($url, false, $context);
            $responseKeys = json_decode($response,true);

            if(empty($responseKeys) || !$responseKeys["success"]) {
              $this->addErrorList($arrayToJs,false,$this->errorMsg['captcha'][1]);
            }
         
        }
         
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][1]);
		}  
		if(empty($email)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['email'][1]);
		} else{
			if(!filter_var($email, FILTER_VALIDATE_EMAIL)) 
					$this->addErrorList($arrayToJs,false,$this->errorMsg['email'][3]);	
		}
         /*
		if(empty($phone)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['phone'][1]);
		} 
		if(empty($subject)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['subject'][1]);
		}  */
         
		if(empty($message)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['message'][1]);
		}  
		
		return $arrayToJs;
	 }
    
    function afterUpdateData($arrParam, $action){
        $setting = new Setting();
        $companyEmail = $setting->getDetailByCode('companyEmail');
        if (empty($companyEmail))
            return;
        
        $companyEmail = $companyEmail[0]['value'];
        $this->sendMail(array('name'=>$arrParam['name'],'email' => $arrParam['email']),$this->lang['contactUs'] .' - ' . $this->domain,$arrParam['message'],array('name' => 'webmaster' ,'email'=>$companyEmail));
    }
    
 
    function normalizeParameter($arrParam, $trim=false){
        
        $arrParam['selStatus'] = (isset($arrParam['selStatus'])) ? $arrParam['selStatus'] : '1'; 
        $arrParam['phone'] = (isset($arrParam['phone'])) ? $arrParam['phone'] : ''; 
        $arrParam['mobile'] = (isset($arrParam['mobile'])) ? $arrParam['mobile'] : ''; 
        $arrParam['selCategoryKey'] = (isset($arrParam['selCategory'])) ? $arrParam['selCategory'] : 0;
        $arrParam['subject'] = (isset($arrParam['subject'])) ? $arrParam['subject'] : ''; 
            
            
        return $arrParam;
    }}

?>
