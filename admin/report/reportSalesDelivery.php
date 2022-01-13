<?php
	  
include '../../_config.php';    
include '../../_include-v2.php';

includeClass('SalesDelivery.class.php'); 
$salesDelivery = createObjAndAddToCol( new SalesDelivery()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$customer = createObjAndAddToCol( new Customer()); 

include '_global.php';

$obj= $salesDelivery;
$securityObject = 'reportSalesDelivery'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 

$_POST['selStatus[]'] = array(2,3);

$arrFilterInformation = array();    
$detailCriteria = '';

$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"100px", 'dbfield' => 'code'); 
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"120px",'format'=>'date');
$arrDataStructure['soCode'] = array('title'=>ucwords($obj->lang['soCode']),  'width'=>"100px", 'dbfield' => 'salesordercode');
$arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"150px");
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"110px");
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"80px");

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['salesDeliveryReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

// detail ...
$arrDataDetailStructure = array(); 
$arrDataDetailStructure['itemCode'] = array('title'=>ucwords($obj->lang['itemCode']),  'dbfield' => 'itemcode', 'width'=>"100px" );  
$arrDataDetailStructure['itemName'] = array('title'=>ucwords($obj->lang['itemName']),  'dbfield' => 'itemname', 'width'=>"250px" );  
$arrDataDetailStructure['orderedQty'] = array('title'=>ucwords($obj->lang['orderedQty']),  'dbfield' => 'orderedqty', 'width'=>"100px" , 'format' => 'number'); 
$arrDataDetailStructure['minQty'] = array('title'=>"Kekurangan",  'dbfield' => 'qtyminus', 'width'=>"100px" , 'format' => 'number'); 
$arrDataDetailStructure['deliveredQty'] = array('title'=>ucwords($obj->lang['deliveredQty']),  'dbfield' => 'deliveredqty', 'width'=>"100px" , 'format' => 'number'); 
$arrDataDetailStructure['unit'] = array('title'=>ucwords($obj->lang['unit']),  'dbfield' => 'unitname', 'width'=>"100px" );  

$arrDetailTemplate = array();
$arrDetailTemplate['reportWidth'] = "750px";
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();

array_push($arrTemplate, $arrDetailTemplate); 




if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';
	if(isset($_POST) && !empty($_POST['code'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['code'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['code']));
	}
	if(isset($_POST) && !empty($_POST['salesCode'])) {
		$criteria .= ' AND '.$obj->tableSalesOrderHeader.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['salesCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode SO', 'filter' => $_POST['salesCode']));
	}
	if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and '.$obj->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	} 
    
	if(isset($_POST) && !empty($_POST['customerName'])) {
		$criteria .= ' AND '.$obj->tableCustomer.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['customerName'].'%').')';
	 	array_push($arrFilterInformation,array("label" => 'Pemasok', 'filter' =>  $_POST['customerName']));
	} 
	 
	if(isset($_POST) && !empty($_POST['itemName'])) { 
        $detailCriteria .= ' AND '.$obj->tableItem.'.name  LIKE ('.$class->oDbCon->paramString('%'.$_POST['itemName'].'%').')';
	    array_push($arrFilterInformation,array("label" => 'Item', 'filter' => $_POST['itemName']));
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
    
		for( $i=0;$i<count($rs);$i++) {   
		      
			$rsDetail = $obj->getDetailWithRelatedInformation($rs[$i]['pkey'],$detailCriteria); 
            if (empty($rsDetail))
                continue;

            $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail); 

        
            $return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate); 

            // ===== FOR EXPORT SECTION 
            array_push($dataToExport, $return['data']);  
            // ===== END FOR EXPORT SECTION

            $tempreport .= $return['html'];
		}
	$obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);	

}
else{ 
	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y');
} 

$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'order by name asc'),'pkey','name');
$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName.'.statuskey',1,true),'pkey','name');   
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');   
   
$arrTwigVar['inputHidItemKey'] =  $class->inputHidden('hidItemKey');
$arrTwigVar['inputCode'] =  $class->inputText('code');
$arrTwigVar['inputSalesCode'] =  $class->inputText('salesCode'); 
$arrTwigVar['inputHidCustomerKey'] =  $class->inputHidden('hidCustomerKey'); 
$arrTwigVar['inputCustomerName'] =  $class->inputText('customerName');
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputItemName'] =  $class->inputText('itemName'); 
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate; 
echo $twig->render('reportSalesDelivery.html', $arrTwigVar);  
 
?>