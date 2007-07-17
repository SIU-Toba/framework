<?php 

class ci_moldes extends toba_ci
{
	protected $s__molde;
	protected $s__proyecto;
	protected $s__tipo_molde_nuevo;

	//-----------------------------------------------------------------------------------
	//---- Crear molde -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_tipo_plan()
	{
		return toba_info_editores::get_lista_tipo_molde();
	}

	function evt__cuadro_tipo_plan__seleccion($datos)
	{
		$this->s__tipo_molde_nuevo = $datos;
		$this->set_pantalla('editar');
	}
	
	function evt__elegir()
	{
		$this->set_pantalla('elegir');
	}
	
	//-----------------------------------------------------------------------------------
	//---- Elegir molde ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_planes($componente)
	{
		return toba_info_editores::get_lista_moldes_existentes();
	}

	function evt__cuadro_planes__ejecutar($seleccion)
	{
		$this->s__molde = $seleccion['molde'];
		$this->s__proyecto = $seleccion['proyecto'];
		$this->set_pantalla('ejecutar');
	}

	function evt__cuadro_planes__editar($seleccion)
	{
		$this->s__proyecto = $seleccion['proyecto'];
		$this->s__molde = $seleccion['molde'];
		$this->set_pantalla('editar');
	}

	function evt__agregar()
	{
		$this->set_pantalla('crear');
	}

	//-----------------------------------------------------------------------------------
	//---- Editar molde ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------	

	function conf__editar()
	{
		if(isset($this->s__tipo_molde_nuevo)) {	
			// molde NUEVO
			$ci = $this->s__tipo_molde_nuevo['ci'];
		} elseif(isset($this->s__proyecto) && isset($this->s__molde)) {	
			// molde Existente
			$ci = toba_catalogo_asistentes::get_ci_molde($this->s__proyecto, $this->s__molde);
		} else {
			throw new toba_error('No se definio el tipo de molde a editar');	
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
	//---- Ejecutar molde ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__ejecutar()
	{
		$datos = toba_info_editores::get_info_molde($this->s__proyecto, $this->s__molde);
		$txt = "<strong>Tipo</strong>: {$datos['tipo']}<br>";
		$txt .= "<strong>Nombre</strong>: {$datos['nombre']}";
		$this->pantalla()->set_descripcion($txt);
	}

	function conf__cuadro_ejecuciones($componente)
	{
		return toba_info_editores::get_lista_ejecuciones_molde($this->s__proyecto, $this->s__molde);
	}

	//El formato del retorno debe ser array('id_ef' => $valor, ...)
	function conf__form_generar($componente)
	{
	}
	
	function evt__form_generar__generar($parametros)
	{
		$asistente = toba_catalogo_asistentes::cargar_por_molde($this->s__proyecto, $this->s__molde);
		$asistente->generar_molde();
		$asistente->crear_operacion();
	}

	function evt__editar($parametros)
	{
		$this->set_pantalla('editar');
	}

	function evt__cancelar_ejecucion()
	{
		unset($this->s__molde);
		unset($this->s__proyecto);
		$this->set_pantalla('elegir');
	}
}
?>