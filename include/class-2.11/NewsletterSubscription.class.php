<?php

class NewsletterSubscription extends BaseClass{
	
    function __construct(){

        parent::__construct();

        $this->tableName = 'subscribe';	
        $this->tableStatus = 'master_status';
        $this->securityObject = 'NewsletterSubscription'; 

        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');   
        $this->arrData['code'] = array('code');
        $this->arrData['email'] = array('email');
        $this->arrData['phone'] = array('phone');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');

        $this->newLoad = true;

        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'email','title' => 'email','dbfield' => 'email','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'phone','title' => 'phone','dbfield' => 'phone','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));


        $this->includeClassDependencies(array(
               'Customer.class.php', 
        ));  

        $this->overwriteConfig();
 
    }

    function getQuery(){
        
        $sql = '
            SELECT
                '.$this->tableName.'.* ,  
                '.$this->tableStatus.'.status as statusname
                
            FROM '.$this->tableStatus.',
                 '.$this->tableName.'
            WHERE   
                  '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey

            ' .$this->criteria ;
                                         
        return $sql;
    }

     function validateForm($arr,$pkey = ''){ 
        $arrayToJs = parent::validateForm($arr,$pkey);  
         
	 	if(isset($arr['email']) && !empty($arr['email'])){
            $email = $arr['email'];
  
			if(!filter_var($email, FILTER_VALIDATE_EMAIL)) 
				$this->addErrorList($arrayToJs,false,$this->errorMsg['email'][3]);
			
			$rsCust = $this->isValueExisted($pkey,'email',$email);	
			if(count($rsCust) <> 0) 
				$this->addErrorList($arrayToJs,false,$this->errorMsg['email'][2]);
		}  

        $arrDetailKeys = array(); 

        return $arrayToJs;
    }
 
    function afterUpdateData($arrParam, $action){
      // kalo pake sendinblue
      $smtpAgent = $this->loadSetting('SMTPAgent');
      $folderId = $this->loadSetting('sendInBlueDefaultFolderId'); 
        switch($smtpAgent){
            case 'sendinblue' : 
                    $this->includeClassDependencies(array("SendInBlue.class.php"));
                    $sendinblue = new SendInblue();
                    $sendinblue->createContact($folderId,$arrParam['email']);
                    break;
            default : break;
        }
    }

    
    function normalizeParameter($arrParam, $trim=false){  
        $arrParam = parent::normalizeParameter($arrParam,true);  
        return $arrParam;
    }
 
}

?>