/* scrollToTop */ 
(function(window, document, $, undefined) {
    'use strict';

    // Constructor
    var ScrollToTop = function(options) {
        this.$doc = $('body');
        this.options = $.extend(ScrollToTop.defaults, options);

        var namespace = this.options.namespace;

        if (this.options.skin === null) {
            this.options.skin = 'default';
        }

        this.classes = {
            skin: namespace + '_' + this.options.skin,
            trigger: namespace,
            animating: namespace + '_animating',
            show: namespace + '_show'
        };

        this.disabled = false;
        this.useMobile = false;
        this.isShow = false;

        var self = this;
        $.extend(self, {
            init: function() {
                self.transition = self.transition();
                self.build();


                if (self.options.target) {
                    if (typeof self.options.target === 'number') {
                        self.target = self.options.target;
                    } else if (typeof self.options.target === 'string') {
                        self.target = Math.floor($(self.options.target).offset().top);
                    }
                } else {
                    self.target = 0;
                }

                self.$trigger.on('click.scrollToTop', function() {
                    self.$doc.trigger('ScrollToTop::jump');
                    return false;
                });

                // bind events
                self.$doc.on('ScrollToTop::jump', function() {
                    if (self.disabled) {
                        return;
                    }

                    self.checkMobile();

                    var speed, easing;

                    if (self.useMobile) {
                        speed = self.options.mobile.speed;
                        easing = self.options.mobile.easing;
                    } else {
                        speed = self.options.speed;
                        easing = self.options.easing;
                    }

                    self.$doc.addClass(self.classes.animating);


                    if (self.transition.supported) {
                        var pos = $(window).scrollTop();

                        self.$doc.css({
                            'margin-top': -pos + self.target + 'px'
                        });
                        $(window).scrollTop(self.target);

                        self.insertRule('.duration_' + speed + '{' + self.transition.prefix + 'transition-duration: ' + speed + 'ms;}');

                        self.$doc.addClass('easing_' + easing + ' duration_' + speed).css({
                            'margin-top': ''
                        }).one(self.transition.end, function() {
                            self.$doc.removeClass(self.classes.animating + ' easing_' + easing + ' duration_' + speed);
                        });
                    } else {
                        $('html, body').stop(true, false).animate({
                            scrollTop: self.target
                        }, speed, function() {
                            self.$doc.removeClass(self.classes.animating);
                        });
                        return;
                    }
                })
                    .on('ScrollToTop::show', function() {
                        if (self.isShow) {
                            return;
                        }
                        self.isShow = true;

                        self.$trigger.addClass(self.classes.show);
                    })
                    .on('ScrollToTop::hide', function() {
                        if (!self.isShow) {
                            return;
                        }
                        self.isShow = false;
                        self.$trigger.removeClass(self.classes.show);
                    })
                    .on('ScrollToTop::disable', function() {
                        self.disabled = true;
                        self.$doc.trigger('ScrollToTop::hide');
                    })
                    .on('ScrollToTop::enable', function() {
                        self.disabled = false;
                        self.toggle();
                    });

                $(window).on('scroll', self._throttle(function() {
                    if (self.disabled) {
                        return;
                    }

                    self.toggle();
                }, self.options.throttle));

                if (self.options.mobile) {
                    $(window).on('resize', self._throttle(function() {
                        if (self.disabled) {
                            return;
                        }

                        self.checkMobile();
                    }, self.options.throttle));
                }

                self.toggle();
            },
            checkMobile: function() {
                var width = $(window).width();

                if (width < self.options.mobile.width) {
                    self.useMobile = true;
                } else {
                    self.useMobile = false;
                }
            },
            build: function() {
                if (self.options.trigger) {
                    self.$trigger = $(self.options.trigger);
                } else {
                    self.$trigger = $('<a href="#" class="' + self.classes.trigger + ' ' + self.classes.skin + '">' + self.options.text + '</a>').appendTo($('body'));
                }

                self.insertRule('.' + self.classes.show + '{' + self.transition.prefix + 'animation-duration: ' + self.options.animationSpeed + 'ms;' + self.transition.prefix + 'animation-name: ' + self.options.namespace + '_' + self.options.animation + ';}');

                if (self.options.mobile) {
                    self.insertRule('@media (max-width: ' + self.options.mobile.width + 'px){.' + self.classes.show + '{' + self.transition.prefix + 'animation-duration: ' + self.options.mobile.animationSpeed + 'ms !important;' + self.transition.prefix + 'animation-name: ' + self.options.namespace + '_' + self.options.mobile.animation + '  !important;}}');
                }
            },
            can: function() {
                var distance;
                if (self.useMobile) {
                    distance = self.options.mobile.distance;
                } else {
                    distance = self.options.distance;
                }
                if ($(window).scrollTop() > distance) {
                    return true;
                } else {
                    return false;
                }
            },
            toggle: function() {
                if (self.can()) {
                    self.$doc.trigger('ScrollToTop::show');
                } else {
                    self.$doc.trigger('ScrollToTop::hide');
                }
            },

            transition: function() {
                var e,
                    end,
                    prefix = '',
                    supported = false,
                    el = document.createElement("fakeelement"),
                    transitions = {
                        "WebkitTransition": "webkitTransitionEnd",
                        "MozTransition": "transitionend",
                        "OTransition": "oTransitionend",
                        "transition": "transitionend"
                    };
                for (e in transitions) {
                    if (el.style[e] !== undefined) {
                        end = transitions[e];
                        supported = true;
                        break;
                    }
                }
                if (/(WebKit)/i.test(window.navigator.userAgent)) {
                    prefix = '-webkit-';
                }
                return {
                    prefix: prefix,
                    end: end,
                    supported: supported
                };
            },
            insertRule: function(rule) {
                if (self.rules && self.rules[rule]) {
                    return;
                } else if (self.rules === undefined) {
                    self.rules = {};
                } else {
                    self.rules[rule] = true;
                }

                if (document.styleSheets && document.styleSheets.length) {
                    document.styleSheets[0].insertRule(rule, 0);
                } else {
                    var style = document.createElement('style');
                    style.innerHTML = rule;
                    document.head.appendChild(style);
                }
            },
            /**
             * _throttle
             * @description Borrowed from Underscore.js
             */
            _throttle: function(func, wait) {
                var _now = Date.now || function() {
                    return new Date().getTime();
                };
                var context, args, result;
                var timeout = null;
                var previous = 0;
                var later = function() {
                    previous = _now();
                    timeout = null;
                    result = func.apply(context, args);
                    context = args = null;
                };
                return function() {
                    var now = _now();
                    var remaining = wait - (now - previous);
                    context = this;
                    args = arguments;
                    if (remaining <= 0) {
                        clearTimeout(timeout);
                        timeout = null;
                        previous = now;
                        result = func.apply(context, args);
                        context = args = null;
                    } else if (!timeout) {
                        timeout = setTimeout(later, remaining);
                    }
                    return result;
                };
            }
        });

        this.init();
    };

    // Default options
    ScrollToTop.defaults = {
        distance: 200,
        speed: 1000,
        easing: 'linear',
        animation: 'fade', // fade, slide, none
        animationSpeed: 500,

        mobile: {
            width: 768,
            distance: 100,
            speed: 1000,
            easing: 'easeInOutElastic',
            animation: 'slide',
            animationSpeed: 200
        },

        trigger: null, // Set a custom triggering element. Can be an HTML string or jQuery object
        target: null, // Set a custom target element for scrolling to. Can be element or number
        text: 'Scroll To Top', // Text for element, can contain HTML

        skin: null,
        throttle: 250,

        namespace: 'scrollToTop'
    };

    ScrollToTop.prototype = {
        constructor: ScrollToTop,
        jump: function() {
            this.$doc.trigger('ScrollToTop::jump');
        },
        disable: function() {
            this.$doc.trigger('ScrollToTop::disable');
        },
        enable: function() {
            this.$doc.trigger('ScrollToTop::enable');
        },
        destroy: function() {
            this.$trigger.remove();
            this.$doc.data('ScrollToTop', null);
            this.$doc.off('ScrollToTop::enable')
                .off('ScrollToTop::disable')
                .off('ScrollToTop::jump')
                .off('ScrollToTop::show')
                .off('ScrollToTop::hide');
        }
    };

    $.fn.scrollToTop = function(options) {
        if (typeof options === 'string') {
            var method = options;
            var method_arguments = arguments.length > 1 ? Array.prototype.slice.call(arguments, 1) : undefined;

            return this.each(function() {
                var api = $.data(this, 'scrollToTop');

                if (api && typeof api[method] === 'function') {
                    api[method].apply(api, method_arguments);
                }
            });
        } else {
            return this.each(function() {
                var api = $.data(this, 'scrollToTop');
                if (!api) {
                    api = new ScrollToTop(options);
                    $.data(this, 'scrollToTop', api);
                }
            });
        }
    };
}(window, document, jQuery));


/* columnConform */ 
function columnConform(obj) { 
    
	 var topPosition = 0;
	 var currentRowStart = -1;
	 var currentTallest = 0;
	 var rowDivs = new Array();

	 // reset height when resize
	 $(obj).each(function() { 
		$(this).height("auto"); 
	 });

		
	 $(obj).each(function(index) {
		  
		 topPosition =  $(this).offset().top;  
		 
		 // start
		 if(currentRowStart == -1) { currentRowStart = topPosition; currentTallest =  $(this).height();}
		 
		 // new row
		 if (currentRowStart != topPosition) {
		
			for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) 
				rowDivs[currentDiv].height(currentTallest);    
			 
		
			//reset
			currentTallest =  $(this).height();
			rowDivs.length = 0; // empty the array
			
			// get the new position after re-arrange
			topPosition =  $(this).offset().top;
			currentRowStart = topPosition;
			
		 } 
		 
	
		 if (currentTallest < $(this).height())
			currentTallest = $(this).height();
 
		 rowDivs.push($(this)); 
		 
	 });
		
	 // do the last row
	 for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) 
		rowDivs[currentDiv].height(currentTallest);     
  

}
 

jQuery(document).ready(function(){ 
           
        $(".input-date" ).datepicker({ showButtonPanel: true, currentText: 'Now', dateFormat:'dd / mm / yy', changeMonth: true,  changeYear: true}); 
        removeEventListenerByClass('prevent-form-submit', 'submit', preventSubmitBeforeJSLoaded); 
    
        onLoadScript(); 
 
        $(".menu-item.dropdown").hover(
              function() {
                $( this ).find("div").first().fadeIn("fast");  
              }, function() {
                $( this ).find("div").first().fadeOut("fast");  
              }
        ); 
    
        $("#index .article .content-panel").hover(
              function() {
                $( this ).find(".title").slideDown("fast");  
              }, function() {
                $( this ).find(".title").slideUp("fast");  
              }
       );  
     
        $("#index .article .content-panel").click(function(event){
            window.open($(this).find("a").attr('href'),"_self");
        });

    
	    $(".btn-quick-search").click(function() {     
			quickSearch( $(this).closest(".search-bar").find('[name=quickSearch]')  );
		});
    
		$("[name=quickSearch]").keyup(function(event) {  
			 if ( event.which == 13 ) { 
                 quickSearch($(this)); 
			  }
        });
		
        $('body').scrollToTop({
            distance: 200,
			speed: 1000,
			easing: 'linear',
			animation: 'fade', // fade, slide, none
			animationSpeed: 500,
			  
			trigger: null, // Set a custom triggering element. Can be an HTML string or jQuery object
			target: null, // Set a custom target element for <a href="http://www.jqueryscript.net/tags.php?/Scroll/">scrolling</a> to. Can be element or number
			text: '<i class="back-to-top fas fa-arrow-circle-up"></i>', // Text for element, can contain HTML
			 
			skin: null,
			throttle: 250,
			 
			namespace: 'scrollToTop'
		});  
		   

        $(".inputnumber").each(function() { if($(this).val() == "") $(this).val(0); }).bind( "blur", function(event) { inputNumberOnBlur($(this)); });
        $(".inputdecimal").each(function() {  if($(this).val() == "") $(this).val(0); }).bind( "blur", function(event) { inputNumberOnBlur($(this),2); });
        $(".inputnumber, .inputdecimal, .input-integer").bind("focus",function(event) { inputNumberOnFocus($(this)); } )
  
//        
    
        // ======= custom per project
    
        /*$(window).bind("load resize", function() {     
           $(".dropdown").each(function() {
               var leftPos = $(this).offset().left; 
               var submenu = $(this).find(".single-level");
               var submenuWidth = submenu.outerWidth(true);
               var winWidth = $( window ).width();

               if (leftPos + submenuWidth >= winWidth)  
                    submenu.css("left", (submenuWidth-$(this).outerWidth(true) ) * -1 )  ;
                else
                    submenu.css("left", 0); 
            });   
            
            columnConform('.auto-height');
        });
        
				 
	    
        */ 
    
        updateCartStatus(); 
    
        $(window).bind("load resize", function() {      
            columnConform('.auto-height');
        });
    
        $(window).resize(); 
    
    
});   


function updateCartStatus(){  
    
		$.ajax({
			type: "POST",
			url: "/ajax-cart.php",
			data: "action=cartStatus",   
			success: function(data){  
                    if (data != ""){ 
                        var temp =  JSON.parse(data);  
                        if (temp.totalqty <= 0 )
                            $(".mnv-cart-qty").hide();
                        else
                            $(".mnv-cart-qty").show();
                         
                        $(".mnv-cart-qty").text(temp.totalqty); 
                    }
                        
			} 
		}); 
}

  
function quickSearch(obj){  
    
     var searchkey =  encodeURIComponent(obj.val()) || ''; 
     
     var selQuickSearch = obj.closest(".search-box").find('[name=selQuickSearchCategory]');
     var quickSearchCategory = encodeURIComponent(selQuickSearch.val()) || 0; 
      
     $('[name=\'parameter-form\']').attr("action","/products/" +'cat='+quickSearchCategory+'&key='+encodeURIComponent(searchkey)).submit();
}

function scrollToTopForm($form){  
    var formTop = $form.offset().top;  
    $('html, body').animate({scrollTop:formTop - 150},500);
}
function updateChkBoxOnClick(obj){   
    var chkValue = $(obj).prop("checked") ? 1 : 0;
    $(obj).val(chkValue); 
    $(obj).next().val(chkValue); 
} 

function updateChkBoxOnChange(obj){   
    
    var checked = "",chkValue = 0;
    
    if($(obj).val() == 1){
        checked = "checked";
        chkValue = 1;
    } 
    
    $(obj).prev().prop("checked",checked).change(); // dont use click !
    $(obj).prev().val(chkValue);
}

function clearAutoCompleteInput(obj,hidKeyName,revalidateField){    
	$(obj).val("");    
    
    if (jQuery.type(hidKeyName) == 'string')
	   $(obj).closest('form').find("[name='"+hidKeyName+"']").first().val(""); 
    else
        hidKeyName.val("");
    
    if (revalidateField == undefined)
        revalidateField = true;
 
    if (revalidateField)
	   $(obj).closest('form').bootstrapValidator('revalidateField', $(obj).attr("name"));
}


function reInsertSelectBox(selectbox,selectOpt, opt){
     
    // update combobox services
    var newOptions = {};
    
    // add rel
    
    // harus dibedain kalo tipena bukan "key & label"
    if(opt != undefined && opt['key']){  
        
        for(i=0;i<selectOpt.length;i++) { 
            var attrRel = {};
            if(opt.rel != undefined) 
               $.each(opt.rel, function(index, item) {   attrRel[index] = selectOpt[i][item]; })

            newOptions[selectOpt[i][opt.key]] =  {"label" : selectOpt[i][opt.label],"rel" : attrRel };        
        }
    }else{  
         $.each(selectOpt, function(key, item) {  newOptions[key] = item; });   
    }
     
    var lastSelectBox;
    selectbox.each(function(){ lastSelectBox = $(this); updateSelectBox($(this),newOptions); }); 
     
    // gk boleh yg terakhir, karena terkadang terakhir itu ad di row-template
    selectbox.first().find('option:eq(0)').prop('selected', true).change(); 
    
}


function updateSelectBox(select,newOptions){
    var options = (select.prop) ? select.prop('options') : select.attr('options');  
    
    $('option', select).remove(); 
   
    $.each(newOptions, function(opIndex, opItem) {  
        options[options.length] = new Option(opItem.label, opIndex); 
        
         if(opItem.rel != undefined ){ 
            $.each(opItem.rel, function(relIndex, relItem) { 
                select.find('option:eq('+(options.length-1)+')').attr(relIndex,relItem);
            });  
        } 
    });
 
}

function renumbering(obj){
    var ctr = 1;
    obj.each(function() {
        $(this).text(ctr++ + '.');
    });
}
