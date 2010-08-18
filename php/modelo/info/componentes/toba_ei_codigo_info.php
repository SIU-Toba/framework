<?php
class toba_ei_codigo_info extends toba_ei_info
{
	static function get_tipo_abreviado()
	{
		return "C�digo";
	}
		

	function get_nombre_instancia_abreviado()
	{
		return "codigo";
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
			"Permite cambiar la configuraci�n del grafico previo a la generaci�n de la salida"
		);
	}		
}
?>