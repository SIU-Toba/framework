//--------------------------------------------------------------------------------
//Clase ef_multi_seleccion_check
ef_multi_seleccion_check.prototype = new ef;
var def = ef_multi_seleccion_check.prototype;
def.constructor = ef_multi_seleccion_check;

	function ef_multi_seleccion_check(id_form, etiqueta, obligatorio, colapsado) {
		ef.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsado);
	}

	def.set_solo_lectura = function(solo_lectura) {
		solo_lectura = (typeof solo_lectura != 'undefined' && solo_lectura);		
		var elem = document.getElementsByName(this._id_form + '[]');
		if (elem.length) {
			//Si son muchos elementos
			for (var i=0; i < elem.length; i++) {
				elem[i].disabled = solo_lectura;
			}
		} else {
			//Es uno unico
			elem.disabled = solo_lectura;
		}		
	}
	
toba.confirmar_inclusion('interface/ef_multi_seleccion');