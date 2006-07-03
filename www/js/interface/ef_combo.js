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
				break;
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
		if (! ef.prototype.validar.call(this))
			return false;		
		var valor = this.valor();
		if (this._obligatorio && 
			(valor == apex_ef_no_seteado || this.input().options.length ==0 ||
				valor == null)) {
			this._error = 'es obligatorio.';
		    return false;
		}
		return true;
	}

	//--- Cascadas
	def.borrar_opciones = function() {
		this.input().options.length = 0;
	}	
	
	def.resetear = function() {
		if (this.esta_cargado()) {
			var opciones = this.input().options;			
			for (var i =0 ; i < opciones.length; i++) {
				if (opciones[i].value == apex_ef_no_seteado) {
					return this.cambiar_valor(apex_ef_no_seteado);
				} else if (opciones[i].value == '') {
					return this.cambiar_valor('');
				}
			}
		}
	}
	
	def.esta_cargado = function() {
		var valor = this.valor();
		return valor != '' &&  valor != apex_ef_no_seteado;	
	}
	
	def.set_valores = function(valores) {
		var input = this.input();
		input.options.length = 0;//Borro las opciones que existan
		//Creo los OPTIONS recuperados
		var hay_datos = false
		for (id in valores){
			if (id !=  apex_ef_no_seteado) {
				hay_datos = true;
			}
			input.options[input.options.length] = new Option(valores[id], id);
			//--- Esto es para poder insertar caracteres especiales dentro del Option
			input.options[input.options.length - 1].innerHTML = valores[id];
		}
		if (hay_datos) {
			input.disabled = false;
			input.focus();
			if (input.onchange) {
				input.onchange();
			}			
		}
	}
	
	
//--------------------------------------------------------------------------------
//Clase ef_radio hereda ef
ef_radio.prototype = new ef;
var def = ef_radio.prototype;
def.constructor = ef_radio;

	function ef_radio(id_form, etiqueta, obligatorio, colapsado, callback) {
		ef.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsado);
		this._callback = callback;
	}

	def.validar = function () {
		if (! ef.prototype.validar.call(this))
			return false;		
		if (this._obligatorio && this.valor() == apex_ef_no_seteado) {
			this._error = 'es obligatorio.';
		    return false;
		}
		return true;
	}
	
	def.valor = function() {
		var elem = this.input();		
		for (var i=0; i < elem.length ; i++) {
			if (elem[i].checked)
				return elem[i].value;
		}
		return apex_ef_no_seteado;
	}
	
	//--- Cascadas
	def.esta_cargado = function() {
		return this.valor() != apex_ef_no_seteado;	
	}	
	
	def.borrar_opciones = function() {
		var opciones = this.get_contenedor();
		while(opciones.childNodes[0]) {
			opciones.removeChild(opciones.childNodes[0]);
		}
	}
	
	def.set_valores = function(valores) {
		this.borrar_opciones();
		var opciones = this.get_contenedor();
		var nuevo = "";
		var i=0;
		if (valores[apex_ef_no_seteado]) {
			nuevo += this._crear_label(this._id_form, apex_ef_no_seteado, valores[apex_ef_no_seteado], i);
			delete(valores[apex_ef_no_seteado]);
			i++;
		}
		for (id in valores) {
			nuevo += this._crear_label(this._id_form, id, valores[id], i);
			i++
		}
		opciones.innerHTML = nuevo;
		this.refrescar();
	}
	
	def._crear_label = function(nombre, valor, etiqueta, i) {
		var id = nombre + i;
		nuevo = "<label class='ef-radio' for='"+ id + "'>";
		nuevo += "<input name='" + nombre + "' id='" + id + "' type='radio' value='" + valor + "'/>";
		nuevo += etiqueta + "</label>"; 
		return nuevo;
	}
	
	def.cuando_cambia_valor = function(callback) {
		this._callback = this._callback + ';' + callback;
		this.refrescar();
	}
	
	def.refrescar = function() {
		var elem = this.input();
		for (var i=0; i < elem.length; i++) {
			addEvent(elem[i], 'onclick', this._callback);
		}
	}
	
	def.set_solo_lectura = function(solo_lectura) {
		if (typeof solo_lectura == 'undefined') 
			solo_lectura = true;
		var elem = this.input();
		for (var i=0; i < elem.length; i++) {
			elem[i].disabled = solo_lectura;
		}
	}	
	
	def.input = function() {
		var input = document.getElementsByName(this._id_form);	
		if (typeof input.length != 'number') {
			input = [input];
		}
		return input;
	}
	
toba.confirmar_inclusion('interface/ef_combo');