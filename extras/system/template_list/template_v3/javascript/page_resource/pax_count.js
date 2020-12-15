$(document).ready(function() {
    $('.advncebtn').click(function() {
        $(this).parent('.togleadvnce').toggleClass('open')
    });
    $('.totlall').click(function() {
        $('.roomcount').toggleClass("fadeinn")
    });
    $('.totlall, .roomcount').click(function(e) {
        e.stopPropagation()
    });

    $('.done1').click(function() {
       $('.roomcount').removeClass("fadeinn")
   });
    $(document).click(function() {
        $('.roomcount').removeClass("fadeinn")
    });
    $('.alladvnce').click(function() {
        $('.advncedown').removeClass("fadeinn");
        $(this).children('.advncedown').toggleClass("fadeinn")
    });
    $('.alladvnce, .advncedown').click(function(e) {
        e.stopPropagation()
    });
    $(document).click(function() {
        $('.advncedown').removeClass("fadeinn")
    });
    $('.btn-number').click(function(e) {
       
        e.preventDefault();
        fieldName = $(this).attr('data-field');
        type = $(this).attr('data-type');
        var current_pax_count_wrapper = $(this).closest('.pax-count-wrapper');        
        var input = $("input[name='" + fieldName + "']", current_pax_count_wrapper);
        var currentVal = parseInt(input.val()); 
       
        if (!isNaN(currentVal)) {
            if (type == 'minus') {
                if (currentVal > input.attr('min')) {
                    input.val(currentVal - 1).change()
                }
                if (parseInt(input.val()) == input.attr('min')) {}
            } else if (type == 'plus') {
                if (currentVal < input.attr('max')) {
                    input.val(currentVal + 1).change()
                }
                if (parseInt(input.val()) == input.attr('max')) {}
            }
       
            
        
        } else {
            input.val(0)
        }
        manage_infant_count(fieldName);
        var form_id = $(this).closest('form').attr('id');
        total_pax_count(form_id)
    });
    $('.activities-btn-number').click(function(e) {
       
        e.preventDefault();
        fieldName = $(this).attr('data-field');
        type = $(this).attr('data-type');
        var current_pax_count_wrapper = $(this).closest('.pax-count-wrapper');        
        var input = $("input[name='" + fieldName + "']", current_pax_count_wrapper);
        var currentVal = parseInt(input.val()); 
       
        if (!isNaN(currentVal)) {
            if (type == 'minus') {
                if (currentVal > input.attr('min')) {
                    input.val(currentVal - 1).change()
                }
                if (parseInt(input.val()) == input.attr('min')) {}
            } else if (type == 'plus') {
                if (currentVal < input.attr('max')) {
                    input.val(currentVal + 1).change()
                }
                if (parseInt(input.val()) == input.attr('max')) {}
            }
       
            
        
        } else {
            input.val(0)
        }
        var form_id = 'check_tourgrade';     
        activites_total_pax_count(form_id)
    });

    $('.input-number').focusin(function() {
        $(this).data('oldValue', $(this).val())
    });
    $('.input-number').change(function() {
        minValue = parseInt($(this).attr('min'));
        maxValue = parseInt($(this).attr('max'));
        valueCurrent = parseInt($(this).val());
        var current_pax_count_wrapper = $(this).closest('.pax-count-wrapper');
        name = $(this).attr('name');
        if (valueCurrent >= minValue) {
            $(".btn-number[data-type='minus'][data-field='" + name + "']", current_pax_count_wrapper).removeAttr('disabled')
        } else {
            alert('Sorry, the minimum value was reached');
            $(this).val($(this).data('oldValue'))
        }
        if (valueCurrent <= maxValue) {
            $(".btn-number[data-type='plus'][data-field='" + name + "']", current_pax_count_wrapper).removeAttr('disabled')
        } else {
            alert('Sorry, the maximum value was reached');
            $(this).val($(this).data('oldValue'))
        }
    });
    $(".input-number").keydown(function(e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 || (e.keyCode == 65 && e.ctrlKey === true) || (e.keyCode >= 35 && e.keyCode <= 39)) {
            return
        }
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault()
        }
    })
});
function activites_total_pax_count(form_id) {

    if (form_id != '') {       
        var pax_count = $('input.pax_count_value', 'form#' + form_id + ' div.pax_count_div').map(function() {
            if(this.value != '') {
                return parseInt(this.value)
            }
        }).get();
       
        var total_pax_count = 0;
        $.each(pax_count, function() {
            total_pax_count += this
        });
        
        $("#total_pax_count").val(total_pax_count);
    }
}

function total_pax_count(form_id) {

    if (form_id != '') {
        var pax_count = $('input.pax_count_value', 'form#' + form_id + ' div.pax_count_div').map(function() {
        	if(this.value != '') {
        		return parseInt(this.value)
        	}
        }).get();
        var total_pax_count = 0;
        $.each(pax_count, function() {
            total_pax_count += this
        });
        if(total_pax_count > 1){
            $('#travel_text').text('Travellers');
        }
        else{
              $('#travel_text').text('Traveller');
        }
       
        $('.total_pax_count', 'form#' + form_id).empty().text(total_pax_count)
    }
}