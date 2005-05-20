/**
*	Clase para manejo de mensajería al usuario
**/
window.status = '';
var cola_mensajes = 
{
	_mensajes: new Array(),
	_responsable: null,
	
	agregar: function(mensaje, gravedad) {
		if (!gravedad) gravedad = 'error';
		this._mensajes.push(new Array(mensaje, gravedad));
	},	

	mostrar : function(responsable) {
		if (this._mensajes.length > 0) {
			if (responsable) {
				this._responsable = responsable;
				responsable.notificar(true);
			}
			this.ventana_alert();
		}
	},
	
	limpiar: function() {
		this._mensajes = new Array();
		if (this._responsable)
			this._responsable.notificar(false);
	},
	
	ventana_modal: function() {
		var mensaje = '<div style="margin-left: 5px">Se han encontrado los siguientes problemas:<br><br>';
		for (var i=0; i < this._mensajes.length; i++) {
			var gravedad;
			if (this._mensajes[i][1] == 'error')
				gravedad = document.getElementById('icono_error').innerHTML;
			else
				gravedad = document.getElementById('icono_info').innerHTML;
			mensaje += gravedad + ' ' + this._mensajes[i][0] + '<br><br>';
		}
		mensaje += '</div>';
		showPopWin(null, mensaje, 400, 100, [['Cerrar','hidePopWin(false)']]);
	},
	
	ventana_alert: function() {
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
			mensaje += gravedad + this._mensajes[i][0] + '\n';
		}
		var encabezado = (hay_error) ? 'Se han encontrado los siguientes problemas:\n\n' : 'Atención:\n\n';
		alert(encabezado + mensaje);
	}
}