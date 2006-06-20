<?
	$filtro = $this->cargar_objeto("objeto_filtro",0);
	if($filtro > -1)
	{
		enter();
		$this->objetos[$filtro]->obtener_interface_vertical();
		$status_filtro = $this->objetos[$filtro]->validar_estado();
		if( $status_filtro[0] )
		{
	 		$where = $this->objetos[$filtro]->obtener_where();//echo $where;
			$cuadro = $this->cargar_objeto("objeto_cuadro",0);
			if($cuadro > -1)
			{
				$this->objetos[$cuadro]->cargar_datos($where);
				enter();
				$this->objetos[$cuadro]->obtener_html();
			}else{
				echo ei_mensaje("No se pudo crear la lista");
			}
			echo "<br>";
		}else{
			$this->objetos[$filtro]->mostrar_info_proceso();
		}
	}else{
		echo ei_mensaje("No se pudo crear el filtro");
		//$this->info();
	}
?>