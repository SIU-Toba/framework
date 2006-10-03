
ci.prototype = new ei();
ci.prototype.constructor = ci;

/**
 * @class Componente responsable de manejar la pantalla y sus distintos elementos
 * @constructor
 */
function ci(instancia, form, input_submit, id_en_controlador) {
	this.controlador = null;							//CI contenedor	
	this._instancia = instancia;						//Nombre de la instancia del objeto, permite asociar al objeto con el arbol DOM
	this._form = form;									//Nombre del form contenedor del objeto
	this._input_submit = input_submit;					//Campo que se setea en el submit del form 
	this._id_en_controlador = id_en_controlador;		//ID del tab actual
	this._deps = {};									//Listado asociativo de dependencias
	this._en_submit = false;							//¿Esta en proceso de submit el CI?
	this._silencioso = false;							//¿Silenciar confirmaciones y alertas? Util para testing
	this._evento_implicito = new evento_ei('', true, '');	//Por defecto se valida los objetos contenidos
	this._parametros = "";								//Parametros opcionales que se pasan al server
	this.reset_evento();
}

	/**
	 *	@private
	 */
	ci.prototype.agregar_objeto = function(objeto, identificador) {
		objeto.set_controlador(this);
		this._deps[identificador] = objeto;
	};

	/**
	 * Retorna la referencia a un componente hijo o dependiente del actual
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
	}
	
	ci.prototype.iniciar = function() {
		for (var dep in this._deps) {
			this._deps[dep].iniciar();
		}
	};
	
	/**
	 * Retorna el nodo DOM donde se muestra el componente (incluye la raiz y el cuerpo)
	 * @type <a href=http://developer.mozilla.org/en/docs/DOM:element>element</a>	 
	 */
	ci.prototype.nodo = function() {
		return document.getElementById(this._instancia + '_cont');	
	};
	
	//---Eventos	
	ci.prototype.set_evento = function(evento) {
		this._evento = evento;
		this.submit();
	};
	

	//---SUBMIT
	/**
	 * Intenta realizar el submit de todos los objetos asociados
	 * El proceso de SUBMIT se divide en partes:<br>
	 * 1- Se sube hasta el CI raiz<br>
	 * 2- El raiz analiza si puede hacerlo (recorriendo los hijos)<br>
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
				//toba.set_ajax(this, toba.servicio__html_parcial);
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
			document.getElementById(this._input_submit + "__param").value = this._parametros;
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
				if(! ( this["evt__" + this._evento.id]() ) ){
					this.reset_evento();
					return false;
				}
			}
			//- 3 - Hay que confirmar la ejecucion del evento?
			//La confirmacion se solicita escribiendo el texto de la misma
			if(this._evento.confirmar !== "") {
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
	 */
	ci.prototype.notificar = function(mostrar) {
		var barra = document.getElementById('barra_' + this._instancia);
		if (barra) {
			if (mostrar) {
				barra.style.display = '';
			} else {
				barra.style.display = 'none';
			}
		}
	};

	//---Navegación 

	/**
	 * Ejecuta el evento de cambiar de pantalla (similar a cambiar de tab manualmente)
	 * @param {string} pantalla Id. de la pantalla destino
	 */
	ci.prototype.ir_a_pantalla = function(pantalla) {
		this.set_evento(new evento_ei('cambiar_tab_' + pantalla, true, ''));
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
	 * @see #desactivar_tab
	 */
	ci.prototype.activar_tab = function(id) {
		var boton = this.get_tab(id);
		if(boton){
			if (boton.onclick_viejo !== '') {
				boton.onclick = boton.onclick_viejo;
			}
		}
	};

	/**
	 * Impide que el usuario pueda pulsar sobre un tab o solapa, aunque mantiene la misma visible
	 * @param {string} id Id. de la pantalla destino
	 * @see #activar_tab
	 */
	ci.prototype.desactivar_tab = function(id) {
		var boton = this.get_tab(id);
		if(boton) {
			boton.onclick_viejo = boton.onclick;
			boton.onclick = '';
		}
	};

	/**
	 * Muestra un tab previamente ocultado
	 * @param {string} id Id. de la pantalla destino
	 */
	ci.prototype.mostrar_tab = function (id) {
		tab = this.get_tab(id);
		if(tab) {
			tab.parentNode.style.display = '';
		}
	};

	/**
	 * Oculta un tab completo
	 * @param {string} id Id. de la pantalla asociada al tab
	 * @see #mostrar_tab
	 */
	ci.prototype.ocultar_tab = function (id) {
		tab = this.get_tab(id);
		if(tab) {
			tab.parentNode.style.display = 'none';
		}
	};

	/**
	 * Retorna la referencia al tag HTML que contiene un tab o solapa
	 * @type <a href=http://developer.mozilla.org/en/docs/DOM:element>element</a>	 	
	 */
	ci.prototype.get_tab = function(id) {
		if (id == this._id_en_controlador) {
			notificacion.agregar('No es posible modificar el estado del tab correspondiente a la pantalla actual');
			notificacion.mostrar();
			return;
		}
		return document.getElementById(this._input_submit + '_cambiar_tab_' + id);
	};	
	
toba.confirmar_inclusion('componentes/ci');
