ef_checkbox.prototype = new ef();
ef_checkbox.prototype.constructor = ef_checkbox;

	/**
	 * @class Clase base de los elementos de formulario. El checkbox tiene un valor que depende si esta chequeao o no, por eso set_estado no afecta al check sino sólo a su value.
     * Para cambiar el check usar chequear(boolean) 
	 * @constructor
	 * @phpdoc Componentes/Efs/toba_ef_checkbox toba_ef_checkbox
	 */
	function ef_checkbox(id_form, etiqueta, obligatorio, colapsado, valor_chequeado) {
		this._valor_positivo = valor_chequeado;
		ef.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsado);
	}
	
	//---Consultas		
	
	/**
	 * Si el checkbox esta tildado retorna el value del input (definido en el editor), sino NULL
	 */
	ef_checkbox.prototype.get_estado = function() {
		if (this.chequeado()) {
			return this.input().value;
		} else {
			return null;
		}
	};
	
	ef_checkbox.prototype.activo = function() {
		return this.input().type !='hidden' && !(this.input().disabled);
	};
	
	
	/**
	 * Determina si el elemento esta checado (tildado)
	 * @type boolean
	 */
	ef_checkbox.prototype.chequeado = function() {
		var input = this.input();
		var chequeado;
		if (input.type == 'hidden') {
			//Caso particular del solo-lectura en server
			chequeado = (input.value == this._valor_positivo);
		} else {
			chequeado = input.checked;
		}			
		return chequeado;
	};	
	
	//---Comandos 
		
	/**
	 * Tilda el checkbox
	 * @param {boolean} valor True para tildar, false para destildar
	 * @param {boolean} disparar_eventos Luego del cambio se disparan los eventos que escuchan la modificacion (onclick). Predeterminado true.
	 */
	ef_checkbox.prototype.chequear = function(valor, disparar_eventos) {
		if (typeof disparar_eventos != 'boolean') {
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

	/**
	 * Si esta tildado destilda y viseversa
	 */
	ef_checkbox.prototype.toggle = function(disparar_eventos) {
		if (typeof disparar_eventos != 'boolean') {
			disparar_eventos = true;
		}		
		this.chequear(! this.chequeado(), disparar_eventos);
	};

	
	ef_checkbox.prototype.cuando_cambia_valor = function(callback) {
		addEvent(this.input(), 'onclick', callback);
	};

	/**
	 * Al eliminar el estado, se destilda el checkbox
	 */
	ef_checkbox.prototype.resetear_estado = function() {
		this.chequear(false);
	};
	
toba.confirmar_inclusion('efs/ef_checkbox');
