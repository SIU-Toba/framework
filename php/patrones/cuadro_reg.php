<?
	$cuadro = $this->cargar_objeto("objeto_cuadro_reg",0);
	if($cuadro > -1)
	{
		$this->objetos[$cuadro]->cargar_datos();
		enter();
		$this->objetos[$cuadro]->obtener_html();
		enter();
	}else{
		echo ei_mensaje("No se pudo crear la lista");
	}
?>