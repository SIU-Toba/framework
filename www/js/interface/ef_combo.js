//--------------------------------------------------------------------------------
//Clase ef_combo hereda ef
ef_combo.prototype = new ef;
var def = ef_combo.prototype;
def.constructor = ef_combo;

	function ef_combo(id_form, etiqueta, obligatorio) {
		ef.prototype.constructor.call(this, id_form, etiqueta, obligatorio);
	}

	def.seleccionar = function () {
		this.input().focus();
	}	
	
	def.validar = function () {
		var valor = this.valor();
		if (this.obligatorio && (valor == apex_ef_no_seteado || valor == '')) {
			this.error = 'El campo ' + this.etiqueta + ' es obligatorio.';
		    return false;
		}
		return true;
	}