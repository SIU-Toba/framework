<?php
//-------------------------------------------------------------------------------------
//--------------------------- Funcionalidad generica SQL ------------------------------
//-------------------------------------------------------------------------------------

	function sql_agregar_clausulas_where($sql,$clausulas_where=null)
/*
 	@@acceso: actividad
	@@desc: Concatena sentencias a la clausula WHERE de un SQL (Utilizando un AND)
	@@param: string | cadena SQL que hay que editar. El lugar de insersion  debe estar marcado con la constante 'apex_sql_where' (%w%)
	@@param: array | lista de sentencias a acoplar
	@@retorno: string | Sentencia SQL modificada
	@@pendiente: Mira la existencia general de un WHERE y no del pedazo anterior de %w%
*/	
	{
	    if(preg_match("/\s+where\s+/i",$sql)){
	        //<MIRAR> Esto falla en SELECT con subquerys que no tienen WHERE en el cuerpo
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
//-------------------------------------------------------------------------------------
	
	function sql_agregar_tablas_from($sql,$tablas_from=null)
/*
 	@@acceso: actividad
	@@desc: Concatena tablas a la clausula FROM de un SQL
	@@param: string | cadena SQL que hay que editar. El lugar de insersion  debe estar marcado con la constante 'apex_sql_from' (%f%) al final de la clausula
	@@param: array | lista de tablas a acoplar
	@@retorno: string | Sentencia SQL modificada
*/	
	{
	    $reemplazo = "";
	    if(is_array($tablas_from)){
			if(count($tablas_from)>0){
		        $reemplazo = ", " . implode(", ",$tablas_from);
			}
	    }
	    return ereg_replace(apex_sql_from,$reemplazo,$sql);
	}
//-------------------------------------------------------------------------------------
	
	function sql_array_a_insert($tabla, $datos)
/*
 	@@acceso: actividad
	@@desc: Convierte un array asociativo en una sentencia de INSERT
	@@param: string | tabla sobre la que se realiza el INSERT
	@@param: array | Array asociativo con el formato 'columna' => 'valor'
	@@retorno: string | Sentencia SQL modificada
*/
	{
		if(!is_array($datos)){
			throw new excepcion_toba("Los datos tienen que ser un array");	
		}
		foreach(array_keys($datos) as $columna){
			if(is_null( $datos[$columna] )){
				$datos[$columna] = "%%NULL%%";//Identifico los nulos
			}else{
				$datos[$columna] = addslashes($datos[$columna]);//Escapo caracteres
			}
		}
		$sql = "INSERT INTO $tabla (" . implode(", ",array_keys($datos)) . ")". 
				" VALUES ('" . implode("', '",$datos) . "');\n";
		$sql = ereg_replace("'%%NULL%%'","NULL",$sql);//Formateo los nulos
		return $sql;	
	}
//-------------------------------------------------------------------------------------

	function filtrar_sql($sentencia)
/*
 	@@acceso: actividad
	@@desc: Saca construcciones SQL de un STRING (necesario para concatenar el texto del usuario en el WHERE de un SQL)
*/
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
//-------------------------------------------------------------------------------------
?>