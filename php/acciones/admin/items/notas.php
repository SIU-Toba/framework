<?
	if($editable = $this->zona->obtener_editable_propagado()){
	//--> Estoy navegando la ZONA con un editable...
		$this->zona->cargar_editable();//Cargo el editable de la zona
		$this->zona->obtener_html_barra_superior();

		$abms = $this->cargar_objeto("objeto_mt_abms",0);
		if($abms > -1){
			$listado_notas = $this->cargar_objeto("objeto_lista",0);
			if($listado_notas > -1){
				$this->objetos[$abms]->procesar();
				$cargar_ef = array( "item_proyecto"=>$editable[0],
									"item"=>$editable[1]);
				$this->objetos[$abms]->cargar_estado_ef($cargar_ef);
				$this->objetos[$listado_notas]->cargar_datos(
						array("(item_proyecto = '".$editable[0]."') ",
								"(item = '".$editable[1]."') ") );
				$this->objetos[$abms]->obtener_html();
				$this->objetos[$listado_notas]->obtener_html();
			}
		}
		echo "<br>";
		$this->zona->obtener_html_barra_inferior();
	}else{
		echo ei_mensaje("No se especifico que EDITABLE utilizar");
	}
?>