/**
 * @class Representa un evento que será consumido por un componente, es sólo una estructura de datos
 * @param id Identificador del evento, ej: 'modificar'
 * @param validar ¿Se debe validar antes de hacer submit?
 * @param confirmar ¿Se debe confirmar antes de hacer submit?
 * @constructor 
 */
function evento_ei(id, validar, confirmar, parametros, es_implicito) {
	this.id = id;
	this.validar = (typeof validar == 'undefined') ? true : validar;
	this.confirmar = (typeof confirmar == 'undefined') ? false : confirmar;
	this.es_implicito = (typeof es_implicito == 'undefined') ? false: es_implicito;
	this.parametros = parametros;
	this._silencioso = false;
	this._id_dep = null;
}

/**
 * @class Clase base de los componentes toba en javascript
 * @constructor
 * @phpdoc Componentes/Eis/toba_ei toba_ei 
 */
function ei(instancia, input_submit) {
	this._instancia = instancia;
	this._input_submit = input_submit;
	this._evento_condicionado_a_datos = false;
	
	/**
	 * Componente padre o contenedor del actual
	 * @type ei
	 */
	this.controlador = null;
}
ei.prototype.constructor = ei;

	/**
	 * @private
	 */
	ei.prototype.iniciar = function() {
	};
	
	/**
	 * Ventana de código que se ejecuta luego de que el componente ha terminado de iniciarse en javascript
	 * @ventana
	 */
	ei.prototype.ini = function() {
	};	

	/**
	 * @private
	 */
	ei.prototype.set_controlador = function(ci, identificador) {
		this.controlador = ci;
		this._id_dep = identificador;
	};
	
	//----------------------------------------------------------------
	//---Eventos	 
	/**
	 * Indica si los eventos implicitos del componente estan condicionados al cambio de datos
	 * @param {boolean} esta_condicionado
	 **/
	ei.prototype.set_eventos_condicionados_por_datos = function(esta_condicionado)
	{
		this._evento_condicionado_a_datos = esta_condicionado;
	}
	
	/**
	 * Informa al componente la presencia de un nuevo evento
	 * @param {evento_ei} evento
	 * @param {boolean} hacer_submit Luego de informar el evento, se inicia el proceso de submit (por defecto true)
	 */
	ei.prototype.set_evento = function(evento, hacer_submit) {
		if (typeof hacer_submit == 'undefined') {
			hacer_submit = true;
		}
		this._evento = evento;
		if (hacer_submit) {
			this.submit();
		}
	};

	/**
	 * Determina cual es el evento que se utiliza cuando no se dispara ninguno explicitamente por el usuario
	 * @param {evento_ei} evento
	 */
	ei.prototype.set_evento_implicito = function(evento) {
		this._evento_implicito = evento;
	};
	
	/**
	 * Limpia el evento actualmente informado al componente
	 */
	ei.prototype.reset_evento = function() {
		this._evento = this._evento_implicito;
	};
		
	//---Submit
	
	/**
	 * Inicia el proceso de submit, este proceso recorre todos los componentes
	 * validandolos y preparandolos para una comunicación con el servidor
	 */
	ei.prototype.submit = function() {
		var padre_esta_listo = this.controlador && !this.controlador.en_submit();
		if (padre_esta_listo) {
			return this.controlador.submit();
		}
	};
	
	/**
	 * Determina si el componente puede hacer submit en base 
	 * al callback redefinible <em>evt__evento</em> donde evento es el id del evento disparado
	 * @type boolean
	 */
	ei.prototype.puede_submit = function() {
		if (this._evento) {
			//La confirmacion se solicita escribiendo el texto de la misma
			if (trim(this._evento.confirmar) !== "") {
				if (!this._silencioso && !(confirm(this._evento.confirmar))){
					this.reset_evento();
					return false;
				}
			}		
			var metodo = "evt__" + this._evento.id;
			var res;
			if(existe_funcion(this, metodo)){
				res = this[metodo](this._evento.parametros);
				if(typeof res != 'undefined' && !res ){		
					this.reset_evento();
					return false;
				}
			}
			metodo = "evt__" + this._id_dep + '__' + this._evento.id;
			if (this.controlador && existe_funcion( this.controlador, metodo)) {
				res = this.controlador[metodo](this._evento.parametros);
				if(typeof res != 'undefined' && !res ){		
					this.reset_evento();
					return false;
				}
			}
		}
		return true;
	};

	/**
	 * Limpia el componente de errores producidos anteriormente
	 */
	ei.prototype.resetear_errores = function() {
	};
	
	/**
	 * Ejecuta un vinculo producido por un evento
	 * Antes de ejecutar el vinculo se llama una callback <em>modificar_vinculo__evento</em> para
	 * que se pueda modificar alguna propiedad del vinculo
	 * @param {string} id_evento
	 * @param {string} id_vinculo 
	 * @see vinculador
	 */
	ei.prototype.invocar_vinculo = function(id_evento, id_vinculo) {
		// Busco la extension de modificacin de vinculos
		var funciv = 'modificar_vinculo__' + id_evento;
		if (existe_funcion(this, funciv)) {
			this[funciv](id_vinculo);
		}
		vinculador.invocar(id_vinculo);
	};

	//----------------------------------------------------------------  
	//---Servicios graficos 
	
	/**
	 * Referencia al tag HTML que contiene el html de todo el componente
	 */
	ei.prototype.cuerpo = function() {
		return document.getElementById('cuerpo_' + this._instancia);	
	};
	
	/**
	 * Referencia al tag HTML padre del componente
	 * @see #cuerpo
	 */
	ei.prototype.raiz = function() {
		return this.cuerpo().parentNode;
	};
	
	/**
	 * Invierte el colapsado del componente
	 * @see #colapsar
	 * @see #descolapsar
	 */
	ei.prototype.cambiar_colapsado = function() {
		cambiar_colapsado(this.boton_colapsar(), this.cuerpo());		
	};
	
	/**
	 * Colapsa el componente permitiendo, que el usuario puede descolapsarlo pulsando un ícono
	 */
	ei.prototype.colapsar = function() {
		colapsar(this.boton_colapsar(), this.cuerpo());
	};
	
	/**
	 * Descolpsa explícitamente el componente previamente colapsado
	 */
	ei.prototype.descolapsar = function() {
		descolapsar(this.boton_colapsar(), this.cuerpo());
	};
	
	/**
	 * Referencia al tag HTML que representa el boton de colapsar/descolapsar
	 */
	ei.prototype.boton_colapsar = function() {
		return document.getElementById('colapsar_boton_' + this._instancia);
	};

	/**
	 * Oculta el componente completo
	 * @see #mostrar
	 */
	ei.prototype.ocultar = function() {
		this.raiz().style.display = 'none';
	};

	/**
	 * Muestra un componente previamente ocultado
	 * @param {boolean} mostrar Pasando true o false permite mostrar u ocultar
	 * @see #ocultar
	 */	
	ei.prototype.mostrar = function(mostrar) {
		if (typeof mostrar == 'undefined') {
			mostrar = true;
		}	
		if (mostrar) {
			this.raiz().style.display = '';	
		} else {
			this.ocultar();	
		}		
	};	

	/**
	 * Desactiva un boton asociado al componente, 
	 * esto no permite que el usuario lo pulse aunque aun es visible
	 * @param {string} id Id. del boton/evento a desactivar
	 * @see #activar_boton
	 */
	ei.prototype.desactivar_boton = function(id) {
		this.get_boton(id).disabled = true;
	};

	/**
	 * Activa un boton previamente desactivado
	 * @param {string} id Id. del boton/evento a activar
	 * @see #desactivar_boton
	 */	
	ei.prototype.activar_boton = function(id) {
		this.get_boton(id).disabled = false;
	};

	/**
	 * Oculta un boton/evento de la vista del usuario
	 * @param {string} id Id. del boton/evento a ocultar
	 * @see #mostrar_boton
	 */	
	ei.prototype.ocultar_boton = function(id) {
		this.get_boton(id).style.display = 'none';
	};

	/**
	 * Muestra un boton previamente ocultado
	 * @param {string} id Id. del boton/evento a mostrar
	 * @see #ocultar_boton
	 */
	ei.prototype.mostrar_boton = function(id) {
		this.get_boton(id).style.display = '';
	};
	
	/**
	 * Referencia al tag HTML de un boton especifico
	 * @param {string} id Id. del boton/evento	 
	 */
	ei.prototype.get_boton = function(id) {
		return document.getElementById(this._input_submit + '_' + id);
	};
	
	ei.prototype.exportar_pdf = function() {
		var url = vinculador.get_url(null, null, 'vista_pdf', null, [this._id]);
		document.location.href = url;
	};

	ei.prototype.exportar_excel = function() {
		var url = vinculador.get_url(null, null, 'vista_excel', null, [this._id]);
		document.location.href = url;
	};
	
	ei.prototype.agregar_notificacion = function(mensaje, nivel) {
		var div = $$(this._input_submit + '_notificacion');
		var img = '<img src="'+ toba.imagen(nivel) + '"/> ';
		var clase = 'ei-barra-sup-desc-' + nivel;		
		div.innerHTML += "<table class='tabla-0 " + clase+ "'><tr><td class='ei-barra-sup-desc-img'>" + img +"</td><td>" + mensaje + "</td></table>\n";
	};	

	ei.prototype.limpiar_notificaciones = function() {
		var div = $$(this._input_submit + '_notificacion');
		div.innerHTML = '';
	};	
	
	//---- Filtrado de opciones de combos editables
	
	/**
	 * Filtrado de opciones de los combos editables:<br>
	 * Se comunica al servidor que debe refrescar las opciones de un ef combo editable en base al valor tipeado por el usuario
	 * Este método dispara la llamada asincronica al servidor
	 * @see #filtrado_ef_ce_respuesta
	 * @param {string} id_ef Id. del ef a refrescar (un ef esclavo)
	 * @param {string) valor. Formato: ef-;-valor-
	 */
	ei.prototype.filtrado_ef_ce_comunicar = function(id_ef, valor, fila) 
	{
		//Empaqueto la informacion que tengo que mandar.
		var parametros = {'filtrado-ce-ef': id_ef, 'filtrado-ce-valor' : valor};
		if (typeof fila != 'undefined') {
			parametros['filtrado-ce-fila'] = fila;
		}
		//-- Pasa los maestros de la cascad por parametros
		var maestros = this.get_valores_maestros(id_ef);
		valores = '';		
		for (var id_maestro in maestros) {
			valores +=  id_maestro + '-;-' + maestros[id_maestro] + '-|-';
		}
		parametros['cascadas-maestros'] = valores;
		
		var callback = {
			success: this.filtrado_ef_ce_respuesta,
			failure: toba.error_comunicacion,
			argument: id_ef,
			scope: this
		};
		var vinculo = vinculador.get_url(null, null, 'filtrado_ef_ce', parametros, [this._id]);
		var con = conexion.asyncRequest('GET', vinculo, callback, null);
	};

	/**
	 * Filtrado de opciones de los combos editables:<br>
	 * Respuesta del servidor ante el pedido de refresco de un ef ce puntual
	 * @param {Object} respuesta La respuesta es un objeto no asociativo con claves responseText que contiene las nuevas opciones del ef
	 */
	ei.prototype.filtrado_ef_ce_respuesta = function(respuesta)
	{
		if (respuesta.responseText === '') {
			var error = 'Error en la respuesta del filtrado de opciones, para más información consulte el log';
			notificacion.limpiar();
			notificacion.agregar(error);
			notificacion.mostrar();
		} else {
			try {
				
				var datos = eval('(' + respuesta.responseText + ')');
				if ('Array' == getObjectClass(datos)) {
					this.ef(respuesta.argument).set_opciones_rs(datos);
				} else {
					this.ef(respuesta.argument).set_opciones(datos);					
				}
				//this.evt__filtrado_ef_ce_fin(this.ef(respuesta.argument), datos);
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
	 * Filtrado de opciones de los combos editables:<br>
	 * Se comunica al servidor para validar un valor que no está entre las opciones visibles
	 * Este método dispara la llamada asincronica al servidor
	 * @see #filtrado_ef_ce_respuesta_validacion
	 * @param {string} id_ef Id. del ef a refrescar (un ef esclavo)
	 * @param {string) valor. Formato: ef-;-valor-
	 */
	ei.prototype.filtrado_ef_ce_validar = function(id_ef, valor, fila) 
	{
		//Empaqueto la informacion que tengo que mandar.
		var parametros = {'filtrado-ce-ef': id_ef, 'filtrado-ce-valor' : valor};
		if (typeof fila != 'undefined') {
			parametros['filtrado-ce-fila'] = fila;
		}
		var callback = {
			success: this.filtrado_ef_ce_respuesta_validacion,
			failure: toba.error_comunicacion,
			argument: id_ef,
			scope: this
		};
		var vinculo = vinculador.get_url(null, null, 'filtrado_ef_ce_validar', parametros, [this._id]);
		var con = conexion.asyncRequest('GET', vinculo, callback, null);
	};

	/**
	 * Filtrado de opciones de los combos editables:<br>
	 * Respuesta del servidor ante el pedido de refresco de un ef ce puntual
	 * @param {Object} respuesta La respuesta es un objeto no asociativo con claves responseText que contiene las nuevas opciones del ef
	 */
	ei.prototype.filtrado_ef_ce_respuesta_validacion = function(respuesta)
	{
		if (respuesta.responseText === '') {
			var error = 'Error en la respuesta del filtrado de opciones, para más información consulte el log';
			notificacion.limpiar();
			notificacion.agregar(error);
			notificacion.mostrar();
		} else {
			try {
				var datos = eval('(' + respuesta.responseText + ')');
				if ('Array' == getObjectClass(datos)) {
					this.ef(respuesta.argument).set_opciones_rs(datos, false);					
				} else {
					this.ef(respuesta.argument).set_opciones(datos,false);					
				}
				this.ef(respuesta.argument)._get_combo().selectOption(0,true,true);
			} catch (e) {
				var error = 'Error en la respuesta.<br>' + "Mensaje Server:<br>" + respuesta.responseText + "<br><br>Error JS:<br>" + e;
				notificacion.limpiar();
				notificacion.agregar(error);
				notificacion.mostrar();				
			}
		}
	};
	
	
//--------------------------------------------------------------------------------	
//Utilidades sobre arbol DOM 
if (self.Node && ! self.Node.prototype.swapNode) {
	/**
	 *	@ignore
	 */
	Node.prototype.swapNode = function (node) {
		var nextSibling = this.nextSibling;
		var parentNode = this.parentNode;
		node.parentNode.replaceChild(this, node);
		parentNode.insertBefore(node, nextSibling);  
	};
}

function intercambiar_nodos(nodo1, nodo2) {
	if (ie6omenor) {	//BUG del IE para mantener el estado de los checkbox
		var intercambio_vals = [];
		var inputs = document.getElementsByTagName('input');
		for (var i=0; i < inputs.length; i++) {
			if (inputs[i].type.toLowerCase() == 'checkbox' && inputs[i].id.indexOf('__fila__') == -1) {
				intercambio_vals.push( [inputs[i].id, inputs[i].checked]);
			}
		}	
	}
	nodo1.swapNode(nodo2);
	if (ie6omenor) {
		for (i=0; i < intercambio_vals.length; i++) {
			var check = intercambio_vals[i];
			document.getElementById(check[0]).checked = check[1];
		}
	}
}

toba.confirmar_inclusion('componentes/ei');