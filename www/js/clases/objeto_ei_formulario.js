//--------------------------------------------------------------------------------
//Clase objeto_ei_formulario 
function objeto_ei_formulario(instancia, ci, rango_tabs, input_submit) {
	this._instancia = instancia;				//Nombre de la instancia del objeto, permite asociar al objeto con el arbol DOM
	this._ci = ci;								//Referencia al CI contenedor
	this._rango_tabs = rango_tabs;
	this._input_submit = input_submit;			//Campo que se setea en el submit del form
	
	this._efs = new Array();					//Lista de objeto_ef contenidos
	this._efs_procesar = new Array();			//ID de los ef's que poseen procesamiento
	this._silencioso = false;					//¿Mostrar confirmaciones y alertas?

	this.reset_evento();
}

var def = objeto_ei_formulario.prototype;
def.constructor = objeto_ei_formulario;

	def.agregar_ef  = function (ef, identificador) {
		if (ef)
			this._efs[identificador] = ef;
	}
	
	def.iniciar = function () {
		for (id_ef in this._efs) {
			this._efs[id_ef].iniciar(id_ef);
			this._efs[id_ef].cambiar_tab(this._rango_tabs[0]);
			this._rango_tabs[0]++;
		}
		this.agregar_procesamientos();
		this.refrescar_procesamientos(true);
	}

	//---Consultas
	def.ef = function(id) {
		return this._efs[id];
	}
	
	//---Submit 
	def.submit = function() {
		//Si no es parte de un submit general, dispararlo
		if (this._ci && !this._ci.en_submit())
			return this._ci.submit();
		/*
			SEBA (borrar):
			Esto seria una forma estandar para todos los eventos
			La idea es que a este componente solo le importa si hay que validar, 
			confirmar o llamar a una ventana de control y no el "sentido" del mismo,
			que cobra valor mas arriba.
		*/
		if(this._evento_id != "") //Si hay un evento seteado...
		{
			//- 1 - Hay que confirmar la ejecucion del evento?
			//La confirmacion se solicita escribiendo el texto de la misma
			if(this._evento_confirmar != "") {
				//this._silencioso ??
				if (!(confirm(this._evento_confirmar))){
					this.reset_evento();
					return false;
				}
			}
			//- 2 - Hay que llamar a una ventana de control especifica para este evento?
			if(this.existe_funcion("evt__" + this._evento_id)){
				if(! ( this["evt__" + this._evento_id]() ) ){
					this.reset_evento();
					return false;
				}
			}
			//- 3 - Hay que realizar las validaciones?
			if(this._evento_validar) {
				if( this.validar() ){
					this.submit_efs();
				}else{
					this.reset_evento();
					return false;	
				}
			}
			//- 4 - El EVENTO se proceso correctamente!
			//Marco la ejecucion del evento para que la clase PHP lo reconozca
			//alert('SE disparo el evento: ' + this._evento_id );
			document.getElementById(this._input_submit).value = this._evento_id;
			return true;
		}else{
			return true;
		}
	}
	
	def.submit_efs = function() {
		//Enviar la noticia del submit a los efs
		for (id_ef in this._efs) {
			this._efs[id_ef].submit();
		}	
	}
	
	//----Validación 
	def.validar = function() {
		return this.validacion_defecto();
	}
	
	def.validacion_defecto = function() {
		for (id_ef in this._efs) {
			var ef = this._efs[id_ef];
			if (! this._efs[id_ef].validar()) {
				if (! this._silencioso) {
					ef.seleccionar();
					alert(ef.error());
					ef.resetear_error();
				}
				return false;
			}
		}
		return true;
	}
	
	//---Procesamiento 
	def.procesar = function (id_ef, es_inicial) {
		if (this.hay_procesamiento_particular_ef(id_ef))
			return this['procesar_' + id_ef](es_inicial);					  //Procesamiento particular, no hay proceso por defecto
	}
			
	//Hace reflexion sobre la clase en busqueda de extensiones	
	def.agregar_procesamientos = function() {
		for (id_ef in this._efs) {
			if (this.hay_procesamiento_particular_ef(id_ef))
				this.agregar_procesamiento(id_ef);
		}
	}

	def.agregar_procesamiento = function (id_ef) {
		if (this._efs[id_ef]) {
			this._efs_procesar[id_ef] = true;
			var callback = this._instancia + '.procesar("' + id_ef + '")';
			this._efs[id_ef].cuando_cambia_valor(callback);
		}
	}	
	
	def.hay_procesamiento_particular_ef = function(id_ef) {
		for (funcion in this) {
			if (funcion == 'procesar_' + id_ef && typeof(this[funcion])=="function")
				return true;
		}		
		return false;
	}	

	def.existe_funcion = function(f) {
		for (funcion in this) {
			if (funcion == f && typeof(this[funcion])=="function")
				return true;
		}		
		return false;
	}

	//---Eventos	
	def.set_evento = function(evento, validar, confirmar) {
		this._evento_id = evento;
		this._evento_validar = validar;
		this._evento_confirmar = confirmar;
	}
	
	def.reset_evento = function() {
		this._evento_id = "";
		this._evento_validar = false;
		this._evento_confirmar = "";
	}

	//---Refresco Grafico
	def.refrescar_todo = function () {
		this.refrescar_procesamientos();
	}		
	
	def.refrescar_procesamientos = function (es_inicial) {
		for (id_ef in this._efs) {
			if (this._efs_procesar[id_ef]) {
				this.procesar(id_ef, es_inicial);
			}
		}
	}	