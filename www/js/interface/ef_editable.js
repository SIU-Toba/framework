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
			this._error = 'El campo ' + this._etiqueta + ' es obligatorio.';
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

	function ef_editable_numero(id_form, etiqueta, obligatorio, masc) {
		ef_editable.prototype.constructor.call(this, id_form, etiqueta, obligatorio);
		if (masc)
			this._forma_mascara = (masc.trim().toLowerCase() != 'no') ? masc : null;
		else
			this._forma_mascara = '###.###,##';
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
	
	def.validar = function() {
		if (! ef_editable.prototype.validar.call(this))
			return false;
		if (isNaN(this.valor())) {
			this._error = 'El campo ' + this._etiqueta + ' debe ser numérico.';
		    return false;
		}
		return true;
	}

//--------------------------------------------------------------------------------
//Clase ef_editable_moneda hereda de ef_editable_numero
ef_editable_moneda.prototype = new ef_editable_numero();
var def = ef_editable_moneda.prototype;
	function ef_editable_moneda(id_form, etiqueta, obligatorio, masc)	{
		ef_editable_numero.prototype.constructor.call(this, id_form, etiqueta, obligatorio);
		this._forma_mascara = (masc) ? masc : '$ ###.###,00';		
	}

//--------------------------------------------------------------------------------
//Clase ef_editable_porcentaje hereda de ef_editable_numero
ef_editable_porcentaje.prototype = new ef_editable_numero();
var def = ef_editable_porcentaje.prototype;
	function ef_editable_porcentaje(id_form, etiqueta, obligatorio, masc)	{
		ef_editable_numero.prototype.constructor.call(this, id_form, etiqueta, obligatorio, masc);
	}
	
	def.validar = function() {
		if (! ef_editable_numero.prototype.validar.call(this))
			return false;
		var valor = this.valor();
		if (valor != '' && (valor < 0 || valor > 100)) {
			this._error = 'El campo ' + this._etiqueta + ' posee un porcentaje fuera de rango.';
		    return false;
		}
		return true;
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
			this.error = 'El campo ' + this._etiqueta + ': Las contraseñas no coinciden.';
		    return false;
		}		
		return true;
	}
	
	def.cambiar_valor = function (nuevo) {
		this.input().value = nuevo;
		document.getElementById(this._id_form + '_test').value = nuevo;
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
	
	def.validar = function() {
		if (! ef_editable.prototype.validar.call(this))
			return false;
		var valida = validar_fecha(this.valor(), false);
		if (valida != true) {
			this._error = valida;
		    return false;
		}		
		return true;
	}	
	
//--------------------------------------------------------------------------------	