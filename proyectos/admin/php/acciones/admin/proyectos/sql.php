<?php
	if($editable = $this->zona->obtener_editable_propagado()){
		$this->zona->cargar_editable();
		$this->zona->obtener_html_barra_superior();
		
		echo ei_mensaje("Esta funcionalidad no esta terminada");

		require_once("nucleo/lib/modelo_datos.php");
		//ei_arbol( obtener_columnas_tabla('admin',"apex_item") ,"TEST");
		//ei_arbol( obtener_tablas("agentes", null, 1) ,"TEST");
		//ei_arbol( obtener_tablas("agentes") ,"TEST");
		//ei_arbol( obtener_select_tabla('admin',"apex_item"), "SQL");
		generar_dump("agentes");
		
		$this->zona->obtener_html_barra_inferior();
	}else{
		echo ei_mensaje("No se explicito el ELEMENTO a editar","error");
	}
?>