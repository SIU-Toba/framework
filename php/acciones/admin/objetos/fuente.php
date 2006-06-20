<?
	if($this->contexto['cargado_ok']){
		$abms = $this->cargar_objeto("objeto_mt_abms",0);
		if($abms > -1){
			$this->objetos[$abms]->procesar_evento(array($this->contexto['info_elemento']["fuente"]));
			$this->objetos[$abms]->obtener_html(array(apex_hilo_qs_edo=>$this->contexto['elemento']));
			//$this->objetos[$abms]->info();
			//dump_session();
		}
	}else{
		echo ei_mensaje("PHP: No se explicito el objeto y por lo tanto no es posible determinar la FUENTE","error");
	}

?>