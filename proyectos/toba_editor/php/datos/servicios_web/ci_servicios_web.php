<?php
class ci_servicios_web extends toba_ci
{
	protected $carga_ok = false;

	function ini()
	{
		if ($editable = toba::zona()->get_editable()) {
			$clave = array();
			$clave['proyecto'] = toba_editor::get_proyecto_cargado();
			$clave['servicio_web'] = $editable[1];			
			$this->carga_ok = $this->dependencia('datos')->cargar($clave);
		}	
	}

	function conf()
	{
		if (!$this->carga_ok) {
			$this->pantalla()->eliminar_evento('eliminar');
		}	
	}
	
	
	//-----------------------------------------------------------------------------------
	//---- form_basicos -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_basicos(toba_ei_formulario $form)
	{
		$datos = $this->dep('datos')->tabla('servicio')->get();
		$form->set_datos($datos);
	}

	function evt__form_basicos__modificacion($datos)
	{
		$this->dep('datos')->tabla('servicio')->set($datos);
	}
	
	//-----------------------------------------------------------------------------------
	//---- ml_propiedades ---------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__ml_parametros(toba_ei_formulario_ml $ml)
	{
		$datos = $this->dep('datos')->tabla('parametros')->get_filas(null, true);
		$ml->set_datos($datos);
	}

	function evt__ml_parametros__modificacion($datos)
	{
		$this->dep('datos')->tabla('parametros')->procesar_filas($datos);
	}

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__procesar()
	{
		$proyecto = toba_editor::get_proyecto_cargado();
		$this->dep('datos')->tabla('servicio')->set(array('proyecto' => $proyecto));		
		$this->dependencia('datos')->sincronizar();
		$zona = toba::solicitud()->zona();
		if (! $zona->cargada()) {
			$datos = $this->dep('datos')->tabla('servicio')->get();
			$clave = array($datos['proyecto'], $datos['servicio_web']);
			$zona->cargar($clave);
		}
		$this->carga_ok = true;
		admin_util::refrescar_barra_lateral();
	}

	function evt__eliminar()
	{
		$this->dependencia('datos')->eliminar_todo();
		toba::solicitud()->zona()->resetear();
		$this->carga_ok = false;
		admin_util::refrescar_barra_lateral();
	}
	
	
	function extender_objeto_js()
	{
		echo 
			$this->dep('form_basicos')->get_objeto_js().".evt__tipo__procesar = function(es_inicial) {
				var mostrar = this.ef('tipo').get_estado() == 'soap';
				this.ef('param_to').mostrar(mostrar);
				this.ef('param_wsa').mostrar(mostrar);
				{$this->objeto_js}.dep('ml_parametros').mostrar(mostrar);
				
			}
		";
	}

}
?>