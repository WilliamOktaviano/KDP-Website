function UpgradeMembership(opt){   
    var thisObj = this;   
     
	
	$("[name=btnSave], [name=btnTradePoint]").click(function(){
	 	$("[name=hidUsePoint]").val($(this).attr("rel-use-point"));
	})
	
    this.loadOnReady = function loadOnReady(){   
		
        $('#mnv-upgrade-membership')
        .bootstrapValidator({
        feedbackIcons: {
        valid: 'glyphicon glyphicon-ok',
        invalid: 'glyphicon glyphicon-remove',
        validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
         
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
			var usePoint = $("[name=hidUsePoint]").val();
			
            location.href=(usePoint == 1) ? "/upgrade-membership-success.html" : "/payment-process/membership/" + result[0]['data']['pkey'];
        }
        }, 'json');
        });
        $("#mnv-upgrade-membership").bind('keyup keypress', function(e) {
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