//---STRING
//----------------------------------------------------------

//Retorna la primer ocurrencia de alguna de estas cadenas
String.prototype.primer_ocurrencia = function (cadenas) {
	var primera = this.length;
	for (var i =0 ; i < cadenas.length; i++) {
		ocurrencia = this.indexOf(cadenas[i]);
		if (ocurrencia != -1 && ocurrencia < primera) {
			primera = ocurrencia
		}
	}
	return primera;
}

//Retorna la ultima ocurrencia de alguna de estas cadenas
String.prototype.ultima_ocurrencia = function (cadenas) {
	var ultima = 0;
	for (var i =0 ; i < cadenas.length; i++) {
		ocurrencia = this.lastIndexOf(cadenas[i]);
		if (ocurrencia > ultima) {
			ultima = ocurrencia
		}
	}
	return ultima;
}

//Retorna el quote necesario para usar en una exp. regular
String.prototype.quote_exp_reg = function () {
	return this.reemplazar('$', '\\$');
}

//Reemplaza todas las ocurrencias de un string en otro
String.prototype.reemplazar = function (buscado, nuevo) {
	var srchNdx = 0; 
	var newStr = ""; 
	while (this.indexOf(buscado,srchNdx) != -1)  
	{
	  newStr += this.substring(srchNdx,this.indexOf(buscado,srchNdx));
	  newStr += nuevo;
	  srchNdx = (this.indexOf(buscado,srchNdx) + buscado.length);
	}
	newStr += this.substring(srchNdx,this.length);
	return newStr;
}

//---ARRAY
//----------------------------------------------------------
Array.prototype.contiene = function (elemento) {
	for (var i=0 ; i < this.length; i++) {
		if (this[i] == elemento)
			return true;
	}
	return false;
}

//----Varios
//--------------------------------------------

function ei_arbol(arreglo)
{
	var salida = '';
	for (dim in arreglo) {
		salida = salida + dim + ' => ' + arreglo[dim] + '\n';
	}
	alert(salida);
}
