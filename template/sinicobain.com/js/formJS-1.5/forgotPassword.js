function ForgotPassword(opt){   
    var thisObj = this;   
    var errMsg = opt.errMsg;
    var lang = opt.lang;
    
    this.loadOnReady = function loadOnReady(){   
        $('#form-forgot-password')
        .bootstrapValidator({
        feedbackIcons: {
        valid: 'glyphicon glyphicon-ok',
        invalid: 'glyphicon glyphicon-remove',
        validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
        email: {
        validators: {
        notEmpty: {
        message: errMsg.email[1]
        },
        emailAddress: {
        message: errMsg.email[3]
        },
        remote: {
        message: errMsg.email[4],
        url: '/ajax-member.php',
        data: {
        type: 'check',
        fieldtype: 'email-negation'
        },
        type: 'POST'
        }
        }
        },
        }
        })
        .on('success.form.bv', function(e) {   
        e.preventDefault(); 
        var $form = $(e.target); 
        var bv = $form.data('bootstrapValidator');
            
        disableButton($form.find("[name=btnSave]"));
            
        $.post($form.attr('action'), $form.serialize(), function(result) {
        disableButton($form.find("[name=btnSave]"),false);
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
        alert(lang.resetPasswordSuccessMessage);
        location.href="/";
        }
        }, 'json');
        });
    };

}