<?
	$filtro = $this->cargar_objeto("objeto_filtro",0);
	if($filtro > -1)
	{
		//$this->info();
		//dump_CONEXIONES();
		$this->objetos[$filtro]->obtener_interface_vertical();
		if($this->objetos[$filtro]->controlar_estado()===true)
		{
	 		$where = $this->objetos[$filtro]->obtener_where();//echo $where;
			$lista = $this->cargar_objeto("objeto_lista",0);
			if($lista > -1)
			{
				$this->objetos[$lista]->cargar_datos($where);
				$this->objetos[$lista]->obtener_html();
			}else{
				echo ei_mensaje("No se pudo crear la lista");
			}
			echo "<br>";
		}
	}else{
		echo ei_mensaje("No se pudo crear el filtro");
		//$this->info();
	}
?>