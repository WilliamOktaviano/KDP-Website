<?php 
class TemplateCustomer extends BaseClass{ 
 
    function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'template_customer_header';
		$this->tableNameDetail = 'template_customer_detail';
        $this->tableStatus = 'master_status';
        $this->tableCustomer = 'customer';
        $this->securityObject = 'Customer';
        
        $this->arrDataDetail = array();  
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['customerkey'] = array('hidCustomerKey'); 
        
        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['trdesc'] = array('trDesc'); 
        $this->arrData['statuskey'] = array('selStatus'); 
        
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70)); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true,'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc', 'width' => 200));
        
        $this->overwriteConfig();

   }
   
    function getQuery(){

        
        return '
			SELECT
                '.$this->tableName.'.*,
                '.$this->tableStatus.'.status as statusname
			FROM 
                '.$this->tableName.',
                '.$this->tableStatus.'
            WHERE
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey   

 		' .$this->criteria ;
		 
    }  
     
    function validateForm($arr,$pkey = ''){ 
        
		$arrayToJs = parent::validateForm($arr,$pkey);
        
        $arrDetail = $arr['hidDetailKey'];
        $arrCustomer = $arr['hidCustomerKey'];
        
        for($i=0;$i<count($arrCustomer);$i++){
            
            if(empty($arrCustomer[$i]))
                $this->addErrorList($arrayToJs,false,$this->errorMsg['customer'][1]);

        }
        
		return $arrayToJs;
	 }

    
    function normalizeParameter($arrParam, $trim=false){ 
        $arrParam = parent::normalizeParameter($arrParam,true);   
    
        return $arrParam;
    }
    
    function getDetailWithRelatedInformation($pkey,$criteria=''){
        
        $sql = 'select
            '.$this->tableNameDetail.'.*,
            '.$this->tableCustomer.'.name as customername
          from
            '.$this->tableNameDetail.',
            '.$this->tableCustomer.'
          where  
            '.$this->tableNameDetail.'.customerkey ='.$this->tableCustomer.'.pkey and
            '. $this->tableNameDetail.'.refkey  = '.$this->oDbCon->paramString($pkey) . ' 
            
            ' ;

        $sql .= $criteria;
         
        return $this->oDbCon->doQuery($sql);

    } 
     
}
?>