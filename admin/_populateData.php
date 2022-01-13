<?php 

$rsTag = $obj->getAllTag();

function addDataListRow($rs,$arrColumn){
 	//global $addDataFile; 
	global $obj; 
	
	$datalistrow = '';
	
    // get status color
    $rsStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','textcolor'); 
     
	for($i=0;$i<count($rs);$i++){   
	  
        $shadowClass = (!empty($rs[$i]['tagkey'])) ? $obj->shadowClass[$rs[$i]['tagkey']] : '';  
        
	    $inputStatusStyle = (isset($rsStatus[$rs[$i]['statuskey']]['label']) && !empty($rsStatus[$rs[$i]['statuskey']]['label'])) ? 'color: ' . $rsStatus[$rs[$i]['statuskey']]['label'] : ''; 
		  
        $readClass = (!$rs[$i]['read']) ? 'unread-status' : '';
            
		$datalistrow .= '<li class="data-record '.$shadowClass.'" relId="'.$rs[$i]['pkey'].'">';
		//$datalistrow .= '<div style="background-color:#333; width: 100%; height: 100%"></div>';
		$datalistrow .= '<div class="table-data-record-header" >';  
        $datalistrow .= '<div class="div-table-row "> '; 
        $datalistrow .= '<div class="div-table-col  read-status-col '.$readClass.'"></div>'; 
        $datalistrow .= '<div style="text-align:center; width:2em;" class="div-table-col unselectable"><input type="checkbox" name="chkRow[]"></div>';
       
        for($j=0;$j<count($arrColumn);$j++){ 

            // compability
            if(isset($arrColumn[$j]['code'])){ 
                if(gettype($arrColumn[$j]['format']) == 'object'){  
                    $content =  $arrColumn[$j]['format']($rs[$i],$obj); 
                    $format = '';
                }else{
                    $content = $rs[$i][$arrColumn[$j]['dbfield']];
                    $format = (isset($arrColumn[$j]['format'])) ? strtolower($arrColumn[$j]['format']) : '';
                }
                   
                $width = (isset($arrColumn[$j]['width']) && !empty($arrColumn[$j]['width'])) ? 'width:'.$arrColumn[$j]['width'].'px' : '';
                $textAlign = (isset($arrColumn[$j]['align'])) ? 'text-align:'.$arrColumn[$j]['align'].';' : '';
            }else{ 
                $content = $rs[$i][$arrColumn[$j][1]];
                $format = (isset($arrColumn[$j][4])) ? strtolower($arrColumn[$j][4]) : '';
                $width = (isset($arrColumn[$j][2]) && !empty($arrColumn[$j][2])) ? 'width:'.$arrColumn[$j][2].'px' : '';
                $textAlign = (isset($arrColumn[$j][3])) ? 'text-align:'.$arrColumn[$j][3].';' : '';
            }
              
            switch($format){
                case 'integer':  $content = $obj->formatNumber($content);
                                 break;
                case 'decimal':  $content = $obj->formatNumber($content,2);
                                 break;
                case 'number':  $content = $obj->formatNumber($content,-2);
                                 break;
                case 'date':  $content = $obj->formatDbDate($content,'',array('returnOnEmpty' => true));
                                 break;
                case 'time':  $content = $obj->formatDbDate($content,'H:i');
                                 break;
                case 'datetime':  $content = $obj->formatDbDate($content,'d / m / Y H:i');
                                 break;
            }
                
             $content = str_replace(chr(13),'<br>',$content);
                 
             $datalistrow .= ' <div style="'.$textAlign.' '. $width.'" class="div-table-col"><span class="unselectable" style="'.$inputStatusStyle.'">'. $content .'</span></div> ';
         } 
         
        $rowIcon = (isset($rs[$i]['systemVariable']) && $rs[$i]['systemVariable'] == 1) ? '<i class="fas fa-lock"></i>' : ''; 
        $rowIcon = (isset($rs[$i]['islinked']) && $rs[$i]['islinked'] == 1) ? '<i class="fas fa-link"></i>' : $rowIcon; 

        $datalistrow .= '<div style="text-align:center; width: 30px;" class="div-table-col-5 tag">'.$rowIcon.'</div>';

        $datalistrow .= '</div>';
		$datalistrow .= '</div> ';   
		$datalistrow .= '<div class="table-data-record-detail'.$rs[$i]['pkey'].' table-data-record-detail" ></div>';  
		$datalistrow .=  '</li> '; 
	
	}
	
	
	 $datalistrow .= '
	  <script>
	  	$( document).ready(function() {     
            $( "#" + selectedTab.newPanel[0].id + " .data-record [name=\'chkRow[]\']").bind( "click", function(event) {  
                if ($(this).prop("checked"))
                    selectMultiRows($(this).closest(".data-record"));
                else
                    deselectRow($(this).closest(".data-record"));
            });
             
               
            $( "#" + selectedTab.newPanel[0].id + " .selectable").selectable({
                 filter : "li",	
                 cancel: ".unselectable, .data-card",  
                 stop: function() {      
	
                    resetSelectedRows();
                     
                    $( ".ui-selected", this ).each(function() { 
                        selectMultiRows($(this).closest(".data-record")); 
                    });
   
                  }
             })   
			 
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

        $statusCriteria =  $obj->tableName .'.'.$field.' in ('.implode(',',$statusKeyCriteria).')'; 
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
 
/* 
$selectedCriteriaWarehouseKey = (!empty($_POST['selectedCriteriaWarehouseKey'])) ?  $_POST['selectedCriteriaWarehouseKey'] : array();  
$warehouseKeyCriteria = array();
if (!empty($selectedCriteriaWarehouseKey )){
    
	foreach($selectedCriteriaWarehouseKey as $row)
		array_push($warehouseKeyCriteria, $obj->oDbCon->paramString($row)); 
	 
	$warehouseCriteria =  $obj->tableName .'.warehousekey in ('.implode(',',$warehouseKeyCriteria).')'; 
    array_push($criteria, $warehouseCriteria);
}  */
 

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


$obj->setCriteria($criteria);  
$sortSql = ' order by '.  $orderby  .' '. $ordertype;

$query = $obj->getQueryForList();
if (empty($query))
    $query = $obj->getQuery();
$rs =  $obj->oDbCon->doQuery( $query . $sortSql );  

$totalDataRows = count($rs);
$totalPages = ceil($totalDataRows/$adminTotalRowsPerPage);

	 
$loadMoreTriggered = "false";
if(!empty($_POST['loadMoreTriggered']))
 	$loadMoreTriggered  = $_POST['loadMoreTriggered'];
 
if ($loadMoreTriggered == "false") { 
	$lastRowIndex = $page * $adminTotalRowsPerPage;  
}else{
	$lastRowIndex = $_POST['lastRowIndex']; 
}

if ( $lastRowIndex > $totalDataRows){
	$lastRowIndex = 0;
}
 
$rs = array_slice($rs,$lastRowIndex,$adminTotalRowsPerPage); 

// check read status
$rs = $obj->getReadStatus($rs);
 

$isEOF = false;
if ( $lastRowIndex + $adminTotalRowsPerPage >= $totalDataRows )
	$isEOF = true;

$arrReturn = array();

if ($loadMoreTriggered=="false"){
	$arrReturn['dataList'] = buildDataList($rs,$arrColumn);  
}else{
	$arrReturn['dataList'] = addDataListRow($rs,$arrColumn);  
}

$arrReturn['eof'] = $isEOF;   
$arrReturn['selectedPageIndex'] = $page;
$arrReturn['totalPages'] = $totalPages; 
$arrReturn['lastRowIndex'] = $lastRowIndex + count($rs);
 
$filterCriteria = $obj->filterCriteria;  
for($k=0;$k<count($filterCriteria);$k++){ 

    //status information
    $arrStatusInformation = array();
    $rsItem = $obj->getFilterItem($filterCriteria[$k]);
 
    $rsTotalRows = $obj->countTotalRows($criteria, $obj->tableName.'.'.$filterCriteria[$k]['field']); 
    $arrTotalRows = array_column($rsTotalRows,'totalrows','groupkey'); 

    for($i=0;$i<count($rsItem);$i++){  
        $arrStatusInformation[$i]['pkey'] = $rsItem[$i]['pkey'];
        $arrStatusInformation[$i]['name'] = $rsItem[$i]['name'];
        $arrStatusInformation[$i]['totalData'] = (isset($arrTotalRows[$rsItem[$i]['pkey']])) ? $arrTotalRows[$rsItem[$i]['pkey']] : 0;  
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
 
$rsTotalRows = $obj->countTotalRows($criteria, $obj->tableName.'.statuskey'); 
$arrTotalRows = array_column($rsTotalRows,'totalrows','groupkey'); 

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
	  

$arrTagKey  = array_column($rsTag,'tagkey'); 
 
$rsTotalRows = $obj->countTotalRows($criteria, $obj->tableName.'.tagkey'); 
$arrTotalRows = array_column($rsTotalRows,'totalrows','groupkey');
	
for($i=0;$i<count($rsTag);$i++){ 
 
	$arrTagInformation[$i]['pkey'] = $rsTag[$i]['pkey'];
	$arrTagInformation[$i]['tagPkey'] = $rsTag[$i]['tagkey'];
	$arrTagInformation[$i]['tagName'] = $rsTag[$i]['tagname'];
	$arrTagInformation[$i]['hexColor'] = $rsTag[$i]['hexcolor']; 
	$arrTagInformation[$i]['totalData'] =  (isset($arrTotalRows[$rsTag[$i]['tagkey']])) ? $arrTotalRows[$rsTag[$i]['tagkey']] : 0;
	
	
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