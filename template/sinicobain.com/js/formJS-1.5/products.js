function Products(opt){   
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
                scrollTop: $(".product-page").offset().top - 50
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
        
        var basicUrl = '/products'; // biar gk aneh kalo search category, tp aad kategori bawaan
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

      var priceFrom = parseInt (unformatCurrency($("[name=priceFrom]").val()));
      var priceTo = parseInt (unformatCurrency($("[name=priceTo]").val()));

      if (priceTo < priceFrom ) priceFrom = 0;  

      if (priceTo != 0) paramUrl += '&pricerange='+ priceFrom +','+ priceTo; 
        
      var orderBy = $("[name=selOrderBy]").val();
      var orderType = $("[name=selOrderType]").val();

      var categorykey = '';   
      $('[name=\'dummychkCategory[]\']:checked').each(function(index, value) {    
            if(index != 0) categorykey += ','; 
            categorykey += $(this).attr("attr-rel") ; 
      }); 
          
      var brandkey = '';   
      $('[name=\'dummychkBrand[]\']:checked').each(function(index, value) {    
            if(index != 0) brandkey += ','; 
            brandkey += $(this).attr("attr-rel"); 
      }); 
    
      paramUrl += '&orderby='+ orderBy +'&ordertype='+orderType;
    
      if(searchkey != '')  paramUrl += '&key='+searchkey;
      if(categorykey != '')  paramUrl += '&categorykey='+categorykey;
      if(brandkey != '')  paramUrl += '&brandkey='+brandkey;
         
        
      $('[name=parameter-form]').attr("action",basicUrl+paramUrl);
      $('[name=parameter-form]').submit();
    }

     /*function updateSelectedFilter(){

        {% for keyvalue in filterkey %} 
            $('.filter-panel').find('.'+{{ keyvalue }}).addClass("option-item-active")
            $('.active-filter').append('<li>'+$('.filter-panel').find('.'+{{ keyvalue }}).text()+'</li>');
        {% endfor %}

        {% if hidHideNotAvailable == 1 %}
            $('.filter-panel .hidHideNotAvailable').addClass("option-item-active") 
            $('.active-filter').append('<li>'+$('.filter-panel .hidHideNotAvailable').text()+'</li>');
        {% endif %}	 


        {% if priceFrom > 0 %}
            $('.filter-panel [name=priceFrom]').val('{{ priceFrom }}').formatCurrency({roundToDecimalPlace: 0 });
        {% endif %}	 

        {% if priceTo > 0 %}
            $('.filter-panel [name=priceTo]').val('{{ priceTo }}').formatCurrency({roundToDecimalPlace: 0 }); 
            $('.active-filter').append('<li>{{ LANG.price }}: {{ priceFrom|number_format }} - {{ priceTo|number_format }}</li>');
        {% endif %}	  

    } */
         
         
    this.loadOnReady = function loadOnReady(){   
        
      $("[name=btnUpdateFilter]").click(function(){  
          thisObj.updateFilter(); 
      }); 
        
     $(".product-icon-filter").click(function(){  
          thisObj.showFilter(); 
      });  
        
     $(".reset-btn").click(function(){  
          thisObj.resetFilter(this); 
      });  
         
        
    };

}