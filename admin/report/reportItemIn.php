<?php
include '../../_config.php';
include '../../_include-v2.php';

includeClass('ItemIn.class.php');
$itemIn = createObjAndAddToCol(new ItemIn());
$item = createObjAndAddToCol(new Item());
$warehouse = createObjAndAddToCol(new Warehouse());

include '_global.php';

$obj = $itemIn;
$securityObject = 'reportItemIn'; // the value of security object is manually inserted to handle 
								  // some modules that have different security object from that of their class
								  
if(!$security->isAdminLogin($securityObject,10,true));  
$hasCOGSAccess = $security->isAdminLogin($item->cogsSecurityObject,10);  

$arrFilterInformation = array(); 
$detailCriteria = ''; 
$_POST['selStatus[]'] = array(2,3);
if(!isset($_POST['isGrouping']))  $_POST['isGrouping'] = 1;
if(!isset($_POST['isShowDetail']))  $_POST['isShowDetail'] = 1;

// ====================== must be set before TWIG
if (!isset($_POST['trStartDate']) || empty($_POST['trStartDate'])){ 
	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y');
}

$orderCriteria = array(); 
$orderCriteria['orderBy'] =  (isset ($_POST) && !empty($_POST['hidOrderBy']) ) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'trdate'; //$obj->tableName.'.
$orderCriteria['orderType'] = (isset ($_POST) && !empty($_POST['hidOrderType'])) ?   $_POST['hidOrderType'] : -1;
    

// ====================== must be set before TWIG


$dataToExport = array();

/* data structure */
$arrTemplate = array();
$isGrouping = (isset($_POST['isGrouping']) && $_POST['isGrouping'] == 1) ? true : false;
$isShowDetail = (isset($_POST['isShowDetail']) && $_POST['isShowDetail'] == 1) ? true : false;

$arrDataStructure = array(); 
$_POST['module'] = IMPORT_TEMPLATE['itemIn'];

switch($EXPORT_TYPE){
    case 2 :   
            $arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"100px",'format'=>'date');
            $arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"150px" );
            $arrDataStructure['itemname'] = array('title'=>ucwords($obj->lang['itemName']),  'dbfield' => 'itemname',  'width'=>'300px' );  
            $arrDataStructure['qty'] = array('title'=>ucwords($obj->lang['qty']),  'dbfield' => 'qty', 'width'=>"60px", 'format' => 'number' ); 
            $arrDataStructure['unit'] = array('title'=>ucwords($obj->lang['unit']),  'dbfield' => 'unitname', 'width'=>"60px");
            $arrDataStructure['costinbaseunit'] = array('title'=>ucwords($obj->lang['cogs']),  'dbfield' => 'costinbaseunit', 'width'=>"80px", 'format' => 'number');  
           break;
        
    default :
            $arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
            $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"150px", 'dbfield' => 'code');
            $arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"100px",'format'=>'date');
            $arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"150px" );

            if(!$isGrouping){ 
                $arrDataStructure['itemcode'] = array('title'=>ucwords($obj->lang['itemCode']),  'dbfield' => 'itemcode', 'width'=>'150px' ); 
                $arrDataStructure['itemname'] = array('title'=>ucwords($obj->lang['itemName']),  'dbfield' => 'itemname',  'width'=>'300px' ); 
                $arrDataStructure['qty'] = array('title'=>ucwords($obj->lang['qty']),  'dbfield' => 'qty', 'width'=>"60px", 'format' => 'number' ); 
                $arrDataStructure['unit'] = array('title'=>ucwords($obj->lang['unit']),  'dbfield' => 'unitname', 'width'=>"60px");
                $arrDataStructure['costinbaseunit'] = array('title'=>ucwords($obj->lang['cogs']),  'dbfield' => 'costinbaseunit', 'width'=>"80px", 'format' => 'number'); 
                $arrDataStructure['cogsunit'] = array('title'=>'',  'dbfield' => 'cogsunit', 'width'=>"60px", 'class' => 'text-muted'); 
                $arrDataStructure['totalcogs'] = array('title'=>ucwords($obj->lang['total']),  'dbfield' => 'totalcogs', 'width'=>"80px", 'format' => 'number','calculateTotal' => true);
            }

            $arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trdesc', 'width'=>"350px");
            $arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px" );
        break;
}
        
		  
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['itemInReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

// kalo export gk perlu ad detail
if ($EXPORT_TYPE != 2){

    if ($isGrouping && $isShowDetail){
        // detail ...
        $arrDataDetailStructure = array();
        $arrDataDetailStructure['itemcode'] = array('title'=>ucwords($obj->lang['itemCode']),  'dbfield' => 'itemcode', 'width'=>'150px', 'format' => 'string'  ); 
        $arrDataDetailStructure['itemname'] = array('title'=>ucwords($obj->lang['itemName']),  'dbfield' => 'itemname', 'mergeExcelCell' => 2); 
        $arrDataDetailStructure['qty'] = array('title'=>ucwords($obj->lang['qty']),  'dbfield' => 'qty', 'width'=>"60px", 'format' => 'number' ); 
        $arrDataDetailStructure['unit'] = array('title'=>ucwords($obj->lang['unit']),  'dbfield' => 'unitname', 'width'=>"60px"  ); 
        $arrDataDetailStructure['costinbaseunit'] = array('title'=>ucwords($obj->lang['cogs']),  'dbfield' => 'costinbaseunit', 'width'=>"80px", 'format' => 'number' ); 
        $arrDataDetailStructure['cogsunit'] = array('title'=>'','width'=>"60px", 'dbfield' => 'cogsunit', 'class' => 'text-muted'); 
        $arrDataDetailStructure['totalcogs'] = array('title'=>ucwords($obj->lang['total']),'width'=>"80px", 'dbfield' => 'totalcogs','format' => 'number','calculateTotal' => true); 

        $arrDetailTemplate = array();
        $arrDetailTemplate['reportWidth'] = "900px";
        $arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
        $arrDetailTemplate['total'] = array();

        array_push($arrTemplate, $arrDetailTemplate);
    }
}

$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');

$arrTwigVar['importUrl'] = $obj->importUrl; 

$arrTwigVar['inputCode'] =  $class->inputText('code'); 
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
//$arrTwigVar['inputHidItemKey'] = $class->inputHidden('hidItemKey');
$arrTwigVar['inputItemName'] =  $class->inputText('itemName'); 
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
//$arrTwigVar['inputChkSN'] =  $class->inputCheckBox('chkSN',array('overwritePost' => false, 'value' => 0, 'class' => 'no-class'));  
$arrTwigVar['inputIsGrouping'] =  $class->inputCheckBox('isGrouping'); 
$arrTwigVar['inputShowDetail'] =  $class->inputCheckBox('isShowDetail'); 
$arrTwigVar['order'] =  $orderCriteria;
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;

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
    
    $order = 'order by '.$orderCriteria['orderBy'].' ' . (($orderCriteria['orderType'] == 1) ? 'desc' : 'asc');  
	//$rs = $obj->searchData('','',true,$criteria,$order); 
    
    // kalo utk export template gk perlu ad data
    $rs = array();
    if ($EXPORT_TYPE != 2)
        $rs = (!$isGrouping) ? $obj->generateItemInReport($criteria.$detailCriteria,$order) :  $obj->searchData('','',true,$criteria,$order);
     
    
    $tempreport = '';  
		
    // ============================= GENERATE DATA ============================= 
 
    for( $i=0;$i<count($rs);$i++) {  
        if($isGrouping && $isShowDetail){
            $rsDetail = $obj->getDetailWithRelatedInformation($rs[$i]['pkey'],$detailCriteria); 
            if (empty($rsDetail)) continue;

            // has detail
            foreach($rsDetail as $key=>$row){
               if(!$hasCOGSAccess){
                   $rsDetail[$key]['costinbaseunit'] = 0;
                   $rsDetail[$key]['totalcogs'] = 0;
               }
            }
            
            $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail);
        }else{
            
            foreach($rs as $key=>$row){
               if(!$hasCOGSAccess){
                   $rs[$key]['costinbaseunit'] = 0;
                   $rs[$key]['totalcogs'] = 0;
               }
            }
            
        }
        
        
        $return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate); 

        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];

        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);
    }

    $tableHeader = $twig->render('template-header.html', $arrTwigVar);
    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation,$tableHeader);

}
 
echo $twig->render('reportItemIn.html', $arrTwigVar);   

?>