<?
	if($editable = $this->zona->obtener_editable_propagado()){
		$this->zona->cargar_editable();
		$this->zona->obtener_html_barra_superior();
		
		$archivo = $this->zona->editable_info['archivo'];
		$proyecto = $this->zona->editable_info['proyecto'];

		$archivo_real = toba::get_hilo()->obtener_proyecto_path() . "/php/" . $archivo;

		if(file_exists($archivo_real)){

			if($flag=$this->hilo->obtener_parametro("interno")){
				$acceso = array("actividad","interno","objeto","nucleo");
				$param = null;
				$nombre = "Ver interface disponible desde una ACTIVIDAD";
			}else{
				$acceso = array("actividad");
				$param = array("interno"=>1);			
				$nombre = "Ver interface Completa";
			}
			include_once("nucleo/lib/interface/form.php");
			$vinculo = $this->vinculador->generar_solicitud(null,null,$param,true);
			$html = form::button("boton",$nombre,"onclick=\"javascript:window.document.location.href='$vinculo'\"");
			ei_separador($html);
			include_once("nucleo/lib/documentador.php");
			$documentador =& new documentador($archivo_real,$acceso);
			$documentador->procesar();
			$documentador->obtener_html();
			
		}else{
			echo ei_mensaje("ATENCION: el archivo <b>'$archivo_real'</b> no existe.","error");
		}
		
		$this->zona->obtener_html_barra_inferior();
	}else{
		echo ei_mensaje("No se explicito la CLASE a editar","error");
	}
?>