<?
	if($editable = $this->zona->obtener_editable_propagado()){
		$this->zona->cargar_editable();
		$this->zona->obtener_html_barra_superior();
		
		$archivo = $this->zona->editable_info['archivo'];
		$proyecto = $this->zona->editable_info['proyecto'];

		if($proyecto == "toba"){
			$archivo_real = $_SESSION["path_php"] . "/" . $archivo;
		}else{
			$archivo_real = $_SESSION["path"] . "/proyectos/$proyecto/php/" . $archivo;
		}
		if(file_exists($archivo_real)){
			ei_separador("ARCHIVO: ". $archivo);
			highlight_file($archivo_real);
		}else{
			echo ei_mensaje("ATENCION: el archivo <b>'$archivo_real'</b> no existe.","error");
		}
		
		$this->zona->obtener_html_barra_inferior();
	}else{
		echo ei_mensaje("No se explicito la CLASE a editar","error");
	}
?>
