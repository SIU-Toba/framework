<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 
/*
	El controlador tiene que implementar:
	
		- get_dbr_dependencias()
*/
class ci_dependencias extends objeto_ci
{
	private $tabla;
	protected $seleccion_dependencia;
	protected $seleccion_dependencia_anterior;

	function destruir()
	{
		parent::destruir();
		//ei_arbol($this->get_tabla()->info(true));
	}

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "seleccion_dependencia";
		$propiedades[] = "seleccion_dependencia_anterior";
		return $propiedades;
	}

	function get_tabla()
	//Acceso al db_tablas
	{
		if (! isset($this->tabla)) {
			$this->tabla = $this->controlador->get_dbr_dependencias();
		}
		return $this->tabla;
	}

	function limpiar_seleccion()
	{
		unset($this->seleccion_dependencia_anterior);
		unset($this->seleccion_dependencia);
		$this->dependencias["cuadro"]->deseleccionar();
	}

	//-------------------------------------------------------------
	//-- Formulario
	//-------------------------------------------------------------

	function evt__formulario__alta($datos)
	{
		$this->get_tabla()->nueva_fila($datos);
	}
	
	function evt__formulario__baja()
	{
		$id_dep = $this->get_tabla()->get_fila_columna($this->seleccion_dependencia_anterior,"identificador");
		$this->get_tabla()->eliminar_fila($this->seleccion_dependencia_anterior);
		//Se dispara un evento que indica cual es la DEPENDENCIA que se elimino (para que el controlador actualize su estado)
		$this->reportar_evento( "del_dep", $id_dep );
		$this->evt__formulario__cancelar();
	}
	
	function evt__formulario__modificacion($datos)
	{
		$this->get_tabla()->modificar_fila($this->seleccion_dependencia_anterior, $datos);
		$this->evt__formulario__cancelar();
	}
	
	function evt__formulario__carga()
	{
		if(isset($this->seleccion_dependencia)){
			$this->seleccion_dependencia_anterior = $this->seleccion_dependencia;
			return $this->get_tabla()->get_fila($this->seleccion_dependencia_anterior);
		}
	}

	function evt__formulario__cancelar()
	{
		$this->limpiar_seleccion();
	}

	//-------------------------------------------------------------
	//-- Cuadro
	//-------------------------------------------------------------

	function evt__cuadro__seleccion($id)
	{
		$this->seleccion_dependencia = $id;
	}

	function evt__cuadro__carga()
	{
		return $this->get_tabla()->get_filas();
	}
	//-------------------------------------------------------------
}
?>