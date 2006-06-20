<?
	if($editable = $this->zona->obtener_editable_propagado()){
		$this->zona->cargar_editable();//Cargo el editable de la zona
		$this->zona->obtener_html_barra_superior();
		include_once("nucleo/browser/clases/objeto_filtro.php");
		$filtro =& new objeto_filtro($editable,$this);
		//$filtro->info_definicion();
		//$filtro->info_estado();

        enter();
   	    $filtro->obtener_interface_vertical();
   	    
		//Parametros requiridos no seteados?, error perfil de datos?
		
		$status_filtro = $filtro->validar_estado();
		if($status_filtro[0])
		{
			if ( $temp = $filtro->obtener_where() ) ei_arbol($temp,"\$filtro->obtener_where()");
			if ( $temp = $filtro->obtener_from() ) ei_arbol($temp,"\$filtro->obtener_from()");
			if ( $temp = $filtro->obtener_info() ) ei_arbol($temp,"\$filtro->obtener_info()");
		}else{
			$filtro->mostrar_info_proceso();
		}
		$this->zona->obtener_html_barra_inferior();
 	}else{
		echo ei_mensaje("No se especifico el Identificador del OBJETO");
	}
	//dump_session();
	//$this->hilo->dump_memoria();
?>
