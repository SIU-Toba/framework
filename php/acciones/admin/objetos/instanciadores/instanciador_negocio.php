<?
	if($editable = $this->zona->obtener_editable_propagado()){

		$this->zona->cargar_editable();//Cargo el editable de la zona
		$this->zona->obtener_html_barra_superior();

		include_once("control_subclase.php");

		include_once("nucleo/negocio/objeto_negocio.php");
		$negocio =& new objeto_negocio($editable,$this);
		ei_arbol($negocio->obtener_reglas(),"REGLAS existentes");
		//ei_arbol($negocio->info_estado(),"Estado");
		//ei_arbol($negocio->info_definicion(),"Definicion");
		enter();

		$this->zona->obtener_html_barra_inferior();
		//dump_session();

	}else{
		echo ei_mensaje("INSTANCIADOR de CUADROS: No se explicito el objeto a a cargar","error");
	}
?>