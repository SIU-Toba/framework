<?
	if($editable = $this->zona->obtener_editable_propagado()){
		$this->zona->cargar_editable();
		$this->zona->obtener_html_barra_superior();
		
		
		include_once("nucleo/lib/elemento_toba.php");
		$elemento = new elemento_toba_item();
		$elemento->cargar_db($editable[0], $editable[1]);
		
		$sqls = $elemento->exportar_sql_insert();
		
		foreach($sqls as $sql)
		{
			pre( $sql );
		}
		
		$this->zona->obtener_html_barra_inferior();
	}else{
		echo ei_mensaje("No se explicito el ELEMENTO a editar","error");
	}

?>