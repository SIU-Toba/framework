<?
include_once("nucleo/consola/emular_web.php");
include_once("nucleo/lib/buffer_i.php");

/*
	Atencion: los campos que son secuencias no tienen que aparecer 
			en las entradas no_duplicado y no_nulo.
*/

	$this->registrar_parametros();
	//Elijo FUENTE. Si la fuente es de un proyecto no toba,
	//debe expresarse: "proyecto,fuente"
	if(isset($this->parametros["-f"])){
		if(ereg(",",$this->parametros["-f"])!==false){
			$x = explode(",",$this->parametros["-f"]);
			$fuente = $x[1];		
			$fuente_proyecto = $x[0];
		}else{
			$fuente = $this->parametros["-f"];		
			$fuente_proyecto = null;
		}
	}else{
		$fuente = "instancia";
		$fuente_proyecto = null;
	}
	
	//Elijo prefijo TABLA
	if(isset($this->parametros["-t"])){
		$tabla = $this->parametros["-t"];
	}else{
		echo "Es necesario explicitar un prefijo '-t'\n";
		exit(0);
	}

	abrir_fuente_datos($fuente, $fuente_proyecto);

	$tablas = $db[$fuente][apex_db]->obtener_tablas_prefijo($tabla);
	if(count($tablas)==0){
		echo "No existen tablas con ese prefijo";
		exit(0);
	}
	
	foreach($tablas as $tabla_x)
	{
		//1)Busco la definicion
		$tabla = $tabla_x['relname'];
		$metadatos = $db[$fuente][apex_db]->obtener_metadatos( $tabla );
		foreach($metadatos as $columnas){
			foreach($columnas as $columna){
				if(isset($columna['default'])){
					if(preg_match("/nextval/",$columna['default'])){
						if(preg_match(".*?(\"[^"]*\").*", $columna['default'])){
							echo "TABLA: $tabla COLUMNA: {$columna['columna']} SEQ: {$columna['default']}\n";
						}		
					}			
				}
			}
		}
	}


?>