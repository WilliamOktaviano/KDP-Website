<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('Employee.class.php');
$employee = createObjAndAddToCol(new Employee());
$city = createObjAndAddToCol(new City());
$loginLog = createObjAndAddToCol(new LoginLog());

$obj = $employee;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
  
$addDataFile = (isset($_GET) && !empty($_GET['showPrivileges'])) ? 'employeeForm?showPrivileges=1' : 'employeeForm';
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Nama', $obj->tableName . '.name'));
array_push($arrSearchColumn, array('Email', $obj->tableName . '.email'));
array_push($arrSearchColumn, array('Kategori', $obj->tableCategory. '.name'));
array_push($arrSearchColumn, array('Telepon', $obj->tableName. '.phone'));
array_push($arrSearchColumn, array('HP', $obj->tableName. '.mobile'));
array_push($arrSearchColumn, array('Email', $obj->tableName. '.email'));
array_push($arrSearchColumn, array('Alamat 1', $obj->tableName . '.address1'));
array_push($arrSearchColumn, array('Alamat 2', $obj->tableName . '.address2'));
array_push($arrSearchColumn, array('Lokasi', $obj->tableCity . '.name'));
array_push($arrSearchColumn, array('Kategori Lokasi', $obj->tableCityCategory . '.name'));
array_push($arrSearchColumn, array('Kode Pos', $obj->tableName . '.zipcode'));

function generateQuickView($obj,$id){
	global $city;
	global $security; 
    global $loginLog;
	
	$rs = $obj->getDataRowById($id);   
	$rsCity = $city->searchData('city.pkey',$rs[0]['citykey'],false); 
	$detail = '';
	  
	// ================================================== INFORMASI DASAR ========================================================================================== //
		
		$basicInformation  = ' <div class="data-card border-blue">
								<h1>'.ucwords($obj->lang['generalInformation']).'</h1> 
								<div class="content">
								<div class="div-table" style="width:100%;">
									<div class="div-table-row">
										<div class="div-table-col" style="width:35%">'.ucwords($obj->lang['username']).'</div> 
										<div class="div-table-col">'.$rs[0]['username'].'</div> 
									</div>
									<div class="div-table-row">
										<div class="div-table-col">'.ucwords($obj->lang['livingAddress']).'</div> 
										<div class="div-table-col">'.$rs[0]['livingaddress1'].'</div> 
									</div>
									<div class="div-table-row">
										<div class="div-table-col"></div> 
										<div class="div-table-col">'.$rs[0]['livingaddress2'].'</div> 
									</div>
									 
									<div class="div-table-row">
										<div class="div-table-col" style="height:1em"></div> 
										<div class="div-table-col"></div> 
									</div>
									
									<div class="div-table-row">
										<div class="div-table-col">'.ucwords($obj->lang['email']).'</div> 
										<div class="div-table-col">'.$rs[0]['email'].'</div> 
									</div>
									<div class="div-table-row">
										<div class="div-table-col">'.ucwords($obj->lang['phone']).'</div> 
										<div class="div-table-col">'.$rs[0]['phone'].'</div> 
									</div>
									<div class="div-table-row">
										<div class="div-table-col">'.ucwords($obj->lang['mobilePhone']).'</div> 
										<div class="div-table-col">'.$rs[0]['mobile'].'</div> 
									</div>
								</div>
								</div>
							</div>  
				'; 	
		 
		 
// ================================================== LOGIN HISTORY ========================================================================================== //
 
		$rsHistory = (!empty($rs[0]['username'])) ? $loginLog->searchData($obj->tableName.'.username',$rs[0]['username'],true,'order by createdon desc limit 10') : array();
		
		$loginInformation = ' <div class="data-card border-green">
						<h1><div style="float:left;">'.ucwords($obj->lang['loginHistory']).'</div></h1> <div style="clear:both;"></div> <div class="content">'; 
		
		
		if (empty($rsHistory)){
			$loginInformation .= '<div style="margin-top:1em">- '.ucfirst($obj->lang['thisUserHasNoHistoryOfLogin']).' -</div>';
		}else{
			$loginInformation .= '<div class="div-table">';
			
			
			for($i=0;$i<count($rsHistory);$i++){
				$color = '';
				if ($rsHistory[$i]['statuskey'] == 2)
					$color = 'color:#af0e21';
					
				$loginInformation .= '<div class="div-table-row">
							<div class="div-table-col" style="width:10em;'.$color.'">'.$obj->formatDbDate($rsHistory[$i]['createdon'],'d / m / Y H:i:s').'</div> 
							<div class="div-table-col" style="'.$color.'">'.$rsHistory[$i]['statusname'].'</div> 
						</div>';
			} 
						
			$loginInformation .= '</div>'; 
		}
		
		
		$loginInformation .= ' </div></div> '; 	 
	  
		
		$detail .= '<div class="div-table" style="width:100%; ">
							<div class="div-table-row">
								<div class="div-table-col-5"  style="width:33%; ">
								'.$basicInformation.'
								</div>  
								<div class="div-table-col-5">
								'.$loginInformation.'
								</div> 
							</div>
					</div>';
				  
		$detail .= '<div style="clear:both;"></div>';	
		 
	 
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
