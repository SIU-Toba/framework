//--------------------------------------------------------------------------------
//Clase ef_checkbox
ef_checkbox.prototype = new ef;
var def = ef_checkbox.prototype;
def.constructor = ef_checkbox;

	function ef_checkbox(id_form, etiqueta, obligatorio) {
		ef.prototype.constructor.call(this, id_form, etiqueta, obligatorio);
	}

	//cuando_cambia_valor (disparar_callback)
	def.cuando_cambia_valor = function(callback) { 
		if (! this.input().onclick)	//Para no romper scripts hechos ad-hoc
			this.input().onclick = callback;	
	}

	def.valor = function() {
		if (this.input().checked)
			return this.input().value;
		else
			return null;
	}	