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

	function conf__asociados()
	{
		$this->pantalla()->eliminar_dep('form');

	}
	
	function conf__no_asociados()
	{
		$this->pantalla()->eliminar_dep('form');

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
		if (isset($this->s__filtro)) {
			if( $this->get_id_pantalla() == 'asociados' ) {
				$componente->set_datos( consultas_instancia::get_usuarios_asociados_proyecto($this->s__proyecto, $this->s__filtro) );
			} else {
				$componente->eliminar_evento('eliminar');
				$componente->set_datos( consultas_instancia::get_usuarios_asociados_proyecto($this->s__proyecto, $this->s__filtro) );
			}
		}
	}
	
	function evt__cuadro__seleccion($id)
	{
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
}
?>