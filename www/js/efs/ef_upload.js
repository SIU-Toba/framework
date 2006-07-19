//--------------------------------------------------------------------------------
//Clase ef_upload
ef_upload.prototype = new ef();
var def = ef_upload.prototype;
def.constructor = ef_upload;

	function ef_upload(id_form, etiqueta, obligatorio, colapsado) {
		ef.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsado);
	}

	def.validar = function () {
		if (this.get_cambiar_archivo()) {
			if (! ef.prototype.validar.call(this)) {
				return false;		
			}
			if (this._obligatorio && trim(this.get_estado()) === "") {
				this._error = 'es obligatorio.';
			    return false;
			}
		}
		return true;
	};
	
	def.set_editable = function() {
		var desicion = document.getElementById(this._id_form + '_desicion');
		if (this.get_cambiar_archivo()) {
			//Lo va a cambiar
			desicion.style.display = 'none';
			this.input().style.display = '';
		} else {
			desicion.style.display = '';
			this.input().style.display = 'none';
		}
	};

	def.get_cambiar_archivo = function() {
		var input = document.getElementById(this._id_form + '_check');
		if (input) {
			return input.checked;
		} else {
			return false;
		}
	};
	
toba.confirmar_inclusion('efs/ef_upload');