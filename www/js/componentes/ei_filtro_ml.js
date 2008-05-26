ei_filtro.prototype = new ei();
ei_filtro.prototype.constructor = ei_filtro;

/**
 * @constructor
 * @phpdoc Componentes/Eis/toba_ei_filtro toba_ei_filtro
 */
function ei_filtro(id, instancia, input_submit) {
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
	
	ei_filtro.prototype.get_valores_maestros = function(id_ef) {
		return [];
	}
	

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
		$(id_fila).style.display = 'none';
		
		//Elimina la fila en la lista interna
		for (i in this._filas) { 
			if (this._filas[i] == fila) {
				this._filas.splice(i, 1); 
				break;
			}
			var anterior = this._filas[i];		
		}
		
		//if (this._filas.empty()) {
			//_grilla
		//}
	};
	

	ei_filtro.prototype.crear_fila = function() {
		var input = $(this._instancia + '_nuevo');
		var id = input.value;
		input.selectedIndex = 0;
		if (in_array(id, this._filas)) {
			//Ya se agrego antes
			return;
		}
		$(this._instancia + '_fila' + id).style.display = '';		
		this._filas.push(id);
		this.seleccionar(id);
		this.refrescar_foco();
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
				$(id_combo + '_ef_extra').style.display = '';
				$(id_combo + '_label_extra').style.display = '';
			} else {
				$(id_combo + '_ef_extra').style.display = 'none';
				$(id_combo + '_label_extra').style.display = 'none';
			}
		}
	}
	
	ei_filtro.prototype.get_condicion = function (columna) {
		var id_combo = 'col_' + this._input_submit + columna;
		return $(id_combo).value;
	}	
	
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
	ei_filtro.prototype.refrescar_foco = function () {
		for (id_ef in this._efs) {
			if (this._efs[id_ef].seleccionar()) {
				break;
			}
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

toba.confirmar_inclusion('componentes/ei_filtro');