<?php 

$rsTag = $obj->getAllTag();

function addDataListRow($rs,$arrColumn){
 	//global $addDataFile; 
	global $obj; 
	
	$datalistrow = '';
	
    // get status color
    $rsStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','textcolor'); 
     
	for($i=0;$i<count($rs);$i++)  
	   $datalistrow .= generateDataRow($obj,$rs[$i],$arrColumn,$rsStatus);
 
	
	 $datalistrow .= '
	  <script>
	  	$( document).ready(function() {    
            bindEl( $( "#" + selectedTab.newPanel[0].id + " .data-record [name=\'chkRow[]\']"),"click", function(){bindDataRowChkBox($(this))} );  
            bindSelectableDataRecord($( "#" + selectedTab.newPanel[0].id )); 
		}); 
	 </script>
     ';
	  
	  
	return $datalistrow;
}

function buildDataList($rs,$arrColumn){ 
    
	global $obj; 
    
    $rowClass = ($obj->isMobile()) ? 'mobile-selectable' : 'selectable'; 
	$datalist  = '<ol class="data-list-row '.$rowClass.'">';
	$datalist .= addDataListRow($rs,$arrColumn); 
	$datalist .= '</ol> '; 
	$datalist .= '<div class="load-more user-select-none">
						'.$obj->lang['nextPage'].'  '.$obj->loadingIcon.' </span> 
				  </div>';
    
	return $datalist; 
} 

global $addDataFile;
  
$criteria = array();

if (isset($_POST) && !empty($_POST['quickSearchKey'])){
	$quickSearchKey = $_POST['quickSearchKey']; 
    
    // backward compatible
    $arrSearchColumn = (isset($obj->arrSearchColumn)) ? $obj->arrSearchColumn : $arrSearchColumn;
        
    $quicksearchcriteria = array();
	for($i=0;$i<count($arrSearchColumn);$i++){
		array_push($quicksearchcriteria, $arrSearchColumn[$i][1] .' like ('.$obj->oDbCon->paramString( '%'.$quickSearchKey.'%' ).') ');	 
	}
	$quicksearchcriteria = '(' .implode(' OR ', $quicksearchcriteria).')';
    array_push($criteria, $quicksearchcriteria);
}

  
if(isset($_POST['filterCriteria']) && !empty($_POST['filterCriteria'])){
    
    foreach($_POST['filterCriteria'] as $filterRow){
      
        if(!isset($filterRow['selectedCriteriaKey']) || empty($filterRow['selectedCriteriaKey'])) continue;

        $field = $filterRow['field'];
        $selectedFilterCriteriaKey = $filterRow['selectedCriteriaKey'];


        $statusKeyCriteria = array();  
        foreach($selectedFilterCriteriaKey as $row)
            array_push($statusKeyCriteria, $obj->oDbCon->paramString($row));  

        $tableName =  (strpos($field, '.') !== false) ? $field : $obj->tableName .'.'.$field ;
        $statusCriteria =  $tableName.' in ('.implode(',',$statusKeyCriteria).')'; 
        
        array_push($criteria, $statusCriteria); 
    }
}

 
$selectedCriteriaTagKey = (!empty($_POST['selectedCriteriaTagKey'])) ?  $_POST['selectedCriteriaTagKey'] : array(); 
$tagKeyCriteria = array();
if (!empty($selectedCriteriaTagKey )){
	foreach($selectedCriteriaTagKey as $row)
		array_push($tagKeyCriteria, $obj->oDbCon->paramString($row));  
	 
	$tagCriteria =  $obj->tableName .'.tagkey in ('.implode(',',$tagKeyCriteria).')'; 
    array_push($criteria, $tagCriteria);
}  


$criteria  =  implode(' AND ', $criteria);
if (!empty($criteria))
    $criteria = ' AND ' . $criteria;
 
$orderby = (!empty($_POST['orderby'])) ? $obj->oDbCon->paramOrder($_POST['orderby']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
$ordertype = (isset($_POST['ordertype']) && !empty($_POST['ordertype']) && $_POST['ordertype'] != 1) ? 'asc' : 'desc';

$page = (!empty($_POST['page'])) ? $_POST['page'] : 0;

if (!is_numeric ($page))  die; 	  
	 
	 
$adminTotalRowsPerPage = $class->loadSetting('adminTotalRowsPerPage'); 

// for COA List
if(!empty($_POST['adminTotalRowsPerPage']))
	 $adminTotalRowsPerPage =  $_POST['adminTotalRowsPerPage']; 

$sortSql = ' order by '.  $orderby  .' '. $ordertype;

  
$filterCriteria = $obj->filterCriteria;   
$arrGroup = array();
foreach($filterCriteria as $row){ 
    if(isset($row['alias']))
        array_push($arrGroup, array('fieldName' => $row['alias'], 'groupkey' => $row['alias'] ));
    else
        array_push($arrGroup, array('fieldName' => $obj->tableName.'.'.$row['field'] , 'groupkey' => $row['field'] ));
}

// tambahin utk tag
array_push($arrGroup, array('fieldName' => $obj->tableName.'.tagkey', 'groupkey' => 'tagkey' ));

$rsCountedTotalRows = $obj->countTotalRows($criteria, $arrGroup);

// 1. select count dulu 
$totalDataRows = $obj->getCountedTotalRows($rsCountedTotalRows); 
$totalPages = ceil($totalDataRows/$adminTotalRowsPerPage);
 
$loadMoreTriggered = (!empty($_POST['loadMoreTriggered'])) ? $_POST['loadMoreTriggered'] : "false"; 
$lastRowIndex = ($loadMoreTriggered == "false") ? ($page * $adminTotalRowsPerPage) : $_POST['lastRowIndex'];  

if ( $lastRowIndex > $totalDataRows)  $lastRowIndex = 0; 
   
$obj->setCriteria($criteria);  
$query = $obj->getQueryForList();
if (empty($query))
    $query = $obj->getQuery();
$rs =  $obj->oDbCon->doQuery( $query . $sortSql . ' limit  '.$lastRowIndex.','.$adminTotalRowsPerPage  );  

// check read status
//$rs = $obj->getReadStatus($rs);
 
$isEOF = ( $lastRowIndex + $adminTotalRowsPerPage >= $totalDataRows ) ? true : false;

$arrReturn = array();

$arrReturn['dataList'] = ($loadMoreTriggered=="false") ? buildDataList($rs,$arrColumn) :  addDataListRow($rs,$arrColumn);  
 
$arrReturn['eof'] = $isEOF;   
$arrReturn['selectedPageIndex'] = $page;
$arrReturn['totalPages'] = $totalPages; 
$arrReturn['lastRowIndex'] = $lastRowIndex + count($rs);

for($k=0;$k<count($filterCriteria);$k++){ 
 
    //status information
    $arrStatusInformation = array();
    $rsItem = $obj->getFilterItem($filterCriteria[$k]);
    
    for($i=0;$i<count($rsItem);$i++){  
        $arrStatusInformation[$i]['pkey'] = $rsItem[$i]['pkey'];
        $arrStatusInformation[$i]['name'] = $rsItem[$i]['name'];
         
        $searchfield =  (!empty($filterCriteria[$k]['alias'])) ? $filterCriteria[$k]['alias'] :$filterCriteria[$k]['field'];
        
        $arrStatusInformation[$i]['totalData'] =  $obj->getCountedTotalRows($rsCountedTotalRows,$searchfield,$rsItem[$i]['pkey']);
    }  
        
    $filterCriteria[$k]['statusInformation'] = $arrStatusInformation;
    $filterCriteria[$k]['selectedCriteriaKey'] = (isset($_POST['filterCriteria'][$k]['selectedCriteriaKey']) && !empty($_POST['filterCriteria'][$k]['selectedCriteriaKey'])) ? $_POST['filterCriteria'][$k]['selectedCriteriaKey'] : array();
          
}

$arrReturn['filterCriteria'] = $filterCriteria;
    
    
//status information
$arrStatusInformation = array();
$rsStatus = $obj->getAllStatus(); 

$changeStatusCallback = ''; 
$statusContextMenu = array();

for($i=0;$i<count($rsStatus);$i++){ 
  
	$changeStatusCallback  .= 'case "'.$rsStatus[$i]['status'].'":  
								 changeStatus("'.$rsStatus[$i]['pkey'].'",key);
								 break;'.chr(13);
								 
	$statusContextMenu[$rsStatus[$i]['status']]['name'] = $rsStatus[$i]['status'];
	
}


$duplicationData = '';
if($obj->isTransaction)
    $duplicationData = 'case "duplicate":  
                                 duplicateData();
                                 break; '; 

//tag information
$arrTagInformation = array(); 
$tagCallback = ''; 
$tagContextMenu = array();

$tagCallback  .= 'case "ClearTag":  
						 changeTag("0",key);
						 break;'.chr(13);

$tagContextMenu['ClearTag']['name'] = $obj->lang['clearTag'];

for($i=0;$i<count($rsTag);$i++){ 
 
	$arrTagInformation[$i]['pkey'] = $rsTag[$i]['pkey'];
	$arrTagInformation[$i]['tagPkey'] = $rsTag[$i]['tagkey'];
	$arrTagInformation[$i]['tagName'] = $rsTag[$i]['tagname'];
	$arrTagInformation[$i]['hexColor'] = $rsTag[$i]['hexcolor']; 
	$arrTagInformation[$i]['totalData'] =  $obj->getCountedTotalRows($rsCountedTotalRows,'tagkey',$rsTag[$i]['tagkey']);
	
	
	$tagCallback  .= 'case "'.$rsTag[$i]['tagname'].'":  
								 changeTag("'.$rsTag[$i]['tagkey'].'",key);
								 break;'.chr(13);
								 
	$tagContextMenu[$rsTag[$i]['tagname']]['name'] = $rsTag[$i]['tagname'];
	
}

$arrReturn['tagInformation'] = $arrTagInformation; 
 
$contextMenu = array();
$contextMenu["selectAll"] = array("name"=>$obj->lang['selectAll'], "icon"=>"selectall");
$contextMenu["deselectAll"] = array("name"=>$obj->lang['deselectAll'], "icon"=>"deselectall");
$contextMenu["separator1"] = "-";
$contextMenu["showDetail"] = array("name" => $obj->lang['showDetail'], "icon"=>"showdetail");
$contextMenu["hideDetail"] = array("name" => $obj->lang['hideDetail'], "icon"=>"hidedetail");

if (!empty($addDataFile)){ 
    $contextMenu["edit"] = array("name" => $obj->lang['viewOrEdit'], "icon" =>"edit"); 
}
 
$contextMenu["changeStatus"] =  array("name" => $obj->lang['changeStatus'], "icon" =>"changestatus","items" => $statusContextMenu);
$contextMenu["changeTag"] = array("name" => $obj->lang['tag'],"icon" =>"tag", "items" => $tagContextMenu);  
 
$contextMenu["delete"] = array("name" => $obj->lang['delete'], "icon" =>"delete"); 
if (!empty($addDataFile)){  
    if($obj->isTransaction)
        $contextMenu["duplicate"] = array("name" =>  $obj->lang['duplicateDeletedData'], "icon" =>"duplicate"); 
}


//======= PRINT 

$callbackFunction = '';
 
if (isset($obj->printMenu)){	   
    $contextMenu['printSeparator'] = '-'; 
	foreach ($obj->printMenu as $key => $row) {   
        if($row['name'] == '-'){ 
            $contextMenu['printSeparator'.$key] = '-'; 
            continue;
        }
        
         $contextMenu[$row['code']] = array("name" => $row['name'], "icon" => $row['icon']); 
         $callbackFunction  .= $obj->generatePrintContextMenu($row['code'], $row['url']); 
	}  
}   

// OLD COMPATIBLE 
if (isset($overwriteContextMenu)){	   
	foreach ($overwriteContextMenu as $key => $value) {     
		$contextMenu[$key] = $overwriteContextMenu[$key];  
		if (!empty($contextMenu[$key]['callbackFunction']))  
		  $callbackFunction  .=  $contextMenu[$key]['callbackFunction'];  
            
	}  
}   


//======= ACTION  
if (isset($obj->actionMenu)){	   
    $contextMenu['printSeparator'] = '-'; 
	foreach ($obj->actionMenu as $key => $row) {    
         $contextMenu[$row['code']] = array("name" => $row['name'], "icon" => $row['icon']);
         $function = (isset($row['function'])) ? $row['function'] : '';
         $url = (isset($row['url'])) ? $row['url'] : '';
         $callbackFunction  .= $obj->generateActionContextMenu($row['code'],$url, $function); 
	}  
}   


$arrReturn['contextMenu'] = array(); 

foreach ( $contextMenu as $key => $value) {   
    if (!empty($value))
      $arrReturn['contextMenu'][$key]=$contextMenu[$key];   
}

$arrReturn['contextMenuCallback'] = '  
									  switch(key) {
											case "selectAll":    
												selectAllRows(); 
												break;
											case "deselectAll": 
												deselectAllRows();
												break;
											case "showDetail": 
												toggleAllSelectedDataDetail(2);
												deselectAllRows(); 
												break;
											case "hideDetail": 
												toggleAllSelectedDataDetail(1);
												deselectAllRows(); 
												break;
											case "edit": 
												openTabForEdit(); 
												break;
											case "delete":  
												 deleteData();
												 break;   
											'.$duplicationData.'   
											'.$changeStatusCallback .' 
											'.$tagCallback .' 
											'.$callbackFunction.'
											default: 
												break;
										} '; 
 

$arrReturn['isTransaction'] = $obj->isTransaction;
$arrReturn['transactionStatus'] = $rsStatus;

echo json_encode($arrReturn);
die;

?>