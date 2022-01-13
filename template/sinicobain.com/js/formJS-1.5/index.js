function Index(opt){   
    var thisObj = this;   
    var itemListLoaded = false;
    var errMsg = opt.errMsg;
    var lang = opt.lang;
    
    this.loadOnReady = function loadOnReady(){   
        initGalleria(opt['banner'],opt['templateJSPath']);
        
        $("[name=btnSubscribeNewsletter]").click(function(){  
            var newletterEmail = $.trim($("[name=txtNewsletterEmail]").val());
            if(newletterEmail.length==""){ 
                alert(errMsg.email[1]);
                return;   
            }
            
            $.ajax({ 
              url: 'ajax-subscribe.php',  
              method : 'POST',
              data: 'action=add&email='+newletterEmail,  
              success: function(data){   
                  alert(lang.newsletterSubscribeSuccess);
               } 
            });
            
        }); 
        
        $('.item-list').slick({
          dots: false,
          autoplay:true,
          autoplaySpeed:3000,
          infinite: true,
          speed: 1000,
          slidesToShow: 6,
          slidesToScroll: 6, 
          nextArrow: '<div class="slick-arrow  next-arrow"><i class="fas fa-chevron-right"></i></div>',
          prevArrow: '<div class="slick-arrow  prev-arrow"><i class="fas fa-chevron-left"></i></div>',
          responsive: [
                {
                  breakpoint: 1050,
                  settings: {
                    slidesToShow: 5,
                    slidesToScroll: 5, 
                  }
                },
                {
                  breakpoint: 900,
                  settings: {
                    slidesToShow: 4,
                    slidesToScroll: 4, 
                  }
                },
                {
                  breakpoint: 700,
                  settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3,
                  }
                },
                {
                  breakpoint: 600,
                  settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                  }
                },
                {
                  breakpoint: 415,
                  settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                  }
                } 
              
          ]
        });
        
        /*$('.item-list').on('afterChange', function(event, slick, currentSlide){
            if(!itemListLoaded){ 
                $(".loading").fadeOut();
                $(".item-list").css({opacity: 0.0, visibility: "visible"}).animate({opacity: 1.0});
                itemListLoaded = true;
            }
        });*/

        $('.portfolio-list').slick({
          dots: false,
          autoplay:true,
          autoplaySpeed:1500,
          infinite: true,
          speed: 1000,
          slidesToShow: 8,
          slidesToScroll: 8, 
          nextArrow: '<div class="slick-arrow  next-arrow"><i class="fas fa-chevron-right"></i></div>',
          prevArrow: '<div class="slick-arrow  prev-arrow"><i class="fas fa-chevron-left"></i></div>',
            responsive: [
                {
                  breakpoint: 1000,
                  settings: {
                    slidesToShow: 6,
                    slidesToScroll: 6, 
                  }
                }, {
                  breakpoint: 900,
                  settings: {
                    slidesToShow: 5,
                    slidesToScroll: 5, 
                  }
                },
                {
                  breakpoint: 700,
                  settings: {
                    slidesToShow: 4,
                    slidesToScroll: 4
                  }
                },
                {
                  breakpoint: 500,
                  settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3
                  }
                } 
          ]
        });

        /*$('.client-list').slick({
          dots: false,
          autoplay:true,
          autoplaySpeed:1500,
          infinite: true,
          speed: 1000,
          slidesToShow: 6,
          slidesToScroll: 6, 
          nextArrow: '<div class="slick-arrow  next-arrow"><i class="fas fa-chevron-right"></i></div>',
          prevArrow: '<div class="slick-arrow  prev-arrow"><i class="fas fa-chevron-left"></i></div>',
            responsive: [
                {
                  breakpoint: 1000,
                  settings: {
                    slidesToShow: 5,
                    slidesToScroll: 5, 
                  }
                },
                {
                  breakpoint: 700,
                  settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3
                  }
                },
                {
                  breakpoint: 450,
                  settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                  }
                } 
          ]
        }); 
*/
    };

    
function initGalleria(banner,templateJSPath){
    var isDesktop = true; 
    
    var desktopdata = banner['slideShow'];
    var mobiledata = banner['mobileSlideShow'];
     
    var mobileWidth = 600;
    var initialdata = desktopdata; 
    var galleriaHeight = 400; //0.4;

     if ($( window ).width()  <= mobileWidth){
        initialdata = mobiledata;
        isDesktop = !isDesktop;
        galleriaHeight = $( window ).width() * 0.6;
     }

    Galleria.loadTheme(templateJSPath + 'galleriathemes/twelve-1.6/galleria.twelve.js');  

    Galleria.run('#banner-galleria', { 
        dataSource: initialdata,
        height:galleriaHeight, 
        popupLinks:true,
        transition: 'slide',
        autoplay: 3000 ,  
        pauseOnInteraction: false,
        _showFullscreen: false,
        _showPopout: false, 
        extend: function() { 
                        var gallery = this; 
                        this.$('stage').hover(function() {
                            gallery.pause();
                        }, function() {
                            gallery.play();
                        });
                    } 
    }); 

    Galleria.ready(function(){
        var gallery = this;

        $( window ).resize(function() {    
            if ($( window ).width() <= mobileWidth && isDesktop == true){
                isDesktop = !isDesktop;	 
                gallery.load(mobiledata); 
            }else if ($( window ).width() > mobileWidth && isDesktop == false){ 
                isDesktop = !isDesktop;	 
               $('#banner-galleria  .galleria-container').css("min-height","");
                gallery.load(desktopdata);
            } 
            resizeGalleriaScaleRatio() 
        }); 

    }); 

    Galleria.on('data',function(){
        var gallery = this;
        window.setTimeout(function(){
            gallery.lazyLoadChunks(3);
        },10);
    });

    function resizeGalleriaScaleRatio(){
           if ($( window ).width() <= mobileWidth){ 
             var width =  $( window ).width() * 0.7;
             $('#banner-galleria  .galleria-container').css("min-height",width +"px");
           }
    }  
}
    
}