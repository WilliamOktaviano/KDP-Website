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

include '_global.php';

$obj= $salesOrder;
$securityObject = 'reportSalesOrder'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 


$_POST['selStatus[]'] = array(2,3);

$arrFilterInformation = array(); 

$dataToExport = array();

/* data structure */
$arrTemplate = array();
$isShowDetail = (isset($_POST['isShowDetail']) && !empty($_POST['isShowDetail'])) ? true : false;

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"150px", 'dbfield' => 'code'); 
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"120px",'format'=>'date');
$arrDataStructure['refcode'] = array('title'=>ucwords($obj->lang['refCode']),  'width'=>"150px", 'dbfield' => 'refcode'); 
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"110px");
$arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"150px");
$arrDataStructure['sales'] = array('title'=>ucwords($obj->lang['salesman']),'dbfield' => 'salesname', 'width'=>"150px");
$arrDataStructure['subtotal'] = array('title'=>ucwords($obj->lang['subtotal']),'dbfield' => 'subtotal','align'=>'right', 'width'=>"100px",'format'=>'integer','calculateTotal' => true); 
$arrDataStructure['finalDiscount'] = array('title'=>ucwords($obj->lang['finalDiscount']),'dbfield' => 'finaldiscount','align'=>'right', 'width'=>"100px",'format'=>'integer','calculateTotal' => true); 
$arrDataStructure['point'] = array('title'=>ucwords($obj->lang['point']),'dbfield' => 'pointvalue','align'=>'right', 'width'=>"100px",'format'=>'integer','calculateTotal' => true); 
$arrDataStructure['tax'] = array('title'=>ucwords($obj->lang['tax']),'dbfield' => 'taxvalue','align'=>'right', 'width'=>"100px",'format'=>'integer','calculateTotal' => true); 
$arrDataStructure['shipmentFee'] = array('title'=>ucwords($obj->lang['shippingFee']),'dbfield' => 'shipmentfee','align'=>'right', 'width'=>"100px",'format'=>'integer','calculateTotal' => true); 
$arrDataStructure['etccost'] = array('title'=>ucwords($obj->lang['etccost']),'dbfield' => 'etccost','align'=>'right', 'width'=>"100px",'format'=>'integer','calculateTotal' => true); 
$arrDataStructure['total'] = array('title'=>ucwords($obj->lang['total']),'dbfield' => 'grandtotal','align'=>'right', 'width'=>"100px",'format'=>'integer','calculateTotal' => true); 
$arrDataStructure['hpp'] = array('title'=>ucwords($obj->lang['cogs']),'dbfield' => 'totalcogs','align'=>'right', 'width'=>"100px",'format'=>'integer','calculateTotal' => true); 
$arrDataStructure['profit'] = array('title'=>ucwords($obj->lang['profit']),'dbfield' => 'profit','align'=>'right', 'width'=>"100px",'format'=>'integer','calculateTotal' => true); 
$arrDataStructure['shipment'] = array('title'=>ucwords($obj->lang['shipment']),'dbfield' => 'shipmentname','width'=>"150px"); 
$arrDataStructure['desc'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trdesc','width'=>"250px"); 
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"80px");

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['salesOrderReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);
if ($isShowDetail){ 
// detail ...
$arrDataDetailStructure = array(); 
$arrDataDetailStructure['itemCode'] = array('title'=>ucwords($obj->lang['itemCode']),  'dbfield' => 'itemcode', 'width'=>"100px" );  
$arrDataDetailStructure['itemName'] = array('title'=>ucwords($obj->lang['itemName']),  'dbfield' => 'itemname', 'width'=>"240px" );  
$arrDataDetailStructure['qty'] = array('title'=>ucwords($obj->lang['qty']),  'dbfield' => 'qty', 'width'=>"60px" , 'format' => 'number'); 
$arrDataDetailStructure['unit'] = array('title'=>ucwords($obj->lang['unit']),  'dbfield' => 'unitname', 'width'=>"100px" ); 
//$arrDataDetailStructure['deliveredQty'] = array('title'=>ucwords($obj->lang['deliveredQty']),  'dbfield' => 'deliveredqty', 'width'=>"180px");
$arrDataDetailStructure['priceInUnit'] = array('title'=>ucwords($obj->lang['price']),'dbfield' => 'priceinunit', 'width'=>"100px",'format'=>'number');
$arrDataDetailStructure['discount'] = array('title'=>ucwords($obj->lang['discount']),'dbfield' => 'discount', 'width'=>"100px",'format'=>'number');
$arrDataDetailStructure['total'] = array('title'=>ucwords($obj->lang['total']),'dbfield' => 'total', 'width'=>"100px",'format'=>'number');
$arrDataDetailStructure['hpp'] = array('title'=>"HPP",'dbfield' => 'costinbaseunit', 'width'=>"100px",'format'=>'number');
$arrDataDetailStructure['profit'] = array('title'=>ucwords($obj->lang['profit'] .' @'),'dbfield' => 'profit', 'width'=>"100px",'format'=>'number');
$arrDataDetailStructure['profitTotal'] = array('title'=>ucwords($obj->lang['subtotal']) . ' '.ucwords($obj->lang['profit']),'dbfield' => 'profittotal', 'width'=>"100px",'format'=>'number');
  
$arrDetailTemplate = array(); 
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();

array_push($arrTemplate, $arrDetailTemplate); 
}



if (isset($_POST) && !empty($_POST['hidAction'])){  
		
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
	
	if(empty($_POST['hidRs'])){ 
    	$result = queryNewReport(get_defined_vars(), array('orderBy' => $orderBy, 'orderType' => $orderType));
		$rs = $result['rs']; 
		$arrFilterInformation = $result['arrFilterInformation'];
	}else{
		$hidRs = json_decode($_POST['hidRs'],true);  
		foreach($hidRs as $key=>$row) $$key = $hidRs[$key];
		$obj->mknatsort ($rs, $orderBy, ($orderType=='asc')?false:true ,true);
	}
    
    $tempreport = ''; 
    $totalRs = count($rs);
    for( $i=0;$i<$totalRs;$i++) {  
        $arrHeaderStyle = array();  
 
        if ($rs[$i]['profit'] < 0)  
            $arrHeaderStyle['profit']['textColor'] = 'C41E3A'; 
        else if ($rs[$i]['profit'] > 0) 
            $arrHeaderStyle['profit']['textColor'] = '568203'; 
        
		$arrHeaderStyle['totalcogs']['textColor'] = '0093AF';
		
        $return = $obj->formatReportRows(array('data' => $rs[$i], 'style' => $arrHeaderStyle ),$arrTemplate); 

        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);

    }
    
	$obj->generateReport($_POST, $tempreport, $arrTemplate,array('dataToExport' => $dataToExport,'rs' => $rs),$arrFilterInformation);

}
else{
   	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y'); 
}

$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrEmployee = $class->convertForCombobox($employee->searchData($employee->tableName.'.statuskey',2,true, ' and '.$employee->tableName.'.issales = 1 '),'pkey','name');   
$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name');
$arrBrand = $class->convertForCombobox($brand->searchData($brand->tableName.'.statuskey',1,true),'pkey','name');      
$arrCity = $class->convertForCombobox($city->searchData($city->tableName.'.statuskey',1,true),'pkey','name');   
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');   
  
 
$arrTwigVar['inputSalesCode'] =  $class->inputText('salesCode');  
$arrTwigVar['inputSalesRefCode'] =  $class->inputText('salesRefCode');   
$arrTwigVar['inputSelCustomer'] =  $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputItemName'] =  $class->inputText('itemName'); 
$arrTwigVar['inputSalesName'] =  $class->inputSelect('selSales[]', $arrEmployee, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputBrandName'] =  $class->inputSelect('selBrand[]', $arrBrand, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"')); 
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelCity'] =  $class->inputSelect('selCity[]', $arrCity, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
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
$arrTwigVar['inputIsShowDetail'] =  $class->inputCheckBox('isShowDetail');
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;
echo $twig->render('reportSalesOrder.html', $arrTwigVar);  
 

function queryNewReport($varCol = array(),$order){ 
	foreach($varCol as $key=>$row) $$key = $varCol[$key];
		 
	$arrFilterInformation = array();
	
	$criteria = '';
	$detailCriteria = '';

	if(isset($_POST) && !empty($_POST['salesCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['salesCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['salesCode']));
	}
	if(isset($_POST) && !empty($_POST['salesRefCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.refcode LIKE ('.$class->oDbCon->paramString('%'.$_POST['salesRefCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode Ref.', 'filter' => $_POST['salesRefCode']));
	}
	if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / ','Y-m-d 23:59'); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
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
    
    
	if(isset($_POST) && !empty($_POST['selCity'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCity']));   
        
       	$criteria .= ' AND '.$obj->tableCustomer.'.citykey in('.$key.')';  

        $rsCriteria = $city->searchData('','',true, ' and '.$city->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$cityName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Lokasi', 'filter' => $cityName ));
        
	}
	
//	kalo diaktifkan, nanti akan bentrok utk perhitungan laba nya, karena di header ad komponen diskon dsb
//	if(isset($_POST) && !empty($_POST['itemName'])) { 
//        $detailCriteria .= ' AND '.$obj->tableItem.'.name  LIKE ('.$class->oDbCon->paramString('%'.$_POST['itemName'].'%').')';
//	    array_push($arrFilterInformation,array("label" => 'Item', 'filter' => $_POST['itemName']));
//	}
//     
//	if(isset($_POST) && !empty($_POST['selBrand'])) { 
//        
//        $key = implode(",", $class->oDbCon->paramString($_POST['selBrand']));   
//        
//        $detailCriteria .=  ' AND '.$obj->tableItem.'.brandkey in('.$key.')';  
//
//        $rsCriteria =  $brand->searchData('','',true, ' and '.$brand->tableName.'.pkey in ('.$key.')');
//	 
//        $arrTempStatus = array();
//		for ($k=0;$k<count($rsCriteria);$k++)
//		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
//			
//		$brandName = implode(", ",$arrTempStatus); 
//	    array_push($arrFilterInformation,array("label" => 'Brand', 'filter' => $brandName));
//        
//	}
    
    if(isset($_POST) && !empty($_POST['selSales'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selSales']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.saleskey in('.$key.')';  

        $rsCriteria =  $employee->searchData('','',true, ' and '.$employee->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$salesName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Sales', 'filter' => $salesName));
        
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

    
	$order = 'order by '.$order['orderBy'].' ' .$order['orderType']; 
     
	$rs = $obj->searchData('','',true,$criteria,$order);
	$rsDetailCol = ($isShowDetail) ? $obj->getDetailCollections($rs,'refkey') : array();
	
	$hasCOGSAccess = $security->isAdminLogin($item->cogsSecurityObject,10);  
	
    $totalRs = count($rs);
    for( $i=0;$i<$totalRs;$i++) {  
        $arrHeaderStyle = array();  

        if(!$hasCOGSAccess) {
			$rs[$i]['totalcogs'] = 0;
			$rs[$i]['profit'] = 0;
		}
    
        $discount = $rs[$i]['finaldiscount'];
        $discountType = $rs[$i]['finaldiscounttype'];
        $subtotal =  $rs[$i]['subtotal'];

        $discountValue = ($discount != 0 && $discountType == 2) ? $discount/100 * $subtotal : $discount;  
        $rs[$i]['finaldiscount']= $discountValue;
  
        if($isShowDetail){ 
         
            if (!isset($rsDetailCol[$rs[$i]['pkey']]))  continue;
            $rsDetail = $rsDetailCol[$rs[$i]['pkey']]; 

			
            $arrDetailStyle = array();
            for($j=0;$j<count($rsDetail);$j++){
 
                if(!$hasCOGSAccess){
                    $rsDetail[$j]['costinbaseunit'] =0;
                    $rsDetail[$j]['profit'] = 0;
                } 
                
                $rsDetail[$j]['profittotal'] = $rsDetail[$j]['qtyinbaseunit'] * $rsDetail[$j]['profit'];
              
                $discount = $rsDetail[$j]['discount'];
                $discountType = $rsDetail[$j]['discounttype'];
                $priceInUnit = $rsDetail[$j]['priceinunit'];

                $discountValue = ($discount != 0 && $discountType == 2) ? $discount/100 * $priceInUnit : $discount;  
                $rsDetail[$j]['discount'] = $discountValue;
				
				if ($rsDetail[$j]['profit'] < 0) { 
                    $arrDetailStyle[$j]['profit']['textColor'] = 'C41E3A';
                    $arrDetailStyle[$j]['profittotal']['textColor'] = 'C41E3A';
                }else if ($rsDetail[$j]['profit'] > 0){ 
                    $arrDetailStyle[$j]['profit']['textColor'] = '568203'; 
                    $arrDetailStyle[$j]['profittotal']['textColor'] = '568203';
                } 
				
            }
			    
            $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail, 'style' => $arrDetailStyle); 
        }
 
    }
	
	
	return array(
		'arrFilterInformation' => $arrFilterInformation, 
		'rs' => $rs
	);
}
?>