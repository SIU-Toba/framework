//--------------------------------------------------------------------------------
//Clase ef_popup
ef_popup.prototype = new ef();
var def = ef_popup.prototype;
def.constructor = ef_popup;

	function ef_popup(id_form, etiqueta, obligatorio, colapsado, vinculo, param_ventana) {
		ef.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsado);
		this.elementos = [];
		this.elementos_cant = 0;
		this._vinculo = vinculo;
		this._param_ventana = param_ventana;
	}
	
	//---Consultas	

	def.get_tab_index = function () {
		if (this.vinculo()) {
			return this.vinculo().tabIndex;
		}
	};
	
	def.input_desc = function() {
		return document.getElementById(this._id_form + '_desc');	
	};
	
	def.get_estado_con_formato = function() {
		return this.input_desc().value;
	};
	
	def.vinculo = function () {
		return document.getElementById(this._id_form + '_vinculo');		
	};

	def.validar = function () {
		if (! ef.prototype.validar.call(this)) {
			return false;		
		}
		if (this._obligatorio && ereg_nulo.test(this.get_estado())) {
			this._error = 'es obligatorio.';
		    return false;
		}
		return true;
	};

	//---Comandos 	
	
	def.seleccionar = function () {
		if (this.vinculo()) {
			this.vinculo().focus();
		}
	};
			
	def.set_tab_index = function(tab_index) {
		if (this.vinculo()) {
			this.vinculo().tabIndex = tab_index;
		}
	};	
	
	def.abrir_vinculo = function() {
		window.popup_elementos[this._id_form] = this;		
		var param = {'ef_popup': this._id_form, 'ef_popup_valor': this.get_estado()};
		vinculador.agregar_parametros(this._vinculo, param);
		//Se deja que el form. lo invoque asi se puede redefinir
		this._controlador.invocar_vinculo(this._id, this._vinculo);
	};
	
	def.set_solo_lectura = function(solo_lectura) {
		solo_lectura = (typeof solo_lectura != 'undefined' && solo_lectura);
		this.input().disabled = solo_lectura;
		this.vinculo().style.visibility = (solo_lectura) ? "hidden" : "visible";
	};	
	
	def.set_estado = function(clave, desc) {
		var input = this.input();
		input.value = clave;
		if (input.onchange) {
			input.onchange();
		}
		this.input_desc().value = desc;
		input.disabled = false;
		try {
			input.focus();
		} catch (e) {
			//Bug IE
		}
	};

//--------------------------------------------------------------------------------
//Funciones varias

var popup_elementos = [];

function popup_callback(indice, clave, desc)
{
	if (popup_elementos[indice] != 'undefined') {
		setTimeout('popup_elementos["'+indice+'"].set_estado("'+clave+'", "'+desc+'")', 1);
	}
}
	
toba.confirmar_inclusion('efs/ef_popup');