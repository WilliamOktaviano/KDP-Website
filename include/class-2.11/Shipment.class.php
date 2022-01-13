<?php

class Shipment extends BaseClass{
   
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'shipment';  
		$this->tableShipmentService = 'shipment_detail';  
        $this->tableSalesOrder = 'sales_order_header';
        $this->tableNameMarketplaceLogistics = 'shipment_marketplace_detail';
		$this->securityObject = 'Shipment'; 
		$this->tableStatus = 'master_status';  
       
       
        $this->JNEUserName = 'ETERNAL';
        $this->JNEAPIKey = '0ef7e748a4a355018a43c5dcc2f7ffe3';
        $this->JNEURL = "http://apiv2.jne.co.id:10101/tracing/api/pricedev";

       
        $this->arrMarketplaceLogistics = array();  
        $this->arrMarketplaceLogistics['pkey'] = array('hidLogisticDetailKey');
        $this->arrMarketplaceLogistics['refkey'] = array('hidDetailKey','ref');   
        $this->arrMarketplaceLogistics['marketplacekey'] = array('hidMarketplaceKey');  
        $this->arrMarketplaceLogistics['marketplacelogisticid'] = array('hidMarketplaceLogisticKey');  
           
        $arrMarketplaceDetails = array(); 
        array_push($arrMarketplaceDetails, array('dataset' => $this->arrMarketplaceLogistics, 'tableName' => $this->tableNameMarketplaceLogistics)); 
        
      
        $this->arrShipmentService = array(); 
        $this->arrShipmentService['pkey'] = array('hidDetailKey', array('dataDetail' =>  $arrMarketplaceDetails));
        $this->arrShipmentService['refkey'] = array('pkey','ref');
        $this->arrShipmentService['servicecode'] = array('serviceCode');
        $this->arrShipmentService['servicename'] = array('serviceName');
       
       
        $arrDetails = array(); 
        array_push($arrDetails, array('dataset' => $this->arrShipmentService, 'tableName' => $this->tableShipmentService)); 
       
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['insurance'] = array('insurance','number');
        $this->arrData['adminfee'] = array('adminFee','number');
        $this->arrData['extcost'] = array('extCost','number');
        $this->arrData['maxweight'] = array('maxWeight','number');
        $this->arrData['minweight'] = array('minWeight','number');
        $this->arrData['url'] = array('url');
        $this->arrData['username'] = array('username');
        $this->arrData['apikey'] = array('apiKey');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['needlocation'] = array('chkDropOffLocation');

       
        $this->arrLockedTable = array();
        $defaultFieldName = 'shipmentkey'; 
        array_push($this->arrLockedTable, array('table'=>'billing_statement_header','field'=>$defaultFieldName));  
        array_push($this->arrLockedTable, array('table'=>'sales_order_car_service_header','field'=>$defaultFieldName));  
        array_push($this->arrLockedTable, array('table'=>'sales_order_header','field'=>$defaultFieldName));  
       
        $this->arrDeleteTable = array(); 
        array_push($this->arrDeleteTable, array('table'=> $this->tableShipmentService,'field' => array('refkey'=>'{id}')));  
   
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'insurance','title' => 'insurance','dbfield' => 'insurance', 'align'=>'right','default'=>true, 'width' => 100, 'format'=>'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'adminCost','title' => 'adminFee','dbfield' => 'adminfee','align'=>'right','default'=>true, 'width' => 100, 'format'=>'number'));
        /*array_push($this->arrDataListAvailableColumn, array('code' => 'extCost','title' => 'extCost','dbfield' => 'extcost','align'=>'right','default'=>true, 'width' => 100, 'format'=>'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'maxWeight','title' => 'maxWeight','dbfield' => 'maxweight','align'=>'right','default'=>true, 'width' => 100, 'format'=>'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'minWeight','title' => 'minWeight','dbfield' => 'minweight','align'=>'right','default'=>true, 'width' => 100, 'format'=>'number'));
        */
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
         
        $this->includeClassDependencies(array( 
                  'City.class.php' ,
                  'Biteship.class.php' ,
                  'Marketplace.class.php' 
         ));  
       
		$this->overwriteConfig();
   }
   
   function getQuery(){
	   
	   return '
			select
				'.$this->tableName. '.*,
				'.$this->tableStatus.'.status as statusname 	
			from 
				'.$this->tableName . ' , '.$this->tableStatus.' 
			where  		 
				'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
 		' .$this->criteria ; 
		 
   }
	
	 function validateForm($arr,$pkey = ''){
		      
		$arrayToJs = parent::validateForm($arr,$pkey); 
          
		$name = $arr['name'];  
		$detailkey = $arr['hidDetailKey'];  
	  
	 	$rs = $this->isValueExisted($pkey,'name',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['shipment'][1]);
		}else if(count($rs) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['shipment'][2]);
		}
		 
        // kalo edit saja
        if(!empty($arr['pkey'])){
             $rsShipmentService = $this->getServices($arr['pkey']); 
         
             foreach($rsShipmentService as $service){
                 $rsSOShipment = $this->getShipmentUsedInSO($service['pkey']);
                 if(!empty($rsSOShipment) && !in_array($service['pkey'],$detailkey)) // cek ad services yg kehapus gk, tp sudah pernah digunakan
                     $this->addErrorList($arrayToJs,false,'<b>'.$service['servicename'].'</b>. '.$this->errorMsg['shipment'][3]);

             }
        }
      
         
		return $arrayToJs;
	 }
    
/*    function getDetailWithRelatedInformation($pkey,$criteria=''){
 
        $sql = 'select
                '.$this->tableName .'.name,
                '.$this->tableShipmentService .'.*
              from
                '.$this->tableName .'
                '.$this->tableShipmentService .'
              where
                '.$this->tableName .'.pkey = '.$this->tableShipmentService .'.refkey and
                refkey = '.$this->oDbCon->paramString($pkey) . ' ';

        $sql .= $criteria;

        return $this->oDbCon->doQuery($sql);

    } */
    
    function getServices($courierkey='',$servicekey = ''){
          $sql = 'select
                '.$this->tableName .'.code,
                '.$this->tableName .'.name,
                '.$this->tableName .'.insurance,
                '.$this->tableShipmentService .'.*
              from
                '.$this->tableName .',
                '.$this->tableShipmentService .'
              where
                '.$this->tableName .'.pkey = '.$this->tableShipmentService .'.refkey ';

        if(!empty($courierkey))
            $sql .= ' and '.$this->tableName .'.pkey in('.$this->oDbCon->paramString($courierkey,',') .')'; 
          
        if(!empty($servicekey))
            $sql .= ' and '.$this->tableShipmentService .'.pkey = '.$this->oDbCon->paramString($servicekey)  ;

        return $this->oDbCon->doQuery($sql);

    }
    
    
    function getAvailableServices($courierkey,$serviceKey = array()){
        $sql = 'select * from '.$this->tableShipmentService.' where refkey = ' . $this->oDbCon->paramString($courierkey);
        
        if(!empty($serviceName))
            $sql .= ' and '.$this->tableShipmentService.'.pkey in ('. $this->oDbCon->paramString($serviceKey,',').')';
        
        //$this->setLog($sql,true);
        return $this->oDbCon->doQuery($sql);
    }
    
    
    function getShipmentUsedInSO($shipmentservicekey){
        $sql = 'select * from '.$this->tableSalesOrder.' where shipmentservicekey = ' . $this->oDbCon->paramString($shipmentservicekey); // gk perlu cek status karena asal sudah ad transaksi, gk boleh dihapus lg
        return $this->oDbCon->doQuery($sql);
    }  
    
    function getShippingInformation($opt){
        
        $city = new City();
        $weight = (!empty($weight)) ? ceil($this->unFormatNumber($weight)) : 0;
        
        $serviceKey = (!empty($opt['serviceKey'])) ? $opt['serviceKey'] : 0;
        $fromCityKey =(!empty($opt['fromCityKey'])) ? $opt['fromCityKey'] : 0;
        $toCityKey = (!empty($opt['toCityKey'])) ? $opt['toCityKey'] : 0;
        $weight = (!empty($opt['weight'])) ? $opt['weight'] : 0;
        $totalValue = (!empty($opt['totalValue'])) ? $opt['totalValue'] : 0;
        $useInsurance =  (!empty($opt['useInsurance'])) ? $opt['useInsurance'] : 0;
        $destination =  (!empty($opt['destination'])) ? $opt['destination'] : array(); 
        $arrItems = (!empty($opt['items'])) ? $opt['items'] : array();
        
        if(empty($fromCityKey)){
            $rsCity = $city->searchDataRow(array($city->tableName.'.pkey'),
                                           'and '.$city->tableName.'.isdefaultshipment = 1'); 
            $fromCityKey = $rsCity[0]['pkey'];
        }
        
        $arrInfo = array(); 
        $courierWithAPI = array('jne','biteship');
        
        // tembak dulu
        $merchant = 'biteship';
        
        $rsCourier = $this->getServices('',$serviceKey);
        $arrCourier = array('courierCode' => $rsCourier[0]['code'], 'serviceCode' =>  $rsCourier[0]['servicecode']);
        
        //$this->setLog($rs,true);
        
        if(in_array($merchant, $courierWithAPI)){
            
            switch(strtolower($merchant)){
                case 'jne' : $arrInfo = $this->getJNEShipment($fromCityKey,$toCityKey,$weight,$serviceKey,$totalValue,$useInsurance);
                             break;
                case 'biteship' :  $biteship = new Biteship();
                                   $arrInfo = $biteship->getRateByLatLng(array(),$destination, $arrCourier, $arrItems);
                                   break;
            }
            
        }
        
        return $arrInfo;
        
    }
    
    
    function getJNEShipment($fromCityKey,$toCityKey,$weight = 1,$serviceKey='',$totalValue=0,$useInsurance=false){
        
        $totalValue = $this->unFormatNumber($totalValue);
        
        $arrReturn = array();
        
        // FROM CITY 
        $city = new City();
        $rsCity = $city->getDataRowById($fromCityKey); 
        $fromCityCode = $rsCity[0]['code'];
            
        
        // TO CITY 
        $rsCity = $city->getDataRowById($toCityKey);        
        $toCityCode = $rsCity[0]['code'];
          
        $curl_post_data = array(
            "username"=> $this->JNEUserName,
            "api_key" => $this->JNEAPIKey,
            'from' => $fromCityCode,
            'thru' => $toCityCode,
            'weight' => $weight
        );
        $fields_string = http_build_query($curl_post_data );

        $curl = curl_init($this->JNEURL);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $fields_string );
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $curl_response = curl_exec($curl);
        curl_close($curl);
         
        $rsJNE = json_decode($curl_response,true); 
        if(!isset($rsJNE['price'])) return $arrReturn;
         
        $rsJNE = array_column($rsJNE['price'],null,'service_code');  
        
        if(!empty($serviceKey)){
            $rsService = $this->getServices('',$serviceKey);
            $serviceCode = $rsService[0]['servicecode'];
            
            if(isset($rsJNE[$serviceCode])){
                $rsJNE[$serviceCode]['insurance'] = ($useInsurance) ? ceil($totalValue * $rsService[0]['insurance'] / 100) : 0;  
                $arrReturn = $rsJNE[$serviceCode];
            }  
        }else{
            $arrReturn = $rsJNE;
        }
          
        //$this->setLog($arrReturn,true);
        return $arrReturn; 
    }
    
   
    function getAllShipment($shipmentkey='',$servicekey=''){
        $sql = 'select
                    '.$this->tableName.'.pkey,
                    '.$this->tableName.'.name,
                    '.$this->tableName.'.needlocation,
                    '.$this->tableShipmentService.'.pkey as servicekey,
                    '.$this->tableShipmentService.'.servicecode,
                    '.$this->tableShipmentService.'.servicename,
                    concat('.$this->tableName.'.name, \' - \' ,'.$this->tableShipmentService.'.servicename) as joinservicename
                from
                    '.$this->tableName .',
                    '.$this->tableShipmentService.'
                where
                    '.$this->tableName .'.pkey = '.$this->tableShipmentService .'.refkey and
                    '.$this->tableName .'.statuskey = 1 
        ';
        
        if (!empty($servicekey))
            $sql .= ' and '.$this->tableShipmentService.'.pkey = ' . $this->oDbCon->paramString($servicekey);
          
        $rs = $this->oDbCon->doQuery($sql); 
        return $rs;
    }
  
    function getAvailableShippingServices($fromCityKey,$toCityKey){ 
        
        $city = new City();
        if(empty($fromCityKey)){
            $rsCity = $city->searchData($city->tableName.'.isdefaultshipment',1); 
            $fromCityKey = $rsCity[0]['pkey'];
        }
        
        
        $rsAllShipment = $this->getAllShipment();
        
        $rsJNE = $this->getJNEShipment($fromCityKey,$toCityKey,1);
        $rsJNE = array_column($rsJNE,'service_code');
        
        foreach($rsAllShipment as $key=>$row){
            if (!in_array($row['servicecode'],$rsJNE))
                unset($rsAllShipment[$key]);
        }
        
        $rsAllShipment = array_values($rsAllShipment);
        
        //$this->setLog($rsAllShipment,true);
        return $rsAllShipment;
    }
	    
   function getMarketplaceLogistics($id,  $criteria = '') {
         
        $marketplace = new Marketplace(); 
       
        $sql =  
            'select   
                '.$this->tableNameMarketplaceLogistics.'.*  
            FROM 
                '.$this->tableName.',
                '.$this->tableShipmentService.',
                '.$this->tableNameMarketplaceLogistics.' 
            WHERE
                '.$this->tableName.'.pkey = '.$this->tableShipmentService.'.refkey and 
                '.$this->tableShipmentService.'.pkey = '.$this->tableNameMarketplaceLogistics.'.refkey and 
                '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($id).'
           ';
         
            
        if (!empty($criteria))  
            $sql .=  ' ' .$criteria;
          
        $rs =  $this->oDbCon->doQuery($sql);
        
         
        $marketplaceLogistics = array();
        $marketplaceObj = $marketplace->getMarketplaceObj();
        foreach($marketplaceObj as $obj)
            $marketplaceLogistics[$obj['key']] = array_column($obj['obj']->getMarketplaceLogistics(),'name','logisticid');
       
       /* 
        $this->setLog('==========',true); 
        $this->setLog($marketplaceLogistics,true);
        $this->setLog('=========',true);
       */
       
        for($i=0;$i<count($rs);$i++)
            $rs[$i]['marketplacelogisticname'] = $marketplaceLogistics[$rs[$i]['marketplacekey']][$rs[$i]['marketplacelogisticid']];
        
        //$this->setLog($rs,true);
       
        return $rs;
    }
    
    function normalizeParameter($arrParam, $trim=false){
         
        $details = array();
        array_push($details,$this->arrMarketplaceLogistics); 
        $arrParam = $this->prepareMultiLevelDetail($arrParam,$details);
         
        $arrParam = parent::normalizeParameter($arrParam,true);
            
        return $arrParam;
    }
    
	function getDetailForAPI($arrKey, $arrIndex = array()){
        if(in_array('services', $arrIndex)){
            $rsDetailsCol = array();
            $rsDetails = $this->getServices($arrKey); 
            $rsDetails = $this->reindexDetailCollections($rsDetails,'refkey'); 
            $rsDetailsCol['services'] = $rsDetails;
        } 
		
        return $rsDetailsCol;
    }
	 
}

?>