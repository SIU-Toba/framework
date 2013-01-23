window.dhx_globalImgPath = toba_alias + "/js/dhtmlxCombo/codebase/imgs/";
include_css(toba_alias + '/js/dhtmlxCombo/codebase/dhtmlxcombo.css');

ef_combo_editable.prototype = new ef();
ef_combo_editable.prototype.constructor = ef_combo_editable;
	
/**
 * @class Combo editable equivalente a un tag SELECT en HTML 
 * @constructor
 * @phpdoc Componentes/Efs/toba_ef_combo_editable toba_ef_combo_editable
 */
 
function ef_combo_editable(id_form, etiqueta, obligatorio, colapsado, tamano, modo_filtro, solo_permitir_selecciones, mantiene_estado_cascada) {
	ef.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsado);
	this._tamano = tamano;
	this._es_oculto = false;
	this._buscar_todo = false;
	this._cascadas_ajax = false;	//El esquema de casacadas se maneja de forma particular
	this._mantiene_estado = mantiene_estado_cascada;
	this._modo_filtro = modo_filtro;
	this._msj_ayuda_div;
	this._msj_ayuda_texto = 'Texto a filtrar o (*) para ver todo.';
	this._msj_ayuda_habilitado = true;
	
	this._permite_solo_selecciones = solo_permitir_selecciones;
	this._ultimas_opciones_server = [];
}

	/**
	 * Como el ml multiplexa los efs, necesita tener una referencia al objeto combo original, se mantiene una variable global con el objeto
	 */
	ef_combo_editable.prototype._get_combo = function() {
		return window['combo_editable_'+this._id_form];
	};
	
	/**
	 * Muestra un mensaje al usuario sobre el input.
	 * Solo funciona con navegadores con soporte para HTML5 (atributo placeholder)
	 */
	
	ef_combo_editable.prototype._put_mensaje_ayuda = function(mensaje) {
		if (! this._msj_ayuda_habilitado) return;
		if (mensaje == undefined) {
			mensaje = this._msj_ayuda_texto;
		}
		var inp_field = this.input();
		inp_field.setAttribute('placeholder', mensaje);
	}

	ef_combo_editable.prototype._quitar_mensaje_ayuda = function() {
		if (! this._msj_ayuda_habilitado) return;
		var inp_field = this.input();
		inp_field.setAttribute('placeholder', '');
	};

	//---Comandos

	ef_combo_editable.prototype.iniciar = function(id, contenedor) { 
		ef.prototype.iniciar.call(this, id, contenedor);
		var combo_original = document.getElementById(this._id_form);
		var solo_lectura = combo_original.disabled;
		
		//Recorro las opciones del combo por si viene cargado desde el servidor, asi no elimino opciones validas
		var valor_actual;
		this._ultimas_opciones_server[this._id_form] = [];							//Inicializo la coleccion para el ML
		
		for (var i in combo_original.options) {			
			valor_actual = combo_original.options[i].value;
			if (isset(valor_actual) && valor_actual != 'nopar') {
				this._ultimas_opciones_server[this._id_form].push(valor_actual);
			}
		}
		
		//Crea el combo_editable
		var combo = dhtmlXComboFromSelect(this._id_form, this._tamano);
		window['combo_editable_'+this._id_form] = combo;
		combo.enableFilteringMode(this._modo_filtro);
		
		//Se utiliza al contenedor para que retorne una referencia estatica al ef, para el caso del ml que multiplexa el objeto por las filas
		var callback = contenedor.instancia_ef(this) + '.proceso_tecla()';
		var e = addEvent(this.input(), 'ontecla', callback); 
		combo.attachEvent('onKeyPressed', e);

		//-- El placeholder siempre va aún cuando tenga un estado seteado. Lo maneja HTML5.
		this._put_mensaje_ayuda();

		//Caso solo_lectura de las cascadas
		if (solo_lectura) {
			this.set_solo_lectura();
		}
	};	
	
	ef_combo_editable.prototype.tiene_texto = function()
	{
		var srch_txt = this._get_combo().getComboText();
		return (this.tiene_estado() || srch_txt != '');		
	}
	
	ef_combo_editable.prototype.cuando_cambia_valor = function(callback) {
		var e = addEvent(this.input(), 'oncambio', callback);
		this._get_combo().attachEvent('onChange', e);	
	};	

	ef_combo_editable.prototype._recargar_opciones = function() {
		clearTimeout(this._timer);
		if (this._es_oculto) {return;}
		sel_txt = this._get_combo().getSelectedText();
		srch_txt = this._get_combo().getComboText();
		if ((sel_txt == srch_txt && trim(srch_txt) !== '') || ((trim(srch_txt) === '') && (! this._buscar_todo))) {
			return;
		}
		//Esto es para que traiga todo hasta el limite impuesto
		if (this._buscar_todo && srch_txt === '') {srch_txt = '%';this._buscar_todo = false;}
		var parametros = [srch_txt];
		this._controlador.filtrado_ef_ce_comunicar(this._id, parametros);
	};

	ef_combo_editable.prototype._setear_opcion = function(valor, msg) {
		if (typeof valor == 'undefined' || valor === '') {
			return false;
		}
		if (typeof msg == 'undefined') {
			msg = false;
		}
		var parametros = [valor];
		var resp = this._controlador.filtrado_ef_ce_validar(this._id, parametros);
		
		var combo = this._get_combo();
		combo.render(false);
		indice = combo.getIndexByValue(valor);
		if (indice >= 0 ) {
			combo.selectOption(indice,true,true);
			combo.closeAll();
		} 
		combo.render(true);
		return;
	};
	
	//---Proceso tecla para filtrar
	ef_combo_editable.prototype.proceso_tecla = function() {
		//window['combo_editable_'+this._id_form];
		var combo = this._get_combo();
		//Reemplazo caracteres no válidos
		srch_txt_old = combo.getComboText();
		//Si es '*' busco todo
		this._buscar_todo = (srch_txt_old == '*');
		srch_txt = srch_txt_old.replace(/^[*\]\[?\\+\(\)\/\}\{\"-\']/gi, '');
		if (srch_txt != srch_txt_old) {	
			combo.setComboText(srch_txt);		
		} 
		if (srch_txt == srch_txt_old || this._buscar_todo) {
			clearTimeout(this._timer);
			this._timer = setTimeout(this._bind(this._recargar_opciones), 400);
		}
		return;
	};
	
	//---Consultas		
	
	/**
	 * Tiene algun elemento seleccionado? (distinto del no_seteado)
	 * @type boolean
	 */
	ef_combo_editable.prototype.tiene_estado = function() {
		var valor = this.get_estado();
		
		return valor !== '' &&  valor != apex_ef_no_seteado;	
	};
	
	ef_combo_editable.prototype.get_estado = function(descr) {
		if (typeof descr == 'undefined') {
				descr = false;
		}	
		var combo = this._get_combo();
		var valor = combo.getSelectedValue();
		var desc = combo.getSelectedText();
		var indice = combo.getIndexByValue(valor);
		if (valor === null) {valor = '';}
		if (typeof valor == 'string') {valor = trim(valor);}
		if (indice < 0 || valor === '') {valor = apex_ef_no_seteado; desc='';}
		if (getObjectClass(valor) == 'Array' && valor.length == 0) {valor = apex_ef_no_seteado; desc= '';}
		if (! descr) {
			return valor;
		} else {
			return desc;
		} 
	};
	
	ef_combo_editable.prototype.set_solo_lectura = function(solo_lectura) {
		this._get_combo().disable(typeof solo_lectura == 'undefined' || solo_lectura);
		this._solo_lectura = typeof solo_lectura == 'undefined' || solo_lectura;
	};
	
	ef_combo_editable.prototype.set_permite_escribir = function(permite_escribir) {
		this._get_combo().readonly(typeof permite_escribir == 'undefined' || (! permite_escribir));
		this._permite_escribir = typeof permite_escribir == 'undefined' || (! permite_escribir);
	};
	
	ef_combo_editable.prototype.validar = function () {
		if (! ef.prototype.validar.call(this)) {
			return false;
		}
		var valor = this.get_estado();
		if (this._obligatorio && valor == apex_ef_no_seteado) {
			this._error = 'es obligatorio.';
		    return false;
		} else {
			return true;
		}	
	};

	/**
	 * Devuelve si el ef mantiene o no su valor anterior entre los pedidos de cascada
	 */
	ef_combo_editable.prototype.mantiene_valor_cascada = function() {
		return this._mantiene_estado;
	};
	//---Comandos 
		
	ef_combo_editable.prototype.seleccionar = function () {
		try {
			var combo = this._get_combo();
			combo.render(false);
			combo.openSelect();
			combo.closeAll();
			combo.render(true);
			return true;
		} catch(e) {
			return false;
		}
	};	
	
	ef_combo_editable.prototype.set_estado = function(nuevo) {
		//Ver si existe entre las opciones que tengo
		var indice = this._get_combo().getIndexByValue(nuevo);
		
		if (indice >= 0 ) {//&& indice != '' && indice != 'undefined') {
			//Si existe la seteo
			this._get_combo().selectOption(indice,true,true);
			return;
		} else {//Si no está valido si existe y la traigo
			this._setear_opcion(nuevo,false);
			return;
		} 
	};
	
	ef_combo_editable.prototype.resetear_estado = function() {
		var combo = this._get_combo();
		combo.unSelectOption();					//Deselecciona la opcion
		combo.setComboText('');					//Elimina el estado visible		
		combo.setComboValue('');				//Elimina el estado interno que se envia al server
		combo.callEvent("onChange",[]);
	};
	
	/**
	 * Elimina las opciones disponibles en el combo
	 */		
	ef_combo_editable.prototype.borrar_opciones = function() {
		var combo = this._get_combo();
		combo.closeAll();
		combo.clearAll();
		this.resetear_estado();
	};	
	
	/**
	 * Cambia las opciones del combo. En navegadores como Opera y Chrome si el resultado contiene claves numericas y alfanumericas se rompe el ordenamiento
	 * @param valores Objeto asociativo id=>valor
	 */	
	ef_combo_editable.prototype.set_opciones = function(valores, desplegar) {
		var rs = []
		var i = 0;
		for (id in valores){
			rs[i] = [id, valores[id]];
			i++;
		}
		this.set_opciones_rs(rs, desplegar);
	};
	
	/**
	 * Cambia las opciones del combo y las inserta de forma ordenada
	 * @param valores Array Arreglo de Arreglo con 1er componente clave y 2da valor
	 */		
	ef_combo_editable.prototype.set_opciones_rs = function(valores, desplegar) {
		if (typeof desplegar == 'undefined') {
			desplegar = true;
		}
		var combo = this._get_combo();
		//Borro las opciones que existan
		combo.closeAll();
		combo.clearAll();
		
		//Creo los OPTIONS recuperados
		var datos = [];
		this._ultimas_opciones_server[this._id_form] = [];
		for (var i = 0; i < valores.length; i++) {
			var clave = valores[i][0];
			var valor = valores[i][1];
			if (clave != 'nopar') {
				datos.push([clave, valor]);
				this._ultimas_opciones_server[this._id_form].push(clave);			
			}
		}
		combo.addOption(datos);
		if (desplegar) {
			combo.openSelect();
		}
		if (datos.length === 0) {
			this.resetear_estado();
		}	
		return;
	};
	
	/**
	 * El ef_combo_editable esta oculto?
	 * @return boolean
	 */
	ef_combo_editable.prototype.es_oculto = function() {
		return this._es_oculto;		
	};
		
	/**
	 * Oculta temporalmente el elemento y su etiqueta
	 * @param {boolean} resetar Además de ocultar el elemento borra su estado o valor actual, por defecto false
	 * @see #mostrar
	 */
	ef_combo_editable.prototype.ocultar = function(resetear) {
		if (typeof resetear == 'undefined') {
			resetear = false;
		}
		if (this._es_oculto === true) {return;}
		ef.prototype.ocultar.call(this,resetear);
		this._get_combo().show(false);
		this._es_oculto = true;		
		if (resetear) {
			this.resetear_estado();
		}
	};
	
	/**
	 * Muestra el elemento previamente ocultado
	 * @param {boolean} resetar Además de ocultar el elemento borra su estado o valor actual
	 * @see #ocultar
	 */
	ef_combo_editable.prototype.mostrar = function(mostrar, resetear) {
		if (typeof mostrar == 'undefined') {
			mostrar = true;
		}
		if (mostrar && this._es_oculto === false) {return;}
		ef.prototype.mostrar.call(this,mostrar,resetear);
		if (mostrar) {
			this._get_combo().show(true);
			this._es_oculto = false;
		} else {
			this.ocultar(resetear);	
		}
	};
	
	ef_combo_editable.prototype.input = function() {
		return this._get_combo().DOMelem_input;
	};

	ef_combo_editable.prototype.habilitar_mensaje_ayuda = function() {
		this._msj_ayuda_habilitado = true;
	};

	ef_combo_editable.prototype.inhabilitar_mensaje_ayuda = function() {
		this._msj_ayuda_habilitado = false;
	};
	
	/**
	 * Define si el combo editable solo permitira elegir entre las opciones o tambien dar altas
	 * @param boolean valor
	 */
	ef_combo_editable.prototype.permitir_solo_selecciones = function (valor) {
		this._permite_solo_selecciones = valor;
	}

	ef_combo_editable.prototype.submit = function () {	
		if (! this._solo_lectura) {
			var es_nuevo = this._es_estado_nuevo();
			if (es_nuevo && this._permite_solo_selecciones) {
				this.resetear_estado();
			}
		}
		ef.prototype.submit.call(this);
	};	

	/**
	 * Devuelve si el estado del combo es nuevo o esta entre las opciones
	 * @private
	 */
	ef_combo_editable.prototype._es_estado_nuevo = function()
	{
		var estado_actual = this.get_estado();
		return (! in_array(estado_actual, this._ultimas_opciones_server[this._id_form]));
	}
	
toba.confirmar_inclusion('efs/ef_combo_editable');
