<?php
  
class VoucherTradePoint extends BaseClass{ 
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'voucher_trade_point_header';
		$this->tableNameDetail = 'voucher_trade_point_detail'; 
		$this->tableCustomer = 'customer';
		$this->tableWarehouse = 'warehouse';
		$this->tableVoucher = 'voucher';
		$this->tableStatus = 'transaction_status';
     
        $this->isTransaction = true; 
		  
		$this->tableNeedToBeCopyOnCancel = array($this->tableNameDetail);
       
		$this->securityObject = 'ItemIn';  
           
        $this->arrDataDetail = array();  
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['voucherkey'] = array('hidVoucherKey'); 
        $this->arrDataDetail['qty'] = array('qty','number'); 
        $this->arrDataDetail['pointneeded'] = array('pointneeded','number'); 
       
        $this->arrData = array();  
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => array('dataset' => $this->arrDataDetail))); 
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date'); 
        $this->arrData['warehousekey'] = array('selWarehouseKey');
	    $this->arrData['customerkey'] = array('hidCustomerKey'); 
        $this->arrData['trdesc'] = array('trDesc');  
        $this->arrData['statuskey'] = array('selStatus');
         
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
   
        $this->includeClassDependencies(array( 
              'Customer.class.php',
              'Voucher.class.php',
              'RewardsPoint.class.php',
              'VoucherTransaction.class.php' 
        ));
       
        $this->overwriteConfig();
       
   }
   
   function getQuery(){
	   
	   $sql = '
			SELECT '.$this->tableName.'.* ,
			   '.$this->tableWarehouse.'.code as warehousecode,
			   '.$this->tableWarehouse.'.name as warehousename,
			   '.$this->tableStatus.'.status as statusname,
			   '.$this->tableCustomer.'.name as customername
			FROM '.$this->tableStatus.', 
                 '.$this->tableName.' left join '.$this->tableCustomer.' on
                   '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey 
            , '.$this->tableWarehouse.'  
			WHERE '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey
 	  ' .$this->criteria ; 
		  
        return $sql;
    }
  	  
    function validateForm($arr,$pkey = ''){
      
		// NANTI PERLU TAMBAHIN JG DI KONFIRMASI
		 
		$arrayToJs = parent::validateForm($arr,$pkey); 

		$customer = new Customer();
		$voucher = new Voucher();
		 
        $customerkey = $arr['hidCustomerKey'];   
		$arrVoucherKey =  $arr['hidVoucherKey'];  
		$arrQty =  $arr['qty'];  

	    if(empty($customerkey)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['customer'][1]);
		}

		$rsCustomer = $customer->searchDataRow(array($customer->tableName.'.point'),' and '.$customer->tableName.'.pkey = ' . $customer->oDbCon->paramString($customerkey));
		$eligiblePoint = (!empty($rsCustomer)) ? $rsCustomer[0]['point'] : 0;
		
		$rsVoucher = $voucher->searchDataRow(array($voucher->tableName.'.pkey',$voucher->tableName.'.name',$voucher->tableName.'.pointneeded'),
									 ' and '.$voucher->tableName.'.statuskey in (2)'
									);
		$rsVoucher = array_column($rsVoucher,null,'pkey');
		 
		$totalPointNeeded = 0;
		for($i=0;$i<count($arrVoucherKey);$i++)  
           $totalPointNeeded += ($arrQty[$i] * $rsVoucher[$arrVoucherKey[$i]]['pointneeded']);
		
		if($totalPointNeeded > $eligiblePoint)  
			$this->addErrorList($arrayToJs,false,$this->errorMsg['point'][3]);
		
		return $arrayToJs;
	 }
	  
    function validateCancel($rsHeader,$autoChangeStatus=false){  
		$this->addErrorList($arrayToJs,false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201]);
		return $arrayToJs;
	}
    
  
	function confirmTrans($rsHeader){
    	
		$id = $rsHeader[0]['pkey'];
		
		$voucherTransaction = new VoucherTransaction();
		$voucher = new Voucher();
		$rewardsPoint = new RewardsPoint();
		
		$rsDetail = $this->getDetailById($id);
			
		$rsVoucher = $voucher->searchDataRow(array($voucher->tableName.'.pkey',$voucher->tableName.'.discounttype',$voucher->tableName.'.value',$voucher->tableName.'.minamount',$voucher->tableName.'.maxdiscount',$voucher->tableName.'.pointneeded'),
									 ' and '.$voucher->tableName.'.statuskey in (2)'
									);
		$rsVoucher = array_column($rsVoucher,null,'pkey');
		
		$rewardsExpiredIn = $this->loadSetting('rewardsExpiredIn'); 
		$refTableType = $this->getTableKeyAndObj($this->tableName,array('key'))['key']; 
			
		// update expired poin
		$rewardsPoint->updateExpPoint($rsHeader[0]['customerkey']);
			
		// potong poin  
		$totalPointNeeded = 0;
		foreach($rsDetail as $row){
			$totalPointNeeded += ($row['qty'] * $rsVoucher[ $row['voucherkey'] ]['pointneeded']);
		}
		 
		$rewardsPoint->deductPoint($rsHeader[0]['customerkey'], $totalPointNeeded, array('pkey' => $id, 'refTableType' => $refTableType));
		
		// add voucher transaksi
		$rsDetail = $this->getDetailById($id);
		 
		foreach($rsDetail as $row){
			
			for($i=0;$i<$row['qty'];$i++){ 
				$arrData = array(); 
				$arrData['code'] = 'xxxxx'; 
				$arrData['trDate'] = $this->formatDBDate($rsHeader[0]['trdate']); 
				$arrData['expDate'] = $this->formatDBDate($rsHeader[0]['trdate']); 
				$arrData['selWarehouse'] = $rsHeader[0]['warehousekey']; 
				$arrData['expDate'] = date('d / m / Y', strtotime($rsHeader[0]['trdate']. ' + '.$rewardsExpiredIn.' months'));
				$arrData['hidRefKey'] = $id; 
				$arrData['refTableType'] = $refTableType; 
				$arrData['hidVoucherKey'] = $row['voucherkey']; 
				$arrData['hidCustomerKey'] = $rsHeader[0]['customerkey']; 
				$arrData['refCode'] = $rsHeader[0]['code']; 
				$arrData['value'] = $rsVoucher[$row['voucherkey']]['value']; 
				$arrData['selDiscountType'] = $rsVoucher[$row['voucherkey']]['discounttype']; 
				$arrData['minAmount'] = $rsVoucher[$row['voucherkey']]['minamount']; 
				$arrData['maxDiscount'] = $rsVoucher[$row['voucherkey']]['maxdiscount'];
				$arrData['selStatus'] = 1; 
				$arrData['_mnv-from-point'] = 1;

				$response = $voucherTransaction->addData($arrData); 
				$voucherTransaction->changeStatus($response[0]['data']['pkey'], 2, '',false,true);
			}

		} 
		
		
	} 
     
	function cancelTrans($rsHeader,$copy){
		
	} 
    
	
    function getDetailForAPI($arrKey, $arrIndex = array()){ 
		$rsDetailsCol = array();
		
        if(in_array('detail', $arrIndex)){   
			//$rsDetailsCol = array();  // ini kalo ad didalam jadinya error kalo ad 2 detail atau lebih, karena kereset lg yg sebelumnya
            $rsDetails = $this->getDetailWithRelatedInformation($arrKey); 
            $rsDetails = $this->reindexDetailCollections($rsDetails,'refkey'); 
            $rsDetailsCol['detail'] = $rsDetails;
        }
         
        return $rsDetailsCol;
    }
	
	function getDetailWithRelatedInformation($pkey,$criteria='',$orderby =''){


        $sql = 'select
                '.$this->tableNameDetail .'.* ,
				'.$this->tableVoucher .'.code as vouchercode,
				'.$this->tableVoucher .'.name as vouchername
              from
                '.$this->tableNameDetail .',
				'.$this->tableVoucher .'
			  where 
			    '.$this->tableNameDetail .'.voucherkey = '.$this->tableVoucher .'.pkey and
			    '.$this->tableNameDetail .'.refkey in ('.$this->oDbCon->paramString($pkey,',') . ') ';

        $sql .= $criteria;

        $sql .= ' ' .$orderby; 
		
        return $this->oDbCon->doQuery($sql);

    } 

	
    function normalizeParameter($arrParam, $trim = false){
			
		$arrVoucherKey = $arrParam['hidVoucherKey'];
		
		$voucher = new Voucher();
		$rsVoucher = $voucher->searchDataRow(array($voucher->tableName.'.pkey',$voucher->tableName.'.name',$voucher->tableName.'.pointneeded'),
									 ' and '.$voucher->tableName.'.pkey in (' . $this->oDbCon->paramString($arrVoucherKey,',').')'
									);
		$rsVoucher = array_column($rsVoucher,null,'pkey');
		
		for($i=0;$i<count($arrVoucherKey);$i++) 
			$arrParam['pointneeded'][$i] = $rsVoucher[$arrVoucherKey[$i]]['pointneeded']; 
	  
        $arrParam = parent::normalizeParameter($arrParam, true);  
        return $arrParam;
    }
     
}
?>