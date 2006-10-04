

ei_calendario.prototype = new ei();
ei_calendario.prototype.constructor = ei_calendario;

/**
 * @class Calendario para visualizar contenidos diarios y seleccionar días o semanas.
 * @constructor
 * @phpdoc Componentes/Eis/toba_ei_calendario toba_ei_calendario 
 */
function ei_calendario(instancia, input_submit) {
	this._instancia = instancia;				//Nombre de la instancia del objeto, permite asociar al objeto con el arbol DOM
	this._input_submit = input_submit;			//Campo que se setea en el submit del form
}

	//---Submit 
	ei_calendario.prototype.submit = function() {
		if (this.controlador && !this.controlador.en_submit()) {
			return this.controlador.submit();
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
