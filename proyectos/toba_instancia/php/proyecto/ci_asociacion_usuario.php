<?php 
class ci_asociacion_usuario extends toba_ci
{
	protected $s__filtro;
	protected $s__proyecto;
	protected $s__usuario;
	protected $s__accion;
	
	//-----------------------------------------------------------------------------------
	//---- Inicializacion ---------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function ini()
	{
		$proyecto = toba::memoria()->get_parametro('proyecto');
		if(isset($proyecto)) $this->s__proyecto = $proyecto;
	}

	function conf__vinculados()
	{
		$desc = 'Usuarios vinculados al proyecto: <strong>'.$this->s__proyecto.'</strong><br>';
		$this->pantalla()->eliminar_dep('filtro');
		$this->pantalla()->set_descripcion($desc);
	}
	
	function conf__no_vinculados()
	{
		$desc = 'Usuarios NO vinculados al proyecto: <strong>'.$this->s__proyecto.'</strong><br>';
		$this->pantalla()->eliminar_dep('filtro');
		$this->pantalla()->set_descripcion($desc);
	}
	
	//-----------------------------------------------------------------------------------
	//---- DEPENDENCIAS -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	//---- filtro -----------------------------------------------------------------------

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
		if(isset($this->s__filtro)) {
			$componente->set_datos($this->s__filtro);
			$componente->colapsar();
		}
	}

	//---- cuadro -------------------------------------------------------

	function conf__cuadro($componente)
	{
		return $componente->set_datos( consultas_instancia::get_usuarios_vinculados_proyecto($this->s__proyecto, $this->s__filtro) );
	}
	
	function conf_evt__cuadro__eliminar(toba_evento_usuario $evt)
	{
		list($proyecto, $usuario) = explode(apex_qs_separador, $evt->get_parametros());
		if ($proyecto == toba::proyecto()->get_id() && $usuario == toba::usuario()->get_id()) {
			$evt->anular();	
		}
	}	
	
	function evt__cuadro__seleccion($id)
	{
		$this->pantalla()->agregar_dep('form');
		$this->dep('datos')->cargar($id);
		$this->dep('cuadro')->seleccionar($id);
	}

	function evt__cuadro__eliminar($id)
	{
		$this->dep('datos')->cargar($id);
		$this->dep('datos')->eliminar_fila(0);	
		$this->dep('datos')->sincronizar();
		$this->dep('datos')->resetear();	
	}

	//---- form -------------------------------------------------------

	function conf__form()
	{
		return $this->dep('datos')->get();
	}
	
	function evt__form__guardar($datos)
	{
		$datos['proyecto'] = $this->s__proyecto;
		$this->dep('datos')->set($datos);
		$this->dep('datos')->sincronizar();
		$this->dep('datos')->resetear();	
	}
	
	function evt__form__cancelar()
	{
		$this->dep('datos')->resetear();	
	}
	
	//---- cuadro2 -------------------------------------------------------

	function conf__cuadro2($componente)
	{
		return $componente->set_datos( consultas_instancia::get_usuarios_no_vinculados_proyecto($this->s__proyecto, $this->s__filtro) );
	}
	
	function evt__cuadro2__seleccion($id)
	{
		$this->pantalla()->agregar_dep('form2');
		$this->dep('cuadro')->seleccionar($id);
		$this->s__usuario = $id['usuario'];
	}

	//---- form2 -------------------------------------------------------

	function conf__form2()
	{
		$datos['usuario'] = $this->s__usuario;
		return $datos;
	}
	
	function evt__form2__guardar($datos)
	{
		//$datos['usuario'] = $this->s__usuario;
		$datos['proyecto'] = $this->s__proyecto;
		$this->dep('datos')->set($datos);
		$this->dep('datos')->sincronizar();
		$this->dep('datos')->resetear();	
		unset($this->s__usuario);
	}
	
	//------------------------------------------------------------------
	
	function get_lista_grupos_acceso()
	{
		return consultas_instancia::get_lista_grupos_acceso_proyecto($this->s__proyecto);
	}
}
?>