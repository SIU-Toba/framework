ei_codigo.prototype = new ei();
ei_codigo.prototype.constructor = ei_codigo;

/**
 * @class Editor de código
 * @constructor
 * @phpdoc Componentes/Eis/toba_ei_codigo toba_ei_codigo
 */
function ei_codigo(id, dim, input_submit, input_codigo) {
	this._id = id;
	this._input_submit = input_submit;
	this._input_codigo = input_codigo;
	
	var textarea = document.getElementById('code');
	this._mirror = CodeMirror.fromTextArea('code', {
	    width: dim[0],
	    height: dim[1],
		content: textarea.value,
		basefiles: ["codemirror_base.js"],
		stylesheet: [
			toba_alias + "/css/codemirror/xmlcolors.css",
			toba_alias + "/css/codemirror/jscolors.css",
			toba_alias + "/css/codemirror/csscolors.css",
			toba_alias + "/css/codemirror/phpcolors.css"
		],
		path: toba_alias + "/js/codemirror/",
		indentUnit: 4,
		autoMatchParens: true
	});
}

	//---Submit
	ei_codigo.prototype.submit = function() {
		if (this.controlador && !this.controlador.en_submit()) {
			return this.controlador.submit();
		}

		if (this._evento) {
			this.set_datos(this._evento.id);
		} else if (this._evento_implicito) {
			this.set_datos(this._evento_implicito.id);
		}
	};

	ei_codigo.prototype.set_datos = function(id_evento) {
		document.getElementById(this._input_submit).value = id_evento;
		document.getElementById(this._input_codigo).value = this._mirror.getCode();
	}

toba.confirmar_inclusion('componentes/ei_codigo');
