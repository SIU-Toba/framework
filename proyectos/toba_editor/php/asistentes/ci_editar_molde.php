<?php 

class ci_editar_molde extends toba_ci
{
	protected $s__molde;
	protected $s__proyecto;
	protected $s__opciones_borrar;
	protected $s__opciones_generacion;
	protected $asistente;

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
		if ($this->get_id_pantalla() != 'pant_basicos') {
			$datos = toba_info_editores::get_info_molde($this->s__proyecto, $this->s__molde);
			$js = $this->pantalla()->evento('editar_basicos')->get_invocacion_js();
			$txt = "<strong>Tipo</strong>: {$datos['tipo']}<br>";
			$txt .= "<strong>Nombre</strong>: {$datos['nombre']}<br>";
			$txt .= "<strong>Carpeta de archivos</strong>: {$datos['carpeta_archivos']}";
			$txt .= "<br>&nbsp;&nbsp;<a href='#' onclick=\"$js\">Editar Opciones Básicas</a>";
			$this->pantalla()->set_descripcion($txt);
		}
	}
	
	
	function evt__editar_basicos()
	{
		$this->set_pantalla('pant_basicos');
	}
	
	//-----------------------------------------------------------------------------
	//---- BORRAR  ----------------------------------------------------------------
	//-----------------------------------------------------------------------------

	function conf__form_borrar(toba_ei_formulario $form)
	{
	}
	
	function evt__form_borrar__modificacion($datos)
	{
		$this->s__opciones_borrar = $datos;
	}
	
	function evt__borrar()
	{
		$asistente = toba_catalogo_asistentes::cargar_por_molde($this->s__proyecto, $this->s__molde);
		$asistente->borrar_generacion_previa();
		//toba::notificacion()->agregar('No implementado', 'info');
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
		$this->set_pantalla('pant_editar');
	}
	
	//-----------------------------------------------------------------------------------
	//---- Generar el molde ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__pant_generar()
	{
		try {
			//Si hay algun tema bloqueante, no dejo hacer nada
			$bloqueos = $this->asistente(true)->get_bloqueos();
			if(! empty($bloqueos)) {
				$this->pantalla()->eliminar_evento('generar');
				$this->pantalla()->eliminar_dep('form_generaciones');
				toba::notificacion()->agregar('Existen problemas que imposibilitan la ejecución del molde. '
												.' Por favor edite el mismo y vuelva a intentar. '
												.'Los errores se describen a continuacion.');
				foreach($bloqueos as $bloqueo) {
					toba::notificacion()->agregar($bloqueo);	
				}
			}
			// Si no hay opciones de generacion, excluyo el form de opciones
			$opciones = $this->asistente()->get_opciones_generacion();
			if(empty($opciones)) {
				$this->pantalla()->eliminar_dep('form_generaciones');
			}
		} catch ( toba_error_asistentes $e ) {
			toba::notificacion()->agregar("El molde que desea cargar posee errores en su definicion: " . $e->getMessage() );
			$this->pantalla()->eliminar_evento('generar');
			$this->pantalla()->eliminar_dep('form_generaciones');
		}
	}

	function conf__cuadro_generaciones($componente)
	{
		return toba_info_editores::get_lista_ejecuciones_molde($this->s__proyecto, $this->s__molde);
	}

	//--- Opciones del molde
	function conf__form_molde(toba_ei_formulario $form)
	{
		$relacion = $this->dep('asistente')->dep('datos');
		$form->set_datos($relacion->tabla('molde')->get());
	}
	
	function evt__form_molde__modificacion($datos)
	{
		$relacion = $this->dep('asistente')->dep('datos');
		$relacion->tabla('molde')->set($datos);		
	}
	
	
	
	//--- Opciones de generacion ----

	function conf__form_generaciones($componente)
	{
		$componente->set_datos( $this->asistente()->get_opciones_generacion() );
	}
	
	function evt__form_generaciones__modificacion($datos)
	{
		$this->s__opciones_generacion = $datos;
	}

	function evt__generar()
	{
		$this->dep('asistente')->sincronizar();
		$this->asistente()->crear_operacion( $this->s__opciones_generacion );
	}	

	function asistente($reset=false)
	{
		if($reset || !isset($this->asistente)) {
			$this->asistente = toba_catalogo_asistentes::cargar_por_molde($this->s__proyecto, $this->s__molde);
			$this->asistente->preparar_molde();
		}
		return $this->asistente;
	}
}
?>