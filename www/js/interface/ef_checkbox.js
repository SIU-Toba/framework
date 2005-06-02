//--------------------------------------------------------------------------------
//Clase ef_checkbox
//	El checkbox tiene un valor que depende si esta chequeao o no, por eso cambiar_valor no afecta al check sino sólo a su value
//	Para cambiar el check usar chequear(boolean) 
ef_checkbox.prototype = new ef;
var def = ef_checkbox.prototype;
def.constructor = ef_checkbox;

	function ef_checkbox(id_form, etiqueta, obligatorio) {
		ef.prototype.constructor.call(this, id_form, etiqueta, obligatorio);
	}
	
	//cuando_cambia_valor (disparar_callback)
	def.cuando_cambia_valor = function(callback) {
		addEvent(this.input(), 'onclick', callback);
	}

	def.valor = function() {
		if (this.chequeado())
			return this.input().value;
		else
			return null;
	}
	
	def.chequear = function(valor) {
		if (typeof valor != 'boolean')
			valor = true;
		var input = this.input();
		input.checked = valor;
		if (input.onclick)
			input.onclick();
	}
	
	def.chequeado = function() {
		return this.input().checked;
	}	