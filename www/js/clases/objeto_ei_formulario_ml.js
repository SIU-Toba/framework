
function objeto_ei_formulario_ml(form, cant_filas)
{
	this.form = form;
	this.efs = new Array();
	this.cant_filas = cant_filas;

	this.agregar_ef = function (objeto)
	{
		if (objeto)
			this.efs[objeto.id()] = objeto;
	}
	
	this.agregar_totalizacion =	function (id_ef, nombre_objeto)
	{
		if (this.efs[id_ef]) {
			for (var fila =0 ; fila < this.cant_filas ; fila++)	{
				var callback = new Function (nombre_objeto + '.totalizar_columna("' + id_ef + '")');
				this.efs[id_ef].set_fila(fila).evento_cambia_valor(callback);
			}
		}
	}
	
	this.totalizar_columna = function (id_ef)
	{
		total = this.get_total_columna(id_ef);
		document.getElementById(id_ef + 's').innerHTML = total;
	}
	
	
	this.get_total_columna = function (id_ef)
	{
		var total = 0;	
		for (var fila =0 ; fila < this.cant_filas ; fila++)	{
			valor = this.efs[id_ef].set_fila(fila).valor();
				total += valor
		}
		return total;
	}	

}