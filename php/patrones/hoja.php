<?
	//$this->obtener_info_objetos();
 	enter();
	$hoja0 = $this->cargar_objeto("objeto_hoja",0);
	if($hoja0 > -1){
		if($this->objetos[$hoja0]->cargar_datos($where)===true){
			$this->objetos[$hoja0]->obtener_html();
		}else{
			echo ei_mensaje("La consulta no devolvio datos");
		}
		//$this->objetos[$abms]->info();
		//dump_session();
	}

?>