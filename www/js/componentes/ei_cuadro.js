
ei_cuadro.prototype = new ei();
ei_cuadro.prototype.constructor = ei_cuadro;

/**
 * @class Un ei_cuadro es una grilla de registros.
 * @constructor
 * @phpdoc Componentes/Eis/toba_ei_cuadro toba_ei_cuadro
 */
function ei_cuadro(id, instancia, input_submit) {
	this._id = id;
	this._instancia = instancia;				//Nombre de la instancia del objeto, permite asociar al objeto con el arbol DOM
	this._input_submit = input_submit;			//Campo que se setea en el submit del form
}
	
	//---Submit 
	ei_cuadro.prototype.submit = function() {
		if (this.controlador && !this.controlador.en_submit()) {
			return this.controlador.submit();
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

	ei_cuadro.prototype.colapsar_corte = function(corte)
	{
		var objeto = document.getElementById(corte);
		toggle_nodo(objeto);
	};
	
toba.confirmar_inclusion('componentes/ei_cuadro');