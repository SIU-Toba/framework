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
	
	function get_comentario_carga()
	{
		return array(
			"Permite cambiar la configuraci�n del esquea previo a la generaci�n de la salida",
			"El formato de carga a trav�s del m�todo set_datos es un arreglo de objetos que implementen la interface toba_nodo_arbol",
		);
	}		
}
?>