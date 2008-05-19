ei_filtro_ml.prototype = new ei();
ei_filtro_ml.prototype.constructor = ei_filtro_ml;

/**
 * @constructor
 * @phpdoc Componentes/Eis/toba_ei_filtro_ml toba_ei_filtro_ml
 */
function ei_filtro_ml(id, instancia, input_submit) {
	this._id = id;
	this._instancia = instancia;				//Nombre de la instancia del objeto, permite asociar al objeto con el arbol DOM
	this._input_submit = input_submit;			//Campo que se setea en el submit del form
	this._silencioso = false;
	
	this.controlador = null;							//Referencia al CI contenedor	
	this._efs = {};		
	this._efs_procesar = {};					//ID de los ef's que poseen procesamiento						
	this._evento_implicito = null;				//No hay evento prefijado
	this._seleccionada = null;
}

	/**
	 *	@private
	 */
	ei_filtro_ml.prototype.agregar_ef  = function (ef, identificador) {
		if (ef) {
			this._efs[identificador] = ef;
		}
	};
	
	ei_filtro_ml.prototype.iniciar = function () {
		for (id_ef in this._efs) {
			this._efs[id_ef].iniciar(id_ef, this);
			this._efs[id_ef].cuando_cambia_valor(this._instancia + '.validar_ef("' + id_ef + '", true)');
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
	ei_filtro_ml.prototype.ef = function(id) {
		return this._efs[id];
	};
	

	//---Submit 
	ei_filtro_ml.prototype.submit = function() {
		if (this.controlador && !this.controlador.en_submit()) {
			return this.controlador.submit();
		}
		if (this._evento) {
			//Enviar la noticia del submit a los efs
			for (id_ef in this._efs) {
				this._efs[id_ef].submit();
			}
			//Marco la ejecucion del evento para que la clase PHP lo reconozca
			document.getElementById(this._input_submit).value = this._evento.id;
		}
	};

	//Chequea si es posible realiza el submit de todos los objetos asociados	
	ei_filtro_ml.prototype.puede_submit = function() {
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

	
	//----Validación 
	/**
	 * Realiza la validación de este componente
	 * Para agregar validaciones particulares globales al formulario, definir el metodo <em>evt__validar_datos</em>.<br>
	 * Para validar efs especificos, definir el método <em>evt__idef__validar</em>
	 */	
	ei_filtro_ml.prototype.validar = function() {
		var ok = true;
		var validacion_particular = 'evt__validar_datos';
		if(this._evento && this._evento.validar) {
			if (existe_funcion(this, validacion_particular)) {
				ok = this[validacion_particular]();		
			}
			for (id_ef in this._cols) {
				ok = this.validar_ef(id_ef) && ok;
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
	ei_filtro_ml.prototype.validar_ef = function(id_ef, es_online) {
		var ef = this._efs[id_ef];
		var validacion_particular = 'evt__' + id_ef + '__validar';
		var ok = ef.validar();
		if (existe_funcion(this, validacion_particular)) {
			ok = this[validacion_particular]() && ok;
		}
		this.set_ef_valido(ef, ok, es_online);
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
	ei_filtro_ml.prototype.set_ef_valido = function(ef, es_valido, solo_online) {
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
	
	ei_filtro_ml.prototype.resetear_errores = function() {
		if (! this._silencioso)	 {
			for (var id_ef in this._efs) {
				if (! this._silencioso) {
					this._efs[id_ef].no_resaltar();
				}
			}	
		}
	};

	//---Procesamiento 
	
	/**
	 *	@private
	 */
	ei_filtro_ml.prototype.procesar = function (id_ef, es_inicial) {
		if (this.hay_procesamiento_particular_ef(id_ef)) {
			return this['evt__' + id_ef + '__procesar'](es_inicial);	//Procesamiento particular, no hay proceso por defecto
		}
	};
			
	/**
	 * Hace reflexion sobre la clase en busqueda de extensiones	
	 * @private
	 */
	ei_filtro_ml.prototype.agregar_procesamientos = function() {
		for (id_ef in this._efs) {
			if (this.hay_procesamiento_particular_ef(id_ef)) {
				this.agregar_procesamiento(id_ef);
			}
		}
	};

	/**
	 * @private
	 */
	ei_filtro_ml.prototype.agregar_procesamiento = function (id_ef) {
		if (this._efs[id_ef]) {
			this._efs_procesar[id_ef] = true;
			var callback = this._instancia + '.procesar("' + id_ef + '")';
			this._efs[id_ef].cuando_cambia_valor(callback);
		}
	};	
	
	/**
	 * @private
	 */
	ei_filtro_ml.prototype.hay_procesamiento_particular_ef = function(id_ef) {
		return existe_funcion(this, 'evt__' + id_ef + '__procesar');
	};	

	//---Refresco Grafico
	
	/**
	 * Fuerza un refuerzo grafico del componente
	 */
	ei_filtro_ml.prototype.refrescar_todo = function () {
		this.refrescar_procesamientos();
	};		
	
	/**
	 *	@private
	 */
	ei_filtro_ml.prototype.refrescar_procesamientos = function (es_inicial) {
		for (var id_ef in this._efs) {
			if (this._efs_procesar[id_ef]) {
				this.procesar(id_ef, es_inicial);
			}
		}
	};	
	
	//----Selección 
	/**
	 * Marca una fila como seleccionada, cambiando su color de fondo 
	 */
	ei_filtro_ml.prototype.seleccionar = function(fila) {
		if  (fila != this._seleccionada) {
			this.deseleccionar_actual();
			this._seleccionada = fila;
		}
	};	
	
	/**
	 * Deselecciona cualquier seleccion anterior de fila
	 * @see #seleccionar
	 */
	ei_filtro_ml.prototype.deseleccionar_actual = function() {
		if (isset(this._seleccionada)) {	//Deselecciona el anterior
			var fila = document.getElementById(this._instancia + '_fila' + this._seleccionada);
			cambiar_clase(fila.cells, 'ei-filtro-ml-fila', 'ei-filtro-ml-fila-selec');			
			delete(this._seleccionada);
		}
	};	

toba.confirmar_inclusion('componentes/ei_filtro_ml');