function Login(opt){   
    var thisObj = this; 
    var errMsg = opt.errMsg;
    
    this.loadOnReady = function loadOnReady(){  

        $('#form-login')
        .bootstrapValidator({
        feedbackIcons: {
        valid: 'glyphicon glyphicon-ok',
        invalid: 'glyphicon glyphicon-remove',
        validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
        loginID: {
        validators: {
        notEmpty: {
        message: errMsg.username[1]
        },
        stringLength: {
        min: 5,
        max: 30,
        message: errMsg.username[3]
        },
        regexp: {
        regexp: /^[a-zA-Z0-9_\.]+$/,
        message: errMsg.username[4]
        },
        }
        },
        loginPassword: {
        validators: {
        notEmpty: {
        message: errMsg.password[1]
        },
        stringLength: {
        min: 5,
        max: 30,
        message: errMsg.password[2]
        },
        }
        },
        }
        })
        .on('success.form.bv', function(e) {

            // Prevent form submission
            e.preventDefault();
            // Get the form instance
            var $form = $(e.target);
            // Get the BootstrapValidator instance
            var bv = $form.data('bootstrapValidator');
            // Use Ajax to submit form data
 
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
                }else{
                    setStatusColorNotification(notifObj,2);
                    location.href="/";
                }
            }, 'json');
        });

    };

}