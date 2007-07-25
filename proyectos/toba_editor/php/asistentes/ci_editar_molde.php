<?php 

class ci_editar_molde extends toba_ci
{
	protected $s__molde;
	protected $s__proyecto;
	protected $s__opciones_borrar;

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
			switch ($evento) {
				case 'editar':
					$this->set_pantalla('pant_editar');
					break;
				case 'generar':
					$this->set_pantalla('pant_generar');
					break;				
				case 'borrar':
					$this->set_pantalla('pant_borrar');
					break;									
			}
		}
		//--- Se agrega la dependencia dinamicamente
		if(isset($this->s__proyecto) && isset($this->s__molde)) {	
			// molde Existente
			$ci = toba_catalogo_asistentes::get_ci_molde($this->s__proyecto, $this->s__molde);
		} else {
			throw new toba_error('No se definio el tipo de molde a editar');	
		}
		$this->agregar_dependencia('asistente', 'toba_editor', $ci);
		$this->dep('asistente')->set_molde($this->s__proyecto, $this->s__molde);				
	
	}
	
	function conf()
	{
		$datos = toba_info_editores::get_info_molde($this->s__proyecto, $this->s__molde);
		$txt = "<strong>Tipo</strong>: {$datos['tipo']}<br>";
		$txt .= "<strong>Nombre</strong>: {$datos['nombre']}";
		$this->pantalla()->set_descripcion($txt);
	}
	
	
	//-----------------------------------------------------------------------------
	//---- BORRAR  ----------------------------------------------------------------
	//-----------------------------------------------------------------------------

	function conf__form_borrar(toba_ei_formulario $form)
	{
		$form->set_mostrar_ayuda_en_tooltips(false);
	}
	
	function evt__form_borrar__modificacion($datos)
	{
		$this->s__opciones_borrar = $datos;
	}
	
	function evt__borrar()
	{
		toba::notificacion()->agregar('No implementado', 'info');
	}
	
	
	//-----------------------------------------------------------------------------------
	//---- Editar molde ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------	

	function conf__pant_editar()
	{

		$this->pantalla()->agregar_dep('asistente');
	}

	function evt__procesar()
	{
		$this->dep('asistente')->sincronizar();
	}
	
	//-----------------------------------------------------------------------------------
	//---- Generar el molde ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_ejecuciones($componente)
	{
		return toba_info_editores::get_lista_ejecuciones_molde($this->s__proyecto, $this->s__molde);
	}

	function conf__form_generar($componente)
	{
	}
	
	function evt__form_generar__modificacion($parametros)
	{

	}
	
	function evt__generar()
	{
		$asistente = toba_catalogo_asistentes::cargar_por_molde($this->s__proyecto, $this->s__molde);
		$asistente->generar_molde();
		$asistente->crear_operacion();
	}	


}
?>