
var editor =
{
	_parametros : [],
	
	set_parametros_previsualizacion : function(parametros) {
		this._parametros = parametros;
	},
	
	//Abrir un proyecto desde el ADMIN
	ejecutar_item : function(item) {
		var parametros_nulos = (typeof this._parametros.punto_acceso == 'undefined' ) ||
								 (typeof this._parametros.proyecto == 'undefined' );
		var parametros_vacios = (this._parametros.punto_acceso === '' ) || 
								(this._parametros.proyecto === '' );
		if ( parametros_nulos || parametros_vacios )  {
			alert('Por favor especifique los parámetros de previsualización');
			this.abrir_editor('frame_centro', vinculador.get_url('toba_editor', '3287'));
			
		} else {
			url = this._parametros.punto_acceso + '?' + window.toba_hilo_qs + '=' + 
					this._parametros.proyecto;
			if (isset(item)) {
				url += 	window.toba_hilo_separador + item;
			}
			// Celda de memoria
			url = url + '&' + apex_hilo_qs_celda_memoria + '=previsualizacion';
			url = url + '&' + toba_hilo_qs_menu  + "=1";
			var opciones = {'resizable':1, 'scrollbars' : '1', 'menubar': '1', 'toolbar': '1', 'location': '1'};
			abrir_popup('previsualizacion',url, opciones, null, false);
		}
	},

	previsualizar : function() {
		this.ejecutar_item(null);
	}, 
	
	//Abrir un editor desde el proyecto
	abrir_editor : function(frame, url) {
		top.frames[frame].document.location.href=url;
	}
};