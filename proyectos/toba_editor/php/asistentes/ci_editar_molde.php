<?php 

class ci_editar_molde extends toba_ci
{
	protected $s__molde;
	protected $s__proyecto;

	function ini()
	{
		$molde = toba::memoria()->get_parametro('molde');
		$proyecto = toba::memoria()->get_parametro('proyecto');
		if (isset($molde)) {
			$this->s__molde = $molde;
		}
		if (isset($proyecto)) {
			$this->s__proyecto = $proyecto;
		}
		$evento = toba::memoria()->get_parametro(apex_ei_evento);
		if (isset($evento)) {
			if ($evento == 'editar') {
				$this->set_pantalla('pant_editar');
			} else {
				$this->set_pantalla('pant_generar');
			}
		}
	}
	

	//-----------------------------------------------------------------------------------
	//---- Editar molde ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------	

	function conf__pant_editar()
	{
		if(isset($this->s__proyecto) && isset($this->s__molde)) {	
			// molde Existente
			$ci = toba_catalogo_asistentes::get_ci_molde($this->s__proyecto, $this->s__molde);
		} else {
			throw new toba_error('No se definio el tipo de molde a editar');	
		}
		$this->agregar_dependencia('asistente', 'toba_editor', $ci);
		$this->pantalla()->agregar_dep('asistente');
	}

	function evt__procesar()
	{
		$this->set_pantalla('pant_generar');
	}
	
	function evt__generar()
	{
		$this->evt__procesar();	
	}
	
	//-----------------------------------------------------------------------------------
	//---- Generar el molde ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__pant_generar()
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


}
?>