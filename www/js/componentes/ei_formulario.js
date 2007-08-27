

ei_formulario.prototype = new ei();
ei_formulario.prototype.constructor = ei_formulario;

/**
 * @class Un formulario simple presenta una grilla de campos editables. 
 * A cada uno de estos campos se los denomina Elementos de Formulario (efs).
 * @see ef
 * @constructor
 * @phpdoc Componentes/Eis/toba_ei_formulario toba_ei_formulario
 */
function ei_formulario(id, instancia, rango_tabs, input_submit, maestros, esclavos, invalidos) {
	this._id = id;
	this._instancia = instancia;				//Nombre de la instancia del objeto, permite asociar al objeto con el arbol DOM
	this._rango_tabs = rango_tabs;
	this._input_submit = input_submit;			//Campo que se setea en el submit del form

	this.controlador = null;							//Referencia al CI contenedor	
	this._efs = {};								//Lista de objeto_ef contenidos
	this._efs_procesar = {};					//ID de los ef's que poseen procesamiento
	this._silencioso = false;					//¿Silenciar confirmaciones y alertas? Util para testing
	this._evento_implicito = null;				//No hay evento prefijado
	this._expandido = false;					//El formulario comienza sin expandirse
	this._maestros = maestros;
	this._esclavos = esclavos;
	this._invalidos = invalidos;
}

	/**
	 *	@private
	 */
	ei_formulario.prototype.agregar_ef  = function (ef, identificador) {
		if (ef) {
			this._efs[identificador] = ef;
		}
	};
	
	ei_formulario.prototype.iniciar = function () {
		for (id_ef in this._efs) {
			this._efs[id_ef].iniciar(id_ef, this);
			this._efs[id_ef].cuando_cambia_valor(this._instancia + '.validar_ef("' + id_ef + '", true)');
			if (this._invalidos[id_ef]) {
				this._efs[id_ef].resaltar(this._invalidos[id_ef]);
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
	 * @type ef
	 * @see ef
	 */
	ei_formulario.prototype.ef = function(id) {
		return this._efs[id];
	};
	
	/**
	 * Retorna un objeto asociativo id_ef => ef, para usarlo en algun ciclo, por ej.
	 * for (id_ef in this.efs()) {
	 *		this.ef(id_ef).metodo()
	 * }
	 * @type Object
	 * @see ef
	 */
	ei_formulario.prototype.efs = function() {
		return this._efs;
	};
	
	
	
	/**
	 * Retorna el estado actual de los efs en un Objeto asociativo id_ef=>valor
	 *	@type Object
	 */
	ei_formulario.prototype.get_datos = function() {
		var datos = {};
		for (var id_ef in this._efs) {
			datos[id_ef] = this._efs[id_ef].get_estado();
		}
		return datos;
	};

	//---Submit 
	ei_formulario.prototype.submit = function() {
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
	ei_formulario.prototype.puede_submit = function() {
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

	//---- Cascadas

	/**
	 * Esquema de Cascadas:<br>
	 * Un ef indica que su valor cambio y por lo tanto sus esclavos deben refrescarse
	 * @param {string} id_ef Identificador del ef maestro que sufrio una modificación
	 */
	ei_formulario.prototype.cascadas_cambio_maestro = function(id_ef, fila)
	{
		if (this._esclavos[id_ef]) {
			this.evt__cascadas_inicio(this.ef(id_ef));
			//--Se recorren los esclavos del master modificado
			for (var i=0; i < this._esclavos[id_ef].length; i++) {
				this.cascadas_preparar_esclavo(this._esclavos[id_ef][i], fila);
			}
		}
	};

	/**
	 * Esquema de Cascadas:<br>	
	 * Determina si los maestros de un ef esclavo tienen sus valores cargados
	 * @param {string} id_esclavo Identificador del ef esclavo
	 * @type boolean	 
	 */
	ei_formulario.prototype.cascadas_maestros_preparados = function(id_esclavo)
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
	ei_formulario.prototype.cascadas_preparar_esclavo = function (id_esclavo, fila)
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
			this.cascadas_comunicar(id_esclavo, valores, fila);
		}
	};
	
	/**
	 * Esquema de Cascadas:<br>
	 * Retorna el estado actual de los maestros directos de un esclavo
	 */
	ei_formulario.prototype.get_valores_maestros = function (id_esclavo)
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
	ei_formulario.prototype.cascadas_en_espera = function(id_ef)
	{
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
	ei_formulario.prototype.cascadas_comunicar = function(id_ef, valores, fila) 
	{
		//Empaqueto toda la informacion que tengo que mandar.
		var parametros = {'cascadas-ef': id_ef, 'cascadas-maestros' : valores};
		if (typeof fila != 'undefined') {
			parametros['cascadas-fila'] = fila;
		}
		var callback = {
			success: this.cascadas_respuesta,
			failure: toba.error_comunicacion,
			argument: id_ef,
			scope: this
		};
		var vinculo = vinculador.crear_autovinculo('cascadas_efs', parametros, [this._id]);
		var con = conexion.asyncRequest('GET', vinculo, callback, null);
	};
	
	/**
	 * Esquema de Cascadas:<br>
	 * Respuesta del servidor ante el pedido de refresco de un ef puntual
	 * @param {Object} respuesta La respuesta es un objeto asociativo con claves responseText que contiene el nuevo valor del ef
	 */
	ei_formulario.prototype.cascadas_respuesta = function(respuesta)
	{
		if (respuesta.responseText === '') {
			var error = 'Error en la respuesta de la cascada, para más información consulte el log';
			notificacion.limpiar();
			notificacion.agregar(error);
			notificacion.mostrar();			
		} else {
			try {
				var datos = eval('(' + respuesta.responseText + ')');
				this.ef(respuesta.argument).set_opciones(datos);
				this.evt__cascadas_fin(this.ef(respuesta.argument), datos);
			} catch (e) {
				var error = 'Error en la respueta.<br>' + "Mensaje Server:<br>" + respuesta.responseText + "<br><br>Error JS:<br>" + e;
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
	ei_formulario.prototype.evt__cascadas_inicio = function(ef_maestro)	{
	}
	
	/**
	 * Esquema de Cascadas:<br>
	 * Ventana de ejecución posterior a la respuesta de una cascada.
	 * Extender para agregar un comportamiento post-respuesta
	 * @param {ef} ef_esclavo Instancia del ef esclavo destino de la cascada
	 * @param {Object} datos Datos de respuesta usados en la cascada
	 * @ventana
	 */
	ei_formulario.prototype.evt__cascadas_fin = function(ef_esclavo, datos) {
	}
	
	
	//----Validación 
	/**
	 * Realiza la validación de este componente
	 * Para agregar validaciones particulares globales al formulario, definir el metodo <em>evt__validar_datos</em>.<br>
	 * Para validar efs especificos, definir el método <em>evt__idef__validar</em>
	 */	
	ei_formulario.prototype.validar = function() {
		var ok = true;
		var validacion_particular = 'evt__validar_datos';
		if(this._evento && this._evento.validar) {
			if (existe_funcion(this, validacion_particular)) {
				ok = this[validacion_particular]();		
			}
			for (id_ef in this._efs) {
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
	ei_formulario.prototype.validar_ef = function(id_ef, es_online) {
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
	 * Informa que un ef cumple o no una validación especifica. 
	 * En caso de que no sea valido el estado del ef se informa al usuario
	 * Si es valido se quita el estado de invalido (la cruz al lado del campo).
	 * @param {ef} ef Ef en cuestión
	 * @param {boolean} es_valido 
	 * @param {boolean} solo_online En caso que no sea valido sólo muestra la cruz al lado del campo y no un mensaje explícito
	 */	
	ei_formulario.prototype.set_ef_valido = function(ef, es_valido, solo_online) {
		if (!es_valido) {
			if (! this._silencioso) {
				ef.resaltar(ef.get_error());
			}
			if (typeof solo_online != 'undefined' && ! solo_online) {
				notificacion.agregar(ef.get_error(), 'error', ef._etiqueta);
			}
			ef.resetear_error();
		} else {		
			ef.no_resaltar();
		}	
	}
	
	ei_formulario.prototype.resetear_errores = function() {
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
	ei_formulario.prototype.procesar = function (id_ef, es_inicial) {
		if (this.hay_procesamiento_particular_ef(id_ef)) {
			return this['evt__' + id_ef + '__procesar'](es_inicial);	//Procesamiento particular, no hay proceso por defecto
		}
	};
			
	/**
	 * Hace reflexion sobre la clase en busqueda de extensiones	
	 * @private
	 */
	ei_formulario.prototype.agregar_procesamientos = function() {
		for (id_ef in this._efs) {
			if (this.hay_procesamiento_particular_ef(id_ef)) {
				this.agregar_procesamiento(id_ef);
			}
		}
	};

	/**
	 * @private
	 */
	ei_formulario.prototype.agregar_procesamiento = function (id_ef) {
		if (this._efs[id_ef]) {
			this._efs_procesar[id_ef] = true;
			var callback = this._instancia + '.procesar("' + id_ef + '")';
			this._efs[id_ef].cuando_cambia_valor(callback);
		}
	};	
	
	/**
	 * @private
	 */
	ei_formulario.prototype.hay_procesamiento_particular_ef = function(id_ef) {
		return existe_funcion(this, 'evt__' + id_ef + '__procesar');
	};	

	//---Cambios graficos
	/**
	 * Invierte la expansión del formulario
	 * Cuando el formulario se encuentra contraido los efs marcados como colapsados en el editor no se muestran
	 * Este metodo no tiene relacion con el colapsar/descolapsar que se encargan de colapsar el componente como un todo	 
	 */
	ei_formulario.prototype.cambiar_expansion = function() {
		this._expandido = ! this._expandido;
		for (var id_ef in this._efs) {
			this._efs[id_ef].cambiar_expansion(this._expandido);
		}
		var img = document.getElementById(this._instancia + '_cambiar_expansion');
		img.src = (this._expandido) ? toba.imagen('contraer') : toba.imagen('expandir');
	};
	
	//---Refresco Grafico
	
	/**
	 * Fuerza un refuerzo grafico del componente
	 */
	ei_formulario.prototype.refrescar_todo = function () {
		this.refrescar_procesamientos();
	};		
	
	/**
	 *	@private
	 */
	ei_formulario.prototype.refrescar_procesamientos = function (es_inicial) {
		for (var id_ef in this._efs) {
			if (this._efs_procesar[id_ef]) {
				this.procesar(id_ef, es_inicial);
			}
		}
	};	

toba.confirmar_inclusion('componentes/ei_formulario');