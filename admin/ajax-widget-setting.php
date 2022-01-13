<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  
includeClass(array('WidgetSetting.class.php'));
$widgetSetting = new WidgetSetting();
$obj = $widgetSetting;  
include 'ajax-general.php';

if (isset($_POST) && !empty($_POST['action'])) {
			switch ( $_POST['action']){ 
                case 'removeWidget' :
                     
                    $widgetkey = $_POST['widgetkey'];
                    $obj->removeWidget($obj->userkey,$widgetkey); 
                    
                    echo json_encode($widgetSetting->getSelectedWidgets()); 
                    break;
                    
                case 'updateSettings' : 
                    
                    $_POST['employeekey'] = $obj->userkey; 
                    $obj->updateSettings($_POST); 
                      
                    echo json_encode($widgetSetting->getSelectedWidgets()); 
 
                    break;
            }
}


die; 
?>