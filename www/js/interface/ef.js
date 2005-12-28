var apex_ef_no_seteado = 'nopar';
var apex_ef_total = 's';

//--------------------------------------------------------------------------------
//Clase ef
function ef(id_form, etiqueta, obligatorio, colapsable) {
	this._id = null;						//El id lo asigna el formulario cuando lo inicia
	this._id_form = id_form;				//El id_form es la clave que permite identificarlo univocamente
	this._id_form_orig = this._id_form;
	this._etiqueta = etiqueta;
	this._obligatorio = obligatorio;
	this._error = null;
	this._colapsable = colapsable;
}
var def = ef.prototype;
def.constructor = ef;

	//---Servicios de inicio y finalización 
	def.iniciar = function(id) {
		this._id = id;
	}

	def.validar = function () {
		return true;
	}	
	
	def.submit = function () {
		var input = this.input();
		if (input && input.disabled)
			input.disabled = false;
	}		
	
	//---Consultas	
	def.id = function() { 
		return this._id;	
	}
	
	def.valor = function() {
		return this.input().value;
	}
	
	def.valor_formateado = function() {
		return this.valor();
	}	

	//Retorna el formato en modo texto de valor
	def.formato_texto = function (valor) {
		return valor;
	}

	def.activo = function() {
		return !(this.input().disabled);
	}
		
	def.input = function() {
		return document.getElementById(this._id_form);
	}
	
	def.nodo = function() {
		return document.getElementById('nodo_' + this._id_form);			
	}

	def.tab = function () {
		return this.input().tabIndex;
	}

	def.error = function() {
		return this._error;
	}
	
	//---Comandos 
	def.cambiar_expansion = function(expandir) {
		if (this._colapsable) {
			if (expandir) 
				this.nodo().style.display = '';
			else
				this.nodo().style.display = 'none';
		}
	}
	
	def.set_error = function(error) {
		this._error = error;
	}
	
	def.resetear_error = function() {
		delete(this._error);
	}

	def.seleccionar = function () {
		try {
			this.input().focus();
			this.input().select();
			return true;
		} catch(e) {
			return false;
		}
	}

	def.resaltar = function(texto, izq) {
		var cont = document.getElementById('cont_' + this._id_form);
		var warn = document.getElementById('ef_warning_' + this._id_form);
		if (! warn) {
			izq = (typeof izq == 'number') ? izq : 14;
			var clon = document.getElementById('ef_warning').cloneNode(true);
			clon.id = 'ef_warning_' + this._id_form;
			var pos = getElementPosition(cont);
			clon.style.left = (pos['left']-izq) + 'px';
			clon.style.display = '';
			cont.insertBefore(clon, cont.firstChild);
			warn = document.getElementById('ef_warning_' + this._id_form);
		}
		warn.title = texto;
		window.status = texto;
	}
	
	def.no_resaltar = function() {
		var cont = document.getElementById('cont_' + this._id_form);
		var warn = document.getElementById('ef_warning_' + this._id_form);
		if (warn) {
			elem = cont.removeChild(warn);
			delete(elem);
			window.status = '';
		}
	}

	def.ocultar = function() {
		this.nodo().style.display = 'none';	
	}
	
	def.mostrar = function() {
		this.nodo().style.display = '';	
	}

	def.desactivar = function () {
		this.input().disabled = true;
	}

	def.activar = function () {
		this.input().disabled = false;		
	}
	
	
	def.cambiar_tab = function(tab_index) {
		if (this.input())
			this.input().tabIndex = tab_index;
	}
	
	def.cambiar_valor = function(nuevo) {
		this.input().value = nuevo;
		if (this.input().onchange)
			this.input().onchange();
	}
	
	//cuando_cambia_valor (disparar_callback)
	def.cuando_cambia_valor = function(callback) { 
		if (this.input())
			addEvent(this.input(), 'onchange', callback);
	}

	//Multiplexacion, permite tener varias instancias del ef
	def.ir_a_fila = function(fila) {
		this._id_form = this._id_form_orig + fila;
		return this;	
	}
	
	//Multiplexacion, deja sin seleccionar la fila en la que está 
	def.sin_fila = function() {
		this._id_form = this._id_form_orig;
		return this;
	}	

	
//--------------------------------------------------------------------------------
//Clase ef_fijo
ef_fijo.prototype = new ef;
var def = ef_fijo.prototype;
def.constructor = ef_fijo;

	function ef_fijo(id_form, etiqueta, obligatorio, colapsado) {
		ef.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsado);
	}
	
	def.cambiar_valor = function(nuevo) {
		this.input().innerHTML = nuevo;
	}	