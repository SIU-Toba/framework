
ef_upload.prototype = new ef();
ef_upload.prototype.constructor = ef_upload;

	/**
	 * @class Ef que selecciona un archivo de su sistema para que esté disponible en el servidor.<br>
	 * Esta basado en el elemento html <a href='http://www.w3schools.com/htmldom/dom_obj_fileupload.asp'>input type=file</a>.<br>
	 * Por razones de seguridad muchos de los servicios de javascript clásicos (como obtener el valor actual del elemento) no están disponibles.
	 * @constructor
	 */
	function ef_upload(id_form, etiqueta, obligatorio, colapsado) {
		ef.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsado);
	}

	ef_upload.prototype.validar = function () {
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
	
	/**
	 * Invierte la visibilidad del input HTML, en base al checkbox para cambiar de archivo
	 * Este método sólo se utiliza cuando se esta modificando un archivo ya subido.	 
	 */	
	ef_upload.prototype.set_editable = function() {
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

	/**
	 * Retorna verdadero si el checkbox para cambiar el archivo esta checado
	 * Este método sólo se utiliza cuando se esta modificando un archivo ya subido.
	 * @type boolean
	 */
	ef_upload.prototype.get_cambiar_archivo = function() {
		var input = document.getElementById(this._id_form + '_check');
		if (input) {
			return input.checked;
		} else {
			return false;
		}
	};
	
toba.confirmar_inclusion('efs/ef_upload');