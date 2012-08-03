//--------------------------------------------------------------------------------
//Clase singleton Toba
var toba;

/**
 * @class Clase estática con servicios globales al framework, usar sin instanciar por ej. <em>toba.componentes();</em>
 * @constructor
 */
toba = new function() {
	this._cons_incl = ['basicos/basico', 'basicos/toba'];
	this._cons_carg = ['basicos/basico', 'basicos/toba'];
	this._url_origen = document.location.href;
	this._objetos = [];
	this._callback_inclusion = null;
	this._ajax = false;
	this._mostrar_aguardar = true;
	this._enviado = false;
	this._menu_popup = false;
	this._onload = [];
};

	/**
	 * @private
	 */
	toba.agregar_objeto = function(o) {
		this._objetos[o._instancia] = o;
	};

	/**
	 * @private
	 */	
	toba.eliminar_objeto = function (id) {
		delete(this._objetos[id]);
	};
	
	/**
	 * Retorna los ids de los componentes instanciados en esta página
	 * @type string
	 */
	toba.componentes = function() {
		var nombres = Array();
		for (o in this._objetos) {
			var clase = getObjectClass(this._objetos[o]);
			nombres.push(this._objetos[o]._instancia + ' [' + clase + ']');
		}
		return nombres.join(', ');
	};
	
	/**
	 * Retorna una referencia a la URL de una imagen especifica
	 * @type string
	 */
	toba.imagen = function (nombre) {
		return lista_imagenes[nombre];
	};
	
	/**
	 * @private
	 */
	toba.set_callback_incl = function(callback) {
		this._callback_inclusion = callback;
	};
	
	/**
	 * Incluye una lista de consumos javascript en forma dinamica
	 * @param {array} consumos Lista de consumos relativos a www/js, sin extension .js (eg. efs/ef_combo)
	 */
	toba.incluir = function(consumos) {
		var a_incluir = [];
		//Se divide en dos para garantizar la notificacion de fin de carga
		for(var i=0; i< consumos.length; i++) {
			//Evita que se cargue dos veces un consumo
			if (! in_array(consumos[i], this._cons_incl) ) {
				this._cons_incl.push(consumos[i]);
				a_incluir.push(consumos[i]);
			}
		}
		if (a_incluir.length > 0) {
			for (i=0; i < a_incluir.length; i++) {
				include_source(toba_alias + '/js/' + a_incluir[i] + '.js');		
			}
		} else {
			//Cuando no carga nada se termina de cargar instantaneamente
			this._disparar_callback_incl();			
		}
	};

	/**
	 * Confirma la inclusión exitosa de un consumo (generalmente llamado por el mismo consumo)
	 * @private
	 */
	toba.confirmar_inclusion = function(consumo) {
		this._cons_carg.push(consumo);
		if (! in_array(consumo, this._cons_incl)) {
			//Por si se incluyo a mano
			this._cons_incl.push(consumo);	
		} else {
			if (isset(this._callback_inclusion) && this._cons_carg.length == this._cons_incl.length) {
				//Se notifica el fin de la carga
				this._disparar_callback_incl();
			}
		}
	};

	/**
	 *@private
	 */
	toba._disparar_callback_incl = function() {
		eval_code(this._callback_inclusion);
		delete(this._callback_inclusion);		
	};
	
	/**
	 * Fuerza a que la comunicacion con el server se haga como un pedido AJAX hacia un componente específico
	 * en lugar del request clásico POST/GET a todo el framework
	 * @param {ei} componente Componente que se va a comunicar con su par PHP
	 * @param {function} servicio Método de esta clase que escucha la respuesta del servidor (servicio)
	 */
	toba.set_ajax = function(componente, servicio) {
		this._ajax = componente;
		this._ajax_servicio = servicio;
	};
	
	
	/**
	 * Comunica los eventos al servidor ya sea a través del clásico POST (haciendo un submit del form)
	 * o a través de httprequest (AJAX)
	 * @see #set_ajax
	 */
	toba.comunicar_eventos = function() {
		if (this._ajax && ! this._hay_uploads()) {
			//Por ahora si hay uploads, usa el metodo convencional ya que los bugs son varios
			var callback =
			{
			  success: this._ajax_servicio,
			  failure: this.error_comunicacion,
			  upload: this._ajax_servicio,
			  scope: this
			};
			var vinculo = vinculador.get_url(null, null, 'html_parcial', null, [ this._ajax._id ] );
			
			//Averigua si posee algún ef upload
			conexion.setForm($$('formulario_toba'), this._hay_uploads());
			var con = conexion.asyncRequest('POST', vinculo, callback, null);
		} else {
			if (this._enviado) {
				return;	//Evita el doble posteo
			}
			this._enviado = true;
			if (toba_espera !== 0) {
				setTimeout ("mostrar_esperar()", toba_espera);
			}
			document.formulario_toba.submit();
		}
	};
	
	toba._hay_uploads = function() {
		for (o in this._objetos) {
			var clase = getObjectClass(this._objetos[o]);
			if (clase == 'ei_formulario' || clase == 'ei_formulario_ml') {
				var efs = this._objetos[o].efs();
				for (var ef in efs) {
					if (getObjectClass(efs[ef]) == 'ef_upload') {
						return true;
					}
				}
			}
		}
		return false;	
	}

	/**
	 * Realiza un pedido GET asincronico simple al servidor, enviando informacion y esperando la respuesta en una funcion aparte
	 * debido a que la respuesta no es sincronica
	 * @param {string} vinculo Vinculo creado con el vinculador
	 * @param {function} nombre_callback Funcion que escucha la respuesta del servidor (opcional si no se quiere escuchar respuesta)
	 */
	toba.comunicar_vinculo = function(vinculo, nombre_callback) {
		var callback = {
			success: nombre_callback, 
			failure: toba.error_comunicacion
		}; 
		var con = conexion.asyncRequest("GET", vinculo, callback, null);
	};
	
	/**
	 * Navega hacia una opción del menú
	 * @param {string} proyecto Nombre del Proyecto
	 * @param {string} operacion Id operación destino
	 * @param {boolean} es_popup Indica si se abrira en la ventana actual o una nueva.
	 * @param {boolean} es_zona Indica si propaga la zona actualmente cargada (si la hay)
	 */
	toba.ir_a_operacion = function(proyecto, operacion, es_popup, es_zona) {
		if (this._menu_popup) {
			es_popup = true;
		}	
		if (typeof es_zona == 'undefined') {
			es_zona = false;
		}
		var url = vinculador.get_url(proyecto, operacion, null, null, null, true, es_zona);
		if (isset(this._callback_menu)) {
			var continuar = this._callback_menu[0].call(this._callback_menu[1], proyecto, operacion, url, es_popup);
			if (! continuar) {
				return false;
			}
		}		
		if (! isset(es_popup) || ! es_popup) {
			document.location.href = url;
		} else {
			celda = 'paralela';
			parametros = {'resizable':1, 'scrollbars' : '1'};
			url = vinculador.concatenar_parametros_url(url, {'tcm': celda});
			abrir_popup(celda, url, parametros);
			setTimeout ("toba.set_menu_popup(false)", 100);	//Para evitar que quede fijo
		}
		if (this._menu_popup) {
			return false;
		}		
	};	

	/**
	 * Cambia la forma en la que trabaja el menu, haciendo que los links se abran en una nueva celda de memoria y en un popup
	 */
	toba.set_menu_popup = function(estado) {	
		//var links = $("menu-h").getElementsByTagName("a");
		var links = getElementsByClass('nivel-0', $$('menu-h'));
		for (var i=0; i<links.length; i++) {
			if (estado) {
				agregar_clase_css(links[i], "menu-link-alt");
			} else {
				quitar_clase_css(links[i], "menu-link-alt");
			}
		}
		this._menu_popup = estado;
	}	
	
	/**
	 * Activar/Desactiva la navegacion via ajax de la operacion
	 */
	toba.set_navegacion_ajax = function(estado) {	
		for (o in this._objetos) {
			var clase = getObjectClass(this._objetos[o]);
			//Busco el ci raiz
			if (clase == 'ci' && ! this._objetos[o].controlador) {
				this._objetos[o]._ajax = estado;
			}
		}	
	}	
	
	/**
	 * Retorna verdadero si esta activa la navegacion via ajax de la operacion
	 */
	toba.get_navegacion_ajax = function() {	
		for (o in this._objetos) {
			var clase = getObjectClass(this._objetos[o]);
			//Busco el ci raiz
			if (clase == 'ci' && ! this._objetos[o].controlador) {
				return this._objetos[o]._ajax;
			}
		}	
		return false;
	}
	
	/**
	 *	Permite definir una funcion o método por la cual pasan todos los pedidos de cambio de operación desde el menú
	 * @param {function} callback Función que se invocara pasando por parametros (proyecto, operacion, url, es_popup)
	 * @param {object} contexto Opcional, objeto al cual pertenece la función (si pertenece a alguno)
	 */
	toba.set_callback_menu = function(callback, contexto) {
		this._callback_menu = [callback, contexto];
	};

	/**
	 * Determina si en alguno de los formularios activos de la pantalla sufrio modificaciones
	 *  @return {boolean}
	 */
	toba.hay_cambios = function() {
		for (o in this._objetos) {
			var clase = getObjectClass(this._objetos[o]);
			if (clase == 'ei_formulario' || clase == 'ei_formulario_ml') {
				if (this._objetos[o].hay_cambios()) {
					return true;
				}
			}
		}
		return false;
	}
	
	/**
	 * Callback utilizada para escuchar la respuesta del html_parcial, esto es un componente recibe nuevamente su html contenido.<br>
	 * La respuesta se divide en tres partes: el innerHTML, los consumos a incluir y el eval del js suelto
	 * @see #set_ajax
	 */
	toba.servicio__html_parcial = function(respuesta) {
		//Primero se borra el rastro de los objetos anteriores en el objeto
		for (var d in this._ajax._deps) {
			toba.eliminar_objeto(this._ajax._deps[d]._instancia);
			delete(this._ajax._deps[d]);
		}
		var partes = this.analizar_respuesta_servicio(respuesta);
		if (partes === false) {
				notificacion.agregar('Se ha producido un error en una etapa temprana del request, verifique el log del servidor');
				notificacion.mostrar();
		} else {
			
			//Reseteo la variable que contiene los objetos del pedido de pagina anterior.
			this._objetos = [];
			
			if (partes[0] != '') {
				//-- Trata de interpretar y agregar cualquier html/js adhoc generado durante los eventos
				ejecutar_scripts(partes[0]);
				var nodo = this._ajax.raiz().parentNode.innerHTML;
				nodo = nodo + partes[0];
			}

			//-- Se cambia la barra superior (busca el fin del tag para no duplicarlo)
			if (partes[1] != '' && isset($$('barra_superior'))) {
				var barra = partes[1].substr(partes[1].indexOf('>') + 1);
				$$('barra_superior').innerHTML = barra;
			}

			//-- Se agrega el html (busca el comienzo del primer div para no duplicar el table)
			ejecutar_scripts(partes[2]);
			this._ajax.raiz().innerHTML = partes[2].substr(partes[2].indexOf('<div'));

			//-- Se cambia el div del editor (si existe)
			if (partes[3] != '') {
				var div = $$('editor_previsualizacion');
				if (isset(div)) {
					div.innerHTML = partes[3];
				}
			}

			//-- Se incluyen librerias js y se programa la evaluacion del codigo cuando termine
			toba.set_callback_incl(partes[5]);
			eval_code(partes[4]);		
			for (var i = 0; i < this._onload.length; i++) {
				this._onload[i]();
			}
		}
	};
	
	/**
	 * Analiza una respuesta ajax en texto plana, partiendola por separadores [--toba--]
	 * @type Array
	 */
	toba.analizar_respuesta_servicio = function(respuesta) {
		var texto = respuesta.responseText;
		var partes = [];
		var pos, pos_anterior = 0;
		while (pos != -1) {
			pos = texto.indexOf('[--toba--]', pos_anterior);
			if (pos != -1) {
				partes.push(texto.substr(pos_anterior, pos-pos_anterior));
				pos_anterior = pos + 10;
			}
		}
		if (pos_anterior === 0) {		//Si no volvieron partes probablemente sea un js causado por un error prematuro
			return false;						//La decision la toma el llamador
		}
		var restante = texto.substr(pos_anterior);
		if (restante.length >0) {
			partes.push(restante);
		}
		return partes;
	};
	
	/**
	 * Callback utiliza para reaccionar ante un error en la comunicacion AJAX con el servidor
	 * Redefinir en caso de querer mostrar algun mensaje o accion distinta
	 */
	toba.error_comunicacion = function(error) {
		//Se asegura que no este navegando hacia otra página y ese sea el motivo del error ajax
		if (typeof toba != 'undefined' && typeof toba._enviado != 'undefined' && ! toba._enviado) {
			var url_actual = document.location.href;
			//-- Si se utiliza un link directo, debería permitir la navegación aunque la respuesta no llegue nunca
			if (toba._url_origen != url_actual) {
				notificacion.limpiar();
				var mensaje = "Error de comunicación AJAX<br>";
				notificacion.agregar(mensaje, 'error', null, var_dump(error, true));
				notificacion.mostrar();
			}
		}
	};

	/**
	 * @private
	 */	
	toba.falta_imagen = function(src) {
		alert('No se encontro la imagen: ' + src);
	};
	
	/**
	 * Muestra un div/imagen conteniendo un mensaje de 'Procesando'
	 * @see #fin_aguardar
	 * @see #set_aguardar
	 */
	toba.inicio_aguardar = function() {
		if (this._mostrar_aguardar) {
			var div = document.getElementById('div_toba_esperar');
			if (div.currentStyle) {
				//Arreglo para el IE para que simule el fixed
				if (div.currentStyle.position == 'absolute') {
					var y = (document.documentElement && document.documentElement.scrollTop) ?
							 	document.documentElement.scrollTop :
							 	document.body.scrollTop;
					div.style.top = y;
				}
			}
			div.style.display = '';	
			document.body.style.cursor = 'wait';
		}
	};
	
	/**
	 * Oculta el div/imagen de 'Procesando'
	 * @see #inicio_aguardar
	 * @see #set_aguardar
	 */
	toba.fin_aguardar = function() {
		if (this._mostrar_aguardar) {		
			document.getElementById('div_toba_esperar').style.display = 'none';
			document.body.style.cursor = '';
		} else {
			this.set_aguardar(true);	
		}
	};
	
	/**
	 * Determina si mostrar o no un div/imagen contienendo un mensaje de 'Procesando' cuando se 
	 * hacen pedidos asincronicos (ajax)
	 * @param {boolean} aguardar Habilitar el mensaje?
	 * @see #inicio_aguardar
	 * @see #fin_aguardar
	 */
	toba.set_aguardar = function(aguardar) {
		this._mostrar_aguardar = aguardar;
	};
	
	/**
	 *
	 */
	toba.agregar_onload = function(llamada) {
		this._onload.push(llamada);
		agregarEvento(window, 'load', llamada);
	}
