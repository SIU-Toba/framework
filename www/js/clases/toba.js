//--------------------------------------------------------------------------------
//Clase singleton Toba
var toba = 
{
	_objetos: new Array(), 
	_imagenes: new Object(),
	
	agregar_objeto : function(o) {
		this._objetos.push(o);
	},
	
	objetos : function() {
		var nombres = Array();
		for (o in this._objetos) {
			var clase = getObjectClass(this._objetos[o]);
			nombres.push(this._objetos[o]._instancia + ' [' + clase + ']');
		}
		return nombres.join(', ');
	},
	
	
	imagen : function (nombre) {
		return lista_imagenes[nombre];
	}
	
}

