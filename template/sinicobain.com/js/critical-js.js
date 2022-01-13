var uploadedImage = {}; // nanti baru dipindahkan jika perlu
var uploadedFile = {};   

function preventSubmitBeforeJSLoaded(obj){
    event.preventDefault(); 
    alert('Please wait until the web fully loaded.');
}

function addEventListenerByClass(className, event, fn) {
    var list = document.getElementsByClassName(className);
    for (var i = 0, len = list.length; i < len; i++) {
        list[i].addEventListener(event, fn, false);
    }
}
function removeEventListenerByClass(className, event, fn) {
    var list = document.getElementsByClassName(className);
    for (var i = 0, len = list.length; i < len; i++) {
        list[i].removeEventListener(event, fn, false);
    }
}

function hideOverlayScreen(){ 
    $("html, body").css("overflow","inherit"); 
    $("#popup-panel").fadeOut("fast");   
    //$(".hide-on-dismiss").hide();
}

function loadOverlayScreen(arrParam){      
    
    $(':focus').blur();
    $("html, body").css("overflow","hidden");
    $("#popup-panel").fadeIn("fast"); 
     
    if (arrParam.url){ 
        $("#popup-panel").load(arrParam.url); 
    } 
    $("#popup-panel" ).on( "click", function(e) {  
        if ($(e.target).hasClass('content-panel')) { return; }
        if ($(e.target).closest('.content-panel').length) { return; }
        hideOverlayScreen();
    });
}

function createImageUploader(fileUploaderTarget,fileInfo,multipleFile){ 
       
	var target = $("." + fileUploaderTarget.name); // sementara asumsi semua nama class gk ad yg sama
	   
	 if (fileInfo.token == undefined || fileInfo.token == "")  
		 fileInfo.token = Math.floor((Math.random() * 1000) + 1).toString() + $.now();     
	
/*  
	uploadedImage[fileInfo.token] = Array();*/
	target.append("<input type=\"hidden\" name=\"" + fileUploaderTarget.name + "\" />"); 
	target.append("<input type=\"hidden\" name=\"token-" + fileUploaderTarget.name + "\" value=\"" + fileInfo.token + "\" />");
	  
	/*
    // ini nanti saja
    if (fileInfo.arrImage != undefined || fileInfo.arrImage == ""){ 
         var i;
      	 for(i=0;i<fileInfo.arrImage.length;i++)  { 
             pushImageThumb(fileUploaderTarget,{"folder":fileInfo.folder, "token":fileInfo.token, "fileName":fileInfo.arrImage[i],"phpThumbHash":fileInfo.phpThumbHash[i]},multipleFile,multipleColor,variantTarget) 
         }
	}*/
	 
    var btnLabel = (fileUploaderTarget.btnLabel) ? fileUploaderTarget.btnLabel : 'Upload File';
    
	var uploader = new qq.FileUploader({
						element: target.find('.file-uploader')[0], 
						action: '/fileuploader.php?action=upload&folder=' + fileInfo.folder + '&token='+ fileInfo.token, 
						allowedExtensions:['jpg','jpeg','png','gif','ico'],
                        template : '<div class="qq-uploader"><div class="qq-upload-drop-area"><span>Drop files here to upload</span></div><div class="qq-upload-button">'+btnLabel+'</div><ul class="qq-upload-list"></ul></div>',
						onComplete: function(id, fileName, responseJSON){   
							if (responseJSON.success == true)
								pushImageThumb(fileUploaderTarget,{"folder":fileInfo.folder, "token":fileInfo.token, "fileName":responseJSON.fileName,"phpThumbHash":responseJSON.phpThumbHash},multipleFile); 
						} 
					});   
	 
	return fileInfo.token;				  
}
   

function pushImageThumb(fileUploaderTarget,fileInfo,multipleFile){  
      
    var path = PHP_CONFIG['uploadTempDocShort']; 
    
	var target = $("." + fileUploaderTarget.name); // sementara asumsi semua nama class gk ad yg sama
	var iconMultipleColor = '';
	 
	if (multipleFile == false) 
		target.find(".image-list").html("");
 
	var extension = fileInfo.fileName.substr( (fileInfo.fileName.lastIndexOf('.') +1) );
	fileurl = "../phpthumb/phpThumb.php?src="+path+ fileInfo.folder + fileInfo.token+ "/"+fileInfo.fileName+"&w=150&h=150&far=C&hash=" + fileInfo.phpThumbHash;
	
	if (extension == 'ico')
		fileurl = PHP_CONFIG['uploadTempURL'] + fileInfo.folder + fileInfo.token+ "/"+fileInfo.fileName;
	  
	 
 	var temp = "<li relfilename=\""+fileInfo.fileName+"\" relPHPThumbHash=\""+fileInfo.phpThumbHash+"\">";
	temp += "<div class=\"file-uploader-image\"><img src=\""+ fileurl +"\"/></div>";
	//temp += "<div class=\"file-uploader-action-bar\">";
	//temp += "<a href=\"#\" onClick=\"deleteImageUploaderThumb(this,{'tabID':'"+fileUploaderTarget.tabID+"' , 'name':'"+fileUploaderTarget.name+"'},'" + fileInfo.token  + "')\"><i class=\"far fa-times\" style=\"float:right; font-size:1.2em;\" ></i></a>";
	//temp += "<a href=\"/phpthumb/phpThumb.php?src="+path+fileInfo.folder + fileInfo.token+ "/"+fileInfo.fileName+"&far=C&hash="+fileInfo.phpThumbHash+"\" target=\"_blank\"><i class=\"far fa-eye\" style=\"float:right; font-size:1.2em; margin-right:0.5em; padding-bottom:0.2em\"></i></a>";
	//temp += "</div>";
	temp += "</li>"; 
	
	target.find(".image-list").append(temp);	
	   
	updateItemImageArray(fileUploaderTarget,fileInfo.token);
	  
    target.find('.image-list li:last img').on('load', function(){
        $(this).closest(".file-uploader-image").css("background-image","none"); 
    }); 
			
}

function updateItemImageArray(fileUploaderTarget,token){
    
	var target = $("." + fileUploaderTarget.name); // sementara asumsi semua nama class gk ad yg sama
    
	 uploadedImage[token] = Array();
	 target.find(".image-list li").each(function(i) {      
		uploadedImage[token].push($(this).attr("relfilename")); 
     });
	   
	 target.find("[name=" + fileUploaderTarget.name + "]").val(uploadedImage[token]);
	 
}


function createFileUploader(fileUploaderTarget,folder,token, arrFile,multipleFile){
    var target = $("." + fileUploaderTarget); // sementara asumsi semua nama class gk ad yg sama

	if (token == undefined || token == "")  
		 token = Math.floor((Math.random() * 1000) + 1).toString() + $.now();     
	
	uploadedFile[token] = Array();
	target.append("<input type=\"hidden\" name=\"" + fileUploaderTarget + "\" />"); 
	target.append("<input type=\"hidden\" name=\"token-" + fileUploaderTarget + "\" value=\"" + token + "\" />");
	  
	if (arrFile != undefined || arrFile == ""){ 
         var i;
		 for(i=0;i<arrFile.length;i++) 
			 pushFileThumb(fileUploaderTarget,folder,token,arrFile[i],multipleFile) 
	}
	 

	var uploader = new qq.FileUploader({
						element: target.find('.file-uploader')[0], 
						action: '/fileuploader.php?action=upload&isfile=1&folder=' + folder + '&token='+ token,  
						onComplete: function(id, fileName, responseJSON){  
							if (responseJSON.success == true)
								pushFileThumb(fileUploaderTarget,folder,token,responseJSON.fileName,multipleFile); 
						} 
					});   
    

	 
	return token;				  
}

function pushFileThumb(fileUploaderTarget,folder,token,fileName,multipleFile){ 
	var target = $("." + fileUploaderTarget); // sementara asumsi semua nama class gk ad yg sama
	var extension = fileName.substr( (fileName.lastIndexOf('.') +1) ).toLowerCase();
	
	var xPos = 0;
	/*switch(extension) { 
        case 'doc':
        case 'docx':
            xPos = 35;
        break;
        case 'pdf':
              xPos = 0;
        break;
        default:
          	 xPos = 70;
    }*/ 
	
 	var temp = "<li relfilename=\""+fileName+"\" >"; 
	temp += "<div class=\"panel\">"; 
	temp += "<div class=\"file-uploader-description\"><a href=\"/download.php?filename="+ folder + token+ "/"+fileName+"\" target=\"_blank\" title=\""+fileName+"\">"+ fileName +"</a></div>"; 
	temp += "<div class=\"delete-icon\" onClick=\"deleteFileUploaderThumb(this,'" + fileUploaderTarget  + "','" + token  + "')\"><i class=\"fas fa-times\"></i></div>";
    temp += "<div style=\"clear:both;\"></div>";
    temp += "</div>";
	temp += "</li>";
     
    if (multipleFile == false){  
		target.find(".file-list").html(""); 
	} 
	
	target.find(".file-list").append(temp);	
	   
	updateItemFileArray(fileUploaderTarget,token);
	    
	target.find('.file-list li:last img').on('load', function(){
	  $(this).closest(".file-uploader-image").css("background-image","none"); 
	});  
			
}   

function updateItemFileArray(fileUploaderTarget,token){
	
	var target = $("." + fileUploaderTarget); // sementara asumsi semua nama class gk ad yg sama

	 uploadedFile[token] = Array();
	 $(" ." +fileUploaderTarget + " .file-list li").each(function(i) {     
			 uploadedFile[token].push($(this).attr("relfilename"));
	  });
	   
	 target.find("[name=" + fileUploaderTarget + "]").val(uploadedFile[token]);
	 
}

function deleteFileUploaderThumb(obj,fileUploaderTarget,token){
	$(obj).closest("li").remove(); 
	updateItemFileArray(fileUploaderTarget,token); 
} 

// === update per project    
function loadSubMenuProfile(){
    $(".subsec-profile").hover(
          function() {
            $( this ).find(".profile-sub-menu").fadeIn("fast");  
          }, function() {
            $( this ).find(".profile-sub-menu").fadeOut("fast");  
          }
   ); 
}

function disableButton(targetContent,status){
    if(status == undefined) status = true;
    
    targetContent.prop("disabled",status);
    
    if(status){ 
        targetContent.find(".loading-icon:first").show();  
    }else{ 
        targetContent.find(".loading-icon:first").hide();  
    }
}

function setStatusColorNotification(targetContent, status){
    // status : 1. error, 2. sukses, 3.... warning
    
    switch(status){
        case 1 :  targetContent.removeClass("bg-green-avocado").addClass("bg-red-salmon show");
                   break;
        case 2 : targetContent.removeClass("bg-red-salmon").addClass("bg-green-avocado show");
                  break  ;
    }
     
}


function inputNumberOnBlur(obj,decimal){  	 
	  if(obj.val() == "" || !$.isNumeric(unformatCurrency(obj.val())) ) { 
          obj.val(0);
		 
		 try {
			$(obj).closest('form').bootstrapValidator('revalidateField', $(obj).attr("name"));   
		 }
		 catch(err) {
			 
		}
 	  }
	  
	  if (decimal == undefined)
	  	decimal = 0 ;
	  obj.formatCurrency({roundToDecimalPlace: decimal });
}

function inputNumberOnFocus(obj){
    
    if (obj.prop('readonly'))  return;
    if (obj.prop('disabled'))  return;
        
     if(obj.val() == 0 || !$.isNumeric(unformatCurrency(obj.val())) )
         obj.val("");
}

function unformatCurrency(value){ 
	if (value == undefined)
		return 0;
		
	return value.replace(/,/g,"");
}	 

function removeDetailRows(obj,alwaysDeleteRow){  
    
    var transactionTable = $(obj).closest(".transaction-detail");
    $listRow = transactionTable.find(">.transaction-detail-row");
    var detailRow = $(obj).closest(".transaction-detail-row");

    //console.log('length ' + $listRow.length);
    if ($listRow.length <= 1 && !alwaysDeleteRow) {  
        detailRow.find("input").not(obj).val("").blur();
        detailRow.find("textarea").html("");
        detailRow.find("select").prop('selectedIndex', 0); 
        detailRow.find(".image-panel").css("background-image","");  
    }  else{     
        detailRow.remove();
    }

    updateRowNumber(transactionTable);

    var handler = $(obj).attr("attrhandler"); 
    if (handler != undefined)  eval(handler);

}

 
function updateRowNumber(obj){ 
    var incr = 1;
    obj.find(".row-number").each(function() { 
        $(this).html(incr+".");
        incr++;
    });
}        

  function addNewTemplateRow(selector, arrValue, rowSelector, rebindHandler, newRowPosition){
                    //selector HARUS class, bkn id
                    //rowSelector => utk obj pada row tertentu. misalnya class ".row-template" terdapat di beberapa tempat dalam satu form

                    var elToken = $("#defaultForm").find("[name='detailRowsToken[]']:enabled"); // diletakan diatas, karena baris baru blm terbentuk

                    var groupName = '.transaction-detail';
                    var newRowClass = 'transaction-detail-row';
                    var $template = (rowSelector) ? rowSelector.find("." +selector) : $("#defaultForm ." + selector);
                    var $newRow   = $template.clone().removeClass(selector).removeClass("row-template").addClass(newRowClass);


                    if (newRowPosition)
                        $newRow.insertAfter(newRowPosition);
                    else    
                        $newRow.insertBefore($template.first());


                    $newRow.show(); 

                     if(arrValue != undefined && arrValue.length > 0){ 
                            var temp = JSON.parse(arrValue);  
                            var i;
                            for(i=0;i<temp.length;i++) {     
                                  if(jQuery.type(temp[i].value) === 'string' )  
                                      temp[i].value = decodeHTMLEntities(temp[i].value); 

                                  $newRow.find("[name=\"" + temp[i].selector +"[]\"]").val( temp[i].value );
                            }  
                      }

                    $newRow.find('.inputnumber').bind( "blur", function(event) { inputNumberOnBlur($(this)); });
                    $newRow.find('.inputdecimal').bind( "blur", function(event) { inputNumberOnBlur($(this),2);});  
                    $newRow.find('.inputautodecimal').bind( "blur", function(event) { inputNumberOnBlur($(this),-2);});  
                    $newRow.find(".inputnumber, .inputdecimal, .inputautodecimal").bind("focus",function(event) { inputNumberOnFocus($(this)); } )

                    $newRow.find('input, select, textarea').removeAttr("disabled tabIndex");   
                    $newRow.find('.no-tabs-index, input[readonly], select[readonly], textarea[readonly]').attr("tabIndex","-1");  // kecuali input date ?
       
                    //set ulang, karena kalo level kedua, sudah ke enabled ketika add row 
                    $newRow.find('.row-template').find('input, select, textarea').attr("disabled",true).attr("tabIndex","-1"); 

                    $newRow.find('.ckeditor').each(function(){  
                        var newID = new Date().getTime() + Math.floor((Math.random() * 99999) + 1); 
                        $(this).attr("id",newID);
                        $("#" + newID).ckeditor(); 
                    });	   

                    $newRow.find('.btn-more-options').bind( "click", function(event) { mnvOptionsRowOnClick($(this))  });
                    $newRow.find(".mnv-barcode-input").keydown(function (e) {barcodeHandler(this,e); });

                    var temp = elToken.last().val();
                    token = (temp) ? parseInt(temp) + 1 : 1; 
                    $newRow.find("[name='detailRowsToken[]']").val(token);

                    var formInput = $newRow.find('.options-row .form-panel').find("input, select, textarea"); 
                    formInput.addClass("label-style");
                    formInput.bind("focusout", function(event) { $(this).addClass("label-style");})
                             .bind("focus", function(event) { $(this).removeClass("label-style") }); 

                    // Set the label and field name  
                    $newRow.find('.remove-button').bind( "click", function(event) {
                        removeDetailRows(this);    
                    });
            
                    $newRow.find('.add-row-button').unbind('click').bind( "click", function(event) {  
                        $row = addNewTemplateRow($(this).attr("attr-template"),null,$(this).closest(groupName),rebindHandler,$(this).closest("." + newRowClass) ); 
                    });

                    updateRowNumber($newRow.closest(groupName));

                    if (rebindHandler) rebindHandler();
                    return $newRow;
}

addEventListenerByClass('prevent-form-submit', 'submit', preventSubmitBeforeJSLoaded); 
