//--------------------------------------------------------------------------------
//Clase ei_calendario
ei_calendario.prototype = new ei();
var def = ei_calendario.prototype;
def.constructor = ei_calendario;

function ei_calendario(instancia, input_submit) {
	this._instancia = instancia;				//Nombre de la instancia del objeto, permite asociar al objeto con el arbol DOM
	this._input_submit = input_submit;			//Campo que se setea en el submit del form
}

	//---Submit 
	def.submit = function() {
		if (this._ci && !this._ci.en_submit()) {
			return this._ci.submit();
		}
		if (this._evento) {
			//Si es la selección de una semana marco la semana
			if (this._evento.id == 'seleccionar_semana') {
				if (this._evento.parametros){
					document.getElementById(this._input_submit + '__seleccionar_semana').value = this._evento.parametros;
				}	
			} else {
				if (this._evento.id == 'seleccionar_dia') {
					if (this._evento.parametros) {
						document.getElementById(this._input_submit + '__seleccionar_dia').value = this._evento.parametros;
					}
				} else {
					document.getElementById(this._input_submit + '__cambiar_mes').value = document.getElementById('monthID').value + '||' + document.getElementById('yearID').value;
				} 		
			}
			//Marco la ejecucion del evento para que la clase PHP lo reconozca
			document.getElementById(this._input_submit).value = this._evento.id;
		}
		return true;
	};

toba.confirmar_inclusion('componentes/ei_calendario');	
