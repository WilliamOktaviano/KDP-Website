<?php session_start();      
    
	date_default_timezone_set('Asia/Jakarta');

	ini_set("zlib.output_compression", "On");
	ini_set('display_errors', 0);
	ini_set('log_errors', 1);
      

    define('CLASS_VERSION', 'class-2.11');
	define('ADMIN_CSS_VERSION', 'adminStyle-3.55.min.css');

	$WEB_FOLDER = '';
    $IS_HISTORY = false;

    $PROTOCOL = (!empty($_SERVER['HTTPS'])) ? 'https' : 'http'; 
    define ('PROTOCOL',$PROTOCOL);
    
	$HTTP_HOST =  PROTOCOL . '://' .$_SERVER ['HTTP_HOST'] ;
	if(substr($HTTP_HOST,-1) <> "/") {
		$HTTP_HOST  .= '/';
	}
	$HTTP_HOST = $HTTP_HOST.$WEB_FOLDER; 
    define ('HTTP_HOST',$HTTP_HOST);
    define ('REQUEST_URI',$_SERVER['REQUEST_URI']);
 
	$DOC_ROOT = $_SERVER ['DOCUMENT_ROOT'] ;
	if(substr($DOC_ROOT,-1) <> "/") {
		$DOC_ROOT .= '/';
	} 
	
	$DOC_ROOT = $DOC_ROOT.$WEB_FOLDER;  
    define('DOC_ROOT',$DOC_ROOT);
   
    $patterns = array('www.',':');
    $replacements = array('','-');
    $DOMAIN_NAME = str_replace($patterns, $replacements, $_SERVER['HTTP_HOST']); 
    

    if(file_exists(DOC_ROOT.'_domain.php')) require_once '_domain.php';  

    // FOR DEVELOPMENT 
    $IS_DEVELOPMENT = false;
    if(file_exists(DOC_ROOT.'_development.php'))
        include '_development.php';  
  
    //echo $_SERVER ['HTTP_HOST'];
     
    // DEFINE  
    define('IS_DEVELOPMENT',$IS_DEVELOPMENT);
    define('DOMAIN_NAME',$DOMAIN_NAME); 
    //define('DOMAIN_COOKIES','_mnv'); // gk boleh ad titik, jd gk bisa pake nama domain
    define('PERSONALIZED_DOC_PATH',DOC_ROOT.'personalized/'.DOMAIN_NAME.'/');
    define('PERSONALIZED_URL_PATH','/personalized/'.DOMAIN_NAME.'/');
    define('DEBUG',false); 
  
    $apiVersion = 'v2/';
    if(in_array(DOMAIN_NAME,array('sinicobain.com'))) 
        $apiVersion = 'v3/';
  
    define('API_URL', (IS_DEVELOPMENT) ? 'https://minerva.local/api/'.$apiVersion : HTTP_HOST.'api/'.$apiVersion );
 
    define('DOMAIN_FOLDER',strtolower(DOMAIN_NAME).'/');
    define('UPLOAD_TEMP_DOC', DOC_ROOT. '../_temp/' .DOMAIN_FOLDER);
    define('UPLOAD_TEMP_DOC_SHORT',  '/../_temp/' .DOMAIN_FOLDER); // untuk phpThumb, agar tidak terlihat path full.  
    define('UPLOAD_TEMP_URL', HTTP_HOST. '../_temp/' .DOMAIN_FOLDER); 
    define('DEFAULT_DOC_UPLOAD_PATH', DOC_ROOT. '../_upload/' .DOMAIN_FOLDER);
    define('DEFAULT_URL_UPLOAD_PATH', HTTP_HOST. '../_upload/' .DOMAIN_FOLDER); // harusnya gk bisa diakses karena naik 1 tingkat
    define('PHPTHUMB_URL_PATH', '/../_upload/' .DOMAIN_FOLDER);

    define('USER_SYSTEM',array('code' => '00000', 'name' => 'SYSTEM'));      

    // INI TIDAK/BUKAN MENGGAMBARKAN HAK AKSES STATUS, karena tidak ad akses update
    define('INSERT_DATA', 11);
    define('UPDATE_DATA', 12);
    define('DELETE_DATA', 13); 
    define('PRINT_DATA', 14);
    define('CHANGE_STATUS_DATA', 1);
    define('DEFAULT_EMPTY_DATE', '01 / 01 / 1970');
    define('DECIMAL_SEPARATOR', '.'); 
 

    $arrTransactionStatus = array();     
    $arrTransactionStatus['menunggu'] = 1; 
    $arrTransactionStatus['konfirmasi'] = 2; 
    $arrTransactionStatus['selesai'] = 3; 
    $arrTransactionStatus['batal'] = 4; 
    define('TRANSACTION_STATUS',$arrTransactionStatus);   
  
    $apStatus = array();     
    $apStatus['open'] = 1; 
    $apStatus['partial'] = 2; 
    $apStatus['lunas'] = 3; 
    $apStatus['batal'] = 4; 
    define('AP_STATUS',$apStatus);   

    $financialReport = array();
    $financialReport['balanceSheet'] = 1;
    $financialReport['incomeStatement'] = 2; 
    define('FINANCIAL_REPORT',$financialReport);   
 
    $voucherCategory = array();
    $voucherCategory['registration'] = 1;
    $voucherCategory['sales'] = 2; 
    $voucherCategory['shipment'] = 3; 
    define('VOUCHER_CATEGORY',$voucherCategory);   
 
    $voucherType = array();
    $voucherType['regular'] = 1;
    $voucherType['collectible'] = 2; 
    define('VOUCHER_TYPE',$voucherType);   


    $customerType = array();
    $customerType['reseller'] = 1;
    $customerType['enduser'] = 2; 
    define('CUSTOMER_TYPE',$customerType);   

    // ITEM TYPE
    define('ITEM',1); 
    define('TRUCKING_SERVICE',2); 
    define('SERVICE',3); // service dan paket barang ??
    define('ITEM_DEPOT',4);

    define('COMPANY_TYPE', array('retail' => 1, 'trucking' => 2)); 
    
    // jenis2 piutang
    define('AP_TYPE', array('itemPurchase' => 1, 
                            'serviceOutsource' => 2, // bisa utk mobil luar atau bagi hasil ?
                            'driverCommission' => 3, // ritase
                            'salesCommission' => 4, 
                            'carServiceMaintenance' => 5, // DN utk pengurangan bagi hasil, 
                            'otherCost' => 6 // DN utk pengurangan bagi hasil
                            ));
    
    // jenis2 hutang
    define('AR_TYPE', array('salesOrder' => 1, 'serviceOrder' => 2, 'creditNote' => 3)); 
    define('AR_EMPLOYEE_TYPE', array('personalLoan' => 1, 'cashBankRealization' => 2)); 
    
    define('INVOICE_TYPE', array('sales' => 1, 'reimbursement' => 2)); 
    define('SYSTEM_UNIT', array('qty' => 1, 'weight' => 2,'length' => 3)); 
     
    $arrSupplier = array();     
    $arrSupplier[1] = 'Trucking'; 
    $arrSupplier[2] = 'Ocean Freight'; 
    $arrSupplier[3] = 'Others'; 
    define('EMKLSUPPLIERCATEGORY',$arrSupplier);


    $arrInputType = array();
    $arrInputType['text'] = 1; 
    $arrInputType['number'] = 2; 
    $arrInputType['textarea'] = 3; 
    $arrInputType['raw'] = 4; 
    $arrInputType['editor'] = 5; 
    $arrInputType['file'] = 6; 
    $arrInputType['select'] = 7; 
    $arrInputType['autocompletejs'] = 8; 
    define('INPUT_TYPE',$arrInputType);   

    // SUPPLIER TYPE
/*    $arrSupplierType = array();
    $arrSupplierType['supplier'] = 1;
    $arrSupplierType['carrier'] = 2;
    $arrSupplierType['agent'] = 3;
    define('SUPPLIER_TYPE',$arrSupplierType);   */

 
    define('VEHICLE_PARTNERSHIP_TYPE', array('oncall' => 1, 'contract' => 2));
 
    define('TIME_STAMP_TYPE', array('trdate' => 1, 'confirmeddate' => 2));


    $arrEMKL = array();
    $arrEMKL['jobType']['import'] = 1;
    $arrEMKL['jobType']['export'] = 2; 
    $arrEMKL['volume']['cbm'] = 1;
    $arrEMKL['volume']['kg'] = 2; 
    $arrEMKL['container']['fcl'] = 1;
    $arrEMKL['container']['lcl'] = 2;
    $arrEMKL['container']['trucking'] = 3;
    $arrEMKL['container']['document'] = 4;
    $arrEMKL['shipping']['sea'] = 1;
    $arrEMKL['shipping']['air'] = 2;
    $arrEMKL['emklType']['fcl'] = 1;
    $arrEMKL['emklType']['lcl'] = 2;
    $arrEMKL['emklType']['trucking'] = 3;
    $arrEMKL['emklType']['document'] = 4;

    define('EMKL',$arrEMKL);   

    define('CURRENCY',array('idr' => 1));   
    define('CLAIM_TYPE',array('repair' => 1, 'replace' => 2, 'upgrade' => 3, 'CN' => 4, 'void' => 5));   
    define('UNIT',array('kg' => 2, 'gram' => 3));
      
    define('CURRENCY_PREFERENCE',array('auto' => 1, 'idr' => 2));   

    $arrAPIAction = array();
    $arrAPIAction['update'] = 'update';
    $arrAPIAction['get'] = 'get';
    $arrAPIAction['delete'] = 'delete';
    define('API_ACTION',$arrAPIAction);   
 
    $importTemplate = array();
    $importTemplate['item'] = 'Template Import - Barang';
    $importTemplate['itemIn'] = 'Template Import - Pemasukaan Barang';
    $importTemplate['itemOut'] = 'Template Import - Pengeluaran Barang';
    $importTemplate['customer'] = 'Template Import - Pelanggan';
    $importTemplate['EMKLPurchaseOrderExport'] = 'Template Import - Purchase Order Export';
    $importTemplate['EMKLPurchaseOrderImport'] = 'Template Import - Purchase Order Import';
    $importTemplate['EMKLJobOrderExport'] = 'Template Import - Job Order Export';
    $importTemplate['salesOrderSubscription'] = 'Template Import - Sales Order Subscription';
    $importTemplate['cashBankIn'] = 'Template Import - Kas Bank Masuk';
    $importTemplate['cashOut'] = 'Template Import - Kas Keluar';
    
    $importTemplate['city'] = 'Template Import - Kota';
    $importTemplate['cityCategory'] = 'Template Import - Kategori Kota';

    $importTemplate['service'] = 'Template Import - Layanan';
    $importTemplate['supplier'] = 'Template Import - Pemasok';  
    $importTemplate['consignee'] = 'Template Import - Consignee';  
    $importTemplate['ap'] = 'Template Import - Hutang (AP)';
    $importTemplate['ar'] = 'Template Import - Piutang (AR)';
    $importTemplate['employee'] = 'Template Import - Karyawan'; // problem kalo gk pake class baru
    $importTemplate['location'] = 'Template Import - Lokasi'; 
    $importTemplate['car'] = 'Template Import - Mobil';

    define('IMPORT_TEMPLATE',$importTemplate);
 
    $defaultCost = array();
    $defaultCost['outsourceDownpayment'] = '1';
    define('DEFAULT_COST',$defaultCost);   

    $arrCOA = array();
    $arrCOA['type']['assets'] = 1;
    $arrCOA['type']['liability'] = 2;
    $arrCOA['type']['equity'] = 3;
    $arrCOA['type']['revenue'] = 4;
    $arrCOA['type']['cogs'] = 5;
    $arrCOA['type']['expense'] = 6; 
    define('COA',$arrCOA);   
    

    $arrMP = array();     
    $arrMP['lazada'] = 1; 
    $arrMP['shopee'] = 2; 
    $arrMP['tokopedia'] = 3; 
    define('MARKETPLACE',$arrMP);   

    $cronRepeat = array();
    $cronRepeat['daily'] = '1';
    $cronRepeat['monthly'] = '2';
    $cronRepeat['annually'] = '3';
    define('CRON_REPEAT',$cronRepeat);   

    $nettingPayment = array();
    array_push($nettingPayment,array( 'pkey'=> '-1', 'name' => 'Netting')); 
    define('NETTING_PAYMENT', $nettingPayment);


    $cashAdv = array();
    array_push($cashAdv,array( 'pkey'=> '-1', 'name' => 'CASH ADV.')); 
    define('CASH_ADVANCE', $cashAdv);


	$path = DOC_ROOT.'log/'; 
	if (!file_exists($path))  mkdir($path, 0755, true);   
	ini_set('error_log', $path.'['.date('d-m-Y') .'] - '.md5(DOC_ROOT).'.txt' ); 


    // REDIRECT ON CUSTOM .....
    function getPersonalizedFiles($fileName,$ext=''){     
        global $class;
        
        $ext = (empty($ext)) ? 'php' : ''; 
        $ext = (!empty($ext)) ? '.'.$ext : '';
        
        $path = 'admin/'.$fileName.$ext;
        $docPersonalizedFile = PERSONALIZED_DOC_PATH.$path;   
        $urlPersonalizedFile = PERSONALIZED_URL_PATH.$path;
        
        return (is_file($docPersonalizedFile)) ? $urlPersonalizedFile : $fileName; 
    }
 
    $testVoucher = (DOMAIN_NAME == 'logol.wintera.co.id' || DOMAIN_NAME == 'logoldemo.wintera.co.id' ) ? true : false; 
    define('TEST_VOUCHER',$testVoucher);  

	//define ('TEST_IP',array('180.252.120.28'));

?>