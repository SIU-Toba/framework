<?php 

class ci_planes extends toba_ci
{
	protected $s__plan;
	protected $s__proyecto;
	protected $s__tipo_plan_nuevo;

	//-----------------------------------------------------------------------------------
	//---- Crear PLAN -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_tipo_plan()
	{
		return toba_info_editores::get_lista_tipo_plan();
	}

	function evt__cuadro_tipo_plan__seleccion($datos)
	{
		$this->s__tipo_plan_nuevo = $datos;
		$this->set_pantalla('editar');
	}
	
	function evt__elegir()
	{
		$this->set_pantalla('elegir');
	}
	
	//-----------------------------------------------------------------------------------
	//---- Elegir PLAN ------------------------------------------------------------------
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
		$this->s__proyecto = $seleccion['proyecto'];
		$this->s__plan = $seleccion['plan'];
		$this->set_pantalla('editar');
	}

	function evt__agregar()
	{
		$this->set_pantalla('crear');
	}

	//-----------------------------------------------------------------------------------
	//---- Editar PLAN ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------	

	function conf__editar()
	{
		if(isset($this->s__tipo_plan_nuevo)) {	
			// Plan NUEVO
			$ci = $this->s__tipo_plan_nuevo['ci'];
		} elseif(isset($this->s__proyecto) && isset($this->s__plan)) {	
			// Plan Existente
			$ci = toba_catalogo_asistentes::get_ci_plan($this->s__proyecto, $this->s__plan);
		} else {
			throw new toba_error('No se definio el tipo de plan a editar');	
		}
		$this->agregar_dependencia('asistente', 'toba_editor', $ci);
		$this->pantalla()->agregar_dep('asistente');
	}

	function evt__guardar()
	{
		$this->set_pantalla('ejecutar');
	}
	
	function evt__cancelar_edicion()
	{
	}

	//-----------------------------------------------------------------------------------
	//---- Ejecutar PLAN ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__ejecutar()
	{
		$datos = toba_info_editores::get_info_plan($this->s__proyecto, $this->s__plan);
		$txt = "<strong>Tipo</strong>: {$datos['tipo']}<br>";
		$txt .= "<strong>Nombre</strong>: {$datos['nombre']}";
		$this->pantalla()->set_descripcion($txt);
	}

	function conf__cuadro_ejecuciones($componente)
	{
		return toba_info_editores::get_lista_ejecuciones_plan($this->s__proyecto, $this->s__plan);
	}

	//El formato del retorno debe ser array('id_ef' => $valor, ...)
	function conf__form_generar($componente)
	{
	}
	
	function evt__form_generar__generar($parametros)
	{
		$asistente = toba_catalogo_asistentes::cargar_por_plan($this->s__proyecto, $this->s__plan);
		$asistente->generar_molde();
		$asistente->crear_operacion();
	}

	function evt__editar($parametros)
	{
		$this->set_pantalla('editar');
	}

	function evt__cancelar_ejecucion()
	{
		unset($this->s__plan);
		unset($this->s__proyecto);
		$this->set_pantalla('elegir');
	}
}
?>