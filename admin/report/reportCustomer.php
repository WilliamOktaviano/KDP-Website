<?php
	 
include '../../_config.php';  
include '../../_include-v2.php'; 

includeClass('Customer.class.php');
$customer = createObjAndAddToCol( new Customer()); 
$customerCategory = createObjAndAddToCol( new CustomerCategory()); 

if($class->isActiveModule('ar'))
    $ar = createObjAndAddToCol( new AR()); 

include '_global.php';

$obj= $customer;
$securityObject = 'reportCustomer'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));  
 
$arrStatus = $obj->getAllStatus();
$arrCategory = $customerCategory->searchData($customerCategory->tableName.'.statuskey',1);

$arrFilterInformation = array();     
 
// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$_POST['module'] = IMPORT_TEMPLATE['customer'];

switch($EXPORT_TYPE){
    case 2 :
            $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),'dbfield' => 'code', 'width'=>"100px");
            $arrDataStructure['name'] = array('title'=>ucwords($obj->lang['name']),'dbfield' => 'name', 'width'=>"200px");
            $arrDataStructure['category'] = array('title'=>ucwords($obj->lang['category']),'dbfield' => 'categoryname', 'width'=>"100px", 'validation' => array_column($arrCategory,'name'));
            $arrDataStructure['address'] = array('title'=>ucwords($obj->lang['address']),'dbfield' => 'address', 'width'=>"250px");
            $arrDataStructure['city'] = array('title'=>ucwords($obj->lang['city']),'dbfield' => 'cityname', 'width'=>"100px");
            // gk perlu kategori, karena akan masalah nanti ketika import API
            //$arrDataStructure['cityCategory'] = array('title'=>ucwords($obj->lang['cityCategory']),'dbfield' => 'citycategoryname', 'width'=>"100px");
            $arrDataStructure['zipcode'] = array('title'=>ucwords($obj->lang['zipcode']),'dbfield' => 'zipcode', 'width'=>"100px"); 
            $arrDataStructure['phone'] = array('title'=>ucwords($obj->lang['phone']),'dbfield' => 'phone', 'width'=>"100px");
            $arrDataStructure['mobile'] = array('title'=>ucwords($obj->lang['mobilePhone']),'dbfield' => 'mobile', 'width'=>"100px");
            $arrDataStructure['fax'] = array('title'=>ucwords($obj->lang['fax']),'dbfield' => 'fax', 'width'=>"100px");
            $arrDataStructure['email'] = array('title'=>ucwords($obj->lang['email']),'dbfield' => 'email', 'width'=>"150px");
            $arrDataStructure['taxid'] = array('title'=>ucwords($obj->lang['taxIdentificationNumber']),'dbfield' => 'taxid', 'width'=>"100px");
            // jgn ditambahkan, gk sesuai dengan API import
            /*$arrDataStructure['currency'] = array('title'=>ucwords($obj->lang['currency']),'dbfield' => 'currencyname', 'width'=>"80px",'align'=>'center');
            $arrDataStructure['top'] = array('title'=>ucwords($obj->lang['termofpayment']),'dbfield' => 'termofpayment', 'width'=>"120px");
            $arrDataStructure['paymentMethod'] = array('title'=>ucwords($obj->lang['paymentTo']),'dbfield' => 'paymentmethodname', 'width'=>"200px"); */
            $arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px", 'validation' => array_column($arrStatus,'status'));

            break;
        
    default :
            $arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
            $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),'dbfield' => 'code', 'width'=>"100px");
            $arrDataStructure['name'] = array('title'=>ucwords($obj->lang['name']),'dbfield' => 'name', 'width'=>"200px");
            $arrDataStructure['categoryname'] = array('title'=>ucwords($obj->lang['category']),'dbfield' => 'categoryname', 'width'=>"200px");
            $arrDataStructure['taxid'] = array('title'=>ucwords($obj->lang['taxIdentificationNumber']),'dbfield' => 'taxid', 'width'=>"200px");
            $arrDataStructure['address'] = array('title'=>ucwords($obj->lang['address']),'dbfield' => 'address', 'width'=>"250px");
            $arrDataStructure['phone'] = array('title'=>ucwords($obj->lang['phone']),'dbfield' => 'phone', 'width'=>"150px");
            $arrDataStructure['email'] = array('title'=>ucwords($obj->lang['email']),'dbfield' => 'email', 'width'=>"180px");
            $arrDataStructure['currency'] = array('title'=>ucwords($obj->lang['currency']),'dbfield' => 'currencyname', 'width'=>"90px",'align'=>'center');
            $arrDataStructure['top'] = array('title'=>ucwords($obj->lang['termofpayment']),'dbfield' => 'termofpayment', 'width'=>"140px");
            $arrDataStructure['paymentMethod'] = array('title'=>ucwords($obj->lang['paymentTo']),'dbfield' => 'paymentmethodname', 'width'=>"200px");
            $arrDataStructure['aroutstanding'] = array('title'=>ucwords($obj->lang['outstanding']),'dbfield' => 'aroutstanding', 'width'=>"100px", 'format'=>'number','calculateTotal' => true);
            $arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
}
  
$arrHeaderTemplate = array();  
$arrHeaderTemplate['reportTitle'] = $obj->lang['customerReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure; 
$arrHeaderTemplate['total'] = array();
 
array_push($arrTemplate, $arrHeaderTemplate);

// ===== END FOR EXPORT SECTION

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
		
		//$arrFilterInformation = $hidRs['arrFilterInformation']; 
		$obj->mknatsort ($rs, $orderBy, ($orderType=='asc')?false:true ,true);
	}
    
    // ============================= GENERATE DATA ============================= 
 
    $tempreport = ''; 
    for( $i=0;$i<count($rs);$i++) {      
    
        $return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate);
        
        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']); 
        // ===== END FOR EXPORT SECTION
        
        $tempreport .= $return['html']; 
         
        // count subtotal for each col
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]); 
         
    }
		 
    $obj->generateReport($_POST, $tempreport, $arrTemplate,array('dataToExport' => $dataToExport,'rs' => $rs),$arrFilterInformation);
}

$arrStatus = $class->convertForCombobox($arrStatus,'pkey','status');   
$arrCategory = $class->convertForCombobox($customerCategory->searchData($customerCategory->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');

$arrTwigVar['importUrl'] = $obj->importUrl; 
$arrTwigVar['inputCustomerCode'] =  $class->inputText('customerCode');  
$arrTwigVar['inputCustomerName'] =  $class->inputText('customerName');   
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelCategory'] =  $class->inputSelect('selCategory[]', $arrCategory, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;   
      
echo $twig->render('reportCustomer.html', $arrTwigVar);  

function queryNewReport($varCol = array(),$order){ 
	foreach($varCol as $key=>$row) $$key = $varCol[$key];
		 
	$arrFilterInformation = array();
	
	$criteria = '';
	if(isset($_POST) && !empty($_POST['customerCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['customerCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['customerCode']));
	}
    
	if(isset($_POST) && !empty($_POST['customerName'])) {
		$criteria .= ' AND '.$obj->tableName.'.name LIKE  ('.$class->oDbCon->paramString('%'.$_POST['customerName'].'%').')'; 
		array_push($arrFilterInformation,array("label" => 'Nama', 'filter' =>  $_POST['customerName']));
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
	 
	if(isset($_POST) && !empty($_POST['selCategory'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCategory']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.categorykey in('.$key.')';  
     
        $rsCriteria = $customerCategory->searchData('','',true, ' and '.$customerCategory->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$categoryName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $class->lang['category'], 'filter' => $categoryName ));
        
	}  
	 
	$order = 'order by '.$order['orderBy'].' ' .$order['orderType']; 
      
	$rs = $obj->searchData('','',true,$criteria,$order);
	 
	$hasARAccess = ($class->isActiveModule('ar')) ? $security->isAdminLogin($ar->securityObject,10) : false;  
	for( $i=0;$i<count($rs);$i++) {    
		  
		$arrCity = array(); 
        if(!empty($rs[$i]['cityname']))  array_push($arrCity,$rs[$i]['cityname']);
        if(!empty($rs[$i]['citycategoryname']))  array_push($arrCity,$rs[$i]['citycategoryname']);

        $city = implode(', ', $arrCity); 
        
        switch($EXPORT_TYPE){
            case 2 : 
               /*
                // jgn pake path lg, diganti API
                $rsPath = $customerCategory->getPath($rs[$i]['categorykey']);
                $rs[$i]['categoryname']  = $rsPath[0]['path']; */
                break;
            default :

                $arrAddress = array();
                if(!empty($rs[$i]['address']))  array_push($arrAddress,$rs[$i]['address']);
                if(!empty($city))  array_push($arrAddress,$city);   
                $rs[$i]['address'] = implode('<br>',$arrAddress); 
                
                $arrPhone = array();
                if(!empty($rs[$i]['phone']))  array_push($arrPhone,$rs[$i]['phone']);
                if(!empty($rs[$i]['mobile']))  array_push($arrPhone,$rs[$i]['mobile']);
                $rs[$i]['phone'] = implode('<br>',$arrPhone); 
        }


        $aroustanding = ($hasARAccess) ? $rs[$i]['aroutstanding'] : 0; 
        $rs[$i]['aroutstanding'] = $aroustanding;  
		 
	 }
	
	return array(
		'arrFilterInformation' => $arrFilterInformation,
		'rs' => $rs
	);
}

?>