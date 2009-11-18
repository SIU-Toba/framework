<?php
class toba_servicio_web_info extends toba_componente_info
{
	static function get_tipo_abreviado()
	{
		return "Servicio Web";		
	}
	

	function get_nombre_instancia_abreviado()
	{
		return "serv";	
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