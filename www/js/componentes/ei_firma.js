ei_firma.prototype = new ei();
ei_firma.prototype.constructor = ei_firma;

/**
 * @class Editor de código
 * @constructor
 * @phpdoc Componentes/Eis/toba_ei_firma toba_ei_firma
 */
function ei_firma(id, input_submit, multiple) {
	this._id = id;
	this._input_submit = input_submit;
	this._multiple = multiple;
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
	
	ei_firma.prototype.ver_pdf_inline = function(url_pdf) {
		var success = new PDFObject({ 
			url: url_pdf,
			pdfOpenParams: { toolbar: "0", statusbar: "0" }
		}).embed("pdf");
		
	}

	ei_firma.prototype.applet_cargado = function() {
		if (document.getElementById("pdf")) {
			document.getElementById("pdf").style.display = "";
		}
		this.evt__applet_cargado();
	}
	
	ei_firma.prototype.firma_ok = function() {
		if (document.getElementById("pdf")) {
			document.getElementById("pdf").style.display = "none";
		}
		this.evt__firma_ok();
	}

	ei_firma.prototype.evt__applet_cargado = function() { };
	ei_firma.prototype.evt__firma_ok = function() { };



toba.confirmar_inclusion('componentes/ei_firma');
