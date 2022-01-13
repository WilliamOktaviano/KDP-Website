function Simulator(opt){   
    var thisObj = this;   
    var errMsg = opt.errMsg;
    var lang = opt.lang; 
    DECIMAL = opt.decimal;
    DOMAIN_NAME = opt.domainName;
    var tabParam = {};

    
    var objAndValue = new Array;  
    objAndValue.push({object:'hidItemKey[]', value :'pkey'}); 
    objAndValue.push({object:'priceInUnit[]', value :'sellingprice'}); 
//		objAndValue.push({object:'selUnit[]', value :'deftransunitkey'}); 
//		objAndValue.push({object:'hidGramasi[]', value :'gramasi'}); 
    var objAndValueForDetailAutoComplete = objAndValue; 
    
     
    this.bindEl = function bindEl(obj,action,func){  
        obj.unbind(action).on(action, func);   
    }
    
     this.updateDetail = function updateDetail(target,objAndValue,ui){

            var detailRow = $(target).closest(".transaction-detail-row"); 
            var itemKeyObj = detailRow.find("[name=\"hidItemKey[]\"]").first();

            for(i=0;i<objAndValue.length;i++){    
                detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).blur();  
                detailRow.find(".image-panel").css("background-image", "url('/phpthumb/phpThumb.php?src=/../_upload/"+DOMAIN_NAME+"/item/"+ ui.item['pkey'] +"/"+ui.item['file']+"&w=200&h=200&hash="+ui.item['phpThumbHash']+"')" );
            }
         
            // harus handle manual utk obj autosearch
            detailRow.find("[name=\"itemName[]\"]").first().val(ui.item['value']); 

            thisObj.calculateDetail(itemKeyObj);
            thisObj.updateCartList();
     }
     
     this.calculateDetail = function calculateDetail(obj){     
            
            var row =  $(obj).closest(".transaction-detail-row");   
            var itemkey =  row.find("[name='hidItemKey[]']").val();

            var qty =  unformatCurrency(row.find("[name='qty[]']").val());
            var priceInUnit =  unformatCurrency(row.find("[name='priceInUnit[]']").val());
            row.find(".price-text").text("");

            var subtotal = qty  *  priceInUnit;
            row.find("[name='detailSubtotal[]'], [name='detailSubtotalMobile[]']").val(subtotal).blur(); 

            if(priceInUnit==0){
                row.find(".mobile-price").removeClass(".price-text");
            }else{
                row.find(".price-text").text(priceInUnit).formatCurrency({roundToDecimalPlace: DECIMAL }); 
            }   
            thisObj.calculateTotal();
   }

    this.calculateTotal = function calculateTotal(){  

        var subtotal = 0; 
        $("[name='detailSubtotal[]']").each(function(){ subtotal += parseInt(unformatCurrency($(this).val())) || 0;  }) 

        var total = (subtotal>=0) ? subtotal : 0;
        $("[name='total']").val(total).blur();

        
        thisObj.updateCartList();
    }
    
   this.bindAutoCompleteForTransactionDetail =  function bindAutoCompleteForTransactionDetail(targetObj,objAndValue,searchFile,handlingFunction){ 

        var objTarget = $( "[name='" + targetObj +"']" );

        var onSelectFunction = '';
        var onChangeFunction = '';


        if ($.isArray(handlingFunction)){
            onSelectFunction = handlingFunction.onSelectFunction;
            onChangeFunction = handlingFunction.onChangeFunction;
        }else{
            onSelectFunction = handlingFunction;
        }

        objTarget.autocomplete({
          source: searchFile,
          autoFocus: true,
          minLength: 1,  
          open: function(event, ui) { 
                if($(this).attr("isbarcode")){  
                    $(this).attr("isbarcode",0);
                    $(this).data("ui-autocomplete").menu.element.children().first().click();
                    $(this).closest("div").next().find("input").focus();
                }
          },       
          select: function( event, ui ) {     

                //var nextInput = $(this).closest("div").next().find("input");

                if (onSelectFunction != undefined && onSelectFunction != ""){

                    //maintain compability
                    if (jQuery.type(onSelectFunction) == 'string')
                        eval(onSelectFunction+"(this,objAndValue,ui)"); 
                    else 
                        onSelectFunction(this,objAndValue,ui);

                    event.preventDefault(); // ini diperlukan agar kita bisa ganti value yg terpilih selain pake nilai 'value'
                }else{ 
                    var tableRow = $(this).closest(".div-table-row");  

                    for(i=0;i<objAndValue.length;i++){  
                        var elObj = tableRow.find("[name='" + objAndValue[i].object +"']").first();
                        var elVal = ui.item[objAndValue[i].value];

                        if (objAndValue[i].type == "date")
                           elVal = moment(elVal).format(_DATE_FORMAT_);

                        elObj.val(elVal).change();//.blur();   // gk boleh ad blur, kalo ad blur, change dibawah gk jalan, terus kalo utk number gmana ??
                        //console.log(objAndValue[i].object + " >> " + $(this).closest(".div-table-row").find("[name='" + objAndValue[i].object +"']").val());

                    }
                    // asumsi nilai field index biasanya bukan angka

                    tableRow.find(".inputnumber, .inputdecimal, .inputautodecimal").blur(); 
                }   

                // kalo focus next, gk sempet keupdate sepertinya, sudah pindah duluan
                //nextInput.focus();
            },  
          search: function( event, ui ) { 	    
                    /*for(i=0;i<objAndValue.length;i++){
                        $(this).closest(".div-table-row").find("[name='" + objAndValue[i].object +"']").first().val("").blur(); 
                    }*/ 
          },
          change: function( event, ui ) {     
                var nextInput = $(this).closest("div").next().find("input");

                var chkPick = $(this).closest(".div-table-row").find("[name='chkPick[]']");
                //if(chkPick.length == 0) return; //klao gk ad checkbox, gk perlu lanjut

                if (ui.item == null) {  
                    chkPick.prop("checked", false);

                    for(i=0;i<objAndValue.length;i++){  
                        $(this).closest(".div-table-row").find("[name='" + objAndValue[i].object +"']").first().val("").blur();
                    }

                    $(this).val("").change();

                } else{  
                    chkPick.prop("checked", true);
                }

                // gk bisa taro di change, karena ad delay nanti terlihat
                //$(this).closest(".div-table-row").find(".inputnumber, .inputdecimal, .inputautodecimal").blur();
                chkPick.change();
                if (onChangeFunction != undefined && onChangeFunction != "") 
                    eval(onChangeFunction+"(this,objAndValue,ui)");


                nextInput.focus();
            },
        });

    }
    
    this.updateCartList = function updateCartList(){ 
        
         $.ajax({
            type: "POST",
            async: false,
            url: "/ajax-simulator.php", 
            data : $("#defaultForm").serialize() + "&action=updateCartList",
            success: function(data){    
                //console.log(data);
            } 
        });
    }
    
    this.onUpdateQtyMobile = function onUpdateQtyMobile(obj){
       var row =  $(obj).closest(".transaction-detail-row");  
       row.find("[name='qty[]']").val($(obj).val()).change();
    }
    
    this.rebindEl = function rebindEl(){ 
       // thisObj.bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete,'ajax-item.php?action=searchData',thisObj.updateDetail);
        thisObj.bindEl($(".transaction-detail-row").find(" [name='qty[]'], [name='priceInUnit[]']" ), 'change',  function(){ thisObj.calculateDetail(this) });  
        thisObj.bindEl($(".transaction-detail-row").find(" [name='qtyMobile[]']" ), 'change',  function(){ thisObj.onUpdateQtyMobile(this) });  
    
    }
         

    this.loadOnReady = function loadOnReady(){   
         
        jQuery.ui.autocomplete.prototype._resizeMenu = function () {
          var ul = this.menu.element;
          ul.outerWidth(this.element.outerWidth());
        } 
        
        var rebindHandler = null; // sementara
        var groupName = '.transaction-detail';
        var newRowClass = 'transaction-detail-row';
         
        thisObj.bindEl( $(".remove-button, .mobile-remove-button"), 'click',  function(){ 
            removeDetailRows(this) 
            thisObj.calculateTotal();
        });  
        
        /*thisObj.bindEl( $(".add-row-button"), 'click', function(){ 
            addNewTemplateRow($(this).attr("attr-template"),null,$(this).closest(groupName),rebindHandler,$(this).closest("." + newRowClass));
            thisObj.rebindEl();              
        } 
        );  */
     
        addNewTemplateRow("detail-row-template",null,null,thisObj.rebindEl);

        $('#defaultForm')
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
        message: errMsg.name[1]
        },
        }
        },
        }
        })
        .on('success.form.bv', function(e) {

        $("[name=btnSave]").prop('disabled', true).addClass("btn-primary-loading");
        // Prevent form submission
        e.preventDefault(); 
        var $form = $(e.target); 
        var bv = $form.data('bootstrapValidator'); 
        disableButton($form.find("[name=btnSave]"));
            
        // Use Ajax to submit form data
        $.post($form.attr('action'), $form.serialize(), function(result) {
        disableButton($form.find("[name=btnSave]"),false);
            
        $("[name=btnSave]").removeClass("btn-primary-loading");
          var error = "";

        for (i=0;i<result.length;i++) error=error + "<li>" + result[i].message + "</li>";
        if (error != "")
        error = "<ul class=\"message-dialog-ul\">" + error + "</ul>";
        $(".notification-msg").html(error).hide().fadeToggle("fast");
        if (!result[0].valid){
        $(".notification-msg").removeClass("bg-green-avocado").addClass("bg-red-cardinal");
        $form.data('bootstrapValidator').resetForm();
        scrollToTopForm($form);
        }else{
        $(".notification-msg").removeClass("bg-red-cardinal").addClass("bg-green-avocado");
        alert(result[0].message);
        location.href="/simulator-list";
        }
        }, 'json');
        });
 
        
    };

}
