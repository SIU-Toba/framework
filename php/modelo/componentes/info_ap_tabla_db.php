<?php
require_once("interfaces.php");

class info_ap_tabla_db implements meta_clase
{
	protected $datos;
	
	function __construct($datos)
	{
		$this->datos = $datos;
	}
	
	//---------------------------------------------------------------------	
	//-- METACLASE
	//---------------------------------------------------------------------

	function get_molde_subclase()
	{
		return new toba_molde_clase( $this->get_subclase_nombre(), $this->get_clase_nombre() );	
	}
	
	function get_clase_nombre()
	{
		return 'toba_ap_tabla_db';
	}

	function get_clase_archivo()
	{
		return 'nucleo/componentes/persistencia/toba_ap_tabla_db_s.php';
	}
	
	function get_subclase_nombre()
	{
		return $this->datos['ap_sub_clase'];
	}

	function get_subclase_archivo()
	{
		return $this->datos['ap_sub_clase_archivo'];	
	}
}
?>