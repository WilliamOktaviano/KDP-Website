<?php
class RewardsPoint extends BaseClass{
  
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'rewards_point';   
		$this->tablePointLog = 'rewards_point_log';    
		$this->tableStatus = 'ar_status';
		$this->tableCustomer = 'customer'; 
        $this->tableSalesOrder = 'sales_order_header';
        $this->tableWarehouse = 'warehouse'; 
		$this->securityObject = 'TransactionPoint'; 
        $this->isTransaction = true;
       
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');
    	$this->arrData['statuskey'] = array('selStatus');
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['refkey'] = array('hidRefKey');
        $this->arrData['reftabletype'] = array('hidRefTableType'); 
        $this->arrData['expdate'] = array('expDate','date'); 
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['point'] = array('point','number');
        $this->arrData['outstanding'] = array('outstanding','number');
        $this->arrData['trdesc'] = array('trDesc');
        
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'refCode','title' => 'reference','dbfield' => 'refcode','default'=>true, 'width' => 120 ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername','default'=>true, 'width' => 200 ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'amount','title' => 'amount','dbfield' => 'amount','default'=>true, 'width' => 100, 'align' => 'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'outstanding','title' => 'outstanding','dbfield' => 'outstanding','default'=>true, 'width' => 100, 'align' => 'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename',  'width' => 120));
        
        //array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
       
//        $this->printMenu = array();
//        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/ar'));

        $this->includeClassDependencies(array(  
                  'Customer.class.php',   
                  'Warehouse.class.php', 
                  'SalesOrder.class.php',
         ));  
       
        $this->overwriteConfig();
	}
		
    function getQuery(){
	   
		$sql = '
				select
					'.$this->tableName. '.*,
                  	'.$this->tableCustomer.'.name as customername,
					'.$this->tableStatus.'.status as statusname,
					'.$this->tableWarehouse.'.name as warehousename  
				from 
					'.$this->tableName . ',
                    '.$this->tableStatus.' ,
                    '.$this->tableCustomer.' ,
                    '.$this->tableWarehouse.' 
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey and 
					'.$this->tableName . '.warehousekey = '.$this->tableWarehouse.'.pkey and 
					'.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey
		' .$this->criteria ; 
        
        //$sql .=  $this->getWarehouseCriteria() ;
        
        return $sql;
	}
	 
     
    
    function validateCancel($id,$autoChangeStatus=false){
	 
		$arrayToJs = array(); 
        $this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' . $this->errorMsg[201]);    
        
		return $arrayToJs;
	 } 	
		  

    function normalizeParameter($arrParam, $trim = false){ 
        $arrParam = parent::normalizeParameter($arrParam,true);   
        return $arrParam;
    }
	
	 
    function afterStatusChanged($rsHeader){
         // kalo dr perubahan status
         // harus set ulang cancelreason
         $pkey = $rsHeader[0]['pkey'];
         $this->resyncCustomerPoint($rsHeader[0]['customerkey']);   
    } 
	
	 function resyncCustomerPoint($customerkey){
		 $customer = new Customer();
		 $rsCustomer = $customer->searchDataRow(array($customer->tableName.'.point',$customer->tableName.'.membershiplevel',$customer->tableName.'.canusepoint'),' and '.$customer->tableName.'.pkey = ' . $customer->oDbCon->paramString($customerkey));
	
		  $sql = ' select coalesce(sum(
							'.$this->tableName.'.outstanding),0) as totalpoint 
							from '.$this->tableName.' 
							where 
							'.$this->tableName.'.statuskey in (2,3) and 
							'.$this->tableName.'.customerkey = '.$this->oDbCon->paramString($customerkey); 
		 $rsTotalPoint = $this->oDbCon->doQuery($sql); 	  
		 $totalPoint = $rsTotalPoint[0]['totalpoint'];
			  
		 if($rsCustomer[0]['canusepoint'] == 1){ 
			 $canUsePoint = 1; 
		 } else {
			 $firstPoint = $this->loadSetting('minimumFirstPoint');   
			 // $canUsePoint cuma boleh sekali diset diawal
			 $canUsePoint = ($totalPoint >= $firstPoint) ? 1 : 0;
		 }
		
		 
         $sql = 'update '.$this->tableCustomer.' 
				set
					'.$this->tableCustomer.'.point = '.$this->oDbCon->paramString($totalPoint).' ,
					canusepoint = '.$this->oDbCon->paramString($canUsePoint).' 
                where 
				'.$this->tableCustomer.'.pkey = '. $this->oDbCon->paramString($customerkey);
           
       $this->oDbCon->execute($sql); 
    }
    
	function getSumTotalRewards($customerkey){
		$sql = 'select sum(outstanding) as point from '.$this->tableName.' where customerkey = '.$this->oDbCon->paramString($customerkey).' and statuskey in (2,3)';	
		$rs = $this->oDbCon->doQuery($sql); 
		return $rs[0]['point'];	

	}
	
	function updateExpPoint($customerkey =''){
		
		try{ 
            
		 	if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]); 
			 
			$sql = 'update '.$this->tableName.' set statuskey = 4 where '.$this->tableName.'.expdate < date(now())';
		
			if(!empty($customerkey))
				$sql .= ' and '.$this->tableName.'.customerkey = '.$this->oDbCon->paramString($customerkey);
			
			//$this->setLog($sql,true);
			$this->oDbCon->execute($sql);
			
		    $this->oDbCon->endTrans();
					  
					 
		} catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false, $e->getMessage());   
		}
         
	}
	
	function recalculatePointOutstanding($customerkey){
			$sql = 'update '.$this->tableName.' set '.$this->tableName.'.outstanding = point - (
							select coalesce(sum('.$this->tablePointLog.'.amount),0) as total from  '.$this->tablePointLog.'
										where  
											'.$this->tablePointLog.'.refkey = '.$this->tableName.'.pkey and
											'.$this->tableName.'.customerkey = '.$this->oDbCon->paramString($customerkey).' and
											'.$this->tableName.'.expdate > date(now())
									) 
						where 
							'.$this->tableName.'.customerkey = '.$this->oDbCon->paramString($customerkey). ' and
							'.$this->tableName.'.expdate > date(now())';
																											
			$this->oDbCon->execute($sql); 
			$this->resyncCustomerPoint($customerkey);  
	}
	
	function deductPoint($customerkey,$totalPoint,$arrTransaction){
		
		$rsPoint = $this->searchDataRow( array( $this->tableName.'.pkey',$this->tableName.'.outstanding'),
							' and '.$this->tableName.'.customerkey = ' .$this->oDbCon->paramString($customerkey).'
							  and '.$this->tableName.'.statuskey = 2 
							  and '.$this->tableName.'.outstanding > 0 ',
						 ' order by '.$this->tableName.'.expdate asc, '.$this->tableName.'.pkey asc'
						);
			
		try{ 
            
		 	if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]); 
			
			 	$ctr =0 ;
				while($totalPoint > 0 && isset($rsPoint[$ctr])){
					$rewardsPointKey = $rsPoint[$ctr]['pkey'];
					
					$pointDeducted = ($totalPoint < $rsPoint[$ctr]['outstanding'] ) ? $totalPoint : $rsPoint[$ctr]['outstanding'];

					// isi di log terus count ulang saja
					// jd pas cancel jg gampang
					
					$sql = 'insert into '.$this->tablePointLog.' 
								(refkey,amount,trdate,reftabletype,reftransactionkey) 
							values (
								'.$this->oDbCon->paramString($rewardsPointKey).', 
								'.$this->oDbCon->paramString($pointDeducted).', 
								now(),
								'.$this->oDbCon->paramString($arrTransaction['refTableType']).',
								'.$this->oDbCon->paramString($arrTransaction['pkey']).'
							) ';
					$this->oDbCon->execute($sql);
 
					$totalPoint -= $pointDeducted;
					$ctr++;
				}
			
			$this->recalculatePointOutstanding($customerkey); 
		    $this->oDbCon->endTrans();
					   
		} catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false, $e->getMessage());   
		}
		 
		
	}
	
	function cancelPointDeduction($customerkey,$arrTransaction){
		try{ 
            
		 	if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]); 
		 
			$sql = 'delete from  '.$this->tablePointLog.' 
					where 	
						reftabletype = '.$this->oDbCon->paramString($arrTransaction['refTableType']).' and
						reftransactionkey  = '.$this->oDbCon->paramString($arrTransaction['pkey']);
			
			$this->oDbCon->execute($sql);
			
			$this->recalculatePointOutstanding($customerkey); 
		    $this->oDbCon->endTrans();
					   
		} catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false, $e->getMessage());   
		}
	}
	 
}
		
?>