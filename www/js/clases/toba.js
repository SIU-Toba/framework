//--------------------------------------------------------------------------------
//Clase singleton Toba
var toba = 
{
	_cons_incl: ['basico', 'clases/toba'],
	_cons_carg: ['basico', 'clases/toba'],
	_objetos: new Array(), 
	_callback_inclusion: null,
	_ajax : false,
	
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
	
	set_ajax : function(callback) {
		this._ajax = callback;
	},
	
	comunicar_eventos : function() {
		if (this._ajax) {
			var callback =
			{
			  success: this.servicio__html_parcial ,
			  failure: this.error_comunicacion,
			  scope: this
			}
			var vinculo = vinculador.autovinculo('html_parcial');
			conexion.setForm('formulario_toba');
			var con = conexion.asyncRequest('POST', vinculo, callback, null);
		} else {
			document['formulario_toba'].submit();
		}
	},
	
	servicio__html_parcial : function(respuesta) {
		//Primero se borra el rastro de los objetos anteriores en el objeto
		for (var d in this._ajax._deps) {
			toba.eliminar_objeto(this._ajax._deps[d]._instancia);
			delete(this._ajax._deps[d]);
		}
		//-- Se agrega el html
		var pos = respuesta.responseText.indexOf('<--toba-->');
		this._ajax.raiz().innerHTML = respuesta.responseText.substr(0, pos);
		
		//-- Se incluyen librerias js y se programa la evaluacion del codigo cuando termine
		var js =respuesta.responseText.substr(pos+10);
		pos = js.indexOf('<--toba-->');
		var incl = js.substr(0, pos);
		toba.set_callback_incl(js.substr(pos+10));
		eval(incl);
	},
	
	error_comunicacion : function(error) {
		alert('Ooops');
	},
	
	inicio_aguardar : function() {
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
	},
	
	fin_aguardar : function() {
		document.getElementById('div_toba_esperar').style.display = 'none';
		document.body.style.cursor = '';
	}
	
}

var vinculador =
{
	autovinculo : function(servicio, parametros) {
		return this.crear(toba_hilo_item, servicio, parametros);
	},
	
	crear: function(destino, servicio, parametros) {
		var vinc = toba_prefijo_vinculo + "&" + toba_hilo_qs + "=" + destino[0] + toba_hilo_separador + destino[1];
		if (typeof servicio != 'undefined') {
			vinc += '&' + toba_hilo_qs_servicio + "=" + servicio;
		}
		return vinc;
	}
	
}
