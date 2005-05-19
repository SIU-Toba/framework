//--------------------------------------------------------------------------------
//Clase objeto_ei_cuadro
objeto_ei_cuadro.prototype = new objeto;
var def = objeto_ei_cuadro.prototype;
def.constructor = objeto_ei_cuadro;

function objeto_ei_cuadro(instancia, input_submit) {
	this._instancia = instancia;				//Nombre de la instancia del objeto, permite asociar al objeto con el arbol DOM
	this._input_submit = input_submit;			//Campo que se setea en el submit del form
}

	//---Submit 
	def.submit = function() {
		if (this._ci && !this._ci.en_submit())
			return this._ci.submit();
		if (this._evento) {
			//Marco la fila seleccionada
			if (this._evento.parametros)
				document.getElementById(this._input_submit + '__seleccion').value = this._evento.parametros;
			//Marco la ejecucion del evento para que la clase PHP lo reconozca
			document.getElementById(this._input_submit).value = this._evento.id;
		}
	}