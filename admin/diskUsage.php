<?php
require_once '../_config.php'; 
require_once '../_include.php'; 
 
$obj = $class;

if(!$security->isAdminLogin('diskUsage',10,true));

$isQuickAdd = false;
$monthdateFormat = '%b %Y';

// get available month that has files
// start from first transaction

$arrTransactionTable = array();
array_push($arrTransactionTable, $truckingServiceOrder);
array_push($arrTransactionTable, $truckingCostCashOut);
array_push($arrTransactionTable, $itemOutDepot);
array_push($arrTransactionTable, $itemInDepot);

$arrSQL = array();
foreach($arrTransactionTable as $table){
    array_push($arrSQL,  'select trdate from '. $table->tableName);
}   

$sql = implode( ' UNION ' , $arrSQL );

// kalo file master, gk ad trdate, adanya createdon
$sql = 'select distinct(DATE_FORMAT(trdate, \''.$monthdateFormat.'\')) as datemonth, month(trdate) as month, year(trdate) as year from ('.$sql.') table_transaction order by trdate asc'; 
$rsMonthDate = $obj->oDbCon->doQuery($sql);

$arrYear = array();
foreach($rsMonthDate as $row){ 
    if(!in_array($row['year'], $arrYear))
        $arrYear[$row['year']] = $row['year']; 
}
  
$arrRows = array();
$totalDiskUsage = 0;
foreach($rsMonthDate as $datemonthRow){
    
    $arrFilePerPeriod = array(); 
    $monthIndex = $datemonthRow['datemonth']; 
    $size = 0;

    foreach($arrTransactionTable as $table){ 
    
        $sql = 'select pkey from '.$table->tableName.' where DATE_FORMAT(trdate, \''.$monthdateFormat.'\') = ' . $obj->oDbCon->paramString($monthIndex);
        $rs = $obj->oDbCon->doQuery($sql);
         
        foreach($rs as $row){
            // compability check
            if(isset($table->fileType)){
                foreach($table->fileType as $fileType){ 
                    $urlpath  = $obj->defaultDocUploadPath.$fileType['uploadFileFolder'].$row['pkey']; 
                    if(is_dir($urlpath))
                        array_push($arrFilePerPeriod,$urlpath);  
                }
            }else{   
                $urlpath  = $obj->defaultDocUploadPath.$table->uploadFileFolder.$row['pkey']; 
                if(is_dir($urlpath))
                    array_push($arrFilePerPeriod,$urlpath);  
            }
        }  
    }
     
    //start calculating
    foreach($arrFilePerPeriod as $folderPath){  
        $size += $obj->getFolderSize($folderPath);  
    }
 
    
    $totalDiskUsage += $size;
    
    if ($size > 0)
    array_push($arrRows, array(
                                'periodname' => $monthIndex, 
                                'month' =>  $datemonthRow['month'] , 
                                'year' => $datemonthRow['year'] , 
                                'size' =>  $size, 
                            ));
     
}
  
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>  
     
<script type="text/javascript">  
 	jQuery(document).ready(function(){   
        var tabID = selectedTab.newPanel[0].id; 
        var tabObj = $("#" + tabID); 
        
         tabObj.find("[name=btnDelete]").on('click',function() {  
             
            var btnObj = $(this);
            var selectedRow = btnObj.closest(".div-table-row");
            var relperiod =  btnObj.attr("relperiod");
            var month = btnObj.attr("relmonth");
            var year = btnObj.attr("relyear");
             
            $( "#dialog-message" ).html("Anda yakin akan menghapus file periode " +relperiod+ " ?");
            $( "#dialog-message" ).dialog({
              width: 300,
              modal: true,
              title:"Konfirmasi Hapus File", 
              open: function() {
                  $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
              }, 
              close:function() {},  
              buttons : {
                  OK : function (){ 
                             selectedRow.find(".delete-col").html('<i class="fas fa-spinner fa-spin"></i>'); 
                             $.ajax({
                                type: "POST",
                                url:  'ajax-disk-usage',
                                data: {
                                        action: 'delete',
                                        month:month,
                                        year:year, 
                                      }, 
                            }).done(function( data ) { 
                                selectedRow.remove(); 
                                 
                                var totalSize = 0;
                                tabObj.find("[name=\'hidSize[]\']").each(function(){   
                                    totalSize += parseFloat($(this).val());
                                });
                                 
                                tabObj.find(".total-size").html(totalSize).formatCurrency({roundToDecimalPlace: 2 });
                                 
                                updateDiskUsage();
                            });    

                            $( this ).dialog( "close" );
                  },
                  Cancel : function (){ 
                    $( this ).dialog( "close" );
                  }
              },
            }); 
         }); 
	}); 
</script>
<style>
    .months-row,.months-caption-row  {font-size: 1.5em;  color: #666; width: 100%}
    .months-row {border-top:1px solid #666; border-bottom:1px solid #666; }
    .months-row .div-table-row .div-table-col, .months-caption-row .div-table-row .div-table-col{padding: 0.5em} 
    .months-row .div-table-row .div-table-col {margin-top:1em}
    .months-row .delete-col {font-size: 0.7em}
    .months-row .delete-col i {font-size: 1.5em; margin-top: 0.3em}
</style>
</head> 

<body>
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
   <form id="defaultForm" method="post" class="form-horizontal" action="#">
    <div class="div-table months-caption-row">
        <div class="div-table-row">  
            <div class="div-table-col" style="text-align:right; font-weight: bold"><?php echo $obj->ucFirst($obj->lang['total']); ?></div> 
            <div class="div-table-col" style="text-align:right; width: 150px; font-weight: bold"><span class="total-size"><?php echo $obj->formatNumber($obj->convertSize($totalDiskUsage),2); ?></span> MB</div>
            <div class="div-table-col" style="width: 150px;"></div>
        </div>
    </div>   
   
    <?php  
        if(empty($arrRows)){ 
              echo '<div style="text-align:center; margin-top:5em;">'.$class->lang['noSpaceBeingUsed'].'</div>';  
        }else{
          echo '<div class="div-table transaction-detail months-row">';
          foreach($arrRows as $key=>$row){ 

                $size = $obj->convertSize($row['size']);

                $_POST['hidSize[]'] = $size;
                echo '<div class="div-table-row odd-style-adjustment">
                            <div class="div-table-col">'.$row['periodname'].$class->inputHidden('hidSize[]').'</div>
                            <div class="div-table-col" style="text-align:right; width: 150px;">'.$obj->formatNumber($size,2).' MB</div>
                            <div class="div-table-col delete-col" style="text-align:center; width: 150px;">'.$class->inputButton('btnDelete',$class->lang['delete'], array('etc' => 'relmonth="'.$row['month'].'" relyear="'.$row['year'].'" relperiod="'.$row['periodname'].'"' ,'class' => 'btn btn-primary btn-red-tone')).'</div>
                      </div>'; 
            }
          echo '</div>';
            
        }
        
       
    ?>  
    
   </form>
</div>
</body>

</html>