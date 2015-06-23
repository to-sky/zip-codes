jQuery(document).ready(function($) {
	var ajaxurl = 'http://wpmonsters/wp-admin/admin-ajax.php';
	var selectState = $('#acf-field-state');
	var selectCity = $('#acf-field-city');
	var selectZip = $('#acf-field-zip');

	selectCity.attr('disabled', true);
	selectZip.attr('disabled', true);

	/* Ajax handler for input State */
    selectState.on('change', function(){
    	var $this = $(this);
		selectZip.attr('disabled', true);
    	$("#btnAddZip").remove();
    	selectZip[0].options.length = 0;

	    var data = {
	    	'action': 'addCities',
	        'state' : $this.val()
	    };

	    $.post(ajaxurl, data, function(data) {
	    	$citySelect = $this.parents('.inside').find('#acf-field-city');
	    	$citySelect.html(data);
			selectCity.removeAttr('disabled');
	    });
    });


    /* Ajax handler for input City */
    selectCity.on('change', function(){
    	$("#btnAddZip").remove();
    	var $this = $(this);
	    var data = {
	    	'action': 'addZip',
	        'city' : $this.val()
	    };

	    $.post(ajaxurl, data, function(data) {
	    	selectZip.html(data);
			selectZip.removeAttr('disabled');	 
    	    selectZip.on('change', function(){
		    	if ($("#btnAddZip").length == 0) {
		    		$('#acf_acf_zip').append( '<button type="button" id="btnAddZip" class="add-zip-code button button-primary button-large">+Add zip</button>');
	    		}   	
		    });
	    });
    });


	/* Handler for button +Add Zip */
    $('#btnAddZip').live('click', function(){
    	var i = event.timeStamp;
	   	var stateValue = selectState.val();
    	var cityValue = selectCity.val();
    	var zipValue = selectZip.val();

    	var stateBlock = '<div class="row"><label class="name-tag">State</label><input type="text" name="state" value="' + stateValue + '" class="value-tag" readonly><input type="hidden" name="hidden-state[]" value="' + stateValue + '"></div>';
    	var cityBlock = '<div class="row"><label class="name-tag">City</label><input type="text" name="city" value="' + cityValue + '" class="value-tag" readonly><input type="hidden" name="hidden-city[]" value="' + cityValue + '"></div>';
    	var zipBlock = '<div class="row"><label class="name-tag">Zip</label><input type="text" name="zip-code" value="' + zipValue + '" class="value-tag" readonly><input type="hidden" name="hidden-zip[]" value="' + zipValue + '"></div>';

    	$('#poststuff').append( '<div id=wrapZips></div>');    	
    	$('#wrapZips').append( '<div id="key_' + i + '" class="zipRow">' + stateBlock + cityBlock + zipBlock +'</div>');
    	$('#key_' + i).append('<button type="button" id="btn_' + i + '" class="del-zip-code button button-primary button-large">Delete</button>');
    	$('#key_' + i).slideDown();

    	selectCity.attr('disabled', true);
		selectZip.attr('disabled', true);
    	selectCity.find('option').remove();
	   	selectZip.find('option').remove();
	   	$("#btnAddZip").remove();
	   	selectState.val(0);

		/* Handler for button Delete */
		$('#btn_' + i).live('click', function(){
			$('#key_' + i).remove();
		});
    });
});