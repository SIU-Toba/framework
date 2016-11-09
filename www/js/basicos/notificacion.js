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
	this._titulo = 'Información';
};
	
	/**
	 * Agrega un mensaje a la cola de notificaciones
	 * Este metodo no muestra el mensaje de notificación resultante, para ello usar en combinación con notifiacion.mostrar()
	 * @param {string} mensaje Mensaje a mostrar
	 * @param {string} gravedad Puede ser 'error' o 'info', esto va a influir en la forma grafica de la notificacion
	 */
	notificacion.agregar = function(mensaje, gravedad, sujeto, mensaje_debug) {
		if (!gravedad) {gravedad = 'error';}
		this._mensajes.push([mensaje, gravedad, sujeto, mensaje_debug]);

		switch (gravedad)
		{
			case 'error': 	this._titulo = 'Se han encontrado los siguientes problemas:';
							break;
			case 'warning': this._titulo = 'Aviso:';
							break;
			default:
							this._titulo = 'Información';				
		}
	};

	/**
	* Permite cambiar el titulo de la ventana de notificación de mensajes
	* @param string Titulo de la ventana
	*/	
	notificacion.set_titulo_ventana = function (titulo) {
		this._titulo = titulo;	
	}

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
		var mensaje = '<div class="overlay-mensaje">';
		for (var i=0; i < this._mensajes.length; i++) {
			var gravedad = '';
			if (this._mensajes[i][1] == 'error') {
				gravedad = '<img src="'+ toba.imagen('error') + '"/> ';
			} else if (this._mensajes[i][1] == 'warning') {
				gravedad = '<img src="'+ toba.imagen('warning') + '"/> ';
			} else {
				gravedad = '<img src="'+ toba.imagen('info') + '"/> ';
			}
			var texto = this._mensajes[i][0];
			if (typeof this._mensajes[i][2] != 'undefined' && isset(this._mensajes[i][2])) {
				texto = "<strong>" + this._mensajes[i][2].decodeEntities() + "</strong> " + texto;
			}
			if (typeof this._mensajes[i][3] != 'undefined') {
				var botonera = "<a onclick='overlay_debug("+i+")' href='#'>Más info...</a>";
				var titulo_debug = "<hr>";
				var texto_debug = this._mensajes[i][3];
				texto += botonera + "<div id='overlay_debug_"+ i +"' style='display:none;'>" + titulo_debug + texto_debug + "</div>";
			}
			mensaje += '<div>' + gravedad + texto + '</div>';
		}
		mensaje += "</div><div class='overlay-botonera'><input id='boton_overlay' class='ei-boton' type='button' value='Aceptar' onclick='overlay()'/></div>";
		this.mostrar_ventana_modal(this._titulo, mensaje);
	};
	
	notificacion.mostrar_ventana_modal = function(titulo, mensaje, ancho, accion_cerrar) {
		var contenedor = document.getElementById('overlay_contenido');	
		if (! isset(accion_cerrar)) {
			accion_cerrar = 'overlay()';	
		}		
		contenedor.onkeypress = function (e) {
			if (!e) { e = window.event; }
			var keycode = isset(e.wich) ? e.which : e.keyCode;
			if (keycode == 27) {
				eval(accion_cerrar);
			}
		};
		if (isset(ancho)) {
			contenedor.style.width = ancho;	
		}

		var img = '<img class="overlay-cerrar" title="Cerrar ventana" src="' + toba.imagen('cerrar') + '" onclick="'	+ accion_cerrar + '" />';
		contenedor.innerHTML = '<div class="overlay-titulo">' + img + titulo + '</div>' + mensaje;
		overlay();
	};
	
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
	if (ie6omenor) {
		//--- Oculta los SELECT por bug del IE
		var selects = document.getElementsByTagName('select');
		for (var i=0; i < selects.length; i++) {
			selects[i].style.visibility = (visible) ? 'visible' : 'hidden';	
		}
	}
	el.style.visibility = (visible) ? "hidden" : "visible";
	if (! visible) {
		scroll(0,0);
		window.firstFocus = function() {};
		var boton = $$('boton_overlay');
		if (boton) {
			boton.focus();
		}
		
	} 
	if (isset(limpiar) && limpiar) {
		$$('overlay_contenido').innerHTML = '';
	}
}

function overlay_debug(i) {
	el = document.getElementById("overlay_debug_"+i);
	var oculto = (el.style.display == "none");
	el.style.display = (oculto) ? "block" : "none";
}

toba.confirmar_inclusion('basicos/notificacion');
