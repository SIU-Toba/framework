<?php

	global $argv;
    //print_r($argv);exit();//PROBAR ARGUMENTOS
	//echo getcwd()."\n";	
	
	$tabla_orden = 1;	//Tabla que se esta procesando
	$tabla_actual = 1;
	$secuencia_orden = 1;
	$secuencia_actual = 1;
	$secuencia_ultima_procesada = 0;
	
    //Saco los primeros  argumentos.
    //Esto es para pasar como argumento 1 una cadena expansible por wildcars (ej: 'pgsql_a*.sql')
	$parametros = $argv;
	for($a=0;$a<5;$a++){
	    array_shift($parametros);
	}
	//print_r($argv);exit();

	$archivos_procesados = 0;
	$cantidad_tablas_total = 0;
	$cantidad_secuencias_total = 0;
	$inserts_total = 0;
	    
    foreach($parametros as $archivo) //Recorro la lista de archivos expandida por el shell
    {
    	//Intento abrir el archivo
		$fd = @fopen ($archivo, "r");
		if(!is_resource($fd)){
			fwrite(STDERR, "ERROR: '$archivo' no es un archivo valido\n" );
		}
		else
		{
			$archivos_procesados++;
			$cantidad_tablas = 0;
			$cantidad_secuencias = 0;
			
	        echo "\n--######################################################################################\n";
    	    echo "--####\n";
	        echo "--####    ARCHIVO:  $archivo\n";
    	    echo "--####\n";
	        echo "--######################################################################################\n\n";
    		$tablas = array();
			$secuencias = array();
			
	        //-------------------------------------------------------------------------------------
    		//--[1]------------------ Armo un ARRAY con la informacion de las TABLAS --------------
	        //-------------------------------------------------------------------------------------

	    	//Recorro el archivo
		    //while ((!feof ($fd)) && $tabla_actual < 3 )
		    while (!feof ($fd))
	    	{
	        	$buffer = fgets($fd, 4096);
	    		//------- Entro en una tabla
		    	if(preg_match("/^create sequence/i",$buffer))
	        	{
			    	$secuencia_actual = $secuencia_orden;
			        $temp = preg_split("/\s+/",$buffer);
		    		$secuencias[$secuencia_actual]["nombre"] = $temp[2];
			    	$secuencia_orden++;
	    		}
	    		//------- Entro en una tabla
		    	if(preg_match("/^create table/i",$buffer))
	        	{
			    	$tabla_actual = $tabla_orden;
			        $temp = preg_split("/\s+/",$buffer);
	    			$tablas[$tabla_actual]["script"] = $archivo;
		    		$tablas[$tabla_actual]["nombre"] = $temp[2];
			    	$tabla_orden++;
	    		}
		    	//------- Entre en una propiedad
			    if(preg_match("/^--:/",$buffer))
	    		{
		    	    $temp = preg_split("/(\s*):(\s*)/",$buffer);
			    	$tablas[$tabla_actual][trim($temp[1])]=trim($temp[2]);
					//En las secuencias solo me interesa la propiedad proyecto
					if(trim($temp[1])=="proyecto"){
						if( ($secuencia_ultima_procesada != $secuencia_actual) &&
							($secuencia_orden != 1) ){
				    		$secuencias[$secuencia_actual]["proyecto"] = trim($temp[2]);
							$secuencia_ultima_procesada++;
						}
					}
	    		}
		    	//------- Entre en una columna
			    if(preg_match("/^\s*?\w+\s*?.*NULL/",$buffer))
	    		{
	                //echo $buffer;
		    	    $temp = preg_split("/\s+|\t/",$buffer);
	                //print_r($temp);
	                $columna = $temp[1];
	                $columna_definicion = addslashes(trim(preg_replace("/^\s*?\w+\s*?/","",$buffer)));
			    	$tablas[$tabla_actual]["columnas"][$columna] = $columna_definicion;
	    		}
		    }
	    	fclose ($fd);
	        //print_r($tablas);
	        //exit();

	        //-------------------------------------------------------------------------------------
	        //--[2]---------------- Armo los INSERTS ----------------------------------------------
	        //-------------------------------------------------------------------------------------

			// A -- Genero el SQL de la TABLA
		    $insert_tabla = "INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ";
	        $insert_columna = "INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ";
	        $sql = "";
	        foreach($tablas as $orden => $tabla)
	        {
	            //Creo el INSERT con la definicion de la tabla
	            $sql_proyecto = (isset($tabla["proyecto"])) ? "'" .$tabla["proyecto"] . "'" : "NULL";
	            $sql_tabla = (isset($tabla["nombre"])) ? "'" .$tabla["nombre"] . "'" : "NULL";
	            $sql_script = (isset($tabla["script"])) ? "'" .$tabla["script"] . "'" : "NULL";
	            $sql_orden = $orden;
	            $sql_dump = (isset($tabla["dump"])) ? "'" .$tabla["dump"] . "'" : "NULL";
	            $sql_dump_from = (isset($tabla["dump_from"])) ? "'" .$tabla["dump_from"] . "'" : "NULL";
	            $sql_dump_where = (isset($tabla["dump_where"])) ? "'" .addslashes($tabla["dump_where"]) . "'" : "NULL";
	            $sql_dump_order_by = (isset($tabla["dump_order_by"])) ? "'" .$tabla["dump_order_by"] . "'" : "NULL";
	            $sql_dump_order_by_from = (isset($tabla["dump_order_by_from"])) ? "'" .$tabla["dump_order_by_from"] . "'" : "NULL";
	            $sql_dump_order_by_where = (isset($tabla["dump_order_by_where"])) ? "'" .addslashes($tabla["dump_order_by_where"]) . "'" : "NULL";
	            $sql_desc = (isset($tabla["desc"])) ? "'" .$tabla["desc"] . "'" : "NULL";
	            $sql_version = (isset($tabla["version"])) ? "'" .$tabla["version"] . "'" : "NULL";
	            $sql_historica = (isset($tabla["historica"])) ? "'" .$tabla["historica"] . "'" : "NULL";
	            $sql_instancia = (isset($tabla["instancia"])) ? "'" .$tabla["instancia"] . "'" : "NULL";
	
	            $sql .= "------  $sql_tabla  -----------------------------------\n";
	            $sql .= $insert_tabla . "($sql_proyecto, $sql_tabla, $sql_script, $sql_orden, $sql_dump, $sql_dump_from, $sql_dump_where, $sql_dump_order_by, $sql_dump_order_by_from, $sql_dump_order_by_where, $sql_desc, $sql_version, $sql_historica, $sql_instancia);\n";
	
	            //Creo el INSERT con la definicion de las COLUMNAS
	            $posicion_columna = 1;
	            foreach($tabla["columnas"] as $col_nombre => $col_definicion)
	            {
	                $sql .= $insert_columna . "($sql_proyecto, $sql_tabla, '$col_nombre', $posicion_columna, '$col_definicion');\n";
					$posicion_columna++;
					$inserts_total++;
	            }
	            $sql .= "\n";
	        }
	        echo $sql;    

			// B -- Genero el SQL de las SECUENCIAS
			//print_r($secuencias);
		    $insert_secuencia = "INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ";
	        $sql = "";
	        if(count($secuencias)>0){
				//print_r($secuencias);
				foreach($secuencias as $secuencia)
		        {	
					$sql .= $insert_secuencia . "('" . $secuencia["proyecto"] . "','" . $secuencia["nombre"] . "');\n";
					$inserts_total++;
				}
		        echo $sql;    
			}
			//Muestro el resultado del parseo del archivo (TABLAS)
			$cantidad_tablas = count($tablas);
			$cantidad_secuencias = count($secuencias);
			fwrite(STDERR, "\nArchivo: $archivo - $cantidad_tablas tablas, $cantidad_secuencias secuencias\n" );
			$cantidad_tablas_total += $cantidad_tablas;
			$cantidad_secuencias_total += $cantidad_secuencias;
		}
    }
	fwrite(STDERR, "\nArchivos procesados: $archivos_procesados\n" );
	fwrite(STDERR, "Tablas escaneadas: $cantidad_tablas_total\n" );
	fwrite(STDERR, "Secuencias escaneadas: $cantidad_secuencias_total\n" );
	fwrite(STDERR, "Inserts SQL generados: $inserts_total\n" );
	
?>
