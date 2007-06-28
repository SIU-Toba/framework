<?php
/*
*	
*/
class toba_molde_elemento_componente extends toba_molde_elemento
{
	protected $clase_proyecto ='toba';
	
	function ini()
	{
		$this->datos->tabla('base')->set_fila_columna_valor(0,'clase',$this->clase);
		$this->datos->tabla('base')->set_fila_columna_valor(0,'clase_proyecto',$this->clase_proyecto);
	}
	
	function get_clave_componente_generado()
	{
		$datos = $this->datos->tabla('base')->get_clave_valor(0);
		return array(	'clave' => $datos['objeto'],
						'proyecto' => $datos['proyecto']);
	}
}
?>