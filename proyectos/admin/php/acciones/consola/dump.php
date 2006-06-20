<?
	//Registrar parametros
	$this->registrar_parametros();
	//print_r($this->parametros);

	//En este ARRAY guardo errores generados durante el dumpeo
	$errores = array();
	$indice_error = 0;
	
	//Cantidad de registros dumpeados
	$cantidad_registros_total = 0;
		
	//Activo el modo VERBOSE
	if(isset($this->parametros["-v"])){
		$mostrar_avisos = true;
	}else{
		$mostrar_avisos = false;
	}

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
	//Tengo que dumpear tablas historicas?? Si se pide explicitamente, no se dumpea
	if(!isset($this->parametros["-h"])){
		$where_filtrar_tablas[] = " ((historica <> 1) OR (historica IS NULL))";
	}
		
	//Filtrar las tablas que son de la INSTANCIA
	if(isset($this->parametros["-i"])){
		$where_filtrar_tablas[] = " (instancia = 1)";
	}
	
	//Filtrar las tablas que NO son de la INSTANCIA
	if(isset($this->parametros["-ni"])){
		$where_filtrar_tablas[] = "  ((instancia <> 1) OR (instancia IS NULL))";
	}
	
	$sql = "SELECT tabla as nombre, proyecto, dump, dump_where, dump_from, dump_order_by FROM apex_mod_datos_tabla " 
            . apex_sql_where . " ORDER BY orden;\n";
	$sql = sql_agregar_clausulas_where($sql, $where_filtrar_tablas);
	//$this->depurar($sql,"Seleccionador de tablas");
	//************** Busco las tablas que voy a dumpear ***************
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
			//--[1]-- Selecciono columnas
			$sql = "SELECT columna FROM apex_mod_datos_tabla_columna " .
					"WHERE  ( (tabla = '" . $tabla['nombre'] . "') AND (dump = 1) ".
					"AND (tabla_proyecto = '" . $tabla['proyecto'] . "') ) ORDER BY orden;\n";
			$this->depurar($sql,"Seleccionador de columnas", false);
			//--[2]-- Recupero columnas
	    	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
			$rs = $db["instancia"][apex_db_con]->Execute($sql);
			if(!$rs){
				//Error, no puedo acceder a las columnas!
				fwrite(STDERR, "*[$posicion_tabla]  ERROR! - {$tabla['nombre']} - No se puede acceder a las columnas. Verificar METADATOS\n");
				$errores[$indice_error]["tabla"] = $tabla['nombre'];
				$errores[$indice_error]["descripcion"] = "Error en el SQL que recupera las columnas de la tabla";
				$errores[$indice_error]["sql"] = $sql;
				$errores[$indice_error]["sql_error"] = $db["instancia"][apex_db_con]->ErrorMsg();
				$indice_error++;
			}elseif($rs->EOF){
				//No hay columnas defiidas!
				fwrite(STDERR, "*[$posicion_tabla] ERROR! - {$tabla['nombre']} - No hay columnas. Verificar METADATOS\n");
				$errores[$indice_error]["tabla"] = $tabla['nombre'];
				$errores[$indice_error]["descripcion"] = "No hay columnas definidas para la tabla";
				$errores[$indice_error]["sql"] = $sql;
				$errores[$indice_error]["sql_error"] = "\n";
				$indice_error++;
			}else{
				//Ya tengo las columnas de la tabla, ahora hay que armar la consulta que dumpea los registros
				$columnas = $rs->getArray();
		        $col = array();
		        foreach($columnas as $columna){
		            $col[] = $columna['columna'];
		        }
				//--[3]-- Armo el SQL que recupera los datos a DUMPEAR
					//ORDER BY (Orden de los registros del DUMP)
					if(trim($tabla['dump_order_by'])!=""){
						$col_order = explode(",",$tabla['dump_order_by']);
						for($a=0; $a<count($col_order);$a++){
							$col_order[$a] = " dd." . trim($col_order[$a]);
						}
						$sql_order_by = " ORDER BY " . implode(", ", $col_order);
					}else{
						$sql_order_by = "";
					}
		        //Base del SQL
				$sql_dump_tabla = "SELECT dd." . implode(", dd.",$col) . " FROM " . 
		                            $tabla['nombre'] . " dd " . apex_sql_from . " " . apex_sql_where . " $sql_order_by ;";
		        //SI si se solicito un proyecto y la tabl a es multiproyecto,
		        //Tengo que agregarle un WHERE al SQL que recupera datos
		        $where_filtrar_registros=array();
		        $from_filtrar_registros=array();
		        if($tabla['dump']=="multiproyecto" || $tabla['dump']=="componente"){
		        	if(isset($this->parametros["-p"])){
				        if($this->parametros["-p"]!="VACIO"){
		                    //WHERE
		                    if(trim($tabla['dump_where'])==""){
		               			$where_filtrar_registros[] = "(proyecto = '".$this->parametros["-p"]."')";
		                    }else{
		               			$where_filtrar_registros[] = ereg_replace("%%",$this->parametros["-p"],$tabla['dump_where']);
		                    }
		                    //FROM
		                    if(trim($tabla['dump_from'])!="") $from_filtrar_registros[] = $tabla['dump_from'];
				        }
		        	}
		        }
				$sql_dump_tabla = sql_agregar_clausulas_where($sql_dump_tabla, $where_filtrar_registros);
				$sql_dump_tabla = sql_agregar_tablas_from($sql_dump_tabla, $from_filtrar_registros);
				$this->depurar($sql_dump_tabla,"DUMP", false);	
	        //--[4]-- Armo el los INSERT SQL que constituyen en DUMP de la tabla
				$sql_insert_base = "INSERT INTO " . $tabla['nombre'] . " (" . implode(", ",$col) . ") VALUES ";
		//		$this->depurar($sql_insert_base,"INSERT", false);		
		    	$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
				// Busco los datos que devuelven la informacion de la tabla
				$rs = $db["instancia"][apex_db_con]->Execute($sql_dump_tabla);
				if(!$rs){
					//Error!
					fwrite(STDERR, "*[$posicion_tabla] ERROR! - {$tabla['nombre']} - No genero un RECORSET. Verificar METADATOS\n");
					$errores[$indice_error]["tabla"] = $tabla['nombre'];
					$errores[$indice_error]["descripcion"] = "El SQL de dumpeo de datos no genero un RECORDSET";
					$errores[$indice_error]["sql"] = $sql;
					$errores[$indice_error]["sql_error"] = $db["instancia"][apex_db_con]->ErrorMsg();
					$indice_error++;
				}elseif($rs->EOF){
					//La tabla no tenia datos
					if($mostrar_avisos){
						fwrite(STDERR, "[$posicion_tabla] AVISO -0- {$tabla['nombre']} - 0 registros \n" );
					}
				}else{
					//La tabla posee datos, genero los INSERT
					$registros = $rs->getArray();
					$cantidad_registros = 0;		//Registros dumpeados para la tabla
	    	        echo "\n-----------  " . $tabla['nombre'] . "  ------------------------\n\n";
	        	    for ($a=0; $a<count($registros);$a++){
	            	    $datos_registro = "";
	                	//Formateo el dato de cada CAMPO
		                for( $b=0; $b<count($registros[$a]);$b++){
	    	                if(is_null($registros[$a][$b])){
	        	                $datos_registro .= "NULL,";
	            	        }else{
	                	        $datos_registro .= "'". addslashes($registros[$a][$b]) ."',";
	                    	}
		                }
	    	            //formateo final...
	        	        $datos_registro = "(" . substr($datos_registro,0,(strlen($datos_registro)-1)) . ");\n";
	            	    //Armo el INSERT y lo imprimo en la SALIDA STANDART
	                	echo $sql_insert_base . $datos_registro;
						$cantidad_registros++;
		            }
					$cantidad_registros_total += $cantidad_registros;
					if($mostrar_avisos){
						fwrite(STDERR, "[$posicion_tabla] AVISO - {$tabla['nombre']} - $cantidad_registros registros\n" );
					}
				}
			}
		}
		$n_err = count($errores);
		fwrite(STDERR, "\nRegistros: $cantidad_registros_total\n" );
		fwrite(STDERR, "Errores: $n_err\n\n" );
		if($n_err > 0){
			fwrite(STDERR, "---------------------------------------------------------------\n\n\n" );
			if($mostrar_avisos){
				for($aa=0; $aa<$n_err; $aa++){
					fwrite(STDERR, "-- ERROR ". ($aa+1) ." ----------------------------------------------------\n\n" );
					fwrite(STDERR, "TABLA: ". $errores[$aa]['tabla'] ."\n");
					fwrite(STDERR, "DESCRICION: ". $errores[$aa]['descripcion'] ."\n");
					fwrite(STDERR, "SQL_ERROR: ". $errores[$aa]['sql_error']);
					fwrite(STDERR, "SQL:\n ". $errores[$aa]['sql'] ."\n\n");
					fwrite(STDERR, "---------------------------------------------------------------\n\n" );
				}
			}
		}
	}
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
?>