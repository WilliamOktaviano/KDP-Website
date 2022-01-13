<?php
include '../../_config.php';
include '../../_include-v2.php';

includeClass(array('ItemAdjustment.class.php','SalesOrder.class.php'));
$itemAdjustment = createObjAndAddToCol(new ItemAdjustment());
$item = createObjAndAddToCol(new Item());
$warehouse = createObjAndAddToCol(new Warehouse());
$salesOrder = createObjAndAddToCol(new SalesOrder());

include '_global.php';

$obj = $itemAdjustment;
$securityObject = 'reportItemAdjustment'; // the value of security object is manually inserted to handle 
								  // some modules that have different security object from that of their class
								  
if(!$security->isAdminLogin($securityObject,10,true));  
$hasCOGSAccess = $security->isAdminLogin($item->cogsSecurityObject,10);  

$arrFilterInformation = array();
$detailCriteria = '';
$_POST['selStatus[]'] = array(2,3);


// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"100px", 'dbfield' => 'code');
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"100px",'format'=>'date');
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"150px");
$arrDataStructure['profitLossCOGS'] = array('title'=>ucwords($obj->lang['diff']) . ' (COGS)','dbfield' => 'lossprofitcogs', 'width'=>"150px", 'format'=>"integer", "sortable" => false,'calculateTotal' => true);
$arrDataStructure['profitLossSelling'] = array('title'=>ucwords($obj->lang['diff']) . ' ('. ucwords($obj->lang['sellingPrice']).')','dbfield' => 'lossprofitselling', 'width'=>"150px", 'format'=>"integer", "sortable" => false,'calculateTotal' => true);
$arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trdesc', 'width'=>"350px");
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
		   
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['itemAdjustmentReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

// detail ...
$arrDataDetailStructure = array();
$arrDataDetailStructure['itemcode'] = array('title'=>ucwords($obj->lang['itemCode']),  'dbfield' => 'itemcode', 'width'=>'100px'); 
$arrDataDetailStructure['itemname'] = array('title'=>ucwords($obj->lang['itemName']),  'dbfield' => 'itemname', 'mergeExcelCell' => 2); 
$arrDataDetailStructure['qtybefore'] = array('title'=>ucwords($obj->lang['prevQty']),  'dbfield' => 'qtybefore', 'width'=>"60px", 'format' => 'number' ); 
$arrDataDetailStructure['qtyafter'] = array('title'=>ucwords($obj->lang['newQty']),  'dbfield' => 'qtyafter', 'width'=>"60px" , 'format' => 'number'  ); 
$arrDataDetailStructure['qtyadjust'] = array('title'=>ucwords($obj->lang['adjustment']),  'dbfield' => 'qtyadjust', 'width'=>"80px" , 'format' => 'number'  ); 
$arrDataDetailStructure['unitname'] = array('title'=>ucwords($obj->lang['unit']),  'dbfield' => 'unitname', 'width'=>"60px"   ); 
$arrDataDetailStructure['costinbaseunit'] = array('title'=>ucwords($obj->lang['cogs']),  'dbfield' => 'costinbaseunit', 'width'=>"60px" , 'format' => 'number'  );  
$arrDataDetailStructure['baseunitname'] = array('title'=>'','width'=>"60px", 'dbfield' => 'baseunitname', 'class' => 'text-muted');   

$arrDetailTemplate = array();
$arrDetailTemplate['reportWidth'] = "1000px";
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();

array_push($arrTemplate, $arrDetailTemplate);

if (isset($_POST) && !empty($_POST['hidAction'])){
	
	$criteria = '';
	if(isset($_POST) && !empty($_POST['code'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['code'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['code']));
	}
	if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
    
	if(isset($_POST) && !empty($_POST['itemName'])) { 
        $detailCriteria .= ' AND '.$obj->tableItem.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['itemName'].'%').')';
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

    
    $rsDetailCol = $obj->getDetailCollections($rs,'refkey',$detailCriteria);
     
    $totalRs = count($rs);
    
    for( $i=0;$i<$totalRs;$i++) {   
  
        if (!isset($rsDetailCol[$rs[$i]['pkey']]))  continue;
        $rsDetail = $rsDetailCol[$rs[$i]['pkey']]; 
  
        $lossProfitCOGS = 0;
        $lossProfitSellingPrice = 0;
        
        for ($j=0;$j<count($rsDetail);$j++){   
            if (!$hasCOGSAccess) $rsDetail[$j]['costinbaseunit'] = 0;   
            
            $lossProfitCOGS += ($rsDetail[$j]['qtyinbaseunit'] * $rsDetail[$j]['costinbaseunit']);
            $lossProfitSellingPrice += ($rsDetail[$j]['qtyinbaseunit'] * $rsDetail[$j]['itemsellingprice']);
            // gk bisa pake avg karena kalo ggk pernah ad transaksi di periode yg dipilih, jadiny 0
            
            $rsDetail[$j]['cogsunit'] = ' / ' . $rsDetail[$j]['baseunitname']; 
            $rsDetail[$j]['baseunitname'] = ' / ' . $rsDetail[$j]['baseunitname'];
        }
        
        $rs[$i]['lossprofitcogs'] = $lossProfitCOGS; 
        $rs[$i]['lossprofitselling'] = $lossProfitSellingPrice; 


         // has detail
        $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail);

        $return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate); 

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
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');

$arrTwigVar['inputCode'] =  $class->inputText('code');
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputHidItemKey'] = $class->inputHidden('hidItemKey');
$arrTwigVar['inputItemName'] =  $class->inputText('itemName'); 
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;  
 
echo $twig->render('reportItemAdjustment.html', $arrTwigVar);   

?>