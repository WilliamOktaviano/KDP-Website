<?php
  
class MembershipSubscription extends BaseClass{ 
  

    function __construct(){

            parent::__construct();


            $this->tableName = 'membership_subscription';
            //$this->tableNameDetail = 'membership_subscription_detail';
            $this->tableCustomer = 'customer';
			$this->tableMembershipLevel = 'membership_level';
            $this->tableWarehouse = 'warehouse'; 
            $this->tableStatus = 'transaction_status';
            $this->tablePayment = 'membership_subscription_payment'; 
            //$this->tablePaymentConfirmation = 'payment_confirmation'; 
		    $this->tableTermOfPayment = 'term_of_payment'; 
            $this->tableARStatus = 'ar_status'; 
            $this->isTransaction = true; 		

			$this->newLoad = true;
            $this->securityObject = 'MembershipSubscription';   

            $this->arrLinkedTable = array(); 
            $defaultFieldName = 'refkey';
            array_push($this->arrLinkedTable, array('table'=>'ar','field'=>$defaultFieldName));  

        
            $this->arrPaymentDetail = array(); 
            $this->arrPaymentDetail['pkey'] = array('hidDetailPaymentKey');
            $this->arrPaymentDetail['refkey'] = array('pkey', 'ref');
            $this->arrPaymentDetail['amount'] = array('paymentMethodValue',array('datatype' => 'number','mandatory'=>true));
            $this->arrPaymentDetail['paymentkey'] = array('selPaymentMethod',array('mandatory'=>true)); 

//            $this->arrVoucherDetail = array(); 
//            $this->arrVoucherDetail['pkey'] = array('hidDetailVoucherKey');
//            $this->arrVoucherDetail['refkey'] = array('pkey', 'ref');
//            $this->arrVoucherDetail['amount'] = array('voucherAmount',array('datatype' => 'number','mandatory'=>true));
//            $this->arrVoucherDetail['voucherkey'] = array('hidVoucherKey',array('mandatory'=>true)); 

            $arrDetails = array();
           //array_push($arrDetails, array('dataset' => $this->arrDataDetail));
            array_push($arrDetails, array('dataset' => $this->arrPaymentDetail, 'tableName' => $this->tablePayment));
//            array_push($arrDetails, array('dataset' => $this->arrVoucherDetail, 'tableName' => $this->tableVoucherDetail));

            $this->arrData = array(); 
            $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails)); 
            $this->arrData['code'] = array('code');
            $this->arrData['customcodekey'] = array('selCustomCode'); 
            $this->arrData['trdate'] = array('trDate','datetime');
            $this->arrData['warehousekey'] = array('selWarehouseKey');
            $this->arrData['customerkey'] = array('hidCustomerKey');
            $this->arrData['termofpaymentkey'] = array('selTermOfPaymentKey');
            $this->arrData['trdesc'] = array('trDesc');
            $this->arrData['subtotal'] = array('subtotal','number');
            $this->arrData['finaldiscounttype'] = array('selFinalDiscountType','number');
            $this->arrData['finaldiscount'] = array('finalDiscount','number');
            $this->arrData['beforetaxtotal'] = array('beforeTaxTotal','number');
            $this->arrData['ispriceincludetax'] = array('isPriceIncludeTax');
            $this->arrData['taxpercentage'] = array('taxPercentage','number');
            $this->arrData['taxvalue'] = array('taxValue','number');
            $this->arrData['grandtotal'] = array('grandtotal','number');
            $this->arrData['totalpayment'] = array('totalPayment','number');
            $this->arrData['balance'] = array('balance','number'); 
            $this->arrData['statuskey'] = array('selStatus');
            $this->arrData['point'] = array('point','number');
            $this->arrData['pointvalue'] = array('pointValue','number');
            $this->arrData['refcode'] = array('refCode');  
			$this->arrData['membershiplevelkey'] = array('selMembershipLevel');  
			$this->arrData['activeperiodmonth'] = array('activePeriod','number');
		 
            $this->arrDataListAvailableColumn = array(); 
            array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
            array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align'=>'center', 'format' => 'date'));
            array_push($this->arrDataListAvailableColumn, array('code' => 'customercode','title' => 'customerCode','dbfield' => 'customercode','default'=>true, 'width' => 100));
            array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername','default'=>true, 'width' => 200));
            array_push($this->arrDataListAvailableColumn, array('code' => 'membershiplevel','title' => 'membership','dbfield' => 'membershiplevel','default'=>true, 'width' => 100, 'align' => 'center'));
            array_push($this->arrDataListAvailableColumn, array('code' => 'activeperiod','title' => 'activePeriod','dbfield' => 'activeperiodmonth','default'=>true, 'width' => 100, 'align' => 'right'));
            array_push($this->arrDataListAvailableColumn, array('code' => 'total','title' => 'price','dbfield' => 'grandtotal','default'=>true, 'width' => 100, 'align' => 'right', 'format'=>'number'));
            array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
            array_push($this->arrDataListAvailableColumn, array('code' => 'paymentsuccessicon','title' => '[icon]paymentGateway', 'dbfield' => 'paymentsuccessicon',  'width' => 40, 'align' => 'center'));
            array_push($this->arrDataListAvailableColumn, array('code' => 'paidstatus','title' => 'payment', 'dbfield' => 'paidstatusname',  'width' => 40));
            
            
//            $this->printMenu = array();  
//            array_push($this->printMenu,array('code' => 'printInvoice', 'name' => $this->lang['print'] . ' ' .$this->lang['invoice'],  'icon' => 'print', 'url' => 'print/salesOrder'));
//            array_push($this->printMenu,array('code' => 'printDeliveryNotes', 'name' => $this->lang['print'] . ' ' .$this->lang['deliveryNotes'],  'icon' => 'print', 'url' => 'print/salesOrderDelivery'));
//            array_push($this->printMenu,array('code' => 'printShippingLabel', 'name' => $this->lang['print'] . ' ' .$this->lang['shippingLabel'],  'icon' => 'print', 'url' => 'print/salesLabel'));
//        
              
            $this->includeClassDependencies(array(
                   'TermOfPayment.class.php', 
                   'Warehouse.class.php',  
                   'PaymentMethod.class.php', 
                   'City.class.php', 
                   'Customer.class.php',  
                   'RewardsPoint.class.php', 
                   'COALink.class.php',
                   'VoucherTransaction.class.php', 
                   'PaymentConfirmation.class.php',
                   'AR.class.php',
                   'ARPayment.class.php', 
                   'ChartOfAccount.class.php',
                   'GeneralJournal.class.php',
                   'MembershipLevel.class.php'
            ));  
        
            $this->overwriteConfig();
    }
 
            
    
    function getQuery(){

        $sql = '
            SELECT '.$this->tableName.'.* ,
               '.$this->tableCustomer.'.code as customercode,
               '.$this->tableCustomer.'.name as customername, 
               '.$this->tableCustomer.'.phone as customerphone, 
               '.$this->tableCustomer.'.email as customeremail, 
               '.$this->tableStatus.'.status as statusname, 
               '.$this->tableTermOfPayment.'.code as termofpaymentcode,
               '.$this->tableTermOfPayment.'.name as termofpaymentname,
               '.$this->tableARStatus.'.status as paidstatusname,
			   '.$this->tableMembershipLevel.'.name as membershiplevel,
               IF(paymentgatewaysuccess=1, "<i class=\"fas fa-check text-green-avocado\"></i>", "") as paymentsuccessicon
            FROM 
                '.$this->tableStatus.',   
                '.$this->tableCustomer.',   
				'.$this->tableMembershipLevel.',
                '.$this->tableName.' 
                    left join '.$this->tableTermOfPayment.' on '.$this->tableName.'.termofpaymentkey = '.$this->tableTermOfPayment.'.pkey
                    left join '.$this->tableARStatus.' on  '.$this->tableName.'.paidstatuskey =  '.$this->tableARStatus.'.pkey
            WHERE '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey and
                  '.$this->tableName.'.membershiplevelkey = '.$this->tableMembershipLevel.'.pkey and
                  '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey  
        ' .$this->criteria ;
		
        $sql .=  $this->getWarehouseCriteria() ;
        $sql .=  $this->getCompanyCriteria() ;
  
        return $sql;
    }
	 


    function reCountSubtotal($arrParam){

            $isPriceIncludeTax = (!empty($arrParam['chkIncludeTax'])) ? 1 : 0;  

			$membershipLevel  = new MembershipLevel();
			$rsMembership = $membershipLevel->getDataRowById($arrParam['selMembershipLevel']);
		
            $subtotal = (!empty($rsMembership)) ? $rsMembership[0]['sellingprice'] : 0  ;
            $grandtotal = $subtotal;
 
			//$arrVoucherKey = $arrParam['hidVoucherKey']; 
            $taxValue = $this->unFormatNumber($arrParam['taxValue']);  
            $finalDiscount = $this->unFormatNumber($arrParam['finalDiscount']); 
            $finalDiscountType = $arrParam['selFinalDiscountType']; 
            $taxPercentage = $this->unFormatNumber($arrParam['taxPercentage']);  
            $shipmentFee = 0 ; //$this->unFormatNumber($arrParam['shipmentFee']); 
            $etcCost = $this->unFormatNumber($arrParam['etcCost']); 
        
            $arrVoucherDetail = array();
		 
            if ($finalDiscount != 0){
                if ($finalDiscountType == 2)
                    $finalDiscount = $finalDiscount/100 * $grandtotal;
            } 
		
			// hitung ulang nilai voucher
//			$voucherValue = 0;
//			$useVoucherPoint = $this->loadSetting('transactionVoucherPoint');
//			
//			if($useVoucherPoint == 1){  
//				$voucherTransaction = new VoucherTransaction();
//				for ($i=0;$i<count($arrVoucherKey);$i++){
//					$arrVoucherDetail[$i]['voucherAmount'] = $voucherTransaction->calculateVoucherValue($arrVoucherKey[$i], $subtotal - $finalDiscount);
//					$voucherValue += $arrVoucherDetail[$i]['voucherAmount'];
//				}
//			}
			     
			$point  = $this->unFormatNumber($arrParam['point']);   
			$pointValue = $point * $this->loadSetting('rewardsPointUnitValue');
			
            $totalFinalDiscountAndPointValue = $finalDiscount + $pointValue ;
 
            $beforeTaxTotal = $subtotal - $totalFinalDiscountAndPointValue;
            $grandtotal = $beforeTaxTotal;

            if ($isPriceIncludeTax == false) {
                    $taxValue = $beforeTaxTotal * $taxPercentage / 100;
                    $taxValue = round($taxValue); // kalo ad koma, nilainya gantung di AR nanti
                    $grandtotal += $taxValue;
            }else{
                    $taxValue = ($taxPercentage/(100 + $taxPercentage)) * $grandtotal;   
                    $beforeTaxTotal = $grandtotal - $taxValue ;
            }

            $grandtotal +=  $shipmentFee + $etcCost;


            $balance = 0;
            $totalPayment = 0; 

            $termOfPayment = new TermOfPayment();
            $rsTOP = $termOfPayment->getDataRowById($arrParam['selTermOfPaymentKey']);  
            if ($rsTOP[0]['duedays'] == 0){ 
                $payment = $arrParam['paymentMethodValue'];
                for($i=0;$i<count($payment);$i++){
                    $totalPayment += $this->unFormatNumber($payment[$i]);
                } 
            }


            $balance = $totalPayment - $grandtotal; 

            $reCountResult = array();
            $reCountResult['subtotal'] = $subtotal;
            $reCountResult['beforeTaxTotal'] = $beforeTaxTotal;
            $reCountResult['isPriceIncludeTax'] = $isPriceIncludeTax;
            $reCountResult['taxValue'] = $taxValue;
            $reCountResult['grandtotal'] = $grandtotal;
            $reCountResult['totalPayment'] = $totalPayment;
            $reCountResult['balance'] = $balance;
            $reCountResult['pointValue'] = $pointValue; 

            return $reCountResult;

    } 
	
    function validateForm($arr,$pkey = ''){ 
            $arrayToJs = parent::validateForm($arr,$pkey); 

			$useVoucherPoint = $this->loadSetting('transactionVoucherPoint');
		
            $customerkey = $arr['hidCustomerKey'];   
            
            //validasi kalo status gk menunggu gk bisa edit 
            if (!empty($pkey)){
                $rs = $this->getDataRowById($pkey);
                if ($rs[0]['statuskey'] <> 1){
                    $this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
                }
            }  

            if(empty($customerkey)){
                $this->addErrorList($arrayToJs,false,$this->errorMsg['customer'][1]);
            }
  
 
			// validasi voucher
		
			// harus bedain, kalo mengecualikan voucher sendiri, tetep harus cek voucher sudah digunakan atau blm..
//			$hasVoucher = false;
//			foreach($arrVoucherKey as $voucherkey)
//				if(!empty($voucherkey)) $hasVoucher = true;
//					
//			if($useVoucherPoint == 1 && $hasVoucher){ 
				
//				$voucherTransaction = new VoucherTransaction();
//				$rsVoucherTransaction = $voucherTransaction->searchDataRow(array($voucherTransaction->tableName.'.pkey', $voucherTransaction->tableName.'.customerkey', $voucherTransaction->tableName.'.expdate', $voucherTransaction->tableName.'.statuskey'),
//																		  ' and '.$voucherTransaction->tableName.'.pkey in ('.$this->oDbCon->paramString($arrVoucherKey,',').')  
//																		  	and '.$voucherTransaction->tableName.'.reftranskey in (0, '.$this->oDbCon->paramString($pkey).')
//																		 ');
//				// status harus cek manual sepertinya,
//				// karena kalo dipake transaksi sendiri, statusnay sudah 3
//				
//				$rsVoucherTransaction = array_column($rsVoucherTransaction,null,'pkey'); 
//				// voucher harus punya customer yg sama
//				$totalVoucher = count($arrVoucherKey);
//				for($i=0;$i<$totalVoucher;$i++) { 
//						
//					if (!isset($rsVoucherTransaction[$arrVoucherKey[$i]])){ 
//						$this->addErrorList($arrayToJs,false, $this->errorMsg['voucher'][6]); 	
//					}else{ 
//						$rsVoucher = $rsVoucherTransaction[$arrVoucherKey[$i]];
//					
//						if( $arrVoucherKey[$i] <> $rsVoucher['pkey']  && $rsVoucher['statuskey'] <> 2)
//							$this->addErrorList($arrayToJs,false, $this->errorMsg['voucher'][3]); 	 
//						 
//						if($rsVoucher['customerkey'] <> $customerkey)
//							$this->addErrorList($arrayToJs,false, $this->errorMsg['voucher'][5]); 	 
//						
//
//						// voucher blm epxired
//						$expDate = $this->formatDBDate($rsVoucher['expdate'],'d / m / Y'); 
//						$transDate = $arr['trDate']; 
//						$dateDiff = $this->dateDiff($expDate,$transDate);
//						if($dateDiff > 0)
//							$this->addErrorList($arrayToJs,false, $this->errorMsg['voucher'][8]); 	 
//
//					}  
//					
//				}
// 
//				
//				$minLevel = $this->loadSetting('minMembershipLevelToUseVoucher');
//				$customer = new Customer();
//				$rsCustomer = $customer->searchDataRow(array($customer->tableName.'.pkey',$customer->tableName.'.membershiplevel'),
//													   ' and '.$customer->tableName.'.pkey = ' . $this->oDbCon->paramString($customerkey));
//				
//				// keanggotaan sudah boleh menggunakan voucher
//				if($rsCustomer[0]['membershiplevel'] < $minLevel)
//					$this->addErrorList($arrayToJs,false, $this->errorMsg['voucher'][7]);
//				
//			}
			
            return $arrayToJs;
    }
 

    function validateConfirm($rsHeader){
		
		$id = $rsHeader[0]['pkey'];
        $rewardsPoint = new RewardsPoint();
        $warehouse = new Warehouse();  
        $coaLink = new COALink();
        $voucherTransaction = new VoucherTransaction(); 
		$customer = new Customer(); 
		
		$customerkey = $rsHeader[0]['customerkey'];
		$rsCustomer = $customer->getDataRowById($customerkey);

		$useVoucherPoint = $this->loadSetting('transactionVoucherPoint');
		
        $rsPayment = $this->getPaymentMethodDetail($id); 
		//$rsVoucher =  $this->getVoucherDetail($id); 
			
        $termOfPayment = new TermOfPayment();
        $rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']);  
        $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false;   
		
        $totalPayment = 0; 
        for($i=0;$i<count($rsPayment); $i++)
            $totalPayment += $rsPayment[$i]['amount'];

        $balance = $totalPayment - $rsHeader[0]['grandtotal'];   
 
        if ($isCash){ 
            $thresholdDiscount = abs($this->loadSetting('roundedPaymentThreshold'));
            if($balance < ($thresholdDiscount * -1)) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[502]);
            else if ($balance > $thresholdDiscount)
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[509]); 
        }else{
              
            //validasi creditlimit
            // masi harus ditambahkan dengan JO yg masih menggantung
            
            if ($rsCustomer[0]['creditlimit'] > 0){  
                $total = $this->unFormatNumber($rsHeader[0]['grandtotal']);
                if ($customer->willExceedCreditLimit($customerkey,$total)){
                     $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['creditlimit'][1]);
                }
            }
            
        }
         
		
        // validasi point
        $point = $rsHeader[0]['point'];
        $currentPoint = $rsCustomer[0]['point'];
        if ($point > $currentPoint)
            $this->addErrorLog(false, $this->errorMsg['point'][3]);

        if (USE_GL){
                $arrCOA = array();
                array_push($arrCOA, 'salesretail' , 'taxout', 'otherrevenue', 'hpp' , 'inventory' , 'inventorytemp', 'salesretaildiscount'); 
                for ($i=0;$i<count($arrCOA);$i++){
                    $rsCOA = $coaLink->getCOALink ($arrCOA[$i], $warehouse->tableName,$rsHeader[0]['warehousekey'], 0); 
                    if (empty($rsCOA))	
                        $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$arrCOA[$i]. ' ' .$this->errorMsg['coa'][3]);
                }   

                if ($isCash){
                    for($i=0;$i<count($rsPayment); $i++){ 
                        if ($rsPayment[$i]['amount'] > 0 ){ 
                            $rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$rsHeader[0]['warehousekey'], $rsPayment[$i]['paymentkey']); 
                            if (empty($rsCOA))	
                                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['coa'][3]);
                        }
                    } 
                }else{ 
                        // validasi COA piutang  
                        $rsCOA = $coaLink->getCOALink ('ar', $warehouse->tableName,$rsHeader[0]['warehousekey'], 0); 
                        if (empty($rsCOA))	
                            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['coa'][3]);
                }
 
         } 
         
		 
		// validasi voucher
		
//		$arrVoucherKey = array_column($rsVoucher,'voucherkey'); 
//		$totalVoucher = count($arrVoucherKey);
//		if($useVoucherPoint == 1 && $totalVoucher > 0){
//			
//			
//			$voucherTransaction = new VoucherTransaction();
//			$rsVoucherTransaction = $voucherTransaction->searchDataRow(array($voucherTransaction->tableName.'.pkey', $voucherTransaction->tableName.'.customerkey', $voucherTransaction->tableName.'.expdate'),
//																	  ' and '.$voucherTransaction->tableName.'.pkey in ('.$this->oDbCon->paramString($arrVoucherKey,',').')  
//																	  	and '.$voucherTransaction->tableName.'.reftranskey in (0, '.$this->oDbCon->paramString($id).')
//																	');
//		 
//			$rsVoucherTransaction = array_column($rsVoucherTransaction,null,'pkey');
//			
//			// voucher harus punya customer yg sama 
//			for($i=0;$i<$totalVoucher;$i++) { 
//
//				if (!isset($rsVoucherTransaction[$arrVoucherKey[$i]])){ 
//					$this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['voucher'][6]); 	
//				}else{ 
//					$rsVoucher = $rsVoucherTransaction[$arrVoucherKey[$i]];
//					
//					if( $arrVoucherKey[$i] <> $rsVoucher['pkey']  && $rsVoucher['statuskey'] <> 2)
//						$this->addErrorLog(false, $this->errorMsg['voucher'][3]); 	 
//
//					if($rsVoucher['customerkey'] <> $customerkey)
//						$this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['voucher'][5]); 	 
// 
//					// voucher blm epxired
//					$expDate = $this->formatDBDate($rsVoucher['expdate'],'d / m / Y'); 
//					$transDate = $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
//					$dateDiff = $this->dateDiff($expDate,$transDate);
//					if($dateDiff > 0)
//						$this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['voucher'][8]); 	 
//
//				}  
//
//			}
//
//
//			$minLevel = $this->loadSetting('minMembershipLevelToUseVoucher');
//			$customer = new Customer();
//			$rsCustomer = $customer->searchDataRow(array($customer->tableName.'.pkey',$customer->tableName.'.membershiplevel'),
//												   ' and '.$customer->tableName.'.pkey = ' . $this->oDbCon->paramString($customerkey));
//
//			// keanggotaan sudah boleh menggunakan voucher
//			if($rsCustomer[0]['membershiplevel'] < $minLevel)
//				$this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['voucher'][7]);
//
//		}

 
    }
  
    function updatePaidStatus($pkey,$paidStatus){
        $sql = 'update '.$this->tableName.' set paidstatuskey = ' . $this->oDbCon->paramString($paidStatus) .' where pkey = ' . $this->oDbCon->paramString($pkey);
        $this->oDbCon->execute($sql);
    }

    function confirmTrans($rsHeader){  
         
        $id = $rsHeader[0]['pkey']; 
       
        $coaLink = new COALink(); 
        $termOfPayment = new TermOfPayment();
        $customer = new Customer();
		
        $rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']); 
		$isCash = ($rsTOP[0]['duedays'] == 0) ? true : false; 
 
		$refTableKey = $this->getTableKeyAndObj($this->tableName,array('key'))['key'];
			
        // MENGHITUNG PAYMENT
        if ($isCash){ 
            $rsPayment = $this->getPaymentMethodDetail($id);    

            for($i=0;$i<count($rsPayment); $i++){  
                  if (USE_GL) {
                       $rsPaymentCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$rsHeader[0]['warehousekey'], $rsPayment[$i]['paymentkey']); 
				       $coakey = $rsPaymentCOA[0]['coakey']; 
				   }else{
				       $coakey = $rsPayment[$i]['paymentkey'];
				   } 
             } 
            
            $this->updatePaidStatus($id,3);
            
        }else{
            //update AR
            $ar = new AR();

            $arrParam = array();	
 
            $arrParam['code'] = 'xxxxxx';
            $arrParam['hidCustomerKey'] = $rsHeader[0]['customerkey'];
            $arrParam['hidSalesKey'] = $rsHeader[0]['saleskey'];
            $arrParam['hidRefKey'] = $id;
            $arrParam['hidRefHeaderKey'] = $id;
            $arrParam['hidRefCode'] =  $rsHeader[0]['code']; 
            $arrParam['hidRefCode2'] =  $rsHeader[0]['refcode'];  
            $arrParam['hidRefTable'] = $refTableKey;
            $arrParam['hidRefDate'] =   $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
            $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
            $arrParam['selARType'] = 1; 
            $arrParam['amount'] = abs($rsHeader[0]['grandtotal']);
            $arrParam['trDesc'] = '';
            $arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');  
            $date = new DateTime($rsHeader[0]['trdate']);
            $date->add(new DateInterval('P'.$rsTOP[0]['duedays'].'D'));
            $arrParam['dueDate'] = $date->format('d / m / Y');// date ('d / m / Y', mktime(0, 0, 0, date("m")  , date("d")+$rsTOP[0]['duedays'], date("Y")));
            $arrParam['createdBy'] = 0;
            $arrParam['islinked'] = 1; 
            $arrParam['overwriteGL'] = 1;

            $arrayToJs = $ar->addData($arrParam); 
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);    
        }
        // END            
 	
		// update membership
		$sql = 'update '.$this->tableCustomer.' set membershiplevel = '.$this->oDbCon->paramString($rsHeader[0]['membershiplevelkey']).' where pkey = ' .$this->oDbCon->paramString($rsHeader[0]['customerkey']);
		$this->oDbCon->execute($sql);
		
		
		if($rsHeader[0]['point'] > 0){ 
			$rewardsPoint = new RewardsPoint(); 
			$rewardsPoint->deductPoint($rsHeader[0]['customerkey'], $rsHeader[0]['point'], array('pkey' => $id, 'refTableType' => $refTableKey));
		} 
		
		
		$customer->resetReferralKeyIfQuotaExceed($rsHeader[0]['customerkey']);
		
        //update jurnal umum 
        $this->updateGL($rsHeader);
         
    } 
 

    function validateCancel($rsHeader,$autoChangeStatus=false){ 
        $id = $rsHeader[0]['pkey'];
    
        //cek ad AR terbayar 
        $ar = new AR();
        $rsARKey = $ar->getTableKeyAndObj($this->tableName); 
        $rsAR = $ar->searchData('','',true,' and reftabletype = '.$this->oDbCon->paramString($rsARKey['key']).' and refkey = '.$this->oDbCon->paramString($id).' and ('.$ar->tableName.'.statuskey  in (2,3))');
        if(!empty($rsAR)) 
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['ar'][2]);
  
    } 



    function cancelTrans($rsHeader,$copy){ 
        $id = $rsHeader[0]['pkey'];
        
		$ar = new AR();
		
		$refTableKey = $this->getTableKeyAndObj($this->tableName,array('key'))['key'];
        
        $rsARKey = $ar->getTableKeyAndObj($this->tableName); 
        $rsAR = $ar->searchData('','',true,' and reftabletype = '.$this->oDbCon->paramString($rsARKey['key']).' and refkey = '.$this->oDbCon->paramString($id).' and '.$ar->tableName.'.statuskey = 1');
        for($i=0;$i<count($rsAR);$i++) {
            $arrayToJs = $ar->changeStatus($rsAR[$i]['pkey'],TRANSACTION_STATUS['batal'],'',false,true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }
  
		// harusnya sudah kehandle di afterStatusChanged
//		$sql = 'update '.$this->tableCustomer.' set membershiplevel = 1 where pkey = ' .$this->oDbCon->paramString($rsHeader[0]['customerkey']);
//		$this->oDbCon->execute($sql);
		
		if($rsHeader[0]['point'] > 0){
        	$rewardsPoint = new RewardsPoint();
			$rewardsPoint->cancelPointDeduction($rsHeader[0]['customerkey'], array('pkey' => $id, 'refTableType' => $refTableKey));
		}
		
        if ($copy)  $this->copyDataOnCancel($id);	  

        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);

    }  
 
	function  afterStatusChanged($rsHeader){
		
		// ambil ulang agar dpt status baru
		$rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
		
		if ($rsHeader[0]['statuskey'] == 2 || $rsHeader[0]['statuskey'] == 4){ 
			$customer = new Customer();
			$customer->updateExpDate($rsHeader[0]['customerkey']);
		}
		
	}
	
    function updateGL($rs){
		return; 
    }
     
	function afterUpdateData($arrParam, $action){  
        $pkey = $arrParam['pkey']; 
		
        // kalo add user baru 
	 	if($action == INSERT_DATA)
        	$this->sendSubscriptionInvoiceEmail($pkey);   
		
    } 
    
	function sendSubscriptionInvoiceEmail($pkey){
		
        global $twig;
        
		$customer = new Customer();
     	
		$rs = $this->searchData('','',' and '.$this->tablename.'.pkey = ' .$this->oDbCon->paramString($pkey) );
 
        // nanti jadikan default variable
        $arrTwigVar = array();
        $arrTwigVar = $this->getDefaultEmailVariable();
         
        $arrTwigVar['CUSTOMER_NAME'] = $rs[0]['customername'];
        $arrTwigVar['MEMBERSHIP_NAME'] = $rs[0]['membershiplevel'];
        $arrTwigVar['MEMBERSHIP_PRICE'] = $rs[0]['grandtotal'];
        
        $twig->render('email-template.html');  
        $content = $twig->render('email-subscription-invoice.html', $arrTwigVar);
 
		//$this->setLog($content,true);
        $this->sendMail(array(), $this->lang['membership'] . ' - ' . DOMAIN_NAME,$content,array('name' => $rs[0]['customername'], 'email'=>$rs[0]['customeremail'])); 
        
	}
	
	
	
    function generateInvoice($pkey){   
        $rsHeader = $this->getDataRowById($pkey);

        $file=  HTTP_HOST . 'membership-invoice/'.$pkey.'/'.md5($pkey . $rsHeader[0]['grandtotal'] . $this->secretKey).'/1';     
        $invoice =  file_get_contents($file);
        
        return $invoice;
    }

     function normalizeParameter($arrParam, $trim = false){
 
			$termOfPayment = new TermOfPayment();
		 	$membershipLevel = new MembershipLevel();
		 
			$arrParam['paymentMethodValue'] = (isset($arrParam['paymentMethodValue'])) ? $arrParam['paymentMethodValue'] : array();
 
			//$arrVoucherkey = $arrParam['hidVoucherKey']; 
		 
			$rsTOP = $termOfPayment->getDataRowById($arrParam['selTermOfPaymentKey']);  
			if ($rsTOP[0]['duedays'] != 0){   
				for($i=0;$i<count( $arrParam['paymentMethodValue']);$i++){ 
					$arrParam['paymentMethodValue'][$i] = 0; 
					$arrParam['hidDetailPaymentKey'][$i] = 0;
				}
			}
  
		 	
		 	$rsLevel = $membershipLevel->searchDataRow(array($membershipLevel->tableName.'.activeperiodmonth'),
													  ' and '.$membershipLevel->tableName.'.pkey = '. $this->oDbCon->paramString($arrParam['selMembershipLevel']));
		 	
		 	$arrParam['activePeriod']  = (!empty($rsLevel)) ? $rsLevel[0]['activeperiodmonth'] : 0;
         
            $reCountResult = $this->reCountSubtotal($arrParam);  
            $arrParam['detailVoucher'] = $reCountResult['detailVoucher'];
            $arrParam['subtotal'] = $reCountResult['subtotal'];
		  
            $arrParam['pointValue'] = $reCountResult['pointValue'];
            $arrParam['beforeTaxTotal'] = $reCountResult['beforeTaxTotal'];
            $arrParam['isPriceIncludeTax'] = $reCountResult['isPriceIncludeTax'];
            $arrParam['taxValue'] = $reCountResult['taxValue'];
            $arrParam['grandtotal'] = $reCountResult['grandtotal'];
            $arrParam['totalPayment'] = $reCountResult['totalPayment'];
            $arrParam['balance'] = $reCountResult['balance']; 
            
		 	
				
        $arrParam = parent::normalizeParameter($arrParam,true); 
        return $arrParam;
    }
     
    function updateMidtransResponse($response){
        
        if($this->isJSON($response))
            $postVars = json_decode($response,true);
        else
            parse_str($response,$postVars);  
        
        $transCode = explode('-',$postVars['order_id']);  
        array_pop($transCode); 
        $transCode = implode('',$transCode); 
        
        $transactionStatus = $postVars['transaction_status'];
        $amount =  $postVars['gross_amount'];
        
         try{  
                if(!$this->oDbCon->startTrans())
                    throw new Exception($this->errorMsg[100]);
 
                $sql = 'update ' .$this->tableName.' set paymentgatewayresponse =  \''.addslashes(json_encode($postVars)).'\'
                        where code = ' . $this->oDbCon->paramString($transCode) ; 
                $this->oDbCon->execute($sql); 

                $this->oDbCon->endTrans();

        }catch (Exception $e){
            $this->oDbCon->rollback();
             
        } 
        
    }
    
    function updatePaymentGatewwayResponse($pkey,$arr){
          try{  
                if(!$this->oDbCon->startTrans())
                    throw new Exception($this->errorMsg[100]);
 
                $sql = 'update '.$this->tableName.' 
                        set 
                            paymentgatewayinvoiceurl = '.$this->oDbCon->paramString($arr['invoiceURL']) .' 
                        where 
                            pkey = ' . $this->oDbCon->paramString($pkey) ;
             
                $this->oDbCon->execute($sql); 

                $this->oDbCon->endTrans();

        }catch (Exception $e){
            $this->oDbCon->rollback();
             
        } 
    }
    
    function sendPaymentInstruction($pkey){
        global $twig;
        
        $sql = 'select
                    '.$this->tableName.'.code,
                    '.$this->tableName.'.paymentgatewayinvoiceurl, 
                    '.$this->tableCustomer.'.name,
                    '.$this->tableCustomer.'.email
                from 
                    '.$this->tableName.'
                    left join '.$this->tableCustomer.' on  '.$this->tableName.'.customerkey =  '.$this->tableCustomer.'.pkey
                where
                    '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($pkey);
        
        
        $rs = $this->oDbCon->doQuery($sql);

        $transactionCode = $rs[0]['code'];
        $email = $rs[0]['email']; // (!empty($rs[0]['email'])) ? $rs[0]['email'] : $rs[0]['recipientemail']; // kalo pake email satu lg, nanti gk konsisten dengna email di shopping cart
        $name =  $rs[0]['name']; //(!empty($rs[0]['name'])) ? $rs[0]['name'] : $rs[0]['recipientname'];
        
        $arrTwigVar = array();
        $arrTwigVar = $this->getDefaultEmailVariable();
         
        $arrTwigVar['CUSTOMER_NAME'] = $name;
        $arrTwigVar['INVOICE_URL'] = $rs[0]['paymentgatewayinvoiceurl'];
        
        $twig->render('email-template.html');  
        $content = $twig->render('email-payment-instruction.html', $arrTwigVar);

        /*$this->setLog($this->lang['paymentInstruction'] . ' ' .$transactionCode.' - ' . DOMAIN_NAME);
        $this->setLog($email);*/
        
        $this->sendMail(array(), $this->lang['paymentInstruction'] . ' ' .$transactionCode.' - ' . DOMAIN_NAME,$content,array('email'=>$email)); 
 
    }
    
    function paymentGatewaySuccess($pkey){
        try{  
                if(!$this->oDbCon->startTrans())
                    throw new Exception($this->errorMsg[100]);

                    $arrayToJs = array(); 


                    $termOfPayment = new TermOfPayment();
                    $rsTOP = $termOfPayment->searchDataRow(array($termOfPayment->tableName.'.pkey'),
                                                            ' and '.$termOfPayment->tableName.'.duedays > 0 ',
                                                            ' order by duedays asc limit 1'
                                                          );
            
                    $topkey = (!empty($rsTOP)) ? $rsTOP[0]['pkey'] : 1;
            
                    $sql = 'update ' .$this->tableName.' 
                            set paymentgatewaysuccess = 1, termofpaymentkey = '.$this->oDbCon->paramString($topkey).' 
                            where  
                            pkey = ' .  $this->oDbCon->paramString($pkey) ;
            
                    $this->oDbCon->execute($sql); 

                    $this->oDbCon->endTrans();

                    $this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   

        }catch (Exception $e){
            $this->oDbCon->rollback();
            $this->addErrorList($arrayToJs,false,$e->getMessage());    
        }

    }
	
//	
//	  
//    function getVoucherDetail($pkey,$paymentMethodKey=''){
//        $paymentMethodKeyCriteria = '';
//        if (!empty($paymentMethodKey))
//            $paymentMethodKeyCriteria = ' and  '.$this->tableVoucherDetail.'.voucherkey = ' . $this->oDbCon->paramString($paymentMethodKey);
//
//        $sql = 'select 
//                    '.$this->tableVoucherDetail.'.*,
//					'.$this->tableVoucherTransaction.'.code
//                from  
//                   '.$this->tableVoucherDetail.',
//				   '.$this->tableVoucherTransaction.'
//                where  
//					'.$this->tableVoucherDetail.'.voucherkey =  '.$this->tableVoucherTransaction.'.pkey and
//                    '.$this->tableVoucherDetail.'.refkey in ('.$this->oDbCon->paramString($pkey,',').')
//					'.$paymentMethodKeyCriteria.'
//				order by 
//					 '.$this->tableVoucherDetail.'.voucherkey asc';	
//        return $this->oDbCon->doQuery($sql); 
//    } 
//       
//    
//     
    
        
}
?>