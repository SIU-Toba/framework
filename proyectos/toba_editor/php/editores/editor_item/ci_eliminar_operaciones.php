<?php 
class ci_eliminar_operaciones extends toba_ci
{
	protected $lista_comp;
	protected $operacion;
	
	//-----------------------------------------------------------------------------------
	//---- Inicializacion ---------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function ini()
	{
		if (! toba::zona()->cargada()) {
			throw new toba_error('La operación se debe invocar desde la zona de un item');
		} else {
			$info = toba::zona()->get_info();
			$this->operacion = new toba_modelo_operacion($info['proyecto'], $info['item']);
		}
	}

	//-----------------------------------------------------------------------------------
	//---- DEPENDENCIAS -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__form__modificacion($datos)
	{
		$this->lista_comp = $datos;
	}

	function conf__form(toba_ei_formulario_ml $form_ml)
	{
		$form_ml->set_datos($this->operacion->get_info_eliminacion());
	}

	function evt__eliminar()
	{
		$opciones = array();
		foreach($this->lista_comp as $comp) {
			$opciones[$comp['componente']] = array('eliminar' => $comp['eliminar'], 'eliminar_archivo' => $comp['eliminar_archivo']);
		}
		$this->operacion->eliminar(true, $opciones, true);
		toba::notificacion()->agregar('La operación y sus componentes seleccionados han sido eliminado');
		toba::zona()->resetear();
	}
}

?>