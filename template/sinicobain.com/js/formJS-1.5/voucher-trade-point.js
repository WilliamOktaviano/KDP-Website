function VoucherTradePoint(){   
    var thisObj = this;   
    //var errMsg = opt.errMsg;
    //var lang = opt.lang; 
    
	var eligiblePoint = 0;
	var pointUnitValue = 0;
	
    //DECIMAL = opt.decimal;
       
    this.loadOnReady = function loadOnReady(){   
 		  
		$(".voucher-item").click(function() {
		  	var arrayPopup = new Array()
            arrayPopup['url'] = '/popup-voucher-tnc/'+ $(this).attr("rel-voucherkey"); 
            loadOverlayScreen(arrayPopup); 
	  	});
		
		$("[name=btnSave]").click(function() {
		  //remove all qty value
		  $("[name=\'qty[]\']").val(0);
		  $(this).closest('.content-panel').find("[name=\'qty[]\']").val(1);
	  	});
		
        $('.mnv-form-voucher')
        .bootstrapValidator({
        feedbackIcons: {
        valid: 'glyphicon glyphicon-ok',
        invalid: 'glyphicon glyphicon-remove',
        validating: 'glyphicon glyphicon-refresh'
        },
        fields: { },
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
            //grecaptcha.reset();
        }else{ 
            setStatusColorNotification(notifObj,2);
            location.href="/voucher";
        }
        }, 'json');
        });
        
    };

}