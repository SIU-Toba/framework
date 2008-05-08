<?php

class toba_ap_tabla_db_info implements toba_meta_clase
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
		//-------------------------------------
		$comentarios = array('El parametro $id_registro es una referencia a la estructura $this->datos');
		$metodo = new toba_codigo_metodo_php('evt__pre_insert',array('$id_registro'),$comentarios);
		$molde->agregar( $metodo );
		//-------------------------------------
		$comentarios = array('El parametro $id_registro es una referencia a la estructura $this->datos');
		$metodo = new toba_codigo_metodo_php('evt__post_insert',array('$id_registro'),$comentarios);
		$molde->agregar( $metodo );
		//-------------------------------------
		$comentarios = array('El parametro $id_registro es una referencia a la estructura $this->datos');
		$metodo = new toba_codigo_metodo_php('evt__pre_update',array('$id_registro'),$comentarios);
		$molde->agregar( $metodo );
		//-------------------------------------
		$comentarios = array('El parametro $id_registro es una referencia a la estructura $this->datos');
		$metodo = new toba_codigo_metodo_php('evt__post_update',array('$id_registro'),$comentarios);
		$molde->agregar( $metodo );
		//-------------------------------------
		$comentarios = array('El parametro $id_registro es una referencia a la estructura $this->datos');
		$metodo = new toba_codigo_metodo_php('evt__pre_delete',array('$id_registro'),$comentarios);
		$molde->agregar( $metodo );
		//-------------------------------------
		$comentarios = array('El parametro $id_registro es una referencia a la estructura $this->datos');
		$metodo = new toba_codigo_metodo_php('evt__post_delete',array('$id_registro'),$comentarios);
		$molde->agregar( $metodo );
		return $molde;
		
	}
	
	function get_clase_nombre()
	{
		return 'toba_ap_tabla_db_s';
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
	
	//---------------------------------------------------------------------
	
	function get_descripcion_subcomponente()
	{
		return 'Administrador de Persistencia';
	}
}
?>