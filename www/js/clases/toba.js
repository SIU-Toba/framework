//--------------------------------------------------------------------------------
//Clase singleton Toba
var toba = 
{
	_cons_incl: ['basico', 'clases/toba'],
	_cons_carg: ['basico', 'clases/toba'],
	_objetos: new Array(), 
	_callback_inclusion: null,
	_ajax : false,
	_mostrar_aguardar: true,
	
	agregar_objeto : function(o) {
		this._objetos[o._instancia] = o;
	},
	
	eliminar_objeto : function (id) {
		delete(this._objetos[id]);
	},
	
	objetos : function() {
		var nombres = Array();
		for (o in this._objetos) {
			var clase = getObjectClass(this._objetos[o]);
			nombres.push(this._objetos[o]._instancia + ' [' + clase + ']');
		}
		return nombres.join(', ');
	},
	
	
	imagen : function (nombre) {
		return lista_imagenes[nombre];
	},
	
	crear_vinculo : function(destino) { 	//array(proyecto, item)
		return vinculador.crear(destino);
	},
	
	set_callback_incl : function(callback) {
		this._callback_inclusion = callback;
	},
	
	incluir : function(consumos) {
		var a_incluir = new Array();
		//Se divide en dos para garantizar la notificacion de fin de carga
		for(var i=0; i< consumos.length; i++) {
			//Evita que se cargue dos veces un consumo
			if (! in_array(consumos[i], this._cons_incl) ) {
				this._cons_incl.push(consumos[i])
				a_incluir.push(consumos[i]);
			}
		}
		if (a_incluir.length > 0) {
			for (var i=0; i < a_incluir.length; i++) {
				include_source(toba_alias + '/js/' + a_incluir[i] + '.js');		
			}
		} else {
			//Cuando no carga nada se termina de cargar instantaneamente
			this._disparar_callback_incl();			
		}
	},
	
	confirmar_inclusion : function(consumo) {
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
	},
	
	_disparar_callback_incl : function() {
		eval(this._callback_inclusion);
		delete(this._callback_inclusion);		
	},
	
	set_ajax : function(objeto) {
		this._ajax = objeto;
	},
	
	comunicar_eventos : function() {
		if (this._ajax) {
			var callback =
			{
			  success: this.servicio__html_parcial ,
			  failure: this.error_comunicacion,
			  scope: this
			}
			var vinculo = vinculador.crear_autovinculo('html_parcial');
			conexion.setForm('formulario_toba');
			var con = conexion.asyncRequest('POST', vinculo, callback, null);
		} else {
			document['formulario_toba'].submit();
		}
	},
	
	comunicar_vinculo : function(vinculo, nombre_callback) {
		var callback = {
			success: nombre_callback, 
			failure: toba.error_comunicacion
		}; 
		var con = conexion.asyncRequest("GET", vinculo, callback, null);
	},
	
	servicio__html_parcial : function(respuesta) {
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
	},
	
	analizar_respuesta_servicio : function(respuesta) {
		var texto = respuesta.responseText;
		var partes = new Array();
		var pos_anterior = 0;
		while (pos != -1) {
			var pos = texto.indexOf('<--toba-->', pos_anterior);
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
	},
	
	error_comunicacion : function(error) {
		alert('Error de comunicacion: ' + error);
	},
	
	inicio_aguardar : function() {
		if (this._mostrar_aguardar) {
			var div = document.getElementById('div_toba_esperar');
			if (div.currentStyle) {
				//Arreglo para el IE para que simule el fixed
				if (div.currentStyle['position'] == 'absolute') {
					var y = (document.documentElement && document.documentElement.scrollTop) ?
							 	document.documentElement.scrollTop :
							 	document.body.scrollTop;
					div.style.top = y;
				}
			}
			div.style.display = '';	
			document.body.style.cursor = 'wait';
		}
	},
	
	fin_aguardar : function() {
		if (this._mostrar_aguardar) {		
			document.getElementById('div_toba_esperar').style.display = 'none';
			document.body.style.cursor = '';
		} else {
			this.set_aguardar(true);	
		}
	},
	
	set_aguardar : function(aguardar) {
		this._mostrar_aguardar = aguardar;
	}
	
}

var vinculador =
{
	_vinculos : new Array(),
	
	crear_autovinculo : function(servicio, parametros, objetos) {
		return this.crear(toba_hilo_item, servicio, parametros, objetos);
	},
	
	crear: function(destino, servicio, parametros, objetos) {
		var vinc = toba_prefijo_vinculo + "&" + toba_hilo_qs + "=" + destino[0] + toba_hilo_separador + destino[1];
		if (typeof servicio != 'undefined') {
			vinc += '&' + toba_hilo_qs_servicio + "=" + servicio;
		}
		if (typeof parametros != 'undefined') {
			vinc = this.agregar_parametros(vinc, parametros);
		}
		return vinc;
	},
	
	agregar_parametros : function(vinculo, parametros) {
		for (var i in parametros) {
			vinculo += '&' + i + '=' + encodeURI(parametros[i]);
		}
		return vinculo;
	},
	
	agregar_vinculo : function(identificador, datos) {
		this._vinculos[ identificador ] = datos;
	},
	
	invocar : function(identificador, parametros_extra) {
		//ei_arbol(this._vinculos[identificador]);
		if (typeof hacer_submit == 'undefined') {
			url = this._vinculos[identificador]['url'];
		} else {
			url = this.agregar_parametros( this._vinculos[identificador]['url'], parametros_extra);
		}
		if (this._vinculos[identificador]['popup'] == '1' ) {
			abrir_popup('cambiar_esto',url,this._vinculos[identificador]['popup_parametros']);
		} else {
			document.location.href = url;
		}
	}
}