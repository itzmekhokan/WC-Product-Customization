jQuery(document).ready(function($) {
	$('.make_pack_btn').click(function(){
		var data = {
		'action': 'package_total',
		'pkg_quantity' : $('.qty').val(),
		'pkg_product' : $('#cart_product').val(),
		'pkg_total_price': $('#package_total').val()
		}

		$.post(admin_ajax.ajaxurl, data, function(response) {
			$('.woocommerce-breadcrumb').after('');
			if(response.error ==true){
				$('.woocommerce-breadcrumb').after('<div class="woocommerce-error">'+response.message+'</div>');
			}else{
				$('.woocommerce-breadcrumb').after('<div class="woocommerce-message">'+response.message+'</div>');
			}
			 
		});
	});


        	
});