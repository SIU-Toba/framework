objeto_ci.prototype = new objeto;
var def = objeto_ci.prototype;
def.constructor = objeto_ci;
//--------------------------------------------------------------------------------
//Clase objeto_ci 
function objeto_ci(instancia, form, input_submit) {
	this._instancia = instancia;						//Nombre de la instancia del objeto, permite asociar al objeto con el arbol DOM
	this._form = form									//Nombre del form contenedor del objeto
	this._input_submit = input_submit;					//Campo que se setea en el submit del form 
	this._ci = null;									//CI contenedor
	this._objetos = new Array();						//Listado de objetos js asociados al CI
	this._en_submit = false;							//¿Esta en proceso de submit el CI?
	this._silencioso = false;							//¿Silenciar confirmaciones y alertas? Util para testing
	this._evento_defecto = new evento_ei('', true, '');	//Por defecto se valida los objetos contenidos
	this.reset_evento();
}

	def.agregar_objeto = function(objeto) {
		objeto.set_ci(this);
		this._objetos.push(objeto);
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
	//1- Se sube hasta el CI raiz
	//2- El raiz analiza si puede hacerlo (recorriendo los hijos)
	//2-Se envia el submit a los hijos y se hace el procesamiento para PHP (esto es irreversible)
	//Intenta realizar el submit de todos los objetos asociados
	def.submit = function() {
		if (this._ci && !this._ci.en_submit()) //Primero debe consultar si su padre está en proceso
			return this._ci.submit();

		this._en_submit = true;				
		if (! this._ci) { //Si es el padre de todos, borrar las notificaciones
			cola_mensajes.limpiar();
			if (this.puede_submit()) {
				this.submit_recursivo();
				document[this._form].submit();
			} else {
				cola_mensajes.mostrar(this);		
			}
		} else {
			this.submit_recursivo();
		}
		this._en_submit = false;
	}
	
	def.submit_recursivo = function()
	{
		for (obj in this._objetos) {
			this._objetos[obj].submit();
		}
		if (this._evento.id != '') {
			document.getElementById(this._input_submit).value = this._evento.id;
		}
	}
	
	def.en_submit = function() {
		return this._en_submit;		
	}
	
	//Chequea si es posible realiza el submit de todos los objetos asociados
	def.puede_submit = function() {
		if (this._evento) {
			//- 1 - Hay que realizar las validaciones y preguntarle a los hijos si pueden hacer submit
			//		La validación no es recursiva para evitar doble chequeos en los hijos
			var ok = this.validar(false);
			ok = ok && this.objetos_pueden_submit();
			if(!ok) {
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
			this.resetear_errores();
			return true;
		}
	}
	
	def.resetear_errores = function() {
		for (obj in this._objetos) {
			this._objetos[obj].resetear_errores();
		}
		this.notificar(false);
	}
	
	//---VALIDACION
	//Realiza la validación de este objeto, y opcionalmente de los que están contenidos
	def.validar = function(recursivo) {
		if (typeof recursivo == 'undefined')
			recursivo = true;
		var validacion_particular = 'evt__validar_datos';
		var ok = true;
		if(this._evento && this._evento.validar) {
			if (existe_funcion(this, validacion_particular))
				ok = ok && this[validacion_particular]();	
			if (recursivo) {
				for (obj in this._objetos) {
					ok = ok && this._objetos[obj].validar(recursivo);
				}
			}
		}
		return ok;
	}
	
	//---Notificaciones
	def.notificar = function(mostrar) {
		var barra = document.getElementById('barra_' + this._instancia);
		if (barra) {
			if (mostrar)
				barra.style.display = '';
			else
				barra.style.display = 'none';
		}
	}

	//---Navegación 
	def.habilitar_tab = function(tab, habilitado) {
		var boton = document.getElementById(this._input_submit + '_cambiar_tab_' + tab);
		if (! habilitado) {
			boton.disabled = true;
			boton.onclick_viejo = boton.onclick;
			boton.onclick = '';
		} else {
			boton.disabled = false;
			if (boton.onclick_viejo != '')
				boton.onclick = boton.onclick_viejo;
		}
	}

	def.ir_a_pantalla = function(pantalla) {
		var boton = document.getElementById(this._input_submit + '_cambiar_tab_' + pantalla);
		boton.onclick();
	}
	
	def.ir_a_anterior = function() {
		this.ir_a_pantalla('_anterior');	
	}	
		
	def.ir_a_siguiente = function() {
		this.ir_a_pantalla('_siguiente');
	}
	
