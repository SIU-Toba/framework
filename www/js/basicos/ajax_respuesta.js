/**
 * @class Clase estatica para el manejo de una respuesta AJAX
 * @constructor
 **/
function ajax_respuesta(modo) {
	this._modo = modo;
	this._contexto = null;
	this._respuesta_plana = '';
}

ajax_respuesta.prototype.constructor = ajax_respuesta;

	/**
	 * @private
	 */	
	ajax_respuesta.prototype.set_callback = function(clase, funcion) {
		if (typeof clase != 'object') {
			throw 'AJAX DATOS: Se requiere que el 3er parámetro sea un objeto';
		}
		if (typeof funcion != 'function') {
			throw 'AJAX DATOS: Se requiere que el 4to parámetro sea un función javascript válida';
		}
		this._clase = clase;
		this._funcion = funcion;
	};
	
	/**
	 * @private
	 */		
	ajax_respuesta.prototype.set_contexto = function(contexto) {
		this._contexto = contexto;
	};
	
	/**
	 * @private
	 */		
	ajax_respuesta.prototype.set_nodo_html = function(nodo) {
		if (nodo !== null && typeof nodo == 'object' && isset(nodo.innerHTML)) {
			this._nodo_html = nodo;
		} else {
			throw 'AJAX HTML: Se requiere que el 3er parámetro sea un nodo html válido';
		}
	};
	
	/**
	 * Retorna parte de una respuesta a un pedido de datos plano (sin encoding)
	 */
	ajax_respuesta.prototype.get_cadena = function(clave) {
		var string_clave = '<--toba:' + clave + '-->';
		var inicial = this._respuesta_plana.indexOf(string_clave);
		if (inicial != -1) {
			inicial = inicial + string_clave.length;
			var fin = this._respuesta_plana.indexOf('<--toba', inicial);
			if (fin == -1) {
				fin = this._respuesta_plana.length;
			}
			return this._respuesta_plana.substr(inicial, fin-inicial);
		} else {
			return null;
		}
	};
		
	/**
	 * @private
	 */		
	ajax_respuesta.prototype.recibir_respuesta = function(response) {
		try {
			switch (this._modo) {
				case 'D':
					var parametro; 
					//-- Comunicación de datos codificados con JSON
					if (response.responseText !== '') {
						parametro = JSON.parse(response.responseText);
					} else{
						parametro = '';
					}
					this._funcion.call(this._clase, parametro, this._contexto);
					break;
				case 'H':
					//-- Comunicación de HTML
					this._nodo_html.innerHTML = response.responseText;
					break;
				case 'P': 
					this._respuesta_plana = response.responseText;
					//-- Comunicación de información plana
					this._funcion.call(this._clase, this, this._contexto);
					break;		
			}			

		} catch (e) {
			var	componente = "<textarea id='displayMore' class='ef-input-solo-lectura' cols='40' rows='35' readonly='true' style='display:none;'>" + response.responseText + '</textarea>';
			var error = 'Error en la respuesta.<br>'  + 'Error JS:<br>' + e  +  '<br>Mensaje Server:<br>' +
					"<a href='#' onclick='toggle_nodo(document.getElementById(\"displayMore\"));'>Mas</a><br>" + componente
					+ '<br><br>' + 'Ver el log del sistema para más información';
			
			notificacion.limpiar();
			notificacion.agregar(error);
			notificacion.mostrar();
		}
	};
	
toba.confirmar_inclusion('basicos/ajax_respuesta');