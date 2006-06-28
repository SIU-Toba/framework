<?php 
//--------------------------------------------------------------------
class ci_efs_prueba extends objeto_ci
{
	protected $datos;

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = 'datos';
		return $propiedades;
	}

	function get_mecanismos_carga()
	{
		$tipos = array(
			array('php', 'Método PHP'),
			array('sql', 'Consulta SQL'),
		);
		//--- Si es un editable, sacar la lista
		if (strpos($this->get_tipo_ef(), 'ef_editable') === false) {
			$tipos[] = array('lista', 'Lista de Valores');
		}
		return $tipos;
	}
	
	//-------------------------------------------------------------------
	//--- DEPENDENCIAS
	//-------------------------------------------------------------------

	//---- carga -------------------------------------------------------

	function get_tipo_ef()
	{
		return 'ef_editable_fecha';
	}
	
	function evt__pre_cargar_datos_dependencias()
	{
		switch ($this->get_tipo_ef()) {
		}
	}
	
	function evt__carga__modificacion($datos)
	{
		$this->datos = $datos;
	}

	function evt__carga__carga()
	{
		return $this->datos;
	}


}

?>