function Cart(opt){   
    var thisObj = this;   
    var errMsg = (opt.errMsg != undefined ) ? opt.errMsg : new Array;
    var lang = (opt.lang != undefined ) ? opt.lang : new Array;
    var shipmentService = (opt.shipmentService != undefined ) ? opt.shipmentService : new Array;
    
	//var eligiblePoint = 0;
	//var pointUnitValue = 0;
	
    DECIMAL = opt.decimal;
    
    this.updateCartTotalSummary = function updateCartTotalSummary(){
        var totalValue = parseFloat(unformatCurrency($(".mnv-cart-table-subtotal").text())) || 0;
        var shipping = parseFloat(unformatCurrency($("[name=shippingCost]").val())) || 0;
        var discount = parseFloat(unformatCurrency($(".mnv-cart-discount").text())) || 0;
		
		// hitung voucher
		var voucherkey = $(".mnv-cart-voucher-label").closest(".div-table-row").find('[name=\'hidVoucherKey[]\']').val() || 0;
		
		var voucher = 0;
		if(voucherkey != 0 ){
			$.ajax({
				type: "POST",
				url: "/ajax-voucher-transaction.php",  
				async: false,
				beforeSend : function(){

				} ,
				data : {action: 'calculateVoucherValue', 
						voucherkey: voucherkey,
						totalsales: totalValue,  
					   },
				success: function(data){      
					voucher = parseFloat(data) * -1;
					$(".mnv-cart-voucher").text(voucher).formatCurrency({roundToDecimalPlace: DECIMAL}); 
				} 
			}); 
		}
		
		var beforeTax = totalValue + discount + voucher; // + point;
        var tax = beforeTax * 0.1;
		$(".mnv-cart-tax").text(tax).formatCurrency({roundToDecimalPlace: DECIMAL});
		
		//var point = parseFloat(unformatCurrency($(".mnv-cart-point").text())) || 0;
		 
        var total = beforeTax + tax + shipping   
        $(".mnv-cart-total").text(total).formatCurrency({roundToDecimalPlace: DECIMAL}); 
    }

    this.updateCartListSummary = function updateCartListSummary(){ 
         var totalqty = 0,
             details = '',
             itemname = '',
             unit = '',
             qty = 0;
        
         $(".mnv-cart-table").find(".transaction-row").each(function() {  
            qty =  parseFloat(unformatCurrency($(this).find("[name='qty[]']").val()));
            itemname =  $(this).find(".mnv-item-name").text();
            unit =  $(this).find(".mnv-unit").text();
            totalqty += qty;
            details += '<li>' + qty + ' ' + unit + ' ' + itemname + '</li>'; 
         }); 
        
        if(details.length > 0)
            details = '<ul>' + details + '</ul>';
         
        if(!$(".summary-qty"))  
            $(".summary-qty").text(totalqty).formatCurrency({roundToDecimalPlace: DECIMAL }); 
        
        $(".mnv-cart-total-sales").text($(".mnv-cart-table-subtotal").text());
        $(".summary-description").html(details);  
    }
    
    this.updateShipmentSummary = function updateShipmentSummary(){ 
         var details = '';
         
        $(".mnv-recipient-name").html("").hide(); 
        if($("[name=recipientName]").val()) 
            $(".mnv-recipient-name").text($("[name=recipientName]").val()).show(); 
         
        $(".mnv-recipient-phone").html("").hide(); 
        if($("[name=recipientPhone]").val()) 
            $(".mnv-recipient-phone").text($("[name=recipientPhone]").val()).show(); 
         
        $(".mnv-recipient-email").html("").hide(); 
        if($("[name=recipientEmail]").val()) 
            $(".mnv-recipient-email").text($("[name=recipientEmail]").val()).show(); 
          
        $(".mnv-recipient-city").html("").hide(); 
        if($("[name=cityName]").val()) 
            $(".mnv-recipient-city").text($("[name=cityName]").val()).show(); 
         
        $(".mnv-recipient-address").html("").hide(); 
        if($("[name=recipientAddress]").val()){
            $(".mnv-recipient-address").html($("[name=recipientAddress]").val().replace(/\n/g, "<br />")).show();  
        }
        
        $(".mnv-recipient-zipcode").html("").hide(); 
        if($("[name=recipientZipcode]").val()) 
            $(".mnv-recipient-zipcode").html($("[name=recipientZipcode]").val()).show();
           
        var insurance = ($("[name=useInsurance]").val() == 1) ? '('+lang.insurance+')' : '';
        $(".mnv-shipping").text($("[name=selShipmentService] option:selected").html() + insurance);
  
        $(".mnv-total-weight").text($("[name=totalWeight]").val());
        $(".mnv-cart-shipping-cost").text($("[name=shippingCost]").val());
         
        this.updateCartTotalSummary();  
    }

    
    this.openNextAccordionPanel = function openNextAccordionPanel($accordion,$header,$scrollUpPos) {
        if(!$header) $header = 'h2';

        var current = $accordion.accordion("option","active"),
            maximum = $accordion.find($header).length,
            next = current+1 === maximum ? 0 : current+1;

        $accordion.accordion("option","active",next);
        //console.log($accordion);

        if($scrollUpPos){ 
            //console.log($accordion.find(".ui-state-active").position().top );
            $('html, body').stop(true, false).animate({
                scrollTop: $scrollUpPos
            }) 
        } 

    }
    
    this.updateShippingInformation = function updateShippingInformation(){   

        var citykey = $("#mnv-form-cart").find("[name=hidRecipientCityKey]").val();
        var serviceKey = $("#mnv-form-cart").find("[name=selShipmentService]").val();
        var weight = $("#mnv-form-cart").find("[name=totalWeight]").val(); 
        var totalValue = parseFloat(unformatCurrency($(".mnv-cart-table-subtotal").text()));
        var useInsurance = $("#mnv-form-cart").find("[name=useInsurance]").val(); 
        var destinationLatLng = $("#mnv-form-cart").find("[name=hidLatLng]").val(); 
        var destinationZipcode = $("#mnv-form-cart").find("[name=recipientZipcode]").val(); 
            
        var arrItemKey = [];
        var itemkey = $("#mnv-form-cart").find("[name=\"hidItemKey[]\"]"); 
        itemkey.each(function() { arrItemKey.push($(this).val()) })
            
        var arrItemQty = [];
        var itemQty = $("#mnv-form-cart").find("[name=\"qty[]\"]"); 
        itemQty.each(function() { arrItemQty.push($(this).val()) })
               
        totalItem = itemkey.length;
        
        var tempLatLng = destinationLatLng.split(",");
        var latlng = {lat:tempLatLng[0], lng: tempLatLng[1]};
        
        var result = [];
        var items = [];
        var destination = { latlng : latlng, zipcode :destinationZipcode };
        
        for(i=0;i<totalItem;i++) { 
            items.push({
                        'itemkey' : arrItemKey[i],
                        'qty' : arrItemQty[i]
                        });  
        } 
        
        if(destinationZipcode.trim() == ""){
            alert(errMsg.zipcode[1]);
            return;
        }
        
        
       $.ajax({
                type: "POST",
                url: "/ajax-shipment.php",  
                asyn: true,
                beforeSend : function(){
                    disableButton($("[name=btnSave]"));
                    $("[name=shippingCost]").val("Updating....");
                } ,
                data : {action: 'getShippingInformation', 
                        destination: destination,
                        serviceKey:serviceKey, 
                        /*fromCityKey:fromCityKey, // Biteship blm pake city
                        toCityKey: toCityKey, */
                        weight : weight,
                        totalValue : totalValue, 
                        useInsurance : useInsurance,
                        items: items
                       },
                success: function(data){    
                    
                    $("[name=shippingCost]").val(0).blur();
                    
                    disableButton($("[name=btnSave]"),false);
                    
                    if(!data) return;  
                    data = JSON.parse(data);
                    if(data.length == 0){
                        alert(errMsg.shipment[4]);
                        return;
                    }
                     
                    $("[name=shippingCost]").val(data.price).blur();
                    thisObj.updateShipmentSummary(); 
                } 
            }); 
    
        return result;
}
    
    this.recalculateShippingCost  = function recalculateShippingCost(){
        // recalculate total weight
        var totalWeight = 0;

        $(".mnv-cart-table").find(".transaction-row").each(function(){  
            totalWeight  +=  parseFloat(unformatCurrency($(this).find("[name='qty[]']").val())) * parseFloat(unformatCurrency($(this).find("[name='hidItemWeight[]']").val())) ;
        });

        $("#mnv-form-cart").find("[name=totalWeight]").val((totalWeight/1000)).blur(); 

        var shippingInformation = thisObj.updateShippingInformation(); 

/*        var shippingPrice = 0,  insurance =0 ;
        if (shippingInformation){
           shippingPrice = parseInt(shippingInformation['price']);
           if(shippingInformation['insurance'])  
               shippingPrice += parseInt(shippingInformation['insurance']);
        }  
 
        thisObj.updateShipmentSummary(); */
    }

    this.recalculateCartTotal = function recalculateCartTotal(obj){  
    
        if (!obj) obj = $(".mnv-cart-table");

        var subtotalObj = obj.find(".mnv-cart-table-subtotal");

        subtotal = 0;
        obj.find(".transaction-row").each(function() {  
            subtotal +=  parseFloat(unformatCurrency($(this).find(".mnv-detail-subtotal").text())); 
        }); 

        subtotalObj.text(subtotal).formatCurrency({roundToDecimalPlace: DECIMAL });
        $(".mnv-cart-total-sales").text(subtotal).formatCurrency({roundToDecimalPlace: DECIMAL });

        updateCartStatus();
        
        // recount shippingCost and weight 
        //if (typeof recalculateShippingCost === "function") 
        thisObj.recalculateShippingCost(); 
		thisObj.updateCartTotalSummary();
		
		//thisObj.calculatePointNeeded();
		 
        // update available voucher
        //updateAvailableVoucher();

    }
    
    this.updateOrderQty = function updateOrderQty(obj){
        var transactionRow =  $(obj).closest(".transaction-row");
        var itemkey = transactionRow.find("[name=\'hidItemKey[]\']").val();
        var qty = parseInt(unformatCurrency($(obj).val()));
        var sellingPrice =  parseFloat(unformatCurrency(transactionRow.find(".mnv-price").text()));
  
        
        $.ajax({
            type: "POST",
            url: "/ajax-cart.php", 
            data : {action:'updateQty', itemkey: itemkey, qty: qty},
            success: function(data){    
                transactionRow.find(".mnv-detail-subtotal").text(qty * sellingPrice).formatCurrency({roundToDecimalPlace: DECIMAL });
                thisObj.recalculateCartTotal();
            } 
        });
    }
    
    this.updateQty = function updateQty(obj){
        var relQty = parseInt($(obj).attr("rel-ctr")) || 0;
        var objQty = $(obj).closest(".transaction-row").find("[name=\'qty[]\']");
        
        var currQty = parseInt(unformatCurrency(objQty.val())) || 0;
        currQty += relQty;
        if(currQty <= 0) currQty = 1;
         
        objQty.val(currQty).change().blur();
    }
        
    this.updateShipmentServices = function updateShipmentServices(){
        var arrServices = shipmentService[$("[name=selCourier]").val()] || [];  
        reInsertSelectBox($("[name=selShipmentService]"),arrServices, {"key" : "servicekey", "label" : "servicename"});  
          
        if(arrServices[0]['needlocation'] ==1)
            $(".dropoff-loc").show();
        else
            $(".dropoff-loc").hide();
    }

//	this.calculatePointNeeded = function calculatePointNeeded(){
//		var totalSales = parseFloat(unformatCurrency($(".mnv-cart-table-subtotal").text()));
//        var shipping = parseFloat(unformatCurrency($("[name=shippingCost]").val())) || 0;
//		var total = totalSales + shipping;
//		  
//		//hitung point maks yg bisa digunakan
//		var pointUsed = Math.ceil(total / thisObj.pointUnitValue);
//		
//		pointUsed = (pointUsed > thisObj.eligiblePoint ) ? thisObj.eligiblePoint  : pointUsed;
//		//pointUsed *= -1;
//		
//		$("[name=point]").val(pointUsed).blur().change();
//	}
	
//	this.loadPointInformation = function loadPointInformation(){
//		$.ajax({
//                type: "POST",
//				async: false,
//                url: "/ajax-member.php", 
//                data : {action:'get-point'},
//                success: function(data){  
//					if(!data) return;  
//                    data = JSON.parse(data);
//                    if(data.length == 0)   return;
//					
//					thisObj.eligiblePoint = parseInt(data.eligiblePoint);
//					thisObj.pointUnitValue = parseInt(data.pointUnitValue);
//					
//                } 
//        });
//	}
	
    this.loadOnReady = function loadOnReady(){   
         
        jQuery.ui.autocomplete.prototype._resizeMenu = function () {
          var ul = this.menu.element;
          ul.outerWidth(this.element.outerWidth());
        }

        $( ".mnv-cart-panel" ).accordion({
                heightStyle: "content",   
                beforeActivate: function(event, ui) {  
                      ui.newHeader.find(".summary").hide();
                } ,
                activate: function(event, ui) { 

                      var oldkey  = ui.oldHeader.attr("relkey");
                      switch(oldkey){
                        case 'cartList' : thisObj.updateCartListSummary(); 
                                            break;
                        case 'shipment' : thisObj.updateShipmentSummary(); 
                                            break;
                      }

                      ui.oldHeader.find(".summary").show(); 
                } 
        });

        $("[name=btnNext]").click(function(){ thisObj.openNextAccordionPanel($(".mnv-cart-panel"),'h2','+=200px');  })
        $("[name=selShipmentService],[name=recipientZipcode], [name=dummyuseInsurance]").change(function(){ thisObj.recalculateShippingCost();  })
        $("[name=selCourier]").change(function(){ thisObj.updateShipmentServices();  })
        
        $("[name='qty[]'").change(function(){   
              thisObj.updateOrderQty(this); 
        }); 
        
        $(".ctr-btn").click(function(){  
          thisObj.updateQty(this); 
        }); 
         
//		$("[name=point]").change(function(){    
//			
//			// calculate point value
//			var point = parseInt(unformatCurrency($(this).val())) || 0;
//			
//			if (point > thisObj.eligiblePoint ){
//				point = thisObj.eligiblePoint;
//				$(this).val(point).blur();
//			}
//			
//			var discValue = point * thisObj.pointUnitValue;
//				
//			$(".mnv-cart-point").text("-" +discValue).formatCurrency({roundToDecimalPlace: DECIMAL});
//			
//			// recount
//			thisObj.updateCartTotalSummary(); 
//        }); 
		
        $(".mnv-delete-cart-row, .mobile-delete-col").click(function() {
            
            $(this).closest(".transaction-row").remove();
            var itemkey = $(this).closest(".transaction-row").find("[name='hidItemKey[]']").val(); 
            
            $.ajax({
                type: "POST",
                url: "/ajax-cart.php", 
                data : {action:'delete', itemkey: itemkey},
                success: function(data){ 
                    thisObj.recalculateCartTotal();
                } 
            });

            renumbering($('.row-number'));
            
        });
		
	  	$(".choose-voucher").click(function() {
		  	var arrayPopup = new Array()
            arrayPopup['url'] = '/popup-voucher.php'; 
            loadOverlayScreen(arrayPopup); 
	  	});
		
          
		//thisObj.loadPointInformation();
		//thisObj.calculatePointNeeded(); // hitung dr php aj, kalo gk perlu pindahin formatcurrency
		
        $('#mnv-form-cart')
        .bootstrapValidator({
        feedbackIcons: {
        valid: 'glyphicon glyphicon-ok',
        invalid: 'glyphicon glyphicon-remove',
        validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
        recipientName: {
        validators: {
        notEmpty: {
        message:  errMsg.name[1]
        },
        }
        },
        recipientPhone: {
        validators: {
        notEmpty: {
        message: errMsg.phone[1]  
        },
        }
        },
        recipientEmail: {
        validators: {
        notEmpty: {
        message: errMsg.email[1]  
        },
        emailAddress: {
        message:errMsg.email[3]  
        }
        }
        },
        recipientAddress: {
        validators: {
        notEmpty: {
        message:errMsg.address[1] 
        },
        }
        },
        recipientZipcode: {
        validators: {
        notEmpty: {
        message:errMsg.zipcode[1] 
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
            
        var notifObj = $form.find(".notification-msg");
        notifObj.html(error).hide().fadeToggle("fast");
            
        if (!result[0].valid){ 
            setStatusColorNotification(notifObj,1); 
            $form.data('bootstrapValidator').resetForm();
            scrollToTopForm($form);
            grecaptcha.reset();
        }else{ 
            setStatusColorNotification(notifObj,2);
            location.href="/payment-process/sales-order/" + result[0]['data']['pkey'];
        }
        }, 'json');
        });
        $("#mnv-form-cart").bind('keyup keypress', function(e) {
        var code = e.keyCode || e.which;
        if (code == 13) {
        var tagName = $(e.target);
        if (!tagName.is("textarea")) {
        e.preventDefault();
        return false;
        }
        }
        });
        
    };
}