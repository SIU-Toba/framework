<?php 
class pant_basico extends toba_ei_pantalla
{
	
	function extender_objeto_js()
	{
		$id_basico = $this->dep('form_basico')->get_id_objeto_js();	
		echo "$id_basico.evt__gen_usa_filtro__procesar(false);";
	}
}

?>