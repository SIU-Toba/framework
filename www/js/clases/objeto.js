//--------------------------------------------------------------------------------
//Clase evento_ci
function evento_ei(id, validar, confirmar, parametros) {
	this.id = id;
	this.validar = validar;
	this.confirmar = confirmar;
	this.parametros = parametros;
}

//--------------------------------------------------------------------------------
//Clase objeto
function objeto(instancia) {
	this._instancia = instancia;
}
def = objeto.prototype;
def.constructor = objeto;

	def.iniciar = function() {
	}

	def.set_ci = function(ci) {
		this._ci = ci;
	}
	
	//----------------------------------------------------------------
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
		if (this._ci && !this._ci.en_submit()) //Primero debe consultar si su padre está en proceso
			return this._ci.submit();
	}
	
	def.puede_submit = function() {
		return true;
	}

	def.resetear_errores = function() {
	}	

	//----------------------------------------------------------------
	//---Servicios graficos
	def.cuerpo = function() {
		return document.getElementById('cuerpo_' + this._instancia);	
	}
	
	def.cambiar_colapsado = function() {
		if (this.cuerpo().style.display == 'none')
			this.descolapsar();
		else
			this.colapsar();
	}
	
	def.colapsar = function() {
		boton = document.getElementById('colapsar_boton_' + this._instancia);
		if (boton) {
			boton.src = toba.imagen('maximizar');
		}
		this.cuerpo().style.display='none';
	}
	
	def.descolapsar = function() {
		boton = document.getElementById('colapsar_boton_' + this._instancia);
		if (boton) {
			boton.src = toba.imagen('minimizar');
		}
		this.cuerpo().style.display= 'block';
	}