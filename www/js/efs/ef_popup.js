
ef_popup.prototype = new ef();
ef_popup.prototype.constructor = ef_popup;

	/**
	 * @class Permite seleccionar un valor a partir de un item de popup. Pensado para conjunto grandes de valores
	 * @constructor
	 * @phpdoc Componentes/Efs/toba_ef_popup toba_ef_popup
	 */
	function ef_popup(id_form, etiqueta, obligatorio, colapsado, vinculo, param_ventana) {
		ef.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsado);
		this.elementos = [];
		this.elementos_cant = 0;
		this._vinculo = vinculo;
		this._param_ventana = param_ventana;
	}
	
	//---Consultas	

	ef_popup.prototype.get_tab_index = function () {
		if (this.vinculo()) {
			return this.vinculo().tabIndex;
		}
	};
	
	/**
	 * Retorna una referencia al input HTML que contiene la descripción del elemento (el edit-box visible)
	 * @type <a href=http://developer.mozilla.org/en/docs/DOM:element>element</a>
	 */
	ef_popup.prototype.input_desc = function() {
		return document.getElementById(this._id_form + '_desc');	
	};
	
	ef_popup.prototype.get_estado_con_formato = function() {
		return this.input_desc().value;
	};
	
	/**
	 * Retorna una referencia al tag HTML que contiene el link para abrir el popup
	 * @type <a href=http://developer.mozilla.org/en/docs/DOM:element>element</a>	 
	 */
	ef_popup.prototype.vinculo = function () {
		return document.getElementById(this._id_form + '_vinculo');		
	};
	
	ef_popup.prototype.get_id_vinculo = function () {
		return this._vinculo;
	};
	
	ef_popup.prototype.validar = function () {
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
	
	ef_popup.prototype.seleccionar = function () {
		if (this.vinculo()) {
			this.vinculo().focus();
		}
	};
			
	ef_popup.prototype.set_tab_index = function(tab_index) {
		if (this.vinculo()) {
			this.vinculo().tabIndex = tab_index;
		}
	};	
	
	/**
	 * Abre la operación de popup asociada pasandole los valores actuales del elemento
	 * Similar a clickear sobre el icono de apertura de popup
	 * En formulario es posible atrapar el vinculo de apertura y modificarlo
	 */
	ef_popup.prototype.abrir_vinculo = function() {
		window.popup_elementos[this._id_form] = this;		
		var param = this._controlador.get_valores_maestros(this._id);
		param[this._id] = this.get_estado();
		param['ef_popup_valor'] = this.get_estado();
		param.ef_popup = this._id_form;
		
		vinculador.agregar_parametros(this._vinculo, param);
		//Se deja que el form. lo invoque asi se puede redefinir
		this._controlador.invocar_vinculo('ef_' + this._id, this._vinculo);
	};
	
	ef_popup.prototype.set_solo_lectura = function(solo_lectura) {
		solo_lectura = (typeof solo_lectura != 'undefined' && solo_lectura);
		this._solo_lectura = solo_lectura;		
		this.input().disabled = solo_lectura;
		if (this.vinculo()) {
			this.vinculo().style.visibility = (solo_lectura) ? "hidden" : "visible";
		}
	};	
	
	
	/**
	 * Cambia las opciones disponibles de selección 
	 */
	ef_popup.prototype.set_opciones = function(opciones) 
	{
		if (typeof opciones == 'object') {
			this.set_opciones_rs(opciones);	
		} else if (typeof opciones != 'boolean') {
			var opciones_rs = [opciones, opciones];			
			this.set_opciones_rs(opciones_rs);
		}
	};
	
	/**
	 * Cambia las opciones disponibles de selección 
	 */
	ef_popup.prototype.set_opciones_rs = function(valores) 
	{		
		var hay_datos = false;
		if (getObjectClass(valores) == 'Array') {
			var id = valores[0];
			var valor = valores[1];
			if (id !=  apex_ef_no_seteado) {
				hay_datos = true;
			}
		}
		if (hay_datos) {
			this.set_estado(id, valor);
		}
		this.activar();		
	};	
	
		
	/**
	 * Cambia el estado actual del elemento
	 * @param clave Nuevo valor o clave 
	 * @param desc Nueva descripción del valor o clave
	 */
	ef_popup.prototype.set_estado = function(clave, desc, disparar_eventos) {
		if(! isset(desc)) {
			desc = '';
		}
		if(! isset(clave)) {
			clave = '';
		}
		var input = this.input();
		input.value = clave;
		if ((!isset(disparar_eventos) || disparar_eventos) && input.onchange) {
			input.onchange();
		}
		this.input_desc().value = (typeof  desc == 'string') ? desc.decodeEntities(): desc;
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
	//---Evita error en firefox 1.5 porque la ventana hija se ha cerrado y este proceso se empieza a disparar desde alli
	if (popup_elementos[indice] != 'undefined') {
		setTimeout('popup_elementos["'+indice+'"].set_estado("'+clave+'", "'+desc+'")', 1);
	}
}
	
toba.confirmar_inclusion('efs/ef_popup');