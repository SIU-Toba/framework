<?
	//$this->obtener_info_objetos();
 	echo "<br>";
	$hoja0 = $this->cargar_objeto("objeto_hoja",0);
	if($hoja0 > -1){
		if($this->objetos[$hoja0]->cargar_datos($where)===true){
			$this->objetos[$hoja0]->generar_html();
		}else{
			$this->objetos[$hoja0]->mostrar_estado();
		}
		//$this->objetos[$abms]->info();
		//dump_session();
	}

 	echo "<br>";
	$hoja1 = $this->cargar_objeto("objeto_hoja",1);
	if($hoja1 > -1){
		if($this->objetos[$hoja1]->cargar_datos($where)===true){
			$this->objetos[$hoja1]->generar_html();
		}else{
			$this->objetos[$hoja1]->mostrar_estado();
		}
		//$this->objetos[$abms]->info();
		//dump_session();
	}
?>