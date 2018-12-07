function asociar_datepicker(object){
	
	$(object).on('changeDate', function() {
	   input = $(object).parent().prev();
	   input.val(
			   $(this).datepicker('getFormattedDate')
	   )
	   input.focus();
	   $(object).datepicker('hide');
	});
}
