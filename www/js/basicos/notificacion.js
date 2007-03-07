window.status = '';

var notificacion;

/**
 * @class Clase estatica para el manejo de mensajería al usuario
 * Su funcionamiento se basa en encolar mensajes con notificacion.agregar() y luego mostrarlos con notificacion.mostrar()
 * Los proyectos pueden variar la forma en que se muestran las notificaciones definiendo un nuevo metodo notificacion.tipo_ventana.<br>
 * Por defecto <em>notificacion.tipo_ventana = notificacion.ventana_modal</em>
 * @constructor
 * @phpdoc SalidaGrafica/toba_notificacion toba::notificacion()
 **/
notificacion = new function() {
	this._mensajes = [];
	this._responsable = null;
};
	
	/**
	 * Agrega un mensaje a la cola de notificaciones
	 * Este metodo no muestra el mensaje de notificación resultante, para ello usar en combinación con notifiacion.mostrar()
	 * @param {string} mensaje Mensaje a mostrar
	 * @param {string} gravedad Puede ser 'error' o 'info', esto va a influir en la forma grafica de la notificacion
	 */
	notificacion.agregar = function(mensaje, gravedad, sujeto) {
		if (!gravedad) {gravedad = 'error';}
		this._mensajes.push([mensaje, gravedad, sujeto]);
	};

	/**
	 * Muestra una ventana con los mensajes encolados hasta el momento
	 * @param {ei} Componente responsable de/los mensajes (opcional)
	 */
	notificacion.mostrar = function(responsable) {
		if (this._mensajes.length > 0) {
			if (responsable) {
				this._responsable = responsable;
				responsable.notificar(true);
			}
			this.tipo_ventana();
		}
	};
	
	/**
	 *	Limpia la cola de mensajes agregados hasta el momento
	 */
	notificacion.limpiar = function() {
		this._mensajes = [];
		if (this._responsable) {
			this._responsable.notificar(false);
		}
	};
	
	/**
	 *	Muestra los mensajes usando una ventana HTML modal
	 */
	notificacion.ventana_modal = function() {
		var contenedor = document.getElementById('overlay_contenido');
		if (!contenedor) {
			//--- Si el mensaje se produce antes del body, usar el alert
			this.ventana_alert();
			return;	
		}
		var mensaje = '';
		var titulo = '';		
		for (var i=0; i < this._mensajes.length; i++) {
			var gravedad = '';
			if (this._mensajes[i][1] == 'error') {
				titulo = 'Se han encontrado los siguientes problemas:';
				gravedad = '<img src="'+ toba.imagen('error') + '"/> ';
			} else {
				gravedad = '<img src="'+ toba.imagen('info') + '"/> ';
			}
			var texto = this._mensajes[i][0];
			if (typeof this._mensajes[i][2] != 'undefined') {
				texto = "<strong>" + this._mensajes[i][2] + "</strong> " + texto;
			}
			mensaje += '<div>' + gravedad + texto + '</div>';
		}
		mensaje += "<div class='overlay-botonera'><input id='boton_overlay' type='button' value='Aceptar' onclick='overlay()'/></div>";
		this.mostrar_ventana_modal(titulo, mensaje);
	};
	
	notificacion.mostrar_ventana_modal = function(titulo, mensaje, ancho, accion_cerrar) {
		var contenedor = document.getElementById('overlay_contenido');	
		if (isset(ancho)) {
			contenedor.style.width = ancho;	
		}
		if (! isset(accion_cerrar)) {
			accion_cerrar = 'overlay()';	
		}
		var img = '<img class="overlay-cerrar" title="Cerrar ventana" src="' + toba.imagen('cerrar') 
					+ '" onclick="'	+ accion_cerrar + '" />';
		contenedor.innerHTML = '<div class="overlay-titulo">' + img + titulo+'</div>' + mensaje;
		overlay();
	}
	
	/**
	 *	Muestra los mensajes usando un alert javascript
	 */	
	notificacion.ventana_alert = function() {
		var mensaje = '';
		var hay_error = false;
		var hay_info = false;
		for (var i=0; i < this._mensajes.length; i++) {
			var gravedad;
			if (this._mensajes[i][1] == 'error') {
				gravedad = '- ';
				hay_error = true;
			}
			else {
				gravedad = '- ';
				hay_info = true;
			}
			var texto = this._mensajes[i][0];
			if (typeof this._mensajes[i][2] != 'undefined') {
				texto = this._mensajes[i][2] + " " + texto;
			}			
			mensaje += gravedad + texto + '\n';
		}
		var encabezado = (hay_error) ? 'Se han encontrado los siguientes problemas:\n\n' : 'Atención:\n\n';
		alert(encabezado + mensaje);
	};
	
	notificacion.tipo_ventana = notificacion.ventana_modal;


function overlay(limpiar) {
	el = document.getElementById("overlay");
	var visible = (el.style.visibility == "visible");
	if (ie) {
		//--- Oculta los SELECT por bug del IE
		var selects = document.getElementsByTagName('select');
		for (var i=0; i < selects.length; i++) {
			selects[i].style.visibility = (visible) ? 'visible' : 'hidden';	
		}
	}
	el.style.visibility = (visible) ? "hidden" : "visible";
	if (! visible) {
		var boton = document.getElementById('boton_overlay');
		if (boton) {
			boton.focus();
			window.firstFocus = function() {};
		}
	} 
	if (isset(limpiar) && limpiar) {
		$('overlay_contenido').innerHTML = '';
	}
}

toba.confirmar_inclusion('basicos/notificacion');