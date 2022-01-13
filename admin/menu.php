<?php  
    
    // DEFINE ALL ACCESSIBLE MENU
    
    $rsAccessibleMenu = $security->getAllAccessibleMenu();
    define('ACCESSIBLE_MENU', array_column($rsAccessibleMenu,'modulecode'));

    function pushMenuItem(&$arrMenuItem,$newMenuItem, $categoryType = array()){
        global $security; 
          
        if (!empty($categoryType)){
            if (!in_array(PLAN_TYPE['categorykey'],$categoryType ))
                return ;
        }
             
         //$userkey =  base64_decode($_SESSION[$security->loginAdminSession]['id']); 
      
         if ( isset($newMenuItem['menu']) && count($newMenuItem['menu'][0]) == 0)  
             return;
        
         /*if ( !empty($newMenuItem['securityObject']) &&  !$security->hasSecurityAccess( $userkey ,$security->getSecurityKey($newMenuItem['securityObject']),10) ) 
                 return;*/
        if ( !empty($newMenuItem['securityObject']) &&  !in_array(strtolower($newMenuItem['securityObject']),ACCESSIBLE_MENU) ) 
                 return;
     
         array_push($arrMenuItem ,$newMenuItem);
        
    }

    function buildMenu($arrMenu,$parent = '' ){ 
          
            $menu = ''; 
	        
            foreach ($arrMenu as $key=>$menuItem) {  
                $class = "submenu";
                
                if (empty($parent))
                        $class="root hvr-sweep-to-right ";
                else if (isset($menuItem['menu']))
                         $class .= " submenu-header";
                
                $icon = '';
                if (!empty($menuItem['icon']))
                    $icon = '<div class="'.$menuItem['icon'].' icon"></div>';
                
                //$menuItem['phplist'] = getPersonalizedFiles($menuItem['phplist'],$ext='');
                     
                if (!empty($menuItem['phplist'])){
                    $menu .= '<div class="'.$class.' menu-child clickable" rel="'.$key.'" reladdr="'.$menuItem['phplist'].'" reltarget="'.$menuItem['target'].'">'.$icon.$menuItem['label'].'</div>';
                }else{
                    $menu .= '<div class="'.$class.' clickable" rel="'.$key.'">'.$icon.$menuItem['label'].'</div>';
                }
                
                if (isset($menuItem['menu'])){
                    if (empty($parent)) 
                        $menu .= '<div class="submenu-panel-'.$key.' submenu-panel">';
                     
                     
                    foreach ($menuItem['menu'] as $menuItemRow)  
                         $menu .= buildMenu($menuItemRow,$menuItem); 
                     
                    if (empty($parent)) 
                        $menu .= '</div>';
                } 
                 
            }
             

            if (empty($parent)) 
                $menu  .= '<div class="main-menu-closer"></div>';

            return $menu;
    }

	$menu = '';	 
 
    $arrMenu = array(); 
  
    // BUSINESS PARTNER 
    $arrBusinessPartner = array ('label' => $class->lang['businessPartner'], 'icon' => 'fas fa-users' );  
    $arrBusinessPartner['menu'] = array(); 

	$menuitem = array();
    pushMenuItem($menuitem , array ('label' => $class->lang['supplier'],   'securityObject' => 'Supplier',   'phplist' => 'supplierList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['customer'] ,   'securityObject' => 'Customer',   'phplist' => 'customerList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['customerCategory'],   'securityObject' => 'CustomerCategory',   'phplist' => 'customerCategoryList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['businessCategory'],   'securityObject' => 'BusinessCategory',   'phplist' => 'businessCategoryList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['customerFeatures'],   'securityObject' => 'CustomerFeatures',   'phplist' => 'customerFeaturesList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['consignee'],   'securityObject' => 'Consignee',   'phplist' => 'consigneeList', 'target' => 'tab' ), array(2));
    pushMenuItem($menuitem , array ('label' => $class->lang['employee'],   'securityObject' => 'Employee',   'phplist' => 'employeeList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['employeeDivision'],   'securityObject' => 'EmployeeCategory',   'phplist' => 'employeeCategoryList', 'target' => 'tab' ));
    
    // biasanya utk front end
    pushMenuItem($menuitem , array ('label' => $class->lang['managementTeam'],   'securityObject' => 'ManagementTeam',   'phplist' => 'managementTeamList', 'target' => 'tab' ));

    if (PLAN_TYPE['usefrontend'] == 1){
         pushMenuItem($menuitem , array ('label' => $class->lang['partners'],   'securityObject' => 'Partners',   'phplist' => 'partnersList', 'target' => 'tab' ));
    }

   // pushMenuItem($menuitem , array ('label' => $class->lang['company'],   'securityObject' => $company->securityObject,   'phplist' => 'companyList', 'target' => 'tab' ));

    $arrSubMenu = array ('label' => $class->lang['company']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array();

    pushMenuItem($submenuitem , array ('label' => $class->lang['company'],   'securityObject' => 'Company',   'phplist' => 'companyList', 'target' => 'tab' )); 
    pushMenuItem($arrSubMenu['menu'], $submenuitem); 
    pushMenuItem($menuitem , $arrSubMenu);    


    // OTHERS
    $arrSubMenu = array ('label' => $class->lang['others']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array();     
    pushMenuItem($submenuitem , array ('label' => $class->lang['templateCustomer'],   'securityObject' => 'Customer',   'phplist' => 'templateCustomerList', 'target' => 'tab' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['templateSupplier'],   'securityObject' => 'Supplier',   'phplist' => 'templateSupplierList', 'target' => 'tab' ));
    pushMenuItem($arrSubMenu['menu'], $submenuitem);   
    pushMenuItem($menuitem , $arrSubMenu);  
    $arrPurchase['menu'] = array();

    pushMenuItem ($arrBusinessPartner['menu'], $menuitem);    
    pushMenuItem ($arrMenu, $arrBusinessPartner); 
  

    // MEMBERSHIP 
    $arrMembership = array ('label' => $class->lang['membership'], 'icon' => 'fas fa-user-tag' );  
    $arrMembership['menu'] = array(); 

	$menuitem = array(); 
	pushMenuItem($menuitem , array ('label' => $class->lang['membershipRegistration'],   'securityObject' => 'MembershipSubscription',   'phplist' => 'membershipSubscriptionList', 'target' => 'tab' ));
	pushMenuItem($menuitem , array ('label' => $class->lang['membershipType'],   'securityObject' => 'MembershipLevel',   'phplist' => 'membershipLevelList', 'target' => 'tab' ));
//    pushMenuItem($menuitem , array ('label' => $class->lang['membershipType'],   'securityObject' => 'Membership',   'phplist' => 'membershipList', 'target' => 'tab' ));
//    pushMenuItem($menuitem , array ('label' => $class->lang['membershipAttendance'],   'securityObject' => 'MembershipAttendance',   'phplist' => 'membershipAttendanceList', 'target' => 'tab' ));
    
  
    pushMenuItem ($arrMembership['menu'], $menuitem);    
    pushMenuItem ($arrMenu, $arrMembership); 
  
 
    // INVENTORY
    $arrInventory = array ('label' => $class->lang['productAndService'], 'icon' => 'fas fa-box');  

    $arrInventory['menu'] = array(); 
 
    $menuitem = array();

    $arrSubMenu = array ('label' => $class->lang['productManagement']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 
    pushMenuItem($submenuitem , array ('label' => $class->lang['itemList'],   'securityObject' => 'Item',   'phplist' => 'itemList', 'target' => 'tab' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['itemDepotList'],   'securityObject' => 'ItemDepot',   'phplist' => 'itemDepotList', 'target' => 'tab' ), array(2));
    //pushMenuItem($submenuitem , array ('label' => $class->lang['itemList'],   'securityObject' => $item->securityObject,   'phplist' => 'pawnItemList', 'target' => 'tab' ), array(4));
    pushMenuItem($submenuitem , array ('label' => $class->lang['itemPackage'],   'securityObject' => 'ItemPackage',   'phplist' => 'itemPackageList', 'target' => 'tab' ), array(1,3));
    pushMenuItem($submenuitem , array ('label' => $class->lang['itemCategory'],   'securityObject' => 'ItemCategory',   'phplist' => 'itemCategoryList', 'target' => 'tab' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['itemUnit'],   'securityObject' => 'ItemUnit',   'phplist' => 'itemUnitList', 'target' => 'tab' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['timeUnit'],   'securityObject' => 'TimeUnit',   'phplist' => 'timeUnitList', 'target' => 'tab' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['itemCondition'],   'securityObject' => 'ItemCondition',   'phplist' => 'itemConditionList', 'target' => 'tab' ), array(1,2,3,5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['itemConversion'],   'securityObject' => 'ItemConversion',   'phplist' => 'itemConversionList', 'target' => 'tab' ));
    pushMenuItem($arrSubMenu['menu'], $submenuitem);  
    pushMenuItem($menuitem , $arrSubMenu);  


    $arrSubMenu = array ('label' => $class->lang['serviceManagement']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array();
    pushMenuItem($submenuitem , array ('label' => $class->lang['serviceList'],   'securityObject' => 'Service',   'phplist' => 'serviceList', 'target' => 'tab' ), array(1,2,3,6));
    //pushMenuItem($submenuitem , array ('label' => $class->lang['workshopServiceList'],   'securityObject' => $service->securityObject,   'phplist' => 'workshopServiceList', 'target' => 'tab' ), array(3));
    pushMenuItem($submenuitem , array ('label' => $class->lang['truckingServiceList'],   'securityObject' => 'TruckingService',   'phplist' => 'truckingServiceList', 'target' => 'tab' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['truckingCostList'],   'securityObject' => 'TruckingCost',   'phplist' => 'truckingCostList', 'target' => 'tab' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['serviceCategory'],   'securityObject' => 'ServiceCategory',   'phplist' => 'serviceCategoryList', 'target' => 'tab' ), array(1,3));
    pushMenuItem($submenuitem , array ('label' => $class->lang['serviceAndCostCategory'],   'securityObject' => 'ServiceCategory',   'phplist' => 'serviceCategoryList', 'target' => 'tab' ), array(2));
    pushMenuItem($arrSubMenu['menu'], $submenuitem); 
    pushMenuItem($menuitem , $arrSubMenu);  

    // SUB MOVEMENT
     $arrSubMenu = array ('label' => $class->lang['itemMovement']);  
     $submenuitem = array();
     $arrSubMenu['menu'] = array();
     pushMenuItem($submenuitem , array ('label' => $class->lang['itemIn'],   'securityObject' => 'ItemIn',   'phplist' => 'itemInList', 'target' => 'tab' ));
     pushMenuItem($submenuitem , array ('label' => $class->lang['itemInReceive'],   'securityObject' => 'ItemInReceive',   'phplist' => 'itemInReceiveList', 'target' => 'tab' ));
     pushMenuItem($submenuitem , array ('label' => $class->lang['itemOut'],   'securityObject' => 'ItemOut',   'phplist' => 'itemOutList', 'target' => 'tab' ));
     pushMenuItem($submenuitem , array ('label' => $class->lang['itemOutDelivery'],   'securityObject' => 'ItemOutDelivery',   'phplist' => 'itemOutDeliveryList', 'target' => 'tab' ));
     pushMenuItem($submenuitem , array ('label' => $class->lang['itemAdjustment'],   'securityObject' => 'ItemAdjustment',   'phplist' => 'itemAdjustmentList', 'target' => 'tab' ));
     pushMenuItem($submenuitem , array ('label' => $class->lang['warehouseTransfer'],   'securityObject' => 'WarehouseTransfer',   'phplist' => 'warehouseTransferList', 'target' => 'tab' ));
     pushMenuItem($arrSubMenu['menu'], $submenuitem);  
     pushMenuItem($menuitem , $arrSubMenu);  


    // SUB MOVEMENT
     $arrSubMenu = array ('label' => $class->lang['assembly']);  
     $submenuitem = array();
     $arrSubMenu['menu'] = array();
     pushMenuItem($submenuitem , array ('label' => $class->lang['billOfMaterials'],   'securityObject' => 'BillOfMaterials',   'phplist' => 'billOfMaterialsList', 'target' => 'tab' ), array(1,3));
     pushMenuItem($submenuitem , array ('label' => $class->lang['assemblyItem'],   'securityObject' => 'Assembly',   'phplist' => 'assemblyList', 'target' => 'tab' ), array(1,3));
     pushMenuItem($arrSubMenu['menu'], $submenuitem);  
     pushMenuItem($menuitem , $arrSubMenu);   

     // OTHERS
     $arrSubMenu = array ('label' => $class->lang['others']);  
     $submenuitem = array();
     $arrSubMenu['menu'] = array();
     pushMenuItem($submenuitem , array ('label' => $class->lang['brandList'],   'securityObject' => 'Brand',   'phplist' => 'brandList', 'target' => 'tab' ));
     pushMenuItem($submenuitem , array ('label' => $class->lang['warehouse'],   'securityObject' => 'Warehouse',   'phplist' => 'warehouseList', 'target' => 'tab' ));
     pushMenuItem($submenuitem , array ('label' => $class->lang['car'],   'securityObject' => 'Car',   'phplist' => 'carList', 'target' => 'tab' ), array(2,3));
     pushMenuItem($submenuitem , array ('label' => $class->lang['carCategory'],   'securityObject' => 'CarCategory',   'phplist' => 'carCategoryList', 'target' => 'tab' ), array(2,3));
     pushMenuItem($submenuitem , array ('label' => $class->lang['carSeries'],   'securityObject' => 'CarSeries',   'phplist' => 'carSeriesList', 'target' => 'tab' ), array(2,3));
     pushMenuItem($submenuitem , array ('label' => $class->lang['chassis'],   'securityObject' => 'Chassis',   'phplist' => 'chassisList', 'target' => 'tab' ), array(2));
     pushMenuItem($submenuitem , array ('label' => $class->lang['chassisCategory'],   'securityObject' => 'ChassisCategory',   'phplist' => 'chassisCategoryList', 'target' => 'tab' ), array(2));
     //pushMenuItem($submenuitem , array ('label' => $class->lang['oilType'],   'securityObject' => 'OilType',   'phplist' => 'oilTypeList', 'target' => 'tab' ), array(3));
     pushMenuItem($submenuitem , array ('label' => $class->lang['itemSpecification'],   'securityObject' => 'ItemSpecification',   'phplist' => 'itemSpecificationList', 'target' => 'tab' ));
     

    if (PLAN_TYPE['usefrontend'] == 1){
         pushMenuItem($submenuitem , array ('label' => $class->lang['itemFilter'],   'securityObject' => 'ItemFilter',   'phplist' => 'itemFilterList', 'target' => 'tab' ), array(1));
         pushMenuItem($submenuitem , array ('label' => $class->lang['filterCategory'],   'securityObject' => 'FilterCategory',   'phplist' => 'filterCategoryList', 'target' => 'tab' ), array(1));
    }

     pushMenuItem ($arrSubMenu['menu'], $submenuitem); 

     pushMenuItem($menuitem , $arrSubMenu);  

    pushMenuItem ($arrInventory['menu'], $menuitem);   
    pushMenuItem ($arrMenu, $arrInventory); 

 
    // PURCHASE
    $arrPurchase = array ('label' => $class->lang['purchase'],'icon' => 'fas fa-shopping-basket'  );  
    $menuitem = array();


    $arrSubMenu = array ('label' => $class->lang['inventory']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array();
    pushMenuItem($submenuitem , array ('label' => $class->lang['purchaseRequest'],   'securityObject' => 'PurchaseRequest',   'phplist' => 'purchaseRequestList', 'target' => 'tab' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['purchaseOrder'],   'securityObject' => 'PurchaseOrder',   'phplist' => 'purchaseOrderList', 'target' => 'tab' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['purchaseReceive'],   'securityObject' => 'PurchaseReceive',   'phplist' => 'purchaseReceiveList', 'target' => 'tab' ));
    pushMenuItem($arrSubMenu['menu'], $submenuitem);  
    pushMenuItem($menuitem , $arrSubMenu);  

    // FF
    $arrSubMenu = array ('label' => 'Freight Forwarding');  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 

    pushMenuItem($submenuitem , array ('label' => $class->lang['purchaseOrderImport'],   'securityObject' => 'EMKLPurchaseOrder',   'phplist' => 'emklPurchaseOrderImportList', 'target' => 'tab' ),array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['purchaseOrderExport'],   'securityObject' => 'EMKLPurchaseOrder',   'phplist' => 'emklPurchaseOrderExportList', 'target' => 'tab' ),array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['purchaseRefund'],   'securityObject' => 'EMKLCommission',   'phplist' => 'emklCommissionList', 'target' => 'tab' ),array(2));
    pushMenuItem($arrSubMenu['menu'], $submenuitem);   
    pushMenuItem($menuitem , $arrSubMenu); 

    // OTHERS
    $arrSubMenu = array ('label' => $class->lang['others']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array();     
    pushMenuItem($submenuitem , array ('label' => $class->lang['templatePurchaseItem'],   'securityObject' => 'TemplateEMKLPurchaseItem',   'phplist' => 'templateEMKLPurchaseItemList', 'target' => 'tab' ),array(2));
    pushMenuItem($arrSubMenu['menu'], $submenuitem);   
    pushMenuItem($menuitem , $arrSubMenu); 
 
    $arrPurchase['menu'] = array();
    pushMenuItem ($arrPurchase['menu'], $menuitem);   
    pushMenuItem ($arrMenu, $arrPurchase); 
 
   
    // SALES
    $arrSales = array ('label' => $class->lang['sales'],'icon' => 'fas fa-shopping-cart');  
    $menuitem = array(); 


    $arrSubMenu = array ('label' => 'Freight Forwarding');  
    $submenuitem = array();
    $arrSubMenu['menu'] = array();    

    pushMenuItem($submenuitem , array ('label' => $class->lang['jobOrderHeaderImport'],   'securityObject' => 'EMKLOrder',   'phplist' => 'emklJobOrderImportHeaderList', 'target' => 'tab' ),array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['importOrderSheet'],   'securityObject' => 'EMKLJobOrder',   'phplist' => 'emklJobOrderImportList', 'target' => 'tab' ),array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['jobOrderHeaderExport'],   'securityObject' => 'EMKLOrder',   'phplist' => 'emklJobOrderExportHeaderList', 'target' => 'tab' ),array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['exportOrderSheet'],   'securityObject' => 'EMKLJobOrder',   'phplist' => 'emklJobOrderExportList', 'target' => 'tab' ),array(2));
    
    //pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrder'].' (EMKL)',   'securityObject' => $emklSalesOrder->securityObject,   'phplist' => 'emklSalesOrderList', 'target' => 'tab' ),array(2));
    
    pushMenuItem($arrSubMenu['menu'], $submenuitem);   
    pushMenuItem($menuitem , $arrSubMenu); 

    // TRUCKING
    $arrSubMenu = array ('label' => 'EMKL / '.  $class->lang['trucking']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array();   
    pushMenuItem($submenuitem , array ('label' => $class->lang['jobType'],   'securityObject' => 'TruckingJob',   'phplist' => 'truckingJobList', 'target' => 'tab' ),array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['jobOrderCategory'],   'securityObject' => 'TruckingServiceOrderCategory',   'phplist' => 'truckingServiceOrderCategoryList', 'target' => 'tab' ),array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['jobOrder'],   'securityObject' => 'TruckingServiceOrder',   'phplist' => 'truckingServiceOrderList', 'target' => 'tab' ),array(2));
    //pushMenuItem($submenuitem , array ('label' => $class->lang['multiPointJobOrder'],   'securityObject' => 'TruckingServiceOrder',   'phplist' => 'multiPointJobOrderList', 'target' => 'tab' ),array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['truckingServiceWorkOrder'],   'securityObject' => 'TruckingServiceWorkOrder',   'phplist' => 'truckingServiceWorkOrderList', 'target' => 'tab' ),array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['GPSTracker']. ' <span style=" margin-left: 5px;  display:inline-block; position:relative; top: 4px; width: 100px; height: 15px; background-position:left; background-size:contain; background-repeat:no-repeat; background-image:url(\'/include/img/partners/accugps.png\') "> </span>',   'securityObject' => 'TruckingServiceWorkOrder',   'phplist' => '/admin/dashboard/truckingworkorder', 'target' => '_blank' ),array(2));
    pushMenuItem($arrSubMenu['menu'], $submenuitem);  
    pushMenuItem($menuitem , $arrSubMenu); 


    // DUMP TRUCK
    $arrSubMenu = array ('label' => 'Dumper');  
    $submenuitem = array();
    $arrSubMenu['menu'] = array();    

    pushMenuItem($submenuitem , array ('label' => $class->lang['project'],   'securityObject' => 'ProjectDumper',   'phplist' => 'projectDumperList', 'target' => 'tab' ),array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrder'] . ' (Dumper)',   'securityObject' => 'SalesOrderDumper',   'phplist' => 'salesOrderDumperList', 'target' => 'tab' ),array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['invoice'] . ' (Dumper)',   'securityObject' => 'SalesOrderDumperInvoice',   'phplist' => 'salesOrderDumperInvoiceList', 'target' => 'tab' ),array(2));
       
    pushMenuItem($arrSubMenu['menu'], $submenuitem);   
    pushMenuItem($menuitem , $arrSubMenu); 

    // RETAIL & BENGKEL
    $arrSubMenu = array ('label' => $class->lang['salesTransaction']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array();     
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrder'],   'securityObject' => 'SalesOrder',   'phplist' => 'salesOrderList', 'target' => 'tab' ),array(1,4,5,6));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrderWorkshop'],   'securityObject' => 'SalesOrderCarService',   'phplist' => 'salesOrderCarServiceList', 'target' => 'tab' ),array(2,3));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrder'].' SC',   'securityObject' => 'SalesOrderSubscription',   'phplist' => 'salesOrderSubscriptionList', 'target' => 'tab' ),array(6));
    pushMenuItem($submenuitem , array ('label' => $class->lang['installationWorkOrder'],   'securityObject' => 'InstallationWorkOrder',   'phplist' => 'installationWorkOrderList', 'target' => 'tab' ),array(6));
    pushMenuItem($submenuitem , array ('label' => $class->lang['BAST'],   'securityObject' => 'InstallationBAST',   'phplist' => 'installationBASTList', 'target' => 'tab' ),array(6));
    pushMenuItem($submenuitem , array ('label' => $class->lang['invoice'],   'securityObject' => 'SalesOrder',   'phplist' => 'invoiceOrderSubscriptionList', 'target' => 'tab' ),array(6));
    pushMenuItem($submenuitem , array ('label' => $class->lang['termination'],   'securityObject' => 'Termination',   'phplist' => 'terminationList', 'target' => 'tab' ),array(6));
    pushMenuItem($submenuitem , array ('label' => $class->lang['ticketSupport'],   'securityObject' => 'TicketSupport',   'phplist' => 'ticketSupportList', 'target' => 'tab' ),array(6));
    pushMenuItem($submenuitem , array ('label' => $class->lang['supportWorkOrder'],   'securityObject' => 'TicketSupportWorkOrder',   'phplist' => 'ticketSupportWorkOrderList', 'target' => 'tab' ),array(6));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesDelivery'],   'securityObject' => 'SalesDelivery',   'phplist' => 'salesDeliveryList', 'target' => 'tab' ),array(1,3,4));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesReturn'],   'securityObject' => 'SalesCarServiceReturn',   'phplist' => 'salesCarServiceReturnList', 'target' => 'tab' ),array(3));
    pushMenuItem($submenuitem , array ('label' => $class->lang['membershipRegistration'],   'securityObject' => 'CustomerMembership',   'phplist' => 'customerMembershipList', 'target' => 'tab' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['offerSimulator'],   'securityObject' => 'OfferSimulator',   'phplist' => 'offerSimulatorList', 'target' => 'tab' ),array(1));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrderCategory'],   'securityObject' => 'SalesOrderPropertyType',   'phplist' => 'salesOrderPropertyTypeList', 'target' => 'tab' ));
	pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrder'] . ' ('.$class->lang['property'].')',   'securityObject' => 'SalesOrderProperty',   'phplist' => 'salesOrderPropertyList', 'target' => 'tab' ));

    pushMenuItem($arrSubMenu['menu'], $submenuitem);   
    pushMenuItem($menuitem , $arrSubMenu); 


    $arrSubMenu = array ('label' => $class->lang['rentalSales']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array();     
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesRentalQuotation'],   'securityObject' => 'SalesRentalQuotation',   'phplist' => 'salesRentalQuotationList', 'target' => 'tab' ) );
    pushMenuItem($submenuitem , array ('label' => $class->lang['rentalSales'],   'securityObject' => 'SalesOrderRental',   'phplist' => 'salesOrderRentalList', 'target' => 'tab' ) );
    pushMenuItem($submenuitem , array ('label' => $class->lang['deliveryWorkOrder'],   'securityObject' => 'SalesOrderRentalWorkOrder',   'phplist' => 'salesOrderRentalWorkOrderList', 'target' => 'tab' ) );
    pushMenuItem($arrSubMenu['menu'], $submenuitem);   
    pushMenuItem($menuitem , $arrSubMenu); 



    // OTHERS
    $arrSubMenu = array ('label' => $class->lang['others']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array();     
    pushMenuItem($submenuitem , array ('label' => $class->lang['carRevenue'],   'securityObject' => 'CarRevenue',   'phplist' => 'carRevenueList', 'target' => 'tab' ),array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['marketplace'],   'securityObject' => 'Marketplace',   'phplist' => 'marketplaceList', 'target' => 'tab' ),array(1));
    pushMenuItem($submenuitem , array ('label' => $class->lang['storefront'],   'securityObject' => 'Storefront',   'phplist' => 'storefrontList', 'target' => 'tab' ), array(1)); 
    pushMenuItem($arrSubMenu['menu'], $submenuitem);   
    pushMenuItem($menuitem , $arrSubMenu); 


    $arrSubMenu = array ('label' => $class->lang['rateList']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array();  
    pushMenuItem($submenuitem , array ('label' => $class->lang['sellingRate'],   'securityObject' => 'TruckingSellingRate',   'phplist' => 'truckingSellingRateList', 'target' => 'tab' ),array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['costRate'],   'securityObject' => 'CostRate',   'phplist' => 'costRateList', 'target' => 'tab' ),array(2));
    pushMenuItem($arrSubMenu['menu'], $submenuitem);  
    pushMenuItem($menuitem , $arrSubMenu); 

    $arrSubMenu = array ('label' => $class->lang['others']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array();  
    pushMenuItem($submenuitem , array ('label' => $class->lang['driverProgressStep'],   'securityObject' => 'WorkProgressStep',   'phplist' => 'workProgressStepList', 'target' => 'tab' ),array(2));
    pushMenuItem($arrSubMenu['menu'], $submenuitem);  
    pushMenuItem($menuitem , $arrSubMenu); 

  
    $arrSubMenu = array ('label' => $class->lang['maintenance']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array();  
    pushMenuItem($submenuitem , array ('label' => $class->lang['maintenanceChecklist'],   'securityObject' => 'CarMaintenanceChecklist',   'phplist' => 'carMaintenanceChecklistList', 'target' => 'tab' ),array(3));
    pushMenuItem($arrSubMenu['menu'], $submenuitem);  
    pushMenuItem($menuitem , $arrSubMenu); 

    $arrSales['menu'] = array();
    pushMenuItem ($arrSales['menu'], $menuitem);   

    pushMenuItem ($arrMenu, $arrSales); 
  
    // MAINTENANCE
    $arrMaintenance = array ('label' => $class->lang['maintenance'],'icon' => 'fas fa-wrench'  );  
    $menuitem = array();
    pushMenuItem($menuitem , array ('label' => $class->lang['carMaintenance'],   'securityObject' => 'CarServiceMaintenance',   'phplist' => 'carServiceMaintenanceList', 'target' => 'tab' ),array(2));
    $arrMaintenance['menu'] = array();
    pushMenuItem ($arrMaintenance['menu'], $menuitem);   
    pushMenuItem ($arrMenu, $arrMaintenance); 
 
   
    // AFTER SALES
/*    $arrSubMenu = array ('label' => $class->lang['afterSales'],'icon' => 'fas fa-tasks'  );  
    $submenuitem = array();
    $arrSubMenu['menu'] = array();
    pushMenuItem($submenuitem , array ('label' => $class->lang['warrantyClaim'],   'securityObject' => $warrantyClaim->securityObject,   'phplist' => 'warrantyClaimList', 'target' => 'tab' ),array(5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['warrantyClaimProgress'],   'securityObject' => $warrantyClaimProgress->securityObject,   'phplist' => 'warrantyClaimProgressList', 'target' => 'tab' ),array(5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['vendorWarrantyClaim'],   'securityObject' => $vendorWarrantyClaim->securityObject,   'phplist' => 'vendorWarrantyClaimList', 'target' => 'tab' ),array(5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['vendorWarrantyClaimReceive'],   'securityObject' => $vendorWarrantyClaimReturn->securityObject,   'phplist' => 'vendorWarrantyClaimReturnList', 'target' => 'tab' ),array(5));
    
    pushMenuItem ($arrSubMenu['menu'], $submenuitem);   
    pushMenuItem ($arrMenu, $arrSubMenu); */
  
    //pushMenuItem($menuitem , array ('label' => $class->lang['pointofsales'],   'securityObject' => $salesOrder->securityObject,   'phplist' => 'pointofsales', 'target' => '_blank' ));
    //pushMenuItem($menuitem , array ('label' => $class->lang['preorderSales'],   'securityObject' => $preorder->securityObject,   'phplist' => 'preorderList', 'target' => 'tab' ));
   

    // DEPOT
    $arrDepot = array ('label' => $class->lang['depot'],'icon' => 'fas fa-warehouse'  );  
    $menuitem = array();
    pushMenuItem($menuitem , array ('label' => $class->lang['itemIn'],   'securityObject' => 'ItemInDepot',   'phplist' => 'itemInDepotList', 'target' => 'tab' ),array(2));
    pushMenuItem($menuitem , array ('label' => $class->lang['itemOut'],   'securityObject' => 'ItemOutDepot',   'phplist' => 'itemOutDepotList', 'target' => 'tab' ),array(2));
    $arrDepot['menu'] = array();
    pushMenuItem ($arrDepot['menu'], $menuitem);   
    pushMenuItem ($arrMenu, $arrDepot); 
 


    // FINANCE
    $arrFinance = array ('label' => $class->lang['finance'], 'icon' => 'fas fa-money-check-alt'); 
 
    $menuitem = array(); 
  
        // ASSETS
        /*
        $arrSubMenu = array ('label' => $class->lang['assets']);  
        $submenuitem = array();
        $arrSubMenu['menu'] = array(); 
        pushMenuItem($submenuitem , array ('label' => $class->lang['assetsList'],   'securityObject' => $assets->securityObject,   'phplist' => 'assetsList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['assetsCategory'],   'securityObject' => $assetsCategory->securityObject,   'phplist' => 'assetsCategoryList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['assetsPurchaseOrder'],   'securityObject' => $purchaseOrderAssets->securityObject,   'phplist' => 'purchaseOrderAssetsList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['assetsDepreciation'],   'securityObject' => $assetsDepreciation->securityObject,   'phplist' => 'assetsDepreciationList', 'target' => 'tab' ));
        pushMenuItem($arrSubMenu['menu'], $submenuitem); 
        pushMenuItem($menuitem , $arrSubMenu); 
        */

        // SALES
        $arrSubMenu = array ('label' => $class->lang['sales']);  
        $submenuitem = array();
        $arrSubMenu['menu'] = array();  
        pushMenuItem($submenuitem , array ('label' => $class->lang['salesInvoice'],   'securityObject' => 'TruckingServiceOrderInvoice',   'phplist' => 'truckingServiceOrderInvoiceList', 'target' => 'tab' ),array(2));
        pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrderInvoiceReceipt'],   'securityObject' => 'SalesOrderInvoiceReceipt',   'phplist' => 'salesOrderInvoiceReceiptList', 'target' => 'tab' ),array(2));
        pushMenuItem($submenuitem , array ('label' => $class->lang['salesInvoice'],   'securityObject' => 'EMKLOrderInvoice',   'phplist' => 'emklOrderInvoiceList', 'target' => 'tab' ),array(2));
        pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrderInvoiceReceipt'],   'securityObject' => 'EMKLInvoiceReceipt',   'phplist' => 'emklInvoiceReceiptList', 'target' => 'tab' ),array(2));
        pushMenuItem($submenuitem , array ('label' => $class->lang['invoiceTaxNumber'],   'securityObject' => 'InvoiceTax',   'phplist' => 'invoiceTaxList', 'target' => 'tab' ),array(2));
        pushMenuItem($submenuitem , array ('label' => $class->lang['salesInvoiceRental'],   'securityObject' => 'SalesOrderRentalInvoice',   'phplist' => 'salesOrderRentalInvoiceList', 'target' => 'tab' ) );
 
        pushMenuItem($arrSubMenu['menu'], $submenuitem); 
        pushMenuItem($menuitem , $arrSubMenu); 

 
        // KAS BANK
        $arrSubMenu = array ('label' => $class->lang['cashBank']);  
        $submenuitem = array();
        $arrSubMenu['menu'] = array(); 
        pushMenuItem($submenuitem , array ('label' => $class->lang['costList'],   'securityObject' => 'CostCashOut',   'phplist' => 'costCashOutList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['revenueList'],   'securityObject' => 'RevenueCashIn',   'phplist' => 'revenueCashInList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['cashAdvance'],   'securityObject' => 'CashAdvance',   'phplist' => 'cashAdvanceList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['cashAdvanceRealization'],   'securityObject' => 'cashAdvanceRealization',   'phplist' => 'cashAdvanceRealizationList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['cashBankIn'],   'securityObject' => 'CashBankIn',   'phplist' => 'cashBankInList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['cashIn'],   'securityObject' => 'CashIn',   'phplist' => 'cashInList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['cashOut'],   'securityObject' => 'CashOut',   'phplist' => 'cashOutList', 'target' => 'tab' ));
         //pushMenuItem($submenuitem , array ('label' => $class->lang['truckingCostCashIn'],   'securityObject' => $truckingCostCashIn->securityObject,   'phplist' => 'truckingCostCashInList', 'target' => 'tab' ),array(2));
        pushMenuItem($submenuitem , array ('label' => $class->lang['truckingCostCashOut'],   'securityObject' => 'TruckingCostCashOut',   'phplist' => 'truckingCostCashOutList', 'target' => 'tab' ),array(2));
        pushMenuItem($submenuitem , array ('label' => $class->lang['cashBankTransfer'],   'securityObject' => 'CashBankTransfer',   'phplist' => 'cashBankTransferList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['cashBankRealization'],   'securityObject' => 'CashBankRealization',   'phplist' => 'cashBankRealizationList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['cashAndBankVoucher'],   'securityObject' => 'CashBank',   'phplist' => 'cashBankList', 'target' => 'tab' ));
        pushMenuItem($arrSubMenu['menu'], $submenuitem); 
  
        pushMenuItem($menuitem , $arrSubMenu); 

        // DOWNPAYMENT
        $arrSubMenu = array ('label' => $class->lang['downpayment']);  
        $submenuitem = array();
        $arrSubMenu['menu'] = array(); 
        pushMenuItem($submenuitem , array ('label' => $class->lang['customerDownpayment'],   'securityObject' => 'CustomerDownpayment',   'phplist' => 'customerDownpaymentList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['customerDownpaymentSettlement'],   'securityObject' => 'CustomerDownpaymentSettlement',   'phplist' => 'customerDownpaymentSettlementList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['supplierDownpayment'],   'securityObject' => 'SupplierDownpayment',   'phplist' => 'supplierDownpaymentList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['supplierDownpaymentSettlement'],   'securityObject' => 'SupplierDownpaymentSettlement',   'phplist' => 'supplierDownpaymentSettlementList', 'target' => 'tab' ));
        pushMenuItem($arrSubMenu['menu'], $submenuitem); 
  
        pushMenuItem($menuitem , $arrSubMenu); 


        // AR/AP
        $arrSubMenu = array ('label' => $class->lang['accountsPayable']);  
        $submenuitem = array();
        $arrSubMenu['menu'] = array(); 
        pushMenuItem($submenuitem , array ('label' => $class->lang['accountsPayable'],   'securityObject' => 'AP',   'phplist' => 'apList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['accountsPayablePayment'],   'securityObject' => 'APPayment',   'phplist' =>  'apPaymentList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['payableTax23'],   'securityObject' => 'APPayableTax23',   'phplist' => 'apPayableTax23List', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['payableTax23Payment'],   'securityObject' => 'APPayableTax23Payment',   'phplist' => 'apPayableTax23PaymentList', 'target' => 'tab' ));
        pushMenuItem($arrSubMenu['menu'], $submenuitem); 
  
        pushMenuItem($menuitem , $arrSubMenu); 

        // AR/AP
        $arrSubMenu = array ('label' => $class->lang['accountsReceivable']);  
        $submenuitem = array();
        $arrSubMenu['menu'] = array(); 
        pushMenuItem($submenuitem , array ('label' => $class->lang['accountsReceivable'],   'securityObject' => 'AR',   'phplist' => 'arList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['accountsReceivablePayment'],   'securityObject' => 'ARPayment',   'phplist' => 'arPaymentList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['arapNetting'],   'securityObject' => 'ARAPNetting',   'phplist' => 'arapNettingList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['creditNote'],   'securityObject' => 'creditNote',   'phplist' => 'creditNoteList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['ARDiscountApproval'],   'securityObject' => 'ARDiscountApproval',   'phplist' => 'arDiscountApprovalList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['prepaidTax23'],   'securityObject' => 'ARPrepaidTax23',   'phplist' => 'arPrepaidTax23List', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['prepaidTax23Payment'],   'securityObject' => 'ARPrepaidTax23Payment',   'phplist' => 'arPrepaidTax23PaymentList', 'target' => 'tab' ));
        pushMenuItem($arrSubMenu['menu'], $submenuitem); 
  
        pushMenuItem($menuitem , $arrSubMenu); 

       
        // AR/AP
        $arrSubMenu = array ('label' => $class->lang['employeeARAP']);  
        $submenuitem = array();
        $arrSubMenu['menu'] = array(); 
        pushMenuItem($submenuitem , array ('label' => $class->lang['employeeAP'],   'securityObject' => 'APEmployee',   'phplist' => 'apEmployeeList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['employeeAPPayment'],   'securityObject' => 'APEmployeePayment',   'phplist' => 'apEmployeePaymentList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['employeeAR'],   'securityObject' => 'AREmployee',   'phplist' => 'arEmployeeList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['employeeARPayment'],   'securityObject' => 'AREmployeePayment',   'phplist' => 'arEmployeePaymentList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['employeeARAPNetting'],   'securityObject' => 'ARAPEmployeeNetting',   'phplist' => 'arapEmployeeNettingList', 'target' => 'tab' ));
        pushMenuItem($arrSubMenu['menu'], $submenuitem); 
  
        pushMenuItem($menuitem , $arrSubMenu); 

       
        // AR/AP
        $arrSubMenu = array ('label' => $class->lang['commission']);  
        $submenuitem = array();
        $arrSubMenu['menu'] = array(); 
        pushMenuItem($submenuitem , array ('label' => $class->lang['employeeCommission'],   'securityObject' => 'APEmployeeCommission',   'phplist' => 'apEmployeeCommissionList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['employeeCommissionPayment'],   'securityObject' => 'APEmployeeCommissionPayment',   'phplist' => 'apEmployeeCommissionPaymentList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['apCommission'],   'securityObject' => 'APCommission',   'phplist' => 'apCommissionList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['apCommissionPayment'],   'securityObject' => 'APCommissionPayment',   'phplist' => 'apCommissionPaymentList', 'target' => 'tab' ));
        pushMenuItem($arrSubMenu['menu'], $submenuitem); 
  
        pushMenuItem($menuitem , $arrSubMenu); 


       
        // Pepaid Expense
        $arrSubMenu = array ('label' => $class->lang['prepaidCost']);  
        $submenuitem = array();
        $arrSubMenu['menu'] = array(); 
        pushMenuItem($submenuitem , array ('label' => $class->lang['prepaidCost'],   'securityObject' => 'prepaidExpense',   'phplist' => 'prepaidExpenseList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['costReconsiliation'],   'securityObject' => 'CostReconsile',   'phplist' => 'costReconsileList', 'target' => 'tab' ));
        pushMenuItem($arrSubMenu['menu'], $submenuitem);
        pushMenuItem($menuitem , $arrSubMenu); 
 
        // GL
        $arrSubMenu = array ('label' => $class->lang['GL']);  
        $submenuitem = array();
        $arrSubMenu['menu'] = array(); 
        pushMenuItem($submenuitem , array ('label' => $class->lang['chartOfAccount'],   'securityObject' => 'ChartOfAccount',   'phplist' => 'chartOfAccountList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['generalJournal'],   'securityObject' => 'GeneralJournal',   'phplist' => 'generalJournalList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['journalBalancing'],   'securityObject' => 'JournalBalancing',   'phplist' => 'journalBalancingList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['coalink'],   'securityObject' => 'COALink',   'phplist' => 'coaLinkForm', 'target' => 'tab' ));
        pushMenuItem ($arrSubMenu['menu'], $submenuitem); 

        pushMenuItem($menuitem , $arrSubMenu); 

        // OTHERS
        $arrSubMenu = array ('label' => $class->lang['others']);  
        $submenuitem = array();
        $arrSubMenu['menu'] = array(); 

        if (PLAN_TYPE['usefrontend'] == 1){
            pushMenuItem($submenuitem , array ('label' => $class->lang['paymentConfirmation'],   'securityObject' => 'PaymentConfirmation',   'phplist' => 'paymentConfirmationList', 'target' => 'tab' ));
        }

       	pushMenuItem($submenuitem , array ('label' => $class->lang['termofpayment'],   'securityObject' => 'TermOfPayment',   'phplist' => 'termOfPaymentList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['invoicePeriod'],   'securityObject' => 'InvoicePeriod',   'phplist' => 'invoicePeriodList', 'target' => 'tab' ));   
        pushMenuItem($submenuitem , array ('label' => $class->lang['paymentMethod'],   'securityObject' => 'PaymentMethod',   'phplist' => 'paymentMethodList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['currency'],   'securityObject' => 'Currency',   'phplist' => 'currencyList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['currencyRate'],   'securityObject' => 'CurrencyRate',   'phplist' => 'currencyRateList', 'target' => 'tab' ));
        //pushMenuItem($submenuitem , array ('label' => $class->lang['leasing'],   'securityObject' => $leasing->securityObject,   'phplist' => 'leasingList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['routineCost'],   'securityObject' => 'RoutineCost',   'phplist' => 'routineCostList', 'target' => 'tab' ));
        pushMenuItem ($arrSubMenu['menu'], $submenuitem); 

    pushMenuItem($menuitem , $arrSubMenu); 

    $arrFinance['menu'] = array();
    pushMenuItem ($arrFinance['menu'], $menuitem);  

    pushMenuItem ($arrMenu, $arrFinance); 
 

    // MEDICAL
    $arrMedical = array ('label' => $class->lang['medicalRecord'], 'icon' => 'fas fa-money-check-alt');  
    $menuitem = array();
    $arrSubMenu['menu'] = array();    
    pushMenuItem($menuitem , array ('label' => $class->lang['medicalRecord'],   'securityObject' => 'MedicalRecord',   'phplist' => 'medicalRecordList', 'target' => 'tab' ));       
    
    $arrMedical['menu'] = array();
    pushMenuItem ($arrMedical['menu'], $menuitem);  

    pushMenuItem ($arrMenu, $arrMedical); 



    // MEDIA 
    if (PLAN_TYPE['usefrontend'] == 1){
        $arrMedia = array ('label' => $class->lang['articleNewsAndMedia'], 'icon' => 'fas fa-newspaper');  
        $menuitem = array();
        pushMenuItem($menuitem , array ('label' => $class->lang['article'],   'securityObject' => 'Article',   'phplist' => 'articleList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['articleCategory'],   'securityObject' => 'ArticleCategory',   'phplist' => 'articleCategoryList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['news'],   'securityObject' => 'News',   'phplist' => 'newsList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['newsCategory'],   'securityObject' => 'NewsCategory',   'phplist' => 'newsCategoryList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['portfolio'],   'securityObject' => 'Portfolio',   'phplist' => 'portfolioList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['portfolioCategory'],   'securityObject' => 'PortfolioCategory',   'phplist' => 'portfolioCategoryList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['gallery'],   'securityObject' => 'Gallery',   'phplist' => 'galleryList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['galleryCategory'],   'securityObject' => 'GalleryCategory',   'phplist' => 'galleryCategoryList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['galleryHumanResource'],   'securityObject' => 'GalleryHumanResource',   'phplist' => 'galleryHumanResourceList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['galleryHumanResourceCategory'],   'securityObject' => 'GalleryHumanResourceCategory',   'phplist' => 'galleryHRCategoryList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['youtube'],   'securityObject' => 'Youtube',   'phplist' => 'youtubeList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['banner'],   'securityObject' => 'Banner',   'phplist' => 'bannerList', 'target' => 'tab' ));
       
        $arrMedia['menu'] = array();
        pushMenuItem ($arrMedia['menu'], $menuitem);   
        pushMenuItem ($arrMenu, $arrMedia); 
    }
  
    // EVENT
    /*
    $arrEvent = array ('label' => $class->lang['event'], 'icon' => 'fas fa-calendar-alt', 'securityObject' => $event->securityObject,   'phplist' => 'eventList', 'target' => 'tab'   );  
    pushMenuItem ($arrMenu, $arrEvent,true); 
    */

    // COURSE 
    $arrOthers = array ('label' => $class->lang['course'], 'icon' => 'fas fa-bullhorn');  
    $menuitem = array();
    
    pushMenuItem($menuitem , array ('label' => $class->lang['courseCategory'],   'securityObject' => 'CourseCategory',  'phplist' => 'courseCategoryList', 'target' => 'tab' ),array(1));
    pushMenuItem($menuitem , array ('label' => $class->lang['courseList'],   'securityObject' => 'Course',   'phplist' => 'courseList', 'target' => 'tab' ),array(1));
    pushMenuItem($menuitem , array ('label' => $class->lang['quiz'],   'securityObject' => 'Quiz',   'phplist' => 'quizList', 'target' => 'tab' ),array(1));
    pushMenuItem($menuitem , array ('label' => $class->lang['quizResult'],   'securityObject' => 'Quiz',   'phplist' => 'quizResultList', 'target' => 'tab' ),array(1));
 
    $arrOthers['menu'] = array();
    pushMenuItem ($arrOthers['menu'], $menuitem);   
    pushMenuItem ($arrMenu, $arrOthers); 
    


    // COURSE 
    $arrOthers = array ('label' => $class->lang['meeting'], 'icon' => 'fas fa-bullhorn');  
    $menuitem = array();
    
    pushMenuItem($menuitem , array ('label' => $class->lang['meetingPoint'],   'securityObject' => 'MeetingPoint',  'phplist' => 'meetingPointList', 'target' => 'tab' ),array(1));
    pushMenuItem($menuitem , array ('label' => $class->lang['meetingSchedule'],   'securityObject' => 'MeetingSchedule',   'phplist' => 'meetingScheduleList', 'target' => 'tab' ),array(1));
    
    $arrOthers['menu'] = array();
    pushMenuItem ($arrOthers['menu'], $menuitem);   
    pushMenuItem ($arrMenu, $arrOthers); 
    


    // CAREER 
    $arrOthers = array ('label' => $class->lang['jobOpportunities'], 'icon' => 'fas fa-handshake');  
    $menuitem = array();

    pushMenuItem($menuitem , array ('label' => $class->lang['careerCategory'],   'securityObject' => 'CareerCategory',   'phplist' => 'careerCategoryList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['careerField'],   'securityObject' => 'CareerField',   'phplist' => 'careerFieldList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['jobOpportunities'],   'securityObject' => 'JobOpportunities',   'phplist' => 'jobOpportunitiesList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['recruitment'],   'securityObject' => 'recruitment',   'phplist' => 'recruitmentList', 'target' => 'tab' ));
    
    $arrOthers['menu'] = array();
    pushMenuItem ($arrOthers['menu'], $menuitem);   
    pushMenuItem ($arrMenu, $arrOthers); 
    
    //PROMO & CAMPAIGN
    
    $arrOthers = array ('label' => $class->lang['promoAndCampaign'], 'icon' => 'fas fa-bullhorn');  
    $menuitem = array();
    pushMenuItem($menuitem , array ('label' => $class->lang['receiptValidation'],   'securityObject' => 'ReceiptValidation',   'phplist' => 'itemUploadReceiptList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['campaign'],  'securityObject' => 'Campaign',   'phplist' => 'campaignList', 'target' => 'tab' ),array(1));
    pushMenuItem($menuitem , array ('label' => $class->lang['voucher'], 'securityObject' => 'Voucher',   'phplist' => 'voucherList', 'target' => 'tab' ),array(1,5));
    pushMenuItem($menuitem , array ('label' => $class->lang['voucherTransaction'],   'securityObject' => 'VoucherTransaction',   'phplist' => 'voucherTransactionList', 'target' => 'tab' ),array(1,5));
    pushMenuItem($menuitem , array ('label' => $class->lang['cashback'], 'securityObject' => 'PointCashback',   'phplist' => 'pointCashbackList', 'target' => 'tab' ));
    
    
    pushMenuItem($menuitem , array ('label' => $class->lang['discountScheme'],   'securityObject' => 'DiscountScheme',   'phplist' => 'discountSchemeList', 'target' => 'tab' ));
    // pushMenuItem($menuitem , array ('label' => $class->lang['rewardPoints'],   'securityObject' => $rewardsPoint->securityObject,   'phplist' => 'rewardsPointList', 'target' => 'tab' ));
    $arrOthers['menu'] = array();
    pushMenuItem ($arrOthers['menu'], $menuitem);   
    pushMenuItem ($arrMenu, $arrOthers); 
    

    /*
    // PORTFOLIO
    $arrOthers = array ('label' => $class->lang['portfolio'], 'icon' => 'fas fa-file-archive-o');  
    $menuitem = array();
    pushMenuItem($menuitem , array ('label' => $class->lang['portfolio'],   'securityObject' => $portfolio->securityObject,   'phplist' => 'portfolioList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['portfolioCategory'],   'securityObject' => $portfolioCategory->securityObject,   'phplist' => 'portfolioCategoryList', 'target' => 'tab' ));
    $arrOthers['menu'] = array();
    pushMenuItem ($arrOthers['menu'], $menuitem);   
    pushMenuItem ($arrMenu, $arrOthers, true); 
    */

    // SECURITY PRIVILAGES
    $arrBusinessPartner = array ('label' => $class->lang['securityPrivileges'], 'icon' => 'fas fa-lock' );  
	$menuitem = array();
    pushMenuItem($menuitem , array ('label' => $class->lang['userPrivileges'],   'securityObject' => 'SecurityPrivileges',   'phplist' => 'securityPrivilegesList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['roleTemplate'],   'securityObject' => 'RoleTemplate',   'phplist' => 'roleTemplateList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['customCode'],   'securityObject' => 'customCode',   'phplist' => 'customCodeList', 'target' => 'tab' ));
    $arrBusinessPartner['menu'] = array();
    pushMenuItem ($arrBusinessPartner['menu'], $menuitem);  
    pushMenuItem ($arrMenu, $arrBusinessPartner); 
 

  
    // REPORT

    $reportPath = 'report/';

    $arrReport = array ('label' => $class->lang['report'], 'icon' => 'fas fa-clipboard-list');  
    $menuitem = array();
  
    // BUSSINESS PARTNER REPORT 
    $arrSubMenu = array ('label' => $class->lang['businessPartner']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 
    pushMenuItem($submenuitem , array ('label' => $class->lang['employeeReport'],   'securityObject' => 'reportEmployee',   'phplist' => $reportPath.'reportEmployee', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['customerReport'],   'securityObject' => 'reportCustomer',   'phplist' => $reportPath.'reportCustomer', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['supplierReport'],   'securityObject' => 'reportSupplier',   'phplist' => $reportPath.'reportSupplier', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['consigneeReport'],   'securityObject' => 'reportConsignee',   'phplist' => $reportPath.'reportConsignee', 'target' => '_blank' ));
    pushMenuItem ($arrSubMenu['menu'], $submenuitem); 
    pushMenuItem($menuitem , $arrSubMenu); 
 
    // INVENTORY
    $arrSubMenu = array ('label' => $class->lang['inventory']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 
    pushMenuItem($submenuitem , array ('label' => $class->lang['itemReport'],   'securityObject' => 'reportItem',   'phplist' => $reportPath.'reportItem', 'target' => '_blank' ), array(1,2,3,5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['serviceReport'],   'securityObject' => 'reportServices',   'phplist' => $reportPath.'reportServices', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['itemPackageReport'],   'securityObject' => 'reportItem',   'phplist' => $reportPath.'reportItemPackage', 'target' => '_blank' ), array(1,2,3,5));
    //pushMenuItem($submenuitem , array ('label' => $class->lang['itemFilterReport'],   'securityObject' => 'reportItemFilter',   'phplist' => 'reportItemFilter', 'target' => '_blank' ),true);
    pushMenuItem($submenuitem , array ('label' => $class->lang['itemInReport'],   'securityObject' => 'reportItemIn',   'phplist' => $reportPath.'reportItemIn', 'target' => '_blank' ), array(1,2,3,5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['itemOutReport'],   'securityObject' => 'reportItemOut',   'phplist' => $reportPath.'reportItemOut', 'target' => '_blank' ), array(1,2,3,5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['itemAdjustmentReport'],   'securityObject' => 'reportItemAdjustment',   'phplist' => $reportPath.'reportItemAdjustment', 'target' => '_blank' ), array(1,2,3,5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['warehouseTransferReport'],   'securityObject' => 'reportWarehouseTransfer',   'phplist' => $reportPath.'reportWarehouseTransfer', 'target' => '_blank' ), array(1,2,3,5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['itemAgingReport'],   'securityObject' => 'reportItem',   'phplist' => $reportPath.'reportItemAging', 'target' => '_blank' ), array(1,2,3,5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['stockCardReport'],   'securityObject' => 'reportStockCard',   'phplist' => $reportPath.'reportStockCard', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['snMovementReport'],   'securityObject' => 'reportItemMovementSN',   'phplist' => $reportPath.'reportItemMovementSN', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => 'SN Gudang',   'securityObject' => 'reportItemMovementSN',   'phplist' => $reportPath.'reportSNInWarehouse', 'target' => '_blank' ));
    pushMenuItem ($arrSubMenu['menu'], $submenuitem);  
    pushMenuItem($menuitem , $arrSubMenu); 


    // PURCHASE REPORT 
    $arrSubMenu = array ('label' => $class->lang['purchase']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 
    pushMenuItem($submenuitem , array ('label' => $class->lang['purchaseOrderReport'],   'securityObject' => 'reportPurchaseOrder',   'phplist' => $reportPath.'reportPurchaseOrder', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['purchaseOrderImportReport'],   'securityObject' => 'reportPurchaseOrderImportFF',   'phplist' => $reportPath.'reportEMKLPurchaseOrderImport', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['purchaseOrderExportReport'],   'securityObject' => 'reportPurchaseOrderExportFF',   'phplist' => $reportPath.'reportEMKLPurchaseOrderExport', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['purchaseReceiveReport'],   'securityObject' => 'reportPurchaseReceive',   'phplist' => $reportPath.'reportPurchaseReceive', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['purchaseRefundReport'],   'securityObject' => 'ReportEMKLCommission',   'phplist' => $reportPath.'reportEMKLCommission', 'target' => '_blank' ));
    pushMenuItem ($arrSubMenu['menu'], $submenuitem); 
    pushMenuItem($menuitem , $arrSubMenu); 

 
    // SALES REPORT 
    $arrSubMenu = array ('label' => $class->lang['sales']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 

    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrderReport'],   'securityObject' => 'reportSalesOrder',   'phplist' => $reportPath.'reportSalesOrder', 'target' => '_blank' ), array(1));
    pushMenuItem($submenuitem , array ('label' => $class->lang['shipmentManifestReport'],   'securityObject' => 'reportSalesOrder',   'phplist' => $reportPath.'reportSalesOrderForShipment', 'target' => '_blank' ), array(1));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrderByGroupReport'],   'securityObject' => 'reportSalesOrder',   'phplist' => $reportPath.'reportSalesOrderItem', 'target' => '_blank' ), array(1));
    pushMenuItem($submenuitem , array ('label' => $class->lang['rentalTimesheetReport'],   'securityObject' => 'reportRentalTimesheet',   'phplist' => $reportPath.'reportRentalSchedule', 'target' => '_blank' ));
       
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrderDumperReport'],   'securityObject' => 'reportSalesOrderDumper',   'phplist' => $reportPath.'reportSalesOrderDumper', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrderSubscriptionReport'],   'securityObject' => 'reportSalesOrderSubscription',   'phplist' => $reportPath.'reportSalesOrderSubscription', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['installationWorkOrderReport'],   'securityObject' => 'reportInstallationWorkOrder',   'phplist' => $reportPath.'reportInstallationWorkOrder', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['installationBASTReport'],   'securityObject' => 'reportInstallationBAST',   'phplist' => $reportPath.'reportInstallationBAST', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['invoiceOrderSubscriptionReport'],   'securityObject' => 'reportInvoiceOrderSubscription',   'phplist' => $reportPath.'reportInvoiceOrderSubscription', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['ticketSupportReport'],   'securityObject' => 'reportTicketSupport',   'phplist' => $reportPath.'reportTicketSupport', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['ticketSupportWorkOrderReport'],   'securityObject' => 'reportTicketSupportWorkOrder',   'phplist' => $reportPath.'reportTicketSupportWorkOrder', 'target' => '_blank' ));
    
    // FF
    pushMenuItem($submenuitem , array ('label' => $class->lang['jobOrderHeaderImportReport'] ,   'securityObject' => 'reportEmklJobOrderHeaderImport',   'phplist' => $reportPath.'reportEMKLJobOrderImportHeader', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrderImportReport'] ,   'securityObject' => 'reportSalesOrderImportFF',   'phplist' => $reportPath.'reportEMKLJobOrderImport', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['jobOrderHeaderExportReport'] ,   'securityObject' => 'reportEmklJobOrderHeaderExport',   'phplist' => $reportPath.'reportEMKLJobOrderExportHeader', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrderExportReport'] ,   'securityObject' => 'reportSalesOrderExportFF',   'phplist' => $reportPath.'reportEMKLJobOrderExport', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['serviceOrderInvoiceReport'],   'securityObject' => 'reportSalesOrderInvoiceFF',   'phplist' => $reportPath.'reportEMKLSalesOrderInvoice', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['uninvoicedSOExportReport'] ,   'securityObject' => 'reportSalesOrderExportFF',   'phplist' => $reportPath.'reportEMKLUninvoicedJobOrderExport', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrderToInvoiceReport'] ,   'securityObject' => 'reportSalesOrderExportFF',   'phplist' => $reportPath.'reportEMKLJobOrderToInvoice', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['transactionCashFlowReport'],   'securityObject' => 'reportTransactionCashFlow',   'phplist' => $reportPath.'reportEMKLTransactionCashFlow', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesDeliveryReport'],   'securityObject' => 'reportSalesDelivery',   'phplist' => $reportPath.'reportSalesDelivery', 'target' => '_blank' ), array(1,2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrderInvoiceReceiptReport'],   'securityObject' => 'reportEMKLInvoiceReceipt',   'phplist' => $reportPath.'reportEMKLInvoiceReceipt', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrderSummaryReport'] ,   'securityObject' => 'reportSalesOrderSummaryFF',   'phplist' => $reportPath.'reportEMKLSalesOrderSummary', 'target' => '_blank' ), array(2));
    
    // service mobil
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrderReport'],   'securityObject' => 'reportSalesOrder',   'phplist' => $reportPath.'reportSalesOrderCarService', 'target' => '_blank' ), array(2,3));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesReturnReport'],   'securityObject' => 'reportSalesCarServiceReturn',   'phplist' => $reportPath.'reportSalesCarServiceReturn', 'target' => '_blank' ), array(2,3));

    pushMenuItem($submenuitem , array ('label' => $class->lang['jobOrderReport'],   'securityObject' => 'reportTruckingServiceOrder',   'phplist' => $reportPath.'reportTruckingServiceOrder', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['workOrderReport'],   'securityObject' => 'reportTruckingServiceWorkOrder',   'phplist' => $reportPath.'reportTruckingServiceWorkOrder', 'target' => '_blank' ), array(2));
    //pushMenuItem($submenuitem , array ('label' => $class->lang['workOrderCostReport'],   'securityObject' => 'reportTruckingServiceWorkOrder',   'phplist' => 'reportTruckingServiceWorkOrderCost', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['costReport'],   'securityObject' => 'reportTruckingCost',   'phplist' => $reportPath.'reportTruckingCost', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['monthlySalesReport'],   'securityObject' => 'reportTruckingServiceOrder',   'phplist' => $reportPath.'reportMonthlySales', 'target' => '_blank' ), array(2));

    pushMenuItem($submenuitem , array ('label' => $class->lang['sellingRateReport'],   'securityObject' => 'reportSellingRate',   'phplist' => $reportPath.'reportTruckingSellingRate', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['costRateReport'],   'securityObject' => 'reportCostRate',   'phplist' => $reportPath.'reportTruckingCostRate', 'target' => '_blank' ));
    
    pushMenuItem ($arrSubMenu['menu'], $submenuitem); 
    pushMenuItem($menuitem , $arrSubMenu); 



    // DEPOT REPORT 
    $arrSubMenu = array ('label' => $class->lang['depot']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 

    pushMenuItem($submenuitem , array ('label' => $class->lang['itemInReport'],   'securityObject' => 'reportItemInDepot',   'phplist' => $reportPath.'reportItemInDepot', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['itemOutReport'],   'securityObject' => 'reportItemOutDepot',   'phplist' => $reportPath.'reportItemOutDepot', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['stockCardDepotReport'],   'securityObject' => 'reportStockCardDepot',   'phplist' => $reportPath.'reportStockCardDepot', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['depotItemMovementReport'],   'securityObject' => 'reportStockCardDepot',   'phplist' => $reportPath.'reportItemMovementDepot', 'target' => '_blank' ), array(2));
    pushMenuItem ($arrSubMenu['menu'], $submenuitem); 
    pushMenuItem($menuitem , $arrSubMenu); 
 

    // FINANCE REPORT
    $arrSubMenu = array ('label' => $class->lang['salesInvoice']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 
    pushMenuItem($submenuitem , array ('label' => $class->lang['serviceOrderInvoiceReport'],   'securityObject' => 'reportTruckingServiceOrderInvoice',   'phplist' => $reportPath.'reportTruckingServiceOrderInvoice', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrderInvoiceReceiptReport'],   'securityObject' => 'reportSalesOrderInvoiceReceipt',   'phplist' => $reportPath.'reportSalesOrderInvoiceReceipt', 'target' => '_blank' ));
    pushMenuItem ($arrSubMenu['menu'], $submenuitem);  
    pushMenuItem($menuitem , $arrSubMenu); 
 
    $arrSubMenu = array ('label' => $class->lang['downpayment']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 
    pushMenuItem($submenuitem , array ('label' => $class->lang['customerDownpaymentReport'],   'securityObject' => 'reportCustomerDownpayment',   'phplist' => $reportPath.'reportCustomerDownpayment', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['supplierDownpaymentReport'],   'securityObject' => 'reportSupplierDownpayment',   'phplist' => $reportPath.'reportSupplierDownpayment', 'target' => '_blank' ));
    pushMenuItem ($arrSubMenu['menu'], $submenuitem);  
    pushMenuItem($menuitem , $arrSubMenu); 

    $arrSubMenu = array ('label' => $class->lang['cashBank']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array();  
    pushMenuItem($submenuitem , array ('label' => $class->lang['garageCashVoucherReport'],   'securityObject' => 'reportCashBankTrucking',   'phplist' => $reportPath.'reportCashBankVoucherTrucking', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['maintenanceCashVoucherReport'],   'securityObject' => 'reportCashBankMaintenance',   'phplist' => $reportPath.'reportCashBankVoucherMaintenance', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['cashAdvanceReport'],   'securityObject' => 'reportCashAdvance',   'phplist' => $reportPath.'reportCashAdvance', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['cashAdvanceRealizationReport'],   'securityObject' => 'reportCashAdvanceRealization',   'phplist' => $reportPath.'reportCashAdvanceRealization', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['cashBankInReport'],   'securityObject' => 'reportCashBankIn',   'phplist' => $reportPath.'reportCashBankIn', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['cashAndBankVoucherReport'],   'securityObject' => 'reportCashBankVoucher',   'phplist' => $reportPath.'reportCashBankVoucher', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['cashInReport'],   'securityObject' => 'reportCashIn',   'phplist' => $reportPath.'reportCashIn', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['cashOutReport'],   'securityObject' => 'reportCashOut',   'phplist' => $reportPath.'reportCashOut', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['cashBankTransferReport'],   'securityObject' => 'reportCashBankTransfer',   'phplist' => $reportPath.'reportCashBankTransfer', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['cashBankRealizationReport'],   'securityObject' => 'reportCashBankRealization',   'phplist' => $reportPath.'reportCashBankRealization', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['truckingCostCashOutReport'],   'securityObject' => 'reportTruckingCostCashOut',   'phplist' => $reportPath.'reportTruckingCostCashOut', 'target' => '_blank' ), array(2));
    //pushMenuItem($submenuitem , array ('label' => $class->lang['truckingCashFlowReportReport'],   'securityObject' => 'reportTruckingCostCashOut',   'phplist' => 'reportTruckingCashFlow', 'target' => '_blank' ), array(2));
    pushMenuItem ($arrSubMenu['menu'], $submenuitem);  
    pushMenuItem($menuitem , $arrSubMenu);


    $arrSubMenu = array ('label' => $class->lang['accountsPayable']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 
    pushMenuItem($submenuitem , array ('label' => $class->lang['APReport'],   'securityObject' => 'reportAP',   'phplist' => $reportPath.'reportAP', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['APPaymentReport'],   'securityObject' => 'reportAPPayment',   'phplist' =>  $reportPath.'reportAPPayment', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['APAgingReport'],   'securityObject' => 'reportAP',   'phplist' =>  $reportPath.'reportAPAging' , 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['APCardReport'],   'securityObject' => 'reportAP',   'phplist' => $reportPath.'reportAPCard', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['payableTax23Report'],   'securityObject' => 'reportAPPayableTax23',   'phplist' => $reportPath.'reportAPPayableTax23', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['payableTax23PaymentReport'],   'securityObject' => 'reportAPPayableTax23Payment',   'phplist' => $reportPath.'reportAPPayableTax23Payment', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['payableTax23PaymentReport'] . ' (Template)',   'securityObject' => 'reportAPPayableTax23Payment',   'phplist' => $reportPath.'reportAPPayableTax23PaymentTemplate', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['payableTax23AgingReport'],   'securityObject' => 'reportAPPayableTax23',   'phplist' =>  $reportPath.'reportAPPayableTax23Aging' , 'target' => '_blank' ));
    pushMenuItem ($arrSubMenu['menu'], $submenuitem);  
    pushMenuItem($menuitem , $arrSubMenu);


    $arrSubMenu = array ('label' => $class->lang['commissionAP']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 
    pushMenuItem($submenuitem , array ('label' => $class->lang['apCommissionReport'],   'securityObject' => 'reportAPCommission',   'phplist' => $reportPath.'reportAPCommission', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['apCommissionPaymentReport'],   'securityObject' => 'reportAPCommissionPayment',   'phplist' => $reportPath.'reportAPCommissionPayment', 'target' => '_blank' ));
    pushMenuItem ($arrSubMenu['menu'], $submenuitem);  
    pushMenuItem($menuitem , $arrSubMenu);



    $arrSubMenu = array ('label' => $class->lang['accountsReceivable']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 
    pushMenuItem($submenuitem , array ('label' => $class->lang['ARReport'],   'securityObject' => 'reportAR',   'phplist' => $reportPath.'reportAR', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['ARPaymentReport'],   'securityObject' => 'reportARPayment',   'phplist' => $reportPath.'reportARPayment', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['ARAgingReport'],   'securityObject' => 'reportAR',   'phplist' =>  $reportPath.'reportARAging' , 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['ARCardReport'].' ('.$class->lang['transaction'].')',   'securityObject' => 'reportAR',   'phplist' => $reportPath.'reportARCard', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['ARCardReport'].' ('.$class->lang['customer'].')',   'securityObject' => 'reportAR',   'phplist' => $reportPath.'reportARCardCustomer', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['prepaidTax23Report'],   'securityObject' => 'reportARPrepaidTax23',  'phplist' => $reportPath.'reportARPrepaidTax23', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['prepaidTax23PaymentReport'],   'securityObject' => 'reportARPrepaidTax23Payment',   'phplist' => $reportPath.'reportARPrepaidTax23Payment', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['prepaidTax23PaymentReport'] . ' (Template)',   'securityObject' => 'reportARPrepaidTax23Payment',   'phplist' => $reportPath.'reportARPrepaidTax23PaymentTemplate', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['prepaidTax23AgingReport'],   'securityObject' => 'reportARPrepaidTax23',  'phplist' => $reportPath.'reportARPrepaidTax23Aging', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['ARAPCashflowReport'],   'securityObject' => 'reportARAPCashflow',  'phplist' => $reportPath.'reportARAPCashflow', 'target' => '_blank' ));
    
 
    pushMenuItem ($arrSubMenu['menu'], $submenuitem);  
    pushMenuItem($menuitem , $arrSubMenu);
 

    $arrSubMenu = array ('label' => $class->lang['employeeAccountsReceivable']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 
    pushMenuItem($submenuitem , array ('label' => $class->lang['employeeAccountsReceivableReport'],   'securityObject' => 'reportAREmployee',   'phplist' => $reportPath.'reportAREmployee', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['employeeAccountsReceivablePaymentReport'],   'securityObject' => 'reportAREmployeePayment',   'phplist' => $reportPath.'reportAREmployeePayment', 'target' => '_blank' ));
    pushMenuItem ($arrSubMenu['menu'], $submenuitem);  
    pushMenuItem($menuitem , $arrSubMenu); 
 
    $arrSubMenu = array ('label' => $class->lang['employeeCommission']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 
    pushMenuItem($submenuitem , array ('label' => $class->lang['employeeCommissionReport'],   'securityObject' => 'reportAPEmployeeCommission',   'phplist' => $reportPath.'reportAPEmployeeCommission', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['employeeCommissionPaymentReport'],   'securityObject' => 'reportAPEmployeeCommissionPayment',   'phplist' => $reportPath.'reportAPEmployeeCommissionPayment', 'target' => '_blank' ));
    pushMenuItem ($arrSubMenu['menu'], $submenuitem);  
    pushMenuItem($menuitem , $arrSubMenu);
  

    $arrSubMenu = array ('label' => $class->lang['generalLedger']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 
    pushMenuItem($submenuitem , array ('label' => $class->lang['generalJournalReport'],   'securityObject' => 'reportGeneralJournal',   'phplist' => $reportPath.'reportGeneralJournal', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['generalLedgerReport'],   'securityObject' => 'reportGeneralLedger',   'phplist' => $reportPath.'reportGeneralLedger', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['balanceSheetReport'],   'securityObject' => 'reportBalanceSheet',   'phplist' => $reportPath.'reportBalanceSheet', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['trialBalanceReport'],   'securityObject' => 'reportTrialBalance',   'phplist' => $reportPath.'reportTrialBalance', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['incomeStatementReport'],   'securityObject' => 'reportIncomeStatement',   'phplist' => $reportPath.'reportIncomeStatement', 'target' => '_blank' ));
    //pushMenuItem($submenuitem , array ('label' => $class->lang['cashFlowReport'],   'securityObject' => 'reportCashFlow',   'phplist' => 'reportCashFlow', 'target' => '_blank' ));
    pushMenuItem ($arrSubMenu['menu'], $submenuitem);  
    pushMenuItem($menuitem , $arrSubMenu); 

     // OTHERS REPORT 
    $arrSubMenu = array ('label' => $class->lang['others']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 
    pushMenuItem($submenuitem , array ('label' => $class->lang['cityReport'],   'securityObject' => 'reportCity',   'phplist' => $reportPath.'reportCity', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['cityCategoryReport'],   'securityObject' => 'reportCityCategory',   'phplist' => $reportPath.'reportCityCategory', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['carReport'],   'securityObject' => 'reportCar',   'phplist' => $reportPath.'reportCar', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['carScheduleReport'],   'securityObject' => 'reportCarSchedule',   'phplist' => $reportPath.'reportCarSchedule', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['vehicleAvailabilityReport'],   'securityObject' => 'reportVehicleAvailability',   'phplist' => $reportPath.'reportVehicleAvailability', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['carMaintenanceReport'],   'securityObject' => 'reportCarServiceMaintenance',   'phplist' => $reportPath.'reportCarServiceMaintenance', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['carMaintenanceHistoryReport'],   'securityObject' => 'reportCarMaintenanceSalesHistory',   'phplist' => $reportPath.'reportCarMaintenanceSalesHistory', 'target' => '_blank' ), array(3));
    pushMenuItem($submenuitem , array ('label' => $class->lang['carMaintenanceHistoryReport'],   'securityObject' => 'reportCarMaintenanceHistory',   'phplist' => $reportPath.'reportCarMaintenanceHistory', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['carTurnoverReport'],   'securityObject' => 'reportTruckingServiceOrder',   'phplist' => $reportPath.'reportCarTurnover', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['ritaseSummaryReport'],   'securityObject' => 'reportRitaseSummary',   'phplist' => $reportPath.'reportRitaseSummary', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['warrantyClaimProgressReport'],   'securityObject' => 'reportWarrantyClaimProgress',   'phplist' => $reportPath.'reportWarrantyClaimProgress', 'target' => '_blank' ), array(5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['loginLogReport'],   'securityObject' => 'reportLoginLog',   'phplist' => $reportPath.'reportLoginLog', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['transactionLogReport'],   'securityObject' => 'reportTransactionLog',   'phplist' => $reportPath.'reportTransactionLog', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['marketplaceLogReport'],   'securityObject' => 'reportMarketplaceLog',   'phplist' => $reportPath.'reportMarketplaceLog', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['timespanReport'],   'securityObject' => 'reportTimespan',   'phplist' => $reportPath.'reportTimespan', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['voucherReport'],   'securityObject' => 'reportVoucher',  'phplist' => $reportPath.'reportVoucher', 'target' => '_blank' ));
    if (PLAN_TYPE['usefrontend'] == 1){
        pushMenuItem($submenuitem , array ('label' => $class->lang['newsletterSubscriptionReport'],   'securityObject' => 'ReportNewsletterSubscription',   'phplist' => $reportPath.'reportNewsletterSubscription', 'target' => '_blank' ), array(1));
    }

	pushMenuItem($submenuitem , array ('label' => $class->lang['apiLogReport'],  'securityObject' => 'reportAPILog',   'phplist' => $reportPath.'reportAPILog', 'target' => '_blank' ));
    

    pushMenuItem ($arrSubMenu['menu'], $submenuitem); 
    pushMenuItem($menuitem , $arrSubMenu); 
   
 

    $arrReport['menu'] = array();
    pushMenuItem ($arrReport['menu'], $menuitem);   
    pushMenuItem ($arrMenu, $arrReport); 
  

    // OTHERS
    $arrOthers = array ('label' => $class->lang['others'], 'icon' => 'fas fa-ellipsis-h');  
    $menuitem = array();     
  
    //pushMenuItem($menuitem , array ('label' => $class->lang['snInformation'],   'securityObject' => $item->securityObject,   'phplist' => 'snInformation', 'target' => 'tab' ), array(5));
    //pushMenuItem($menuitem , array ('label' => $class->lang['changeItemSN'],   'securityObject' => $changeItemSN->securityObject,   'phplist' => 'changeItemSNList', 'target' => 'tab' ), array(5));
    pushMenuItem($menuitem , array ('label' => $class->lang['container'],   'securityObject' => 'container',   'phplist' => 'containerList', 'target' => 'tab' ), array(2));
    pushMenuItem($menuitem , array ('label' => $class->lang['depotList'],   'securityObject' => 'Depot',   'phplist' => 'depotList', 'target' => 'tab' ), array(2));
    pushMenuItem($menuitem , array ('label' => $class->lang['portList'],   'securityObject' => 'Terminal',   'phplist' => 'portList', 'target' => 'tab' ), array(2));
    pushMenuItem($menuitem , array ('label' => $class->lang['terminalList'],   'securityObject' => 'Terminal',   'phplist' => 'terminalList', 'target' => 'tab' ), array(2));
    pushMenuItem($menuitem , array ('label' => $class->lang['vesselList'],   'securityObject' => 'vessel',   'phplist' => 'vesselList', 'target' => 'tab' ), array(2));
    //pushMenuItem($menuitem , array ('label' => $class->lang['warrantyPeriod'],   'securityObject' => $warrantyPeriod->securityObject,   'phplist' => 'warrantyPeriodList', 'target' => 'tab' ), array(5));
    pushMenuItem($menuitem , array ('label' => $class->lang['itemChecklist'],   'securityObject' => 'itemChecklist',   'phplist' => 'itemChecklistList', 'target' => 'tab' ), array(1,2,5));
    pushMenuItem($menuitem , array ('label' => $class->lang['itemChecklistGroup'],   'securityObject' => 'ItemChecklistGroup',   'phplist' => 'itemChecklistGroupList', 'target' => 'tab' ), array(1,5));
    pushMenuItem($menuitem , array ('label' => $class->lang['issueCategory'],   'securityObject' => 'IssueCategory',   'phplist' => 'issueCategoryList', 'target' => 'tab' ), array(5));
    pushMenuItem($menuitem , array ('label' => $class->lang['vehicleChecklist'],   'securityObject' => 'CarChecklist',   'phplist' => 'carChecklistList', 'target' => 'tab' ), array(2));
    pushMenuItem($menuitem , array ('label' => $class->lang['jobOpportunities'],   'securityObject' => 'JobOpportunities',   'phplist' => 'jobOpportunitiesList', 'target' => 'tab' ));
   
    pushMenuItem($menuitem , array ('label' => $class->lang['shipment'],   'securityObject' => 'Shipment',   'phplist' => 'shipmentList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['city'],   'securityObject' => 'City',   'phplist' => 'cityList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['cityCategory'],   'securityObject' => 'CityCategory',   'phplist' => 'cityCategoryList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['location'],   'securityObject' => 'location',   'phplist' => 'locationList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['division'],   'securityObject' => 'division',   'phplist' => 'divisionList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['faq'],   'securityObject' => 'FAQ',   'phplist' => 'faqList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['cancelReason'],   'securityObject' => 'CancelReason',   'phplist' => 'cancelReasonList', 'target' => 'tab' ));
   
    pushMenuItem($menuitem , array ('label' => $class->lang['media'],   'securityObject' => 'Media',   'phplist' => 'mediaList', 'target' => 'tab' ), array(6));
    pushMenuItem($menuitem , array ('label' => $class->lang['jobDetails'],   'securityObject' => 'JobDetails',   'phplist' => 'jobDetailsList', 'target' => 'tab' ), array(6));
    pushMenuItem($menuitem , array ('label' => $class->lang['stagesProcess'],   'securityObject' => 'StagesProcess',   'phplist' => 'stagesProcessList', 'target' => 'tab' ), array(6));
    
    if (PLAN_TYPE['usefrontend'] == 1){
        pushMenuItem($menuitem , array ('label' => $class->lang['downloadList'],   'securityObject' => 'Download',   'phplist' => 'downloadList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['webpage'],   'securityObject' => 'Page',   'phplist' => 'pageList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['testimonial'],   'securityObject' => 'Testimonial',   'phplist' => 'testimonialList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['contactUs'],   'securityObject' => 'Contact',   'phplist' => 'contactUsList', 'target' => 'tab' )); 
        pushMenuItem($menuitem , array ('label' => $class->lang['contactUsCategory'],   'securityObject' => 'ContactCategory',   'phplist' => 'contactCategoryList', 'target' => 'tab' )); 
        pushMenuItem($menuitem , array ('label' => $class->lang['newsletterSubscription'],   'securityObject' => 'NewsletterSubscription',   'phplist' => 'subscribeList', 'target' => 'tab' ), array(1));
    }
  

	//pushMenuItem($menuitem , array ('label' => $class->lang['bugReport'],   'securityObject' => '',   'phplist' => 'bugList', 'target' => 'tab' ));

 
    // SETTINGS
    $arrSubMenu = array ('label' => $class->lang['setting']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 


    //pushMenuItem($submenuitem , array ('label' => $class->lang['variableSetting'],   'securityObject' => $setting->securityObject,   'phplist' => 'setting', 'target' => 'tab' ));
    $rsSettingCategory = $setting->getSettingCategory();
    for($i=0;$i<count($rsSettingCategory);$i++) 
        pushMenuItem($submenuitem , array ('label' => $rsSettingCategory[$i]['category'],   'securityObject' => 'Setting',   'phplist' => 'setting/'. $rsSettingCategory[$i]['pkey'], 'target' => 'tab' ));

    pushMenuItem($submenuitem , array ('label' => $class->lang['codeSetting'],   'securityObject' => 'AutoCode',   'phplist' => 'autoCodeForm', 'target' => 'tab' ));
    pushMenuItem ($arrSubMenu['menu'], $submenuitem); 
    pushMenuItem($menuitem , $arrSubMenu); 

    // TOOLS
    $arrSubMenu = array ('label' => $class->lang['tools']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array();  
 
    pushMenuItem($submenuitem , array ('label' => $class->lang['import'].' SN',   'securityObject' => 'reportItem',   'phplist' => 'import/serialnumber', 'target' => '_blank' ),array(5));
    pushMenuItem ($arrSubMenu['menu'], $submenuitem); 

    pushMenuItem($menuitem , $arrSubMenu); 

    $arrOthers['menu'] = array();
    pushMenuItem ($arrOthers['menu'], $menuitem);   
    pushMenuItem ($arrMenu, $arrOthers); 
  

    $menu = buildMenu($arrMenu);  
 
    echo $menu; 
?>