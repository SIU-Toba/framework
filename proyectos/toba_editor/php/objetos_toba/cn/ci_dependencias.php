<?php
/*
	El controlador tiene que implementar:
	
		- get_dbr_dependencias()
*/
class ci_dependencias extends toba_ci
{
	private $tabla;
	protected $seleccion_dependencia;
	protected $seleccion_dependencia_anterior;

	/*function destruir()
	{
		parent::destruir();
		//ei_arbol($this->get_tabla()->info(true));
	}*/

	function ini()
	{
		$props = array('seleccion_dependencia', 'seleccion_dependencia_anterior');
		$this->set_propiedades_sesion($props);
	}
	
	function get_tabla()	
	{	//Acceso al db_tablas
		if (! isset($this->tabla)) {
			$this->tabla = $this->controlador->get_dbr_dependencias();
		}
		return $this->tabla;
	}

	function limpiar_seleccion()
	{
		unset($this->seleccion_dependencia_anterior);
		unset($this->seleccion_dependencia);
		$this->dependencia('cuadro')->deseleccionar();
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
		$id_dep = $this->get_tabla()->get_fila_columna($this->seleccion_dependencia_anterior, 'identificador');
		$this->get_tabla()->eliminar_fila($this->seleccion_dependencia_anterior);
		$this->evt__formulario__cancelar();
	}
	
	function evt__formulario__modificacion($datos)
	{
		$id_nuevo = $datos['identificador'];
		$id_anterior = $this->get_tabla()->get_fila_columna($this->seleccion_dependencia_anterior, 'identificador');
		$this->get_tabla()->modificar_fila($this->seleccion_dependencia_anterior, $datos);
		$this->evt__formulario__cancelar();
	}
	
	function conf__formulario()
	{
		if (isset($this->seleccion_dependencia)) {
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

	function conf__cuadro()
	{
		return $this->get_tabla()->get_filas();
	}
	//-------------------------------------------------------------
}
?>