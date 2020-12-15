$(document).ready(function() {
	function enable_validation()
	{
		$('[type="submit"]').on('click', function(event) {
			var current_form = $(this).closest('form');
			var failure_count = 0;
			if (current_form) {
				if ((current_form.attr('novalidate') == undefined || current_form.attr('novalidate') == "") &&
					(current_form.attr('disabled') == undefined || current_form.attr('disabled') != 'disabled')) {
					$('input:not(:disabled), select:not(:disabled), textarea:not(:disabled)', current_form).filter(':not([type=submit], [type=button], [type=reset], button)').each(function() {
						if ($(this).attr('required') || $(this).val().trim() != '') {
							//Lets validate only required and which are not empty
							if (core_validate(this) == false) {
								failure_count++;
							}
						}
					});
				}
			}
			/*
			* If failure_count is greaterthan 0 that means,
			*	form contain errors that will be prevent here. 
			*/
			if(failure_count > 0){
				event.preventDefault();
				$('input[type="text"].invalid-ip:visible').first().focus();
			}
		});
	}
	enable_validation();
	/*
	 * calling core_validate function with different event for validate form input elements.
	 */
	$('input:not(:disabled), select:not(:disabled), textarea:not(:disabled)').filter(':not([type=submit], [type=button], [type=reset], button)').on('change keyup blur', function() {
		core_validate(this);
	});
	/**
	*Validate only core type
	*/
	function core_validate(current_element)
	{
		var _status = true;
		// checking and validating all type of input elements by calling checkBlank function
		switch (current_element.type) {
			case 'text':
			case 'number':
			case 'email':
			case 'password':
			case 'date':
			case 'textarea':
			case 'file':
			case 'select-one':
			case 'radio':
			case 'checkbox':
				if (current_element.id) {
					var dt = $('#'+current_element.id).attr("DT");
					var regExp = '';
					_status = checkBlank(current_element,regExp);
				}
					_status = checkBlank(current_element,regExp);
				break;
		}
		return _status;
	}
	/*
	 * Check the blank and undefined value of form input elements
	 */
	function checkBlank(currElement, regexExp){
		var _status = true;
		if(currElement.type == 'radio' || currElement.type == 'checkbox'){
			if($('input[id='+currElement.id+']:checked').length <= 0){
				$(currElement).addClass("invalid-ip");
			} else {
				$(currElement).removeClass("invalid-ip");
			}
		} else {
			if($(currElement).attr('required') && (currElement.value.trim() == '' || currElement.value == 'undefined' || (currElement.value == 'INVALIDIP' && currElement.type == 'select-one'))){
				$(currElement).addClass("invalid-ip");
				_status = false;
			} else {
				_status = true;
				$(currElement).removeClass("invalid-ip");
			}
		}
		return _status;
	}
	/*
	 * Enable Popover
	 */
	$('.provabHelpText').popover();
	/*
	 * The code below will enable popover for input elements in form
	 */
	window.provab_popover = function(popover_list) {
		if ($.isArray(popover_list) == true) {
			selectors = '';
			$.each(popover_list, function(k, v) {
				selectors += '#' + v + ',';
			});
			selectors = selectors.substr(0, (parseInt(selectors.length) - 1));
			$(selectors).popover();
		}
	}
});
