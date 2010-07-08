<?php
class toba_ei_mapa_info extends toba_ei_info
{
	static function get_tipo_abreviado()
	{
		return "Mapa";
	}
		

	function get_nombre_instancia_abreviado()
	{
		return "mapa";
	}	
		
	//------------------------------------------------------------------------
	//------ METACLASE -------------------------------------------------------
	//------------------------------------------------------------------------

	function get_molde_subclase()
	{
		return $this->get_molde_vacio();
	}
	
	function get_comentario_carga()
	{
		return array(
			"Permite cambiar la configuración del mapa previo a la generación de la salida",
		);
	}		
}
?>