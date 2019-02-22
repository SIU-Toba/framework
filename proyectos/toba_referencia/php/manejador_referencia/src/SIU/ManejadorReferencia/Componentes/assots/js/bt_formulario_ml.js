/**
 * 
 */
ei_formulario_ml.prototype.refrescar_seleccion = function () {
		if (isset(this._seleccionada)) {
			var id = '#'+this._instancia + '_fila' + this._seleccionada;
			$(id).toggleClass('active');
			var subir_fila = document.getElementById(this._instancia + '_subir' + this._seleccionada);
			var bajar_fila = document.getElementById(this._instancia + '_bajar' + this._seleccionada);
			if (subir_fila) {
				subir_fila.style.visibility = 'visible';
			}
			if (bajar_fila) {
				bajar_fila.style.visibility = 'visible';			
			}
			
			if (this.boton_eliminar()) {
				this.boton_eliminar().disabled = false;
			}
			if (this.boton_subir()) {
				this.boton_subir().disabled = false;
				this.boton_bajar().disabled = false;			
			}
		} else {
			if (this.boton_eliminar()) {
				this.boton_eliminar().disabled = true;
			}
			if (this.boton_subir()) {
				this.boton_subir().disabled = true;
				this.boton_bajar().disabled = true;
			}
		}
	};

ei_formulario_ml.prototype.deseleccionar_actual = function() {
	if (isset(this._seleccionada)) {	//Deselecciona el anterior
		var fila = $('#'+this._instancia + '_fila' + this._seleccionada);
		if (fila) {
			fila.toggleClass('active');			
			var subir_fila = document.getElementById(this._instancia + '_subir' + this._seleccionada);
			var bajar_fila = document.getElementById(this._instancia + '_bajar' + this._seleccionada);
			if (subir_fila) {
				subir_fila.style.visibility = 'hidden';
			}			
			if (bajar_fila) {
				bajar_fila.style.visibility = 'hidden';
			}
		}
		delete(this._seleccionada);
		
	}
};