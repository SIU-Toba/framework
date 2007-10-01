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
	this._objetos = [];
	this._callback_inclusion = null;
	this._ajax = false;
	this._mostrar_aguardar = true;
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
	
	toba._disparar_callback_incl = function() {
		eval(this._callback_inclusion);
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
		if (this._ajax) {
			var callback =
			{
			  success: this._ajax_servicio,
			  failure: this.error_comunicacion,
			  scope: this
			};
			var vinculo = vinculador.get_url(null, null, 'html_parcial');
			conexion.setForm('formulario_toba');
			var con = conexion.asyncRequest('POST', vinculo, callback, null);
		} else {
			document.formulario_toba.submit();
		}
	};
	
	/**
	 * Realiza un pedido GET asincronico simple al servidor, enviando informacion y esperando la respuesta en una funcion aparte
	 * debido a que la respuesta no es sincronica
	 * @param {string} vinculo Vinculo creado con el vinculador
	 * @param {function} nombre_callback Funcion que se invoca una vez que responde el server (opcional si no se quiere escuchar respuesta)
	 */
	toba.comunicar_vinculo = function(vinculo, nombre_callback) {
		var callback = {
			success: nombre_callback, 
			failure: toba.error_comunicacion
		}; 
		var con = conexion.asyncRequest("GET", vinculo, callback, null);
	};
	
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
		//-- Se agrega el html
		this._ajax.raiz().innerHTML = partes[0];
		
		//-- Se incluyen librerias js y se programa la evaluacion del codigo cuando termine
		toba.set_callback_incl(partes[2]);
		eval(partes[1]);
	};
	
	/**
	 * Analiza una respuesta ajax en texto plana, partiendola por separadores <--toba-->
	 * @type Array
	 */
	toba.analizar_respuesta_servicio = function(respuesta) {
		var texto = respuesta.responseText;
		var partes = [];
		var pos, pos_anterior = 0;
		while (pos != -1) {
			pos = texto.indexOf('<--toba-->', pos_anterior);
			if (pos != -1) {
				partes.push(texto.substr(pos_anterior, pos-pos_anterior));
				pos_anterior = pos + 10;
			}
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
		alert('Error de comunicacion AJAX');
	};

	/**
	 * @private
	 */	
	toba.falta_imagen = function(src) {
		alert('No se encontro la imagen: ' + src);
	}
	
	/**
	 * Muestra un div/imagen contienendo un mensaje de 'Procesando'
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
	 * @param {boolean} aguardar habilitar el mensaje?
	 * @see #inicio_aguardar
	 * @see #fin_aguardar
	 */
	toba.set_aguardar = function(aguardar) {
		this._mostrar_aguardar = aguardar;
	};
