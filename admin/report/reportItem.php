<?php  
include '../../_config.php';  
include '../../_include-v2.php'; 
  
includeClass('Item.class.php');
$item = createObjAndAddToCol( new Item());

$warehouse = createObjAndAddToCol(new Warehouse());
$itemUnit = createObjAndAddToCol(new ItemUnit());
$itemCategory = createObjAndAddToCol(new ItemCategory());
$itemMovement = createObjAndAddToCol(new ItemMovement());
$brand = createObjAndAddToCol(new Brand());

include '_global.php';

$obj= $item;
$securityObject = 'reportItem'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
$_POST['selStatus[]'] = array(1);

$hasCOGSAccess = $security->isAdminLogin($item->cogsSecurityObject,10);  

$arrFilterInformation = array();
$detailCriteria = ''; 
$warehouseCriteria = '';

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

// kalo utk template 
$arrItemType = array();
$arrItemType['1'] = 'Barang';
$arrItemType['2'] = 'Jasa';
define('ITEM_TYPE',$arrItemType); 

$arrDataStructure = array();
$_POST['module'] = IMPORT_TEMPLATE['item'];
 
$selWarehouse = array();

$arrStatus = $obj->getAllStatus();
$arrCategory = $itemCategory->searchData($itemCategory->tableName.'.statuskey',1,true, ' and '.$itemCategory->tableName.'.isleaf = 1', ' order by name asc');
$arrWeightUnit = $obj->getSystemWeight();
$arrUnit = $itemUnit->searchData($itemUnit->tableName.'.statuskey',1);
$arrBrand = $brand->searchData('','',true, ' and '.$brand->tableName.'.statuskey = 1','order by name asc');

switch($EXPORT_TYPE){
    case 2 :

            $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"100px", 'dbfield' => 'code');
            $arrDataStructure['name'] = array('title'=>ucwords($obj->lang['name']),  'width'=>"250px", 'dbfield' => 'name');
            //$arrDataStructure['itemType'] = array('title'=>ucwords($obj->lang['type']),  'width'=>"250px", 'dbfield' => 'itemtypename');
            $arrDataStructure['itemCategory'] = array('title'=>ucwords($obj->lang['itemCategory']),  'width'=>"200px", 'dbfield' => 'categoryname', 'validation' => array_column($arrCategory,'name'));
            $arrDataStructure['brand'] = array('title'=>ucwords($obj->lang['brand']),  'width'=>"140px", 'dbfield' => 'brandname', 'validation' => array_column($arrBrand,'name'));
            $arrDataStructure['condition'] = array('title'=>ucwords($obj->lang['itemCondition']),  'width'=>"100px", 'dbfield' => 'conditionname', 'validation' => array('Baru','Pernah Digunakan'));
            $arrDataStructure['gramation'] = array('title'=>ucwords($obj->lang['weight']),'dbfield' => 'gramasi', 'width'=>"80px" ,'format'=>'number');
            $arrDataStructure['weightunitname'] = array('title'=>ucwords($obj->lang['unit']),'dbfield' => 'weightunitname', 'width'=>"75px", 'validation' => array_column($arrWeightUnit,'name'));
            //$arrDataStructure['qoh'] = array('title'=>ucwords($obj->lang['qoh']),'dbfield' => 'qtyonhand', 'width'=>"75px" ,'format'=>'number');
            $arrDataStructure['unitname'] = array('title'=>ucwords($obj->lang['baseunit']),'dbfield' => 'baseunitname', 'width'=>"75px", 'validation' => array_column($arrUnit,'name'));
            $arrDataStructure['minstock'] = array('title'=>ucwords($obj->lang['minStock']),'dbfield' => 'minstockqty', 'width'=>"75px");
            $arrDataStructure['maxstock'] = array('title'=>ucwords($obj->lang['maxStock']),'dbfield' => 'maxstockqty', 'width'=>"75px");
            //$arrDataStructure['aging'] = array('title'=>ucwords($obj->lang['aging']) . ' ('.ucwords($obj->lang['day(s)']).')','dbfield' => 'maxaging', 'align' => 'right', 'format' => 'integer','sortable' => false, 'width'=>"75px");
            //$arrDataStructure['cogs'] = array('title'=>ucwords($obj->lang['cogs']),'dbfield' => 'cogs', 'width'=>"95px" ,'format'=>'number');
            $arrDataStructure['sellingPrice'] = array('title'=>ucwords($obj->lang['sellingPrice']),'dbfield' => 'sellingprice', 'width'=>"90px" ,'format'=>'number');
            $arrDataStructure['shortDescription'] = array('title'=>ucwords($obj->lang['shortDescription']),'dbfield' => 'shortdescription', 'width'=>"200px");
            //$arrDataStructure['url'] = array('title'=>ucwords($obj->lang['imageLink']),'dbfield' => 'imageslink', 'width'=>"100px");
            $arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px", 'validation' => array_column($arrStatus,'status'));

            break;
        
    default :
            $arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
            $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"100px", 'dbfield' => 'code');
            $arrDataStructure['name'] = array('title'=>ucwords($obj->lang['name']),  'width'=>"250px", 'dbfield' => 'name');
            $arrDataStructure['itemCategory'] = array('title'=>ucwords($obj->lang['itemCategory']),  'width'=>"200px", 'dbfield' => 'categoryname');
            $arrDataStructure['brand'] = array('title'=>ucwords($obj->lang['brand']),  'width'=>"140px", 'dbfield' => 'brandname');
            $arrDataStructure['gramation'] = array('title'=>ucwords($obj->lang['weight']),'dbfield' => 'gramasi', 'width'=>"80px" ,'format'=>'number');
            $arrDataStructure['weightunitname'] = array('title'=>ucwords($obj->lang['unit']),'dbfield' => 'weightunitname', 'width'=>"75px");
            $arrDataStructure['qoh'] = array('title'=>ucwords($obj->lang['qty']),'dbfield' => 'qtyonhand', 'width'=>"75px" ,'format'=>'number', 'calculateTotal' => true);
            $arrDataStructure['unitname'] = array('title'=>ucwords($obj->lang['unit']),'dbfield' => 'baseunitname', 'width'=>"75px");
            $arrDataStructure['aging'] = array('title'=>ucwords($obj->lang['aging']) . ' ('.ucwords($obj->lang['day(s)']).')','dbfield' => 'maxaging', 'align' => 'right', 'format' => 'integer','sortable' => false, 'width'=>"75px");
            $arrDataStructure['cogs'] = array('title'=>ucwords($obj->lang['cogs']),'dbfield' => 'cogs', 'width'=>"95px" ,'format'=>'number');
            $arrDataStructure['totalcogs'] = array('title'=>ucwords($obj->lang['totalCOGS']), 'dbfield' => 'totalcogs', 'width'=>"95px" ,'format'=>'number', "sortable" => false, 'calculateTotal' => true);
            $arrDataStructure['sellingPrice'] = array('title'=>ucwords($obj->lang['sellingPrice']),'dbfield' => 'sellingprice', 'width'=>"90px" ,'format'=>'number');
            $arrDataStructure['shortDescription'] = array('title'=>ucwords($obj->lang['shortDescription']),'dbfield' => 'shortdescription', 'width'=>"200px");
          	$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");

}
  
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['itemReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

	
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
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
        $selWarehouse = $_POST['selWarehouse'];
        
       	$warehouseCriteria = ' AND warehousekey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Gudang', 'filter' => $statusName ));
        
	}	
    
    if (PLAN_TYPE['usefrontend'] == 1){
        if(isset($_POST) && !empty($_POST['selFilter'])) { 

            $key = implode(",", $class->oDbCon->paramString($_POST['selFilter']));     

            $rsFilterCategory = $filterCategory->searchData($filterCategory->tableName.'.statuskey',1);

            $arrIntersect = array();
            for ($i=0;$i<count($rsFilterCategory);$i++){
                $rsFilter = $itemFilter->searchData($itemFilter->tableName.'.statuskey',1,true, ' and categorykey = ' . $obj->oDbCon->paramString($rsFilterCategory[$i]['pkey']));
                $rsFilterKey = array_column($rsFilter, 'pkey');
                $rsIntersectFilterKey = array_intersect($rsFilterKey,$_POST['selFilter']);

                if(empty($rsIntersectFilterKey))
                    continue;

                $rsFilteredItem = $itemFilter->getItemInFilter($rsIntersectFilterKey); 
                $rsFilteredItemKey = array_column($rsFilteredItem, 'itemkey'); 


                if (empty($arrIntersect)) 
                    $arrIntersect = $rsFilteredItemKey;
                 else 
                    $arrIntersect = array_intersect($arrIntersect,$rsFilteredItemKey);

            }

            $criteria .=  ' and '.$obj->tableName.'.pkey in ('.implode(',',$arrIntersect).')'; 

            $rsCriteria = $itemFilter->searchData('','',true, ' and '.$itemFilter->tableName.'.pkey in ('.$key.')');

            $arrTempStatus = array();
            for ($k=0;$k<count($rsCriteria);$k++)
                array_push($arrTempStatus,$rsCriteria[$k]['name']);

            $statusName = implode(", ",$arrTempStatus); 
            array_push($arrFilterInformation,array("label" => 'Filter', 'filter' => $statusName ));

        }	
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
    
    $groupCriteria = ''; 
	if(isset($_POST) && !empty($_POST['chkAvailable'])){ 
        $groupCriteria = 'having qtyonhand > 0';
    }
      
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
    
	 
	$order = 'order by '.$orderBy.' ' .$orderType; 
    
    $qohOnly =  (isset($_POST['chkQOH']) && !empty($_POST['chkQOH'])) ? true : false; 
	$rs = $obj->searchData('','',true,$criteria,$order,'',$groupCriteria , $warehouseCriteria, $qohOnly); 
         
    $temp = 1;
    $tempreport = ''; 

    if (empty($rs))
        $tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';

    for( $i=0;$i<count($rs);$i++) {   

        if(!$hasCOGSAccess) 
            $rs[$i]['cogs']  = 0;  

        $rs[$i]['totalcogs'] = $rs[$i]['qtyonhand'] * $rs[$i]['cogs'];

        $rs[$i]['itemtypename'] = ITEM_TYPE[$rs[$i]['itemtype']];
        
        $rsAging = $itemMovement->getItemAging($rs[$i]['pkey'],$selWarehouse);
        $rs[$i]['maxaging'] = $rsAging['maxaging']; 
                      
        switch($EXPORT_TYPE){
            case 2 : 
                $rsPath = $itemCategory->getPath($rs[$i]['categorykey']);
                $rs[$i]['categoryname']  = $rsPath[0]['path'];
                break;
        }
            
        // has detail
        //$rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail); 

        $return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate); 

        array_push($dataToExport, $return['data']);  

        $tempreport .= $return['html'];
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);
    }

    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);
}

$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrStatus = $class->convertForCombobox($arrStatus,'pkey','status');  
$arrCategory = $class->convertForCombobox($arrCategory,'pkey','name');   
$arrBrand = $class->convertForCombobox($arrBrand,'pkey','name'); 

 
/*if (PLAN_TYPE['usefrontend'] == 1){
    $arrFilter = array();
    $rsItemFilterCategory = $filterCategory->searchData($filterCategory->tableName.'.statuskey','1',true);

    for($i=0;$i<count($rsItemFilterCategory);$i++){
         $rsFilter = $itemFilter->searchData('categorykey',$rsItemFilterCategory[$i]['pkey'],true, ' and '.$itemFilter->tableName.'.statuskey = 1'); 
         $arrFilter[$rsItemFilterCategory[$i]['name']] = $class->convertForCombobox($rsFilter,'pkey','name') ;  
    }  
    
    $arrTwigVar['inputSelFilter'] =  $class->inputSelect('selFilter[]', $arrFilter , array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
}*/


$arrTwigVar['importUrl'] = $obj->importUrl; 

$arrTwigVar['inputItemCode'] =  $class->inputText('itemCode');  
$arrTwigVar['inputHidItemKey'] = $class->inputHidden('hidItemKey');
$arrTwigVar['inputItemName'] =  $class->inputText('itemName');

$arrTwigVar['inputSelCategory'] =  $class->inputSelect('selCategory[]', $arrCategory, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelBrand'] =  $class->inputSelect('selBrand[]', $arrBrand, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputChkQOH'] =  $class->inputCheckBox('chkQOH',array('overwritePost' => false, 'value' => 1, 'class' => 'no-class'));  
$arrTwigVar['inputChkAvailable'] =  $class->inputCheckBox('chkAvailable',array('overwritePost' => false, 'value' => 1, 'class' => 'no-class'));  
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;   
echo $twig->render('reportItem.html', $arrTwigVar);  
 
?>