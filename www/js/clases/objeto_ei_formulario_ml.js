//--------------------------------------------------------------------------------
//Clase objeto_ei_formulario_ml
var def = objeto_ei_formulario_ml.prototype;

	function objeto_ei_formulario_ml(form, cant_filas) {
		this.form = form;
		this.efs = new Array();
		this.cant_filas = cant_filas;
	}


	def.agregar_ef  = function (ef) {
		if (ef)
			this.efs[ef.id()] = ef;
	}

	def.agregar_totalizacion = function (id_ef, listener) {
		if (this.efs[id_ef]) {
			for (var fila =0 ; fila < this.cant_filas ; fila++)	{
				var callback = new Function (listener + '.totalizar_columna("' + id_ef + '")');
				this.efs[id_ef].posicionarse_en_fila(fila).cuando_cambia_valor(callback);
				callback();
			}
		}
	}

	def.totalizar_columna = function (id_ef) {
		total = this.get_total_columna(id_ef);
		document.getElementById(id_ef + 's').innerHTML = total;
	}

	def.get_total_columna = function (id_ef) {
		var total = 0;	
		for (var fila =0 ; fila < this.cant_filas ; fila++)	{
			valor = this.efs[id_ef].posicionarse_en_fila(fila).valor();
			valor = (valor == '' || isNaN(valor)) ? 0 : valor;
			total += valor
		}
		return total;
	}
	
	def.validar = function() {
		for (var fila =0 ; fila < this.cant_filas ; fila++)	{
			for (id_ef in this.efs) {
				if (! this.efs[id_ef].posicionarse_en_fila(fila).validar())
					return false;
			}
		}
		return true;
	}

//--------------------------------------------------------------------------------	