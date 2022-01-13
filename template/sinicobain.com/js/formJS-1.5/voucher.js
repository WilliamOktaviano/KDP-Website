function Voucher(){   
    var thisObj = this;   
    
    this.loadOnReady = function loadOnReady(){   
 		    
        $(".voucher-item").click(function() {
		  	var arrayPopup = new Array()
            arrayPopup['url'] = '/popup-voucher-tnc/'+ $(this).attr("rel-voucherkey"); 
            loadOverlayScreen(arrayPopup); 
	  	});
		
    };

}