$(document).ready(function() {
	const base_url = ProcessWire.config.AddFieldValidateName.base_url;
	
	$('#ProcessFieldEdit').on('submit', function(event) {
		event.preventDefault();
		const $form = $(this);
		$.getJSON(base_url + '?validate_field_name=' + $('#Inputfield_name').val(), function(data) {
			if(data.allowed) {
				$form.off('submit');
				$('#Inputfield_submit_save_field').trigger('click');
			} else {
				const $inputfield = $('#wrap_Inputfield_name');
				$inputfield.find('.InputfieldError').remove(); // Remove any existing error message
				$inputfield.addClass('InputfieldStateError uk-alert-danger');
				$inputfield.find('.InputfieldContent').prepend('<p class="InputfieldError ui-state-error"><i class="fa fa-fw fa-flash"></i><span>' + data.error + '</span></p>');
			}
		});
	});
});
