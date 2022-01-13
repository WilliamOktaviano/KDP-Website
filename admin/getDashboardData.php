<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 


// harus disesuaikan dengan tipe client
includeClass(array(
                    'Marketplace.class.php', 
                    'Customer.class.php', 
                    'AP.class.php', 
                    'AR.class.php', 
                    'APEmployee.class.php', 
                    'AREmployee.class.php', 
                    'Car.class.php', 
                    'WidgetSetting.class.php', 
                    'ChartOfAccount.class.php', 
                    'PurchaseOrder.class.php', 
                    'CashBankRealization.class.php', 
                  ));

$customer = createObjAndAddToCol(new Customer()); 

$isActiveModule = $class->isActiveModule(array('TruckingServiceOrder','SalesOrder','Item'));

if($isActiveModule['truckingserviceorder']){ 
    
    includeClass(array( 
                    'TruckingServiceOrder.class.php',
                    'TruckingServiceWorkOrder.class.php',
                    'TruckingServiceOrderInvoice.class.php' 
                  ));
    
    $truckingServiceOrder = createObjAndAddToCol(new TruckingServiceOrder());   
    $truckingServiceOrderInvoice = createObjAndAddToCol(new TruckingServiceOrderInvoice()); 
    $truckingServiceWorkOrder = createObjAndAddToCol(new TruckingServiceWorkOrder()); 
}


if($isActiveModule['salesorder']){ 
        
    includeClass(array( 'SalesOrder.class.php',  ));
    $salesOrder = createObjAndAddToCol(new SalesOrder()); 
}


if($isActiveModule['item']){ 
includeClass('Item.class.php');
$item = createObjAndAddToCol(new Item());  
}

$widgetSetting = createObjAndAddToCol(new WidgetSetting()); 
    
$rowsLimit = 25;
$amountIn = 1000;

$footerTemplate = '
<div class="footer">
    <div class="flex">
        <div class="consume">{{FOOTER}}</div>
        <div class="icon-panel"><i class="fal fa-cog"></i> <i class="fal fa-times remove-widget"></i></div>
    </div>
</div>';

$templatePanel = '<div class="content">
<div class="title">{{TITLE}}</div>
<div class="body">{{CONTENT}}</div>
'.$footerTemplate.'
</div>';

$templateGraphPanel = '<div class="content">{{CONTENT}}</div>'.$footerTemplate; 

if(!isset($_POST) || empty($_POST['data'])) die;
	  
$arrReturn = array();
foreach($_POST['data'] as $data){
    //$class->setLog($data['action'],true);
        
    $arrayToJs = array();   
    switch ($data['action']){ 
        case 'salesGraph' :  
        case 'truckingSalesGraph' :  
            $arrayToJs = generatePanelSalesGraph($data['action'], $data['startPeriod'],$data['endPeriod']);  
            break;
        case 'marketplace' :  
            $arrayToJs = generatePanelMarketplace($data['action']);  
            break;
        case 'profitByItemGraph' :  
            $arrayToJs = generatePanelProfitByItemGraph($data['action'],$data['startPeriod'],$data['endPeriod']);  
            break;
        case 'profitByCategoryGraph' :  
            $arrayToJs = generatePanelProfitByCategoryGraph($data['action'],$data['startPeriod'],$data['endPeriod']);  
            break;
        case 'profitByBrandGraph' :  
            $arrayToJs = generatePanelProfitByBrandGraph($data['action'],$data['startPeriod'],$data['endPeriod']);  
            break; 
        case 'bestSellingGraph' :  
            $arrayToJs = generatePanelBestSellingGraph($data['action'],$data['startPeriod'],$data['endPeriod']);  
            break; 

        case 'topCustomerGraph' : 
        case 'truckingTopCustomerGraph' :  
            $arrayToJs = generatePanelTopCustomerGraph($data['action'],$data['startPeriod'],$data['endPeriod']);  
            break;

         case 'pendingPurchaseOrder' :  
            $arrayToJs = generatePanelPendingPurchaseOrder($data['action']);  
            break;

         case 'pendingSalesOrder' :  
            $arrayToJs = generatePanelPendingSalesOrder($data['action']);  
            break;

        case 'minStock' :  
            $arrayToJs = generatePanelMinStock($data['action']);  
            break;

         case 'maxStock' :  
            $arrayToJs = generatePanelMaxStock($data['action']);  
            break;

         case 'notMovingStock' :  
            $arrayToJs = generatePanelNotMovingStock($data['action']);  
            break;

         case 'emptyStock' :  
            $arrayToJs = generatePanelEmptyStock($data['action']);  
            break;

        case 'overdueAP' :  
            $arrayToJs = generatePanelOverdueAP($data['action']);  
            break;

        case 'overdueAR' :  
            $arrayToJs = generatePanelOverdueAR($data['action']);  
            break;

        case 'underMarginSalesOrder' :  
            $arrayToJs = generatePanelUnderMarginSalesOrder($data['action']);  
            break;

        case 'truckingServiceOrderStatus' :   
            $bgDiv = '<div style="font-size:7em; position:absolute; right:0.3em; bottom: 0.1em; color: rgba(255,255,255, 0.1)"><i class="far fa-edit"></i></div>';
            $arrayToJs = generateTransactionStatus($class->lang['jobOrder'],$truckingServiceOrder, $data['startPeriod'],$data['endPeriod'],$bgDiv);  
            break;

        case 'truckingServiceWorkOrderStatus' :   
            $bgDiv = '<div style="font-size:7em; position:absolute; right:0.3em; bottom: 0.1em; color: rgba(255,255,255, 0.1)"><i class="fas fa-tasks"></i></div>';
            $arrayToJs = generateTransactionStatus($class->lang['WorkOrder'],$truckingServiceWorkOrder, $data['startPeriod'],$data['endPeriod'],$bgDiv);  
            break; 

        case 'truckingServiceOrderInvoiceStatus' :   
            $bgDiv = '<div style="font-size:7em; position:absolute; right:0.3em; bottom: 0.1em; color: rgba(255,255,255, 0.1)"><i class="fas fa-receipt"></i></div>';
            $arrayToJs = generateTransactionStatus($class->lang['salesInvoice'],$truckingServiceOrderInvoice, $data['startPeriod'],$data['endPeriod'], $bgDiv);  
            break;

        case 'truckingCostRevenueGraph' :    
            $arrayToJs = generatePanelTruckingCostRevenueGraph($data['action'],$data['startPeriod'],$data['endPeriod']);   
            break;

        case 'truckingVehicleOverdue' :    
            $arrayToJs = generatePanelVehicleOverdue($data['action']);   
            break;

        case 'dailyTransactionSummary' :
            $arrayToJs = generatePanelDailyTransactionSummary($data['action']);   
            break;
        case 'dailyMarketplaceTransactionSummary' :
            $arrayToJs = generatePanelDailyMarketplaceTransactionSummary($data['action']);   
            break;
        case 'cashBankOutstanding' :
            $arrayToJs = generatePanelCashBankOutstanding($data['action']);   
            break; 
        case 'topCustomerByJO' :
            $arrayToJs = generatePanelTopCustomerByJO($data['action'],$data['startPeriod'],$data['endPeriod']);  
            break;
            
        case 'cashBankRealizationSummary' :
            $arrayToJs = generatePanelCashBankRealizationSummary($data['action']);  
            break;
        case 'customerCreditLimitSummary' :
            $arrayToJs = generatePanelCustomerCreditLimitSummary($data['action']);  
            break;
    } 
    
    $arrReturn[$data['action']] = $arrayToJs; 
}

echo json_encode($arrReturn);   
die;
 
function generatePanelSalesGraph($panelName, $startPeriod, $endPeriod){ 
   
    global $class;   
    global $amountIn;
    global $security;
    global $customer;
    global $item;
    
    $hasCOGSAccess = $security->isAdminLogin($item->cogsSecurityObject,10);
    
    $arrTitle = array();
    array_push($arrTitle, $class->lang['period']);
    array_push($arrTitle, $class->lang['sales']);
      
    $arrData = array();

    switch (PLAN_TYPE['categorykey']){
        case 1 :    global $salesOrder;
                    $obj = $salesOrder;  
                     
                    // kalo ad marketplace
                    $rsMarketplaceCustomer = $customer->searchData($customer->tableName.'.ismarketplace',1,true); // gk peduli aktif atau gk
                    $rsMarketplaceCustomer = array_column($rsMarketplaceCustomer,'pkey'); 
            
                    if(!empty($rsMarketplaceCustomer))  array_push($arrTitle, $class->lang['marketplace']);
                    if($hasCOGSAccess)  array_push($arrTitle, $class->lang['profit']);
            
                    //all sales
                    array_push($arrData, $obj->getSalesByMonth( $startPeriod, $endPeriod ));  
                     
                    //marketplace sales
                    if(!empty($rsMarketplaceCustomer))  
                    array_push($arrData, $obj->getSalesByMonth( $startPeriod, $endPeriod, ' and '.$obj->tableName.'.customerkey in ('.$obj->oDbCon->paramString($rsMarketplaceCustomer,',').')' ));  
                     
                    // COGS
                    if($hasCOGSAccess) 
                        array_push($arrData, $obj->getProfitByMonth( $startPeriod, $endPeriod )); 
            
                    break;
        case 2 :    global $truckingServiceOrder;
                    global $truckingServiceOrderInvoice;
                    $obj = $truckingServiceOrder;
             

                    $arrTitle = array();
                    array_push($arrTitle, $class->lang['period']);
                    array_push($arrTitle, $class->lang['jobOrder']);
            
                    if($hasCOGSAccess)  array_push($arrTitle, $class->lang['cost']);
                    
                    array_push($arrTitle, $class->lang['invoice']);
             
                    array_push($arrData, $obj->getJobOrderByMonth( $startPeriod, $endPeriod ));
                
                    if($hasCOGSAccess) array_push($arrData, $obj->getTruckingCostByMonth( $startPeriod, $endPeriod )); 
                    
                    array_push($arrData, $truckingServiceOrderInvoice->getInvoiceByMonth( $startPeriod, $endPeriod ));  
            
                    break;
        case 3 :    global $salesOrderCarService;
                    $obj = $salesOrderCarService;
                    break;   
    }
 
     
    $title = $obj->lang['salesGraph'].' '.$obj->lang['inThousand']; 
  
    $arrayToJs = array();  
    array_push($arrayToJs,$arrTitle) ; 
    
    $rsDataPeriod = array();
    
    for ($ctr=0;$ctr<count($arrData);$ctr++){   
        $rs = $arrData[$ctr];
        for($i=0;$i<count($rs);$i++)  
            $rsDataPeriod[$rs[$i]['month'] .$rs[$i]['year']][$ctr] = (isset($rs[$i][$ctr])) ? $rs[$i]['total'] : 0;  
    }
    
    $period = $obj->getMonthPeriod($startPeriod, $endPeriod);
    
    foreach ($period as $dt) {
        
        $keyIndex = $dt->format('nY'); 
       
        $tempArray = array();
        array_push($tempArray, $dt->format('M Y'));
         
        for ($ctr=0;$ctr<count($arrData);$ctr++){   
            $value = (isset($rsDataPeriod[$keyIndex][$ctr])) ? $rsDataPeriod[$keyIndex][$ctr] / $amountIn  : 0 ;
            array_push($tempArray, $value); 
        }
        
        array_push($arrayToJs,$tempArray) ; 
    }
    
    $arrParam = array();
    $arrParam['data'] = $arrayToJs;
    $arrParam['callbackName'] = 'drawSalesChart';
    $arrParam['panelName'] = $panelName;
    $arrParam['title'] = $title ;
     
    return generateLineChart($arrParam); 
        
}
 
function generatePanelProfitByCategoryGraph($panelName,$startPeriod, $endPeriod){ 
   
    global $class;    
    global $item;
    global $amountIn;
    global $templateGraphPanel;
    global $security;
    
    $hasCOGSAccess = $security->isAdminLogin($item->cogsSecurityObject,10);
    if(!$hasCOGSAccess) return;
    
    $arrTitle = array();
    array_push($arrTitle, $class->lang['category']);
    array_push($arrTitle, $class->lang['amount']); 
     
    switch (PLAN_TYPE['categorykey']){
        case 1 :    global $salesOrder;
                    $obj = $salesOrder; 
                    break;
        case 2 :    global $truckingServiceOrder;
                    $obj = $truckingServiceOrder; 
                    break;
        case 3 :    global $salesOrderCarService;
                    $obj = $salesOrderCarService;
                    break;  
    }
    
    
    $title = $class->lang['profitByCategory']; 
  
    $rs = $obj->getMostProfitableSalesByGroup($item->tableName.'.categorykey', $startPeriod, $endPeriod ); 
      
    $arrayToJs = array(); 
    array_push($arrayToJs,$arrTitle) ; 
    
    for ($i=0;$i<count($rs);$i++){   
        $rs[$i]['profit'] = ($rs[$i]['profit'] > 0) ? $rs[$i]['profit']  / $amountIn : 0 ;
        
        $tempArray = array();
        array_push($tempArray, htmlspecialchars_decode($rs[$i]['categoryname']));
        array_push($tempArray, round($rs[$i]['profit']));
        array_push($arrayToJs,$tempArray) ; 
    }
    
     
    $arrParam = array();
    $arrParam['data'] = $arrayToJs;
    $arrParam['callbackName'] = 'drawProfitByCategoryChart';
    $arrParam['panelName'] = $panelName;
    $arrParam['title'] = $title ;
    
    $html = generateBarChart($arrParam);
    
    $replacement = array(); 
    $replacement['title'] = '';   
    $replacement['content'] = '';   
    $replacement['footer'] = $startPeriod.' - '.$endPeriod;    
    return replaceContent($replacement, $templateGraphPanel).$html;  
}
 
function generatePanelProfitByBrandGraph($panelName, $startPeriod, $endPeriod){ 

    global $class;   
    global $item;
    global $amountIn;
    global $templateGraphPanel;
    global $security; 
    
    $hasCOGSAccess = $security->isAdminLogin($item->cogsSecurityObject,10);
    if(!$hasCOGSAccess) return;
        
    $arrTitle = array();
    array_push($arrTitle, $class->lang['brand']);
    array_push($arrTitle, $class->lang['amount']); 
    
    
    switch (PLAN_TYPE['categorykey']){
        case 1 :    global $salesOrder;
                    $obj = $salesOrder; 
                    break;
        case 3 :    global $salesOrderCarService;
                    $obj = $salesOrderCarService;
                    break;  
    }
     
    $title = $class->lang['profitByBrand'];
  
    $rs = $obj->getMostProfitableSalesByGroup($item->tableName.'.brandkey', $startPeriod, $endPeriod); 
     
    if (empty($rs)) 
        return '<div class="data-chart-not-available">'.$title.'<div class="text-silver">'. strtolower($obj->lang['chartNotAvailable']).'</div></div>';
      
    $arrayToJs = array(); 
    array_push($arrayToJs,$arrTitle) ; 
    
    for ($i=0;$i<count($rs);$i++){ 
        $rs[$i]['profit'] = ($rs[$i]['profit'] > 0) ? $rs[$i]['profit']  / $amountIn : 0 ;
        
        $tempArray = array();
        array_push($tempArray, htmlspecialchars_decode($rs[$i]['brandname']));
        array_push($tempArray, round($rs[$i]['profit']));
        array_push($arrayToJs,$tempArray) ; 
    }
    
    $arrParam = array();
    $arrParam['data'] = $arrayToJs;
    $arrParam['callbackName'] = 'drawProfitByBrandChart';
    $arrParam['panelName'] = $panelName;
    $arrParam['title'] = $title ;
    
    $html = generateBarChart($arrParam);

    $replacement = array(); 
    $replacement['title'] = '';   
    $replacement['content'] = '';   
    $replacement['footer'] = $startPeriod.' - '.$endPeriod;    
    return replaceContent($replacement, $templateGraphPanel).$html;  
}

function generatePanelProfitByItemGraph($panelName, $startPeriod, $endPeriod){ 
   
    global $class;    
    global $item; 
    global $amountIn;
    global $templateGraphPanel;
    global $security;
    
    $hasCOGSAccess = $security->isAdminLogin($item->cogsSecurityObject,10);
    if(!$hasCOGSAccess) return;
        
    $arrTitle = array();
    array_push($arrTitle, $class->lang['item']);
    array_push($arrTitle, $class->lang['amount']); 
    
    switch (PLAN_TYPE['categorykey']){
        case 1 :    global $salesOrder;
                    $obj = $salesOrder; 
                    break;
        case 3 :    global $salesOrderCarService;
                    $obj = $salesOrderCarService;
                    break;  
    }
     
    $title = $class->lang['profitByItem'];
      
    $rs = $obj->getMostProfitableSalesByGroup($item->tableName.'.pkey', $startPeriod, $endPeriod); 
 
    $arrayToJs = array(); 
    array_push($arrayToJs,$arrTitle) ; 
    
    for ($i=0;$i<count($rs);$i++){ 
        $rs[$i]['profit'] = ($rs[$i]['profit'] > 0) ? $rs[$i]['profit']  / $amountIn : 0 ;
        
        $tempArray = array();
        array_push($tempArray, htmlspecialchars_decode($rs[$i]['itemname']));
        array_push($tempArray, round($rs[$i]['profit']));
        array_push($arrayToJs,$tempArray) ; 
    }
      
    $arrParam = array();
    $arrParam['data'] = $arrayToJs;
    $arrParam['callbackName'] = 'drawProfitByItemChart';
    $arrParam['panelName'] = $panelName;
    $arrParam['title'] = $title ; 
     
    $html = generateBarChart($arrParam);
    
    $replacement = array(); 
    $replacement['title'] = '';   
    $replacement['content'] = '';   
    $replacement['footer'] = $startPeriod.' - '.$endPeriod;    
    return replaceContent($replacement, $templateGraphPanel).$html;  
      
}

function generatePanelBestSellingGraph($panelName,$startPeriod, $endPeriod){ 
    
    global $class;    
    global $item; 
    global $templateGraphPanel;
        
    $arrTitle = array();
    array_push($arrTitle, $class->lang['item']);
    array_push($arrTitle, $class->lang['qty']); 
    
    switch (PLAN_TYPE['categorykey']){
        case 1 :    global $salesOrder;
                    $obj = $salesOrder; 
                    break;
        case 2 :    global $truckingServiceOrderInvoice;
                    $obj = $truckingServiceOrderInvoice;
                    break;  
        case 3 :    global $salesOrderCarService;
                    $obj = $salesOrderCarService;
                    break;  
    }
     
    $title = $class->lang['bestSellingItems'];
      
    $rs = $obj->getBestSellingByGroup($item->tableName.'.pkey', $startPeriod, $endPeriod); 
 
    $arrayToJs = array(); 
    array_push($arrayToJs,$arrTitle) ; 
     
    for ($i=0;$i<count($rs);$i++){  
        $tempArray = array();
        array_push($tempArray, htmlspecialchars_decode($rs[$i]['itemname']));
        array_push($tempArray, round($rs[$i]['qty']));
        array_push($arrayToJs,$tempArray) ; 
    }
    
    $arrParam = array();
    $arrParam['data'] = $arrayToJs;
    $arrParam['callbackName'] = 'drawBestSellingItemChart';
    $arrParam['panelName'] = $panelName;
    $arrParam['title'] = $title ;
           
    $html = generateBarChart($arrParam);
    
    $replacement = array(); 
    $replacement['title'] = '';   
    $replacement['content'] = '';   
    $replacement['footer'] = $startPeriod.' - '.$endPeriod;    
    return replaceContent($replacement, $templateGraphPanel).$html;  
}

function generatePanelTopCustomerGraph($panelName,$startPeriod, $endPeriod){ 
    
    global $class;   
    global $customer;
    global $amountIn;
    global $templateGraphPanel;
    
    $arrTitle = array();
    array_push($arrTitle, $class->lang['customer']);
    array_push($arrTitle, $class->lang['amount']); 
    
    switch (PLAN_TYPE['categorykey']){
        case 1 :    global $salesOrder;
                    $obj = $salesOrder; 
                    break;
        case 2 :    global $truckingServiceOrderInvoice;
                    $obj = $truckingServiceOrderInvoice; 
                    break;
        case 3 :    global $salesOrderCarService;
                    $obj = $salesOrderCarService;
                    break;  
    }
      
    $title = $obj->lang['topCustomers'];
      
    $rs = $obj->getBestSalesAmountByGroup($customer->tableName.'.pkey', $startPeriod, $endPeriod);  
    
    $arrayToJs = array(); 
    array_push($arrayToJs,$arrTitle) ; 
     
    for ($i=0;$i<count($rs);$i++){  
        $rs[$i]['amount'] = ($rs[$i]['amount'] > 0) ? $rs[$i]['amount']  / $amountIn : 0 ;
        
        $tempArray = array();
        array_push($tempArray, htmlspecialchars_decode($rs[$i]['customername']));
        array_push($tempArray, round($rs[$i]['amount']));
        array_push($arrayToJs,$tempArray) ; 
    }
        
    $arrParam = array();
    $arrParam['data'] = $arrayToJs;
    $arrParam['callbackName'] = 'drawTopCustomerChart';
    $arrParam['panelName'] = $panelName;
    $arrParam['title'] = $title ;
     
    $html = generateBarChart($arrParam);
      
    $replacement = array(); 
    $replacement['title'] = '';   
    $replacement['content'] = '';   
    $replacement['footer'] = $startPeriod.' - '.$endPeriod;    
    return replaceContent($replacement, $templateGraphPanel).$html; 
     
    
}

function generatePanelTruckingCostRevenueGraph($panelName,$startPeriod, $endPeriod){

    global $class;   
    global $truckingServiceOrder; 
    global $amountIn;
    global $templateGraphPanel;
    
    $arrTitle = array();
    array_push($arrTitle, $class->lang['cost']);
    array_push($arrTitle, $class->lang['revenue']); 
    
    switch (PLAN_TYPE['categorykey']){
        case 1 :    global $salesOrder;
                    $obj = $salesOrder; 
                    break;
        case 2 :    global $truckingServiceOrder;
                    $obj = $truckingServiceOrder; 
                    break;
        case 3 :    global $salesOrderCarService;
                    $obj = $salesOrderCarService;
                    break;  
    }
      
    $title =  ''; //$obj->lang['cost'];
      
    $rs = $obj->getTruckingCostRevenueAmount($startPeriod, $endPeriod);  
    
    $arrayToJs = array(); 
    array_push($arrayToJs,$arrTitle) ; 
     
    //for ($i=0;$i<count($rs);$i++){  
        //$rs[$i]['amount'] = ($rs[$i]['amount'] > 0) ? $rs[$i]['amount']  / $amountIn : 0 ;
        
        $rs[0]['revenueamount'] = ($rs[0]['revenueamount']  > 0) ? $rs[0]['revenueamount']   / $amountIn : 0 ;
        $tempArray = array();
        array_push($tempArray,$class->lang['revenue']);
        array_push($tempArray,$rs[0]['revenueamount']);
        array_push($arrayToJs,$tempArray) ; 
    
        $rs[0]['costamount'] = ($rs[0]['costamount']  > 0) ? $rs[0]['costamount']   / $amountIn : 0 ;
        $tempArray = array();
        array_push($tempArray,$class->lang['cost']);
        array_push($tempArray,$rs[0]['costamount']);
        array_push($arrayToJs,$tempArray) ; 
        
    //} 
    
    $arrParam = array();
    $arrParam['data'] = $arrayToJs;
    $arrParam['callbackName'] = 'drawTruckingCostRevenueChart';
    $arrParam['panelName'] = $panelName;
    $arrParam['title'] = $title ;
    
    $html = generatePieChart($arrParam,array('pieHole'=>'0.4', 'legendPos' => 'bottom'));
  
    $replacement = array(); 
    $replacement['title'] = '';   
    $replacement['content'] = '';   
    $replacement['footer'] = $startPeriod.' - '.$endPeriod;    
    return replaceContent($replacement, $templateGraphPanel).$html; 
    
}

function generatePanelPendingPurchaseOrder($panelName,$rowsLimit = 10){ 
    
    $purchaseOrder = createObjAndAddToCol(new PurchaseOrder()); 
    
    global $security;
    global $templatePanel;
    
    $obj = $purchaseOrder;
    
    if(!$security->isAdminLogin($obj->securityObject,10)) return;
     
    $rs =  $obj->searchData($obj->tableName.'.statuskey',1,true,'','order by '.$obj->tableName.'.trdate desc','limit 0,'.  $rowsLimit );

    $content = '<div class="div-table table-with-border" style="width:100%">';

    for ($i=0;$i<count($rs);$i++){ 
        $content .='     
        <div class="div-table-row">
        <div class="div-table-col-3" style="width:8em">'.$rs[$i]['code'].'</div>
        <div class="div-table-col-3" style="width:7.5em; text-align:center">'.$obj->formatDbDate($rs[$i]['trdate']).'</div>
        <div class="div-table-col-3">'.$rs[$i]['suppliername'].'</div>
        </div>
        ';  
    }
        
    $content .= '</div>';
    
    $replacement = array();
    $replacement['title'] =$obj->lang['unproccesedPurchaseOrder'];  
    $replacement['content'] = $content;  

     return replaceContent($replacement, $templatePanel);
}

function generatePanelPendingSalesOrder($panelName,$rowsLimit = 10){ 
    
    global $security;
    global $templatePanel; 
   
    switch (PLAN_TYPE['categorykey']){
        case 1 :    global $salesOrder;
                    $obj = $salesOrder; 
                    break;
        case 2 :    global $truckingServiceOrder;
                    $obj = $truckingServiceOrder;
                    break;  
        case 3 :    global $salesOrderCarService;
                    $obj = $salesOrderCarService;
                    break;  
    }
    
    if(!$security->isAdminLogin($obj->securityObject,10)) return;
   
    $rs =  $obj->searchData($obj->tableName.'.statuskey',1,true,'','order by '.$obj->tableName.'.trdate desc','limit 0,'.  $rowsLimit );

    $content = '<div class="div-table table-with-border" style="width:100%">';

    for ($i=0;$i<count($rs);$i++){ 
        $content .='     
        <div class="div-table-row">
        <div class="div-table-col-3" style="width:8em">'.$rs[$i]['code'].'</div>
        <div class="div-table-col-3" style="width:7.5em; text-align:center">'.$obj->formatDbDate($rs[$i]['trdate']).'</div>
        <div class="div-table-col-3"  >'.$rs[$i]['customername'].'</div>
        <div class="div-table-col-3" style="width:7.5em; text-align:right" >'.$obj->formatNumber($rs[$i]['beforetaxtotal']).'</div>
        </div>
        '; 
    }
        
    $content .= '</div>';
    
    $replacement = array();
    $replacement['title'] =$obj->lang['unproccesedSalesOrder'];  
    $replacement['content'] = $content;  

    return replaceContent($replacement, $templatePanel);  
}

function generatePanelMinStock($panelName,$rowsLimit = 10){ 
    global $item;
    global $security;
    global $templatePanel;
    
    $obj = $item;
    
    if(!$security->isAdminLogin($obj->securityObject,10))   return;
   
    $rs =  $obj->searchData($obj->tableName.'.statuskey','1',true,' and minstockqty > 0 ','order by name asc','limit 0,'.  $rowsLimit  ,' having qtyonhand < minstockqty'); // .$obj->oDbCon->paramString($minStock) );

    $content = '<div class="div-table table-with-border" style="width:100%">';

    for ($i=0;$i<count($rs);$i++){  
        $content .='     
        <div class="div-table-row">
        <div class="div-table-col-3" >'.$rs[$i]['name'].'</div>
        <div class="div-table-col-3" style="width: 4em; text-align:right">'.$obj->formatNumber($rs[$i]['qtyonhand']).'</div>
        </div>
        '; 
    }
        
    $content .= '</div>';
      
    $replacement = array();
    $replacement['title'] = $obj->lang['lowStock'];  
    $replacement['content'] = $content;  

    return replaceContent($replacement, $templatePanel);
     
} 

function generatePanelMaxStock($panelName,$rowsLimit = 10){ 
    global $item;
    global $security;
    global $templatePanel; 
    
    $obj = $item;
      
    $rs =  $obj->searchData($obj->tableName.'.statuskey','1',true,' and maxstockqty > 0 ','order by qtyonhand desc','limit 0,'.  $rowsLimit  ,' having qtyonhand > maxstockqty '); // .$obj->oDbCon->paramString($maxStock) );

    $content = '<div class="div-table table-with-border" style="width:100%">';

    for ($i=0;$i<count($rs);$i++){ 
          
        $content .='     
        <div class="div-table-row">
        <div class="div-table-col-3" >'.$rs[$i]['name'].'</div>
        <div class="div-table-col-3" style="width: 4em; text-align:right">'.$obj->formatNumber($rs[$i]['qtyonhand']).'</div>
        </div>
        '; 
    }
        
    $content .= '</div>';
      
    $replacement = array();
    $replacement['title'] = $obj->lang['overStock'];  
    $replacement['content'] = $content;   

    return replaceContent($replacement, $templatePanel);
     
}  

function generatePanelEmptyStock($panelName,$rowsLimit = 10){ 
    global $item;
    global $security;
    global $templatePanel;  
    
    $obj = $item;
     
    $rs = $obj->getLatestEmptyStock($rowsLimit);
    
    $content = '<div class="div-table table-with-border" style="width:100%">';

    for ($i=0;$i<count($rs);$i++){  
        $content .='     
        <div class="div-table-row">
            <div class="div-table-col-3">'.$rs[$i]['name'].'</div> 
            <div class="div-table-col-3" style="width: 10em; text-align:center">'.$obj->formatDBDate($rs[$i]['createdon'],'d / m / Y H:i').'</div> 
        </div>
        '; 
    }
        
    $content .= '</div>';
      
    $replacement = array();
    $replacement['title'] = $obj->lang['emptyStock'];  
    $replacement['content'] = $content;  

    return replaceContent($replacement, $templatePanel);
     
} 
 
function generatePanelNotMovingStock($panelName,$rowsLimit = 10){
    global $item;
    global $templatePanel;
    
    $monthInterval = 3;
    
    $rs = $item->getNotMovingStock($monthInterval,$rowsLimit);
    
    $content = '<div class="div-table table-with-border" style="width:100%">';
    for ($i=0;$i<count($rs);$i++){  
        $content .='     
        <div class="div-table-row">
        <div class="div-table-col-3">'.$rs[$i]['name'].'</div>
        <div class="div-table-col-3" style="width:2em; text-align:right;">'.$item->formatNumber($rs[$i]['qtyonhand']).'</div> 
        <div class="div-table-col-3" style="width:4em">'. $rs[$i]['baseunitname'] .'</div> 
        </div>
        '; 
    }
        
    $content .= '</div>';
    
    $replacement = array();
    $replacement['title'] = $item->lang['notMovingStock'];  
    $replacement['content'] = $content;  

    return replaceContent($replacement, $templatePanel);
}

function generatePanelOverdueAP($panelName,$rowsLimit = 10){  
    $ap = createObjAndAddToCol(new AP());  
    global $security; 
    global $templatePanel; 
    
    $obj = $ap;
      
    $rs =  $obj->searchData('','',true,' and (' . $obj->tableName.'.statuskey in (1,2) ) and duedate <  date(now())','order by duedate asc' , 'limit 0,'.  $rowsLimit);

    $content = '<div class="div-table table-with-border" style="width:100%">';
    
    for ($i=0;$i<count($rs);$i++){ 
         
        $overrideClass = ($rs[$i]['statuskey'] == 2) ? 'text-princeton-orange' : '';
        
        //<div class="div-table-col-3" style="width:8em; text-align:center">'.$obj->formatDbDate($rs[$i]['duedate']).'</div>
        $content .='     
        <div class="div-table-row '.$overrideClass.'">
        <div class="div-table-col-3" style="width:7em">'.$rs[$i]['code'].'</div>
        <div class="div-table-col-3" >'.$rs[$i]['suppliername'].'</div>
        <div class="div-table-col-3" style="width:5em;text-align:right">'.$obj->formatNumber($rs[$i]['outstanding']).'</div>
        </div>
        '; 
    }
        
    $content .= '</div>';
    
    $replacement = array();
    $replacement['title'] =$obj->lang['overdueAccountsPayable'];  
    $replacement['content'] = $content;  

     return replaceContent($replacement, $templatePanel);
}

function generatePanelOverdueAR($panelName,$rowsLimit = 10){ 
    
    $ar = createObjAndAddToCol(new AR()); 
    
    global $security; 
    global $templatePanel;  
    
    $obj = $ar;
      
    $rs =  $obj->searchData('','',true,' and (' . $obj->tableName.'.statuskey = 1 or ' . $obj->tableName.'.statuskey = 2) and duedate <  date(now())','order by duedate asc', 'limit 0,' . $rowsLimit );

    $content = '<div class="div-table table-with-border" style="width:100%">';

    for ($i=0;$i<count($rs);$i++){ 
        
        $overrideClass = ($rs[$i]['statuskey'] == 2) ? 'text-princeton-orange' : '';
        
        //<div class="div-table-col-3" style="width:8em; text-align:center">'.$obj->formatDbDate($rs[$i]['duedate']).'</div>
        $content .='     
        <div class="div-table-row '.$overrideClass.'">
        <div class="div-table-col-3" style="width:7em">'.$rs[$i]['code'].'</div>
        <div class="div-table-col-3" >'.$rs[$i]['customername'].'</div>
        <div class="div-table-col-3" style="width:5em; text-align:right">'.$obj->formatNumber($rs[$i]['outstanding']).'</div>
        </div>
        '; 
    }
        
    $content .= '</div>';
    
    $replacement = array();
    $replacement['title'] =$obj->lang['overdueAccountsReceivable'];  
    $replacement['content'] = $content;  

     return replaceContent($replacement, $templatePanel);
}  

function generatePanelMarketplace($panelName){
    
    $marketplace = createObjAndAddToCol(new Marketplace()); 
    
    global $templatePanel; 
    
    $obj = $marketplace;
    $marketplaceObjs = $obj->getMarketplaceObj();
 
    $content = '<div class="div-table table-with-border marketplace-status" style="width: 100%">';
    
    foreach($marketplaceObjs as $marketplaceRow){  
        $content .='     
        <div class="div-table-row marketplace-'.$marketplaceRow['key'].'">
            <div class="div-table-col-3" style="vertical-align:middle">'.$marketplaceRow['name'].'</div>
            <div class="div-table-col-3" style="width:3em; text-align:center">
                 <div class="status-icon disconnect-icon" style="float:left"><i class="fas fa-times-circle"></i></div>
                 <div class="status-icon connect-icon" style="float:left; display:none"><i class="fas fa-check-circle"></i></div>
            </div> 
        </div>
        '; 
    }
        
    $content .= '</div>'; 
            
    // test marketplace connection
    $script = '<script> 
                function testMarketplaceConnection(marketplacekey){
                  var marketplaceRow = $(".marketplace-"+marketplacekey);

                   $.ajax({
                    type: "GET",
                    url: "ajax-marketplace.php",
                    data: "action=testConnection&marketplacekey=" + marketplacekey, 
                    beforeSend : function (){
                            marketplaceRow.removeClass("active").addClass("inactive");
                            marketplaceRow.find(".disconnect-icon").show();
                            marketplaceRow.find(".connect-icon").hide();
                    },
                    success: function(data){      
                        if (!data) return; 
                        
                        var data = JSON.parse(data);  
                        if (data.status == true){ 
                            marketplaceRow.removeClass("inactive").addClass("active"); 
                            marketplaceRow.find(".disconnect-icon").hide();
                            marketplaceRow.find(".connect-icon").show();
                        }else{
                            marketplaceRow.attr("reladdr",data.authURL);
                        } 
                    }
                  }); 
                }    
    ';
    
    foreach($marketplaceObjs as $marketplaceRow) 
        $script .= 'testMarketplaceConnection('.$marketplaceRow['key'].');'; 
    
    $script .= 'var domObj = '.getPanelDOM($panelName,true).'; ';
    $script .= 'domObj.find(".marketplace-status .inactive").click(function(){   
                    var reladdr = $(this).attr("reladdr");
                    if(!reladdr) return; 
                    var win=window.open(reladdr, "_blank");
                    win.focus();  
                });';
    
    $script .= '</script>';
    $script = $obj->minimizeJavascriptSimple($script);
        
    $content .= $script;
     
    $replacement = array();
    $replacement['title'] =$obj->lang['marketplace'];  
    $replacement['content'] = $content;  

    return replaceContent($replacement, $templatePanel);
}

function generatePanelUnderMarginSalesOrder($panelName, $rowsLimit = 10){  
    global $security; 
    global $templatePanel;  
    global $item;
    
    switch (PLAN_TYPE['categorykey']){
        case 1 :    global $salesOrder;
                    $obj = $salesOrder; 
                    break;
        case 3 :    global $salesOrderCarService;
                    $obj = $salesOrderCarService;
                    break;  
    } 
      
    $rs =  $obj->searchData('','',true,' and '.$obj->tableName.'.statuskey in (2,3) and profit < 0 ',' order by pkey desc','limit 0,'.  $rowsLimit );

    $content = '<div class="div-table table-with-border" style="width:100%">';

    for ($i=0;$i<count($rs);$i++){ 
        //       <div class="div-table-col-5" style="width:30%; text-align:center">'.$obj->formatDbDate($rs[$i]['trdate']).'</div>
        $content .='     
        <div class="div-table-row">
        <div class="div-table-col-3" style="width:10em">'.$rs[$i]['code'].'</div>
        <div class="div-table-col-3">'.$rs[$i]['customername'].'</div>
        </div>
        '; 
    }
        
    $content .= '</div>';
    
    $replacement = array();
    $replacement['title'] =$obj->lang['underMarginSalesOrder'];  
    $replacement['content'] = $content;  

     return replaceContent($replacement, $templatePanel);
}
 
function replaceContent($replacement, $templatePanel){
    $patterns = array();
    $patterns['title'] = '/({{TITLE}})/'; 
    $patterns['content'] = '/({{CONTENT}})/';
    $patterns['footer'] = '/({{FOOTER}})/';
    
    return preg_replace($patterns, $replacement, $templatePanel);
}
 
function generateBarChart ($arrParam){ 
    global $class;
     
    $arrData = $arrParam['data']; 
    $callbackName = $arrParam['callbackName'];  
    $panelName = $arrParam['panelName'].' .content'; 
    $title = $arrParam['title'];
    
    // insert color style
    $arrData[0][2] = array('role' => 'style');
    for($i=1;$i<count($arrData);$i++){
        $arrData[$i][2] = isset($class->graphColorSet[$i]) ? $class->graphColorSet[$i] : '';
    } 
     
    $content ='
     <script>  
            setTimeout(function(){google.load(\'visualization\', \'1\', {\'callback\':\''.$callbackName.'\', \'packages\':[\'corechart\']})} );   
            
            // tetep harus ad, agar keresize ketika di tab Dashboard
            $(window).resize(function(){   if (getSelectedTabIndex() == 0) '.$callbackName.'(); });   
            dashboardRedrawFunc.push('.$callbackName.');

            function '.$callbackName.'() { 
            
                var data = google.visualization.arrayToDataTable('.json_encode($arrData).');
   
                var options = {
                  title: \''.$title.'\',  
                  legend: {position: \'none\'}, 
                  chartArea: {\'width\': \'100%\', \'left\' : \'110\', \'right\' : \'30\'},
                  titleTextStyle : { color: \'#333\', fontSize: \'16\', fontName: \'Palanquin\' } ,
                  animation: {
                        duration: 1500,
                        startup: true 
                    }
                };

                var chart = new google.visualization.BarChart('.getPanelDOM($panelName).');
                  
                chart.draw(data,options);
                 
                setTimeout(function(){
                     var domObj = '.getPanelDOM($panelName,true).';    
                     domObj.find(\'text[text-anchor=end]\').each(function () {   
                       $(this).attr("text-anchor","front"); 
                       $(this).attr("x",5); 
                     }); 
                }, 2000);
 
              }
        </script>';
    
    return $class->minimizeJavascriptSimple($content);
}

function generatePieChart ($arrParam,$arrOptions){ 
    global $class;
     
    $arrData = $arrParam['data']; 
    $callbackName = $arrParam['callbackName'];  
    $panelName = $arrParam['panelName'] .' .content'; 
    $title = $arrParam['title'];
    
    $pieHole = isset($arrOptions['pieHole']) ? $arrOptions['pieHole'] : 0;
    $legendPos = isset($arrOptions['legendPos']) ? $arrOptions['legendPos'] : 'right';
    
    $content ='
     <script>  
            setTimeout(function(){google.load(\'visualization\', \'1\', {\'callback\':\''.$callbackName.'\', \'packages\':[\'corechart\']})} );  

            // tetep harus ad, agar keresize ketika di tab Dashboard
            $(window).resize(function(){   if (getSelectedTabIndex() == 0) '.$callbackName.'(); });   
            dashboardRedrawFunc.push('.$callbackName.');

            function '.$callbackName.'() { 
                var data = google.visualization.arrayToDataTable('.json_encode($arrData,JSON_NUMERIC_CHECK).');

                var options = {
                  title: \''.$title.'\',
                  chartArea: {\'width\': \'100%\'},
                  titleTextStyle : { color: \'#333\', fontSize: \'16\', fontName: \'Palanquin\' } ,
                  slices : '.$class->graphPieColorSet.',
                  pieHole: '.$pieHole.',
                  legend: \''.$legendPos.'\',
                  animation: {
                        duration: 1500,
                        startup: true 
                  }
                };

                var chart = new google.visualization.PieChart('.getPanelDOM($panelName).');

                chart.draw(data,options);
              }
        </script>';
    
    return $class->minimizeJavascriptSimple($content);
}

function generateLineChart($arrParam){  
    global $class;
    
    $arrData = $arrParam['data']; 
    $callbackName = $arrParam['callbackName'];  
    $panelName = $arrParam['panelName']; 
    $title = $arrParam['title'];
      
    $content = '
        <script>  
            setTimeout(function(){google.load(\'visualization\', \'1\', {\'callback\':\''.$callbackName.'\', \'packages\':[\'corechart\']})} );  
     
            // tetep harus ad, agar keresize ketika di tab Dashboard
            $(window).resize(function(){   if (getSelectedTabIndex() == 0) '.$callbackName.'(); });   
            dashboardRedrawFunc.push('.$callbackName.');

            function '.$arrParam['callbackName'].'() {   

            var period  = \'\'; 
            var data = google.visualization.arrayToDataTable($.parseJSON(\''.json_encode($arrData).'\'));';

 
    $content .= chr(13). '  period = "'.$arrData[1][0].' - '. $arrData[count($arrData)-1][0] .'";' . chr(13); 
             
    $content .= '    
                  // Set chart options
                   
                  var options = {
                                    title: \''.$title.'\n\' + period,
                                    chartArea: {\'width\': \'90%\', \'left\': \'80\' },
                                    curveType: \'none\',
                                    pointSize: 5,
                                    vAxis: { gridlines: { count: 8 }, textStyle: {   color:\'#666\', fontSize: 12 } }, 
                                    tooltip : {  textStyle: {   color:\'#666\', fontSize: 12 } },
                                    hAxis: { textStyle: {   color:\'#666\', fontSize: 12 } }, 
                                    legend: {\'position\': \'bottom\'},
                                    titleTextStyle : { color: \'#333\', fontName: \'Palanquin\' } ,
                                    series : '.$class->graphLineColorSet.',
                                    animation: {
                                        duration: 1500,
                                        startup: true  
                                    }
                                };

                  // Instantiate and draw our chart, passing in some options.
                  var chart = new google.visualization.AreaChart('.getPanelDOM($panelName).'); 
                  
                  chart.draw(data, options);
            }

        </script>                            
    ';  
    
    return $class->minimizeJavascriptSimple($content);
}

function generateTransactionStatus($title,$obj,$startPeriod, $endPeriod, $bgDiv=''){  
    
    global $truckingServiceWorkOrder;
    global $truckingServiceOrder;
    
    $startPeriod = date("Y-m-01", strtotime($startPeriod));
    $endPeriod = date("Y-m-t 23:59", strtotime($endPeriod));
        
    $rsStatus = $obj->getAllStatus(); 
    $statusCriteria = '';
     
    $statusCriteria = ' and '. $obj->tableName.'.trdate between '.$obj->oDbCon->paramString($startPeriod).' and ' . $obj->oDbCon->paramString($endPeriod);
    $statusCriteria .=  $obj->getWarehouseCriteria();
    
    $arrGroup = array();
    array_push($arrGroup, array('fieldName' => $obj->tableName.'.statuskey', 'groupkey' => 'statuskey' ));
    $rsCountedTotalRows = $obj->countTotalRows($statusCriteria,$arrGroup); 
    
    $total = $obj->getCountedTotalRows($rsCountedTotalRows);  //array_sum($arrTotalRows);
    
    // ==== hitung total waktu 
    // buat JO aj dulu. SPK gk terlalu guna karena tergantung tujuan 
    $totalPerStatus = ($obj->tableName == $truckingServiceOrder->tableName) ? $obj->calculateDateDiffPerStatus($statusCriteria)['totalPerStatus'] : '';
    
    $content = '<div class="auto-height transaction-status-panel">';
    $content .= $bgDiv;
                  
    $content .= '<div class="title">';
    $content .= '<div class="flex">';
    $content .= '<div class="consume">'.strtoupper($title).'</div>'; 
    $content .= '<div style="text-align:right; font-weight:normal">'.$obj->formatNumber($total).'</div>';
    $content .= '</div>'; 
    $content .= '</div>'; 
    
    $content .= '<div class="div-table">';

    for($i=0;$i<count($rsStatus);$i++){
        
        $statuskey = $rsStatus[$i]['pkey']; 
        $avgDaysLabel = (isset($totalPerStatus[$statuskey]['label'])) ? $totalPerStatus[$statuskey]['label'] : '';
         
        $totalData = $obj->getCountedTotalRows($rsCountedTotalRows,'statuskey', $rsStatus[$i]['pkey']);  //(isset($arrTotalRows[$rsStatus[$i]['pkey']])) ? $arrTotalRows[$rsStatus[$i]['pkey']] : 0;
        $totalDataLabel = $obj->formatNumber($totalData);
            
        // khusus SPK 
        if($obj->tableName == $truckingServiceWorkOrder->tableName){
            if($rsStatus[$i]['pkey'] == 2){ 
                $qtyOutsource = $obj->getTotalOutsource($rsStatus[$i]['pkey']); 
                $totalDataLabel = $obj->formatNumber ($totalData - $qtyOutsource) .' / ' . $obj->formatNumber($qtyOutsource); 
                //$rsStatus[$i]['status'] .=  ' ('.$obj->lang['inhouse'].'/'.$obj->lang['outsource'].')';
            }
        }
        
        $content .= '<div class="div-table-row">';
        $content .= '<div class="div-table-col-3">'.$rsStatus[$i]['status'].' <span class="tag">'.$avgDaysLabel.'</span></div>'; 
        $content .= '<div class="div-table-col-3" style="text-align:right">'.$totalDataLabel.'</div>';
        $content .= '</div>';
    } 
 
    $content .= '</div>
     <div style="clear:both; height: 3em"></div>
     <div class="footnote">
        <div>'.$obj->lang['cancellationRate'].': '. $obj->formatNumber(( $totalData / $total) * 100,2).' %</div>
        <div>'.$obj->formatDBDate($startPeriod,'M Y').' - '.$obj->formatDBDate($endPeriod,'M Y').'</div>
     </div> 
    </div>'; 
      
    return $content;
}

function generatePanelVehicleOverdue($panelName){ 
    
    $car = createObjAndAddToCol(new Car()); 
    
    global $security;
    global $templatePanel;   
    global $widgetSetting;
    
    $obj = $car;
    
    if(!$security->isAdminLogin($obj->securityObject,10)) return;
   
    // cek properties
    $rsSettings = $widgetSetting->getPropertiesValue($panelName);
    $rsSettings = array_column($rsSettings,null,'properties');
     
    $timelimit = (isset($rsSettings['overduedays']['value']) && !empty($rsSettings['overduedays']['value'])) ? $rsSettings['overduedays']['value'] : $rsSettings['overduedays']['defaultvalue']  ;
       
    $expiredType = array();
    
    $licenseExpired = (isset($rsSettings['licenseexpired']['value']) && !empty($rsSettings['licenseexpired']['value'])) ? $rsSettings['licenseexpired']['value'] : $rsSettings['licenseexpired']['defaultvalue']  ;
    if($licenseExpired) array_push($expiredType,'licenseexpired');
    
    $taxExpired = (isset($rsSettings['taxexpired']['value']) && !empty($rsSettings['taxexpired']['value'])) ? $rsSettings['taxexpired']['value'] : $rsSettings['taxexpired']['defaultvalue']  ;
    if($taxExpired) array_push($expiredType,'taxexpired');
    
    $kirExpired = (isset($rsSettings['kirexpired']['value']) && !empty($rsSettings['kirexpired']['value'])) ? $rsSettings['kirexpired']['value'] : $rsSettings['kirexpired']['defaultvalue']  ;
    if($kirExpired) array_push($expiredType,'kirexpired');
      
    $rs = $obj->getExpiryLicense($timelimit,$expiredType);

    $content = '<div class="div-table table-with-border" style="width:100%">';

    for ($i=0;$i<count($rs);$i++){ 
        
        $class = ($rs[$i]['duedate'] < 0) ? 'text-red-cardinal' : ''; 
        
        $content .='     
        <div class="div-table-row '.$class.'"> 
        <div class="div-table-col-3" style="width:12em">'.$rs[$i]['policenumber'].'</div>
        <div class="div-table-col-3" style="width:5em">'.$rs[$i]['typename'].'</div>
        <div class="div-table-col-3" style="text-align:center">'.$obj->formatDbDate($rs[$i]['expireddate']).'</div> 
        </div>
        '; 
    }
        
    $content .= '</div>';
    
    $replacement = array();
    $replacement['title'] =$obj->lang['vehicleLicenseOverdue'];  
    $replacement['content'] = $content;  

    return replaceContent($replacement, $templatePanel);  
}

function generatePanelDailyTransactionSummary($panelName, $limitdays = 10){
     
    global $security;
    global $templatePanel; 
    global $salesOrder;
    
    $obj = $salesOrder;
     
    if(!$security->isAdminLogin($obj->securityObject,10)) return;
   
    $rs =  $obj->getDailyTransactionSummary($limitdays);

    $content = '<div class="div-table table-with-border" style="width:100%">';

    for ($i=0;$i<count($rs);$i++){ 
          
        $icon = '';

        $currSales = $rs[$i]['totalsales'];
        $prevSales =  $rs[$i+1]['totalsales'];

        if($i <> count($rs) -1) { 
            if($currSales != $prevSales ){
                $icon = ($currSales < $prevSales) ? '<i class="fas fa-caret-down text-red-cardinal"></i>' : '<i class="fas fa-caret-up  text-green-avocado"></i>';
            }
        }
        
        $content .='     
        <div class="div-table-row"> 
        <div class="div-table-col-3" style="width:8.5em; text-align:center">'.$obj->formatDBDate($rs[$i]['trdate']).'</div> 
        <div class="div-table-col-3" style="width:8em;text-align:right; ">'.$rs[$i]['totalsoldinunit'].'</div>  
        <div class="div-table-col-3" style="text-align:right">'.$obj->formatNumber($currSales).'</div> 
        <div class="div-table-col-3" style="width:1em; text-align:center">'.$icon.'</div> 
        </div>
        '; 
         
    }
        
    $content .= '</div>';
    
    $replacement = array();
    $replacement['title'] =$obj->lang['dailyTransactionSummary'];  
    $replacement['content'] = $content;  

    return replaceContent($replacement, $templatePanel);  
}


function generatePanelDailyMarketplaceTransactionSummary($panelName, $limitdays = 10){
     
    global $security;
    global $templatePanel; 
    global $salesOrder;
    global $customer;
    
    $obj = $salesOrder;
     
    if(!$security->isAdminLogin($obj->securityObject,10)) return;
   

    // kalo ad marketplace
    $rsMarketplaceCustomer = $customer->searchData($customer->tableName.'.ismarketplace',1,true); // gk peduli aktif atau gk
    $rsMarketplaceCustomer = array_column($rsMarketplaceCustomer,'pkey');

    $rs =  $obj->getDailyTransactionSummary($limitdays, ' and '.$obj->tableName.'.customerkey in ('.$obj->oDbCon->paramString($rsMarketplaceCustomer,',').')' );

    $content = '<div class="div-table table-with-border" style="width:100%">';

    for ($i=0;$i<count($rs);$i++){ 
          
        $icon = '';

        $currSales = $rs[$i]['totalsales'];
        $prevSales =  $rs[$i+1]['totalsales'];

        if($i <> count($rs) -1) { 
            if($currSales != $prevSales ){
                $icon = ($currSales < $prevSales) ? '<i class="fas fa-caret-down text-red-cardinal"></i>' : '<i class="fas fa-caret-up  text-green-avocado"></i>';
            }
        }
        
        $content .='     
        <div class="div-table-row"> 
        <div class="div-table-col-3" style="width:8.5em; text-align:center">'.$obj->formatDBDate($rs[$i]['trdate']).'</div> 
        <div class="div-table-col-3" style="width:8em;text-align:right; ">'.$rs[$i]['totalsoldinunit'].'</div>  
        <div class="div-table-col-3" style="text-align:right">'.$obj->formatNumber($currSales).'</div> 
        <div class="div-table-col-3" style="width:1em; text-align:center">'.$icon.'</div> 
        </div>
        '; 
         
    }
        
    $content .= '</div>';
    
    $replacement = array();
    $replacement['title'] =$obj->lang['dailyMarketplaceTransactionSummary'];  
    $replacement['content'] = $content;  

    return replaceContent($replacement, $templatePanel);  
}

function generatePanelCashBankOutstanding ($panelName){
    $chartOfAccount = createObjAndAddToCol(new ChartOfAccount());
    
    global $security;
    global $templatePanel; 
    
    $obj = $chartOfAccount;
     
    if(!$security->isAdminLogin($obj->securityObject,10)) return;
   
    $rs = $obj->searchData($obj->tableName.'.iscashbank',1,true,' and '.$obj->tableName.'.isleaf = 1');
  
    $content = '<div class="div-table table-with-border" style="width:100%">';

    for ($i=0;$i<count($rs);$i++){ 
           
        $content .='     
        <div class="div-table-row"> 
        <div class="div-table-col-3" style="width:15em;">'.$rs[$i]['name'].'</div>  
        <div class="div-table-col-3" style="text-align:right">'.$obj->formatNumber($rs[$i]['amount']).'</div>  
        </div>
        '; 
         
    }
        
    $content .= '</div>';
    
    $replacement = array();
    $replacement['title'] =$obj->lang['cashBank'];  
    $replacement['content'] = $content;  

    return replaceContent($replacement, $templatePanel); 
}


function generatePanelTopCustomerByJO ($panelName,$startPeriod, $endPeriod){ 
    global $security;
    global $templatePanel; 
    global $truckingServiceOrder; 
    global $customer; 
    
    $obj = $truckingServiceOrder;
     
    if(!$security->isAdminLogin($obj->securityObject,10)) return;
   
    $rs = $obj->getBestSalesAmountByGroup($customer->tableName.'.pkey', $startPeriod, $endPeriod);  
  
    $content = '<div class="div-table table-with-border" style="width:100%">';

    for ($i=0;$i<count($rs);$i++){ 
           
        $content .='     
        <div class="div-table-row"> 
        <div class="div-table-col-3" style="width:22em;">'.$rs[$i]['customername'].'</div>  
        <div class="div-table-col-3" style="text-align:right">'.$obj->formatNumber($rs[$i]['amount']).'</div>  
        </div>
        '; 
         
    }
        
    $content .= '</div>';
    
    $replacement = array();
    $replacement['title'] =$obj->lang['topCustomers'];  
    $replacement['content'] = $content;  

    return replaceContent($replacement, $templatePanel); 
}

function generatePanelCustomerCreditLimitSummary($panelName){
    global $security;
    global $templatePanel;  
    global $customer; 
    
    $obj = $customer;
     
    if(!$security->isAdminLogin($obj->securityObject,10)) return;
   
    $rs = $obj->getCustomerCreditLimitSummary($customer->tableName.'.pkey', true, ' order by aroutstanding desc');  
  
    $content = '<div class="div-table table-with-border" style="width:100%">';

    for ($i=0;$i<count($rs);$i++){ 
           
        $content .='     
        <div class="div-table-row"> 
        <div class="div-table-col-3" >'.$rs[$i]['name'].'</div>  
        <div class="div-table-col-3" style="text-align:right;width:8em">'.$obj->formatNumber($rs[$i]['creditlimit']).'</div>  
        <div class="div-table-col-3 text-red-cardinal" style="text-align:right;width:8em">'.$obj->formatNumber($rs[$i]['creditlimit'] - $rs[$i]['aroutstanding']).'</div>  
        </div>
        '; 
         
    }
        
    $content .= '</div>';
    
    $replacement = array();
    $replacement['title'] =$obj->lang['creditLimit'];  
    $replacement['content'] = $content;  

    return replaceContent($replacement, $templatePanel); 
}

function generatePanelCashBankRealizationSummary ($panelName){  
    $apEmployee = createObjAndAddToCol(new APEmployee());   
    $arEmployee = createObjAndAddToCol(new AREmployee());   
    
    $obj = createObjAndAddToCol(new CashBankRealization()); 
         
    global $security;
    global $templatePanel;
     
    if(!$security->isAdminLogin($obj->securityObject,10)) return;
   
    $rs = $obj->getRealizationDashboardSummary();  
  
    $content = '<div class="div-table table-with-border" style="width:100%; font-size:1.2em">';

    $total = 0;
    
    for ($i=0;$i<count($rs);$i++){ 
        $class = ($rs[$i]['amount'] < 0) ? 'text-red-cardinal' : '';
        
        $content .='     
        <div class="div-table-row '.$class.'"> 
        <div class="div-table-col-3" style="width:22em;">'.$rs[$i]['label'].'</div>  
        <div class="div-table-col-3" style="text-align:right">'.$obj->formatNumber($rs[$i]['amount']).'</div>  
        </div>
        '; 
         
        $total += $rs[$i]['amount'];
    }
        
     $class = ($total< 0) ? 'text-red-cardinal' : '';
     $content .='     
        <div class="div-table-row '.$class.'" style="font-weight:bold"> 
        <div class="div-table-col-3" style="border-bottom:0">'.$obj->lang['total'].'</div>  
        <div class="div-table-col-3" style="border-bottom:0; text-align:right">'.$obj->formatNumber($total).'</div>  
        </div>
        '; 
    
    $content .= '</div>';
    
    $replacement = array();
    $replacement['title'] =$obj->lang['cashBankRealization'];  
    $replacement['content'] = $content;  

    return replaceContent($replacement, $templatePanel); 
}

function getPanelDOM($panelName, $asJqueryObj = false){ 
    return '$("#dashboard").find(".'.$panelName.'")' . (($asJqueryObj) ? '' : '[0]');
}
?> 