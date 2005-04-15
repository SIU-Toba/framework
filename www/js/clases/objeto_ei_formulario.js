//--------------------------------------------------------------------------------
//Clase objeto_ei_formulario 
function objeto_ei_formulario(instancia, ci, rango_tabs, input_submit, evento_defecto) {
	this._instancia = instancia;				//Nombre de la instancia del objeto, permite asociar al objeto con el arbol DOM
	this._ci = ci;								//Referencia al CI contenedor
	this._rango_tabs = rango_tabs;
	this._input_submit = input_submit;			//Campo que se setea en el submit del form
	this._evento_defecto = evento_defecto;		//Evento por defecto del submit
	
	this._efs = new Array();					//Lista de objeto_ef contenidos
	this._efs_procesar = new Array();			//ID de los ef's que poseen procesamiento
	this._evento = this._evento_defecto;
	this._silencioso = false;					//¿Mostrar confirmaciones y alertas?
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
			
		var evento_actual = this._evento;
		this._evento = this._evento_defecto;
		switch (evento_actual) {
			case 'E':
				return this.evento_eliminar(); break;
			case 'A':			
				return this.evento_agregar(); break;
			case 'M':
				return this.evento_modificar(); break;
			case 'L':
				return this.evento_limpiar(); break;
			default:
				return this.evento_ninguno();
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

	
	//---Eventos	
	def.set_evento = function(evento, huella) {
		this._evento = evento;
		document.getElementById(this._input_submit).value = huella;		//Deja la huella del evento
	}
	
	def.evento_agregar = function() {
		return this.evento_modificar();	
	}

	def.evento_modificar = function() {
		if (this.validar()) {
			this.submit_efs();
			return true;
		} else {
			return false
		}
	}
	
	def.evento_eliminar = function() {
		if (this._silencioso || confirm('¿Desea ELIMINAR el registro?'))
			return true;
		else
			return false;
	}

	def.evento_limpiar = function() {
		return this.evento_ninguno();
	}
	
	def.evento_ninguno = function() {
		document.getElementById(this._input_submit).value = '';	//Borra la huella del evento anterior 	
		return true;
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