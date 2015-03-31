
ef_upload.prototype = new ef();
ef_upload.prototype.constructor = ef_upload;

	/**
	 * @class Ef que selecciona un archivo de su sistema para que esté disponible en el servidor.<br>
	 * Esta basado en el elemento html <a href='http://www.w3schools.com/htmldom/dom_obj_fileupload.asp'>input type=file</a>.<br>
	 * Por razones de seguridad muchos de los servicios de javascript clásicos (como obtener el valor actual del elemento) no están disponibles.
	 * @constructor
	 * @phpdoc Componentes/Efs/toba_ef_upload toba_ef_upload
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
	
	/**
	 * Retorna verdadero si el ef tiene algún valor cargado
	 * @type boolean
	 */	
	ef_upload.prototype.tiene_estado = function() {
		var archivo = document.getElementById(this._id_form);
		var desicion = document.getElementById(this._id_form + '_desicion');
		var checkbox = document.getElementById(this._id_form + '_check');
				
		var con_descripcion = (desicion && trim(desicion.innerHTML) != ''); 
		var eligio = (archivo && archivo.files.length > 0);				//Determino si eligio archivo alguno		
		
		//Si no hay checkbox o esta checkeado, la respuesta depende del input html
		if (! checkbox || checkbox.checked) {		
			return eligio;			
		} else if(checkbox && ! checkbox.checked) {	//Si existe pero no esta chequeado, hay que ver si venia con estado.
			return con_descripcion;
		}
	};
	
	ef_upload.prototype.get_estado = function() {
		var decision = document.getElementById(this._id_form + '_desicion');
		var checkbox = document.getElementById(this._id_form + '_check');
		
		//Si no hay checkbox o esta checkeado, la respuesta depende del input html
		if (! checkbox || checkbox.checked) {		
			return ef.prototype.get_estado.call(this);			
		} else if(checkbox && ! checkbox.checked) {	//Si existe pero no esta chequeado, hay que ver si venia con estado.
			return decision.innerHTML;
		}
	};

toba.confirmar_inclusion('efs/ef_upload');