<?php

class Biteship extends BaseClass{
   
   function __construct(){
		
		parent::__construct();
        $this->secretkey =  $this->loadSetting('courierGatewayAPIKey');
    
        $this->includeClassDependencies(array(
              'Shipment.class.php',
              'SalesOrder.class.php' ,
              'Customer.class.php' ,
              'Warehouse.class.php' ,
              'Item.class.php',
              'Shipment.class.php' 
        ));
       
   }
   
    
  function getAllCouriers(){
       $url = 'v1/couriers';
       $response = $this->execute($url);
      return $response;
  }
    
    function getRateByLatLng($origin = array(),$destination, $arrCourier, $arrItems){
        
        //jika ad informasi yg kosong, return 0 saja
        //  || empty($destination['latlng'])  // khusus gojek
        if(empty($arrItems) || empty($destination['zipcode'])){
            return array('price' => 0);
        }
        
        $item = new Item();
        
        $url = 'v1/rates/couriers';
        
        if(empty($origin)){
            $warehouse = new Warehouse();
            $rsWarehouse = $warehouse->searchDataRow(array($warehouse->tableName.'.pkey',$warehouse->tableName.'.location',$warehouse->tableName.'.zip'), 
                                                   ' and '.$warehouse->tableName.'.systemVariable = 1'
                                                  );
            
            $zipcode = $rsWarehouse[0]['zipcode'] ;
            $latlng = $rsWarehouse[0]['location'];
            $latlng = explode(',',$latlng);
        }else{
            $latlng = $destination['latlng'];
            $zipcode = $destination['zipcode'];
        }
        
        
        $destinationLatLng = $destination['latlng'];
        $destinationZipcode = $destination['zipcode'];
        
        $payload = array();
        $payload['origin_latitude'] = (!empty($latlng[0])) ? floatval($latlng[0]) : 0;
        $payload['origin_longitude'] = (!empty($latlng[0])) ? floatval($latlng[1]) : 0;
        $payload['origin_postal_code'] = $zipcode;
        $payload['destination_latitude'] = floatval($destinationLatLng['lat']);
        $payload['destination_longitude'] = floatval($destinationLatLng['lng']);
        $payload['destination_postal_code'] = $destinationZipcode;
        
        $payload['couriers'] = $arrCourier['courierCode'];
        
        $arrItemKeys = array_column($arrItems,'itemkey');
        
        $rsItem = $item->searchDataRow(array($item->tableName.'.pkey',$item->tableName.'.name',$item->tableName.'.length',$item->tableName.'.width',$item->tableName.'.height',$item->tableName.'.sellingprice',$item->tableName.'.gramasi',$item->tableName.'.weightunitkey' ),
                                      ' and '.$item->tableName.'.pkey in ('.$this->oDbCon->paramString($arrItemKeys,',').')'
                                      );
        $rsItem = array_column($rsItem,null,'pkey');
        
         
        $payload['items'] = array();
        foreach($arrItems as $row){
            
            $tempItem = $rsItem[$row['itemkey']];
            $weight =  $rsItem[$row['itemkey']]['gramasi'];
            if ($rsItem[$row['itemkey']]['weightunitkey'] == UNIT['kg'])
                $weight *= 1000;
            
            //$weight *= $row['qty']; // weight per item
                
            array_push($payload['items'], array(
                                'name' => $tempItem['name'],
                                'description' => '',
                                'length' => $tempItem['length'],
                                'width' => $tempItem['width'],
                                'height' => $tempItem['height'],
                                'weight' => $weight,
                                'quantity' => $row['qty'],
                                'value' => $tempItem['sellingprice']
                            )); 
        }
          
       $response = $this->execute($url,'POST', $payload);
      
       $selectedService = array();
       foreach($response['pricing'] as $row){ 
           if($row['courier_service_code'] == $arrCourier['serviceCode']){
               $selectedService = $row;
               break;
           }
       }     
        
       return $selectedService;
    }
    
    
    function execute($url,$action='GET',$payload = ''){
        $payload = json_encode($payload); 
        
//        $this->setLog('====== payload =======',true);
//        $this->setLog($payload,true);
        
        $baseurl = 'https://api.biteship.com/';
        $url = $baseurl.$url;
        
        $header = array(
            'Content-Type: application/json',  
            'Authorization: '.$this->secretkey
        );

        $connection = curl_init(); 

        if ($action <> 'GET') 
            curl_setopt($connection, CURLOPT_POSTFIELDS, $payload);

        curl_setopt($connection, CURLOPT_URL, $url); 
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);  
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($connection, CURLOPT_HTTPHEADER,$header); 
        curl_setopt($connection, CURLOPT_CUSTOMREQUEST, $action);

        $response = curl_exec($connection); 
        //$this->setLog($response,true);
        curl_close($connection);   
        return json_decode($response,true);

    }
    
 function placeOrder($orderId){
     
     $url = 'v1/orders';
        
     $salesOrder = new SalesOrder();
     $warehouse = new Warehouse();
     $shipment = new Shipment();
     $item = new Item();
     $setting = new Setting();
     $arr = array();
     
     
     $rsSalesOrder = $salesOrder->getDataRowById($orderId);
     $rsSalesOrderDetail = $salesOrder->getDetailWithRelatedInformation($orderId);
     $rsWarehouse = $warehouse->getDataRowById($rsSalesOrder[0]['warehousekey']);
     $rsShipment = $shipment->getDataRowById($rsSalesOrder[0]['shipmentkey']);
     $rsShipmentService = $shipment->getServices($rsSalesOrder[0]['shipmentkey'],$rsSalesOrder[0]['shipmentservicekey']);
         
     $companyName =  $this->loadSetting('companyName');
     $companyAddress =  $this->loadSetting('companyAddress');
     $companyPhone = $setting->getDetailByCode('companyPhone')[0]['value'];
     $companyEmail = $setting->getDetailByCode('companyEmail');
     $companyEmail = (!empty($companyEmail)) ? $companyEmail[0]['value'] : 'no-reply@'.DOMAIN_NAME;
     
     $arr['shipper_contact_name'] = $companyName;
     $arr['shipper_contact_phone'] = $companyPhone;
     $arr['shipper_contact_email'] = $companyEmail;
     $arr['shipper_organization'] =  $companyName;
     $arr['origin_contact_name'] = $rsWarehouse[0]['name'];
     $arr['origin_contact_phone'] = $rsWarehouse[0]['phone'];
     $arr['origin_address'] = $rsWarehouse[0]['address'];
     $arr['origin_note'] = '';
     $arr['origin_postal_code'] = $rsWarehouse[0]['zip'];
     $loc = explode(',',$rsWarehouse[0]['location']);
     $arr['origin_coordinate'] = array('latitude' => $loc[0], 'longitude' =>$loc[1]);
         
     $arr['destination_contact_name'] = $rsSalesOrder[0]['recipientname'];
     $arr['destination_contact_phone'] = $rsSalesOrder[0]['recipientphone'];
     $arr['destination_contact_email'] = $rsSalesOrder[0]['recipientemail'];
     $arr['destination_address'] = $rsSalesOrder[0]['recipientaddress'];
     $arr['destination_postal_code'] =  $rsSalesOrder[0]['recipientzipcode'];
     $arr['destination_note'] = ''; 
     $loc = explode(',',$rsSalesOrder[0]['recipientlatlng']);
     $arr['destination_coordinate'] =  array('latitude' => $loc[0], 'longitude' =>$loc[1]);
     
//     $arr['destination_cash_on_delivery'] = '';
//     $arr['destination_cash_on_delivery_type'] = '';
     
     $arr['courier_company'] = $rsShipment[0]['code']; 
     $arr['courier_type'] = $rsShipmentService[0]['servicecode']; 
     //$arr['courier_insurance'] = ''; 
     $arr['delivery_type'] = 'now'; 
     $arr['delivery_date'] =  date('Y-m-d'); 
     $arr['delivery_time'] = date('H:i'); 
    
     $arr['order_note'] = $rsSalesOrder[0]['trdesc'];
     $arr['items'] = array();
     
     // ambil ulang dimensi barang
     $arrItemKeys = array_column($rsSalesOrderDetail,'itemkey');
     $rsItem = $item->searchDataRow(array($item->tableName.'.pkey,'.$item->tableName.'.gramasi,'.$item->tableName.'.width,'.$item->tableName.'.length,'.$item->tableName.'.height'),
                                   ' and '.$item->tableName.'.pkey in ('.$this->oDbCon->paramString($arrItemKeys,',').') '
                                   );
     $rsItem = array_column($rsItem,null,'pkey');
     
     foreach($rsSalesOrderDetail as $itemRow){
         
         $item = array();
         
         $arrItem = $rsItem[$itemRow['itemkey']];
         
         $item['id'] = $itemRow['itemkey'];
         $item['name'] =$itemRow['itemname'];
         $item['description'] ='';
         $item['value'] = $itemRow['priceinunit'];
         $item['quantity'] = $itemRow['qtyinbaseunit'];
         $item['height'] = $arrItem['height'];
         $item['length'] = $arrItem['length'];
         $item['weight'] = $arrItem['gramasi'];
         $item['width'] = $arrItem['width'];

         array_push( $arr['items'] , $item);
     }
     
     
     //$this->setLog($arr,true);
     $response = $this->execute($url,'POST', $arr);
      
     //$this->setLog($response,true);
    
 }
    
}

?>
