function TildaCheck(elemento)
{
	//Evaluo si estoy chequeando o deschequeando al elemento
	if (elemento.checked)
		estado = true;
	else
		estado = false; 
	
	//Referencia al formulario actual
	formulario = elemento.form;
	//Recorro todos los elementos del form
	for (x=0 ; x < formulario.elements.length ; x++)
	{
		valor_elemento = formulario.elements[x].value;
		//Si el elemento es un hijo y corresponde a un checkbox lo activo/desactivo segun corresponda
		if ((valor_elemento.match(elemento.value)) && (formulario.elements[x].type=="checkbox"))
			formulario.elements[x].checked = estado;
		//Camino inverso, ahora busco los padres, pero solo para chequearlos, no los puedo deschequear porque pueden crearse inconcistencias.	
		if ((elemento.value.match(valor_elemento)) && (formulario.elements[x].type=="checkbox") && (estado))
			formulario.elements[x].checked = estado;
	}	
	
	//Busco si el elemento que estoy deschequeando es el ultimo hijo seleccionado, para deseleccionar a su padre
	if (!estado)
	{
		//Busco el ultimo caracter "_"
		dato_elemento = elemento.value.length;
		//alert (dato_elemento);
		n_parcial = elemento.value;
		y = dato_elemento;
		while(y > 0)
		{
			letra_parcial = n_parcial.substr(y,1);
			if (letra_parcial == "_")
			{
				//Encontre el ultimo "_" ahora genero el padre
				valor_padre = n_parcial.substr(0,y+1);
				break;
			}
			y--;
		}
		
		//Si ya no encuentra el caracter "_" (esto pasaria con el padre "siu") no dejo que siga
		if (y == 0)
			return false;
		
		//Busco elementos con el mismo comienzo que esten chequeados
		for (xx=0 ; xx < formulario.elements.length ; xx++)	
		{
			valor_elemento = formulario.elements[xx].value;
			if (valor_elemento.match(valor_padre))
			{
				//Si ya existe un hijo chequeado no tengo que desmarcar nada
				if (formulario.elements[xx].checked)
				{
					marca_padre = false;
					break;
				}	
			}
			marca_padre = true;
		}
		
		//Si viene en true tengo que deshabilitar el padre
		if (marca_padre)
		{
			tamano_padre = valor_padre.length - 1;
			nombre_padre = "permiso|" + valor_padre.substr(0,tamano_padre);
			//Si el objeto no existe
			if (formulario.elements[nombre_padre] != undefined)
			{
				formulario.elements[nombre_padre].checked = false;
				//Llamo nuevamente a la funcion para que vuelva a verificar por el padre del elemento destildado
				obj_padre = formulario.elements[nombre_padre];
				TildaCheck(formulario.elements[nombre_padre]);
			}
			else
			{
				return false;	
			}	
		}
	}	
}
