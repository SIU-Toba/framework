ei_firma.prototype = new ei();
ei_firma.prototype.constructor = ei_firma;

/**
 * @class Editor de código
 * @constructor
 * @phpdoc Componentes/Eis/toba_ei_firma toba_ei_firma
 */
function ei_firma(id, input_submit) {
	this._id = id;
	this._input_submit = input_submit;

}

	//-------------------------------------------------------------------------
	//-------------------------------METODOS-----------------------------------
	//-------------------------------------------------------------------------


	//---Submit
	ei_firma.prototype.submit = function() {
		if (this.controlador && !this.controlador.en_submit()) {
			return this.controlador.submit();
		}
	};

	ei_firma.prototype.set_datos = function(id_evento) {
		document.getElementById(this._input_submit).value = id_evento;
	}

	ei_firma.prototype.puede_submit = function() {
		return true;
	}

toba.confirmar_inclusion('componentes/ei_firma');
