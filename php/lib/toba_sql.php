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
		return preg_replace(apex_sql_where,$reemplazo,$sql);
		//if(!stristr($sql,"where"))
	}

	/**
	 * Concatena sentencias a la clausula WHERE de un SQL (Utilizando un AND)
	 */	
	function sql_concatenar_where($sql,$clausulas_where=array(), $comentario='')
	{
		if (empty($clausulas_where)) {
			return $sql;
		}
		$sql = trim($sql);
		//Elimino el ultimo punto y coma
		if ($sql != '' && $sql[strlen($sql) - 1] == ';') {
			 $sql = substr($sql, 0, -1);
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
					|| (  (strtolower($palabra) == 'order') && (strtolower($palabras[$posicion+1]) == 'by') ) 
					|| (  strtolower($palabra) == 'limit') ) {
					$punto_insercion = $posicion - 1;
					break;
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
			if(trim($separadores[$i]) !=';') {
				$sql .= $palabras[$i].$separadores[$i];
			} else {
				$sql .= $palabras[$i];
			}
			//--- Concateno la clausula WHERE -------------------------------
			if ($i == $punto_insercion) {
				$sql .= ($posee_where) ? ' AND ' : ' WHERE ';
				$sql .= "\n";
				if ($comentario != '') {
					$sql .=	"/*-------- $comentario --------*/\n";
				}
				$sql .= implode(' AND ', $clausulas_where);
				if ($comentario != '') {
					$sql .=	"\n/*------------------------*/\n";
				}					
				$sql .= "\n";
			}
			if($separadores[$i]==';') {
				$sql .= $separadores[$i];
			}
		}
		return $sql;
	}

	function sql_concatenar_clausulas_producto_cartesiano($sql, $fuente_datos , $where)
	{		
		//-- 1: Preparo el SQL
		// Le saco el ';' de atras
		$sql = trim($sql);
		if(  substr($sql, -1, 1) ==';') {
			$sql = substr($sql, 0, (strlen($sql)-1) );
		}		
		//-- 2: Regenero la SQL copiando los tokens ---
		$en_join = false;
		$sql_final = '';
		$tabla = '';
		$tokens = preg_split("/(\s+)/",$sql);		//ei_arbol($tokens);
		$cant = count($tokens);
		for ($i = 0; $i < $cant ; $i++) {		
			if ($en_join && isset($where[$tabla])) {		//Estoy dentro de un JOIN y existe una clausula para esa tabla
				if ((stripos($tokens[$i],'(') !== false) && (strlen($tokens[$i]) == 1)) {	//Si es un parentesis que abre
					$sql_final .= $tokens[$i] . $where[$tabla] . ' AND ';			//Primero va el parentesis y luego la expresion
				} else {				
					$sql_final .= $where[$tabla] . ' AND ' .$tokens[$i] . ' ';		//Sino primero la expresion y luego lo que venga
				}
				unset($where[$tabla]);
				continue;
			}
			
			$sql_final .= $tokens[$i] . ' ';				//paso el token actual para seguir armando la SQL
			if (stripos($tokens[$i], 'JOIN')  !== false) {		//es la clausula del proximo JOIN
				$en_join = false;
				$tmp_name = $tokens[$i+1];
				$pos_schema = stripos($tmp_name, '.');					
				$tabla = ($pos_schema !== false) ? substr($tmp_name, $pos_schema + 1) : $tmp_name;				
			} elseif (strtolower(trim($tokens[$i])) == 'on') {
				$en_join = true;
			}
		}
		return $sql_final;
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
	    return preg_replace(apex_sql_from,$reemplazo,$sql);
	}
	
	/**
	 * Concatena columnas a la clausula WHERE de un SQL
	 * @param array $columnas Matriz de columna, tipo. Donde tipo es asc o des
	 */
	function sql_agregar_ordenamiento($sql, $columnas)
	{
		if (count($columnas) > 0)
		{
			//Busca la ultima ocurrencia de ORDER BY en el SQL, espera el ORDER BY sin espacio entre las palabras
			$buscado = 'ORDER BY';
			$ultima_pos = strripos($sql, $buscado);
			if ($ultima_pos !== false) {	//Si la encuentra
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
	function sql_array_a_insert($tabla, $datos, $db = null)
	{
		if(!is_array($datos)){
			throw new toba_error("Los datos tienen que ser un array");	
		}
		if (!$db) {
			$db = toba::db();
		}
		foreach(array_keys($datos) as $columna){
			if(is_null( $datos[$columna] )){
				$datos[$columna] = "NULL";//Identifico los nulos
			}else{
				if (is_resource($datos[$columna])) {
					$datos[$columna] = stream_get_contents($datos[$columna]);
				}			
				if (is_bool($datos[$columna])) {
					$datos[$columna] = ($datos[$columna]) ? 'TRUE' : 'FALSE';
				} else {
					$datos[$columna] = $db->quote($datos[$columna]);//Escapo caracteres
				}				
			}
		}
		$sql = "INSERT INTO $tabla (" . implode(", ",array_keys($datos)) . ")". 
				" VALUES (" . implode(", ",$datos) . ");";
		return $sql;	
	}
	
	/**
	 * Convierte un array asociativo en una sentencia de INSERT
	 * @param array $datos Array asociativo con el formato 'columna' => 'valor'
	 */
	function sql_array_a_insert_condicional($tabla, $datos, $db = null)
	{
		if(!is_array($datos)){
			throw new toba_error("Los datos tienen que ser un array");	
		}
		$clausulas = array();
		foreach(array_keys($datos) as $columna){
			if(is_null( $datos[$columna] )){
				$datos[$columna] = "NULL";//Identifico los nulos
			}else{
				if (is_resource($datos[$columna])) {
					$datos[$columna] = stream_get_contents($datos[$columna]);
				}			
				if (is_bool($datos[$columna])) {
					$datos[$columna] = ($datos[$columna]) ? 'TRUE' : 'FALSE';
				} else {
					$datos[$columna] = $db->quote($datos[$columna]);//Escapo caracteres
				}				
			}
			$clausulas[] = " $columna = {$datos[$columna]} ";
		}
		$sql = "INSERT INTO $tabla (" . implode(", ",array_keys($datos)) . ')'
				.' ( SELECT ' . implode(', ',$datos) 				
				.' WHERE NOT EXISTS ('
				. ' SELECT 1 '
				. ' FROM ' . $tabla 
				. ' WHERE ' . implode(' AND ', $clausulas) .')'
				.');';			
			
		return $sql;	
	}
	
	/**
	 * Convierte un array asociativo en una sentencia de INSERT formateada para evitar conflictos cuando hay merges
	 * @param array $datos Array asociativo con el formato 'columna' => 'valor'
	 */
	function sql_array_a_insert_formateado($tabla, $datos, $db = null)
	{
		if(!is_array($datos)){
			throw new toba_error("Los datos tienen que ser un array");	
		}
		foreach(array_keys($datos) as $columna){
			if(is_null( $datos[$columna] )){
				$datos[$columna] = "NULL";//Identifico los nulos
			}else{
				if (is_resource($datos[$columna])) {
					$datos[$columna] = stream_get_contents($datos[$columna]);
				}
				if (is_bool($datos[$columna])) {
					$datos[$columna] = ($datos[$columna]) ? 'TRUE' : 'FALSE';
				} else {
					$datos[$columna] = $db->quote($datos[$columna]);//Escapo caracteres
				}
			}
		}
		$sql = "INSERT INTO $tabla (" . implode(', ',array_keys($datos)) . ')'. 
				' VALUES (';

		$i = 1;
		foreach ($datos as $clave => $valor) {
			$coma = ($i < count($datos)) ? ',' : ' ';
			$sql .= "\n\t$valor$coma --$clave";			//Sacar las comillas simples cuando el quote funcione correctamente
			$i++;
		}
		$sql .=	"\n);\n";
		return $sql;	
	}	

	/**
	 * Convierte un array asociativo en una sentencia de UPDATE
	 * @param array $datos Array asociativo con el formato 'columna' => 'valor'
	 * @param array $where Array asociativo con el formato 'columna' => 'valor'
	 */
	function sql_array_a_update($tabla, $datos, $where, $db = null)
	{
		if(!is_array($datos)){
			throw new toba_error("Los datos tienen que ser un array");
		}
		$datos = limpiar_array_sql($datos, $db);

		$updates = array();
		foreach ($datos as $k => $v) {
			$updates[] = "$k = $v";
		}
		$set = implode(',', $updates);

		if(!empty($where)){
			$where = limpiar_array_sql($where, $db);
			$wheres = array();
			foreach ($where as $k => $v) {
				$wheres[] = "$k = $v";
			}
			$w = "WHERE ".implode(' AND ', $wheres);
		}else {
			$w = '';
		}

		$sql = "UPDATE $tabla SET $set $w;";
		return $sql;
	}


	function limpiar_array_sql($datos, $db = null)
	{
		if (!$db) {
			$db = toba::db();
		}
		foreach (array_keys($datos) as $columna) {
			if (is_null($datos[$columna])) {
				$datos[$columna] = "NULL"; //Identifico los nulos
			} else {
				if (is_resource($datos[$columna])) {
					$datos[$columna] = stream_get_contents($datos[$columna]);
				}
				if (is_bool($datos[$columna])) {
					$datos[$columna] = ($datos[$columna]) ? 'TRUE' : 'FALSE';
				} else {
					$datos[$columna] = $db->quote($datos[$columna]); //Escapo caracteres
				}
			}
		}
		return $datos;
	}

	/**
	 * Saca construcciones SQL de un STRING (necesario para concatenar el texto del usuario en el WHERE de un SQL)
	 */
	function filtrar_sql($sentencia)
	{
		//echo "entrada al FILTRO SQL: $sentencia <br>";
		$temp = trim($sentencia);
		$temp = preg_replace("%","",$temp);
		$temp = preg_replace("=","",$temp);
		$temp = preg_replace("\"","",$temp);
		$temp = preg_replace("\\\\","",$temp);
		$temp = preg_replace("'","",$temp);
		$temp = preg_replace("WHERE","",$temp);
		$temp = preg_replace("ORDER BY","",$temp);
		$temp = preg_replace("GROUP BY","",$temp);
		$temp = preg_replace("\(","",$temp);
		$temp = preg_replace("\)","",$temp);
		$temp = preg_replace(" AND ","",$temp);
		$temp = preg_replace(" OR ","",$temp);
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
		$alias = 't_';
		foreach(explode('_', $tabla) as $parte) {
			$alias .= substr($parte, 0, 1);
		}
		return $alias;
	}
	
?>
