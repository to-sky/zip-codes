jQuery(document).ready(function($) {
	var ajaxurl = 'http://wpmonsters/wp-admin/admin-ajax.php';

    $('#acf-field-state').on('change', function(){
    	$("#acf-city").css("display", "block");

	    var data = {
	    	'action': 'addCities',
	        'state' : $('#acf-field-state').val()
	    };

	    $.post(ajaxurl, data, function(response) {
	    	console.log(response);
	    });
    });
});