//--------------------------------------------------------------------------------
//Clase ef_popup
ef_popup.prototype = new ef;
var def = ef_popup.prototype;
def.constructor = ef_popup;

	function ef_popup(id_form, etiqueta, obligatorio, colapsado, vinculo, param_ventana) {
		ef.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsado);
		this.elementos = new Array();
		this.elementos_cant = 0;
		this._vinculo = vinculo;
		this._param_ventana = param_ventana;
		
	}

	def.validar = function () {
		if (! ef.prototype.validar.call(this))
			return false;		
		if (this._obligatorio && ereg_nulo.test(this.valor())) {
			this._error = 'es obligatorio.';
		    return false;
		}
		return true;
	}

	def.seleccionar = function () {
		if (this.vinculo())
			this.vinculo().focus();
	}
		
	def.tab = function () {
		if (this.vinculo())
			return this.vinculo().tabIndex;
	}
			
	def.cambiar_tab = function(tab_index) {
		if (this.vinculo())
			this.vinculo().tabIndex = tab_index;
	}	
	
	def.input_desc = function() {
		return document.getElementById(this._id_form + '_desc');	
	}
	
	def.valor_formateado = function() {
		return this.input_desc().value;
	}
	
	def.vinculo = function () {
		return document.getElementById(this._id_form + '_vinculo');		
	}
	
	def.abrir_vinculo = function() {
		window.popup_elementos[this._id_form] = this;		
		var param = {'ef_popup': this._id_form, 'ef_popup_valor': this.valor()};
		vinculador.agregar_parametros(this._vinculo, param);
		//Se deja que el form. lo invoque asi se puede redefinir
		this._controlador.invocar_vinculo(this._id, this._vinculo);
	}
	
	def.set_solo_lectura = function(solo_lectura) {
		solo_lectura = (typeof solo_lectura != 'undefined' && solo_lectura);
		this.input().disabled = solo_lectura;
		this.vinculo().style.visibility = (solo_lectura) ? "hidden" : "visible";
	}	
	
	def.cambiar_valor = function(clave, desc) {
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
	}

//--------------------------------------------------------------------------------
//Funciones varias

var popup_elementos = new Array();

function popup_callback(indice, clave, desc)
{
	if (popup_elementos[indice] != 'undefined') {
		setTimeout('popup_elementos["'+indice+'"].cambiar_valor("'+clave+'", "'+desc+'")', 1);
	}
}
	
toba.confirmar_inclusion('interface/ef_popup');