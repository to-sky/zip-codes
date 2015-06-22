/* Ajax */
jQuery(document).ready(function($) {
	var ajaxurl = 'http://wpmonsters/wp-admin/admin-ajax.php';

	$('#acf-field-city').attr('disabled', true);
	$('#acf-field-zip').attr('disabled', true);

	/* Handler for input State */
    $('#acf-field-state').on('change', function(){
    	var $this = $(this);
		$('#acf-field-city').removeAttr('disabled');
		$('#acf-field-zip').attr('disabled', true);
    	$("#btnAddZip").remove();
    	$('#acf-field-zip')[0].options.length = 0;

	    var data = {
	    	'action': 'addCities',
	        'state' : $this.val()
	    };

	    $.post(ajaxurl, data, function(data) {
	    	$citySelect = $this.parents('.inside').find('#acf-field-city');
	    	$citySelect.html(data);
	    });
    });


    /* Handler for input City */
    $('#acf-field-city').on('change', function(){
    	$("#btnAddZip").remove();
		$('#acf-field-zip').removeAttr('disabled');
    	var $this = $(this);
	    var data = {
	    	'action': 'addZip',
	        'city' : $this.val()
	    };

	    $.post(ajaxurl, data, function(data) {
	    	$('#acf-field-zip').html(data);
    	    $('#acf-field-zip').on('change', function(){
		    	if ($("#btnAddZip").length == 0) {
		    		$('#acf_acf_zip').append( '<button type="button" id="btnAddZip" >+Add zip</button>');
		    		$("#btnAddZip").addClass( 'button button-primary button-large' );
	    		}		    	
		    });
	    });
    });


	/* Handler for buttom +Add Zip */
    $('#btnAddZip').live('click', function(){
	   	var stateValue = $('#acf-field-state').val();
    	var cityValue = $('#acf-field-city').val();
    	var zipValue = $('#acf-field-zip').val();
    	var stateBlock = '<div class="row"><span class="name-tag">State</span><span class="value-tag">' + stateValue + '</span></div>';
    	var cityBlock = '<div class="row"><span class="name-tag">City</span><span class="value-tag">' + cityValue + '</span></div>';
    	var zipBlock = '<div class="row"><span class="name-tag">Zip</span><span class="value-tag">' + zipValue + '</span></div>';

    	$('#poststuff').append( '<div id="newZipCode">' + stateBlock + cityBlock + zipBlock +'</div>');
    	$("#newZipCode").animate(
    	{
    		opacity: 1,
	        height: '60px',
	        width: '700px'
	    });

    	$("#newZipCode").nextAll().animate(
    	{	
    		opacity: 1,
	        height: '60px',
	        width: '700px'
	    });

    	$('#acf-field-city').attr('disabled', true);
		$('#acf-field-zip').attr('disabled', true);
    	$('#acf-field-city').find('option').remove();
	   	$('#acf-field-zip').find('option').remove();
	   	$("#btnAddZip").remove();
	   	$('#acf-field-state').val(0);
    });
});