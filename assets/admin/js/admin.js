( function( $ ) {	

	/* set the maximum number of tags allowed - pulled from options */
	var maxtags = gal_tax.maxtags;
	var taxonomy_name = gal_tax.taxonomy_name;	

    var Mytagbox;

	Mytagbox = {

        selectcheckbox : function(){

            var count = $("#taxonomy-"+taxonomy_name+" ul li [type='checkbox']:checked").length;
			if( count >= maxtags ) {				
                   $(".gal_tax_chk").each(function(e){
				    if(!this.checked)
					    $(this).attr('disabled','true');
				    });
			}
			else{

                  $(".gal_tax_chk").each(function(e){
					    $(this).removeAttr('disabled');
				    }); 

			}

        }

	};	

	$(document).ready( function() {
		
     Mytagbox.selectcheckbox();


    $('.gal_tax_chk').on('change',function(e){

    	     Mytagbox.selectcheckbox();
     
    });

	
	});			
})( jQuery );
