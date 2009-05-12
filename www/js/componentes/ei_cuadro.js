
ei_cuadro.prototype = new ei();
ei_cuadro.prototype.constructor = ei_cuadro;

/**
 * @class Un ei_cuadro es una grilla de registros.
 * @constructor
 * @phpdoc Componentes/Eis/toba_ei_cuadro toba_ei_cuadro
 */
function ei_cuadro(id, instancia, input_submit, filas) {
	this._id = id;
	this._instancia = instancia;				//Nombre de la instancia del objeto, permite asociar al objeto con el arbol DOM
	this._input_submit = input_submit;			//Campo que se setea en el submit del form
	this._filas = filas;
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
					if (this._evento.parametros_extra) {
						document.getElementById(this._input_submit + '__extra').value = this._evento.parametros_extra;
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
	
	
	//--------------------------------------
	//-----    Seleccion Multiple
	//--------------------------------------

	/**
	 * Informa al componente la presencia de un nuevo evento
	 * @param {evento_ei} evento
	 * @param {boolean} hacer_submit Luego de informar el evento, se inicia el proceso de submit (por defecto true)
	 */
	ei_cuadro.prototype.set_evento = function(evento, hacer_submit, input) {
		if (typeof hacer_submit == 'undefined') {
			hacer_submit = true;
		}	
		ei.prototype.set_evento.call(this, evento, hacer_submit);
		if (!hacer_submit) {
			var fila = input.parentNode.parentNode.parentNode;
			if (input.checked) {
				agregar_clase_css(fila, 'ei-cuadro-fila-sel');
			} else {
				quitar_clase_css(fila, 'ei-cuadro-fila-sel');
			}
		}
	};	
	
	ei_cuadro.prototype.seleccionar = function(fila, id_evento)
	{
		var check = $(this._input_submit + fila + '_' + id_evento);
		check.checked = !check.checked;
		check.onclick();
	};	
		
	

	ei_cuadro.prototype.get_ids_seleccionados = function(id_evento)
	{
		var seleccion = [];
		for (i in this._filas) {
			var check = $(this._input_submit + this._filas[i] + '_' + id_evento);
			if (check.checked) {
				seleccion.push(check.value);
			}
		}
		return seleccion;
	};	
	
	
toba.confirmar_inclusion('componentes/ei_cuadro');