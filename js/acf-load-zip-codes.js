/* Ajax */
jQuery(document).ready(function($) {
	var ajaxurl = 'http://wpmonsters/wp-admin/admin-ajax.php';

    $('#acf-field-state').on('change', function(){
    	var $this = $(this);
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



    $('#acf-field-city').on('change', function(){
    	$("#btnAddZip").remove();
    	var $this = $(this);
	    var data = {
	    	'action': 'addZip',
	        'city' : $this.val()
	    };

	    $.post(ajaxurl, data, function(data) {
	    	$('#acf-field-zip').html(data);
    	    $('#acf-field-zip').on('change', function(){
		    	if ($("#btnAddZip").length == 0) {
		    		$('#acf_acf_zip').append( '<button type="button" id="btnAddZip" >+Add zip</button>').trigger('create' );
		    		$("#btnAddZip").addClass( 'button button-primary button-large' );
	    		}		    	
		    });
	    });
    });


    $('#btnAddZip').live('click', function(){
    	var timestamp = event.timeStamp;
    	var $fieldsRow = $('<div class="inside"><div id="acf-state" class="field field_type-select field_key-zip_state" data-field_name="state" data-field_key="zip_state" data-field_type="select"><p class="label"><label for="acf-field-state">State</label></p><select id="acf-field-state" class="select" name="fields[zip_state_' + timestamp + ']"><option value="0" selected="selected">-- Select State --</option></select></div><div id="acf-city" class="field field_type-select field_key-zip_city" data-field_name="city" data-field_key="zip_city" data-field_type="select"><p class="label"><label for="acf-field-city">City</label></p><select id="acf-field-city" class="select" name="fields[zip_city_' + timestamp + ']"><option value="0">-- Select City --</option></select></div><div id="acf-zip" class="field field_type-select field_key-zip_zip" data-field_name="zip" data-field_key="zip_zip" data-field_type="select"><p class="label"><label for="acf-field-zip">Zip</label></p><select id="acf-field-zip" class="select" name="fields[zip_zip_' + timestamp + ']"><option value="0" selected="selected">-- Select Zip --</option></select></div><div style="display:none"><input type="hidden" name="acf_nonce" value="1b7b369ef1"></div></div>');

	    $fieldsRow.appendTo("#acf_acf_zip");
    });
});