//--------------------------------------------------------------------------------
//Clase objeto_ei_formulario 
function objeto_ei_formulario(instancia, rango_tabs, input_submit) {
	this._instancia = instancia;				//Nombre de la instancia del objeto, permite asociar al objeto con el arbol DOM
	this._rango_tabs = rango_tabs;
	this._input_submit = input_submit;			//Campo que se setea en el submit del form

	this._ci = null;								//Referencia al CI contenedor	
	this._efs = new Object();					//Lista de objeto_ef contenidos
	this._efs_procesar = new Object();			//ID de los ef's que poseen procesamiento
	this._silencioso = false;					//¿Silenciar confirmaciones y alertas? Util para testing
	this._evento_defecto = null;				//No hay evento prefijado
}

var def = objeto_ei_formulario.prototype;
def.constructor = objeto_ei_formulario;

	def.set_ci = function(ci) {
		this._ci = ci;
	}

	def.agregar_ef  = function (ef, identificador) {
		if (ef)
			this._efs[identificador] = ef;
	}
	
	def.iniciar = function () {
		for (id_ef in this._efs) {
			this._efs[id_ef].iniciar(id_ef);
			this._efs[id_ef].cambiar_tab(this._rango_tabs[0]);
			this._efs[id_ef].cuando_cambia_valor(this._instancia + '.validar_ef("' + id_ef + '", true)');
			this._rango_tabs[0]++;
		}
		this.agregar_procesamientos();
		this.refrescar_procesamientos(true);
		this.reset_evento();
	}

	//Retorna el nodo DOM donde se muestra el componente
	def.nodo = function() {
		return document.getElementById(this._instancia + '_cont');	
	}
	
	//---Consultas
	def.ef = function(id) {
		return this._efs[id];
	}

	//---Eventos	
	def.set_evento = function(evento) {
		this._evento = evento;
		this.submit();
	}

	def.set_evento_defecto = function(evento) {
		this._evento_defecto = evento;
	}	
	
	def.reset_evento = function() {
		this._evento = this._evento_defecto;
	}
	
	//---Submit 
	def.submit = function() {
		if (this._ci && !this._ci.en_submit())
			return this._ci.submit();
		if (this._evento) {
			//Enviar la noticia del submit a los efs
			for (id_ef in this._efs) {
				this._efs[id_ef].submit();
			}
			//Marco la ejecucion del evento para que la clase PHP lo reconozca
			document.getElementById(this._input_submit).value = this._evento.id;
		}
	}

	//Chequea si es posible realiza el submit de todos los objetos asociados	
	def.puede_submit = function() {
		//Si no es parte de un submit general, dispararlo
		if(this._evento) //Si hay un evento seteado...
		{
			//- 1 - Hay que realizar las validaciones
			if(! this.validar() ) {
				this.reset_evento();
				return false;
			}
			//- 2 - Hay que llamar a una ventana de control especifica para este evento?
			if(existe_funcion(this, "evt__" + this._evento.id)){
				if(! ( this["evt__" + this._evento.id]() ) ){
					this.reset_evento();
					return false;
				}
			}
			//- 3 - Hay que confirmar la ejecucion del evento?
			//La confirmacion se solicita escribiendo el texto de la misma
			if(this._evento.confirmar != "") {
				if (!this._silencioso && !(confirm(this._evento.confirmar))){
					this.reset_evento();
					return false;
				}
			}
			return true;
		}else{
			return true;
		}
	}

	//----Validación 
	def.validar = function() {
		var ok = true;
		var validacion_particular = 'evt__validar_datos';		
		if(this._evento && this._evento.validar) {
			if (existe_funcion(this, validacion_particular))
				ok = this[validacion_particular]();		
			for (id_ef in this._efs) {
				ok = this.validar_ef(id_ef) && ok;
			}
		} else {
			this.resetear_errores();
		}
		if (!ok)
			this.reset_evento();
		return ok;
	}
	
	def.validar_ef = function(id_ef, es_online) {
		var ef = this._efs[id_ef];
		var validacion_particular = 'evt__' + id_ef + '__validar';
		var ok = true;
		if (existe_funcion(this, validacion_particular)) {
			ok = this[validacion_particular]();
		}				
		if (! ef.validar()) {
			if (! this._silencioso)
				ef.resaltar(ef.error());
			if (! es_online)
				cola_mensajes.agregar(ef.error());
			ef.resetear_error();
			return false
		}
		if (! this._silencioso)
			ef.no_resaltar();
		return true;
	}
	
	def.resetear_errores = function() {
		if (! this._silencioso)	 {
			for (id_ef in this._efs) {
				if (! this._silencioso)
					this._efs[id_ef].no_resaltar();		
			}	
		}
	}

	//---Procesamiento 
	def.procesar = function (id_ef, es_inicial) {
		if (this.hay_procesamiento_particular_ef(id_ef))
			return this['evt__' + id_ef + '__procesar'](es_inicial);	//Procesamiento particular, no hay proceso por defecto
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
		return existe_funcion(this, 'evt__' + id_ef + '__procesar');
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