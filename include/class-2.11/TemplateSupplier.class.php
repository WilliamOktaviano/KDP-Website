<?php 
class TemplateSupplier extends BaseClass{ 
 
    function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'template_supplier_header';
		$this->tableNameDetail = 'template_supplier_detail';
        $this->tableStatus = 'master_status';
        $this->tableSupplier = 'supplier';
        $this->securityObject = 'Supplier';
        
        $this->arrDataDetail = array();  
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['supplierkey'] = array('hidSupplierKey'); 
        
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

        
        $sql = '
			SELECT
                '.$this->tableName.'.*,
                '.$this->tableStatus.'.status as statusname
			FROM 
                '.$this->tableName.',
                '.$this->tableStatus.'
            WHERE
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey   

 		' .$this->criteria ;
//		$this->setLog($sql,true); 
		return $sql;
    }  
     
    function validateForm($arr,$pkey = ''){ 
        
		$arrayToJs = parent::validateForm($arr,$pkey);
        
        $arrDetail = $arr['hidDetailKey'];
        $arrSupplier = $arr['hidSupplierKey'];
        
        for($i=0;$i<count($arrSupplier);$i++){
            
            if(empty($arrSupplier[$i]))
                $this->addErrorList($arrayToJs,false,$this->errorMsg['supplier'][1]);

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
            '.$this->tableSupplier.'.name as suppliername
          from
            '.$this->tableNameDetail.',
            '.$this->tableSupplier.'
          where  
            '.$this->tableNameDetail.'.supplierkey ='.$this->tableSupplier.'.pkey and
            '. $this->tableNameDetail.'.refkey  = '.$this->oDbCon->paramString($pkey) . ' 
            
            ' ;

        $sql .= $criteria; 
        return $this->oDbCon->doQuery($sql);

    } 
    
}
?>
