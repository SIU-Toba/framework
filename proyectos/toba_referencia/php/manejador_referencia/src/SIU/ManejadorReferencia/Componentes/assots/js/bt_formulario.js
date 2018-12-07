
ef.prototype.resaltar = function(texto, izq) {
	var cont = this.get_contenedor();
	var div_group = $(cont).closest('.form-group');

	if( !div_group.hasClass( "has-error has-feedback" )){
		div_group.toggleClass('has-error has-feedback');
		$OuterDiv = $('<span>'+texto+'</span>').toggleClass('help-block');
		

		div_group.append($OuterDiv);
		
	}
};

ef.prototype.no_resaltar = function() {
	var cont = this.get_contenedor();
	var div_group = $(cont).closest('.form-group');

	if( div_group.hasClass( "has-error has-feedback" )){
		div_group.toggleClass('has-error has-feedback');

		
		$(div_group).children('span').remove(); // Elimina el mensaje

	}
};
