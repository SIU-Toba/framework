<?php 

class ci_listado_moldes extends toba_ci
{
	//-----------------------------------------------------------------------------------
	//---- Elegir molde ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_planes($componente)
	{
		return toba_info_editores::get_lista_moldes_existentes();
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




}
?>