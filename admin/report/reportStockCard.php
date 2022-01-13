<?php
	 
include '../../_config.php';  
include '../../_include-v2.php';
include '_global.php';

includeClass(array("ItemMovement.class.php","Item.class.php","ItemCategory.class.php","Brand.class.php"));
$itemMovement = new ItemMovement();
$brand = new Brand();
$obj= $itemMovement;

$item = new Item();
$itemCategory = new ItemCategory();
$warehouse = new Warehouse();

$securityObject = 'reportStockCard'; // the value of security object is manually inserted to handle 
									 // some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 
$_POST['selStatus[]'] = array(1);
$hasCOGSAccess = $security->isAdminLogin($item->cogsSecurityObject,10);  
 

if(!isset($_POST['isShowDetail']))  $_POST['isShowDetail'] = 1; 
$isShowDetail = (isset($_POST['isShowDetail']) && $_POST['isShowDetail'] == 1) ? true : false;

$arrFilterInformation = array();  
 
// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"150px", 'dbfield' => 'code'); 
$arrDataStructure['name'] = array('title'=>ucwords($obj->lang['itemName']),'dbfield' => 'name', 'width'=>"300px", 'mergeExcelCell' => 3); 
$arrDataStructure['itemCategory'] = array('title'=>ucwords($obj->lang['itemCategory']),  'width'=>"180px", 'dbfield' => 'categoryname', 'mergeExcelCell' => 3);
$arrDataStructure['brand'] = array('title'=>ucwords($obj->lang['brand']),  'width'=>"140px", 'dbfield' => 'brandname');
$arrDataStructure['qoh'] = array('title'=>ucwords($obj->lang['qoh']),'dbfield' => 'qtyonhand', 'width'=>"100px",'format'=>'number', "sortable" => false); 
$arrDataStructure['cogs'] = array('title'=>ucwords($obj->lang['cogs']),'dbfield' => 'cogs', 'width'=>"100px",'format'=>'number', "sortable" => false); 
$arrDataStructure['totalcogs'] = array('title'=>ucwords($obj->lang['totalCOGS']), 'dbfield' => 'totalcogs', 'width'=>"95px" ,'format'=>'number', "sortable" => false, 'calculateTotal' => true);
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
		   
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['stockCardReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

if($isShowDetail){
// detail ...
$arrDataDetailStructure = array();  
$arrDataDetailStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"90px",'format'=>'date'); 
$arrDataDetailStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"90px"); 
$arrDataDetailStructure['beginningQty'] = array('title'=>ucwords($obj->lang['beginningQty']),'dbfield' => 'startQty', 'width'=>"70px",'format'=>'number'); 
$arrDataDetailStructure['movementQty'] = array('title'=>ucwords($obj->lang['movement']),'dbfield' => 'qtyinbaseunit', 'width'=>"70px",'format'=>'number'); 
$arrDataDetailStructure['balanceQty'] = array('title'=>ucwords($obj->lang['qoh']),'dbfield' => 'afterQty', 'width'=>"70px",'format'=>'number', 'textColor' => '0093AF');  
$arrDataDetailStructure['unit'] = array('title'=>ucwords($obj->lang['unit']),'dbfield' => 'baseunitname', 'width'=>"90px");  
$arrDataDetailStructure['startCOGS'] = array('title'=>ucwords($obj->lang['beginningCOGS']),'dbfield' => 'startCOGS', 'width'=>"90px",'format'=>'number'); 
$arrDataDetailStructure['movementCOGS'] = array('title'=>ucwords($obj->lang['movement']),'dbfield' => 'movementCOGS', 'width'=>"90px",'format'=>'number'); 
$arrDataDetailStructure['afterCOGS'] = array('title'=>ucwords($obj->lang['cogs']),'dbfield' => 'afterCOGS', 'width'=>"90px",'format'=>'number', 'textColor' => '568203'); 
$arrDataDetailStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'note', 'width'=>"350px", 'mergeExcelCell' => 3);
  
$arrDetailTemplate = array(); 
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();

array_push($arrTemplate, $arrDetailTemplate);  
}
	
if (isset($_POST) && !empty($_POST['hidAction'])){   
	
	$criteria = '';
	
	if(isset($_POST) && !empty($_POST['trStartDate'])){
		//$criteria .= ' and trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	} 
	if(isset($_POST) && !empty($_POST['itemCode'])) {
		$criteria .= ' AND '.$obj->tableItem.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['itemCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['itemCode']));
	}
	if(isset($_POST) && !empty($_POST['itemName'])) {  
        $criteria .= ' AND '.$obj->tableItem.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['itemName'].'%').')';
	    array_push($arrFilterInformation,array("label" => 'Item', 'filter' => $_POST['itemName']));
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
        
        $criteria .= ' AND brandkey in('.$key.')';  

        $rsCriteria = $brand->searchData('','',true, ' and '.$brand->tableName.'.pkey in ('.$key.')');
	 
        $arrTempCategory = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempCategory,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempCategory); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['brand'], 'filter' => $statusName));
	} 
	
    if(isset($_POST) && !empty($_POST['selStatus'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selStatus']));   
        
       	$criteria .= ' AND '.$item->tableName.'.statuskey in('.$key.')';  

        $rsCriteria =  $item->getStatusById ($key);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['status']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Status', 'filter' => $statusName));
        
	}
 
 	$warehouseCriteria = '';
	$warehousekey = '';  

	if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $warehousekey = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
       	$warehouseCriteria .= ' AND warehousekey in('.$warehousekey.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$warehousekey.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$warehouseName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Gudang', 'filter' => $warehouseName ));
        
	}
	 
  
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';

	 
	$order = 'order by '.$orderBy.' ' .$orderType; 
	 
    // oerwrite jenis item
    $item->itemType = "1,3"; 
	$rsItem = $item->searchData('','',true,$criteria,$order); 
    
    $startDate = date('d / m / Y',strtotime(str_replace('\'','',$obj->oDbCon->paramDate($_POST['trStartDate'],' / ','Y-m-d')).' -1 day'));
    $endDate = $_POST['trEndDate'];
    
    $warehousekey =  (isset($_POST['selWarehouse'])) ? $_POST['selWarehouse']  : '';
	
	$tempreport = '';

    if (empty($rsItem))
         $tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';
  
    $arrItemKey = array_column($rsItem,'pkey');
    
    // QOH
    $arrStartQty = $obj->sumItemsMovement($arrItemKey,$warehousekey,$startDate);  
    $arrStartQty = array_column($arrStartQty,null,'itemkey');
    
    $arrEndQty = $obj->sumItemsMovement($arrItemKey,$warehousekey,$endDate);  
    $arrEndQty = array_column($arrEndQty,null,'itemkey');
    
    // COGS
    $arrStartCOGS = array();
    $arrEndCOGS = array();
    if($hasCOGSAccess){ 
        $arrStartCOGS = $obj->sumItemsCOGSMovement($arrItemKey, $warehousekey ,$startDate) ;  
        $arrStartCOGS = array_column($arrStartCOGS,null,'itemkey');
         
        $arrEndCOGS = $obj->sumItemsCOGSMovement($arrItemKey, $warehousekey ,$endDate) ;  
        $arrEndCOGS = array_column($arrEndCOGS,null,'itemkey');
    }
    
    for($j=0;$j<count($rsItem);$j++){ 
		 
        $itemkey = $rsItem[$j]['pkey'];
        
        $endQty = (isset($arrEndQty[$itemkey])) ? $arrEndQty[$itemkey]['qtyinbaseunit'] : 0;
        $endCOGS = (isset($arrEndCOGS[$itemkey])) ? $arrEndCOGS[$itemkey]['costinbaseunit'] : 0;

        $rsItem[$j]['qtyonhand'] = $endQty;
        $rsItem[$j]['cogs'] = ($endQty > 0) ? $endCOGS / $endQty : 0;
        $rsItem[$j]['totalcogs'] = $endCOGS;
        
        
        if($isShowDetail){ 
            $startQty = (isset($arrStartQty[$itemkey]) ) ? $arrStartQty[$itemkey]['qtyinbaseunit'] : 0;
            $startCOGS = (isset($arrStartCOGS[$itemkey]) ) ? $arrStartCOGS[$itemkey]['costinbaseunit'] : 0;
            
            $dateMethod = $class->loadSetting('movementDateMethod');
            $datefield = ($dateMethod == 2) ? 'trdate' : 'createdon'; 
            $rsDetail = $obj->searchData('','',true,' and '.$obj->tableName.'.statuskey = 1 and itemkey = ' .$class->oDbCon->paramString($itemkey) . $warehouseCriteria . ' and '.$obj->tableName.'.'.$datefield.' between '.$class->oDbCon->paramDate($_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate($_POST['trEndDate'], ' / ','Y-m-d 23:59:59') ,'order by '.$obj->tableName.'.'.$datefield.' asc');

            $arrDetailStyle = array();
            for( $i=0;$i<count($rsDetail);$i++) {   
                 if (!$hasCOGSAccess) 
                    $rsDetail[$i]['costinbaseunit'] = 0; 

                if ($i==0){ 
                    $rsDetail[$i]['startQty'] = $startQty;
                    $rsDetail[$i]['startCOGS'] = $startCOGS;
                }else{
                    $rsDetail[$i]['startQty'] = $rsDetail[$i-1]['afterQty'];
                    $rsDetail[$i]['startCOGS'] = $rsDetail[$i-1]['afterCOGS'];
                }


                 $rsDetail[$i]['afterQty'] = $rsDetail[$i]['startQty'] + $rsDetail[$i]['qtyinbaseunit']; 

                 $rsDetail[$i]['movementCOGS'] = $rsDetail[$i]['qtyinbaseunit'] * $rsDetail[$i]['costinbaseunit']; 
                 $rsDetail[$i]['afterCOGS'] = $rsDetail[$i]['startCOGS'] + $rsDetail[$i]['movementCOGS'];


                if ($rsDetail[$i]['qtyinbaseunit'] < 0)  
                    $arrDetailStyle[$i]['qtyinbaseunit']['textColor'] = 'C41E3A';   

            }


            $qoh = (isset($rsDetail[$i-1])) ?  $rsDetail[$i-1]['afterQty'] : $startQty;
            $cogs = (isset($rsDetail[$i-1])) ?  $rsDetail[$i-1]['afterCOGS'] : $startCOGS ;

            //$rsItem[$j]['qtyonhand'] = $qoh; 
            //$rsItem[$j]['totalcogs'] = $cogs;

            if(!$hasCOGSAccess)  $rsItem[$j]['cogs'] = 0;

            // has detail
            $rsItem[$j]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail, 'style' => $arrDetailStyle);
        }
        
                  
        $return = $obj->formatReportRows(array('data' => $rsItem[$j]),$arrTemplate); 
            
        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];

        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);
            
		}  
    
        $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);
}
else{ 
	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y');
} 


$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name'); 
$arrCategory = $class->convertForCombobox($itemCategory->searchData($itemCategory->tableName.'.statuskey',1,true, ' and '.$itemCategory->tableName.'.isleaf = 1', ' order by name asc'),'pkey','name');   
$arrBrand = $class->convertForCombobox($brand->searchData($brand->tableName.'.statuskey',1,true,'', ' order by name asc'),'pkey','name');   
$arrStatus = $item->getAllStatus();
$arrStatus = $class->convertForCombobox($arrStatus,'pkey','status');  

$arrTwigVar['inputItemCode'] =  $class->inputText('itemCode');
$arrTwigVar['inputHidItemKey'] = $class->inputHidden('hidItemKey');
$arrTwigVar['inputItemName'] =  $class->inputText('itemName'); 
$arrTwigVar['inputSelCategory'] =  $class->inputSelect('selCategory[]', $arrCategory, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelBrand'] =  $class->inputSelect('selBrand[]', $arrBrand, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));   
$arrTwigVar['inputShowDetail'] =  $class->inputCheckBox('isShowDetail'); 
$arrTwigVar['autoLoad'] =  0; 
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;   
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 

echo $twig->render('reportStockCard.html', $arrTwigVar);   
?>