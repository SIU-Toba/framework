//--------------------------------------------------------------------------------
//Clase ef_editable
ef_cuit.prototype = new ef;
var def = ef_cuit.prototype;
def.constructor = ef_cuit;

	function ef_cuit(id_form, etiqueta, obligatorio, colapsado) {
		ef.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsado);
	}

	def.input = function(posicion) {
		return document.getElementById(this._id_form + '_' + posicion);
	}
	
	def.validar = function () {
		var valor = this.get_estado();
		if (this._obligatorio && ereg_nulo.test(valor)) {
			this._error = ' es obligatorio.';
		    return false;
		}
		if (isNaN(valor)) {
			this._error = ' tiene que ser numérico.';
		    return false;
		}
		if (valor != '' && ! es_cuit(valor)) {
			this._error = ' no es una clave válida.';
		    return false;			
		}
		return true;
	}	
	
	def.get_estado = function() {
		var estado = 	this.input(1).value.pad(2, '0', 0) + 
						this.input(2).value.pad(8, '0', 0) + 
						this.input(3).value;
		if (estado == 0) {
			return '';	
		}
		return estado;
	}	
	
	def.set_estado = function(nuevo,posicion) {
		this.input(posicion).value = nuevo;
		if (this.input(posicion).onblur) {
			this.input(posicion).onblur();
		}
	}	
	
	//cuando_cambia_valor (disparar_callback)
	def.cuando_cambia_valor = function(callback) { 
		addEvent(this.input(1), 'onblur', callback);
		addEvent(this.input(2), 'onblur', callback);
		addEvent(this.input(3), 'onblur', callback);
	}
	
	def.set_solo_lectura = function(solo_lectura) {
		for (var i=1 ; i<4; i++) {
			this.input(i).readOnly = (typeof solo_lectura == 'undefined' || solo_lectura);
		}
	};		

	function es_cuit(nro) {
		var suma;
		var resto;
		var verif;
		var pos = nro.split('');
		if (! /^\d{11}$/.test(nro)) return false;
		
		while (true) {
			suma = (pos[0] * 5 + pos[1] * 4 + pos[2] * 3 +
			pos[3] * 2 + pos[4] * 7 + pos[5] * 6 +
			pos[6] * 5 + pos[7] * 4 + pos[8] * 3 + pos[9] * 2);
			resto = suma % 11;
			if (resto == 0) {
				verif = 0;
				break;
			} 
			else if (resto == 1 && (pos[1] == 0 || pos[6] == 7)) {
				pos[1] = 4;
				continue;
			} else {
				verif = 11 - resto;
				break;
			}
		}
		return pos[10] == verif;
	}	
		
toba.confirmar_inclusion('interface/ef_cuit');
