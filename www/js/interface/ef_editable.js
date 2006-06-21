//--------------------------------------------------------------------------------
//Clase ef_editable
ef_editable.prototype = new ef();
var def = ef_editable.prototype;
def.constructor = ef_editable;

	function ef_editable(id_form, etiqueta, obligatorio, colapsado, masc) {
		ef.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsado);
		this._forma_mascara = (masc && masc.trim().toLowerCase() != 'no') ? masc : null;
		this._mascara = null;
	}

	def.iniciar = function(id, contenedor) { 
		ef.prototype.iniciar.call(this, id, contenedor);
		if (this._forma_mascara) {
			this._mascara = new mascara_generica(this._forma_mascara);
			this._mascara.attach(this.input());
			this.cambiar_valor(this.input().value);
		}
	}	
	
	def.submit = function() { 
		if (this._mascara) {
			this.input().value = this.valor();
		}
		ef.prototype.submit.call(this);
	}
	
	def.validar = function () {
		if (! ef.prototype.validar.call(this))
			return false;		
		if (this._obligatorio && ereg_nulo.test(this.valor())) {
			this._error = 'es obligatorio.';
		    return false;
		}
		return true;
	}	
	
	def.valor = function() {
		if (this._mascara) {
			//Refresco del valor, esto es necesario por la multiplexacion del ML
			this._mascara.format(this.input().value, false);
			return this._mascara.valor_sin_formato();
		}
		else
			return this.input().value;
	}	
	
	def.valor_formateado = function() {
		return this.input().value;
	}		
	
	def.formato_texto = function(valor) {
		if (this._mascara)
			return this._mascara.format(valor, false, true);
		else
			return valor;
	}
	
	def.cambiar_valor = function(nuevo) {
		if (this._mascara) {
			var valor = this._mascara.format(nuevo, false, true);
			this.input().value = valor;
			var desc = document.getElementById(this._id_form + '_desc');
			if (desc) {
				desc.value = valor;
			}
		} else {
			return ef.prototype.cambiar_valor.call(this, nuevo);	
		}
		if (this.input().onblur) {
			this.input().onblur();
		}
	}	
	
	//cuando_cambia_valor (disparar_callback)
	def.cuando_cambia_valor = function(callback) { 
		addEvent(this.input(), 'onblur', callback);
	}
	
	def.set_solo_lectura = function(solo_lectura) {
		this.input().readOnly = (typeof solo_lectura == 'undefined' || solo_lectura);
	}	
	
	def.activo = function() {
		return !(this.input().readOnly);
	}

	/**
	* @todo Falta manejar el caso del solo-lectura
	*/
	def.cascada_cargar = function(nuevo_valor) {
		this.cambiar_valor(nuevo_valor);
		this.input().focus();
		atender_proxima_consulta();
	}
	
	def.cascada_resetear = function() {
		this.set_solo_lectura(true);
		this.cambiar_valor('');
	}

	
//--------------------------------------------------------------------------------
//Clase ef_editable_numero hereda de ef_editable
ef_editable_numero.prototype = new ef_editable;
var def = ef_editable_numero.prototype;
def.constructor = ef_editable_numero;

	function ef_editable_numero(id_form, etiqueta, obligatorio, colapsado, masc, rango, mensaje) {
		ef_editable.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsado);
		if (masc)
			this._forma_mascara = (masc.trim().toLowerCase() != 'no') ? masc : null;
		else
			this._forma_mascara = '###.###,##';
		this._rango = rango;					//[0] => inferior [1] => superior
		this._mensaje = mensaje;				//Mensaje a mostrar cuando no se valida el número
	}
	
	def.iniciar = function(id, controlador) { 
		ef.prototype.iniciar.call(this, id, controlador);
		if (this._forma_mascara) {
			this._mascara = new mascara_numero(this._forma_mascara);
			this._mascara.attach(this.input());
			this.cambiar_valor(this.input().value);
		}
	}	
	
	def.valor = function() {
		var valor = ef_editable.prototype.valor.call(this);
		return (valor == '') ? '' : parseFloat(valor);
	}

	def.validar_rango = function() {
		//this._rango[0-1][0] es limite [0-1][1] determina inclusive
		var ok = true;
		var valor = this.valor();
		if (typeof valor != 'number' || ! this._rango)
			return true;
		if (this._rango[0][0] != '*')
			ok = (this._rango[0][1]) ? (valor >= this._rango[0][0]) : (valor > this._rango[0][0]);
		if (ok && this._rango[1][0] != '*')
			ok = (this._rango[1][1]) ? (valor <= this._rango[1][0]) : (valor < this._rango[1][0]);
		return ok;
	}	
	
	def.validar = function() {
		if (! ef_editable.prototype.validar.call(this))
			return false;
		if (isNaN(this.valor())) {
			this._error = 'debe ser numérico.';
		    return false;
		}
		if (!this.validar_rango()) {
			this._error = this._mensaje;
		    return false;
		}
		return true;
	}

//--------------------------------------------------------------------------------
//Clase ef_editable_moneda hereda de ef_editable_numero
ef_editable_moneda.prototype = new ef_editable_numero();
var def = ef_editable_moneda.prototype;
	function ef_editable_moneda(id_form, etiqueta, obligatorio, colapsado, masc, rango, mensaje)	{
		ef_editable_numero.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsado, masc, rango, mensaje);
		this._forma_mascara = (masc) ? masc : '$ ###.###,00';		
	}

//--------------------------------------------------------------------------------
//Clase ef_editable_porcentaje hereda de ef_editable_numero
ef_editable_porcentaje.prototype = new ef_editable_numero();
var def = ef_editable_porcentaje.prototype;
	function ef_editable_porcentaje(id_form, etiqueta, obligatorio, colapsado, masc, rango, mensaje)	{
		ef_editable_numero.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsado, masc, rango, mensaje);
	}
	
	def.formato_texto = function(valor) {
		return (ef_editable_numero.prototype.formato_texto.call(this, valor) + ' %');
	}
	
//--------------------------------------------------------------------------------
//Clase ef_editable_clave hereda de ef_editable
ef_editable_clave.prototype = new ef_editable;
var def = ef_editable_clave.prototype;

	function ef_editable_clave(id_form, etiqueta, obligatorio, colapsado)	{
		ef_editable.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsado);
	}	

	def.validar = function() {
		if (! ef_editable.prototype.validar.call(this))
			return false;
		var orig = this.input();
		var test = document.getElementById(this._id_form + '_test');
		if (orig.value != test.value)
		{
			this._error = ': Las contraseñas no coinciden.';
		    return false;
		}		
		return true;
	}
	
	def.cambiar_valor = function (nuevo) {
		var input = this.input();
		input.value = nuevo;
		document.getElementById(this._id_form + '_test').value = nuevo;
		if (input.onblur)
			input.onblur();
	}
	
	def.cambiar_tab = function(tab_index) {
		this.input().tabIndex = tab_index;
		document.getElementById(this._id_form + '_test').tabIndex = tab_index+1;
	}	
	
	//cuando_cambia_valor (disparar_callback)
	def.cuando_cambia_valor = function(callback) { 
		addEvent(this.input(), 'onblur', callback);
		addEvent(document.getElementById(this._id_form + '_test'), 'onblur', callback);
	}
	
	
//--------------------------------------------------------------------------------
//Clase ef_editable_fecha hereda de ef_editable
ef_editable_fecha.prototype = new ef_editable;
var def = ef_editable_fecha.prototype;

	function ef_editable_fecha(id_form, etiqueta, obligatorio, colapsable, masc)	{
		ef_editable.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsable);
		this._forma_mascara = (masc) ? masc : 'dd/mm/yyyy';
	}	
	
	def.iniciar = function(id, controlador) {
		ef.prototype.iniciar.call(this, id, controlador);
		this._mascara = new mascara_fecha(this._forma_mascara);
		this._mascara.attach(this.input());
	}
	
	def.valor = function() {
		return this.input().value;
	}
	
	def.fecha = function() {
		if (this.validar()) {
			var arr = this.valor().split('/');
			return new Date(arr[2], arr[1] - 1, arr[0]);
		}
		return null;
	}
	
	def.validar = function() {
		if (! ef_editable.prototype.validar.call(this))
			return false;
		try {
			var valida = validar_fecha(this.valor(), false);
		} catch (e) {
			valida = "no contiene una fecha válida";
		}
		if (valida != true) {
			this._error = valida;
		    return false;
		}		
		return true;
	}	
	
	def.vinculo = function() {
		return document.getElementById('link_' + this._id_form);
	}
	
	def.set_solo_lectura = function(solo_lectura) {
		solo_lectura = (typeof solo_lectura != 'undefined' && solo_lectura);
		ef_editable.prototype.set_solo_lectura.call(this, solo_lectura);
		this.vinculo().style.visibility = (solo_lectura) ? "hidden" : "visible";
	}
	
//--------------------------------------------------------------------------------
//Clase ef_editable_multilinea hereda de ef_editable
ef_editable_multilinea.prototype = new ef_editable;
var def = ef_editable_multilinea.prototype;
def.constructor = ef_editable_multilinea;

	function ef_editable_multilinea(id_form, etiqueta, obligatorio, colapsado, masc, maximo, ajustable)	{
		ef_editable.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsado);
		this._maximo = maximo;
		this._ajustable = ajustable;
	}	
	
	def.iniciar = function(id, controlador) { 
		ef.prototype.iniciar.call(this, id, controlador);
		if (this._ajustable) {
			resizeTa.agregar_elemento(this.input());
		}
	}
	
	def.validar = function() {
		if (! ef_editable.prototype.validar.call(this)) {
			return false;
		}
		var elem = this.input();
		if (this._maximo != null && elem.value.length > this._maximo) {
			elem.value = elem.value.substring(0, this._maximo);
		}
		return true;
	}
	
//--------------------------------------------------------------------------------	
toba.confirmar_inclusion('interface/ef_editable');