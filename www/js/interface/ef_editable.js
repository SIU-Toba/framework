//--------------------------------------------------------------------------------
//Clase ef_editable
ef_editable.prototype = new ef;
var def = ef_editable.prototype;
def.constructor = ef_editable;

	function ef_editable(id_form, etiqueta, obligatorio) {
		ef.prototype.constructor.call(this, id_form, etiqueta, obligatorio);
	}

	def.valor = function() {
		return this.input().value;
	}
	
	def.input = function() {
		return document.getElementById(this.id_form);
	}
	
	def.cuando_cambia_valor = function(callback) { 
		if (! this.input().onchange)	//Para no romper scripts hechos ad-hoc
			this.input().onchange = callback;	
	}
	
	def.seleccionar = function () {
		this.input().focus();
		this.input().select();
	}
	
	def.validar = function () {
		if (this.obligatorio && ereg_nulo.test(this.valor())) {
			this.seleccionar();
			alert('El campo ' + this.etiqueta + ' es obligatorio.');
		    return false;
		}
		return true;
	}
	
//--------------------------------------------------------------------------------
//Clase ef_editable_numero
ef_editable_numero.prototype = new ef_editable;
var def = ef_editable_numero.prototype;
def.constructor = ef_editable_numero;

	function ef_editable_numero(id_form, etiqueta, obligatorio)	{
		ef_editable.prototype.constructor.call(this, id_form, etiqueta, obligatorio);
	}
	
	def.valor = function() {
		var valor = ef_editable.prototype.valor.call(this);
		return (valor == '') ? '' : parseFloat(valor);
	}
	
	def.validar = function() {
		if (! ef_editable.prototype.validar.call(this))
			return false;
		var valor = this.valor();
		if (isNaN(valor)) {
			this.seleccionar();
			alert('El campo ' + this.etiqueta + ' es numérico.');
		    return false;
		}
		return true;
	}
	

//--------------------------------------------------------------------------------
//Clase ef_editable_porcentaje
ef_editable_porcentaje.prototype = new ef_editable_numero;
var def = ef_editable_porcentaje.prototype;

	function ef_editable_porcentaje(id_form, etiqueta, obligatorio)	{
		ef_editable_numero.prototype.constructor.call(this, id_form, etiqueta, obligatorio);
	}	
	
	def.validar = function() {
		if (! ef_editable_numero.prototype.validar.call(this))
			return false;
		var valor = this.valor();
		if (valor != '' && (valor < 0 || valor > 100)) {
			this.seleccionar();
			alert('El campo ' + this.etiqueta + ' posee un porcentaje fuera de rango.');
		    return false;
		}
		return true;
	}
	
//--------------------------------------------------------------------------------
//Clase ef_editable_clave
ef_editable_clave.prototype = new ef_editable;
var def = ef_editable_clave.prototype;

	function ef_editable_clave(id_form, etiqueta, obligatorio)	{
		ef_editable.prototype.constructor.call(this, id_form, etiqueta, obligatorio);
	}	

	def.validar = function() {
		if (! ef_editable.prototype.validar.call(this))
			return false;
		
		var orig = document.getElementById(this.id_form);
		var test = document.getElementById(this.id_form + '_test');
		if (orig.value != test.value)
		{
			orig.value = test.value = '';
			this.seleccionar();
			alert('El campo ' + this.etiqueta + ': Las contraseñas no coinciden.');
		    return false;
		}		
		return true;
	}
	
//--------------------------------------------------------------------------------
//Clase ef_editable_fecha
ef_editable_fecha.prototype = new ef_editable;
var def = ef_editable_fecha.prototype;

	function ef_editable_fecha(id_form, etiqueta, obligatorio)	{
		ef_editable.prototype.constructor.call(this, id_form, etiqueta, obligatorio);
	}	

	def.validar = function() {
		if (! ef_editable.prototype.validar.call(this))
			return false;
		if (! validar_fecha(this.valor())) {
			this.seleccionar();
		    return false;
		}		
		return true;
	}	
	
//--------------------------------------------------------------------------------	