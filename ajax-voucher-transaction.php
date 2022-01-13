<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';

includeClass(array( 
				   'VoucherTransaction.class.php'
                  ));

$voucherTransaction = new VoucherTransaction(); 

$obj = $voucherTransaction;    

include 'ajax-general.php';

 switch ( $_POST['action']){ 
	case 'calculateVoucherValue' : 
	    
		if(empty($_POST['voucherkey']) || empty($_POST['totalsales'])) die; 
        echo $voucherTransaction->calculateVoucherValue($_POST['voucherkey'],$_POST['totalsales']);
        break; 
          
 }

die;
  
?>