<?php 
class ci_planes extends toba_ci
{
	protected $s__plan;
	protected $s__proyecto;
	
	function ini()
	{
	}

	function conf()
	{
	}
	
	function evt__cancelar_ejecucion()
	{
		unset($this->s__plan);
		unset($this->s__proyecto);
		$this->set_pantalla('elegir');
	}

	//-----------------------------------------------------------------------------------
	//---- Elegir -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_planes($componente)
	{
		return toba_info_editores::get_lista_planes_existentes();
	}

	function evt__cuadro_planes__ejecutar($seleccion)
	{
		$this->s__plan = $seleccion['plan'];
		$this->s__proyecto = $seleccion['proyecto'];
		$this->set_pantalla('ejecutar');
	}

	function evt__cuadro_planes__editar($seleccion)
	{
	}

	//-----------------------------------------------------------------------------------
	//---- Ejecutar -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_ejecuciones($componente)
	{
		return toba_info_editores::get_lista_ejecuciones_plan($this->s__proyecto, $this->s__plan);
	}

	function evt__form_generar__generar($parametros)
	{
		$asistente = toba_catalogo_asistentes::cargar_por_plan($this->s__proyecto, $this->s__plan);
		$asistente->generar_molde();
		$asistente->crear_operacion();
	}

	//El formato del retorno debe ser array('id_ef' => $valor, ...)
	function conf__form_generar($componente)
	{
	}
}
?>