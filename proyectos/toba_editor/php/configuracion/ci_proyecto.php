<?php 
require_once('modelo/consultas/dao_editores.php');

class ci_proyecto extends objeto_ci
{
	protected $iniciado = false;
	
	function evt__inicializar()
	{
		if(!$this->iniciado) {
			$this->dependencia('datos')->cargar(array('proyecto'=>editor::get_proyecto_cargado()));
			$this->iniciado = true;
		}	
	}

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = 'iniciado';
		return $propiedades;
	}

	function evt__modificacion()
	{
		$this->dependencia('datos')->sincronizar();
	}

	//-------------------------------------------------------------------
	//--- DEPENDENCIAS
	//-------------------------------------------------------------------

	//---- basica -------------------------------------------------------

	function evt__basica__modificacion($datos)
	{
		$this->dependencia('datos')->set($datos);
	}

	function evt__basica__carga()
	{
		return $this->dependencia('datos')->get();
	}

	//---- login -------------------------------------------------------

	function evt__login__modificacion($datos)
	{
		$this->dependencia('datos')->set($datos);
	}

	function evt__login__carga()
	{
		$datos = $this->dependencia('datos')->get();
		if (isset($datos['item_inicio_sesion'])) {
			$datos['carpeta_inicio_sesion'] = dao_editores::get_carpeta_de_item(	$datos['item_inicio_sesion'], 
																					$datos['proyecto']);
		}
		if (isset($datos['item_pre_sesion'])) {
			$datos['carpeta_pre_sesion'] = dao_editores::get_carpeta_de_item(	$datos['item_pre_sesion'], 
																				$datos['proyecto']);
		}
		return $datos;
	}

	//---- nucleo -------------------------------------------------------

	function evt__nucleo__modificacion($datos)
	{
		$this->dependencia('datos')->set($datos);
	}

	function evt__nucleo__carga()
	{
		return $this->dependencia('datos')->get();
	}
}
?>