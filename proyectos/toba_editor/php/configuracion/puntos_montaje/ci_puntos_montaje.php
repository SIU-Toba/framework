<?php
class ci_puntos_montaje extends toba_ci
{
	protected $s__tipo;
	protected $s__tipo_punto;
	protected $s__editando;
	protected $s__etiqueta;
	/**
	 * @var toba_modelo_pms
	 */
	protected $pms;

	function ini()
	{
		$proyecto = toba_modelo_catalogo::instanciacion()->get_proyecto(toba::instancia()->get_id(),
											toba_editor::get_proyecto_cargado());
		$this->pms = $proyecto->get_pms();
	}

	//-----------------------------------------------------------------------------------
	//---- ci_puntos_montaje ------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function cambiar_tab__anterior()
	{
		if ($this->get_id_pantalla() == 'otro') {
			$this->set_pantalla('tipo_montaje');
		}
	}

	function conf()
	{
		if (!isset($this->s__tipo)) {
			$this->set_pantalla('tipo_montaje');
		} elseif ($this->es_proyecto()) {
			$this->set_pantalla('proyecto_toba');
			$this->pantalla()->evento('cambiar_tab__siguiente')->ocultar();
		} else {
			$this->set_pantalla('otro');
		}
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro_puntos_montaje --------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_puntos_montaje(toba_ei_cuadro $cuadro)
	{
		$cuadro->set_datos($this->get_listado());
	}

	function evt__cuadro_puntos_montaje__seleccion($datos)
	{
		$punto = $this->pms->get_por_id($datos['id']);
		$datos = array();
		$datos['id'] = $punto->get_id();
		$datos['etiqueta'] = $punto->get_etiqueta();
		$datos['descripcion'] = $punto->get_descripcion();

		if ($punto->es_de_proyecto()) {
			$datos['proyecto_ref'] = $punto->get_proyecto_referenciado();
			$this->dep('form_proyecto')->set_datos($datos);
		} else {
			$datos['path_pm'] = $punto->get_path();
			$this->dep('form_otro')->set_datos($datos);
		}
		$this->s__etiqueta = $datos['etiqueta'];
		$this->s__tipo = $punto->get_tipo();
		$this->s__editando = true;
	}

	function evt__cuadro_puntos_montaje__eliminar($datos)
	{
		$this->eliminar($datos);
		unset($this->s__tipo);
	}
	
	//-----------------------------------------------------------------------------------
	//---- form_eleccion_tipo -----------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__form_eleccion_tipo__modificacion($datos)
	{
		$this->s__tipo = $datos['tipo'];
		$this->s__editando = false;
	}


	//-----------------------------------------------------------------------------------
	//---- form_proyecto ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_proyecto()
	{
		if ($this->estoy_editando()) {
			$this->dep('form_proyecto')->ef('proyecto_ref')->set_solo_lectura(true);
		}
		$this->s__tipo_punto = $this->s__tipo;
		unset($this->s__tipo);
	}

	function evt__form_proyecto__modificacion($datos)
	{
		$datos['tipo'] = $this->s__tipo_punto; // Seteamos el tipo acá así no se pasa por parámetro
		$this->grabar_proyecto($datos);
	}

	//-----------------------------------------------------------------------------------
	//---- form_otro --------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_otro()
	{
		$this->s__tipo_punto = $this->s__tipo;
		unset($this->s__tipo);
	}

	function evt__form_otro__modificacion($datos)
	{
		$datos['tipo'] = $this->s__tipo_punto; // Seteamos el tipo acá así no se pasa por parámetro
		if ($this->estoy_editando()) {
			$datos['etiqueta_anterior'] = $this->s__etiqueta;
		} else {
			$datos['etiqueta_anterior'] = $datos['etiqueta'];
		}
		$this->grabar_otro($datos);
	}

	//-----------------------------------------------------------------------------------
	//---- EXTRAS -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function proyectos_accesibles()
	{
		$proyectos_acc = toba_instancia::instancia()->get_proyectos_accesibles();
		foreach ($proyectos_acc as $key => $proyecto) {
			if ($proyecto[0] == toba_editor::get_proyecto_cargado()) {
				unset($proyectos_acc[$key]);
				break;
			}
		}
		return $proyectos_acc;
	}

	protected function es_proyecto()
	{
		return	$this->s__tipo == toba_punto_montaje::tipo_proyecto ||
				$this->s__tipo == toba_punto_montaje::tipo_pers;
	}

	protected function estoy_editando()
	{
		return isset($this->s__editando) && $this->s__editando;
	}

	function get_listado()
	{
		return $this->pms->get_listado(true);
	}

	function grabar_otro($datos)
	{
		$datos['proyecto'] = toba_editor::get_proyecto_cargado();
		$datos['proyecto_ref'] = '';
		$punto = toba_punto_montaje_factory::construir($datos);
		$this->pms->guardar($punto);
	}

	function grabar_proyecto($datos)
	{
		$datos['proyecto'] = toba_editor::get_proyecto_cargado();

		if ($datos['tipo'] == toba_punto_montaje::tipo_proyecto) {
			$datos['path_pm'] = 'php';
		} else {
			$datos['path_pm'] = 'personalizacion/php';
		}
		$punto = toba_punto_montaje_factory::construir($datos);

		$this->pms->guardar($punto);
	}

	function eliminar($datos)
	{
		$id = $datos['id'];
		$punto = $this->pms->get_por_id($id);
		$this->pms->baja($punto);
	}
}
?>