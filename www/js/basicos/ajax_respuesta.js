
/**
 * @class Clase estatica para el manejo de una respuesta AJAX
 * @constructor
 **/
function ajax_respuesta(modo) {
	this._modo = modo;
};
ajax_respuesta.prototype.constructor = ajax_respuesta;

	ajax_respuesta.prototype.set_callback = function(clase, funcion) {
		this._clase = clase;
		this._funcion = funcion;
	}
	
	ajax_respuesta.prototype.set_nodo_html = function(nodo) {
		if (nodo != null && typeof nodo == 'object' && isset(nodo['innerHTML'])) {
			this._nodo_html = nodo;
		} else {
			throw 'AJAX HTML: Se requiere que el 3er parámetro sea un nodo html válido';
		}
	}

	ajax_respuesta.prototype.recibir = function(response) {
		try {
			switch (this._modo) {
				case 'D': 
					var parametro = eval('(' + response.responseText + ')');
					this._funcion.call(this._clase, parametro)
					break;
				case 'H':
					this._nodo_html.innerHTML = response.responseText;
					break;
			}

		} catch (e) {
			var error = 'Error en la respuesta.<br>' + "Mensaje Server:<br>" + response.responseText + "<br><br>Error JS:<br>" + e;
			notificacion.limpiar();
			notificacion.agregar(error);
			notificacion.mostrar();
		}
	}
	
