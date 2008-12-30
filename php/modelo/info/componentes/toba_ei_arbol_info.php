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
		$eventos['cambio_apertura']['comentarios'] = array('Para cada nodo del árbol notifica si se encuentra expandido visualmente','@param array $apertura arreglo asociativo <id_nodo> => 0|1 determinando si esta abierto o no');
		$eventos['ver_propiedades']['parametros'] = array('$nodo');
		$eventos['ver_propiedades']['comentarios'] = array('Notifica que el usuario ingreso a un nodo específico', '@param string $nodo Identificador del nodo seleccionado');
		return $eventos;
	}
	
	function get_comentario_carga()
	{
		return array(
			"Permite cambiar la configuración del arbol previo a la generación de la salida",
			"El formato de carga a través del método set_datos es un arreglo de objetos que implementen la interface toba_nodo_arbol",
		);
	}	

}
?>