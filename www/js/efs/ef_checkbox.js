//--------------------------------------------------------------------------------
//Clase ef_checkbox
//	El checkbox tiene un valor que depende si esta chequeao o no, por eso set_estado no afecta al check sino sólo a su value
//	Para cambiar el check usar chequear(boolean) 
ef_checkbox.prototype = new ef();
var def = ef_checkbox.prototype;
def.constructor = ef_checkbox;

	function ef_checkbox(id_form, etiqueta, obligatorio, colapsado) {
		ef.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsado);
	}
	
	//---Consultas		
	
	def.get_estado = function() {
		if (this.chequeado()) {
			return this.input().value;
		} else {
			return null;
		}
	};
	
	def.activo = function() {
		return this.input().type !='hidden' && !(this.input().disabled);
	};
	
	def.chequeado = function() {
		var input = this.input();
		var chequeado;
		if (input.type == 'hidden') {
			//Caso particular del solo-lectura en server
			chequeado = input.value;
		} else {
			chequeado = input.checked;
		}			
		return chequeado;
	};	
	
	//---Comandos 
		
	def.chequear = function(valor, disparar_eventos) {
		if (typeof eventos != 'boolean') {
			disparar_eventos = true;
		}
		if (typeof valor != 'boolean') {
			valor = true;
		}
		var input = this.input();
		input.checked = valor;
		if (disparar_eventos && input.onclick) {
			input.onclick();
		}
	};

	//cuando_cambia_valor (disparar_callback)
	def.cuando_cambia_valor = function(callback) {
		addEvent(this.input(), 'onclick', callback);
	};

	def.resetear_estado = function() {
		this.chequear(false);
	};
	
toba.confirmar_inclusion('interface/ef_checkbox');
