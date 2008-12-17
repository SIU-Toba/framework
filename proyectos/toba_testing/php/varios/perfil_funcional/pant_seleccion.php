<?php
class pant_seleccion extends toba_ei_pantalla
{
	function generar_layout()
	{
		if ($this->existe_dependencia('filtro')) {
			$this->dep('filtro')->generar_html();
		}
		echo '<hr />';
		$this->dep('cuadro')->generar_html();
	}
}

?>