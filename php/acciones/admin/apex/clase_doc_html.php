<?
	if($editable = $this->zona->obtener_editable_propagado()){
		$this->zona->cargar_editable();
		$this->zona->obtener_html_barra_superior();
		
		$archivo = $this->zona->editable_info['archivo'];
		$proyecto = $this->zona->editable_info['proyecto'];

		if($proyecto == "toba"){

			//Documentacion para el proyecto TOBA
			//Diagrama de CLASES
			if(trim($this->zona->editable_info["doc_clase"])!=""){
				ei_separador("Clases");
				ei_centrar(recurso::imagen_apl($this->zona->editable_info["doc_clase"],true));
			}

			//Diagrama de E-R
			if(trim($this->zona->editable_info["doc_clase"])!=""){
				ei_separador("Tablas");
				ei_centrar(recurso::imagen_apl($this->zona->editable_info["doc_db"],true));
			}

			// SQL de creacion.
			if(trim($this->zona->editable_info["doc_sql"])!=""){
				ei_separador("SQL");
				foreach(explode(",",$this->zona->editable_info["doc_sql"]) as $archivo)
				{
					$sql = $_SESSION["path"] ."/". trim($archivo);
					if(is_file($sql))
					{
						$f = fopen ($sql, "r");
						$contents = fread ($f, filesize ($sql));
						fclose ($f);
						echo "<PRE class='texto-ss'>ARCHIVO: $archivo\n".date("F j, Y, g:i a")."\n\n$contents</PRE>";
					}else{
						echo ei_mensaje("No se ha especificado el SQL de creacion.");
					}
				}
			}
		}

		$this->zona->obtener_html_barra_inferior();
	}else{
		echo ei_mensaje("No se explicito la CLASE a editar","error");
	}
?>