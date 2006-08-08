//--------------------------------------------------------------------------------
//Clase ei_formulario 
ei_formulario.prototype = new ei();
var def = ei_formulario.prototype;
def.constructor = ei_formulario;

function ei_formulario(id, instancia, rango_tabs, input_submit, maestros, esclavos, invalidos) {
	this._id = id;
	this._instancia = instancia;				//Nombre de la instancia del objeto, permite asociar al objeto con el arbol DOM
	this._rango_tabs = rango_tabs;
	this._input_submit = input_submit;			//Campo que se setea en el submit del form

	this._ci = null;							//Referencia al CI contenedor	
	this._efs = {};								//Lista de objeto_ef contenidos
	this._efs_procesar = {};					//ID de los ef's que poseen procesamiento
	this._silencioso = false;					//¿Silenciar confirmaciones y alertas? Util para testing
	this._evento_implicito = null;				//No hay evento prefijado
	this._expandido = false;					//El formulario comienza sin expandirse
	this._maestros = maestros;
	this._esclavos = esclavos;
	this._invalidos = invalidos;
}

	def.agregar_ef  = function (ef, identificador) {
		if (ef) {
			this._efs[identificador] = ef;
		}
	};
	
	def.iniciar = function () {
		for (id_ef in this._efs) {
			this._efs[id_ef].iniciar(id_ef, this);
			this._efs[id_ef].set_tab_index(this._rango_tabs[0]);
			this._efs[id_ef].cuando_cambia_valor(this._instancia + '.validar_ef("' + id_ef + '", true)');
			this._rango_tabs[0] = this._rango_tabs[0] + 5;
			if (this._invalidos[id_ef]) {
				this._efs[id_ef].resaltar(this._invalidos[id_ef]);
			}
		}
		this.agregar_procesamientos();
		this.refrescar_procesamientos(true);
		this.reset_evento();
		if (this.configurar) {
			this.configurar();
		}
	};

	//---Consultas
	def.ef = function(id) {
		return this._efs[id];
	};

	//Retorna el nodo DOM donde se muestra el componente
	def.nodo = function() {
		return document.getElementById(this._instancia + '_cont');	
	};

	//---Submit 
	def.submit = function() {
		if (this._ci && !this._ci.en_submit()) {
			return this._ci.submit();
		}
		if (this._evento) {
			//Enviar la noticia del submit a los efs
			for (id_ef in this._efs) {
				this._efs[id_ef].submit();
			}
			//Marco la ejecucion del evento para que la clase PHP lo reconozca
			document.getElementById(this._input_submit).value = this._evento.id;
		}
	};

	//Chequea si es posible realiza el submit de todos los objetos asociados	
	def.puede_submit = function() {
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
			if(trim(this._evento.confirmar) !== "") {
				if (!this._silencioso && !(confirm(this._evento.confirmar))){
					this.reset_evento();
					return false;
				}
			}
		}
		return true;
	};

	//---- Cascadas
	def.cascadas_cambio_maestro = function(id_ef)
	{
		if (this._esclavos[id_ef]) {
			//--Se recorren los esclavos del master modificado
			for (var i=0; i < this._esclavos[id_ef].length; i++) {
				this.cascadas_preparar_esclavo(this._esclavos[id_ef][i]);
			}
		}
	};
	
	def.cascadas_maestros_preparados = function(id_esclavo)
	{
		for (var i=0; i< this._maestros[id_esclavo].length; i++) {
			if (! this.ef(this._maestros[id_esclavo][i]).tiene_estado()) {
				return false;
			}
		}
		return true;
	};
	
	def.cascadas_preparar_esclavo = function (id_esclavo)
	{
		//Primero se resetea por si la consulta nunca retorna
		this.cascadas_en_espera(id_esclavo);
	
		//---Todos los maestros tienen estado?
		var con_estado = true;
		var valores = '';
		for (var i=0; i< this._maestros[id_esclavo].length; i++) {
			var id_maestro = this._maestros[id_esclavo][i];
			if (this.ef(id_maestro).tiene_estado()) {
				var valor = this.ef(id_maestro).get_estado();
				valores +=  id_maestro + '-;-' + valor + '-|-';
			} else {
				con_estado = false;
				break;
			}
		}
		//--- Si estan todos los maestros puedo ir al server a preguntar el valor de este
		if (con_estado) {
			this.cascadas_comunicar(id_esclavo, valores);
		}
	};
	
	def.cascadas_en_espera = function(id_ef)
	{
		//Se resetea y desactiva al ef y todos sus esclavos
		this.ef(id_ef).borrar_opciones();		
		this.ef(id_ef).desactivar();
		if (this._esclavos[id_ef]) {
			for (var i=0; i< this._esclavos[id_ef].length; i++) {
				this.cascadas_en_espera(this._esclavos[id_ef][i]);
			}
		}
	};
	
	
	def.cascadas_comunicar = function(id_ef, valores) 
	{
		//Empaqueto toda la informacion que tengo que mandar.
		var parametros = {'cascadas-ef': id_ef, 'cascadas-maestros' : valores};
		var callback = {
			success: this.cascadas_respuesta,
			failure: toba.error_comunicacion,
			argument: id_ef,
			scope: this
		};
		var vinculo = vinculador.crear_autovinculo('cascadas_efs', parametros, [this._id]);
		var con = conexion.asyncRequest('GET', vinculo, callback, null);
	};
	
	def.cascadas_respuesta = function(respuesta)
	{
		if (respuesta.responseText === '') {
			var error = 'Error en la respuesta de la cascada, para más información consulte el log';
			cola_mensajes.limpiar();
			cola_mensajes.agregar(error);
			cola_mensajes.mostrar();			
		} else {
			try {
				var datos = eval('(' + respuesta.responseText + ')');
				this.ef(respuesta.argument).set_opciones(datos);
				this.ef(respuesta.argument).activar();
			} catch (e) {
				var error = 'Error en la respueta.<br>' + "Mensaje Server:<br>" + respuesta.responseText + "<br><br>Error JS:<br>" + e;
				cola_mensajes.limpiar();
				cola_mensajes.agregar(error);
				cola_mensajes.mostrar();				
			}
		}
	};
	
	//----Validación 
	def.validar = function() {
		var ok = true;
		var validacion_particular = 'evt__validar_datos';
		if(this._evento && this._evento.validar) {
			if (existe_funcion(this, validacion_particular)) {
				ok = this[validacion_particular]();		
			}
			for (id_ef in this._efs) {
				ok = this.validar_ef(id_ef) && ok;
			}
		} else {
			this.resetear_errores();
		}
		if (!ok) {
			this.reset_evento();
		}
		return ok;
	};
	
	def.validar_ef = function(id_ef, es_online) {
		var ef = this._efs[id_ef];
		var validacion_particular = 'evt__' + id_ef + '__validar';
		var ok = ef.validar();
		if (existe_funcion(this, validacion_particular)) {
			ok = this[validacion_particular]() && ok;
		}
		if (!ok) {
			if (! this._silencioso) {
				ef.resaltar(ef.get_error());
			}
			if (! es_online) {
				cola_mensajes.agregar(ef.get_error(), 'error', ef._etiqueta);
			}
			ef.resetear_error();
			return false;
		}
		if (! this._silencioso) {
			ef.no_resaltar();
		}
		return true;
	};
	
	def.resetear_errores = function() {
		if (! this._silencioso)	 {
			for (var id_ef in this._efs) {
				if (! this._silencioso) {
					this._efs[id_ef].no_resaltar();
				}
			}	
		}
	};

	//---Procesamiento 
	def.procesar = function (id_ef, es_inicial) {
		if (this.hay_procesamiento_particular_ef(id_ef)) {
			return this['evt__' + id_ef + '__procesar'](es_inicial);	//Procesamiento particular, no hay proceso por defecto
		}
	};
			
	//Hace reflexion sobre la clase en busqueda de extensiones	
	def.agregar_procesamientos = function() {
		for (id_ef in this._efs) {
			if (this.hay_procesamiento_particular_ef(id_ef)) {
				this.agregar_procesamiento(id_ef);
			}
		}
	};

	def.agregar_procesamiento = function (id_ef) {
		if (this._efs[id_ef]) {
			this._efs_procesar[id_ef] = true;
			var callback = this._instancia + '.procesar("' + id_ef + '")';
			this._efs[id_ef].cuando_cambia_valor(callback);
		}
	};	
	
	def.hay_procesamiento_particular_ef = function(id_ef) {
		return existe_funcion(this, 'evt__' + id_ef + '__procesar');
	};	

	//---Cambios graficos
	def.cambiar_expansion = function() {
		this._expandido = ! this._expandido;
		for (var id_ef in this._efs) {
			this._efs[id_ef].cambiar_expansion(this._expandido);
		}
		var img = document.getElementById(this._instancia + '_cambiar_expansion');
		img.src = (this._expandido) ? toba.imagen('contraer') : toba.imagen('expandir');
	};
	
	//---Refresco Grafico
	def.refrescar_todo = function () {
		this.refrescar_procesamientos();
	};		
	
	def.refrescar_procesamientos = function (es_inicial) {
		for (var id_ef in this._efs) {
			if (this._efs_procesar[id_ef]) {
				this.procesar(id_ef, es_inicial);
			}
		}
	};	

toba.confirmar_inclusion('componentes/ei_formulario');