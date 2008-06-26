<?php
//-------------------------------------------------------------------------------------
//--------------------------- Funcionalidad generica SQL ------------------------------
//-------------------------------------------------------------------------------------

	/**
	 * Concatena sentencias a la clausula WHERE de un SQL (Utilizando un AND)
	 */	
	function sql_agregar_clausulas_where($sql,$clausulas_where=null)
	{
	    if(preg_match("/\s+where\s+/i",$sql)){
	        //TODO: Esto falla en SELECT con subquerys que no tienen WHERE en el cuerpo
	        //del query central, por ahora SALE... 
	        $prefijo = " AND ";
	    }else{
	        $prefijo = " WHERE ";
	    }
	    $reemplazo = "";
	    if(is_array($clausulas_where)){
			if(count($clausulas_where)>0){
		        $reemplazo = $prefijo . " (" . implode(" AND ",$clausulas_where) . ") ";
			}
		}
		return ereg_replace(apex_sql_where,$reemplazo,$sql);
		//if(!stristr($sql,"where"))
	}

	/**
	 * Concatena sentencias a la clausula WHERE de un SQL (Utilizando un AND)
	 */	
	function sql_concatenar_where($sql,$clausulas_where=array())
	{
		if (empty($clausulas_where)) {
			return $sql;
		}
		$balance_parentesis = 0; //Para saber si estoy metido en unos parentesis
		$todos = preg_split("/([\s,;]+)/", $sql, -1, PREG_SPLIT_DELIM_CAPTURE);
		$separadores = array();
		$palabras = array();
		$par = false;
		//Se divide entre palabras y separadores para no perder la union luego
		foreach ($todos as $candidato) {
			if ($par) {
				$separadores[] = $candidato;
			} else {
				$palabras[] = $candidato;
			}
			$par = !$par;
		}
		$separadores[] = ''; 
		$punto_insercion = null;
		$posee_where = false;
		//--- Averiguo el punto de insercion ----------------------------------------
		foreach($palabras as $posicion => $palabra) {
			$balance_parentesis += substr_count($palabra, '(');
			$balance_parentesis -= substr_count($palabra, ')');
			if( $balance_parentesis == 0) {
				if( (strtolower($palabra) == 'where')	) {
					$posee_where = true;
					continue;
				}
				if( ( (strtolower($palabra) == 'group') && (strtolower($palabras[$posicion+1]) == 'by') )
					|| (  (strtolower($palabra) == 'order') && (strtolower($palabras[$posicion+1]) == 'by') ) ) {
					$punto_insercion = $posicion - 1;
					continue;
				}
			}
		}
		//Llego al final sin encontrar naad
		if(!isset($punto_insercion)){
			if(in_array(';',$separadores)){
				$punto_insercion = count($palabras)-2;
			} else {
				$punto_insercion = count($palabras)-1;
			}
		}
		$sql = '';
		for ($i = 0; $i < count($palabras) ; $i++) {
			if($separadores[$i]!=';') {
				$sql .= $palabras[$i].$separadores[$i].' ';
			} else {
				$sql .= $palabras[$i].' ';
			}
			//--- Concateno la clausula WHERE -------------------------------
			if ($i == $punto_insercion) {
				$sql .= ($posee_where) ? ' AND ' : ' WHERE ';
				$sql .= "\n";
				$sql .= implode(' AND ', $clausulas_where);
				$sql .= "\n";
			}
			if($separadores[$i]==';') {
				$sql .= $separadores[$i];
			}
		}
		return $sql;
	}

	/**
	 * Concatena tablas a la clausula FROM de un SQL
	 */	
	function sql_agregar_tablas_from($sql,$tablas_from=null)
	{
	    $reemplazo = "";
	    if(is_array($tablas_from)){
			if(count($tablas_from)>0){
		        $reemplazo = ", " . implode(", ",$tablas_from);
			}
	    }
	    return ereg_replace(apex_sql_from,$reemplazo,$sql);
	}
	
	/**
	 * Concatena columnas a la clausula WHERE de un SQL
	 * @param array $columnas Matriz de columna, tipo. Donde tipo es asc o des
	 */
	function sql_agregar_ordenamiento($sql, $columnas)
	{
		if (count($columnas) > 0)
		{
			//Busca la ultima ocurrencia de ORDER BY en el SQL
			$buscado = 'ORDER BY';
			$ultima_pos = strripos($sql, $buscado);
			if ($ultima_pos) {	//Si la encuentra
				$anexo = "";
				$parte_inicial = substr($sql, 0, $ultima_pos + strlen($buscado));
				$parte_final = ", ".substr($sql, $ultima_pos + strlen($buscado), strlen($sql));
			} else {
				$anexo = "\nORDER BY ";
				$parte_inicial = $sql;
				$parte_final = '';
			}
			foreach ($columnas as $columna){
				switch ($columna[1]){
					case 'asc': $tipo_orden = 'ASC';break;
					case 'des': $tipo_orden = 'DESC';break;
					default: $tipo_orden = '';
				}
				$anexo .= " {$columna[0]} $tipo_orden, " ;
			}
			$anexo = substr($anexo, 0, -2); //Se le saca la ultima coma	
			$sql = $parte_inicial.$anexo.$parte_final;
		}
		return $sql;
	}
	
	/**
	 * Convierte un array asociativo en una sentencia de INSERT
	 * @param array $datos Array asociativo con el formato 'columna' => 'valor'
	 */
	function sql_array_a_insert($tabla, $datos)
	{
		if(!is_array($datos)){
			throw new toba_error("Los datos tienen que ser un array");	
		}
		foreach(array_keys($datos) as $columna){
			if(is_null( $datos[$columna] )){
				$datos[$columna] = "%%NULL%%";//Identifico los nulos
			}else{
				$datos[$columna] = addslashes($datos[$columna]);//Escapo caracteres
			}
		}
		$sql = "INSERT INTO $tabla (" . implode(", ",array_keys($datos)) . ")". 
				" VALUES ('" . implode("', '",$datos) . "');";
		$sql = ereg_replace("'%%NULL%%'","NULL",$sql);//Formateo los nulos
		return $sql;	
	}
	
	/**
	 * Convierte un array asociativo en una sentencia de INSERT formateada para evitar conflictos cuando hay merges
	 * @param array $datos Array asociativo con el formato 'columna' => 'valor'
	 */
	function sql_array_a_insert_formateado($tabla, $datos)
	{
		if(!is_array($datos)){
			throw new toba_error("Los datos tienen que ser un array");	
		}
		foreach(array_keys($datos) as $columna){
			if(is_null( $datos[$columna] )){
				$datos[$columna] = "%%NULL%%";//Identifico los nulos
			}else{
				$datos[$columna] = addslashes($datos[$columna]);//Escapo caracteres
			}
		}
		$sql = "INSERT INTO $tabla (" . implode(", ",array_keys($datos)) . ")". 
				" VALUES (";

		$i = 1;
		foreach ($datos as $clave => $valor) {
			$coma = ($i < count($datos)) ? ',' : ' ';
			$sql .= "\n\t'".$valor."'$coma --$clave";
			$i++;
		}
		$sql .=	"\n);\n";
		$sql = ereg_replace("'%%NULL%%'","NULL",$sql);//Formateo los nulos
		return $sql;	
	}	

	/**
	 * Saca construcciones SQL de un STRING (necesario para concatenar el texto del usuario en el WHERE de un SQL)
	 */
	function filtrar_sql($sentencia)
	{
		//echo "entrada al FILTRO SQL: $sentencia <br>";
		$temp = trim($sentencia);
		$temp = ereg_replace("%","",$temp);
		$temp = ereg_replace("=","",$temp);
		$temp = ereg_replace("\"","",$temp);
		$temp = ereg_replace("\\\\","",$temp);
		$temp = ereg_replace("'","",$temp);
		$temp = ereg_replace("WHERE","",$temp);
		$temp = ereg_replace("ORDER BY","",$temp);
		$temp = ereg_replace("GROUP BY","",$temp);
		$temp = ereg_replace("\(","",$temp);
		$temp = ereg_replace("\)","",$temp);
		$temp = ereg_replace(" AND ","",$temp);
		$temp = ereg_replace(" OR ","",$temp);
		//echo "salida del filtro SQL: $temp <br>";
		return trim($temp);
	}

	function sql_array_tablas_drop( $tablas, $cascade = true )
	{
		$sql = array();
		$sql_cascade = $cascade ? 'CASCADE' : '';
		foreach ( $tablas as $tabla ) {
			$sql[] = "DROP TABLE $tabla $sql_cascade;";
		}
		return $sql;
	}

	/*
	*	Espera un array asociativo con el nombre de la tabla como clave
	*		y el WHERE como valor
	*/
	function sql_array_tablas_delete( $tablas )
	{
		$sql = array();
		foreach ( $tablas as $tabla => $where ) {
			$sql[] = "DELETE FROM $tabla $where;";
		}
		return $sql;
	}

	function sql_array_secuencias_drop( $tablas )
	{
		$sql = array();
		foreach ( $tablas as $tabla ) {
			$sql[] = "DROP SEQUENCE $tabla;";
		}
		return $sql;
	}

	/**
	 * Dada una tabla, asume un posible alias para el select
	 */
	function sql_get_alias($tabla)
	{
		$alias = '';
		foreach(explode('_', $tabla) as $parte) {
			$alias .= substr($parte, 0, 1);
		}
		return $alias;
	}
	
?>