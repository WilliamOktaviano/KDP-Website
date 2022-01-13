<?php
	 
include '../../_config.php';  
include '../../_include-v2.php';

includeClass('SalesOrder.class.php');
$salesOrder = createObjAndAddToCol( new SalesOrder()); 
$item = createObjAndAddToCol( new Item()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$customer = createObjAndAddToCol( new Customer());
$brand = createObjAndAddToCol( new Brand()); 
$city = createObjAndAddToCol( new City()); 
$shipment = createObjAndAddToCol( new Shipment()); 

include '_global.php';

$obj= $salesOrder;
$securityObject = 'reportSalesOrder'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 
$hasCOGSAccess = $security->isAdminLogin($item->cogsSecurityObject,10);  

$_POST['selStatus[]'] = array(2,3);
$arrDateType= array(
    '1' => $obj->lang['transactionDate'],
    '2' => $obj->lang['confirmedDate'],
);

$arrFilterInformation = array(); 
$detailCriteria = '';

$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"100px", 'dbfield' => 'code'); 
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"120px",'format'=>'date');
$arrDataStructure['refcode'] = array('title'=>ucwords($obj->lang['refCode']),  'width'=>"160px", 'dbfield' => 'refcode'); 
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"110px");
$arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"150px");
$arrDataStructure['shippingInformation'] = array('title'=>ucwords($obj->lang['shippingInformation']),'dbfield' => 'shippinginformation', 'width'=>"250px", "sortable" => false);
$arrDataStructure['shipment'] = array('title'=>ucwords($obj->lang['shipment']),'dbfield' => 'shipmentname', 'width'=>"150px" );
$arrDataStructure['detailItem'] = array('title'=>ucwords($obj->lang['item']),'dbfield' => 'detailitems', 'width'=>"250px","sortable" => false);
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"80px");

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['shipmentManifestReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();


array_push($arrTemplate, $arrHeaderTemplate);



if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';
	if(isset($_POST) && !empty($_POST['salesCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['salesCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['salesCode']));
	}
	if(isset($_POST) && !empty($_POST['salesRefCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.refcode LIKE ('.$class->oDbCon->paramString('%'.$_POST['salesRefCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode Ref.', 'filter' => $_POST['salesRefCode']));
	}
    if(isset($_POST) && !empty($_POST['trStartDate'])){
      switch($_POST['selDateType']){
            case '1' : $fieldName = $obj->tableName.'.trdate';  break;
            case '2' : $fieldName = $obj->tableName.'.confirmedon'; break; 
            default : $fieldName = $obj->tableName.'.trdate';  break;
                
        }
        
		$criteria .= ' and '.$fieldName.' between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / ','Y-m-d 23:59'); 
		array_push($arrFilterInformation,array("label" => $arrDateType[$_POST['selDateType']], 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
    
	 if(isset($_POST) && !empty($_POST['selCustomer'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCustomer']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.customerkey in('.$key.')';  

        $rsCriteria = $customer->searchData('','',true, ' and '.$customer->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Pelangan', 'filter' => $statusName ));
        
	}	
    
    
    if(isset($_POST) && !empty($_POST['selShipment'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selShipment']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.shipmentservicekey in ('.$key.')';

        $rsCriteria = $shipment->searchData('','',true, ' and '.$shipment->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$shipmentName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Pengiriman', 'filter' => $shipmentName ));
        
	}
	 
 
	if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.warehousekey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$warehouseName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Gudang', 'filter' => $warehouseName ));
        
	}
	
	if(isset($_POST) && !empty($_POST['selStatus'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selStatus']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.statuskey in('.$key.')';  

        $rsCriteria =  $obj->getStatusById ($key);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['status']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Status', 'filter' => $statusName));
        
	}

    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
    
    
		   
	$order = 'order by '.$orderBy.' ' .$orderType; 
	$rs = $obj->searchData('','',true,$criteria,$order);
		
    $tempreport = '';

    if (empty($rs)) 
        $tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';
         
    $rsDetailCol = $obj->getDetailCollections($rs,'refkey',$detailCriteria) ;
    
    $totalRs = count($rs);
    $rsCity =  $city->searchDataRow(array($city->tableName.'.pkey',$city->tableName.'.name'),
                                    ' and '.$city->tableName.'.pkey in ('.$class->oDbCon->paramString(array_column($rs,'recipientcitykey'),',').')'
                                   );
    $rsCity = array_column($rsCity,'name','pkey');
    
    
    for( $i=0;$i<$totalRs;$i++) {  
        $arrHeaderStyle = array(); 
        
        $cityname = (isset($rsCity[$rs[$i]['recipientcitykey']])) ? ', '.$rsCity[$rs[$i]['recipientcitykey']] : '';
        
        $arrShipping = array();
        if(!empty($rs[$i]['recipientname'])) array_push($arrShipping, $rs[$i]['recipientname']) ;
        if(!empty($rs[$i]['recipientphone'])) array_push($arrShipping, $rs[$i]['recipientphone']) ;
        if(!empty($rs[$i]['recipientaddress']))array_push($arrShipping, $rs[$i]['recipientaddress'].''.$cityname) ;
        
        $rs[$i]['shippinginformation'] = implode(' <br>', $arrShipping);
        
        
        if (!isset($rsDetailCol[$rs[$i]['pkey']]))  continue;
        $rsDetail = $rsDetailCol[$rs[$i]['pkey']]; 

        $arrItem = array();
        for($j=0;$j<count($rsDetail);$j++){

            $qty = $obj->formatNumber($rsDetail[$j]['qty']);

            $itemname = $qty.' '.$rsDetail[$j]['unitname'] .' '. $rsDetail[$j]['itemname'];
            array_push($arrItem,$itemname);

        }

        $rs[$i]['detailitems'] = implode('<br>',$arrItem) ;
        
        $return = $obj->formatReportRows(array('data' => $rs[$i], 'style' => $arrHeaderStyle ),$arrTemplate); 

        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);

    }
    

    $footnote = '';
    
	$obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation,'', $footnote);

}
else{
   	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y'); 
}

$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrEmployee = $class->convertForCombobox($employee->searchData($employee->tableName.'.statuskey',2,true),'pkey','name');   
$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name');
$arrBrand = $class->convertForCombobox($brand->searchData($brand->tableName.'.statuskey',1,true),'pkey','name');      
$arrCity = $class->convertForCombobox($city->searchData($city->tableName.'.statuskey',1,true),'pkey','name');   
$arrShipment = $class->convertForCombobox($shipment->getAllShipment(),'servicekey','joinservicename');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');   
  
$arrTwigVar['inputSelDateType'] =  $class->inputSelect('selDateType', $arrDateType);  
$arrTwigVar['inputSalesCode'] =  $class->inputText('salesCode');  
$arrTwigVar['inputSalesRefCode'] =  $class->inputText('salesRefCode');   
$arrTwigVar['inputSelCustomer'] =  $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputItemName'] =  $class->inputText('itemName'); 
$arrTwigVar['inputBrandName'] =  $class->inputSelect('selBrand[]', $arrBrand, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"')); 
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelCity'] =  $class->inputSelect('selCity[]', $arrCity, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelShipment'] =  $class->inputSelect('selShipment[]', $arrShipment, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputTemplateCustomer'] = $class->inputAutoComplete(array(   
                                                                        'element' => array('value' => 'selTemplateCustomer',
                                                                                           'key' => 'hidTemplateCustomerKey'),
                                                                        'source' => array(
                                                                                            'url' => '../ajax-template-customer.php',
                                                                                            'data' => array(  'action' =>'searchData')
                                                                                        ), 
                                                                        'placeholder' => $obj->lang['searchTemplate'].'...',
                                                                        'callbackFunction' => 'updateCustomer(this)' 
                                                                      )); 
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;
echo $twig->render('reportSalesOrderForShipment.html', $arrTwigVar);  
 
?>