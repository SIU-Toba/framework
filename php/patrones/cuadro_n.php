<?
	if(is_array($this->indice_objetos['objeto_cuadro'])){
		$cuadros = count($this->indice_objetos['objeto_cuadro']);
		enter();
		for($a=0;$a<$cuadros;$a++){
			$cuadro[$a] = $this->cargar_objeto("objeto_cuadro",$a);
			if($cuadro[$a] > -1)
			{
				$this->objetos[$cuadro[$a]]->cargar_datos();
				$this->objetos[$cuadro[$a]]->obtener_html();
				enter();
			}else{
				echo ei_mensaje("No se pudo crear la lista");
			}
		}
	}else{
		echo ei_mensaje("No hay cuadros disponibles");
	}
?>