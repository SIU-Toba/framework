<?php
require_once('objetos_toba/efs/ci_efs.php');

/**
 * La idea de este ci es reutilizar la logica de edición de efs en todo lo posible
 */
class ci_cols extends ci_efs
{

	protected $campo_clave = 'nombre';
	
	function get_tipo_ef()
	{
		$tipo = $this->get_tabla()->get_fila_columna( $this->s__seleccion_efs, "tipo");
		$metodo = "get_clase_ef";
		return call_user_func(array('toba_filtro_columna_'.$tipo, $metodo));		
	}	
	
	
}
?>