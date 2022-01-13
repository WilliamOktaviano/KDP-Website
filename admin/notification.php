<?php 
$notification = array();
$hasDiskUsageAccess = $security->isAdminLogin('diskUsage',10);

$warehouse = new Warehouse();

// license...
$rs = $security->checkLicense();
$showWarning = false;

if ($rs['duedate'] < 0){  
    $temp = array();
    $temp['css'] =  'bg-pastel-red text-red-cardinal';
    $temp['message'] = $rs['message']; 
    array_push($notification,$temp);
    $showWarning = true;
    
}else if ($rs['duedate'] <= 14){
    $temp = array();
    $temp['css'] =  'bg-pastel-orange text-ginger';
    $temp['message'] = $rs['message']; 
    array_push($notification,$temp);
    $showWarning = true;
    
} else{ 
    $temp = array();
    $temp['css'] =  'bg-pastel-green text-green-avocado';
    $temp['message'] = $rs['message']; 
    array_push($notification,$temp);
}
     

// plan type...
$rs = $security->getUserPlanType();

// USER
$maxuser = $rs[0]['maxuser']; 
$maxuser =  ($maxuser < 0) ? '&infin;' :  $class->formatNumber($maxuser);
$rsEmployee = $employee->searchData();
$maxuser = $obj->formatNumber(count($rsEmployee)) .' / '.$maxuser;


// WAREHOUSE
$maxwarehouse = $rs[0]['maxwarehouse'];
$maxwarehouse =  ($maxwarehouse < 0) ? '&infin;' :  $class->formatNumber($maxwarehouse);
$rsWarehouse = $warehouse->searchData();
$maxwarehouse = $obj->formatNumber(count($rsWarehouse)) .' / '.$maxwarehouse;

// PRODUCTS
$productmax = $rs[0]['maxproduct'];
$productmax =  ($productmax < 0) ? '&infin;' :  $class->formatNumber($productmax);
 
// PURCHASE
$maxpurchaseorder = $rs[0]['maxpurchaseorder'];
$maxpurchaseorder =  ($maxpurchaseorder < 0) ? '&infin;' :  $class->formatNumber($maxpurchaseorder);
 
// SALES
$maxsalesorder = $rs[0]['maxsalesorder'];
$maxsalesorder =  ($maxsalesorder < 0) ? '&infin;' :  $class->formatNumber($maxsalesorder);
  
 
// DISK USAGE
$maxdiskusage = $rs[0]['maxdiskusage'];
$unit = 'GB'; 
$maxdiskusage =  ($maxdiskusage < 0) ? '&infin;' :  $class->formatNumber($security->convertSize($maxdiskusage,$unit,'MB')) . ' '.$unit;
 

$webFrontEnd = ($rs[0]['usefrontend']) ? 'Ya' : 'Tidak';


$details = array();
array_push($details, array('title' => $class->lang['user'], 'value' => $maxuser ));
array_push($details, array('title' => $class->lang['item'], 'value' => $productmax, 'category' => array(1)));
array_push($details, array('title' => $class->lang['warehouse'], 'value' => $maxwarehouse));
array_push($details, array('title' => $class->lang['imagesPerItem'], 'value' => $class->formatNumber($rs[0]['maxproductimage'],0), 'category' => array(1)));
array_push($details, array('title' => $class->lang['imageSize'], 'value' => $class->formatNumber($rs[0]['maximagesize'],0). ' MB' , 'category' => array(1))) ;
array_push($details, array('title' => $class->lang['filesPerItem'], 'value' => $class->formatNumber($rs[0]['maxproductfile'],0), 'category' => array(1)));
array_push($details, array('title' => $class->lang['maxSizeUploadPerFile'], 'value' => $class->formatNumber($rs[0]['maxfilesize'],0). ' MB'));
 

$planDetail = '<div class="div-table">';

foreach($details as $row){ 
    if (isset($row['category']) && !in_array(PLAN_TYPE['categorykey'] ,$row['category']) ) continue; 
    $planDetail .= '<div class="div-table-row">
                        <div class="div-table-col">
                            <div class="row-header">'.$row['title'].'</div>
                            <div>'.$row['value'].'</div>
                        </div> 
                    </div>';
}
 
$planDetail .= '<div class="div-table-row">
                        <div class="div-table-col">
                            <div class="row-header">'.$class->lang['fileDiskUsage'].'</div>
                            <div><span class="file-disk-usage">'. $class->formatNumber($class->convertSize(($rs[0]['useddatabaseusage'] +  $rs[0]['useddiskusage']),'GB','MB'),2) .' GB / '.$maxdiskusage.'</div>';

if ($hasDiskUsageAccess) $planDetail .= '<div  style="margin-top:0.5em"><a href="javascript: openTab(\''.$class->lang['diskUsage'].'\',\'diskUsage\');">'.$class->lang['manage'].' '.$class->lang['diskUsage'].'</a></div>';

$planDetail .= '</div>  
                    </div>';
 

$planDetail .= '</div>';
 
/*
    <div class="div-table-row">
        <div class="div-table-col row-header">Jenis Plan</div>
        <div class="div-table-col">'.$rs[0]['name'].'</div>
    </div>
        <div class="div-table-row">
        <div class="div-table-col row-header">Maks. Transaksi Pembelian</div>
        <div class="div-table-col">'.$maxpurchaseorder.' / bln</div>
    </div>
    <div class="div-table-row">
        <div class="div-table-col row-header">Maks. Transaksi Penjualan</div>
        <div class="div-table-col">'.$maxsalesorder.' / bln</div>
    </div>
    */

$temp = array();
$temp['css'] =  'bg-light-gray text-black-jet';
$temp['message'] = $planDetail; 

array_push($notification,$temp);

$returnNotification = '';

if (!empty($notification)){
    $returnNotification = '<ul class="system-notification-list">';

    for($i=0;$i<count($notification);$i++){
        $returnNotification .= '<li class="'.$notification[$i]['css'].'">'.$notification[$i]['message'].'</li>'; 
    }
        
    $returnNotification .= '</ul>';  
    
    if ($showWarning)    
        $jqueryScript .= '$(".notification-icon .warning-icon").show();';
}

echo $returnNotification;
?>