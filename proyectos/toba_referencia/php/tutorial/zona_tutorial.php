<?php

class zona_tutorial extends toba_zona 
{

	function cargada()
	{
		return true;
	}	
	
	function generar_html_barra_vinculos()
	{
		$salida = " ";
		$id_actual = toba::solicitud()->get_datos_item('item');
		$i = 1;
		$anterior = null;
		$siguiente = null;
		foreach($this->items_vecinos as $item){
			$es_el_ultimo = (count($this->items_vecinos) == $i);
			$es_el_actual = false;
			if ($item['item'] == $id_actual) {
				$es_el_actual = true;
			}
			if ($es_el_actual) {
				$vinculo = null;
			} else {
				$vinculo = toba::vinculador()->get_url($item['item_proyecto'], $item['item'], 
														array(), array('zona' =>true, 'validar'=>false));
			}	
			if (isset($vinculo)) {
	 			$salida .= "<a href='$vinculo'>";
				$salida .= $item['nombre'];
				$salida .= "</a>";
			} else {
				$salida .= "<strong>".$item['nombre']."</strong>";
			}
			if (! $es_el_ultimo) {
				$salida .= " | ";	
			}
			$i++;
			if ($i % 5 == 0) {
				$salida = substr($salida, 0, -2);
				$salida .= "<br>";
			}			
		}
		echo $salida;
	}	
}

?>