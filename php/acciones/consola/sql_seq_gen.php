<?
	//Registrar parametros
	$this->registrar_parametros();
	
	//print_r($this->parametros);
	//exit(2);
	
	//En este ARRAY guardo errores generados durante el dumpeo
	$errores = array();
	$indice_error = 0;
	
	//Activo el modo VERBOSE
	if(isset($this->parametros["-v"])){
		$mostrar_avisos = true;
	}else{
		$mostrar_avisos = false;
	}

	global $ADODB_FETCH_MODE,$db;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

	//-------------------------------------------------------------------------------
	//--[1]--  Selecciono que tablas voy a UTILIZAR  --------------------------------
	//-------------------------------------------------------------------------------
    $where_filtrar_seq = array();
    //Tengo que dumpear un proyecto especifico??
	if(isset($this->parametros["-p"])){
		if($this->parametros["-p"]!="VACIO"){
			$where_filtrar_seq[] = " (proyecto = '".$this->parametros["-p"]."' OR dump = 'multiproyecto')";
		}
	}
		
	$sql = "SELECT secuencia, proyecto FROM apex_mod_datos_secuencia " 
            . apex_sql_where . ";\n";
	$sql = sql_agregar_clausulas_where($sql, $where_filtrar_seq);
	//$this->depurar($sql,"Seleccionador de tablas");
	//Busco las tablas que voy a dumpear
	$rs = $db["instancia"][apex_db_con]->Execute($sql);
	if(!$rs){
		//Error, no se pudo generar la lista de tablas a dumpear
		fwrite(STDERR, "ERROR: No se pudo obtener la lista de SECUENCIAS a dumpear (No se genero el RECORSET)\n");
		exit(4);
	}elseif($rs->EOF){
		//La tabla no tenia datos
		fwrite(STDERR, "ERROR: No se pudo obtener la lista de SECUENCIAS a dumpear (No hay registros)\n");
		exit(4);
	}else{
		//La lista de tablas se genero correctamente
		$secuencias = $rs->getArray();
		//$this->depurar($tablas,"Descripcion tablas a dumpear");
		//-------------------------------------------------------------------------------
		//--[2]--  Proceso tabla por tabla  ---------------------------------------------
		//-------------------------------------------------------------------------------
		$sql = "";
		foreach($secuencias as $secuencia)
		{
			$sql .= "DROP SEQUENCE {$secuencia['secuencia']};\n";
		}
		echo $sql . "\n";
	}
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
?>