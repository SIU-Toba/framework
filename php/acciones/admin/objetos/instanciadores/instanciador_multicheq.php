<?
	if($editable = $this->zona->obtener_editable_propagado()){
		include_once("nucleo/browser/clases/objeto_ut_multicheq.php");
		$this->zona->cargar_editable();//Cargo el editable de la zona
		$this->zona->obtener_html_barra_superior();
		$multicheq =& new objeto_ut_multicheq($editable,$this);
		$multicheq->inicializar(array("nombre_formulario"=>"prueba"));
		$multicheq->cargar_datos();
		include_once("control_subclase.php");
		enter();
		$multicheq->obtener_html();          
		$multicheq->mostrar_info_proceso();
		//$multicheq->info_definicion();

		$this->zona->obtener_html_barra_inferior();
	}else{
		echo ei_mensaje("El objeto solicitado no existe.","error");
	}
?>