//--------------------------------------------------------------------------------
//Clase ef_combo hereda ef
ef_combo.prototype = new ef;
var def = ef_combo.prototype;
def.constructor = ef_combo;

	function ef_combo(id_form, etiqueta, obligatorio, colapsado) {
		ef.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsado);
	}

	def.seleccionar = function () {
		try {
			this.input().focus();
			return true;
		} catch(e) {
			return false;
		}
	}	
	
	def.cambiar_valor = function(nuevo) {
		var input = this.input();
		var opciones = input.options;
		var ok = false;
		for (var i =0 ; i < opciones.length; i++) {
			if (opciones[i].value == nuevo) {
				opciones[i].selected = true;
				ok = true;
			}
		}
		if (!ok) {
			var msg = 'El combo no tiene a ' + nuevo + ' entre sus elementos.'
			throw new Error(msg, msg);
		}
		if (input.onchange)
			input.onchange();
	}
	
	def.validar = function () {
		var valor = this.valor();
		if (this._obligatorio && (valor == apex_ef_no_seteado || valor == '')) {
			this._error = 'El campo ' + this._etiqueta + ' es obligatorio.';
		    return false;
		}
		return true;
	}