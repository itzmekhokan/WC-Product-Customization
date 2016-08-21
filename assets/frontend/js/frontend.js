jQuery(document).ready(function($) {
	// bxslider for single product image slider
	$('.bxslider').bxSlider({
	  mode: 'fade',
	  captions: true,
	  slideWidth: 730
	});


    // flex slider
    $('#carousel').flexslider({
        animation: "slide",
        controlNav: false,
        animationLoop: false,
        slideshow: false,
        itemWidth: 210,
        itemMargin: 5,
        asNavFor: '#slider'
      });

      $('#slider').flexslider({
        animation: "slide",
        controlNav: false,
        animationLoop: false,
        slideshow: false,
        sync: "#carousel",
        start: function(slider){
          $('body').removeClass('loading');
        }
    });
 

	// format val to n number of decimal places
    // modified version of Danny Goodman's (JS Bible)
    function formatDecimal(val, n) {
        n = n || 2;
        var str = "" + Math.round ( parseFloat(val) * Math.pow(10, n) );
        while (str.length <= n) {
            str = "0" + str;
        }
        var pt = str.length - n;
        return str.slice(0,pt) + "." + str.slice(pt);
    }
        
    function updateTotal(e) {
        var form = this.form;
        var val = parseFloat( form.elements['total'].value );
        if ( this.checked ) {
            val += parseFloat(this.value);
        } else {
            val -= parseFloat(this.value);
        }
        form.elements['total'].value = formatDecimal(val);
    }
    
    function attachCheckboxHandlers() {
        var el = document.getElementById('makepackage');
    
        var tops = el.getElementsByTagName('input');
    
        for (var i=0, len=tops.length; i<len; i++) {
            if ( tops[i].type === 'checkbox' ) {
                tops[i].onclick = updateTotal;
            }
        }
        
    }
        
    attachCheckboxHandlers();
    
    // disable submission of all forms on this page
    for (var i=0, len=document.forms.length; i<len; i++) {
        document.forms[i].onsubmit = function() { return false; };
    }



});