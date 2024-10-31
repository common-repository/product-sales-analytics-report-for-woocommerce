jQuery( document ).ready(function() {
	jQuery('.psarfw-option-section #psarfw_sbp_field_report_time').change(function(){	
		if(jQuery('#psarfw_sbp_field_report_time').val() == 'custom') {
			jQuery('.psarfw_sbp_custom_time').addClass('show'); 
		} else {
			jQuery('.psarfw_sbp_custom_time').removeClass('show'); 
		} 
	});
});