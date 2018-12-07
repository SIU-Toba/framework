window.status = '';

var notificacion;

/**
 * @class Clase estatica para el manejo de mensajerï¿½a al usuario
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
	 * Este metodo no muestra el mensaje de notificaciï¿½n resultante, para ello usar en combinaciï¿½n con notifiacion.mostrar()
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
	* Permite cambiar el titulo de la ventana de notificaciï¿½n de mensajes
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
notificacion.ventana_modal = function() {
	var contenedor = $('#modal_notificacion');
	
	if (!contenedor) {
		//--- Si el mensaje se produce antes del body, usar el alert
		this.ventana_alert();
		return;	
	}
	var mensaje = '<div class="modal-body">';
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
			texto = "<strong>" + this._mensajes[i][2] + "</strong> " + texto;
		}
		if (typeof this._mensajes[i][3] != 'undefined') {
			var botonera = "<a onclick='overlay_debug("+i+")' href='#'>Más info...</a>";
			var titulo_debug = "<hr>";
			var texto_debug = this._mensajes[i][3];
			texto += botonera + "<div id='overlay_debug_"+ i +"' style='display:none;'>" + titulo_debug + texto_debug + "</div>";
		}
		mensaje += '<div>' + gravedad + texto + '</div>';
	}
	mensaje += "</div>"
	mensaje += "<div class='modal-footer'><button type='button' class='btn btn-default' data-dismiss='modal'>Aceptar</button></div>";
	this.mostrar_ventana_modal(this._titulo, mensaje);
};

notificacion.mostrar_ventana_modal = function(titulo, mensaje, ancho, accion_cerrar) {
	var contenedor = $('#modal_notificacion .modal-content');	
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
		contenedor.css('width', ancho);	
	}

	var inner = '<div class="modal-header"> <h4 class="modal-title">' + titulo + '</h4></div>' + mensaje 
	contenedor.html( inner );
	overlay();
};
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
	var encabezado = (hay_error) ? 'Se han encontrado los siguientes problemas:\n\n' : 'AtenciÃ³n:\n\n';
	alert(encabezado + mensaje);
};

notificacion.tipo_ventana = notificacion.ventana_modal;


function overlay(limpiar) {
	var modal = $("#modal_notificacion");
	$("#modal_notificacion").modal('show')
	if (isset(limpiar) && limpiar) {
		$('overlay_contenido').innerHTML = '';
	}
}
function overlay_debug(i) {
	el = document.getElementById("overlay_debug_"+i);
	var oculto = (el.style.display == "none");
	el.style.display = (oculto) ? "block" : "none";
}

toba.confirmar_inclusion('basicos/notificacion');
