<?
	//Registrar parametros
	$this->registrar_parametros();
	//print_r($this->parametros);
	
	global $ADODB_FETCH_MODE,$db;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

	//-------------------------------------------------------------------------------
	//--[1]--  Selecciono que tablas voy a DUMPEAR  ---------------------------------
	//-------------------------------------------------------------------------------

    $where_filtrar_tablas = array();
    $where_filtrar_registros = array();
    //Tengo que dumpear un proyecto especifico??
	if(isset($this->parametros["-p"])){
		if($this->parametros["-p"]!="VACIO"){
			$where_filtrar_tablas[] = " (proyecto = '".$this->parametros["-p"]."' OR dump = 'multiproyecto')";
		}
	}
	//Tengo que dumpear un subconjunto de tablas??
	if(isset($this->parametros["-t"])){
		if($this->parametros["-t"]!="VACIO"){
			$where_filtrar_tablas[] = " ( tabla ~* '".$this->parametros["-t"]."' )";
		}
	}
	//Tengo que dumpear tablas historicas?? Por defecto no las dumpeo
	if(!isset($this->parametros["-h"])){
		$where_filtrar_tablas[] = " ((historica <> 1) OR (historica IS NULL))";
	}
		
	$sql = "SELECT tabla as nombre, proyecto, dump, dump_where, dump_from FROM toba_mod_datos_tabla " 
            . apex_sql_where . " ORDER BY orden;\n";
	$sql = sql_agregar_clausulas_where($sql, $where_filtrar_tablas);

	//$this->depurar($sql,"Seleccionador de tablas");

	$rs = $db["apl"][apex_db_con]->Execute($sql);
	$tablas = $rs->getArray();
	//$this->depurar($tablas,"Descripcion tablas a dumpear");
	
	//-------------------------------------------------------------------------------
	//--[2]--  Proceso tabla por tabla  ---------------------------------------------
	//-------------------------------------------------------------------------------

	foreach($tablas as $tabla)
	{
		//--[1]-- Selecciono columnas
		$sql = "SELECT COUNT(*) as registros FROM " . $tabla['nombre'] . ";\n";
		//$this->depurar($sql,"Seleccionador de columnas", false);
		//--[2]-- Recupero columnas
    	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		$rs = $db["apl"][apex_db_con]->Execute($sql);
		$registros = $rs->getArray();
		echo $tabla['nombre'] . ": " . $registros[0]["registros"] . "\n";
	}
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	
?>