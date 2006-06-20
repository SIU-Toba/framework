<?
	if($this->contexto['cargado_ok']){
		//-[1]- Cargo la lista de NOTAS
		$listado_notas = $this->cargar_elemento("objeto_lista",0);
		if($listado_notas > -1){
			//-[2]- Cargo el ABM de NOTAS
			$abms = $this->cargar_elemento("objeto_mt_abms",0);
			if($abms > -1){
				$this->objetos[$abms]->establecer_valor_items(array("clase"=>$this->contexto['elemento'],
													"usuario_origen"=>$this->info["usuario"]),true); 
				$this->objetos[$abms]->procesar_evento();
				$this->objetos[$listado_notas]->cargar_datos(array("(objeto = '$this->contexto['elemento']') "));
				//Creo la interface
				$this->objetos[$abms]->obtener_html(array($indice_zona=>$this->contexto['elemento']));
				$this->objetos[$listado_notas]->obtener_html(array($indice_zona=>$this->contexto['elemento']));
				//$this->objetos[$abms]->info();
				//dump_session();
			}
		}
	}else{
		echo ei_mensaje("No se explicito el ELEMENTO (Contexto ZONA)","error");
	}

?>