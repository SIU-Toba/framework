<?
	$cuadro = $this->cargar_objeto("objeto_cuadro",0);
	if($cuadro > -1)
	{
		$this->objetos[$cuadro]->cargar_datos();
		enter();
		//echo $this->objetos[$cuadro]->obtener_sql();
		$this->objetos[$cuadro]->obtener_html();
		enter();
	}else{
		echo ei_mensaje("No se pudo crear la lista");
	}
?>