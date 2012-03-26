<?php

class ci_navegacion extends toba_ci
{
	protected $s__filtro;

	//-------------------------------------------------------------------
	//--- Eventos GLOBALES
	//-------------------------------------------------------------------
	
	function evt__guardar()
	{
		$this->dep('datos')->sincronizar();
		$this->dep('datos')->resetear();
		$this->set_pantalla('seleccionar');
	}

	function evt__cancelar()
	{
		$this->dep('datos')->resetear();
		$this->set_pantalla('seleccionar');
	}

	function evt__agregar()
	{
		$this->dep('editor')->limpiar_datos();
		$this->set_pantalla('editar');
	}
	
	function evt__eliminar()
	{
		$this->dep('datos')->eliminar();
		$this->dep('datos')->resetear();
		$this->set_pantalla('seleccionar');
	}
	
	function conf__seleccionar()
	{
		$this->dep('filtro')->desactivar_efs(array('asociados'));
	}

	//---- filtro -------------------------------------------------------

	function evt__filtro__filtrar($datos)
	{
		$this->s__filtro = $datos;
	}

	function evt__filtro__cancelar()
	{
		unset($this->s__filtro);
	}

	function conf__filtro($componente)
	{
		if (isset($this->s__filtro)) {
			$componente->set_datos($this->s__filtro);
		} 
	}
	
	//---- cuadro -------------------------------------------------------

	function conf_evt__cuadro__eliminar(toba_evento_usuario $evt)
	{
		$usuario = $evt->get_parametros();
		if ($usuario == toba::usuario()->get_id()) {
			$evt->anular();	
		}
	}
	
	function conf__cuadro($componente)
	{
		if (isset($this->s__filtro)) {
			$proyecto = $this->s__filtro['proyecto'];
			switch ($this->s__filtro['pertenencia']){
				case 'P' :
					$datos = consultas_instancia::get_usuarios_vinculados_proyecto($proyecto, $this->s__filtro);
					break;
				case 'N' :
					$datos = consultas_instancia::get_usuarios_no_vinculados_proyecto($proyecto, $this->s__filtro);
					break;
				case 'T':
					$datos = consultas_instancia::get_lista_usuarios($this->s__filtro);
					break;
				case 'S' :
					$datos = consultas_instancia::get_usuarios_no_vinculados_proyecto(null, $this->s__filtro);
					break;
			}
			$componente->set_datos($datos);
			$componente->desactivar_modo_clave_segura();
		}
	}
	
	function evt__cuadro__seleccion($id)
	{
		$this->dep('editor')->limpiar_datos();
		$this->dep('datos')->cargar($id);
		$this->set_pantalla('editar');
	}

	function evt__cuadro__eliminar($id)
	{
		$this->dep('datos')->cargar($id);
		$this->evt__eliminar();	
	}
}
?>