<?php 
class pant_catalogo_objetos extends toba_ei_pantalla
{
	function generar_layout()
	{
		$this->dep('fotos')->generar_html();
		$this->dep('filtro')->generar_html();
		$this->dep('listado')->generar_html();
	}
}

?>