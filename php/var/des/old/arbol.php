<?php

class arbol
{
	
	//tabla : tabla sobre la que se arma el catalogo
	//campo_padre : campo de la tabla que actua como padre
	//valor_padre : valor que tiene el padre (en el 1er llamado se suele especifica el padre de nivel jerarquico superior)
	//campo_clave : campo que actua como PK
	//campo_mostrar : campo que contiene el valor que se desea mostrar
	//campo_orden   : si existe, campo sobre el cual se desea ordenar los resultados
	//condicion 	: si existe, condicion adicional que se desea agregar a la consulta
	//sql		 	: consulta sql
	//segunda_columna	: permite generar una segunda columna (ej : agregar botones, links, etc).
	//					  Debe especificarse el nombre de la funcion, dicha funcion recibira como 
	//					  parametros un arreglo con los valores de la fila actual	
	
	var $tabla;
	var $campo_padre;
	var $valor_padre;
	var $campo_clave;
	var $campo_mostrar;
	var $campo_orden;
	var $condicion;
	var $sql;
	var $segunda_columna;

	function GeneraArbol($nivel)
	{
		global $db;
		global $canal;
		global $ADODB_FETCH_MODE;
		//Arreglos con clases de estilo CSS
		global $arbol_clase;
	
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		
		$rs = $db->Execute($this->sql);
		//Si la consulta no devuelve nada salgo
		if ($rs->EOF)
			return false;
		
		//Convierto el query en un arreglo
		$catalogo		= $rs->GetArray();
		
		$sizecatalogo	= sizeof($catalogo);
		$ancho			= 25;
	  	$ancho_total	= 0;
		
		for($x=0;$x<$sizecatalogo;$x++)
	  	{      
			//Muevo a la posicion x
	 		$fila = array_slice($catalogo,$x);
	  		//Obtengo el arreglo de esa posicion
	  		$fila = $fila[0];
	
			$id_clave	 = trim($fila[$this->campo_clave]);
			
			//$id_elemento = trim($fila["catalogo"]);
			//$id_padre	 = trim($fila["padre"]);
		
			echo "<table cellspacing=1 cellpading=0>\n";
	 		echo "<tr>\n";
			
			//Saco el ancho segun el nivel jerarquico
			$ancho_total = $nivel * $ancho; 		
			
			echo "<td width='$ancho_total'>\n";
			echo "</td>\n";		
			
			//Calculo el ancho de la columna, en base al tamaño de la primera
			$ancho_celda = 400 - $ancho_total;
			
			//Defino la clase a asignar segun el nivel
			$pos = $fila["catalogo_tipo"];
			
			echo "<td class='$arbol_clase[$pos]' width='$ancho_celda' valign=\"top\">\n";
			echo trim($fila[$this->campo_mostrar]);
			echo "</td>\n";
			
			if (!is_null($this->segunda_columna))
				$funcion = $this->segunda_columna;
				eval($funcion($fila,$arbol_clase));
						
			echo "</tr>\n";
		
			$this->sql =  "select * from {$this->tabla} WHERE {$this->campo_padre}='" . $fila[$this->campo_clave] . "' AND {$this->campo_clave} <> '" . $fila[$this->campo_clave] . "' {$this->condicion} ";
			if (!is_null($campo_orden))
				$this->sql .= " ORDER BY {$this->campo_orden}";
			
			$this->GeneraArbol($nivel+1);
		  	echo "</table>\n";
	  	}
	}
}

?>