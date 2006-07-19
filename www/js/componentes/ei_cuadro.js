//--------------------------------------------------------------------------------
//Clase ei_cuadro
ei_cuadro.prototype = new ei();
var def = ei_cuadro.prototype;
def.constructor = ei_cuadro;

function ei_cuadro(instancia, input_submit) {
	this._instancia = instancia;				//Nombre de la instancia del objeto, permite asociar al objeto con el arbol DOM
	this._input_submit = input_submit;			//Campo que se setea en el submit del form
}
	
	//---Submit 
	def.submit = function() {
		if (this._ci && !this._ci.en_submit()) {
			return this._ci.submit();
		}
		if (this._evento) {
			switch (this._evento.id) {
				case 'cambiar_pagina':
					document.getElementById(this._input_submit + '__pagina_actual').value = this._evento.parametros;
					break;
				case 'ordenar':
					document.getElementById(this._input_submit + '__orden_columna').value = this._evento.parametros.orden_columna;
					document.getElementById(this._input_submit + '__orden_sentido').value = this._evento.parametros.orden_sentido;
					break;
				default:
					if (this._evento.parametros) {
						document.getElementById(this._input_submit + '__seleccion').value = this._evento.parametros;
					}
					break;				
			}
			//Marco la ejecucion del evento para que la clase PHP lo reconozca
			document.getElementById(this._input_submit).value = this._evento.id;
		}
	};

	//Chequea si es posible realiza el submit de todos los objetos asociados	
	def.puede_submit = function() {
		if(this._evento) { //Si hay un evento seteado...
			//- 1 - Hay que llamar a una ventana de control especifica para este evento?
			if(existe_funcion(this, "evt__" + this._evento.id)){
				if(! ( this["evt__" + this._evento.id](this._evento.parametros) )) {
					this.reset_evento();
					return false;
				}
			}		
			//- 2 - Hay que confirmar la ejecucion del evento?
			//La confirmacion se solicita escribiendo el texto de la misma
			if (trim(this._evento.confirmar) !== "") {
				if (!this._silencioso && !(confirm(this._evento.confirmar))){
					this.reset_evento();
					return false;
				}
			}
		}
		return true;
	};
	
toba.confirmar_inclusion('componentes/ei_cuadro');