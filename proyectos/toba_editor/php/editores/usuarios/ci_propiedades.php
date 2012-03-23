<?php

class ci_propiedades extends toba_ci
{
	protected $usuario_actual = '';
	protected $grupo_acceso;
	protected $eliminado = false;
	const clave_falsa = 'xS34Io9gF2JD';
	
	function ini()
	{
		$zona = toba::zona();
		$cargar = false;
		$editable = $zona->get_editable();
		if (isset($editable)) {
			if ($editable != $this->usuario_actual) {
				//Lo que tiene la zona es nuevo, asi que se cargan los datos
				$this->usuario_actual = $editable;
				$condiciones['basicas'] = "basicas.usuario='{$this->usuario_actual}'";
				$condiciones['proyecto'] = "proyecto.proyecto='".toba_editor::get_proyecto_cargado()."'";
				$this->dependencia('datos')->persistidor()->cargar_con_wheres($condiciones);
			}
		}
		
		//Si se pasa el grupo de acceso se resetea la operación porque se asume un alta
		$g_acceso = toba::memoria()->get_parametro('grupo_acceso');
		if (isset($g_acceso)) {
			$this->evt__cancelar();
			$this->grupo_acceso = $g_acceso;
		}
	}
	
	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = 'usuario_actual';
		return $propiedades;
	}
	
	//---- Eventos CI -------------------------------------------------------

	function conf()
	{
		if (! isset($this->usuario_actual)) {
			$this->pantalla()->eliminar_estado_sesion('eliminar');
		}
	}		
	
	function evt__procesar()
	{
		$this->dependencia('datos')->sincronizar();
		if (! isset($this->usuario_actual)) {
			//Si era un alta
			$basicas = $this->dependencia('datos')->tabla('basicas')->get();
			$this->usuario_actual = $basicas['usuario'];
			//Hay que avisarle a la zona
			toba::zona()->cargar($this->usuario_actual);
		}
	}

	function evt__cancelar()
	{
		$this->dependencia('datos')->resetear();
		unset($this->usuario_actual);
		toba::solicitud()->zona()->resetear();
	}

	function evt__eliminar()
	{
		$this->dependencia('datos')->eliminar();
		$this->eliminado = true;
		toba::notificacion()->agregar('El usuario ha sido eliminado.', 'info');
		$this->evt__cancelar();
	}
	//-------------------------------------------------------------------
	//--- DEPENDENCIAS
	//-------------------------------------------------------------------

	//---- form_basicas -------------------------------------------------------

	function evt__form_basicas__modificacion($datos)
	{
		//Esto produce que si el usuario no modifica explicitamente la clave, esta no se cambie
		if ($datos['clave'] == self::clave_falsa ) {
			unset($datos['clave']);	
		}
		$this->dependencia('datos')->tabla('basicas')->set($datos);
	}

	function conf__form_basicas()
	{
		$datos = $this->dependencia('datos')->tabla('basicas')->get();
		if (isset($datos)) {
			$datos['clave'] = self::clave_falsa;
		}
		return $datos;
	}

	//---- form_proyecto -------------------------------------------------------

	function evt__form_proyecto__modificacion($datos)
	{
		if (isset($datos['usuario_grupo_acc'])) {
			//--- Se agrega o cambia el grupo de acceso
			$datos['proyecto'] = toba_editor::get_proyecto_cargado();
			$this->dependencia('datos')->tabla('proyecto')->set($datos);
		} else {
			//--- Se elimina o se mantiene sin grupo de acceso en este proyecto
			$this->dependencia('datos')->tabla('proyecto')->set(null);
		}
	}

	function conf__form_proyecto()
	{
		$base = $this->dependencia('datos')->tabla('proyecto')->get();
		if (!isset($base) && isset($this->grupo_acceso)) {
			$base = array();
			$base['usuario_grupo_acc'] = $this->grupo_acceso;
		}
		return $base;
	}


}

?>