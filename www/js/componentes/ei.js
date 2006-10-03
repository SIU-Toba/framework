/**
 * @class Representa un evento que será consumido por un CI
 * @param id Identificador del evento, ej: 'modificar'
 * @param validar ¿Se debe validar antes de hacer submit?
 * @param confirmar ¿Se debe confirmar antes de hacer submit?
 * @constructor 
 */
function evento_ei(id, validar, confirmar, parametros) {
	this.id = id;
	this.validar = (typeof validar == 'undefined') ? true : validar;
	this.confirmar = (typeof confirmar == 'undefined') ? false : confirmar;
	this.parametros = parametros;
	this._silencioso = false;
}

/**
 * @class Clase base de los componentes toba en javascript
 * @constructor
 */
function ei(instancia, input_submit) {
	this._instancia = instancia;
	this._input_submit = input_submit;
}
ei.prototype.constructor = ei;

	/**
	 * @private
	 */
	ei.prototype.iniciar = function() {
	};

	/**
	 * @private
	 */
	ei.prototype.set_controlador = function(ci) {
		this.controlador = ci;
	};
	
	//----------------------------------------------------------------
	//---Eventos	 
	
	/**
	 * Informa al componente la presencia de un nuevo evento
	 * @param {evento_ei} evento
	 * @param {boolean} hacer_submit Luego de informar el evento, se inicia el proceso de submit (por defecto true)
	 */
	ei.prototype.set_evento = function(evento, hacer_submit) {
		if (typeof hacer_submit == 'undefined') {
			hacer_submit = true;
		}
		this._evento = evento;
		if (hacer_submit) {
			this.submit();
		}
	};

	/**
	 * Determina cual es el evento que se utiliza cuando no se dispara ninguno explicitamente por el usuario
	 * @param {evento_ei} evento
	 */
	ei.prototype.set_evento_implicito = function(evento) {
		this._evento_implicito = evento;
	};
	
	/**
	 * Limpia el evento actualmente informado al componente
	 */
	ei.prototype.reset_evento = function() {
		this._evento = this._evento_implicito;
	};
		
	//---Submit
	
	/**
	 * Inicia el proceso de submit, este proceso recorre todos los componentes
	 * validandolos y preparandolos para una comunicación con el servidor
	 */
	ei.prototype.submit = function() {
		var padre_esta_listo = this.controlador && !this.controlador.en_submit();
		if (padre_esta_listo) {
			return this.controlador.submit();
		}
	};
	
	/**
	 * Determina si el componente puede hacer submit en base 
	 * al callback redefinible <em>evt__evento</em> donde evento es el id del evento disparado
	 * @type boolean
	 */
	ei.prototype.puede_submit = function() {
		if(this._evento && existe_funcion(this, "evt__" + this._evento.id)){
			if(! ( this["evt__" + this._evento.id]() ) ){
				this.reset_evento();
				return false;
			}
		}
		return true;
	};

	/**
	 * Limpia el componente de errores producidos anteriormente
	 */
	ei.prototype.resetear_errores = function() {
	};
	
	/**
	 * Ejecuta un vinculo producido por un evento
	 * Antes de ejecutar el vinculo se llama una callback <em>modificar_vinculo__evento</em> para
	 * que se pueda modificar alguna propiedad del vinculo
	 * @see vinculador
	 */
	ei.prototype.invocar_vinculo = function(id_evento, id_vinculo) {
		// Busco la extension de modificacin de vinculos
		var funciv = 'modificar_vinculo__' + id_evento;
		if (existe_funcion(this, funciv)) {
			this[funciv](id_vinculo);
		}
		vinculador.invocar(id_vinculo);
	};

	//----------------------------------------------------------------  
	//---Servicios graficos 
	
	/**
	 * Referencia al tag HTML que contiene el html de todo el componente
	 */
	ei.prototype.cuerpo = function() {
		return document.getElementById('cuerpo_' + this._instancia);	
	};
	
	/**
	 * Referencia al tag HTML padre del componente
	 * @see #cuerpo
	 */
	ei.prototype.raiz = function() {
		return this.cuerpo().parentNode;
	};
	
	ei.prototype.cambiar_colapsado = function() {
		cambiar_colapsado(this.obtener_boton_colapsar(), this.cuerpo());		
	};
	
	ei.prototype.colapsar = function() {
		colapsar(this.obtener_boton_colapsar(), this.cuerpo());
	};
	
	ei.prototype.descolapsar = function() {
		descolapsar(this.obtener_boton_colapsar(), this.cuerpo());
	};
	
	ei.prototype.obtener_boton_colapsar = function() {
		return document.getElementById('colapsar_boton_' + this._instancia);
	};

	ei.prototype.desactivar_boton = function(id) {
		this.get_boton(id).disabled = true;
	};

	ei.prototype.activar_boton = function(id) {
		this.get_boton(id).disabled = false;
	};

	ei.prototype.ocultar_boton = function(id) {
		this.get_boton(id).style.display = 'none';
	};

	ei.prototype.mostrar_boton = function(id) {
		this.get_boton(id).style.display = '';
	};
	
	ei.prototype.get_boton = function(id) {
		return document.getElementById(this._input_submit + '_' + id);
	};

toba.confirmar_inclusion('componentes/ei');