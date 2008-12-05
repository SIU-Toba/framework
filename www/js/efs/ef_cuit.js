ef_cuit.prototype = new ef();
ef_cuit.prototype.constructor = ef_cuit;

	/**
	 * @class Triple editbox que constituyen las 3 partes del CUIT/CUIL
	 * @constructor
	 * @phpdoc Componentes/Efs/toba_ef_cuit toba_ef_cuit
	 */
	function ef_cuit(id_form, etiqueta, obligatorio, colapsado, desactivar_validacion) {
		ef.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsado);
		this._desactivar_validacion = desactivar_validacion;
	}

	ef_cuit.prototype.input = function(posicion) {
		if (! isset(posicion)) {
			posicion = 2;
		}
		return document.getElementById(this._id_form + '_' + posicion);
	};
	
	ef_cuit.prototype.validar = function () {
		if (! ef.prototype.validar.call(this)) {
			return false;
		}		
		var valor = this.get_estado();
		if (this._obligatorio && ereg_nulo.test(valor)) {
			this._error = ' es obligatorio.';
		    return false;
		}
		if (isNaN(valor)) {
			this._error = ' tiene que ser numérico.';
		    return false;
		}
		if (valor !== '' && ! this._desactivar_validacion && ! es_cuit(valor)) {
			this._error = ' no es una clave válida.';
		    return false;			
		}
		return true;
	};	
	
	/**
	 * Retorna el cuit/cuil actual como un unico string, sin caracteres intermedio
	 * Por ejemplo 20271957786
	 * @type string
	 */
	ef_cuit.prototype.get_estado = function() {
		var estado = 	this.input(1).value.pad(2, '0', "PAD_LEFT") + 
						this.input(2).value.pad(8, '0',"PAD_LEFT") + 
						this.input(3).value;
		if (estado === '0000000000') {
			return '';	
		}
		return estado;
	};	
	
	ef_cuit.prototype.get_tab_index = function () {
		return this.input(1).tabIndex;
	};	
	
	ef_cuit.prototype.set_tab_index = function(tab_index) {
		this.input(1).tabIndex = tab_index;
		this.input(2).tabIndex = tab_index;
		this.input(3).tabIndex = tab_index;
	};
	
	
	ef_cuit.prototype.set_estado = function(nuevo,posicion) {
		if (! isset(posicion)) {
			this.set_estado_posicion(nuevo.substr(0, 2), 1);
			this.set_estado_posicion(nuevo.substr(2, 8), 2);
			this.set_estado_posicion(nuevo.substr(10,1), 3);
		} else {
			this.set_estado_posicion(nuevo, posicion);
		}
	};	
	
	ef_cuit.prototype.set_estado_posicion = function(nuevo,posicion) {
		this.input(posicion).value = nuevo;
		if (this.input(posicion).onblur) {
			this.input(posicion).onblur();
		}
	};	
		
	
	/**
	 * Borra el estado actual del elemento, el nuevo estado depende de cada ef, generalmente equivale a un string vacio
	 */
	ef_cuit.prototype.resetear_estado = function() {
		this.set_estado('', 1);
		this.set_estado('', 2);
		this.set_estado('', 3);
	};	
	
	//cuando_cambia_valor (disparar_callback)
	ef_cuit.prototype.cuando_cambia_valor = function(callback) { 
		addEvent(this.input(1), 'onblur', callback);
		addEvent(this.input(2), 'onblur', callback);
		addEvent(this.input(3), 'onblur', callback);
	};
	
	ef_cuit.prototype.set_solo_lectura = function(solo_lectura) {
		this._solo_lectura = solo_lectura;
		for (var i=1 ; i<4; i++) {
			this.input(i).readOnly = (typeof solo_lectura == 'undefined' || solo_lectura);
		}
	};		
	
	ef_cuit.prototype.desactivar_validacion = function(desactivar) {
		this._desactivar_validacion = desactivar;
	};			
	
	
	ef_cuit.prototype.get_desactivar_validacion = function() {
		return this._desactivar_validacion;
	};				
	
//--------------------------------------------	
function es_cuit(nro) {
	if (typeof ef_cuit_excepciones != 'undefined') {
		if (in_array(nro, ef_cuit_excepciones)) {
			return true;
		}
	}
	var suma;
	var resto;
	var verif;
	var pos = nro.split('');
	if (! (/^\d{11}$/).test(nro)) {
		return false;
	}
	
	while (true) {
		suma = (pos[0] * 5 + pos[1] * 4 + pos[2] * 3 +	pos[3] * 2 + pos[4] * 7 + pos[5] * 6 +	pos[6] * 5 + pos[7] * 4 + pos[8] * 3 + pos[9] * 2);
		resto = suma % 11;
		if (resto === 0) {
			verif = 0;
			break;
		} 
		else if (resto == 1 && (pos[1] === 0 || pos[6] == 7)) {
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
