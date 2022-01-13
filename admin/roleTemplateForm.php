<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('RoleTemplate.class.php');
$roleTemplate = createObjAndAddToCol(new RoleTemplate());

$obj= $roleTemplate;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'roleTemplateList';
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$rs = prepareOnLoadData($obj); 

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
	$_POST['name'] = $rs[0]['name'];  
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$rsSecurityObjectCategory = $security->getSecurityObjectCategory();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style>
#security-module {padding:0; margin:0; list-style:none;} 
#security-module li{padding:0; margin:0;}
#security-module li .chkSecurityList, #security-module li .chkModuleName{font-size:1.5em;margin-right:0.2em;} 
.status-item{display:inline-block;  background-color:#dedede; padding:0.7em 1em 1em 1em; margin-left:0.2em; cursor:pointer;}
.module-item{display:inline-block; background-color:#3399CC; white-space: nowrap; padding:0.7em 1em 1em 1em; color:#FFF; width:20em; cursor:pointer;}

</style> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>  
<script type="text/javascript">  
	
     function RoleTemplate(tabID) { 
         
        this.checkSecurityModule = function checkSecurityModule(obj){  
                                         var objName = obj.prop("name");
                                         var len = obj.closest("li").find(".chkSecurityList:not(:checked)").length;

                                         if (len == 0)
                                            obj.closest("li").find(".chkModuleName").first().prop("checked",true);
                                         else
                                            obj.closest("li").find(".chkModuleName").first().prop("checked",false);

                                    } 
        
     }
     
	
	jQuery(document).ready(function(){  
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        setOnDocumentReady(tabID);  
        
        roleTemplate = new RoleTemplate(tabID);
         
		  
		$("#" + tabID + " #security-module li .chkSecurityList").click(function() {    
		      if ($(this).prop("checked")) 
			 	 $(this).closest(".status-item").addClass("bg-green-avocado text-white");
			  else
			 	 $(this).closest(".status-item").removeClass("bg-green-avocado text-white");
				 
			 roleTemplate.checkSecurityModule($(this));	 
		 });
	
 		 $("#" + tabID + " .chkModuleName").click(function() {     
		       var checked = $(this).prop("checked"); 
		       $(this).closest("li").find(".chkSecurityList").each(function() {   
					 if ($(this).prop("checked") != checked)
					   $(this).click();
				});
		 }); 
		 
        $('#' + tabID + ' [name=btnSelectAll]').click(function() {      
             $('#' + tabID + ' [name^=chkSecurityModuleAccess]').prop("checked",false).click();
        }); 
        $('#' + tabID + ' [name=btnDeselectAll]').click(function() {      
             $('#' + tabID + ' [name^=chkSecurityModuleAccess]').prop("checked",true).click();
        }); 
        
		 $('#defaultForm-' + tabID )
			.bootstrapValidator({ 
				feedbackIcons: {
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                code: { 
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code['1'],
                        }, 
                    }
                },  
               
				name: { 
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.name['1']
                        },  
                    }
                },    
				
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
     <div class="div-table main-tab-table-1" style="float:left">
            <div class="div-table-row">
                <div class="div-table-col"> 
                    <div class="div-tab-panel"> 
                        <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['generalInformation']); ?></div> 
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
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php echo $obj->inputText('name'); ?> 
                                </div> 
                            </div>   
                    </div>
                </div>
         </div>
      </div>
       
 
   <div class="div-table-tab-form" style="float:left; margin-top:2em; width:100%;"> 
               <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['privileges']); ?></div>
              <div>
                    <?php echo $obj->inputLinkButton('btnSelectAll',$obj->lang['selectAll'],array('class' => 'btn btn-link', 'etc' => 'style="padding:0;"')); ?>
                    <?php echo $obj->inputLinkButton('btnDeselectAll',$obj->lang['deselectAll'],array('class' => 'btn btn-link', 'etc'=>'style="padding:0; margin-left:1em"')); ?>
                    <div style="clear:both; height:1em"></div> 
              </div>
                <ul id="security-module" >
				<?php 
					$listSecurityAccess = '';
					
                    for ($catCtr=0;$catCtr<count($rsSecurityObjectCategory);$catCtr++){ 
                        
                        $rsSecurityObject  = $security->generateSecurityObject($rsSecurityObjectCategory[$catCtr]['pkey']);    
                        
                        $listSecurityAccess .= '<div style="clear:both; height: 1em"></div><div class="section-title">'.strtoupper($rsSecurityObjectCategory[$catCtr]['name']).'</div>';
                        
                        $colStyle = ($rsSecurityObjectCategory[$catCtr]['pkey'] == 10) ? ' style="display:inline-block; float:left; margin-right:2em; " ' : '';
                        
                        for ($i=0;$i<count($rsSecurityObject);$i++){ 
                            $listAccessItem = '';  
                            $unChecked = false; 
                            
                            $listSecurityAccess .= '<li '.$colStyle.'>';

                            $arrStatusName[10] = $obj->lang['showAll'];
                            $arrStatusName[11] = $obj->lang['add'];
                            $arrStatusName[12] = $obj->lang['delete'];

                            if ($rsSecurityObject[$i]['modulestatus'] ==  'view_only'){
                                    $selectedClass = '';
                                    $checked = '';

                                    if (!empty($id) && $security->hasSecurityRole($id,$rsSecurityObject[$i]['pkey'],10)){ 
                                        $checked = ' checked="checked"';
                                        $selectedClass = 'bg-green-avocado text-white';	
                                    } else{
                                        $unChecked = true;	
                                    }


                                    $listAccessItem .= '<label  class="status-item '.$selectedClass.'" ><input type="checkbox" class="chkSecurityList" value="10" name="chkList'.$rsSecurityObject[$i]['pkey'].'[]" '.$checked.'/>'.$obj->lang['showAll'].'</label>';
                            }else if ($rsSecurityObject[$i]['modulestatus'] ==  'view_and_update'){

                                    $selectedClass = '';
                                    $checked = '';

                                    if (!empty($id) && $security->hasSecurityRole($id,$rsSecurityObject[$i]['pkey'],10)){ 
                                        $checked = ' checked="checked"';
                                        $selectedClass = 'bg-green-avocado text-white';	
                                    } else{
                                        $unChecked = true;	
                                    }


                                    $listAccessItem .= '<label  class="status-item '.$selectedClass.'" ><input type="checkbox" class="chkSecurityList" value="10" name="chkList'.$rsSecurityObject[$i]['pkey'].'[]" '.$checked.'/>'.$obj->lang['showAll'].'</label>';


                                    $selectedClass = '';
                                    $checked = '';

                                    if (!empty($id) && $security->hasSecurityRole($id,$rsSecurityObject[$i]['pkey'],11)){ 
                                        $checked = ' checked="checked"';
                                        $selectedClass = 'bg-green-avocado text-white';	
                                    } else{
                                        $unChecked = true;	
                                    }


                                    $listAccessItem .= '<label  class="status-item '.$selectedClass.'" ><input type="checkbox" class="chkSecurityList" value="11" name="chkList'.$rsSecurityObject[$i]['pkey'].'[]" '.$checked.'/>Update</label>';
                            }else{
                                for ($k=10;$k<=12;$k++){ 
                                    $selectedClass = '';
                                    $checked = '';

                                    if (!empty($id) && $security->hasSecurityRole($id,$rsSecurityObject[$i]['pkey'],$k)){ 
                                        $checked = ' checked="checked"';
                                        $selectedClass = 'bg-green-avocado text-white';	
                                    } else{
                                        $unChecked = true;	
                                    }


                                    $listAccessItem .= '<label  class="status-item '.$selectedClass.'" ><input type="checkbox" class="chkSecurityList" value="'. $k .'" name="chkList'.$rsSecurityObject[$i]['pkey'].'[]" '.$checked.'/> '. $arrStatusName[$k] .'</label>';
                                 }

                               $rsStatus = $security->getAllStatus($rsSecurityObject[$i]['modulestatus']);  
                                for ($j=0;$j< count($rsStatus);$j++) { 
                                    $checked = '';
                                    $selectedClass = '';
                                    if (!empty($id) &&  $security->hasSecurityRole($id,$rsSecurityObject[$i]['pkey'],$rsStatus[$j]['pkey'])){
                                       $checked = ' checked="checked"';
                                       $selectedClass = 'bg-green-avocado text-white';
                                    }else{
                                        $unChecked = true;	
                                    }

                                    $listAccessItem .= '<label class="status-item '.$selectedClass.'" ><input type="checkbox"  class="chkSecurityList"  value="'. $rsStatus[$j]['pkey'].'" name="chkList'.$rsSecurityObject[$i]['pkey'].'[]" '.$checked.'/> '.$rsStatus[$j]['status'].'</label>';

                                } 
                            }


                             $checked = '';
                             $selectedClass = '';
                             if ($unChecked == false ){
                                  $checked = ' checked="checked"';
                                  $selectedClass = 'bg-green-avocado text-white';
                             }

                             $listSecurityAccess .= '<label class="module-item"><input  type="checkbox"  class="chkModuleName '.$selectedClass.'" name="chkSecurityModuleAccess '. $rsSecurityObject[$i]['pkey'].'" '.$checked.'> '.strtoupper($rsSecurityObject[$i]['modulename']).'</label>';
                             $listSecurityAccess .= $listAccessItem;
                             $listSecurityAccess .= '</li>';

                        }  
                    } 
					
					echo $listSecurityAccess;
				 ?>  
                       		 
              </ul>
			 
   </div>  
      
        <div class="form-button-margin"></div>
        <div class="form-button-panel" > 
       	 <?php echo $obj->generateSaveButton(); ?> 
        </div> 
        
    </form>   
     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>