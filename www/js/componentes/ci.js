
ci.prototype = new ei();
ci.prototype.constructor = ci;

/**
 * @class Componente responsable de manejar la pantalla y sus distintos elementos
 * @constructor
 * @phpdoc Componentes/Eis/toba_ci toba_ci
 */
function ci(id, instancia, form, input_submit, id_en_controlador, ajax) {
	this._id = id;
	this.controlador = null;							//CI contenedor	
	this._instancia = instancia;						//Nombre de la instancia del objeto, permite asociar al objeto con el arbol DOM
	this._form = form;									//Nombre del form contenedor del objeto
	this._input_submit = input_submit;					//Campo que se setea en el submit del form 
	this._id_en_controlador = id_en_controlador;		//ID del tab actual
	this._deps = {};									//Listado asociativo de dependencias
	this._en_submit = false;							//?Esta en proceso de submit el CI?
	this._silencioso = false;							//?Silenciar confirmaciones y alertas? Util para testing
	this._evento_implicito = new evento_ei('', true, '');	//Por defecto se valida los objetos contenidos
	this.reset_evento();
	this._ajax = ajax;
	this._pantallas = {};
	this._pantallas_inactivas = {};
}

	/**
	 *	@private
	 */
	ci.prototype.agregar_objeto = function(objeto, identificador) {
		objeto.set_controlador(this, identificador);
		this._deps[identificador] = objeto;
	};

	/**
	 *	@private
	 */
	ci.prototype.agregar_pantallas = function (pantallas_disponibles) {
		this._pantallas = pantallas_disponibles;
		for (var ind in this._pantallas) {
			if (! this._pantallas[ind]) {					//Ciclo por las pantallas desactivando aquellas que asi deben estar
				this.desactivar_tab(ind);
			}
		}
	}

	/**
	 * Retorna la referencia a un componente hijo o dependiente del actual
	 * @param {string} identificador Identificador de la dependencia o hijo.
	 * @type ei
	 */
	ci.prototype.dependencia = function(identificador) {
		return this._deps[identificador];
	};
	
	/**
	 * @see #dependencia
	 */
	ci.prototype.dep = function(identificador) {
		return this.dependencia(identificador);	
	};

	/**
	 *@private
	 */
	ci.prototype.iniciar = function() {
		for (var dep in this._deps) {
			this._deps[dep].iniciar();
		}
		//Ventana de extensión para los proyectos
		for (var dep in this._deps) {
			this._deps[dep].ini();
		}		
	};
	
	//---Eventos
	ci.prototype.set_evento = function(evento) {
		this._evento = evento;
		this.submit();
	};
	
	/**
	 * Determina cual es el evento que se utiliza cuando no se dispara ninguno explicitamente por el usuario
	 * @param {evento_ei} evento
	 */
	ci.prototype.set_evento_implicito = function(evento) {
		this._evento_implicito = evento;
		this.reset_evento();
	};	
	

	//---SUBMIT
	/**
	 * Intenta realizar el submit de todos los objetos asociados
	 * El proceso de SUBMIT se divide en partes:<br>
	 * 1- Se sube hasta el CI raiz<br>
	 * 2- El componente raiz analiza si puede hacerlo (recorriendo los hijos)<br>
	 * 3-Se envia el submit a los hijos y se hace el procesamiento para PHP (esto es irreversible)<br>
	 */
	ci.prototype.submit = function() {
		if (this.controlador && !this.controlador.en_submit()) { //Primero debe consultar si su padre está en proceso
			return this.controlador.submit();
		}

		this._en_submit = true;
		if (! this.controlador) { //Si es el padre de todos, borrar las notificaciones
			notificacion.limpiar();
			if (this.puede_submit()) {
				this.submit_recursivo();
				if (this._ajax) {
					toba.set_ajax(this, toba.servicio__html_parcial);
				} else {
					toba.set_ajax(null);
				}
				toba.comunicar_eventos();
			} else {
				if (window. notificacion) {
					notificacion.mostrar(this);
				}
			}
		} else {
			this.submit_recursivo();
		}
		this._en_submit = false;
	};

	/**
	 * @private
	 */
	ci.prototype.submit_recursivo = function()
	{
		for (var dep in this._deps) {
			this._deps[dep].submit();
		}
		if (this._evento.id !== '') {
			document.getElementById(this._input_submit).value = this._evento.id;
			document.getElementById(this._input_submit + "__param").value = this._evento.parametros;
		}
	};
	
	/**
	 *	@private
	 */
	ci.prototype.en_submit = function() {
		return this._en_submit;		
	};
	
	ci.prototype.puede_submit = function() {
		if (this._evento) {
			//- 1 - Hay que realizar las validaciones y preguntarle a los hijos si pueden hacer submit
			//		La validación no es recursiva para evitar doble chequeos en los hijos
			var ok = this.validar(false);
			ok = ok && this.objetos_pueden_submit();
			if(!ok) {
				this.reset_evento();
				return false;
			} 
			//- 2 - Hay que llamar a una ventana de control especifica para este evento?
			if(existe_funcion(this, "evt__" + this._evento.id)){
				var res = this["evt__" + this._evento.id](this._evento.parametros);
				if(typeof res != 'undefined' && !res ){
					this.reset_evento();
					return false;
				}
			}
			//- 3 - Hay que confirmar la ejecucion del evento?
			//La confirmacion se solicita escribiendo el texto de la misma
			if(trim(this._evento.confirmar) !== "") {
				if (!this._silencioso && !(confirm(this._evento.confirmar))){
					this.reset_evento();
					return false;
				}
			}
			return true;
		} else {
			return true;
		}
	};
	
	/**
	 * @private
	 */
	ci.prototype.objetos_pueden_submit = function() {
		if(this._evento && this._evento.validar) {
			var ok = true;
			for (var dep in this._deps) {
				ok = this._deps[dep].puede_submit() && ok;
			}
			return ok;			
		} else {
			this.resetear_errores();
			return true;
		}
	};
	
	ci.prototype.resetear_errores = function() {
		for (var dep in this._deps) {
			this._deps[dep].resetear_errores();
		}
		this.notificar(false);
	};
	
	/**
	 * Realiza la validación de este objeto, y opcionalmente de los que están contenidos
	 * Para agregar validaciones particulares a nivel de este ci, definir el metodo <em>evt__validar_datos</em>
	 */
	ci.prototype.validar = function(recursivo) {
		if (typeof recursivo == 'undefined') {
			recursivo = true;
		}
		var validacion_particular = 'evt__validar_datos';
		var ok = true;
		if(this._evento && this._evento.validar) {
			if (existe_funcion(this, validacion_particular)) {
				ok = ok && this[validacion_particular]();	
			}
			if (recursivo) {
				for (var dep in this._deps) {
					ok = ok && this._deps[dep].validar(recursivo);
				}
			}
		}
		return ok;
	};
	
	//---Notificaciones
	/**
	 * Muestra en la barra del componente un icono de acceso a las notificaciones
	 * @see notificacion
	 * @param {boolean} mostrar Mostrar (true) u ocultar (false);
	 * @deprecated
	 */
	ci.prototype.notificar = function(mostrar) {

	};

	//---Navegación 

	/**
	 * Ejecuta el evento de cambiar de pantalla (similar a cambiar de tab manualmente)
	 * @param {string} pantalla Id. de la pantalla destino
	 */
	ci.prototype.ir_a_pantalla = function(pantalla) {
		if (this.existe_pantalla(pantalla) && this.evt__salida_tab(pantalla)) {
			this.set_evento(new evento_ei('cambiar_tab_' + pantalla, true, ''));
		}
	};
	
	/**
	 * Permite escuchar la salida del tab actual y controlar si se puede hacer o no 
	 * retornando true/false
	 * @param {string} pantalla_destino Pantalla a la que se va a navegar
	 * @type boolean
	 */
	ci.prototype.evt__salida_tab = function(pantalla_destino) {
		return true;
	};
	
	/**
	 * Cuando el componente tiene navegacion wizard, navega hacia la pantalla anterior
	 */
	ci.prototype.ir_a_anterior = function() {
		this.ir_a_pantalla('_anterior');	
	};	

	/**
	 * Cuando el componente tiene navegacion wizard, navega hacia la pantalla siguiente
	 */	
	ci.prototype.ir_a_siguiente = function() {
		this.ir_a_pantalla('_siguiente');
	};

	//--- Control de TABS
	
	/**
	 * Activa un tab previamente desactivado
	 * @param {string} id Id del tab/pantalla a activar
	 * @see #desactivar_tab
	 */
	ci.prototype.activar_tab = function(id) {
		if (this.existe_pantalla(id) && isset(this._pantallas_inactivas[id])) {	//Si la pantalla esta efectivamente inactiva
			this._pantallas[id] = true;								
			var boton = this.get_tab(id);
			if(boton) {											//Obtengo el elemento y le actualizo el evento onclick
				boton.onclick = this._pantallas_inactivas[id];
				this._pantallas_inactivas[id] = null;
				boton.className = '';
			}
		}
	};

	/**
	 * Impide que el usuario pueda pulsar sobre un tab o solapa, aunque mantiene la misma visible
	 * @param {string} id Id. del tab/pantalla destino
	 * @see #activar_tab
	 */
	ci.prototype.desactivar_tab = function(id) {
		if (this.existe_pantalla(id) && ! isset(this._pantallas_inactivas[id])) {	//Si la pantalla esta efectivamente activa
			this._pantallas[id] = false;							//Desactivo la pantalla
			var boton = this.get_tab(id);			
			if(isset(boton)) {
				this._pantallas_inactivas[id] = boton.onclick;
				boton.onclick = null;
				boton.className = 'ci-tabs-boton-desact';
			}
		}
	};

	/**
	 * Muestra un tab previamente ocultado
	 * @param {string} id Id. del tab/pantalla destino
	 * @see #desactivar_tab
	 */
	ci.prototype.mostrar_tab = function (id) {
		if (this.existe_pantalla(id)) {
			tab = this.get_tab(id);
			if(tab) {
				tab.parentNode.style.display = '';
			}
		}
	};

	/**
	 * Oculta un tab completo
	 * @param {string} id Id. de la pantalla asociada al tab
	 * @see #mostrar_tab
	 */
	ci.prototype.ocultar_tab = function (id) {
		if (this.existe_pantalla(id)) {		
			tab = this.get_tab(id);
			if(tab) {
				tab.parentNode.style.display = 'none';
			}
		}
	};

	/**
	 * Retorna la referencia al tag HTML que contiene un tab o solapa
	 * @type <a href=http://developer.mozilla.org/en/docs/DOM:element>element</a>
	 * @param {string} id Id del tab/pantalla
	 */
	ci.prototype.get_tab = function(id) {
		if (id == this._id_en_controlador) {
			var mensaje = 'No es posible modificar el estado del tab correspondiente a la pantalla actual';
			notificacion.agregar(mensaje);
			notificacion.mostrar();
			throw mensaje;
		}
		if (this.existe_pantalla(id)) {
			return document.getElementById(this._input_submit + '_cambiar_tab_' + id);
		}
	};	
	
	ci.prototype.existe_pantalla = function (id)
	{
		if (isset(this._pantallas[id])) {
			return true;
		}
		return false;
	}	
	
	/**
	 * Retorna la referencia al tag HTML al pie del cuerpo del CI
	 * @type <a href=http://developer.mozilla.org/en/docs/DOM:element>element</a>	 	
	 */	
	ci.prototype.nodo_pie = function() {
		return document.getElementById(this._instancia + '_pie');
	};
	
	//----------------------------------------------------------------  
	//---Servicios AJAX

	/**
	 * Pide al servidor un conjunto de datos en forma asincrónica
	 * Al ser un pedido asincronico necesita una clase/metodo aparte para notificar la respuesta
	 * 
	 * @param {string} metodo Sufijo del método PHP al que se le hara la pregunta (Si el método es 'ajax__mirespuesta' necesita ingresar 'mirespuesta')
	 * @param {mixed} parametros Parametros que se enviaran al servidor. Se recibirán en el primer parámetro del método php. Puede ser un tipo simple, arreglo o arreglo asociativo
	 * @param {object} clase_js Objeto javascript al que se le retornará la respuesta del servidor, usualmente 'this'
	 * @param {function} funcion_js Metodo de la clase al que se le retornará la respuesta del servidor.  La estructura de datos que retorne el server se utilizará como 1er parámetro en la llamada
	 * @param {mixed} contexto_js Opcional. Se puede incluir una variable conteniendo un contexto a recordar cuando se notifique la respuesta. Posteriormente se utiliza como 2do parámetro en la llamada de la callback
	 */
	ci.prototype.ajax = function(metodo, parametros, clase_js, funcion_js, contexto_js) {
		var respuesta = new ajax_respuesta('D');
		respuesta.set_callback(clase_js, funcion_js);
		respuesta.set_contexto(contexto_js);
		var callback_real = {
			success: respuesta.recibir_respuesta,
			failure: toba.error_comunicacion,
			scope: respuesta
		};
		var param = {'ajax-metodo': metodo, 'ajax-modo': 'D', 'ajax-param': serializar(parametros)};
		var vinculo = vinculador.get_url(null, null, 'ajax', param, [this._id]);
		var con = conexion.asyncRequest('GET', vinculo, callback_real, null);		
	};
	
	/**
	 * Pide al servidor en forma asincrónica un HTML que actualizara un nodo dado
	 * 
	 * @param {string} metodo Sufijo del método PHP al que se le hara la pregunta (Si el método es 'ajax__mirespuesta' necesita ingresar 'mirespuesta')
	 * @param {mixed} parametros Parametros que se enviaran al servidor. Se recibirán en el primer parámetro del método php. Puede ser un tipo simple, arreglo o arreglo asociativo
	 * @param {object} nodo_html Nodo HTML que se actualizará con la respuesta del server. Se utiliza la propiedad innerHTML del nodo.
	 */
	ci.prototype.ajax_html = function(metodo, parametros, nodo_html) {
		var respuesta = new ajax_respuesta('H');
		respuesta.set_nodo_html(nodo_html);
		var callback_real = {
			success: respuesta.recibir_respuesta,
			failure: toba.error_comunicacion,
			scope: respuesta
		};
		var param = {'ajax-metodo': metodo, 'ajax-modo': 'H', 'ajax-param': serializar(parametros)};
		var vinculo = vinculador.get_url(null, null, 'ajax', param, [this._id]);
		var con = conexion.asyncRequest('GET', vinculo, callback_real, null);		
	};		
	
	/**
	 * Pide al servidor un conjunto de datos en forma asincrónica
	 * Al ser un pedido asincronico necesita una clase/metodo aparte para notificar la respuesta
	 * 
	 * @param {string} metodo Sufijo del método PHP al que se le hara la pregunta (Si el método es 'ajax__mirespuesta' necesita ingresar 'mirespuesta')
	 * @param {mixed} parametros Parametros que se enviaran al servidor. Se recibirán en el primer parámetro del método php. Puede ser un tipo simple, arreglo o arreglo asociativo. En caso de necesitar un tipo más compejo serializar manualmente
	 * @param {object} clase_js Objeto javascript al que se le retornará la respuesta del servidor, usualmente 'this'
	 * @param {function} funcion_js Metodo de la clase al que se le retornará la respuesta del servidor. Un objeto de tipo ajax_respuesta se utilizará como 1er parámetro en la llamada
	 * @param {mixed} contexto_js Opcional. Se puede incluir una variable conteniendo un contexto a recordar cuando se notifique la respuesta. Posteriormente se utiliza como 2do parámetro en la llamada de la callback
	 * @see ajax_respuesta
	 */
	ci.prototype.ajax_cadenas = function(metodo, parametros, clase_js, funcion_js, contexto_js) {
		var respuesta = new ajax_respuesta('P');
		respuesta.set_callback(clase_js, funcion_js);
		respuesta.set_contexto(contexto_js);
		var callback_real = {
			success: respuesta.recibir_respuesta,
			failure: toba.error_comunicacion,
			scope: respuesta
		};
		var param = {'ajax-metodo': metodo, 'ajax-modo': 'P', 'ajax-param': parametros};
		var vinculo = vinculador.get_url(null, null, 'ajax', param, [this._id]);
		var con = conexion.asyncRequest('GET', vinculo, callback_real, null);		
	};
	
toba.confirmar_inclusion('componentes/ci');
