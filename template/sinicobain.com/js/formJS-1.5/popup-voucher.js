function PopupVoucher(opt){   
    var thisObj = this;     
  
	this.updateVoucher = function updateVoucher(obj){
		var label = $(obj).attr("rel-label");
		var voucherkey = $(obj).attr("rel-key");
		//var totalsales =  parseFloat(unformatCurrency($(".mnv-cart-total-sales").text())) || 0;
		
		$(".mnv-cart-voucher-label").text(label);
		$(".mnv-cart-voucher-label").closest(".div-table-row").find('[name=\'hidVoucherKey[]\']').val(voucherkey);
		
		cart = new Cart(opt);
		cart.updateCartTotalSummary();
		
		hideOverlayScreen();
		//hitung ulang seperti di cart
	}
	
    this.loadOnReady = function loadOnReady(){   
       	$(".voucher-list li").click(function() {
		  	thisObj.updateVoucher(this); 
	  	});  
    };

}