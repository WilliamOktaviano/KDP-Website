function Recruitment(opt){   
    var thisObj = this;   
    var errMsg = opt.errMsg;
    var lang = opt.lang;
    
    this.loadOnReady = function loadOnReady(){   
                     
        createFileUploader(opt.fileUploaderTarget,opt.fileFolder,"","",true);
        
        $('#form-recruitment')
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
        email: {
        validators: {
        notEmpty: {
        message: errMsg.email[1]
        },
        emailAddress: {
        message: errMsg.email[3]
        },
        }
        },
        phone: {
        validators: {
        notEmpty: {
        message: errMsg.phone[1]
        },
        }
        },
        address: {
        validators: {
        notEmpty: {
        message: errMsg.address[1]
        },
        }
        },
        }    
        })
        .on('success.form.bv', function(e) {
        
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
            
        disableButton($form.find("[name=btnSave]"));
            
        // Use Ajax to submit form data
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
        $(".form-panel").html("<div class=\"label-color\"><br>Data telah berhasil dikirim.<br>Tim kami akan segera menghubungi Anda.</div>");
        scrollToTopForm($form);            
        }
        }, 'json');
        }); 
    };

}