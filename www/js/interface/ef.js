var apex_ef_no_seteado = 'nopar';

//--------------------------------------------------------------------------------
//Clase ef
function ef(id_form, etiqueta, obligatorio) {
	this.id_form = id_form;	
	this.id_form_orig = id_form;
	this.etiqueta = etiqueta;
	this.obligatorio = obligatorio;
	this.error;							//Mantiene el ultimo error de validación
}
var def = ef.prototype;
def.constructor = ef;

	def.id = function() { 
		return this.id_form;	
	}

	//cuando_cambia_valor (disparar_callback)
	def.cuando_cambia_valor = function(callback) {
		return; 
	}

	//permite tener varias instancias del ef
	def.posicionarse_en_fila = function(fila) {
		this.id_form = this.id_form_orig + fila;
		return this;	
	}
	
	//Comportamientos para los elementos HTML estandar
	def.valor = function() {
		return this.input().value;
	}
	
	def.input = function() {
		return document.getElementById(this.id_form);
	}
	
	def.cambiar_tab = function(tab_index) {
		this.input().tabIndex = tab_index;
	}
	
	def.cuando_cambia_valor = function(callback) { 
		if (! this.input().onchange)	//Para no romper scripts hechos ad-hoc
			this.input().onchange = callback;	
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

	def.validar = function () {
		return true;
	}	
	
	def.obtener_error = function() {
		return this.error;
	}
	
	def.resetear_error = function() {
		delete(this.error);
	}

	
