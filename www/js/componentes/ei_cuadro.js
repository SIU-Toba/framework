
ei_cuadro.prototype = new ei();
ei_cuadro.prototype.constructor = ei_cuadro;

/**
 * @class Un ei_cuadro es una grilla de registros.
 * @constructor
 * @phpdoc Componentes/Eis/toba_ei_cuadro toba_ei_cuadro
 */
function ei_cuadro(id, instancia, input_submit, filas, ids_eventos_multiple) {
	this._id = id;
	this._instancia = instancia;				//Nombre de la instancia del objeto, permite asociar al objeto con el arbol DOM
	this._input_submit = input_submit;			//Campo que se setea en el submit del form
	this._filas = filas;
	this._ids_eventos_multiple = ids_eventos_multiple;
	this._selector_fila_selec = null;			//Mantiene la fila seleccionada actualmente
	this._filas_disponibles = [];					//Mantiene las filas disponibles para el ordenamiento
	this._fila_seleccion_anterior = null;
}
	
	//---Submit 
	ei_cuadro.prototype.submit = function() {
		if (this.controlador && !this.controlador.en_submit()) {
			return this.controlador.submit();
		}		
		if (this._evento) {
			switch (this._evento.id) {
				case 'cambiar_pagina':
					document.getElementById(this._input_submit + '__pagina_actual').value = this._evento.parametros;
					break;
				case 'ordenar':
					document.getElementById(this._input_submit + '__orden_columna').value = this._evento.parametros.orden_columna;
					document.getElementById(this._input_submit + '__orden_sentido').value = this._evento.parametros.orden_sentido;
					break;
				case 'ordenar_multiple':
					document.getElementById(this._input_submit + '__ordenamiento_multiple').value = this._evento.parametros;
				break;
				default:
					if (this._evento.parametros) {
						document.getElementById(this._input_submit + '__seleccion').value = this._evento.parametros;
					}										
					if (this._evento.parametros_extra) {
						document.getElementById(this._input_submit + '__extra').value = this._evento.parametros_extra;
					}					
					break;				
			}
			//Marco la ejecucion del evento para que la clase PHP lo reconozca
			document.getElementById(this._input_submit).value = this._evento.id;
		}

		//Aca deberia ciclar por los eventos multiples e ir asignando a los hidden los valores
		for (var evt  in this._ids_eventos_multiple) {
			var nombre_evt = this._ids_eventos_multiple[evt];
			var parametros = this.get_ids_seleccionados(nombre_evt);
			if (document.getElementById(this._input_submit+'__'+nombre_evt) != null) {				//Si existe el hidden para el evento
				document.getElementById(this._input_submit+'__'+nombre_evt).value = parametros.join(toba_hilo_separador_interno);
			}
		}
	};
	

	ei_cuadro.prototype.colapsar_corte = function(corte)
	{
		var objeto = document.getElementById(corte);
		toggle_nodo(objeto);
	};
	
	
	//--------------------------------------
	//-----    Seleccion Multiple
	//--------------------------------------

	/**
	 * Informa al componente la presencia de un nuevo evento
	 * @param {evento_ei} evento
	 * @param {boolean} hacer_submit Luego de informar el evento, se inicia el proceso de submit (por defecto true)
	 */
	ei_cuadro.prototype.set_evento = function(evento, hacer_submit, input) {
		if (typeof hacer_submit == 'undefined') {
			hacer_submit = true;
		}
		ei.prototype.set_evento.call(this, evento, hacer_submit);
		if (!hacer_submit) {
			var fila = input.parentNode.parentNode.parentNode;
			if (in_array(evento, this._ids_eventos_multiple)) {
				this.seleccionar_fila_multiple(input, fila);
			} else {
				this.seleccionar_fila(input, fila);
			}
		}
	};	
	
	ei_cuadro.prototype.seleccionar = function(fila, id_evento)
	{
		var check = $$(this._input_submit + fila + '_' + id_evento);
		if (check) {
			check.checked = !check.checked;
			check.onclick();
		}
	};
		
	ei_cuadro.prototype.get_ids_seleccionados = function(id_evento)
	{
		var seleccion = [];
		for (i in this._filas) {
			var check = $$(this._input_submit + this._filas[i] + '_' + id_evento);
			if (check && check.checked) {
				seleccion.push(check.value);
			}
		}
		return seleccion;
	};

	ei_cuadro.prototype.ir_a_pagina = function(valor)
	{
		this.set_evento(new evento_ei('cambiar_pagina', '','', valor), true);
	}

	ei_cuadro.prototype.seleccionar_fila_multiple = function(input, fila)
	{
		if (input.checked) {
			agregar_clase_css(fila, 'ei-cuadro-fila-sel');
		} else {
			quitar_clase_css(fila, 'ei-cuadro-fila-sel');
		}
	}

	ei_cuadro.prototype.seleccionar_fila = function(input, fila)
	{		
		if (this._fila_seleccion_anterior != null) {
			quitar_clase_css(this._fila_seleccion_anterior, 'ei-cuadro-fila-sel');
		}	
		this._fila_seleccion_anterior = fila;		
		agregar_clase_css(fila, 'ei-cuadro-fila-sel');
	}

	ei_cuadro.prototype.invertir_seleccionados = function(id_evento)
	{
		if (! in_array(id_evento, this._ids_eventos_multiple)) {
			return false;
		}
		
		for (i in this._filas) {
			this.seleccionar(this._filas[i], id_evento);
		}
	};
	
	ei_cuadro.prototype.seleccionar_todos = function(id_evento)
	{
		if (! in_array(id_evento, this._ids_eventos_multiple)) {
			return false;
		}
		
		for (i in this._filas) {
			var check = $$(this._input_submit + this._filas[i] + '_' + id_evento);
			if (check && !check.checked) {
				this.seleccionar(this._filas[i], id_evento);
			}
		}
	};
	ei_cuadro.prototype.deseleccionar_todos = function(id_evento)
	{
		if (! in_array(id_evento, this._ids_eventos_multiple)) {
			return false;
		}
		
		for (i in this._filas) {
			var check = $$(this._input_submit + this._filas[i] + '_' + id_evento);
			if (check && check.checked) {
				this.seleccionar(this._filas[i], id_evento);
			}
		}
	};
	//------------------------------------------------------------------------------------------------------------------
	//							FUNCIONES PARA EL SELECTOR DE ORDENAMIENTO MULTIPLE
	//------------------------------------------------------------------------------------------------------------------
	/**
	 *  Metodo utilizado para lanzar el evento de ordenamiento multiple columna
	 *  @param {array} valores Arreglo con formato array('columna' => 'sentido')
	 */
	ei_cuadro.prototype.set_ordenamiento_multiple = function (valores)
	{
		var datos = [];
		var par_col_orden = '';
		for (var columna in valores) {
			if (valores[columna] !== null) {
					par_col_orden = columna + toba_hilo_separador_interno + valores[columna];
					datos.push(par_col_orden);
			} else {
				notificacion.agregar('Falta definir un sentido para la columna');
				notificacion.mostrar();
				notificacion.limpiar();
				return false;
			}
		}
		var parametros = datos.join(toba_hilo_separador);
		this.set_evento(new evento_ei('ordenar_multiple', false, false, parametros), true);
	}

	/**
	 * Metodo que muestra el selector de ordenamiento al usuario
	 * @param {array} filas Arreglo con los id de columna que estan disponibles
	 * @ignore
	 */
	ei_cuadro.prototype.mostrar_selector = function(filas)
	{
		this._filas_disponibles = filas;
		var html = document.getElementById(this._input_submit  + '_selector_ordenamiento').innerHTML;
		notificacion.mostrar_ventana_modal('Seleccione el criterio de ordenamiento a aplicar', html, '400px', 'overlay(true)');
	}

	/**
	 * Metodo que deja activa una fila en particular para poder seleccionar el sentido
	 * @param {string} fila
	 * @ignore
	 */
	ei_cuadro.prototype.activar_fila_selector = function(fila)
	{
		var activo = document.getElementById('check_' + fila).checked;
		var radio_asc = document.getElementById(fila + '0');
		var radio_des = document.getElementById(fila + '1');

		radio_asc.disabled = (! activo);
		radio_des.disabled = (! activo);
		//document.getElementById(fila + '0').disabled = (!activo);
	}

	/**
	 * Metodo que selecciona una fila como la actual y la marca en la interfase
	 * @param {string} fila
	 * @ignore
	 */
	ei_cuadro.prototype.seleccionar_fila_selector = function(fila)
	{
		if (isset(this._selector_fila_selec) && (fila != this._selector_fila_selec)) {
			this.deseleccionar_fila_selector();
		}
		this._selector_fila_selec = fila;
		var nodo = document.getElementById('fila_' + this._selector_fila_selec);
		reemplazar_clase_css(nodo, 'ei-ml-fila', 'ei-ml-fila-selec');
	}

	/**
	 * Metodo para deseleccionar la fila actual, la desmarca en la interface
	 * @ignore
	 */
	ei_cuadro.prototype.deseleccionar_fila_selector = function()
	{
		var nodo = document.getElementById('fila_' + this._selector_fila_selec);
		reemplazar_clase_css(nodo, 'ei-ml-fila-selec', 'ei-ml-fila');
	}

	/**
	 * Metodo que sube una fila en la interfase y en definitiva su prioridad para el ordenamiento
	 * @ignore
	 */
	ei_cuadro.prototype.subir_fila_selector = function()
	{
		var posicion = this.obtener_posicion_actual(this._selector_fila_selec);
		if (posicion != null) {
			var anterior = posicion - 1;
			if (this._filas_disponibles[anterior]) {
				this.intercambiar_posiciones(anterior, posicion);
			}
		}
	}

	/**
	 * Metodo que baja una fila en la interfase y en definitiva su prioridad para el ordenamiento
	 * @ignore
	 */
	ei_cuadro.prototype.bajar_fila_selector = function()
	{
		var posicion = this.obtener_posicion_actual(this._selector_fila_selec);
		if (posicion != null) {
			var anterior = posicion;
			anterior++;
			if (this._filas_disponibles[anterior]) {
				this.intercambiar_posiciones(posicion, anterior);
			}
		}
	}

	/**
	 * Metodo que dada una fila obtiene su prioridad actual
	 * @ignore
	 */
	ei_cuadro.prototype.obtener_posicion_actual = function(fila)
	{
		for(var indice in this._filas_disponibles) {
			if (this._filas_disponibles[indice] == fila) {
				return indice;
			}
		}
		return null;
	}

	/**
	 * Metodo que intercambia las posiciones de dos filas en la interface,
	 * tambien intercambia sus prioridades para el orden final
	 * @ignore
	 */
	ei_cuadro.prototype.intercambiar_posiciones = function(anterior, nueva)
	{
		var fila_ant = this._filas_disponibles[anterior];
		var fila_nueva = this._filas_disponibles[nueva];

		var nodo_ant = document.getElementById('fila_' + fila_ant);
		var nodo_nuevo = document.getElementById('fila_' + fila_nueva);

		nodo_nuevo.swapNode(nodo_ant);
		this._filas_disponibles[anterior] = fila_nueva;
		this._filas_disponibles[nueva] = fila_ant;
	}

	/**
	 * Metodo que obtiene la prioridad final de ordenamiento, arma el arreglo
	 * con las columnas y su correspondiente sentido. Finalmente llama
	 * al metodo que dispara el evento.
	 * @ignore
	 */
	ei_cuadro.prototype.aplicar_criterio_ordenamiento = function()
	{
		var seleccion_actual = [];
		var check;
		var sentido;
		var columna = '';
		for (var indice in this._filas_disponibles)
		{
			columna = this._filas_disponibles[indice];
			check = document.getElementById('check_' + columna).checked;
			if (check) {
				sentido = null;
				var elementos = getElementsByName_iefix('input', 'radio_' + columna);
				for(var opcion in elementos) {
					if (elementos[opcion].checked) {
						sentido = elementos[opcion].value;
					}
				}
				seleccion_actual[columna] = sentido;
			}
		}
		overlay(true);
		this.set_ordenamiento_multiple(seleccion_actual);
	}
	
	ei_cuadro.prototype.exportar_excel_sin_cortes = function() {
		var param = {es_plano:true};
		var url = vinculador.get_url(null, null, 'vista_excel', param, [this._id]);
		document.location.href = url;
	};
	
toba.confirmar_inclusion('componentes/ei_cuadro');
