/**
 * Representa un evento que será consumido por un CI
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


function ei(instancia) {
	this._instancia = instancia;
}
def = ei.prototype;
def.constructor = ei;

	def.iniciar = function() {
	};

	def.set_ci = function(ci) {
		this._ci = ci;
	};
	
	//----------------------------------------------------------------
	//---Eventos	 
	def.set_evento = function(evento, hacer_submit) {
		if (typeof hacer_submit == 'undefined') {
			hacer_submit = true;
		}
		this._evento = evento;
		if (hacer_submit) {
			this.submit();
		}
	};

	def.set_evento_implicito = function(evento) {
		this._evento_implicito = evento;
	};
	
	def.reset_evento = function() {
		this._evento = this._evento_implicito;
	};
		
	//---Submit
	def.submit = function() {
		var padre_esta_listo = this._ci && !this._ci.en_submit();
		if (padre_esta_listo) {
			return this._ci.submit();
		}
	};
	
	def.puede_submit = function() {
		if(this._evento && existe_funcion(this, "evt__" + this._evento.id)){
			if(! ( this["evt__" + this._evento.id]() ) ){
				this.reset_evento();
				return false;
			}
		}
		return true;
	};

	def.resetear_errores = function() {
	};
	
	def.invocar_vinculo = function(id_evento, id_vinculo) {
		// Busco la extension de modificacin de vinculos
		var funciv = 'modificar_vinculo__' + id_evento;
		if (existe_funcion(this, funciv)) {
			this[funciv](id_vinculo);
		}
		vinculador.invocar(id_vinculo);
	};

	//----------------------------------------------------------------  
	//---Servicios graficos 
	def.cuerpo = function() {
		return document.getElementById('cuerpo_' + this._instancia);	
	};
	
	def.raiz = function() {
		return this.cuerpo().parentNode;
	};
	
	def.cambiar_colapsado = function() {
		if (this.cuerpo().style.display == 'none') {
			this.descolapsar();
		} else {
			this.colapsar();
		}
	};
	
	def.colapsar = function() {
		var boton = this.obtener_boton_colapsar();
		if (boton) {
			boton.src = toba.imagen('maximizar');
		}
		this.cuerpo().style.display='none';
	};
	
	def.descolapsar = function() {
		boton = this.obtener_boton_colapsar();
		if (boton) {
			boton.src = toba.imagen('minimizar');
		}
		this.cuerpo().style.display= 'block';
	};
	
	def.obtener_boton_colapsar = function() {
		return document.getElementById('colapsar_boton_' + this._instancia);
	};

toba.confirmar_inclusion('componentes/ei');