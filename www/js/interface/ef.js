//--------------------------------------------------------------------------------
//Clase ef
function ef(id_form, etiqueta, obligatorio) {
	this.id_form = id_form;	
	this.id_form_orig = id_form;
	this.etiqueta = etiqueta;
	this.obligatorio = obligatorio;
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

	def.posicionarse_en_fila = function(fila) {
		this.id_form = this.id_form_orig + fila;
		return this;	
	}
	
	def.validar = function () {
		return true;
	}


	
