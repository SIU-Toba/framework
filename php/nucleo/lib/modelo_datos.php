<?php
//Funciones que permiten construir un DUMPEADOR en base a los metadatos de "apex_mod_datos_*"

	function generar_dump( $proyecto=null, $reg_ex_nombre=null, $historicas=null )
	//Genera un DUMP
	{
		$tablas = obtener_tablas( $proyecto,$reg_ex_nombre,$historicas);
		if($tablas[0]){
			foreach($tablas[1] as $tabla)
			//Proceso cada una de las tablas
			{
				$sql_dump = obtener_sql_tabla($tabla["proyecto"], $tabla["nombre"] );
				if($sql_dump[0]){
					//Preparo el filtrado de registros
					echo $sql_dump[1] . "<br>";

					echo $sql_dump[2] . "<br>";
					echo "<br><br>";
				}else{
					echo "Error en tabla: " . $tabla["nombre"];
				}
			}
		}else{
			die("Error");
		}
	}
	//---------------------------------------------------------------------------

	function obtener_tablas( $proyecto=null, $reg_ex_nombre=null, $historicas=null )
	//Devuelve el listado de tablas a dumpear con sus caracteristicas
	//En base a los parametros se determina el alcance del DUMP
	{
		global $db, $ADODB_FETCH_MODE;
	   $where_filtrar_tablas = array();
		if(isset($proyecto)){
			$where_filtrar_tablas[] = " (proyecto = '$proyecto' OR dump = 'multiproyecto')";
		}
		//Tengo que dumpear un subconjunto de tablas??
		if(isset($reg_ex_nombre)){
			$where_filtrar_tablas[] = " ( tabla ~* '$reg_ex_nombre' )";
		}
		//Tengo que dumpear tablas historicas?? Por defecto no las dumpeo
		if(!isset($historicas)){
			$where_filtrar_tablas[] = " ((historica <> 1) OR (historica IS NULL))";
		}
		$sql = "SELECT tabla as nombre, proyecto, dump, dump_where, dump_from FROM apex_mod_datos_tabla " 
	            . apex_sql_where . " ORDER BY orden;\n";
		$sql = sql_agregar_clausulas_where($sql, $where_filtrar_tablas);
		$rs = $db["instancia"][apex_db_con]->Execute($sql);
		if(!$rs){
			return array(0,"No se genero el Recordset");
		}elseif($rs->EOF){
			return array(0,"No existen columnas para la tabla seleccionada");
		}else{
			return array(1, $rs->getArray());
		}
	}
	//---------------------------------------------------------------------------

	function obtener_sql_tabla($tabla_proyecto, $tabla, $dump=true)
	//Devuelve un SELECT completo a una TABLA
	{
		$columnas = obtener_columnas_tabla($tabla_proyecto, $tabla, $dump);
		if( $columnas[0] ){
			//SQL de SELECCION de DATOS
			$sql_select = "SELECT xx." . implode(", xx.",$columnas[1]) . " FROM " . 
	    	            $tabla . " xx " . apex_sql_from . " " . apex_sql_where .";\n";
			//SQL de INSERSION
			$sql_insert = "INSERT INTO $tabla (" . implode(", ",$columnas[1]) . ") VALUES ";
			return array(1, $sql_select, $sql_insert);
		}else{
			return array(0, "Error generando el SQL de la TABLA $tabla. ( ".$columnas[1]." )");
		}
	}
	//---------------------------------------------------------------------------

	function obtener_select_tabla($tabla_proyecto, $tabla, $dump=true)
	//Devuelve un SELECT completo a una TABLA
	{
		$columnas = obtener_columnas_tabla($tabla_proyecto, $tabla, $dump);
		if( $columnas[0] ){
			//SQL de SELECCION de DATOS
			$sql_select = "SELECT " . implode(", ",$columnas[1]) . " FROM " . 
	    	            $tabla  . apex_sql_where .";\n";
			return array(1, $sql_select);
		}else{
			return array(0, "Error generando el SQL de la TABLA $tabla. ( ".$columnas[1]." )");
		}
	}
	//---------------------------------------------------------------------------

	function obtener_columnas_tabla($tabla_proyecto, $tabla, $dump=true)
	//Devuelve las columnas de una tabla
	{
		global $db, $ADODB_FETCH_MODE;
		$where = ($dump) ? " AND (dump = 1) " : "";
		$sql = "SELECT columna FROM apex_mod_datos_tabla_columna " .
				"WHERE  ( (tabla = '$tabla') $where ".
				"AND (tabla_proyecto = '$tabla_proyecto') ) ORDER BY orden;\n";
    	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		$rs = $db["instancia"][apex_db_con]->Execute($sql);
		if(!$rs){
			return array(0,"No se genero el Recordset");
		}elseif($rs->EOF){
			return array(0,"No existen columnas para la tabla seleccionada");
		}else{
			$temp = $rs->getArray();
			for($a=0; $a<count($temp);$a++){
				$columnas[] = $temp[$a]["columna"];
			}
			return array(1, $columnas);
		}
	}
	//---------------------------------------------------------------------------

?>