<?
	$filtro = $this->cargar_objeto("objeto_filtro",0);
	if($filtro > -1){
        enter();
        $this->objetos[$filtro]->obtener_interface_vertical();
		echo "<br>";
		//$this->objetos[$filtro]->info();
		$grafico =	$this->cargar_objeto("objeto_grafico",0);
		if($grafico > -1){

	          //Si el filtro pasa sus controles...
			$status_filtro = $this->objetos[$filtro]->validar_estado();
			if( $status_filtro[0] )
			{
		 		$where = $this->objetos[$filtro]->obtener_where();//echo $where;
				$from = $this->objetos[$filtro]->obtener_from();
	
				if($this->objetos[$grafico]->cargar_datos($where,$from)===true)
				{
					$this->objetos[$grafico]->obtener_html();
				}else{
					$this->objetos[$grafico]->mostrar_estado();
					//echo "SALIO MAL";
				}
			}else{
				$this->objetos[$filtro]->mostrar_info_proceso();
			}
		}else{
			echo ei_mensaje("No se pudo crear el GRAFICO");
		}
	}else{
		echo ei_mensaje("No se pudo crear el filtro");
	}		
?>