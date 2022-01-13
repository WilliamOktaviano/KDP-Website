<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  
 
$arrReturn = array();

// ERR MSG
$arrReturn['phpErrorMsg'] =  $class->errorMsg; 

// LANG
$arrReturn['phpLang'] =  $class->lang;

// CONFIG
$arrConfig = array();
$arrConfig["uploadTempDoc"] = UPLOAD_TEMP_DOC;
$arrConfig["uploadTempDocShort"] = UPLOAD_TEMP_DOC_SHORT;
$arrConfig["uploadTempURL"] = UPLOAD_TEMP_URL;
$arrConfig["defaultDocUploadPath"] = DEFAULT_DOC_UPLOAD_PATH;
$arrConfig["adminTotalRowsPerPage"] = $class->adminTotalRowsPerPage;
 
$arrReturn['phpConfiguration'] =  $arrConfig; 

// SETTINGS 
$arrConfig = array();
$rsSetting =  $setting->getSettingData();
for ($i=0;$i<count($rsSetting);$i++){
	$code = $rsSetting[$i]['code'];
	 
	if ($rsSetting[$i]['multivalue'] == 0){ 
			if ($rsSetting[$i]['type'] == 3 )
				$arrConfig ['setting'][$code] =str_replace(chr(13),'<br>',$rsSetting[$i]['value']);
			else
				$arrConfig ['setting'][$code] = $rsSetting[$i]['value'] ;
	}else{ 
		$arrDetail = $setting->getDetailByCode($code);
		$arrConfig ['setting'][$code] = $arrDetail;
	} 
		 
}   

$arrReturn['phpSetting'] =  $arrConfig;

// MODULE SETTINGS
$rs = array();
$sql = 'select * from tablekey where decimalnumber > 0';
$rs = $class->oDbCon->doQuery($sql);
$rs = array_column($rs,null,'pkey');
  
$arrReturn['phpModuleSetting'] =  $rs;

// return 
echo json_encode($arrReturn);

die;

?>