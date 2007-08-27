
/**
 * @class Clase estatica para el manejo de una respuesta AJAX
 * @constructor
 **/
function ajax_respuesta(modo) {
	this._modo = modo;
	this._contexto = null;
	this._partes = {};
};
ajax_respuesta.prototype.constructor = ajax_respuesta;

	ajax_respuesta.prototype.set_callback = function(clase, funcion) {
		if (typeof clase != 'object') {
			throw 'AJAX DATOS: Se requiere que el 3er parámetro sea un objeto';
		}
		if (typeof funcion != 'function') {
			throw 'AJAX DATOS: Se requiere que el 4to parámetro sea un función javascript válida';
		}
		this._clase = clase;
		this._funcion = funcion;
	}
	
	ajax_respuesta.prototype.set_contexto = function(contexto) {
		this._contexto = contexto;
	}
	
	ajax_respuesta.prototype.set_nodo_html = function(nodo) {
		if (nodo != null && typeof nodo == 'object' && isset(nodo['innerHTML'])) {
			this._nodo_html = nodo;
		} else {
			throw 'AJAX HTML: Se requiere que el 3er parámetro sea un nodo html válido';
		}
	}

	
	ajax_respuesta.prototype.get = function(parametro) {
		
	}
	
	ajax_respuesta.prototype.recibir_respuesta = function(response) {
		try {
			switch (this._modo) {
				case 'D': 
					//-- Comunicación de datos
					var parametro = eval('(' + response.responseText + ')');
					this._funcion.call(this._clase, parametro, this._contexto)
					break;
				case 'H':
					//-- Comunicación de HTML
					this._nodo_html.innerHTML = response.responseText;
					break;
				case 'P':					
					//-- Comunicación de Bajo nivel
					var texto = response.responseText;
					var pos, pos_anterior = 0;
					while (pos != -1) {
						pos = texto.indexOf('<--toba-->', pos_anterior);
						if (pos != -1) {
							this._partes.push(texto.substr(pos_anterior, pos-pos_anterior));
							pos_anterior = pos + 10;
						}
					}
					var restante = texto.substr(pos_anterior);
					if (restante.length >0) {
						this._partes.push(restante);
					}					
					this._funcion.call(this._clase, this, this._contexto)					
					break;
			}

		} catch (e) {
			var error = 'Error en la respuesta.<br>' + "Mensaje Server:<br>" + response.responseText + "<br><br>Error JS:<br>" + e;
			notificacion.limpiar();
			notificacion.agregar(error);
			notificacion.mostrar();
		}
	}
	
