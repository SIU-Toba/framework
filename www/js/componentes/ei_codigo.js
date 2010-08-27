ei_codigo.prototype = new ei();
ei_codigo.prototype.constructor = ei_codigo;

/**
 * @class Editor de código
 * @constructor
 * @phpdoc Componentes/Eis/toba_ei_codigo toba_ei_codigo
 */
function ei_codigo(id, dim, input_submit, input_codigo) {
	this._id = id;
	this._class_iframe = this._id + '_frame';
	this._input_submit = input_submit;
	this._input_codigo = input_codigo;
	// se deja como variable global para que el indentador propio pueda
	// accederla... acepto sugerencias
	indentUnit  = 4;
	
	var textarea = document.getElementById('code');
	this._mirror = CodeMirror.fromTextArea('code', {
	    width: dim[0],
	    height: dim[1],
		content: textarea.value,
		iframeClass: this._class_iframe,
		basefiles: ["codemirror_base.js"],
		textWrapping: false,
		parserConfig: {customPHPIndentor: this.indentador },
		stylesheet: [
			toba_alias + "/css/codemirror/xmlcolors.css",
			toba_alias + "/css/codemirror/jscolors.css",
			toba_alias + "/css/codemirror/csscolors.css",
			toba_alias + "/css/codemirror/phpcolors.css"
		],
		path: toba_alias + "/js/codemirror/",
		indentUnit: indentUnit,
		autoMatchParens: true
	});
}

	//-------------------------------------------------------------------------
	//-------------------------------METODOS-----------------------------------
	//-------------------------------------------------------------------------

	ei_codigo.prototype.indentador = function(lexical) {
		return function(firstChars) {
			var firstChar = firstChars && firstChars.charAt(0), type = lexical.type;
			var closing = firstChar == type;
			if (type == "form" && firstChar == "{")
				return lexical.indented;
			else if (type == "stat" || type == "form")
				return lexical.indented + indentUnit;
			else if (lexical.info == "switch" && !closing)
				return lexical.indented + (/^(?:case|default)\b/.test(firstChars) ? indentUnit : 2 * indentUnit);
			else if (lexical.align)
				return lexical.column - (closing ? 1 : 0);
			else if (lexical.prev == undefined)
				return lexical.indented;
			else
				return lexical.indented + (closing ? 0 : indentUnit);
			};
	};

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

	ei_codigo.prototype.puede_submit = function()
	{
		if (this.tiene_errores()) {
			notificacion.agregar("El código ingresado tiene errores, verifique"
								 + " los nodos resaltados e intente de nuevo");
			return false;
		}

		return true;
	}

	//-------------------------------------------------------------------------
	//--------------------------API PARA USO EXTERNO---------------------------
	//-------------------------------------------------------------------------
	ei_codigo.prototype.buscar = function() {
		var texto = prompt("Ingrese el texto a buscar:", "");
		if (!texto) return;
		var first = true;
		do {
			var cursor = this._mirror.getSearchCursor(texto, first);
			first = false;
			while (cursor.findNext()) {
				cursor.select();
				if (!confirm("Desea buscar de nuevo?"))	return;
			}
		} while (confirm("Se llegó al final del documento. Comenzar desde arriba?"));
	}

	ei_codigo.prototype.set_numeros_linea = function(valor) {
		this._mirror.setLineNumbers(valor);
	}

	ei_codigo.prototype.cambiar_tabsize = function(size) {
		this._mirror.setIndentUnit(size);
		this._mirror.reindent();
	}

	ei_codigo.prototype.tiene_errores = function() {
		var iframe = getElementsByClass(this._class_iframe, document, 'iframe')[0];
		var errors = getElementsByClass('syntax-error', iframe.contentWindow.document);
		return errors.length > 0;
	}

toba.confirmar_inclusion('componentes/ei_codigo');
