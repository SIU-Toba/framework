<?php
class toba_ei_esquema_info extends toba_ei_info
{
	static function get_tipo_abreviado()
	{
		return "Esquema";		
	}
		

	function get_nombre_instancia_abreviado()
	{
		return "esquema";	
	}	
		
	//------------------------------------------------------------------------
	//------ METACLASE -------------------------------------------------------
	//------------------------------------------------------------------------

	function get_molde_subclase()
	{
		return $this->get_molde_vacio();
	}
}
?>