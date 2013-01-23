	ei_filtro.prototype = new ei();
	ei_filtro.prototype.constructor = ei_filtro;

	/**
	 * @class ei_filtro
	 * @constructor
	 * @phpdoc Componentes/Eis/toba_ei_filtro toba_ei_filtro
	 */
	function ei_filtro(id, instancia, input_submit, maestros, esclavos) {
		this._id = id;
		this._instancia = instancia;				//Nombre de la instancia del objeto, permite asociar al objeto con el arbol DOM
		this._input_submit = input_submit;			//Campo que se setea en el submit del form
		this._silencioso = false;

		this.controlador = null;							//Referencia al CI contenedor
		this._efs = {};
		this._efs_procesar = {};					//ID de los ef's que poseen procesamiento
		this._evento_implicito = null;				//No hay evento prefijado
		this._seleccionada = null;
		this._filas = [];
		this._compuestos = [];
		this._maestros = maestros;
		this._esclavos = esclavos;
		this._tmp_valores_esclavos = {};		//lista temporal de valores a guardar hasta que retorna la cascada
		this._cambios_excluir_efs = [];
		this._boton_procesar_cambios;
	}

	/**
	 *	@private
	 */
	ei_filtro.prototype.agregar_ef  = function (ef, identificador, visible, compuesto) {
		if (ef) {
			this._efs[identificador] = ef;
			if (visible) {
				this._filas.push(identificador);
			}
			if (compuesto) {
				this._compuestos.push(identificador);
			}
		}
	};
	
	ei_filtro.prototype.iniciar = function () {
		for (id_ef in this._efs) {
			this._efs[id_ef].iniciar(id_ef, this);
			this._efs[id_ef].cuando_cambia_valor(this._instancia + '.validar_ef("' + id_ef + '", true)');
			this._efs[id_ef].validar();
			this.cambio_condicion(id_ef);
			//-- Si es compuesto 
			if (in_array(id_ef, this._compuestos)) {
				//Inicia la fila extra
				this._efs[id_ef].ir_a_fila('extra');
				this._efs[id_ef].iniciar(id_ef, this);
				//this._efs[id_ef].cuando_cambia_valor(this._instancia + '.validar_ef("' + id_ef + '", true, true)');
				this._efs[id_ef].sin_fila();
			}
		}
		this.agregar_procesamientos();
		this.refrescar_procesamientos(true);
		this.refrescar_botonera();
		this.reset_evento();
		if (this.configurar) {
			this.configurar();
		}
	};

	//---Consultas
	/**
	 * Accede a la instancia de un ef especifico
	 */
	ei_filtro.prototype.ef = function(id) {
		return this._efs[id];
	};
	
	/**
	 *	@private
	 */
	ei_filtro.prototype.instancia_ef  = function (objeto_ef) {
		var id = objeto_ef.get_id();
		return this._instancia + ".ef('"+ id + "')";
	};	
	
	
	ei_filtro.prototype.get_valores_maestros = function(id_ef) {
		return [];
	};
	
	/**
	 * Devuelve si una columna dada esta activa o no en el filtro.
	 * @param string id Identificador de la columna
	 * @type boolean
	 */
	ei_filtro.prototype.esta_activa = function(id) {
		return (in_array(id, this._filas));
	};

	//---Submit 
	ei_filtro.prototype.submit = function() {
		if (this.controlador && !this.controlador.en_submit()) {
			return this.controlador.submit();
		}
		if (this._evento) {
			//Enviar la noticia del submit a los efs
			var filas_con_datos = [];
			for (id_fila in this._filas) {
				var id_ef = this._filas[id_fila];
				//Si el ef no tiene valor, no se envia al server
				if (this._efs[id_ef].tiene_estado()) {
					//Si la columna es compuesta, chequear que ambos efs tengan estado y uno sea menor que el otro
					if (in_array(id_ef, this._compuestos) && this.get_condicion(id_ef) == 'entre') {				
						if (! this._efs[id_ef].ir_a_fila('extra').tiene_estado()) {
							continue;	//Si el segundo ef no tiene estado, no seguir
						}
						this._efs[id_ef].submit();
						this._efs[id_ef].sin_fila();
					}
					this._efs[id_ef].submit();
					filas_con_datos.push(id_ef);
				}
			}
			//Lista de filas a filtrar
			var lista_filas = filas_con_datos.join(toba_hilo_separador);
			document.getElementById(this._instancia + '_listafilas').value = lista_filas;
			//Marco la ejecucion del evento para que la clase PHP lo reconozca
			document.getElementById(this._input_submit).value = this._evento.id;
		}
	};

	//Chequea si es posible realiza el submit de todos los objetos asociados	
	ei_filtro.prototype.puede_submit = function() {
		if(this._evento) //Si hay un evento seteado...
		{
			//- 1 - Hay que realizar las validaciones
			if(! this.validar() ) {
				this.reset_evento();
				return false;
			}
			if (! ei.prototype.puede_submit.call(this)) {
				return false;
			}			
		}
		return true;
	};
	
	
	//---ABM 
	/**
	 * Elimina del formulario la fila actualmente seleccionada
	 * El HTML solo se oculta, no se elimina, con lo cual puede ser recuperado en su estado actual
	 */
	ei_filtro.prototype.eliminar_seleccionada = function() {
		var fila = this._seleccionada;
		anterior = this.eliminar_fila(fila);		
		delete(this._seleccionada);
	};
	
	ei_filtro.prototype.eliminar_fila = function(fila) {
			//'Elimina' la fila en el DOM
		var id_fila = this._instancia + '_fila' + fila;
		cambiar_clase(document.getElementById(id_fila).cells, 'ei-fitro-ml-fila', 'ei-filtro-fila-selec');
		$$(id_fila).style.display = 'none';
		
		//Elimina la fila en la lista interna
		for (i in this._filas) { 
			if (this._filas[i] == fila) {
				this._filas.splice(i, 1); 
				break;
			}
			var anterior = this._filas[i];		
		}
		//Tengo que ver que pasa con los esclavos
		for(var esclavo in this._esclavos[fila]) {
			this.eliminar_fila(this._esclavos[fila][esclavo]);
		}
		this.refrescar_botonera();
	};
	

	ei_filtro.prototype.crear_fila = function(id, es_esclavo) {
		if (! isset(es_esclavo)) {
			es_esclavo = false
		}
		if (id == undefined) {
			var input = $$(this._instancia + '_nuevo');
			var id = input.value;
		}
		if (id == 'nopar' || in_array(id, this._filas)) {
			//Ya se agrego antes
			return;
		}
		$$(this._instancia + '_fila' + id).style.display = '';		
		this._filas.push(id);
		for (var esclavo in this._esclavos[id])	 {
			this.crear_fila(this._esclavos[id][esclavo], true);
		}
		if (! es_esclavo) {
			this.seleccionar(id);
			this.refrescar_foco(id);
		}
		this.refrescar_botonera();
	};
		

	//---- Cascadas

	/**
	 * Esquema de Cascadas:<br>
	 * Un ef indica que su valor cambio y por lo tanto sus esclavos deben refrescarse
	 * @param {string} id_ef Identificador del ef maestro que sufrio una modificación
	 */
	ei_filtro.prototype.cascadas_cambio_maestro = function(id_ef)
	{
		if (this._esclavos[id_ef]) {
			this.evt__cascadas_inicio(this.ef(id_ef));
			//--Se recorren los esclavos del master modificado
			for (var i=0; i < this._esclavos[id_ef].length; i++) {
				this.cascadas_preparar_esclavo(this._esclavos[id_ef][i]);
			}
		}
	};

	/**
	 * Esquema de Cascadas:<br>
	 * Determina si los maestros de un ef esclavo tienen sus valores cargados
	 * @param {string} id_esclavo Identificador del ef esclavo
	 * @type boolean
	 */
	ei_filtro.prototype.cascadas_maestros_preparados = function(id_esclavo)
	{
		for (var i=0; i< this._maestros[id_esclavo].length; i++) {
			var ef = this.ef(this._maestros[id_esclavo][i]);
			if (ef && ! ef.tiene_estado()) {
				return false;
			}
		}
		return true;
	};

	/**
	 * Esquema de Cascadas:<br>
	 * Un ef esclavo esta listo para refrescar su valor en base a sus maestros,
	 * para esto en este metodo se recolecta los valores de sus maestros y se dispara
	 * la comunicación con el servidor
	 * @param {string} id_esclavo Identificador del ef esclavo que se refrescara
	 */
	ei_filtro.prototype.cascadas_preparar_esclavo = function (id_esclavo)
	{
		//Primero se resetea por si la consulta nunca retorna
		this.cascadas_en_espera(id_esclavo);

		//---Todos los maestros tienen estado?
		var con_estado = true;
		var valores = '';
		for (var i=0; i< this._maestros[id_esclavo].length; i++) {
			var id_maestro = this._maestros[id_esclavo][i];
			var ef = this.ef(id_maestro);
			if (ef && ef.tiene_estado()) {
				var valor = this.ef(id_maestro).get_estado();
				valores +=  id_maestro + '-;-' + valor + '-|-';
			} else if (ef) {
				//-- Evita caso del oculto
				con_estado = false;
				break;
			}
		}
		//--- Si estan todos los maestros puedo ir al server a preguntar el valor de este
		if (con_estado) {
			if (this.ef(id_esclavo)._cascadas_ajax) {
				//Caso normal
				this.cascadas_comunicar(id_esclavo, valores);
			} else {
				//Caso combo_editable
				this.ef(id_esclavo).set_solo_lectura(false);
			}
		}
	};

	/**
	 * Esquema de Cascadas:<br>
	 * Retorna el estado actual de los maestros directos de un esclavo
	 */
	ei_filtro.prototype.get_valores_maestros = function (id_esclavo)
	{
		var maestros = {};
		for (var i=0; i< this._maestros[id_esclavo].length; i++) {
			var id_maestro = this._maestros[id_esclavo][i];
			var ef = this.ef(id_maestro);
			if (ef && ef.tiene_estado()) {
				maestros[id_maestro] = this.ef(id_maestro).get_estado();
			}
		}
		return maestros;
	};

	/**
	 * @private
	 */
	ei_filtro.prototype.cascadas_en_espera = function(id_ef)
	{
		if (this.ef(id_ef).tiene_estado() && this.ef(id_ef).mantiene_valor_cascada()) {	//Guardo el estado actual por si acaso vuelve en la respuesta
			this._tmp_valores_esclavos[id_ef] = this.ef(id_ef).get_estado();
		}
		//Se resetea y desactiva al ef y todos sus esclavos
		this.ef(id_ef).borrar_opciones();
		this.ef(id_ef).desactivar();
		if (this._esclavos[id_ef]) {
			for (var i=0; i< this._esclavos[id_ef].length; i++) {
				this.cascadas_en_espera(this._esclavos[id_ef][i]);
			}
		}
	};

	/**
	 * Esquema de Cascadas:<br>
	 * Se comunica al servidor que debe refrescar el valor de un ef en base a valores especificos de sus efs maestros
	 * Este método dispara la llamada asincronica al servidor
	 * @see #cascadas_respuesta
	 * @param {string} id_ef Id. del ef a refrescar (un ef esclavo)
	 * @param {string valores Lista plana de valores. Formato: ef1-;-valor1-|-ef2-;-valor2-|- etc.
	 */
	ei_filtro.prototype.cascadas_comunicar = function(id_ef, valores)
	{
		//Empaqueto toda la informacion que tengo que mandar.
		var parametros = {'cascadas-col': id_ef, 'cascadas-maestros' : valores};
		var callback = {
			success: this.cascadas_respuesta,
			failure: toba.error_comunicacion,
			argument: id_ef,
			scope: this
		};
		var vinculo = vinculador.get_url(null, null, 'cascadas_columnas', parametros, [this._id]);
		var con = conexion.asyncRequest('GET', vinculo, callback, null);
	};

	/**
	 * Esquema de Cascadas:<br>
	 * Respuesta del servidor ante el pedido de refresco de un ef puntual
	 * @param {Object} respuesta La respuesta es un objeto asociativo con claves responseText que contiene el nuevo valor del ef
	 */
	ei_filtro.prototype.cascadas_respuesta = function(respuesta)
	{
		if (respuesta.responseText === '') {
			var error = 'Error en la respuesta de la cascada, para más información consulte el log';
			notificacion.limpiar();
			notificacion.agregar(error);
			notificacion.mostrar();			
		} else {
			try {
				var datos_rs = eval('(' + respuesta.responseText + ')');
				var datos_asociativo;
				if ('Array' == getObjectClass(datos_rs)) {
					datos_asociativo = [];
					for (var ind = 0; ind < datos_rs.length ; ind++) {
						datos_asociativo[datos_rs[ind][0]] = datos_rs[ind][1];
					}
					//Se le pasa el formato RS para que no se rompa el ordenamiento, para el resto se usa el asociativo por BC
					this.ef(respuesta.argument).set_opciones_rs(datos_rs);
				} else {
					datos_asociativo = datos_rs;
					this.ef(respuesta.argument).set_opciones(datos_asociativo);
				}
				if(this.ef(respuesta.argument).mantiene_valor_cascada() && isset(this._tmp_valores_esclavos[respuesta.argument])) {
					var valor_viejo = this._tmp_valores_esclavos[respuesta.argument];
					if (isset(datos_asociativo[valor_viejo])) {
						this.ef(respuesta.argument).set_estado(valor_viejo);
					}
				}
				this.evt__cascadas_fin(this.ef(respuesta.argument), datos_asociativo);
			} catch (e) {
				var componente = "<textarea id='displayMore' class='ef-input-solo-lectura' cols='30' rows='35' readonly='true' style='display:none;'>" + respuesta.responseText + '</textarea>';
				var error = 'Error en la respueta.<br>' +  'Error JS:<br>' + e + '<br>Mensaje Server:<br>'   +
						"<a href='#' onclick='toggle_nodo(document.getElementById(\"displayMore\"));'>Mas</a><br>" + componente;
				notificacion.limpiar();
				notificacion.agregar(error);
				notificacion.mostrar();				
			}
		}
	};

	/**
	 * Esquema de Cascadas:<br>
	 * Ventana de ejecución anterior al pedido de respuesta de la cascada
	 * Extender para agregar un comportamiento anterior a la respuesta
	 * @param {ef} ef_maestro Instancia del ef maestro que inicia la cascada
	 * @ventana
	 */
	ei_filtro.prototype.evt__cascadas_inicio = function(ef_maestro)	{
	};

	/**
	 * Esquema de Cascadas:<br>
	 * Ventana de ejecución posterior a la respuesta de una cascada.
	 * Extender para agregar un comportamiento post-respuesta
	 * @param {ef} ef_esclavo Instancia del ef esclavo destino de la cascada
	 * @param {Object} datos Datos de respuesta usados en la cascada
	 * @ventana
	 */
	ei_filtro.prototype.evt__cascadas_fin = function(ef_esclavo, datos) {
	};

	
	//----Validación 
	/**
	 * Realiza la validación de este componente
	 * Para agregar validaciones particulares globales al formulario, definir el metodo <em>evt__validar_datos</em>.<br>
	 * Para validar efs especificos, definir el método <em>evt__idef__validar</em>
	 */	
	ei_filtro.prototype.validar = function() {
		var ok = true;
		var validacion_particular = 'evt__validar_datos';
		if(this._evento && this._evento.validar) {
			if (existe_funcion(this, validacion_particular)) {
				ok = this[validacion_particular]();		
			}
			for (id_fila in this._filas) {
				ok = this.validar_ef(this._filas[id_fila]) && ok;
			}
		} else {
			this.resetear_errores();
		}
		if (!ok) {
			this.reset_evento();
		}
		return ok;
	};
	
	/**
	 *	@private
	 */
	ei_filtro.prototype.validar_ef = function(id_ef, es_online, es_extra) {
		if (! isset(es_extra)) {
			es_extra = false;
		}
		var ef = this._efs[id_ef];
		if (es_extra) {
			ef.ir_a_fila('extra');
		}
		var validacion_particular = 'evt__' + id_ef + '__validar';
		var ok = ef.validar();
		if (existe_funcion(this, validacion_particular)) {
			ok = this[validacion_particular]() && ok;
		}
		this.set_ef_valido(ef, ok, es_online);
		if (es_extra) {
			ef.sin_fila();
		}
		return ok;
	};
	
	/**
	 * Informa que una ef que cumple o no una validación especifica. 
	 * En caso de que no sea valido el estado de la ef se informa al usuario
	 * Si es valido se quita el estado de invalido (la cruz al lado del campo).
	 * @param {ef} la ef en cuestión
	 * @param {boolean} es_valido 
	 * @param {boolean} solo_online En caso que no sea valido sólo muestra la cruz al lado del campo y no un mensaje explícito
	 */	
	ei_filtro.prototype.set_ef_valido = function(ef, es_valido, solo_online) {
		if (!es_valido) {
			if (! this._silencioso) {
				ef.resaltar(ef.get_error());
			}
			if (typeof solo_online == 'undefined' || !solo_online) {
				notificacion.agregar(ef.get_error(), 'error', ef._etiqueta);
			}
			ef.resetear_error();
		} else {		
			ef.no_resaltar();
		}	
	};
	
	ei_filtro.prototype.resetear_errores = function() {
		if (! this._silencioso)	 {
			for (var id_ef in this._efs) {
				if (! this._silencioso) {
					this._efs[id_ef].no_resaltar();
				}
			}	
		}
	};

	//---Procesamiento 
	
	ei_filtro.prototype.cambio_condicion = function (columna) {
		if (in_array(columna, this._compuestos)) {
			var id_combo = 'col_' + this._input_submit + columna;			
			var condicion = this.get_condicion(columna);
			if (condicion == 'entre') {
				$$(id_combo + '_ef_extra').style.display = '';
				$$(id_combo + '_label_extra').style.display = '';
			} else {
				$$(id_combo + '_ef_extra').style.display = 'none';
				$$(id_combo + '_label_extra').style.display = 'none';
			}
		}
	};
	
	ei_filtro.prototype.get_condicion = function (columna) {
		var id_combo = 'col_' + this._input_submit + columna;
		return $$(id_combo).value;
	};	
	
	/**
	 *	@private
	 */
	ei_filtro.prototype.procesar = function (id_ef, es_inicial) {
		if (this.hay_procesamiento_particular_ef(id_ef)) {
			return this['evt__' + id_ef + '__procesar'](es_inicial);	//Procesamiento particular, no hay proceso por defecto
		}
	};
			
	/**
	 * Hace reflexion sobre la clase en busqueda de extensiones	
	 * @private
	 */
	ei_filtro.prototype.agregar_procesamientos = function() {
		for (id_ef in this._efs) {
			if (this.hay_procesamiento_particular_ef(id_ef)) {
				this.agregar_procesamiento(id_ef);
			}
		}
	};

	/**
	 * @private
	 */
	ei_filtro.prototype.agregar_procesamiento = function (id_ef) {
		if (this._efs[id_ef]) {
			this._efs_procesar[id_ef] = true;
			var callback = this._instancia + '.procesar("' + id_ef + '")';
			this._efs[id_ef].cuando_cambia_valor(callback);
		}
	};	
	
	/**
	 * @private
	 */
	ei_filtro.prototype.hay_procesamiento_particular_ef = function(id_ef) {
		return existe_funcion(this, 'evt__' + id_ef + '__procesar');
	};	

	//---Refresco Grafico
	
	/**
	 * Toma la fila seleccionada y le pone foco al primer ef que lo acepte
	 */
	ei_filtro.prototype.refrescar_foco = function (id) {
		
		if (! isset(id)) {
			for (id_ef in this._efs) {
				if (this._efs[id_ef].seleccionar()) {
					break;
				}
			}
		} else {
			this._efs[id].seleccionar();
		}
	};
	
	
	/**
	 *	@private
	 */
	ei_filtro.prototype.refrescar_procesamientos = function (es_inicial) {
		for (var id_ef in this._efs) {
			if (this._efs_procesar[id_ef]) {
				this.procesar(id_ef, es_inicial);
			}
		}
	};	
	
	
	/**
	 * @private
	 */
	ei_filtro.prototype.refrescar_botonera = function () {
		var combo = $$(this._instancia + '_nuevo');
		var hay_faltantes = false;
		for (var i=0; i<combo.length; i++) {
			var id = combo.options[i].value;
			if (id != 'nopar') {
				//-- Esta seleccionada la fila?
				if (in_array(id, this._filas)) {
					combo.options[i].disabled = true;
				} else if (this._es_columna_esclava_sin_maestro_activo(id)) {				//Es un campo esclavo en la llamada inicial?
					combo.options[i].disabled = true;
				} else {
					hay_faltantes = true;
					combo.options[i].disabled = false;
				}
			}
		}
		var display = (! hay_faltantes) ? 'none' : '';
		$$('botonera_' + this._instancia).style.display = display;
	};
	
	/**
	 * Resalta la línea seleccionada 
	 * @private
	 */
	ei_filtro.prototype.refrescar_seleccion = function () {
		if (isset(this._seleccionada)) {
			cambiar_clase(document.getElementById(this._instancia + '_fila' + this._seleccionada).cells, 'ei-filtro-fila-selec', 'ei-filtro-fila');
		}
	};	
	
	//----Selección 
	/**
	 * Marca una fila como seleccionada, cambiando su color de fondo 
	 */
	ei_filtro.prototype.seleccionar = function(fila) {
		if  (fila != this._seleccionada) {
			this.deseleccionar_actual();
			this._seleccionada = fila;
			this.refrescar_seleccion();
		}
	};	
	
	/**
	 * Deselecciona cualquier seleccion anterior de fila
	 * @see #seleccionar
	 */
	ei_filtro.prototype.deseleccionar_actual = function() {
		if (isset(this._seleccionada)) {	//Deselecciona el anterior
			var fila = document.getElementById(this._instancia + '_fila' + this._seleccionada);
			cambiar_clase(fila.cells, 'ei-filtro-fila', 'ei-filtro-fila-selec');			
			delete(this._seleccionada);
		}
	};

	ei_filtro.prototype.set_procesar_cambios = function(examinar, boton_destino, excluir_efs) {
		this._con_examen_cambios = examinar;
		if (! isset(excluir_efs)) {
			excluir_efs = [];
		}
		this._cambios_excluir_efs = excluir_efs;
		if (boton_destino) {
			this._boton_procesar_cambios = boton_destino;
			this.evt__procesar_cambios = this._procesar_cambios;	//Se brinda una implementacion por defecto
		}
	};

	ei_filtro.prototype._procesar_cambios = function(existen_cambios) {
		if (existen_cambios) {
			this.activar_boton(this._boton_procesar_cambios);
		} else {
			this.desactivar_boton(this._boton_procesar_cambios);
		}
	};

	ei_filtro.prototype._es_columna_esclava_sin_maestro_activo = function (id) {
		var es_esclavo = false;
		for (var _ef in this._esclavos) {
			if (in_array(id, this._esclavos[_ef])) {
				es_esclavo = true;
				break;
			}					
		}
		
		var maestro_activo = true;
		for (_ef in this._maestros[id]) {
			maestro_activo = maestro_activo && in_array(this._maestros[id][_ef], this._filas);
		}		
		
		return (es_esclavo && ! maestro_activo);
	}

toba.confirmar_inclusion('componentes/ei_filtro');