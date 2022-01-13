function ProductsDetail(opt){   
    var thisObj = this;    
    var templateJSPath = opt['templateJSPath'];
    var lang = opt.lang;
    
    this.updateQty = function updateQty(obj){
        var relQty = parseInt($(obj).attr("rel-ctr")) || 0;
        
        var currQty = parseInt(unformatCurrency($("[name=\'orderQty[]\']").val())) || 0;
        currQty += relQty;
        if(currQty <= 0) currQty = 1;
         
        $("[name=\'orderQty[]\']").val(currQty).blur();
    }
    
    this.loadOnReady = function loadOnReady(){   
        
        $(".ctr-btn").click(function(){  
          thisObj.updateQty(this); 
        }); 
        
        
        $(".new-offer").click(function(){  
            $.ajax({
                type: "POST",
                async: false,
                url: "/ajax-simulator.php", 
                data : $("#order-form").serialize() + "&action=addToCart",
                success: function(data){    
                    alert(lang.offerSimulatorUpdated);
                } 
            });
        }); 
        
        
        
            /*$(".item-variant-link .panel-data").click(function (){ 
                $(".item-variant-link .panel-data").removeClass("selected");
                $(this).addClass("selected");	
                $(".price .value").html($(this).attr("relprice"));
                $("[name='hiditemkey[]']").val($(this).attr("relpkey"));
            });

            $(".item-variant-link:first .panel-data").click(); */

            $('#order-form')
                        .bootstrapValidator({ 
                            feedbackIcons: {
                                valid: 'glyphicon glyphicon-ok',
                                invalid: 'glyphicon glyphicon-remove',
                                validating: 'glyphicon glyphicon-refresh'
                        },
                        fields: {
                            orderQty: {
                                validators: { 
                                    greaterThan: {
                                        value: 0,
                                        inclusive: false,
                                        separator: ',', 
                                        message: '{{ ERRORMSG.qty[1] }}'
                                    }
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
                        $.post($form.attr('action'), $form.serialize(), function(result) {
                            // alert("Your message has been sent and we will be in touch with you as soon as possible.");
                             updateCartStatus();
                             location.href="/cart";
                        }, 'json');
                    });

          /*  $(".button-notify").click(function() {   
                {% if loginName is empty %}	
                    alert('{{ LANG.loginRequired }}');	
                {% else %}
                    notifyme('{{ rsItem[0].pkey }}');
                {% endif %} 

            });   
   */
        
    };

}