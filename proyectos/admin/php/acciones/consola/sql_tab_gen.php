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
    $where_filtrar_tablas = array();
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
		
	$sql = "SELECT tabla as nombre, proyecto, dump, dump_where, dump_from FROM apex_mod_datos_tabla " 
            . apex_sql_where . " ORDER BY orden DESC;\n";
	$sql = sql_agregar_clausulas_where($sql, $where_filtrar_tablas);
	//$this->depurar($sql,"Seleccionador de tablas");
	//Busco las tablas que voy a dumpear
	$rs = $db["instancia"][apex_db_con]->Execute($sql);
	if(!$rs){
		//Error, no se pudo generar la lista de tablas a dumpear
		fwrite(STDERR, "ERROR: No se pudo obtener la lista de tablas a dumpear (No se genero el RECORSET)\n");
		exit(4);
	}elseif($rs->EOF){
		//La tabla no tenia datos
		fwrite(STDERR, "ERROR: No se pudo obtener la lista de tablas a dumpear (No hay registros)\n");
		exit(4);
	}else{
		//La lista de tablas se genero correctamente
		$tablas = $rs->getArray();
		//$this->depurar($tablas,"Descripcion tablas a dumpear");
		//-------------------------------------------------------------------------------
		//--[2]--  Proceso tabla por tabla  ---------------------------------------------
		//-------------------------------------------------------------------------------
		foreach($tablas as $posicion_tabla => $tabla)
		{
	        //SI si se solicito un proyecto y la tabl a es multiproyecto,
	        //Tengo que agregarle un WHERE al SQL que recupera datos
	        $where_filtrar_registros=array();
	        $from_filtrar_registros=array();
	        if($tabla['dump']=="multiproyecto"){
	        	if(isset($this->parametros["-p"])){
			        if($this->parametros["-p"]!="VACIO"){
	                    //WHERE
	                    if(trim($tabla['dump_where'])==""){
							//Por defecto se utiliza la columna 'proyecto' de la tabla
	               			$where_filtrar_registros[] = "(proyecto = '".$this->parametros["-p"]."')";
	                    }else{
	               			$where_filtrar_registros[] = ereg_replace("%%",$this->parametros["-p"],$tabla['dump_where']);
	                    }
	                    //FROM
	                    if(trim($tabla['dump_from'])!="") $from_filtrar_registros[] = $tabla['dump_from'];
			        }
	        	}
	        }
			//Elijo el tipo de SQL que quiero generar. Esto hay que arreglarlo			
			if(isset($this->parametros["-x"])){
				if($this->parametros["-x"]!="VACIO"){
					switch($this->parametros["-x"]){
						case "del":
							$sql = "DELETE FROM {$tabla['nombre']} ". apex_sql_where . ";";
							break;
						case "del_full":
							$sql = "DELETE FROM {$tabla['nombre']} ;";
							break;
						case "drop":
							$sql = "DROP TABLE {$tabla['nombre']} ;";
							break;
						case "vac":
							$sql = "VACUUM VACUUM FULL ANALYSE {$tabla['nombre']};";
							break;
					}
				}
			}else{
				$sql = "DELETE FROM {$tabla['nombre']} ". apex_sql_where . ";";
			}
			//Parche temporal!!
			$where = null;
			foreach($where_filtrar_registros as $w){
				$where[] = ereg_replace(" dd", $tabla['nombre'],$w);
			}

			$sql = sql_agregar_clausulas_where($sql, $where);
			echo $sql . "\n";
		}
	}
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
?>