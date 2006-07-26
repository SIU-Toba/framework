<?
	if($editable = $this->zona->get_editable()){
	//--> Estoy navegando la ZONA con un editable...
		$this->zona->obtener_html_barra_superior();

		$abms = $this->cargar_objeto("objeto_mt_abms",0);
		if($abms > -1){
			$cuadro = $this->cargar_objeto("objeto_cuadro",0);
			if($cuadro > -1){
				$this->objetos[$abms]->procesar();
				$cargar_ef = array( "objeto_proyecto"=>$editable[0],
									"objeto"=>$editable[1]);
				$this->objetos[$abms]->cargar_estado_ef($cargar_ef);
				$this->objetos[$cuadro]->cargar_datos(
						array(	"objeto_proyecto = '".$editable[0]."'",
									"objeto = '".$editable[1]."'")
						);
				enter();
				$this->objetos[$cuadro]->obtener_html();
				$this->objetos[$abms]->obtener_html();
			}
		}
		$this->zona->obtener_html_barra_inferior();
	}else{
		echo ei_mensaje("No se especifico que EDITABLE utilizar");
	}
?>