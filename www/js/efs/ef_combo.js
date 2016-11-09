ef_combo.prototype = new ef();
ef_combo.prototype.constructor = ef_combo;

	/**
	 * @class Combo equivalente a un tag SELECT en HTML 
	 * @constructor
	 * @phpdoc Componentes/Efs/toba_ef_combo toba_ef_combo
	 */
	function ef_combo(id_form, etiqueta, obligatorio, colapsado, mantiene_estado_cascada) {
		ef.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsado);
		this._mantiene_estado = mantiene_estado_cascada;
	}

	//---Consultas		
	
	/**
	 * Tiene algun elemento seleccionado? (distinto del no_seteado)
	 * @type boolean
	 */
	ef_combo.prototype.tiene_estado = function(){
		var valor = this.get_estado();
		return valor !== '' &&  valor != apex_ef_no_seteado;	
	};
	
	ef_combo.prototype.validar = function () {
		if (! ef.prototype.validar.call(this)) {
			return false;
		}
		var valor = this.get_estado();
		if (this._obligatorio && 
				this.input().type != 'hidden' &&
			(valor == apex_ef_no_seteado || this.input().options.length === 0 ||
				valor === null)) {
			this._error = 'es obligatorio.';
		    return false;
		}
		return true;
	};
	
	//---Comandos 
		
	ef_combo.prototype.seleccionar = function () {
		try {
			this.input().focus();
			return true;
		} catch(e) {
			return false;
		}
	};	
	
	ef_combo.prototype.set_estado = function(nuevo) {
		var input = this.input();
		var opciones = input.options;
		var ok = false;
		if (opciones){
			for (var i =0 ; i < opciones.length; i++) {
				if (opciones[i].value == nuevo) {
					opciones[i].selected = true;
					ok = true;
					break;
				}
			}
		}
		if (!ok) {
			if (nuevo === null) {
				//Reentra con la constante que marca el valor 'no-seteado' del combo, para permitir pasar un null desde una sql al combo
				this.resetear_estado();
				return;
			}
			var msg = 'El combo no tiene a ' + nuevo + ' entre sus elementos.';
			throw new Error(msg, msg);
		}
		if (input.onchange) {
			input.onchange();
		}
	};
	
	ef_combo.prototype.resetear_estado = function() {
		if (this.tiene_estado()) {
			var opciones = this.input().options;			
			for (var i =0 ; i < opciones.length; i++) {
				if (opciones[i].value == apex_ef_no_seteado) {
					return this.set_estado(apex_ef_no_seteado);
				} else if (opciones[i].value === '') {
					return this.set_estado('');
				}
			}
		}
	};

	/**
	 * Elimina las opciones disponibles en el combo
	 */		
	ef_combo.prototype.borrar_opciones = function() {
		var input = this.input();
		if (input.options) {	
			input.options.length = 0;
		}
	};	
	
	/**
	 * Cambia las opciones del combo. En navegadores como Opera y Chrome si el resultado contiene claves numericas y alfanumericas se rompe el ordenamiento
	 * @param valores Objeto asociativo id=>valor
	 */	
	ef_combo.prototype.set_opciones = function(valores) {
		var rs = []
		var i = 0;
		for (id in valores){
			rs[i] = [id, valores[id]];
			i++;
		}
		this.set_opciones_rs(rs);
	};
	
	/**
	 * Cambia las opciones del combo y las inserta de forma ordenada
	 * @param valores Array Arreglo de Arreglo con 1er componente clave y 2da valor
	 */		
	ef_combo.prototype.set_opciones_rs = function(valores) {
		this.borrar_opciones();

		var input = this.input();
		if (! input.options) {
			input.options = [];
		}
		//Creo los OPTIONS recuperados
		var hay_datos = false;
		for (var i = 0; i < valores.length; i++) {
			var id = valores[i][0];
			var valor = valores[i][1];
			if (id !=  apex_ef_no_seteado) {
				hay_datos = true;
			}
			input.options[input.options.length] = new Option(valor, id);
			//--- Esto es para poder insertar caracteres especiales dentro del Option
			input.options[input.options.length - 1].innerHTML = (typeof  valor == 'string') ?  valor.decodeEntities(): valor;
		}
		if (hay_datos) {
			this.set_solo_lectura(false);
			this.seleccionar();
			if (input.onchange) {
				input.onchange();
			}			
		}
	};
	
	/**
	 * Devuelve si el ef mantiene o no su valor anterior entre los pedidos de cascada
	 */
	ef_combo.prototype.mantiene_valor_cascada = function() {
		return this._mantiene_estado;
	};
	
// ########################################################################################################
// ########################################################################################################

ef_radio.prototype = new ef();
ef_radio.prototype.constructor = ef_radio;

	/**
	 * @class Radio buttons equivalentes a <em>input type='radio'</em>
	 * @constructor
	 * @phpdoc Componentes/Efs/toba_ef_radio toba_ef_radio
	 */
	function ef_radio(id_form, etiqueta, obligatorio, colapsado, cant_columnas) {
		ef.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsado);
		this._cant_columnas = cant_columnas;
	}

	//---Consultas	
	
	ef_radio.prototype.get_estado = function() {
		var elem = this.input();		
		for (var i=0; i < elem.length ; i++) {
			if (elem[i].checked) {
				return elem[i].value;
			}
			if (elem[i].type == 'hidden') {
				return elem[i].value;
			}			
		}
		return apex_ef_no_seteado;
	};
	
	ef_radio.prototype.tiene_estado = function() {
		return this.get_estado() != apex_ef_no_seteado;	
	};	
	
	ef_radio.prototype.validar = function () {
		if (! ef.prototype.validar.call(this)) {
			return false;
		}
		if (this._obligatorio && this.get_estado() == apex_ef_no_seteado) {
			this._error = 'es obligatorio.';
		    return false;
		}
		return true;
	};
	
	ef_radio.prototype.input = function() {

		var input = getElementsByName_iefix('input', this._id_form);
		if (typeof input.length != 'number') {
			input = [input];
		}
		return input;
	};	

	//---Comandos	
	
	ef_radio.prototype.set_estado = function(nuevo) {
		var input = this.input();
		if (input){
			var ok = false;
			for (var i =0 ; i < input.length; i++) {
				if (input[i].value == nuevo) {
					input[i].checked = true;
					ok = true;
					break;
				}
			}
		}
		if (!ok) {
			var msg = 'El radiobutton no tiene a ' + nuevo + ' entre sus elementos.';
			throw new Error(msg, msg);
		}
		if (input.onchange) {
			input.onchange();
		}
	};	
	
	ef_radio.prototype.resetear_estado = function() {
		if (this.tiene_estado()) {
			var elem = this.input();		
			for (var i=0; i < elem.length ; i++) {
				if (elem[i].checked) {
					elem[i].checked = false;
				}
				if (elem[i].value == apex_ef_no_seteado) {
					elem[i].checked = true;
				}
			}
		}
	};	
	
	/**
	 * Elimina las opciones disponibles en el radio-button
	 */			
	ef_radio.prototype.borrar_opciones = function() {
		var opciones = this.get_contenedor_opciones();
		while(opciones.childNodes[0]) {
			opciones.removeChild(opciones.childNodes[0]);
		}
	};
	
	
	/**
	 * Cambia las opciones del radio-button. En navegadores como Opera y Chrome si el resultado contiene claves numericas y alfanumericas se rompe el ordenamiento
	 * @param valores Objeto asociativo id=>valor
	 */	
	ef_radio.prototype.set_opciones = function(valores) {
		var rs = []
		var i = 0;
		for (id in valores){
			rs[i] = [id, valores[id]];
			i++;
		}
		this.set_opciones_rs(rs);
	};
	
	/**
	 * Cambia las opciones del combo y las inserta de forma ordenada
	 * @param valores Array Arreglo de Arreglo con 1er componente clave y 2da valor
	 */			
	ef_radio.prototype.set_opciones_rs = function(rs) {
		this.borrar_opciones();
		var opciones = this.get_contenedor_opciones();
		var nuevo = "<table>";
		//--- Tiene que reconstruir la tabla
		var hay_datos=false;
		for (var i = 0; i < rs.length; i++) {
			var id = rs[i][0];
			var valor = rs[i][1];
    		if (i % this._cant_columnas === 0) {
    			nuevo += "<tr>\n";	
    		}			
			nuevo += this._crear_label(this._id_form, id, valor, i);
    		if (i+1 % this._cant_columnas === 0) {
    			nuevo += "</tr>\n";	
    		}
    		hay_datos=true;
		}
		nuevo += '</table>';
		opciones.innerHTML = nuevo;
		if (hay_datos) {
			this.activar();
		}		
		this.refrescar_callbacks();
	};
	
	/**
	 * @private
	 */
	ef_radio.prototype._crear_label = function(nombre, valor, etiqueta, i) {
		var id = nombre + i;
		nuevo = "<td><label class='ef-radio' for='"+ id + "'>";
		nuevo += "<input name='" + nombre + "' id='" + id + "' type='radio' value='" + valor + "'/>";
		nuevo += etiqueta + "</label></td>\n"; 
		return nuevo;
	};
	
	/**
	 * Retorna el tag HTML que contiene los input radio
	 */
	ef_radio.prototype.get_contenedor_opciones = function() {
		return document.getElementById('opciones_' + this._id_form);	
	};
	
	ef_radio.prototype.cuando_cambia_valor = function(callback) {
		addEvent(this.get_contenedor_opciones(), 'onchange_general', callback);		
		this.refrescar_callbacks();
	};
	
	/**
	 *	@private
	 */
	ef_radio.prototype.refrescar_callbacks = function() {
		var elem = this.input();
		var callback = this.get_contenedor_opciones().onchange_general;
		for (var i=0; i < elem.length; i++) {
			addEvent(elem[i], 'onclick', callback);
		}
	};
	
	ef_radio.prototype.set_solo_lectura = function(solo_lectura) {
		if (typeof solo_lectura == 'undefined') {
			solo_lectura = true;
		}
		var elem = this.input();
		for (var i=0; i < elem.length; i++) {
			elem[i].disabled = solo_lectura;
		}
	};	
	
	ef_radio.prototype.set_tab_index = function(tab_index) {
		var elem = this.input();
		if (elem.length > 0) {
			elem[0].tabIndex = tab_index;
		}
	};
	
	ef_radio.prototype.get_contenedor = function() {
		var cont = document.getElementById('cont_' + this._id_form);	
		if (! cont) {
			return this.get_contenedor_opciones();
		}
		return cont;
	};
	ef_radio.prototype.activo = function() {
		var input = this.input();
		var seteado = true;
		var activo = false;
		//El radio solo existe si todos sus elementos verifican isset
		for(elemento in input){
			seteado = seteado && isset(input[elemento]);
		}

		if (seteado) {
			//El radio esta activo si al menos uno de sus elementos esta activo.
			for (elemento in input){
				activo = activo || (input[elemento].type !='hidden' && !input[elemento].disabled)
			}
		} 
		return activo;
	};
	

	
toba.confirmar_inclusion('efs/ef_combo');