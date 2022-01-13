<?php   

$FILE_NAME = basename ($_SERVER['PHP_SELF'] ,".php");

require_once DOC_ROOT. 'connections/_connection.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/BaseClass.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/AutoCode.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CustomCode.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Lang.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Mobile_Detect.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Employee.class.php';   
 
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
   

$autoCode = new AutoCode(); 
$customCode =  new CustomCode() ; 
$employee =  createObjAndAddToCol(new Employee());
 
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
              
            // number : -2 // jd -3 aj
            // accounting : null
            // integer : 0
            // decimal : 2

            switch($format){
                case 'integer':  $content = $obj->formatNumber($content,0);
                                 break;
                case 'decimal':  $content = $obj->formatNumber($content,2);
                                 break;
                case 'number':  $content = $obj->formatNumber($content,-3);
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

function includeClass($classFile, $createObject = true){
    if (!is_array($classFile)) $classFile = array($classFile);
    
    foreach($classFile as $file){ 
         $filePath = DOC_ROOT. 'include/'.CLASS_VERSION.'/'.$file;
            if(is_file($filePath))
                require_once $filePath; 
    }
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

// DEFINE
define('USE_SN', false); // $item->loadSetting('showSerialNumber') // temporary disabled
?>
