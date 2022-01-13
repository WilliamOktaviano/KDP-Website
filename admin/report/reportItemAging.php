<?php  
include '../../_config.php';  
include '../../_include-v2.php'; 

includeClass('Item.class.php');
$item = createObjAndAddToCol(new Item());
$warehouse = createObjAndAddToCol(new Warehouse());
$itemCategory = createObjAndAddToCol(new ItemCategory());
$brand = createObjAndAddToCol(new Brand());
$itemMovement = createObjAndAddToCol(new ItemMovement());

include '_global.php';
 
$obj= $item;
$securityObject = 'reportItem'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
$_POST['selStatus[]'] = array(1);

//$hasCOGSAccess = $security->isAdminLogin($item->cogsSecurityObject,10);  
 
$arrFilterInformation = array();
$detailCriteria = '';  

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();
$selWarehouse = array();


$arrDataStructure = array();
$_POST['module'] = IMPORT_TEMPLATE['item'];

$arrDate = (isset($_POST['trDate'])) ? $_POST['trDate'] : array();
    
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"170px", 'dbfield' => 'code');
$arrDataStructure['name'] = array('title'=>ucwords($obj->lang['name']),  'width'=>"300px", 'dbfield' => 'name');

foreach ($arrDate as $dt) {   
    $dtField = $obj->oDbCon->paramDate($dt,' / '); 
    $arrDataStructure[$dt] = array('title'=>$dt,  'width'=>"100px",'align'=>'center', 'sortable' => false, 'dbfield' =>'aging'.$dtField); 
}

$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['itemAgingReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');  
$arrCategory = $class->convertForCombobox($itemCategory->searchData($itemCategory->tableName.'.statuskey',1,true, ' and '.$itemCategory->tableName.'.isleaf = 1', ' order by name asc'),'pkey','name');   
$arrBrand = $class->convertForCombobox($brand->searchData('','',true, ' and '.$brand->tableName.'.statuskey = 1','order by name asc'),'pkey','name'); 

$arrTwigVar['inputItemCode'] =  $class->inputText('itemCode');   
$arrTwigVar['inputItemName'] =  $class->inputText('itemName');

for($i=0;$i<2;$i++){
    // perlu dibenerin nanti
    $_POST['trDate[]'] = ((isset($arrDate[$i]))) ?  $arrDate[$i] : date('d / m / Y'); 
    $arrTwigVar['inputDate'][$i] = $class->inputDate('trDate[]', array('etc' => 'style="text-align:center"'));  
}

$arrTwigVar['inputSelCategory'] =  $class->inputSelect('selCategory[]', $arrCategory, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelBrand'] =  $class->inputSelect('selBrand[]', $arrBrand, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
//$arrTwigVar['inputChkQOH'] =  $class->inputCheckBox('chkQOH',array('overwritePost' => false, 'value' => 1, 'class' => 'no-class'));  
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;   
	
if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';
	if(isset($_POST) && !empty($_POST['itemCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['itemCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['itemCode']));
	}
	if(isset($_POST) && !empty($_POST['itemName'])) {
		$criteria .= ' AND '.$obj->tableName.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['itemName'].'%').')'; 
		array_push($arrFilterInformation,array("label" => 'Nama', 'filter' => $_POST['itemName']));
	}
    
	if(isset($_POST) && !empty($_POST['selCategory'])) { 
         
        $key = implode(",", $class->oDbCon->paramString($_POST['selCategory']));   
        
        $criteria .= ' AND categorykey in('.$key.')';  

        $rsCriteria = $itemCategory->searchData('','',true, ' and '.$itemCategory->tableName.'.pkey in ('.$key.')');
	 
        $arrTempCategory = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempCategory,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempCategory); 
	    array_push($arrFilterInformation,array("label" => 'Kategori', 'filter' => $statusName));
	}
    
    
    if(isset($_POST) && !empty($_POST['selBrand'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selBrand']));   
        
       	$criteria .= ' AND '.$obj->tableBrand.'.pkey in('.$key.')';  

        $rsCriteria = $brand->searchData('','',true, ' and '.$brand->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Merk', 'filter' => $statusName));
        
	}
    
    
	if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $selWarehouse = $_POST['selWarehouse'];
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
       	$warehouseCriteria = ' AND warehousekey in('.$key.')';  // gk tau kepake gk ??

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Gudang', 'filter' => $statusName ));
            
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
    
/*    $groupCriteria = ''; 
	if(isset($_POST) && !empty($_POST['chkAvailable'])){ 
        $groupCriteria = 'having qtyonhand > 0';
    }*/
      
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
     
	$order = 'order by '.$orderBy.' ' .$orderType; 
    
    //$qohOnly =  (isset($_POST['chkQOH']) && !empty($_POST['chkQOH'])) ? true : false; 
	$rs = $obj->searchDataAgingReport('','',true,$criteria,$order); 
         
    $temp = 1;
    $tempreport = ''; 

    if (empty($rs))
        $tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';

    for($i=0;$i<count($rs);$i++) {   
        
        foreach ($arrDate as $dt) {  
             $dtField = $obj->oDbCon->paramDate($dt,' / ');  
            
             $rsAgingIndex = $itemMovement->getItemAging($rs[$i]['pkey'],$selWarehouse,$dt); 
             $rs[$i]['aging'.$dtField] = $rsAgingIndex['maxaging'];
        }   


        $return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate);  
        array_push($dataToExport, $return['data']);   
        $tempreport .= $return['html'];
        
    }

    $tableHeader = $twig->render('template-header.html', $arrTwigVar);
    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation,$tableHeader);
}
 
echo $twig->render('reportItemAging.html', $arrTwigVar);  
 
?>