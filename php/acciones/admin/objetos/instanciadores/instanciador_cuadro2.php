<?
	if($editable = $this->zona->obtener_editable_propagado()){

		include_once("nucleo/browser/clases/objeto_ei_cuadro.php");
		$this->zona->cargar_editable();//Cargo el editable de la zona
		$this->zona->obtener_html_barra_superior();
		$cuadro =& new objeto_ei_cuadro($editable,$this);
		$cuadro->cargar_datos();
		//$cuadro->info_definicion();
		//$cuadro->info_estado();
		enter();
		$cuadro->obtener_html();
		//$cuadro->dump_memoria();

		$this->zona->obtener_html_barra_inferior();
		//dump_session();

	}else{
		echo ei_mensaje("INSTANCIADOR de CUADROS: No se explicito el objeto a a cargar","error");
	}
?>