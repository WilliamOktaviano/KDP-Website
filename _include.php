<?php  
//$start_time = microtime(TRUE);

$FILE_NAME = basename ($_SERVER['PHP_SELF'] ,".php");

require_once DOC_ROOT. 'connections/_connection.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/BaseClass.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/AutoCode.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Category.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ChartOfAccount.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Partners.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Employee.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/EmployeeCategory.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/City.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Item.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemDepot.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemCategory.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemUnit.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemMovement.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CashMovement.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/PurchaseRequest.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/PurchaseOrder.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/PurchaseReceive.class.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Supplier.class.php';       
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';      
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Currency.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CurrencyRate.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TermOfPayment.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/PaymentMethod.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemIn.class.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemOut.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemAdjustment.class.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/WarehouseTransfer.class.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemPromo.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Brand.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/RewardsPoint.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/EmailBlast.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Shipment.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CashIn.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CashOut.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CashBankTransfer.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CashBankIn.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemMovementPO.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/LoginLog.class.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/AR.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/AP.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ARAPNetting.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ARPayment.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/APPayment.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/AREmployee.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/AREmployeePayment.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/GeneralJournal.class.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/BillOfMaterials.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Assembly.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/RoleTemplate.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CityCategory.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/COALink.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CustomerCategory.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/DiscountScheme.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemChecklist.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemChecklistGroup.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Downpayment.class.php';      
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CustomerDownpayment.class.php';      
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/SupplierDownpayment.class.php';      
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Company.class.php';      
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemPackage.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/WorkProgressStep.class.php';      
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Tag.class.php';        
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/WorkProgress.class.php';      
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/IssueCategory.class.php';      
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CustomCode.class.php';         
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ARPrepaidTax23.class.php';      
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ARPrepaidTax23Payment.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/APPayableTax23.class.php';      
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/APPayableTax23Payment.class.php';         
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/WarrantyClaim.class.php';         
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/WarrantyClaimProgress.class.php';           
//require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Bug.class.php';              
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CashBankRealization.class.php';      
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CarServiceMaintenance.class.php';      
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/WarrantyPeriod.class.php';      
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemInReceive.class.php';      
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemOutDelivery.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CostCashOut.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/RevenueCashIn.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Exception.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/PHPMailer.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CreditNote.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/RoutineCost.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemCondition.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/WidgetSetting.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TemplateCustomer.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TemplateSupplier.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Membership.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Questionnaire.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/QuestionnaireResponse.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/SocialMedia.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CustomerMembership.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/MembershipAttendance.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemSpecification.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Voucher.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/VoucherTransaction.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CarChecklist.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Media.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/JobDetails.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/SalesOrderSubscription.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/InvoiceOrderSubscription.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/InstallationWorkOrder.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TicketSupport.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TicketSupportWorkOrder.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/StagesProcess.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TimeUnit.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/InstallationWorkOrder.class.php';
include_once DOC_ROOT. 'phpthumb/phpThumb.config.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/SalesCarServiceReturn.class.php';      
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/JobOpportunities.class.php';      
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Lang.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ManagementTeam.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Campaign.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ProjectDumper.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/SalesOrderDumper.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/SalesOrderDumperInvoice.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/SalesOrderRentalInvoice.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CourseCategory.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Course.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Quiz.class.php';  
 
$GLOBALS['ObjCol'] = array();
$GLOBALS['oDbCon'] = new Database($rs[0]['dbusername'],$rs[0]['dbpass'],$rs[0]['dbname'],$host);

$class = new Baseclass();     

$GLOBALS['class'] = $class;

$setting = new Setting(); 	   
$security = new Security();  
 
// load settings
$TABLEKEY_SETTINGS = $class->loadTableKeySettings();
define('TABLENAME_SETTINGS', array_column($TABLEKEY_SETTINGS,null,'tablename'));
define('TABLEKEY_SETTINGS', array_column($TABLEKEY_SETTINGS,null,'pkey'));

// define plan configuration 
$PLAN_TYPE = $security->getUserPlanType();
$PLAN_TYPE = $PLAN_TYPE[0];   
define('PLAN_TYPE', $PLAN_TYPE);

$useGL = $class->loadSetting('useGL');
$useGL = ($useGL == 1) ? true : false;  
define('USE_GL', $useGL);
 
$advancedFinance = $class->loadSetting('advancedFinance');
$advancedFinance = ($advancedFinance == 1) ? true : false; 
define('ADV_FINANCE', $advancedFinance);

define('PARTIAL_SHIPMENT',  PLAN_TYPE['partialshipment']); 

$multiCompany  = $class->loadSetting('multiCompany');
define('MULTI_COMPANY', ($multiCompany ==1) ? true : false);

/*$multiWarehouse  = $class->loadSetting('warehousePrivileges');
define('WAREHOUSE_PRIVILEGES', ($multiWarehouse ==1) ? true : false);*/

$autoCode = new AutoCode(); 
$employee =  createObjAndAddToCol(new Employee());
$employeeCategory = createObjAndAddToCol( new EmployeeCategory()); 
$partners = createObjAndAddToCol( new Partners());  
$city = createObjAndAddToCol( new City());
$warehouse = createObjAndAddToCol( new Warehouse()); 
$item = createObjAndAddToCol( new Item()); 
$itemDepot = createObjAndAddToCol( new ItemDepot()); 
$itemCategory = createObjAndAddToCol( new ItemCategory()); 
$itemUnit = createObjAndAddToCol( new ItemUnit());
$itemMovement = createObjAndAddToCol( new ItemMovement());
$cashMovement = createObjAndAddToCol( new CashMovement());
$purchaseRequest = createObjAndAddToCol(new PurchaseRequest());  
$purchaseOrder = createObjAndAddToCol(new PurchaseOrder());  
$purchaseReceive = createObjAndAddToCol(new PurchaseReceive()); 
$supplier = createObjAndAddToCol( new Supplier());
$customer = createObjAndAddToCol( new Customer());
$currency = createObjAndAddToCol( new Currency());
$currencyRate = createObjAndAddToCol( new CurrencyRate());
$termOfPayment = createObjAndAddToCol( new TermOfPayment());
$paymentMethod = createObjAndAddToCol( new PaymentMethod());
$itemIn = createObjAndAddToCol( new ItemIn());
$itemOut = createObjAndAddToCol( new ItemOut());
$itemAdjustment = createObjAndAddToCol( new ItemAdjustment());
$warehouseTransfer = createObjAndAddToCol( new WarehouseTransfer());
$itemPromo = createObjAndAddToCol( new ItemPromo());
$brand = createObjAndAddToCol( new Brand());
$rewardsPoint = createObjAndAddToCol( new RewardsPoint());
$emailBlast = new EmailBlast() ;
$shipment = createObjAndAddToCol( new Shipment());
$chartOfAccount= createObjAndAddToCol( new ChartOfAccount());
$cashIn= createObjAndAddToCol( new CashIn());
$cashOut= createObjAndAddToCol( new CashOut());
$cashBankTransfer = createObjAndAddToCol( new CashBankTransfer());
$cashBankIn = createObjAndAddToCol( new CashBankIn());
$itemMovementPO =  new ItemMovementPO();
$loginLog =  new LoginLog() ; 
$ar = createObjAndAddToCol(new AR());
$arEmployee = createObjAndAddToCol(new AREmployee());
$ap = createObjAndAddToCol(new AP());
$arapNetting = createObjAndAddToCol( new ARAPNetting());
$arPayment = createObjAndAddToCol( new ARPayment());
$arEmployeePayment = createObjAndAddToCol( new AREmployeePayment());
$apPayment = createObjAndAddToCol(new APPayment());
$generalJournal = createObjAndAddToCol(new GeneralJournal()); 
$billOfMaterials = createObjAndAddToCol( new BillOfMaterials()); 
$assembly = createObjAndAddToCol( new Assembly()); 
$roleTemplate = createObjAndAddToCol( new RoleTemplate());   
$cityCategory = createObjAndAddToCol( new CityCategory()); 
$coaLink =  new COALink() ;
$customerCategory = createObjAndAddToCol( new CustomerCategory());
$discountScheme = createObjAndAddToCol( new DiscountScheme());
$itemChecklist = createObjAndAddToCol( new ItemChecklist());
$itemChecklistGroup = createObjAndAddToCol( new ItemChecklistGroup()); 
$customerDownpayment = createObjAndAddToCol(new CustomerDownpayment()); 
$supplierDownpayment = createObjAndAddToCol( new SupplierDownpayment()); 
$company = createObjAndAddToCol( new Company()); 
$itemPackage = createObjAndAddToCol( new ItemPackage());  
$workProgressStep =  createObjAndAddToCol( new WorkProgressStep());
$tag = createObjAndAddToCol(new Tag());
$workProgress = createObjAndAddToCol( new WorkProgress());
$issueCategory = createObjAndAddToCol( new IssueCategory());
$customCode =  new CustomCode() ; 
$arPrepaidTax23 = createObjAndAddToCol( new ARPrepaidTax23());
$arPrepaidTax23Payment = createObjAndAddToCol( new ARPrepaidTax23Payment());
$apPayableTax23 = createObjAndAddToCol( new APPayableTax23());
$apPayableTax23Payment = createObjAndAddToCol( new APPayableTax23Payment()); 
$warrantyClaim = createObjAndAddToCol( new WarrantyClaim());
$warrantyClaimProgress = createObjAndAddToCol( new WarrantyClaimProgress());
//$bug = new Bug(DOMAIN_NAME);
$cashBankRealization = createObjAndAddToCol( new CashBankRealization()); 
$carServiceMaintenance = createObjAndAddToCol( new CarServiceMaintenance());
$warrantyPeriod = createObjAndAddToCol( new WarrantyPeriod());
$itemInReceive = createObjAndAddToCol( new ItemInReceive());
$itemOutDelivery = createObjAndAddToCol( new ItemOutDelivery());  
$salesCarServiceReturn = createObjAndAddToCol(new SalesCarServiceReturn());  
$costCashOut = createObjAndAddToCol(new CostCashOut());  
$revenueCashIn = createObjAndAddToCol(new RevenueCashIn());  
$creditNote = createObjAndAddToCol(new CreditNote());  
$routineCost = createObjAndAddToCol(new RoutineCost());  
$itemCondition = createObjAndAddToCol(new ItemCondition());     
$widgetSetting = new WidgetSetting();
$templateCustomer = createObjAndAddToCol(new TemplateCustomer());
$templateSupplier = createObjAndAddToCol(new TemplateSupplier());
$membership =  createObjAndAddToCol(new Membership());   
$questionnaire = createObjAndAddToCol(new Questionnaire());
$questionnaireResponse = createObjAndAddToCol(new QuestionnaireResponse());  
$socialMedia = createObjAndAddToCol(new SocialMedia());  
$customerMembership = createObjAndAddToCol(new CustomerMembership());
$membershipAttendance = createObjAndAddToCol(new MembershipAttendance());
$itemSpecification =  createObjAndAddToCol(new ItemSpecification());
$voucher =  createObjAndAddToCol(new Voucher());
$voucherTransaction =  createObjAndAddToCol(new VoucherTransaction());
$carChecklist =  createObjAndAddToCol(new CarChecklist());
$media =  createObjAndAddToCol(new Media());
$jobDetails =  createObjAndAddToCol(new JobDetails());
$salesOrderSubscription =  createObjAndAddToCol(new SalesOrderSubscription());
$invoiceOrderSubscription =  createObjAndAddToCol(new InvoiceOrderSubscription());
$installationWorkOrder = createObjAndAddToCol(new InstallationWorkOrder()); 
$ticketSupport = createObjAndAddToCol(new TicketSupport()); 
$ticketSupportWorkOrder = createObjAndAddToCol(new TicketSupportWorkOrder());
$stagesProcess = createObjAndAddToCol(new StagesProcess());
$timeUnit = createObjAndAddToCol(new TimeUnit());
$installationWorkOrder = createObjAndAddToCol(new InstallationWorkOrder());
$jobOpportunities = createObjAndAddToCol(new JobOpportunities());
$managementTeam = createObjAndAddToCol(new ManagementTeam());
$campaign = createObjAndAddToCol(new Campaign());
$projectDumper = createObjAndAddToCol(new ProjectDumper());
$salesOrderDumper = createObjAndAddToCol(new SalesOrderDumper());
$salesOrderDumperInvoice = createObjAndAddToCol(new SalesOrderDumperInvoice());
$salesOrderRentalInvoice = createObjAndAddToCol(new SalesOrderRentalInvoice());
$courseCategory = createObjAndAddToCol(new CourseCategory());
$course = createObjAndAddToCol(new Course());
$quiz = createObjAndAddToCol(new Quiz());

$lang = createObjAndAddToCol(new Lang()); 

// INCLUDE CLASS SESUAI KEBUTUHAN

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemFilter.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/FilterCategory.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/PaymentConfirmation.class.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Article.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ArticleCategory.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Banner.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/News.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/NewsCategory.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Event.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Page.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Contact.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Youtube.class.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Survey.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Gallery.class.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Testimonial.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Portfolio.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/PortfolioCategory.class.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Download.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/DownloadCategory.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Division.class.php';   
$itemFilter =  createObjAndAddToCol( new ItemFilter());
$filterCategory =  createObjAndAddToCol( new FilterCategory());
$paymentConfirmation =  createObjAndAddToCol( new PaymentConfirmation()); 
$article =  createObjAndAddToCol( new Article());
$articleCategory =  createObjAndAddToCol( new ArticleCategory());
$banner =  createObjAndAddToCol( new Banner()); 
$news =  createObjAndAddToCol( new News());
$newsCategory =  createObjAndAddToCol( new NewsCategory());
$event =  createObjAndAddToCol( new Event()); 
$page =  createObjAndAddToCol( new Page()); 
$contact =  createObjAndAddToCol( new Contact()); 
$youtube =  createObjAndAddToCol( new Youtube()); 
$survey =  createObjAndAddToCol( new Survey()); 
$gallery =  createObjAndAddToCol( new Gallery()); 
$testimonial =  createObjAndAddToCol( new Testimonial()); 
$portfolio =  createObjAndAddToCol( new Portfolio()); 
$portfolioCategory =  createObjAndAddToCol( new PortfolioCategory()); 
$download = createObjAndAddToCol(new Download()); 
$downloadCategory = createObjAndAddToCol(new DownloadCategory());  
$division = createObjAndAddToCol(new Division());  


require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/SalesOrder.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/SalesDelivery.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Marketplace.class.php';

require_once DOC_ROOT. 'lazada/LazopSdk.php';

$salesOrder = createObjAndAddToCol(new SalesOrder());  
$salesDelivery = createObjAndAddToCol(new SalesDelivery());  
$lazada = createObjAndAddToCol(new Lazada());
$shopee = createObjAndAddToCol(new Shopee());
$tokopedia = createObjAndAddToCol(new Tokopedia());
$marketplace = createObjAndAddToCol(new Marketplace());


require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Service.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Car.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CarCategory.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CarSeries.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Depot.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Port.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Terminal.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Chassis.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ChassisCategory.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ServiceCategory.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Consignee.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingSellingRate.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingServiceOrder.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingServiceOrderCategory.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingServiceWorkOrder.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingServiceOrderInvoice.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CostRate.class.php';      
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingJob.class.php';      
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Vessel.class.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingCostCashOut.class.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingCostCashIn.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Location.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CarMaintenanceChecklist.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/OilType.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingPurchaseOrder.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CarTurnover.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemInDepot.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemOutDepot.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemDepotMovement.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/EMKLJobOrderHeader.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/EMKLJobOrder.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/EMKLOrderInvoice.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Container.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/EMKLPurchaseOrder.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/EMKLCommission.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CarRevenue.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/VendorWarrantyClaim.class.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/VendorWarrantyClaimReturn.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/APEmployeeCommission.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/APEmployeeCommissionPayment.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/SalesOrderInvoiceReceipt.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ChangeItemSN.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TemplateEMKLPurchaseItem.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CashBank.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Mobile_Detect.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/SalesRentalQuotation.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/SalesOrderRental.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/SalesOrderRentalWorkOrder.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/InstallationBAST.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/InvoiceTax.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Termination.class.php';   
//require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Rig.class.php';   

$truckingService = createObjAndAddToCol(new Service());
$truckingCost = createObjAndAddToCol(new Service(TRUCKING_SERVICE,1));   
$service = createObjAndAddToCol(new Service(SERVICE)); 
$depot = createObjAndAddToCol(new Depot());
$terminal = createObjAndAddToCol(new Terminal());
$car = createObjAndAddToCol(new Car());

$carCategory = createObjAndAddToCol(new CarCategory()); 
$carSeries = createObjAndAddToCol(new CarSeries()); 
$chassis = createObjAndAddToCol(new Chassis()); 
$chassisCategory = createObjAndAddToCol(new ChassisCategory()); 
$serviceCategory = createObjAndAddToCol(new ServiceCategory());
$consignee = createObjAndAddToCol(new Consignee());
$truckingSellingRate = createObjAndAddToCol(new TruckingSellingRate()); 
$truckingServiceOrder = createObjAndAddToCol(new TruckingServiceOrder());
$truckingServiceOrderCategory = createObjAndAddToCol(new TruckingServiceOrderCategory());
$truckingServiceWorkOrder = createObjAndAddToCol(new TruckingServiceWorkOrder());
$truckingServiceOrderInvoice = createObjAndAddToCol(new TruckingServiceOrderInvoice());
$costRate = createObjAndAddToCol(new CostRate());
$truckingJob = createObjAndAddToCol(new TruckingJob());
$vessel = createObjAndAddToCol(new Vessel());
$truckingCostCashOut = createObjAndAddToCol(new TruckingCostCashOut());
$truckingCostCashIn = createObjAndAddToCol(new TruckingCostCashIn());
$location = createObjAndAddToCol(new Location());
$carMaintenanceChecklist = createObjAndAddToCol(new CarMaintenanceChecklist());
$oilType = createObjAndAddToCol(new OilType());
$truckingPurchaseOrder = createObjAndAddToCol(new TruckingPurchaseOrder()); 
$invoiceTax = createObjAndAddToCol(new InvoiceTax()); 
$carTurnover = new CarTurnover() ;

$itemInDepot =  createObjAndAddToCol(new ItemInDepot());
$itemOutDepot =  createObjAndAddToCol(new ItemOutDepot());
$itemDepotMovement = new ItemDepotMovement() ; 

$emklJobOrderHeader = createObjAndAddToCol(new EMKLJobOrderHeader()); 
$emklJobOrderHeaderExport = createObjAndAddToCol(new EMKLJobOrderHeader(EMKL['jobType']['export']));
$emklJobOrderHeaderImport = createObjAndAddToCol(new EMKLJobOrderHeader(EMKL['jobType']['import']));

$emklJobOrder = createObjAndAddToCol(new EMKLJobOrder());
$emklJobOrderExport = createObjAndAddToCol(new EMKLJobOrder(EMKL['jobType']['export']));
$emklJobOrderImport= createObjAndAddToCol(new EMKLJobOrder(EMKL['jobType']['import'])); 

$emklPurchaseOrderExport = createObjAndAddToCol(new EMKLPurchaseOrder(EMKL['jobType']['export']));
$emklPurchaseOrderImport= createObjAndAddToCol(new EMKLPurchaseOrder(EMKL['jobType']['import']));
$container = createObjAndAddToCol(new Container());
$carRevenue = createObjAndAddToCol(new CarRevenue());
$port = createObjAndAddToCol(new Port());
$vendorWarrantyClaim = createObjAndAddToCol(new VendorWarrantyClaim()); 
$emklOrderInvoice = createObjAndAddToCol(new EMKLOrderInvoice());
$vendorWarrantyClaimReturn = createObjAndAddToCol(new VendorWarrantyClaimReturn()); 
$apEmployeeCommission = createObjAndAddToCol(new APEmployeeCommission()); 
$apEmployeeCommissionPayment = createObjAndAddToCol(new APEmployeeCommissionPayment()); 
$emklCommission = createObjAndAddToCol(new EMKLCommission());  
$salesOrderInvoiceReceipt = createObjAndAddToCol(new SalesOrderInvoiceReceipt());   
$changeItemSN = createObjAndAddToCol(new ChangeItemSN());       
$templateEMKLPurchaseItem = createObjAndAddToCol(new TemplateEMKLPurchaseItem()); 
$cashBank = createObjAndAddToCol(new CashBank()); 
$salesOrderRental = createObjAndAddToCol(new SalesOrderRental()); 
$salesRentalQuotation = createObjAndAddToCol(new SalesRentalQuotation()); 
$salesOrderRentalWorkOrder = createObjAndAddToCol(new SalesOrderRentalWorkOrder());
$installationBAST = createObjAndAddToCol(new InstallationBAST()); 
$termination = createObjAndAddToCol(new Termination()); 
//$rig = createObjAndAddToCol(new Rig());

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/SalesOrderCarService.class.php';  
$salesOrderCarService = createObjAndAddToCol(new SalesOrderCarService());   
 
$class->setLog(date('d / m / Y H:i') . ' '.$_SERVER['HTTP_REFERER'] . ' => ' . $_SERVER['PHP_SELF'] ,true,'includelog.txt');

function prepareOnLoadData($obj){ 
    
    $rs = array();
    
    $obj->usingAutoCode = $obj->useAutoCode($obj->tableName);
    
    if (!empty($_GET['id'])){ 
        $id = $_GET['id'];	
        $rs = $obj->getDataRowById($id);

        $obj->rs = $rs; 
        //$obj->updateReadStatus(); 
        
        $_POST['action'] = 'edit';
        $_POST['hidModifiedOn'] = $rs[0]['modifiedon'];  
        $_POST['hidId'] = $rs[0]['pkey'];
        $_POST['code'] = $rs[0]['code'];
        $_POST['selStatus'] = $rs[0]['statuskey']; 
        
        // utk modul2 tertentu dulu
        $obj->loadData($rs);
        
        if (isset($rs[0]['companykey']))
            $_POST['selMainCompany'] = $rs[0]['companykey'];  
        
    }else{ 
        $_POST['action'] = 'add';  
    }

    return $rs;
} 

function prepareOnLoadDataForm($obj,$showMainCompany=true){
    $employee = new Employee();
    
    echo $obj->inputHidden('hidId'); 
    echo $obj->inputHidden('hidModifiedOn');  
    echo $obj->inputHidden('action');  
     
    if (!$showMainCompany || !empty($_GET['id']))
        return;
    
     $rsCompany = $employee->getAccessCompany($obj->userkey);  
     if (count($rsCompany) > 1) {  
         $arrCompany = $obj->convertForCombobox($rsCompany,'companykey','companyname');   

         $selCompany = ' <div class="main-company-options">
                            <div class="div-table">
                                <div class="div-table-row">
                                    <div class="div-table-col-5" style="vertical-align:top; line-height: 2.8em">'.ucwords($obj->lang['company']).'</div> 
                                    <div class="div-table-col-5" style="width:1em; vertical-align:top; line-height: 2.8em; ">:</div>
                                    <div class="div-table-col-5" style="width:20em">'.$obj->inputSelect('selMainCompany',$arrCompany).'</div>
                                </div>
                            </div>
                          </div>
                          <div style="clear:both; border-top:1px solid #dedede; height: 2em"></div>';
         
        echo $selCompany;
     }
 
} 

function createObjAndAddToCol($obj){ 
    $GLOBALS['ObjCol'][$obj->tableName] = $obj;
    return $obj;
}

function generateDataRow($obj,$rs,$arrColumn,$rsStatus,$show = true){
        $datalistrow = '';
        $shadowClass = (!empty($rs['tagkey'])) ? $obj->shadowClass[$rs['tagkey']] : '';  
        
	    $inputStatusStyle = (isset($rsStatus[$rs['statuskey']]['label']) && !empty($rsStatus[$rs['statuskey']]['label'])) ? 'color: ' . $rsStatus[$rs['statuskey']]['label'] : ''; 
		  
        $readClass = (isset($rs['read']) && !$rs['read']) ? 'unread-status' : '';
            
        $showStyle = ($show) ? '' : 'style="display:none"';
    
		$datalistrow .= '<li class="data-record '.$shadowClass.'" relId="'.$rs['pkey'].'" '.$showStyle.'>'; 
		$datalistrow .= '<div class="table-data-record-header" >';  
        $datalistrow .= '<div class="div-table-row "> '; 
        $datalistrow .= '<div class="div-table-col  read-status-col '.$readClass.'"></div>'; 
        $datalistrow .= '<div style="text-align:center; width:2em;" class="div-table-col unselectable"><input type="checkbox" name="chkRow[]"></div>';
       
        for($j=0;$j<count($arrColumn);$j++){ 

            // compability
            if(isset($arrColumn[$j]['code'])){ 
                if(gettype($arrColumn[$j]['format']) == 'object'){  
                    $content =  $arrColumn[$j]['format']($rs,$obj); 
                    $format = '';
                }else{
                    $content = $rs[$arrColumn[$j]['dbfield']];
                    $format = (isset($arrColumn[$j]['format'])) ? strtolower($arrColumn[$j]['format']) : '';
                }
                   
                $width = (isset($arrColumn[$j]['width']) && !empty($arrColumn[$j]['width'])) ? 'width:'.$arrColumn[$j]['width'].'px' : '';
                $textAlign = (isset($arrColumn[$j]['align'])) ? 'text-align:'.$arrColumn[$j]['align'].';' : '';
            }else{ 
                $content = $rs[$arrColumn[$j][1]];
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
                
             // gk boleh replace EOL, jd double
             $content = str_replace(array(chr(13)),array('<br>'),$content);
                 
             $datalistrow .= ' <div style="'.$textAlign.' '. $width.'" class="div-table-col"><span class="unselectable" style="'.$inputStatusStyle.'">'. $content .'</span></div> ';
         } 
         
        $rowIcon = (isset($rs['systemVariable']) && $rs['systemVariable'] == 1) ? '<i class="fas fa-lock"></i>' : ''; 
        $rowIcon = (isset($rs['islinked']) && $rs['islinked'] == 1) ? '<i class="fas fa-link"></i>' : $rowIcon; 

        $datalistrow .= '<div style="text-align:center; width: 30px;" class="div-table-col-5 tag">'.$rowIcon.'</div>';

        $datalistrow .= '</div>';
		$datalistrow .= '</div> ';   
		$datalistrow .= '<div class="table-data-record-detail'.$rs['pkey'].' table-data-record-detail" ></div>';  
		$datalistrow .=  '</li> '; 
    
        return $datalistrow;
}


function getMemoryLog(){
   /* Currently used memory */
   $mem_usage = memory_get_usage();
   
   /* Peak memory usage */
   $mem_peak = memory_get_peak_usage();

   $var = 'The script is now using: ' . number_format($mem_usage / 1024) . ' KB of memory'.chr(13);
   $var .= 'Peak usage: ' . number_format($mem_peak / 1024) . ' KB of memory.'.chr(13);
    
  return $var;
}
 
function getPerformanceLog($start_time){ 
    $end_time = microtime(TRUE); 
    $time_taken = $end_time - $start_time; 
    $time_taken = round($time_taken,5); 
    
    $var = 'Page generated in '.$time_taken.' seconds.';
    $var .= chr(13).getMemoryLog();
    
    return $var;
}



/*
$end_time = microtime(TRUE); 
$time_taken = $end_time - $start_time; 
$time_taken = round($time_taken,5); 
$class->setLog('Page generated in '.$time_taken.' seconds.',true);
$class->setLog(print_mem(),true);
 */
// DEFINE
define('USE_SN',false);
?>