<?php
require_once('nucleo/browser/clases/objeto_ci.php');
require_once('api/elemento_item.php');

class ci_composicion_item extends objeto_ci
{
	private $proyecto;
	private $item;

	function set_item($proyecto, $item)
	{
		$this->proyecto = $proyecto;
		$this->item = $item;
	}

	function evt__arbol__carga()
	{
		$this->dependencias['arbol']->set_frame_destino(apex_frame_centro);
		$this->dependencias['arbol']->set_puede_sacar_foto(false);
		$this->dependencias['arbol']->set_nivel_apertura(5);		
		$item = new elemento_item();
		$item->cargar_db( $this->proyecto, $this->item);	
		return $item;
	}
}

?>