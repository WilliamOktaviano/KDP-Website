<?php
if(!$security->isAdminLogin($securityObject,10,true)) die;
$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');
   
if (!isset($arrColumn) && isset($obj->arrDataListAvailableColumn) )
    $arrColumn = $obj->generateDataListColumn($FILE_NAME);


// ========================================================================== AJAX SECTION ==========================================================================
if (isset($_POST['generateDataRecords']) && !empty($_POST['generateDataRecords']) ){  
	include ('populateData.php');  
}

// ========================================================================== ADD DATA SECTION ==========================================================================

if (isset($_POST['action']) && !empty($_POST['action']) ){
	include ('dataProcess.php');
}

// ========================================================================== QUICK VIEW SECTION ==========================================================================
if (isset($_POST['generateQuickView']) && !empty($_POST['generateQuickView']) ){
	echo generateQuickView($obj,$_POST['id']);
	die;
}
 
$addDataFile = (isset($addDataFile)) ? $addDataFile : '';
$addDataFile = getPersonalizedFiles($addDataFile);
//$FILE_NAME = getPersonalizedFiles($FILE_NAME); // harusnya gk perlu karena sudah diatur di database utk custom

// ======================================================== NAVBAR  
$arrNavbarButton = array();

$addDataActionClass = 'btn-action';
$idAdd = 'btn-add-new';
$idEdit = 'btn-edit-data';
$hasAddAccess = $security->isAdminLogin($obj->securityObject,11,false); 
if(!$hasAddAccess){
 $addDataActionClass .= ' disabled';
 $idAdd = ''; 
}  

if (!empty($addDataFile)){  
    array_push($arrNavbarButton, '<li id="'.$idAdd.'" class="'.$addDataActionClass.'" style="font-size:26px; line-height: 34px"  title="'.$obj->lang['add'].'"><i class="fas fa-plus"></i></li>');
    array_push($arrNavbarButton, '<li id="'.$idEdit.'" class="btn-action"  title="'.$obj->lang['edit'].'"><i class="fas fa-edit"></i></li>');
}

$actionClass = 'btn-action';
$id = 'btn-delete';
if(!$security->isAdminLogin($obj->securityObject,12,false)){
 $actionClass .= ' disabled';
 $id = '';
}
  
array_push($arrNavbarButton, '<li id="'.$id.'" class="'.$actionClass.'" style="font-size:26px; line-height: 34px" title="'.$obj->lang['delete'].'"><i class="fas fa-times"></i></li>');

array_push($arrNavbarButton, '<li id="btn-refresh" class="btn-action"  title="'.$obj->lang['refresh'].'" style="font-size:20px"><i class="fas fa-sync"></i></li>');

if($FILE_NAME == 'salesOrderList' && $obj->isActiveModule('marketplace')) 
    array_push($arrNavbarButton, '<li id="btn-sync-marketplace" class="btn-action"  title="'.$obj->lang['syncToAllMarketplaces'].'" style="font-size:20px"><i class="fas fa-cart-arrow-down"></i></li>');


if (isset($obj->importUrl) && !empty($obj->importUrl)){  
     $importBtn = ($hasAddAccess) ? '<li id="btn-import"  class="'.$addDataActionClass.'" title="'.$obj->lang['import'].'" ><a href="'.$obj->importUrl.'" target="_blank"><i class="fas fa-file-import"></i></a></li>' :  '<li id="btn-import"  class="'.$addDataActionClass.'" title="'.$obj->lang['import'].'" ><i class="fas fa-file-import"></i></li>'; 
     array_push($arrNavbarButton, $importBtn);  
}  

array_push($arrNavbarButton, '<li class="separator">&nbsp;</li>');
 
if (!isset($quickView) || $quickView == true) 
    array_push($arrNavbarButton, ' <li id="btn-expand-all" class="btn-action" title="'.$obj->lang['showDetail'].'" ><i class="far fa-window-maximize"></i></li>');
         


// ===== change status 
$changeStatusSubMenu = '<li class="btn-action dropdown change-status">
                                <a href="#" data-toggle="dropdown" class="dropdown-toggle"><i class="fas fa-check-circle"  title="'.$obj->lang['changeStatus'].'"></i></a>
                                <ul class="dropdown-menu single-level">';
 
foreach($arrStatus as $key => $row)
    $changeStatusSubMenu .= '<li class="menu-item" rel-index="'.$key.'" title="'.$row['label'].'" ><div>'.$row['label'].'</div></li>'; 

$changeStatusSubMenu .= '</ul></li>';
    
array_push($arrNavbarButton, $changeStatusSubMenu);


// ===== print 
if(isset($obj->printMenu) && !empty($obj->printMenu)){
    $printSubMenu = '<li class="btn-action dropdown print-transaction">
                                <a href="#" data-toggle="dropdown" class="dropdown-toggle"><i class="fas fa-print" title="'.$obj->lang['print'].'"></i></a>
                                <ul class="dropdown-menu single-level">';

    foreach($obj->printMenu as $key => $row)
        $printSubMenu .= '<li class="menu-item"  title="'.$row['name'].'" rel-url="'.$row['url'].'"><div>'.$row['name'].'</div></li>'; 

    $printSubMenu .= '</ul></li>';
    
    array_push($arrNavbarButton, $printSubMenu);
}

 
// ===== page & search bar 
$searchPanel  = '<li class="navbar-right" style="position: relative">
                       '.$obj->inputText('quick-search', array('class' =>'form-control quick-search-text-box', 'etc' => 'placeholder="'.$obj->lang['search'].'" style="width:20em" ' ) ).' 
                        <div class="clear-text-panel"><i class="fas fa-spinner fa-spin loading-icon"></i><i class="fab fa-sistrix search-icon"></i><i class="far fa-times-circle clear-text-icon" ></i></div>
                 </li>';

array_push($arrNavbarButton, $searchPanel);

array_push($arrNavbarButton, '<li class="navbar-right"  style="margin-right:1em"><div style="float:left;">'.$obj->inputSelect('selPage', array(), array('etc' => 'style="padding:0em 0.5em;"')).'</div></li>');  
     

// ======================================================== NAVBAR  


?>


<script>
 	$(document).ready(function() {  
            
            var selectedTabId = selectedTab.newPanel[0].id;
            var selectedTabObj = $("#" + selectedTabId);
        
	 		tabParam[selectedTabId].phpDataListFile = "<?php echo  $FILE_NAME; ?>";
	 		tabParam[selectedTabId].addDataFile = "<?php echo  $addDataFile ; ?>"; 
			
            selectedTabObj.find(".tab-title").html(tabParam[selectedTabId].title);
           
			<?php 
			if (!isset($quickView) || $quickView == true)
				echo 'tabParam[selectedTabId].quickView = true;';
			else
				echo 'tabParam[selectedTabId].quickView = false;';
			?> 
			
            <?php 
                if ($obj->isTransaction){  
                      $rsTableInf = $obj->getTableKeyAndObj($obj->tableName,array('defaultstatus'));
                      if (empty($rsTableInf['defaultstatus'])){  
                          $defaultStatusFilter = array_keys($arrStatus);
                          array_pop($defaultStatusFilter);
                          $defaultStatusFilter = implode(',', $defaultStatusFilter);
                      }else {
                          $defaultStatusFilter = $rsTableInf['defaultstatus'] ;
                      }
                    
                      echo 'var defaultStatusFilter = Array('.$defaultStatusFilter.');';  
                      echo 'var temp = {\'selectedCriteriaKey\' : defaultStatusFilter, \'field\' : \'statuskey\'};';  
                      echo 'tabParam[selectedTabId][\'filterCriteria\'][0] = temp;';
                }  
            ?>
         
			updateData(false);
			   
            var navbarMenuItem = <?php  echo json_encode($arrNavbarButton); ?>;  
            var navbarMenu = ''; 
            for(var i=0; i<navbarMenuItem.length; i++){
                navbarMenu += navbarMenuItem[i];
            }
        
            selectedTabObj.find('.action-bar-navbar .navbar-nav').append(navbarMenu);
        
         
			// assign ID ke div data-list
			// blm tau kepake ap gk selanjutnya.... 
			selectedTabObj.find(".data-list").attr("id","data-list-"+selectedTabId);   
			
			//refresh button 
			selectedTabObj.find("#btn-refresh").attr("id","btn-refresh-"+selectedTabId);   
			$("#btn-refresh-" + selectedTabId).bind( "click", function( event ) { refreshDataList($(this),selectedTabId); });
        
            //marketplace download button 
			selectedTabObj.find("#btn-sync-marketplace").attr("id","btn-sync-marketplace-"+selectedTabId);   
			$("#btn-sync-marketplace-" + selectedTabId).bind( "click", function( event ) { refreshDataList($(this),selectedTabId, true); });
        
            //checkbox
            selectedTabObj.find("[name=chkRow-master]").bind( "change", function( event ) {  
                if($(this).prop("checked"))
				    selectAllRows();
                else
                    deselectAllRows();
			}); 
			
			//add button 
			selectedTabObj.find("#btn-add-new").attr("id","btn-add-new-"+selectedTabId); 
			$("#btn-add-new-"+selectedTabId).bind( "click", function( event ) {    
				 var title = encodeURI(selectedTab.newTab[0].textContent);
				 addTab("<i class=\"far fa-file-alt title-icon\" ></i>" + title ,"<?php echo $addDataFile ;?>?title=" + title + "&fileName=<?php echo $FILE_NAME; ?>&selectedPanelId="+selectedTabId); 
			});
			
			//edit button 
			selectedTabObj.find("#btn-edit-data").attr("id","btn-edit-data-"+selectedTabId); 
			$("#btn-edit-data-"+selectedTabId).bind( "click", function( event ) {  
				 openTabForEdit();
            });
			
			//delete button 
			selectedTabObj.find("#btn-delete").attr("id","btn-delete-"+selectedTabId); 
			$("#btn-delete-"+selectedTabId ).bind( "click", function( event ) {  
			 	 deleteData();
			});
			  
			selectedTabObj.find(".change-status .menu-item").bind( "click", function( event ) {   
				var statusKey = $(this).attr("rel-index");
				var statusName = $(this).attr("title");
				changeStatus(statusKey,statusName);
			});
        
                
			selectedTabObj.find(".print-transaction .menu-item").bind( "click", function( event ) {   
				var url = $(this).attr("rel-url"); 
				printTransaction(url);
			});
            
		 
			//paging
			selectedTabObj.find("[name=selPage]").attr("name","selPage-"+selectedTabId);   
			$("[name=selPage-"+selectedTabId +"]").bind( "change", function( event ) { updateData(false); }); 
			      
            //expand-all button 
			selectedTabObj.find("#btn-expand-all").attr("id","btn-expand-all-"+selectedTabId);   
			$("#btn-expand-all-" + selectedTab.newPanel[0].id).bind( "click", function( event ) {  toggleAllSelectedDataDetail() });
        
			//quick search
            selectedTabObj.find("[name=quick-search]").attr("name","quick-search-"+selectedTabId);
        
			//sortcolumn
			selectedTabObj.find(".sortable").bind( "click", function( event ) {sortColumn($(this),selectedTabId);}); 
            selectedTabObj.find(".clear-text-icon").bind( "click", function( event ) { clearText($(this),selectedTabId);}); 
            $("[name=quick-search-"+ selectedTabId +"]").bind( "keyup", function( event ) {  quickSearch($(this),selectedTabId); });
            $("[name=quick-search-"+ selectedTabId +"]").bind( "blur", function( event ) {  quickSearchOnLostFocus($(this),selectedTabId); });
        
            // preview column
            selectedTabObj.find(".data-list-settings .inputnumber").bind("blur", function(event) { inputNumberOnBlur($(this)); });
            selectedTabObj.find(".data-list-settings .inputnumber").bind("focus", function(event) { inputNumberOnFocus($(this)); });
            selectedTabObj.find(".data-list-settings [name='chkTitle[]']").bind("change", function( event ) {  updatePreview($(this)); });
            selectedTabObj.find(".data-list-settings [name='width[]']").bind("change", function( event ) {  updatePreview($(this)); });
            selectedTabObj.find("[name='btnSaveDataListSettings']").bind("click", function( event ) {  updateDataListSettings(selectedTabObj); })
            selectedTabObj.find(".btn-data-list-setting").bind("click", function( event ) { selectedTabObj.find(".data-list-settings").fadeToggle(300);  })
          
            selectedTabObj.find(".column-preview").sortable().disableSelection();
          
 
	}); 
</script>
<div class="panel-data-list">    
 <div class="container">
    <div style="clear:both;"></div>
    <div class="action-bar-fixed user-select-none">
        <div class="tab-title"></div> 
         
        <!-- NAVBAR --> 
        <div class="navbar navbar-default action-bar-navbar"><div id="navbar-collapse-grid" class="navbar-collapse collapse"> <ul class="nav navbar-nav"></ul> </div>  </div> 
         
        <div class="table-data-list">
            <div class="div-table-row">    
                <div class="div-table-col-5 col-header read-status-col" ></div>  
                <div style="width: 2em;" class="div-table-col-5 col-header"><input type="checkbox" name="chkRow-master"></div>
                 <?php	    
                for($j=0;$j<count($arrColumn);$j++){   

                    if(isset($arrColumn[$j]['title'])){
                                
                        $title = '';
                        if(!empty( $arrColumn[$j]['title']))
                            $title =  (strpos($arrColumn[$j]['title'], '[icon]' ) !== false) ? '<i class="icon '.str_replace('[icon]','',$arrColumn[$j]['title']).'"></i>': $obj->lang[$arrColumn[$j]['title']];
             
                        
                        $width = (!empty( $arrColumn[$j]['width'])) ?  'width:' .$arrColumn[$j]['width'].'px;' : '';
                        $textAlign = (!empty( $arrColumn[$j]['align'])) ?  'text-align:' .$arrColumn[$j]['align'].';' : '';
                        $dbfield = $arrColumn[$j]['dbfield'];
                       // $title = (!empty( $arrColumn[$j]['title'])) ?  $obj->lang[$arrColumn[$j]['title']]  : '';
                    }else{ 
                        $width = (!empty( $arrColumn[$j][2])) ?  'width:' .$arrColumn[$j][2].'px;' : '';
                        $textAlign = (!empty( $arrColumn[$j][3])) ?  'text-align:' .$arrColumn[$j][3].';' : '';
                        $dbfield = $arrColumn[$j][1];
                        $title = (!empty( $arrColumn[$j][0])) ?  $arrColumn[$j][0]  : '';
                    }

                    echo '<div style="'.$textAlign.' '.$width.' " class="div-table-col-5 col-header sortable" relcol="'.$dbfield.'" reltype="-1">'.$title.'<div class="order-type"></div></div>';
                }
                ?>	  
                <div style="text-align:center; width: 30px;" class="div-table-col-5 col-header">
                <?php if (isset($obj->arrDataListAvailableColumn) && !empty($obj->arrDataListAvailableColumn)) echo '<i class="fal fa-cog btn-data-list-setting"></i>'; ?>
                </div>
            </div>
        </div>  
        <?php if(isset($obj->arrDataListAvailableColumn) && !empty($obj->arrDataListAvailableColumn)){ ?>
        <div class="in-tab-overlay data-list-settings"> 
            <div class="data-list-settings-panel">
            <div style="clear:both; height: 1em"></div>  
            <div class="div-table" style="width:100%">
                <div class="div-table-row column-preview">
                <?php  
                   foreach($arrColumn as $columnRow){ 
                    $columnTitle =  (strpos($columnRow['title'], '[icon]' ) !== false) ? '<i class="icon '.str_replace('[icon]','',$columnRow['title']).'"></i>': $obj->lang[$columnRow['title']];
                       
                    $width = (isset($columnRow['width']) && !empty($columnRow['width']) ) ? ' width:'.$columnRow['width'].'px;' : '';
                    $textAlign = (isset($columnRow['align']) && !empty($columnRow['align']) ) ? ' text-align:'.$columnRow['align'].';' : '';
                    echo '<div class="div-table-col-5 column" style="'.$width.$textAlign.'" relcol="'.$columnRow['code'].'"> '.$columnTitle.'</div>';
                } ?>
                </div>
            </div>
            <div class="text-muted" style="margin-top:0.5em; font-style:italic"><?php echo $obj->lang['dragToReorder']; ?></div>
            <div style="clear:both; height: 2em"></div>
            <ul class="column-name-options">
                <?php
                        
                        $selectedColumn = array_column($arrColumn,null,'code');
                        foreach($obj->arrDataListAvailableColumn as $key => $row){  
                            $row['title'] = str_replace('[icon]','',$row['title']);
                            $obj->arrDataListAvailableColumn[$key]['langtitle'] = $obj->lang[$row['title']];
                        }
    
                        $availableColumn = $obj->sortMultiDimensionArray($obj->arrDataListAvailableColumn,'langtitle');
       
                        foreach($availableColumn as $columnRow){ 
                            
                             $code = $columnRow['code']; 
                             $title = $columnRow['langtitle'];

                             // ambil dr hasil save kalo ad 
                             $checked = false ;
                             if(isset($selectedColumn[$code])){ 
                                 $checked = true;
                                 $width = $selectedColumn[$code]['width'];
                             }else{  
                                 $width = $columnRow['width'];
                             }

                             $_POST['hidCode[]']  = $code;
                             $_POST['title[]']  = $title;
                             $_POST['width[]']  = $obj->formatNumber($width); 
                             $_POST['chkTitle[]']  = $checked;

                             echo '<li>
                                    <div class="div-table" style="width:98%"> 
                                        <div class="div-table-row"> 
                                            <div class="div-table-col" style="width: 25px; text-align:center">'.$obj->inputCheckBox('chkTitle[]').$obj->inputHidden('hidCode[]').'</div>
                                            <div class="div-table-col">'.$obj->inputNumber('title[]', array ('disabled' => true,'class' => 'form-control s-input')).'</div> 
                                            <div class="div-table-col" style="padding-left:0.5em;width: 50px; text-align:right">'.$obj->inputNumber('width[]', array ('etc' => 'style="text-align:right;"', 'class' => 'form-control inputnumber s-input')).'</div> 
                                            <div class="div-table-col" style="width: 10px; padding-left: 0.5em">px</div> 
                                        </div>
                                    </div>
                                    </li>';    
                            
                        }
     
                ?>
            </ul>
            <div style="clear:both; height: 1em"></div>
            <div style="padding:0.5em; text-align:center"><?php echo $obj->inputButton('btnSaveDataListSettings',ucwords($obj->lang['save'])); ?></div>
            </div>
        </div> 
        <?php  } ?>
    </div> 
    <div class="data-list-margin" style="clear:both;"></div>
    <div class="data-list"></div>
 </div>    
</div>