<?php
class toba_ei_arbol_info extends toba_ei_info
{
	static function get_tipo_abreviado()
	{
		return "Árbol";		
	}
	

	function get_nombre_instancia_abreviado()
	{
		return "arbol";	
	}		
	
	//------------------------------------------------------------------------
	//------ METACLASE -------------------------------------------------------
	//------------------------------------------------------------------------

	function get_molde_subclase()
	{
		return $this->get_molde_vacio();
	}
	
	function eventos_predefinidos()
	{
		$eventos = parent::eventos_predefinidos();	
		$eventos['cambio_apertura']['parametros'] = array('$apertura');
		$eventos['cambio_apertura']['comentarios'] = array("arreglo asociativo 'id_del_nodo' => 0|1 determinando si esta abierto o no");
		$eventos['ver_propiedades']['parametros'] = array('$nodo');
		$eventos['ver_propiedades']['comentarios'] = array();
		return $eventos;
	}
}
?>