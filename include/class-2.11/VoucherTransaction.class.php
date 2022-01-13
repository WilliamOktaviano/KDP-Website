<?php

class VoucherTransaction extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'voucher_transaction'; 
		$this->tableVoucher = 'voucher'; 
		$this->tableStatus = 'transaction_status';  
		$this->tableWarehouse = 'warehouse'; 
		$this->tableCustomer = 'customer'; 
        $this->tableCustomerMembership = 'customer_membership';
		$this->securityObject = 'VoucherTransaction';
	    $this->isTransaction = true; 
		
        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey'); 
        $this->arrData['code'] = array('code'); 
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['expdate'] = array('expDate','date');
        $this->arrData['warehousekey'] = array('selWarehouse');
        $this->arrData['refkey'] = array('hidRefKey');
        $this->arrData['reftabletype'] = array('refTableType');
        $this->arrData['refvoucherkey'] = array('hidVoucherKey');
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['refcustomerkey'] = array('hidRefCustomerKey'); 
        $this->arrData['refcode'] = array('refCode'); 
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus'); 
        $this->arrData['discounttype'] = array('selDiscountType'); 
        $this->arrData['minamount'] = array('minAmount','number');
        $this->arrData['maxdiscount'] = array('maxDiscount','number');
        $this->arrData['value'] = array('value','number'); 
       
        $this->arrDataListAvailableColumn = array(); 
       
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'trdate','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 80, 'align'=>'center', 'format' => 'date'));    
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'voucherlabel','default'=>true, 'width' => 150));    
        array_push($this->arrDataListAvailableColumn, array('code' => 'expdate','title' => 'expiredOn','dbfield' => 'expdate','default'=>true, 'width' => 100, 'align'=>'center', 'format' => 'date'));    
        array_push($this->arrDataListAvailableColumn, array('code' => 'useddate','title' => 'usedOn','dbfield' => 'useddate','default'=>true, 'width' => 100, 'align'=>'center', 'format' => 'date'));    
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername','default'=>true, 'width' => 160));    
        array_push($this->arrDataListAvailableColumn, array('code' => 'refcode','title' => 'reference','dbfield' => 'refcode',  'width' => 150));    
        //array_push($this->arrDataListAvailableColumn, array('code' => 'value','title' => 'value','dbfield' => 'value','default'=>true, 'width' => 100, 'align' => 'right', 'format'=>'number'));   
        array_push($this->arrDataListAvailableColumn, array('code' => 'note','title' => 'note','dbfield' => 'trdesc', 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        
        $this->printMenu = array();  
        array_push($this->printMenu,array('code' => 'printVoucher', 'name' => $this->lang['print'] . ' ' .$this->lang['voucher'],  'icon' => 'print', 'url' => 'print/voucher'));
                    
	   $this->includeClassDependencies(array(
            'Voucher.class.php' 
        ));   

        $this->overwriteConfig();
               
   }
    
	function getQuery(){
	   
	   $sql = '
			select
					'.$this->tableName. '.*,  
                    '.$this->tableWarehouse.'.name as warehousename,
                    '.$this->tableCustomer.'.code as customercode,
                    '.$this->tableCustomer.'.name as customername,
                    '.$this->tableStatus.'.status as statusname,
					'.$this->tableVoucher.'.pkey as voucherkey,
					'.$this->tableVoucher.'.name as voucherlabel,
					'.$this->tableVoucher.'.shortdesc as vouchershortdesc,
					'.$this->tableVoucher.'.trdesc as voucherdesc
				from 
					'.$this->tableName.', 
                    '.$this->tableWarehouse.',
                    '.$this->tableCustomer.',
					'.$this->tableStatus.',
					'.$this->tableVoucher.'
				where  		 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey and 
					'.$this->tableName . '.customerkey = '.$this->tableCustomer.'.pkey and
					'.$this->tableName . '.refvoucherkey = '.$this->tableVoucher.'.pkey and
					'.$this->tableName . '.warehousekey = '.$this->tableWarehouse.'.pkey 
        
        ' .$this->criteria ;
        
        $sql .=  $this->getWarehouseCriteria() ;
        
        return $sql;
    }
    
	function validateForm($arr,$pkey = ''){ 
                 
        $arrayToJs = parent::validateForm($arr,$pkey);
    
        $voucherkey = $arr['hidVoucherKey'];
        $voucher = new Voucher();
        $rsVoucher = $voucher->getDataRowById($voucherkey);

        if(empty($voucherkey))
           $this->addErrorList($arrayToJs,false,$this->errorMsg['voucher'][1]); 
        
        // kalo status sudah selesai
        if($rsVoucher[0]['statuskey'] <> 2)
            $this->addErrorLog(false,'<strong>'.$rsVoucher[0]['code'].'</strong>. '.$this->errorMsg['voucher'][3]);
 
		// kalo qty sudah melebihi
		$maxqty =  $rsVoucher[0]['qty'];
		$qtyused = $rsVoucher[0]['qtyused'];
		if($maxqty > 0){
			if($qtyused > $maxqty) 
				$this->addErrorLog(false,  $this->errorMsg['voucher'][4]); 
		}   

		// jika tgl diisi
		if(!empty($rsVoucher[0]['startdate']) && !empty($rsVoucher[0]['enddate'])){
			$date1 = str_replace('\'','',$this->oDbCon->paramDate($arr['trDate'],' / ','Y-m-d'));
			$trDate = strtotime($date1);
			$stardDate = strtotime($rsVoucher[0]['startdate']);
			$endDate = strtotime($rsVoucher[0]['enddate']);

			if($trDate > $endDate || $trDate < $stardDate)
				$this->addErrorList($arrayToJs,false, $this->errorMsg['voucher'][3]);
		}
	
		return $arrayToJs;
	 }
    
    
    function afterStatusChanged($rsHeader){ 
        $voucher = new Voucher();
        $voucher->updateQtyUsed($rsHeader[0]['refvoucherkey']); 
    }
    
    function validateConfirm($rsHeader){
        
        $voucher = new Voucher(); 
        $rsVoucher = $voucher->getDataRowById($rsHeader[0]['refvoucherkey']);
        
        // kalo status sudah selesai
    	if($rsVoucher[0]['statuskey'] <> 2)
           $this->addErrorLog(false,'<strong>'.$rsVoucher[0]['code'].'</strong>. '.$this->errorMsg['voucher'][3]);
    
        
        // kalo qty sudah melebihi
        $maxqty =  $rsVoucher[0]['qty'];
        $qtyused = $rsVoucher[0]['qtyused'];
        if($maxqty > 0){
            if($qtyused > $maxqty) 
                $this->addErrorLog(false,  $this->errorMsg['voucher'][4]); 
        }        
		
		if(!empty($rsVoucher[0]['startdate']) && !empty($rsVoucher[0]['enddate'])){
			// kalo tgl expired sdh melebih batas waktu
			$trDate = strtotime($rsHeader[0]['trdate']);
			$startDate = strtotime($rsVoucher[0]['startdate']);
			$endDate = strtotime($rsVoucher[0]['enddate']);
			if($trDate > $endDate || $trDate < $startDate)
				$this->addErrorLog(false,  $this->errorMsg['voucher'][3]);
		} 
        
    }
    
    function confirmTrans($rsHeader){
        
        //$voucher = new Voucher();     
        
    }
    
    function validateCancel($rsHeader,$autoChangeStatus=false){
        $id = $rsHeader[0]['pkey'];
    } 
    
    
    function normalizeParameter($arrParam, $trim = false){  
        $arrParam = parent::normalizeParameter($arrParam,true); 

        
        return $arrParam;
        
    }
    
    function getAvailableVoucher($customerkey){
         
        $sql = 'select
                    '.$this->tableName.'.*,
                    '.$this->tableVoucher.'.name as voucherlabel,
                    '.$this->tableVoucher.'.shortdesc ,
                    '.$this->tableVoucher.'.trdesc 
                from
                    '.$this->tableName.', 
                    '.$this->tableVoucher.' 
                where 
                    '.$this->tableName.'.customerkey = '.$this->oDbCon->paramString($customerkey).' and 
                    '.$this->tableName.'.refvoucherkey = '.$this->tableVoucher.'.pkey and 
                    '.$this->tableName.'.statuskey = 2' 
            ;
        
        return $this->oDbCon->doQuery($sql);
         
        
    }
    
    
 function updateVoucherAvailability($voucherkey){
        
        $arrTable = array();
        array_push($arrTable, $this->tableCustomerMembership);
        //array_push($arrTable, $this->tableSalesOrderHeader);
         
        
		try{ 
            
		 	if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]); 
			 
			
            foreach($arrTable as $table){

                    $sql = 'select * from '.$table.'  where '.$table.'.voucherkey = '.$this->oDbCon->paramString($voucherkey).' and '.$table.'.statuskey in (1,2,3)';
                    $rs = $this->oDbCon->doQuery($sql);
                    $rs[0]['tablename'] = $table;
                    $rsTableKey = $this->getTableKeyAndObj($rs[0]['tablename']); 

                
                    if(empty($rs) || empty($rs[0]['pkey'])){
                        //not used
                        $reftranskey = 0;   
                        $reftranstablekey = 0;
                        $statuskey = 2;
                        $useddate = DEFAULT_EMPTY_DATE;
                    }else{
                        //used
                        $reftranskey = $rs[0]['pkey'];
                        $reftranstablekey = $rsTableKey['key'];
                        $statuskey = 3;
                        $useddate = date('d / m / Y');  
                    }
                 
                    $sql = 'update 
                                    '.$this->tableName.' 
                            set 
                                statuskey = '.$statuskey.',
                                reftranskey = '.$reftranskey.',
                                reftranstablekey = '.$reftranstablekey.',
                                useddate = '.$this->oDbCon->paramDate($useddate, ' / ').' 
                            
                            where '.$this->tableName.'.statuskey in (2,3) and '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($voucherkey);
                    //$this->setLog($rs,true);
                    $this->oDbCon->execute($sql); 

            }
            
		    $this->oDbCon->endTrans();
					  
					 
		} catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false, $e->getMessage());   
		}
        

        
    }
	
	function generateDefaultQueryForAutoComplete($returnField){ 
        $sql = 'select
                '.$returnField['key'].',
                '.$returnField['value'].' as value,
                '.$this->tableName . '.code,
                '.$this->tableName . '.value as vouchervalue,
                '.$this->tableName . '.discounttype 
            from 
                '.$this->tableName.', 
                '.$this->tableStatus.'  
            where  		 
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey  
        ';
        
        $sql .=  $this->getCompanyCriteria() ;
        return $sql; 
    }
        
           
     function useVoucher($arrVoucherKey, $refkey,$reftabletype){
		 
		try{ 
            
		 	if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]); 
			 
			 // update semua voucher yg berhubungan dengan refkey ini
			 $sql = 'update  
			 			'.$this->tableName.' 
					set reftranstablekey = 0, statuskey = 2, reftranskey = 0,useddate = \'0000-00-00\' 
					where  
						'.$this->tableName.'.reftranskey = '.$this->oDbCon->paramString($refkey). ' 
						and '.$this->tableName.'.reftranstablekey = '.$this->oDbCon->paramString($reftabletype);
			$this->oDbCon->execute($sql); 
			
			// back confirmed, status langsung proses saja diatas
//			foreach($arrVoucherKey as $voucherkey)
//            	$this->changeStatus($voucherkey ,2);
 
			$sql = 'update 
                        '.$this->tableName.' 
                    set 
                        reftranstablekey = '.$this->oDbCon->paramString($reftabletype).',
                        reftranskey = '.$this->oDbCon->paramString($refkey).',
                        useddate = now() ,
						statuskey = 3
                        where '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($arrVoucherKey,',');
             
            $this->oDbCon->execute($sql); 

			// status langsung proses saja diatas			
//			foreach($arrVoucherKey as $voucherkey)
//            	$this->changeStatus($voucherkey ,3);
            
		    $this->oDbCon->endTrans();
					  
					 
		} catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false, $e->getMessage());   
		}
        
    }
	
	function calculateVoucherValue($voucherkey,$totalSales){
		
		$totalSales = $this->unFormatNumber($totalSales);
		
		$rsVoucher = $this->searchDataRow(array($this->tableName.'.pkey', $this->tableName.'.expdate',
												$this->tableName.'.minamount',$this->tableName.'.maxdiscount',
												$this->tableName.'.discounttype',$this->tableName.'.value'),
										  ' and '.$this->tableName.'.pkey = ' . $this->oDbCon->paramString($voucherkey));
		
		// status gk bisa dimasukkan dlam criteria, karena di admin form, perlu utk hitung ulang ketika edit
		
		if(empty($rsVoucher)) return 0;
		
		$expDate = $rsVoucher[0]['expdate'];
		$minAmount = $rsVoucher[0]['minamount'];
		$maxDiscount = $rsVoucher[0]['maxdiscount'];
		$discountType = $rsVoucher[0]['discounttype'];
		$value = $rsVoucher[0]['value'];
		
		//$this->setLog($totalSales .'<'. $minAmount,true);
		if($minAmount > 0 && $totalSales < $minAmount ) return 0;
		
		//$this->setLog($discountType,true);
		
		if($discountType == 2){ 
			$value = $value / 100 *  $totalSales; 
			if($maxDiscount > 0 && $value > $maxDiscount) $value = $maxDiscount; 
		}
		
		return ($value > $totalSales) ? $totalSales : $value; // harusnya gk ad max discount kalo jenisnya nilai voucher
		
	}
	
  }

?>