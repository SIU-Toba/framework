
var vinculador =
{
	_vinculos : [],
		
	crear_autovinculo : function(servicio, parametros, objetos) {
		return this.crear(toba_hilo_item, servicio, parametros, objetos);
	},
	
	crear: function(destino, servicio, parametros, objetos) {
		var vinc = toba_prefijo_vinculo + "&" + toba_hilo_qs + "=" + destino[0] + toba_hilo_separador + destino[1];
		if (typeof servicio != 'undefined') {
			vinc += '&' + toba_hilo_qs_servicio + "=" + servicio;
		}
		if (typeof parametros != 'undefined') {
			vinc = this.concatenar_parametros_url(vinc, parametros);
		}
		if (typeof objetos != 'undefined') {
			vinc += '&' + toba_hilo_qs_objetos_destino + "=";
			for (var i=0; i<objetos.length; i++) {
				vinc += objetos[i][0] + toba_hilo_separador + objetos[i][1] + ',';
			}
		}
		return vinc;
	},
	
	concatenar_parametros_url : function(vinculo, parametros) {
		for (var i in parametros) {
			vinculo += '&' + i + '=' + encodeURI(parametros[i]);
		}
		return vinculo;
	},
	
	ir_a_proyecto : function(proyecto) {
		window.location.href = window.url_proyectos[proyecto];
	},

	//--------------------------------------------------
	// Manejo de vinculos registrados en PHP
	//--------------------------------------------------
	
	invocar : function(identificador) {
		if (typeof this._vinculos[identificador] == 'undefined') {
		 	cola_mensajes.agregar('Ud. no tiene permisos para ingresar a esta operación');
		 	cola_mensajes.mostrar();
		 	return;
		}
		if (this._vinculos[identificador].activado != 1) { return; }	//Desactivado
		if (typeof this._vinculos[identificador].parametros == 'undefined') {
			url = this._vinculos[identificador].url;
		} else {
			url = this.concatenar_parametros_url( 	this._vinculos[identificador].url,
													this._vinculos[identificador].parametros );
		}
		if (this._vinculos[identificador].popup == '1' ) {
			abrir_popup(identificador,url,this._vinculos[identificador].popup_parametros);
		} else {
			if( this._vinculos[identificador].target !== '' ) {
				idtarget = this._vinculos[identificador].target;
				window.parent.frames[idtarget].document.location.href = url;
			} else {
				document.location.href = url;
			}
		}
	},

	agregar_parametros: function(identificador, parametros) {
		if (typeof this._vinculos[identificador] == 'undefined') {return;}
		if (typeof this._vinculos[identificador].parametros == 'undefined') {
			this._vinculos[identificador].parametros= parametros;
		} else {
			for (var i in parametros) {
				this._vinculos[identificador].parametros[i] = parametros[i];
			}	
		}
	},

	desactivar_vinculo : function(identificador) {
		if (typeof this._vinculos[identificador] == 'undefined' ) {return;}
		this._vinculos[identificador].activado = 0;
	},

	activar_vinculo : function(identificador) {
		if (typeof this._vinculos[identificador] == 'undefined' ) {return;}
		this._vinculos[identificador].activado = 1;
	},
	
	// A travez de este metodo el vinculador de PHP habla con el de JS.
	agregar_vinculo : function(identificador, datos) {
		this._vinculos[ identificador ] = datos;
	}
};
toba.confirmar_inclusion('basicos/vinculador');