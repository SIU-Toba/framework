<?php
require_once('administracion_proceso_gui.php');
class ci_administracion_proyecto extends toba_ci
{
	protected $_proyecto_actual;
	protected $_instancia_actual;
	protected $_comando_actual;
	protected $_log_comando;
	protected $s__ya_exporto = false;

	function ini()
	{
		$this->_proyecto_actual = toba_editor::get_proyecto_cargado();
		$this->_log_comando = new administracion_proceso_gui();
		$this->_instancia_actual = toba_modelo_catalogo::instanciacion()->get_instancia(toba::instancia()->get_id(), $this->_log_comando);
	}

	//-----------------------------------------------------------------------------------
	//---- Configuraciones --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__pant_inicial(toba_ei_pantalla $pantalla)
	{
		if (! $this->s__ya_exporto) {
			$this->evento('regenerar')->anular();
		}
	}

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__actualizar()
	{
		$this->_comando_actual = $this->generar_linea_comando_proyecto('actualizar');
		$proyecto  = $this->_instancia_actual->get_proyecto($this->_proyecto_actual);
		$proyecto->exportar();
		$this->_instancia_actual->exportar_local();
		$proyecto->actualizar();
		$proyecto->regenerar();
	}

	function evt__exportar()
	{
		$this->_comando_actual = $this->generar_linea_comando_proyecto('exportar');
		$this->_instancia_actual->get_proyecto($this->_proyecto_actual)->exportar();
		$this->_instancia_actual->exportar_local();
		$this->s__ya_exporto = true;
	}

	function evt__regenerar()
	{
		$this->_comando_actual = $this->generar_linea_comando_proyecto('regenerar');
		$this->_instancia_actual->get_proyecto($this->_proyecto_actual)->regenerar();
	}

	function evt__compilar()
	{
		$this->_comando_actual = $this->generar_linea_comando_proyecto('compilar');
		$this->_instancia_actual->get_proyecto($this->_proyecto_actual)->compilar();
	}

	function get_comando_en_ejecucion()
	{
		if (isset($this->_comando_actual)) {
			return $this->_comando_actual;
		}
	}

	function get_log_comando_ejecucion()
	{
		if (isset($this->_log_comando)) {
			return texto_plano($this->_log_comando->get_info_log());
		}
	}

	function generar_linea_comando_proyecto($comando)
	{
		return "toba proyecto $comando -p  {$this->_proyecto_actual}";
	}
}
?>