//--------------------------------------------------------------------------------
//Clase ef_upload
ef_upload.prototype = new ef;
var def = ef_upload.prototype;
def.constructor = ef_upload;

	function ef_upload(id_form, etiqueta, obligatorio, colapsado) {
		ef.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsado);
	}

	def.validar = function () {
		if (this._obligatorio && this.valor() == "") {
			this._error = 'El campo ' + this._etiqueta + ' es obligatorio.';
		    return false;
		}
		return true;
	}

	