<?
	$filtro = $this->cargar_objeto("objeto_filtro",0);
	if($filtro > -1){

 		$where = $this->objetos[$filtro]->obtener_where();//ei_arbol($where,"WHERE");
 		$from = $this->objetos[$filtro]->obtener_from();//ei_arbol($from,"FROM");
		$filtro_info = $this->objetos[$filtro]->obtener_info();//ei_arbol($filtro_info,"INFO");
	
		//Si el filtro esta activado, muestro los resultados
		if(count($where)>0)
		{
			enter();
			$this->objetos[$filtro]->obtener_interface_vertical();

			$cuadro = $this->cargar_objeto("objeto_cuadro",0);
			if($cuadro > -1){
				$this->objetos[$cuadro]->cargar_datos($where);
				enter();
				$this->objetos[$cuadro]->obtener_html();
				enter();
			}else{
				echo ei_mensaje("El cuadro no esta disponible");
			}
	
		}else{
			//echo ei_mensaje("Establesca los parametros del filtro para restringir la busqueda");

			enter();
			$this->objetos[$filtro]->obtener_interface_vertical();
		}
	}else{
		echo ei_mensaje("No es posible acceder al filtro");
	}
?>