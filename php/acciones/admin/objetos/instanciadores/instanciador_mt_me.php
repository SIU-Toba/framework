<?
	if($editable = $this->zona->obtener_editable_propagado()){
		$this->zona->cargar_editable();//Cargo el editable de la zona
		$this->zona->obtener_html_barra_superior();

		
		require_once("nucleo/browser/clases/objeto_ci_me_tab.php");
		require_once("nucleo/lib/objeto_vacio.php");
		$cn = new objeto_vacio("Controlador de Negocio");
		$mt = new objeto_ci_me_tab($editable);
		$mt->asignar_controlador_negocio( $cn );
		$mt->procesar();
		$mt->obtener_html();
		//$mt->mostrar_memoria();
		enter();

		$this->zona->obtener_html_barra_inferior();
		//dump_session();
	}else{
		echo ei_mensaje("INSTANCIADOR de ABMS: No se explicito el objeto a a cargar","error");
	}
?>