//--------------------------------------------------------------------------------
//Clase ef_editable
ef_editable.prototype = new ef;
var def = ef_editable.prototype;
def.constructor = ef_editable;

	function ef_editable(id_form, etiqueta, obligatorio, masc) {
		ef.prototype.constructor.call(this, id_form, etiqueta, obligatorio);
		this._forma_mascara = (masc && masc.trim().toLowerCase() != 'no') ? masc : null;
		this._mascara = null;
	}

	def.iniciar = function(id) { 
		ef.prototype.iniciar.call(this, id);
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
	}
	
	def.validar = function () {
		if (this._obligatorio && ereg_nulo.test(this.valor())) {
			this._error = this._etiqueta + ' es obligatorio.';
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
		if (this._mascara)
			this.input().value = this._mascara.format(nuevo, false, true);
		else
			return ef.prototype.cambiar_valor.call(this, nuevo);	
		if (this.input().onblur) {
			this.input().onblur();
		}
	}	
	
	//cuando_cambia_valor (disparar_callback)
	def.cuando_cambia_valor = function(callback) { 
		addEvent(this.input(), 'onblur', callback);
	}

	
//--------------------------------------------------------------------------------
//Clase ef_editable_numero hereda de ef_editable
ef_editable_numero.prototype = new ef_editable;
var def = ef_editable_numero.prototype;
def.constructor = ef_editable_numero;

	function ef_editable_numero(id_form, etiqueta, obligatorio, masc, rango, mensaje) {
		ef_editable.prototype.constructor.call(this, id_form, etiqueta, obligatorio);
		if (masc)
			this._forma_mascara = (masc.trim().toLowerCase() != 'no') ? masc : null;
		else
			this._forma_mascara = '###.###,##';
		this._rango = rango;					//[0] => inferior [1] => superior
		this._mensaje = mensaje;				//Mensaje a mostrar cuando no se valida el número
	}
	
	def.iniciar = function(id) { 
		ef.prototype.iniciar.call(this, id);
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
		valor = this.valor();
		if (typeof valor != 'number')
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
			this._error = this._etiqueta + ' debe ser numérico.';
		    return false;
		}
		if (!this.validar_rango()) {
			this._error = this._etiqueta + this._mensaje;
		    return false;
		}
		return true;
	}

//--------------------------------------------------------------------------------
//Clase ef_editable_moneda hereda de ef_editable_numero
ef_editable_moneda.prototype = new ef_editable_numero();
var def = ef_editable_moneda.prototype;
	function ef_editable_moneda(id_form, etiqueta, obligatorio, masc, rango, mensaje)	{
		ef_editable_numero.prototype.constructor.call(this, id_form, etiqueta, obligatorio, masc, rango, mensaje);
		this._forma_mascara = (masc) ? masc : '$ ###.###,00';		
	}

//--------------------------------------------------------------------------------
//Clase ef_editable_porcentaje hereda de ef_editable_numero
ef_editable_porcentaje.prototype = new ef_editable_numero();
var def = ef_editable_porcentaje.prototype;
	function ef_editable_porcentaje(id_form, etiqueta, obligatorio, masc, rango, mensaje)	{
		ef_editable_numero.prototype.constructor.call(this, id_form, etiqueta, obligatorio, masc, rango, mensaje);
	}
	
	def.formato_texto = function(valor) {
		return (ef_editable_numero.prototype.formato_texto.call(this, valor) + ' %');
	}
	
//--------------------------------------------------------------------------------
//Clase ef_editable_clave hereda de ef_editable
ef_editable_clave.prototype = new ef_editable;
var def = ef_editable_clave.prototype;

	function ef_editable_clave(id_form, etiqueta, obligatorio)	{
		ef_editable.prototype.constructor.call(this, id_form, etiqueta, obligatorio);
	}	

	def.validar = function() {
		if (! ef_editable.prototype.validar.call(this))
			return false;
		var orig = this.input();
		var test = document.getElementById(this._id_form + '_test');
		if (orig.value != test.value)
		{
			this._error = this._etiqueta + ': Las contraseñas no coinciden.';
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
	
//--------------------------------------------------------------------------------
//Clase ef_editable_fecha hereda de ef_editable
ef_editable_fecha.prototype = new ef_editable;
var def = ef_editable_fecha.prototype;

	function ef_editable_fecha(id_form, etiqueta, obligatorio, masc)	{
		ef_editable.prototype.constructor.call(this, id_form, etiqueta, obligatorio);
		this._forma_mascara = (masc) ? masc : 'dd/mm/yyyy';
	}	
	
	def.iniciar = function(id) {
		ef.prototype.iniciar.call(this, id);
		this._mascara = new mascara_fecha(this._forma_mascara);
		this._mascara.attach(this.input());
	}
	
	def.valor = function() {
		return this.input().value;
	}
	
	def.fecha = function() {
		if (this.validar()) {
			var arr = this.valor().split('/');
			return fecha = new Date(arr[2], arr[1] - 1, arr[0]);
		}
		return null;
	}
	
	def.validar = function() {
		if (! ef_editable.prototype.validar.call(this))
			return false;
		try {
			var valida = validar_fecha(this.valor(), false);
		} catch (e) {
			valida = "no es una fecha válida";
		}
		if (valida != true) {
			this._error = this._etiqueta + ': ' + valida;
		    return false;
		}		
		return true;
	}	
	
//--------------------------------------------------------------------------------	