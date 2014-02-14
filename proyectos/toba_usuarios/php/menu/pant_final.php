<?php
class pant_final extends toba_ei_pantalla
{
	private $menu;
	
	function set_menu($obj)
	{
		$this->menu = $obj;			
	}
	
	function generar_layout()
	{
		$estilo = $this->menu->plantilla_css();
		if ($estilo != '') {
			echo toba_recurso::link_css($estilo, 'screen', false);
		}	
		
		$this->menu->mostrar();
	}

}

?>