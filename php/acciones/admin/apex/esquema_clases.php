<?
	$cuadro = $this->cargar_objeto("objeto_esquema_db",0);
	if($cuadro > -1)
	{
		$this->objetos[$cuadro]->cargar_datos();
		enter();
		$where = array();
		$where[] = "proyecto = '".$this->hilo->obtener_proyecto()."'";
		$this->objetos[$cuadro]->cargar_datos($where);
		$this->objetos[$cuadro]->obtener_html();
		enter();
	}else{
		echo ei_mensaje("No se pudo crear la lista");
	}
?>