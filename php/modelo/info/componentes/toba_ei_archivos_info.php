<?php
class toba_ei_archivos_info extends toba_ei_info
{
	static function get_tipo_abreviado()
	{
		return "Archivos";		
	}
	

	function get_nombre_instancia_abreviado()
	{
		return "archivos";	
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
			"Permite cambiar la configuraci�n del componente previo a la generaci�n de la salida",
		);
	}	
	
	function eventos_predefinidos()
	{
		$eventos = parent::eventos_predefinidos();
		$eventos['seleccionar_archivo']['parametros'] = array('$archivo');
		$eventos['seleccionar_archivo']['comentarios'] = array("Indica que el usuario seleccion� un archivo puntual de la lista", '@param string $archivo');
		return $eventos;
	}
}
?>