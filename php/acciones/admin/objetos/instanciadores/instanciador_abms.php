<?
	if($editable = $this->zona->obtener_editable_propagado()){
		include_once("nucleo/browser/clases/objeto_mt_s_abm.php");
		$this->zona->cargar_editable();//Cargo el editable de la zona
		$this->zona->obtener_html_barra_superior();

		include_once("control_subclase.php");

		$abms =& new objeto_mt_abms($editable,$this);
		$abms->obtener_html();
		$this->zona->obtener_html_barra_inferior();
		//dump_session();
	}else{
		echo ei_mensaje("INSTANCIADOR de ABMS: No se explicito el objeto a a cargar","error");
	}
?>