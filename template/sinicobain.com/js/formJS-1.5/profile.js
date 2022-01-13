function Profile(opt){   
    var thisObj = this;   
    var errMsg = opt.errMsg;
    var lang = opt.lang;
    
    this.loadOnReady = function loadOnReady(){   
        $('#form-editprofile')
        .bootstrapValidator({
        feedbackIcons: {
        valid: 'glyphicon glyphicon-ok',
        invalid: 'glyphicon glyphicon-remove',
        validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
        currentPassword: {
        validators: {
        notEmpty: {
        message: errMsg.password[1]
        },
        stringLength: {
        min: 5,
        max: 30,
        message: errMsg.password[2]
        },
        remote: {
        message:  errMsg.username[5],
        url: '/ajax-member.php',
        data: {
        type: 'check',
        fieldtype: 'checkPassword'
        },
        type: 'POST'
        }
        }
        },
        password: {
        validators: {
        stringLength: {
        min: 5,
        max: 30,
        message: errMsg.password[2]
        },
        identical: {
        field: 'passwordConfirmation',
        message: errMsg.password[3]
        }
        }
        },
        passwordConfirmation: {
        validators: {
        stringLength: {
        min: 5,
        max: 30,
        message: errMsg.password[2]
        },
        identical: {
        field: 'password',
        message: errMsg.password[3]
        }
        }
        },
        emergencyContactName: {
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
        remote: {
        message: errMsg.email[2],
        url: '/ajax-member.php',
        data: {
        type: 'check',
        edit : 1,
        fieldtype: 'email'
        },
        type: 'POST'
        }
        }
        },
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
        alert(result[0].message);
        location.href="/profile";
        }
        }, 'json');
        }); 
    };

}