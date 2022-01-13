function Career(opt){   
    var thisObj = this;    
    var selectedCategoryKey = opt['selectedCategoryKey'];
    var selectedCategoryName = opt['selectedCategoryName'];
     
    this.resetFilter = function resetFilter(obj){
        var box = $(obj).closest(".filter-box");
        box.find(".inputnumber").val(0);
        box.find("[type=checkbox]:checked").click();
        
    }
    
    this.showFilter = function showFilter(){
        var display = $(".filter-col").css("display");
        if(display == "none"){  
            $('html, body').animate({
                scrollTop: $(".career-page").offset().top - 50
            }, 1000);
        }
        
        $(".filter-col").slideToggle();
        
    }
    
    this.updateFilter = function updateFilter(){
        $("[name=btnUpdateFilter]").attr("disabled",true);
        disableButton($("[name=btnUpdateFilter]"));
        
        varParam = [];
        //var basicUrl = '/products/'+selectedCategoryKey; // perlu categorykey nanti
        var paramUrl = '';

        //if (selectedCategoryKey != 0 ) 
        //    basicUrl +=  '/' + selectedCategoryName; 
        
        var basicUrl = '/careers-list'; // biar gk aneh kalo search category, tp aad kategori bawaan
        basicUrl += '/page=0';

      /*$('.filter-panel .option-item-active').each(function() {   
        if ($(this).attr("val") != undefined) { 
             varParam.push($(this).attr("val"));
        }
      }); 

      if ($('.filter-panel .hidHideNotAvailable').hasClass("option-item-active"))
         paramUrl += '&hidHideNotAvailable=1';

      if (varParam.length > 0 ){
           paramUrl += '&filterkey='+varParam;
      }*/
        
      var searchkey = $("[name=quickSearch]").val();

      var categorykey = '';   
      $('[name=\'dummychkCategory[]\']:checked').each(function(index, value) {    
            if(index != 0) categorykey += ','; 
            categorykey += $(this).attr("attr-rel") ; 
      }); 
          
      var jobfieldkey = '';   
      $('[name=\'dummychkJobField[]\']:checked').each(function(index, value) {    
            if(index != 0) jobfieldkey += ','; 
            jobfieldkey += $(this).attr("attr-rel"); 
      }); 
        
       var citykey = '';   
      $('[name=\'dummychkCity[]\']:checked').each(function(index, value) {    
            if(index != 0) citykey += ','; 
            citykey += $(this).attr("attr-rel"); 
      }); 
    
    
      if(searchkey != '')  paramUrl += '&key='+searchkey;
      if(categorykey != '')  paramUrl += '&categorykey='+categorykey;
      if(jobfieldkey != '')  paramUrl += '&jobfieldkey='+jobfieldkey;
      if(citykey != '')  paramUrl += '&citykey='+citykey;
         
        
      $('[name=parameter-form]').attr("action",basicUrl+paramUrl);
      $('[name=parameter-form]').submit();
    }

         
         
    this.loadOnReady = function loadOnReady(){   
        
      $("[name=btnUpdateFilter]").click(function(){  
          thisObj.updateFilter(); 
      }); 
        
     $(".career-icon-filter").click(function(){  
          thisObj.showFilter(); 
      });  
        
     $(".reset-btn").click(function(){  
          thisObj.resetFilter(this); 
      });  
         
        
    };

}