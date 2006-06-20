<? 
/*
* 	FALTA:
* 		- Abstraer las tablas fijas para agregar facilmente y usar en otros elementos
* 		- Pasar a funcion para consumirlo en otros lados
* 		- Mostrar accesos directos al elemento nuevo
* 		- Asignar un nombre al objeto nuevo
*/

    if($editable = $this->zona->obtener_editable_propagado()){ 
        $this->zona->cargar_editable(); 
        $this->zona->obtener_html_barra_superior(); 

		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;         
		require_once("nucleo/lib/modelo_datos.php");		//Acceso al modelo de datos
		require_once("nucleo/browser/interface/form.php");
		
		$formulario = "duplicador";
		$boton_post = "copiar";
		$boton_post_nombre = "Clonar OBJETO";
	
		//Formulario que dispara el PROCESO
		echo form::abrir($formulario, $this->vinculador->generar_solicitud(null,null,null,true));
		enter();
		ei_centrar( form::submit($boton_post,$boton_post_nombre) );
		echo form::cerrar();

		//*********** COPIAR el OBJETO ***************

		if( ($_SERVER["REQUEST_METHOD"]=="POST") && 
		($_POST[$boton_post]==$boton_post_nombre) )
		//SI hay un POST, y estuvo disparado por este formulario
		{
			//--[ 1 ]-- Recupero datos ACTUALES del OBJETO

			//Para la CABECERA del OBJETO
			$temp = obtener_select_tabla("toba","apex_objeto");
			if( $temp[0] ){
				//SQL que dumpea a la CABECERA de OBJETOS
				$where_base[] = "( objeto = '". $this->zona->editable_info["objeto"]. "')";
				$where_base[] = "( proyecto = '". $this->zona->editable_info["proyecto"]. "')";
				$sql_base = sql_agregar_clausulas_where($temp[1], $where_base);
				//Recupero los datos BASICOS
				$rs = $db["instancia"][apex_db_con]->Execute($sql_base);
				if(!$rs){
					monitor::evento("bug","CLONAR OBJETO: No se genero el recordset. ". $db["instancia"][apex_db_con]->ErrorMsg()." -- SQL: {$sql_base} -- ");
				}
				if($rs->EOF){
					echo ei_mensaje("CLONAR OBJETO: Error al recuperar la informacion del objeto  -- SQL: {$sql_base}");
				}
				$datos_base[0] = current($rs->getArray());

				//---- PARA las dependencias del objeto				

				$deps = obtener_select_tabla("toba","apex_objeto_dependencias");
				$where_deps[] = "( objeto_consumidor = '". $this->zona->editable_info["objeto"]. "')";
				$where_deps[] = "( proyecto = '". $this->zona->editable_info["proyecto"]. "')";
				$sql_deps = sql_agregar_clausulas_where($deps[1], $where_deps);
				//echo $sql_deps;
				$rs = $db["instancia"][apex_db_con]->Execute($sql_deps);
				if(!$rs){
					monitor::evento("bug","CLONAR OBJETO: No se genero el recordset. ". $db["instancia"][apex_db_con]->ErrorMsg()." -- SQL: {$sql_deps} -- ");
				}
				if($rs->EOF){
					echo ei_mensaje("CLONAR OBJETO: Error al recuperar la informacion del objeto  -- SQL: {$sql_deps}");
				}
				$datos_deps = $rs->getArray();
				//----------------------------------
				
				//echo ei_mensaje($sql_base, "SQL BASE");
				//ei_arbol( $datos_base, "DATOS BASE" );
	
				//PARA las TABLAS especificas de la CLASE a la que el OBJETO pertenece
				$plan_especifico = parsear_propiedades( $this->zona->editable_info["clase_plan_sql"] );
				//ei_arbol( $plan_especifico );
				foreach( $plan_especifico as $tabla => $clave )
				{
					$where_especifico[0] = "( $clave = '". $this->zona->editable_info["objeto"]. "')";
					$where_especifico[1] = "( ${clave}_proyecto = '". $this->zona->editable_info["proyecto"]. "')";
					$temp = obtener_select_tabla("toba",$tabla);
					if( $temp[0] ){
						//Armo el SELECT de la tabla asociada
						$sql_especifico[$tabla] = sql_agregar_clausulas_where($temp[1], $where_especifico);
						//Recupero los datos de la TABLA especifica
						$rs = $db["instancia"][apex_db_con]->Execute($sql_especifico[$tabla]);
						if(!$rs){
							monitor::evento("bug","CLONAR OBJETO: No se genero el recordset. ". $db["instancia"][apex_db_con]->ErrorMsg()." -- SQL: {$sql_especifico[$tabla]} -- ");
						}
						if($rs->EOF){
							echo ei_mensaje("CLONAR OBJETO: Error al recuperar la informacion del objeto  -- SQL: {$sql_especifico[$tabla]}");
						}
						$datos_especificos[$tabla] = $rs->getArray();
					}
				}
				//ei_arbol( $sql_especifico, "SQL especificos" );

				//ei_arbol($datos_deps);
				//ei_arbol( $datos_especificos, "DATOS especificos" );

	//----------------------- Levantar datos VIEJOS -------------------------------
	
				//--[ 2 ]-- Recuperar NUEVO ID de la secuencia de OBJETOS
	
				$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
				$sql = "SELECT nextval('apex_objeto_seq'::text);";
				$rs = $db["instancia"][apex_db_con]->Execute($sql);
				if(!$rs || $rs->EOF){
					monitor::evento("bug","NO es posible obtener el ID del objeto NUEVO " .$db["instancia"][apex_db_con]->ErrorMsg());
				}			
				$clon = $rs->fields[0];
				//echo ei_mensaje($clon);
				
				//--[ 3 ]-- Modificar DATOS para crear los NUEVOS REGISTROS
	
				//Modificaciones FIJAS
				$datos_base[0]["objeto"] = $clon;
				$datos_base[0]["creacion"] = null; //QUe use el DEFAULT de la base
				$datos_base[0]["nombre"] = "* COPIA de " . $datos_base[0]["nombre"];

				//Modificar los valores de las dependencias
				foreach( array_keys($datos_deps) as $dep ){
					$datos_deps[$dep]["objeto_consumidor"] = $clon;
				}

				//Tablas ESPECIFICAS
				foreach(array_keys($datos_especificos) as $tabla)
				{
					for($registro=0; $registro<count($datos_especificos[$tabla]); $registro++)
					{
						$clave = $plan_especifico[$tabla];//Recupero la clave de la tabla
						if( isset($datos_especificos[$tabla][$registro][$clave]) ){
							//Cambio el ID por el ID del CLON
							$datos_especificos[$tabla][$registro][$clave]= $clon;
						}
					}
				}
				//ei_arbol( $datos_base, "DATOS basicos" );
				//ei_arbol( $datos_especificos, "DATOS especificos" );
	
				//--[ 4 ]-- Generar INSERTS

				//Tabla cabecera
				foreach( array_keys($datos_base) as $tabla ){
					$sql_inserts[] = sql_array_a_insert("apex_objeto",$datos_base[$tabla]);
				}
				//TABLAS especificas
				foreach(array_keys($datos_especificos) as $tabla){
					for($registro=0; $registro<count($datos_especificos[$tabla]); $registro++){
						$sql_inserts[] = sql_array_a_insert($tabla, $datos_especificos[$tabla][$registro]);
					}
				}
				//Tabla de DEPENDENCIAS
				foreach( array_keys($datos_deps) as $dep ){
					$sql_inserts[] = sql_array_a_insert("apex_objeto_dependencias", $datos_deps[$dep] );
				}

				//ei_arbol($sql_inserts);
				
				//--[ 5 ]-- Ejecutar TRANSACCION			

				$estado = ejecutar_transaccion( $sql_inserts );
				if ($estado[0] ){
					echo ei_mensaje("El objeto Ha sido duplicado CORRECTAMENTE");
					$this->zona->refrescar_listado_editable_apex();
				}else{
					echo ei_mensaje($estado[1]);
					ei_arbol($sql_inserts,"SQL");
				}
				
			}else{
				echo ei_mensaje("Error, no se pude acceder a la informacion del MODELO de DATOS");
			}
		}
        $this->zona->obtener_html_barra_inferior(); 
    }else{ 
        echo ei_mensaje("No se explicito el ELEMENTO a editar","error"); 
    } 
?>