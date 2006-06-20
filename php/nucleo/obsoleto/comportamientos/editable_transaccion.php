<?
	if($editable = $this->zona->obtener_editable_propagado()){
		$this->zona->cargar_editable();
		$this->zona->obtener_html_barra_superior();
		
		//La clase que se deea cargar es el PARAMETRO 1
		if(isset($this->info["item_parametro_a"])){
			$mt = $this->cargar_objeto($this->info["item_parametro_a"],0);
			if($mt > -1){
				$this->objetos[$mt]->procesar($editable);
				$this->objetos[$mt]->obtener_html();
			}else{
				echo ei_mensaje("No fue posible instanciar el MT");
			}				
		}else{
			echo ei_mensaje("Debe especificarse el tipo de marco a cargar en el parametro 1");
		}
	
		$this->zona->obtener_html_barra_inferior();
	}else{
		echo ei_mensaje("No se explicito el ELEMENTO a editar","error");
	}
?>