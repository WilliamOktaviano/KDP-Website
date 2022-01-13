function Articles(opt){   
    var thisObj = this;     
 
    
    this.updateWidth = function updateWidth(){
        var totalWidth = 0;
        
        $(".category ul li").each(function() { 
            totalWidth += $(this).width() + 20; 
        });  
        
        $(".category ul").css("width",totalWidth+"px"); 
    }
    
    this.loadOnReady = function loadOnReady(){   
        thisObj.updateWidth();   
    };

}