function Registration(opt){   
    var thisObj = this;   
    var errMsg = opt.errMsg;
    var lang = opt.lang;
    
    this.loadOnReady = function loadOnReady(){   

    $('#form-registration')
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
    userName: {
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
    remote: {
    message: errMsg.username[2],
    url: '/ajax-member.php',
    data: {
    type: 'check',
    fieldtype: 'username'
    },
    type: 'POST'
    }
    }
    },
    password: {
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
    passwordConfirmation: {
    validators: {
    notEmpty: {
    message: errMsg.passwordConfirmation[1]
    },
    stringLength: {
    min: 5,
    max: 30,
    message: errMsg.passwordConfirmation[2]
    },
    identical: {
    field: 'password',
    message: errMsg.password[3]
    }
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
    scrollToTopForm($form);
    grecaptcha.reset();
    }else{
    setStatusColorNotification(notifObj,2);
    alert(lang.registrationSuccessMessage);
    location.href="/";
    }
    }, 'json');
    });

    };

}