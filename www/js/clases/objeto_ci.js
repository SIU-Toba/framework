//--------------------------------------------------------------------------------
//Clase evento_ci
function evento_ei(id, validar, confirmar) {
	this.id = id;
	this.validar = validar;
	this.confirmar = confirmar;
}

//--------------------------------------------------------------------------------
//Clase objeto_ci 
function objeto_ci(instancia, form, input_submit) {
	this._instancia = instancia;						//Nombre de la instancia del objeto, permite asociar al objeto con el arbol DOM
	this._form = form									//Nombre del form contenedor del objeto
	this._input_submit = input_submit;					//Campo que se setea en el submit del form 
	this._ci_padre = null;									//CI contenedor
	this._objetos = new Array();						//Listado de objetos js asociados al CI
	this._en_submit = false;							//¿Esta en proceso de submit el CI?
	this._silencioso = false;							//¿Silenciar confirmaciones y alertas? Util para testing
	this._evento_defecto = new evento_ei('', true, '');	//Por defecto se valida los objetos contenidos
	this.reset_evento();
}

var def = objeto_ci.prototype;
def.constructor = objeto_ci;

	def.agregar_objeto = function(objeto) {
		objeto.set_ci(this);
		this._objetos.push(objeto);
	}
	
	def.set_input_tab = function(input) {
		this._input_submit_tab = input;
	}
	
	def.set_ci = function(ci) {
		this._ci_padre = ci;
	}

	def.iniciar = function() {
	}
	
	//Retorna el nodo DOM donde se muestra el componente
	def.nodo = function() {
		return document.getElementById(this._instancia + '_cont');	
	}
	
	//---Eventos	
	def.set_evento = function(evento) {
		this._evento = evento;
		this.submit();
	}
	
	def.reset_evento = function() {
		this._evento = this._evento_defecto;
	}
	

	//---SUBMIT
	//El proceso de SUBMIT se divide en partes:
	//1- Analiza si se puede hacer submit
	//	1.1- Se valida el CI y sus hijos
	//	1.2- Los hijos analizan si pueden hacer submit
	//2-Se envia el submit a los hijos y se hace el procesamiento para PHP (esto es irreversible)
	
	//Intenta realizar el submit de todos los objetos asociados
	def.submit = function() {
		if (this._ci_padre && !this._ci_padre.en_submit())
				return this._ci_padre.submit();

		this._en_submit = true;
		if (this.puede_submit()) {
			for (obj in this._objetos) {
				this._objetos[obj].submit();
			}
			if (this._evento.id != '') {
				document.getElementById(this._input_submit).value = this._evento.id;
			}
			if (! this._ci_padre) {  //Sólo el CI raiz es el encargado de hacer submit
				//alert(this._instancia);
				document[this._form].submit();			
			}
			return true;
		}
		this._en_submit = false;
		return false;
	}
	
	def.en_submit = function() {
		return this._en_submit;		
	}
	
	//Chequea si es posible realiza el submit de todos los objetos asociados
	def.puede_submit = function() {
		if (this._evento) {
			//- 1 - Hay que realizar las validaciones y preguntarle a los hijos si pueden hacer submit
			if(! this.validar() || !this.objetos_pueden_submit()) {
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
		} else {
			return true;
		}
	}
	
	def.objetos_pueden_submit = function() {
		if(this._evento && this._evento.validar) {
			ok = true;
			for (obj in this._objetos) {
				ok = this._objetos[obj].puede_submit() && ok;
			}
			return ok;			
		} else {
			return true;
		}
	}
	
	//---VALIDACION
	def.validar = function() {
		if(this._evento && this._evento.validar) {
			ok = true;
			for (obj in this._objetos) {
				ok = this._objetos[obj].validar() && ok;
			}
			return ok;
		} else {
			return true;
		}
	}
	
	//---DEBUG
	def.debug_recuadrar = function() {
	}