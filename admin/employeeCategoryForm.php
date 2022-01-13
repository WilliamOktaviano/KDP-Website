<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 
 
includeClass(array(
'Category.class.php',    
'EmployeeCategory.class.php'
)); 
$employeeCategory = createObjAndAddToCol(new EmployeeCategory());

$obj= $employeeCategory;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
     
$formAction = 'employeeCategoryList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
$rs = prepareOnLoadData($obj); 

if (!empty($_GET['id'])){ 
	$_POST['name'] = $rs[0]['name']; 
} 

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');   

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 


<script type="text/javascript">
  
	jQuery(document).ready(function(){  
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        setOnDocumentReady(tabID);   
			 
		 $('#defaultForm-' + tabID)
			.bootstrapValidator({ 
				feedbackIcons: {
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
            },
         fields: {
                  name: { 
                    validators: {
                        notEmpty: {
                            message:  phpErrorMsg.division['1']
                        }, 
                    }
                },  
				
				code: { 
                    validators: {
                        notEmpty: {
                            message:  phpErrorMsg.code['1']
                        }, 
                    }
				} 
				
            }
        })
        .on('success.form.bv', function(e) { 
                 <?php echo $obj->submitFormScript(); ?>
        });
	});
			
</script>

</head> 

<body> 
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>">
        <?php prepareOnLoadDataForm($obj); ?>  
       <div class="div-table main-tab-table-1">
            <div class="div-table-row">
                <div class="div-table-col"> 
                        <div class="div-tab-panel" >
         	 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']); ?></label> 
                                <div class="col-xs-9"> 
                                       <?php echo  $obj->inputSelect('selStatus', $arrStatus); ?>
                                </div> 
                            </div>  
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code']); ?></label> 
                                <div class="col-xs-9">  
                                    <?php echo $obj->inputAutoCode('code'); ?>
                                </div> 
                            </div>   
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['division']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('name'); ?>
                                </div> 
                            </div>   
 
                     </div>
                </div>
           </div>
      </div> 
        
    <div class="form-button-panel" >  
     <?php echo $obj->generateSaveButton(); ?> 
    </div>  
    </form>   
     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>