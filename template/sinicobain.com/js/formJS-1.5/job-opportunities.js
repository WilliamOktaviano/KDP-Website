function JobOpportunities(opt){   
    var thisObj = this;    
     
         
    this.loadOnReady = function loadOnReady(){   
         $("[name='/about-company.php']").addClass("selected"); 
        
         $('.testimonial-list').slick({
          dots: false,
          autoplay:true,
          autoplaySpeed:1500,
          infinite: true,
          speed: 1000,
          slidesToShow: 1,
          slidesToScroll: 1, 
          nextArrow: '<div class="slick-arrow  next-arrow"><i class="fas fa-chevron-right"></i></div>',
          prevArrow: '<div class="slick-arrow  prev-arrow"><i class="fas fa-chevron-left"></i></div>', 
        });
    };

}