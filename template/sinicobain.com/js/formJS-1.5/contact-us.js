function ContactUs(opt){   
    var thisObj = this; 
    var errMsg = opt.errMsg;
    var lang = opt.lang;

    this.loadOnReady = function loadOnReady(){  
        
        $('#form-contactus')
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
					
					phone: { 
						validators: {
							notEmpty: {
								message: errMsg.phone[1]
							}, 
						}
					},
					
					email: { 
						 validators: {
							notEmpty: {
								message: errMsg.email[1]
							},  
							emailAddress: {
								message:  errMsg.email[3]
							}
						}
					},
				 	message: { 
						validators: {
							notEmpty: {
								message: errMsg.message[1]
							}, 
						}
					},
					 subject: { 
						validators: {
							notEmpty: {
								message: errMsg.subject[1]
							}, 
						}
					}, 
					
				}
			})
			.on('success.form.bv', function(e) {
				
				$("[name=btnSave]").prop('disabled', true).addClass("btn-primary-loading");
				
				// Prevent form submission
				e.preventDefault(); 
				// Get the form instance
				var $form = $(e.target);
	
				// Get the BootstrapValidator instance
				var bv = $form.data('bootstrapValidator');
	
				// Use Ajax to submit form data
				$.post($form.attr('action'), $form.serialize(), function(result) {
					$("[name=btnSave]").removeClass("btn-primary-loading");
					
					var error = "";
					for (i=0;i<result.length;i++)
						error = error + "<li>" + result[i].message + "</li>";
						
					if (error != "")
						error = "<ul class=\"message-dialog-ul\">" + error + "</ul>";
						
					if (!result[0].valid){
						$(".notification-msg").html(error).hide().fadeToggle("fast");
						$(".notification-msg").removeClass("bg-green-avocado").addClass("bg-red-cardinal");
						$form.data('bootstrapValidator').resetForm();
                        scrollToTopForm($form);s
						grecaptcha.reset(); 
					}else{
						alert(lang.contactUsSuccessful);
						location.href="/";
					}
					
				}, 'json');
			}); 
         
    };

}