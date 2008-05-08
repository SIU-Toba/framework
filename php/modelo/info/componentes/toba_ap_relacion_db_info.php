<?php

class toba_ap_relacion_db_info implements toba_meta_clase
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
		$molde = new toba_codigo_clase( $this->get_subclase_nombre(), $this->get_clase_nombre() );	
		$molde->agregar( new toba_codigo_metodo_php('ini') );
		$molde->agregar( new toba_codigo_metodo_php('evt__pre_sincronizacion') );
		$molde->agregar( new toba_codigo_metodo_php('evt__post_sincronizacion') );
		$molde->agregar( new toba_codigo_metodo_php('evt__pre_eliminacion') );
		$molde->agregar( new toba_codigo_metodo_php('evt__post_eliminacion') );
		return $molde;
	}
	
	function get_clase_nombre()
	{
		return 'toba_ap_relacion_db';
	}

	function get_clase_archivo()
	{
		return 'nucleo/componentes/persistencia/toba_ap_relacion_db.php';
	}
	
	function get_subclase_nombre()
	{
		return $this->datos['ap_clase'];
	}

	function get_subclase_archivo()
	{
		return $this->datos['ap_archivo'];	
	}

	//---------------------------------------------------------------------
	
	function get_descripcion_subcomponente()
	{
		return 'Administrador de Persistencia';
	}
}
?>