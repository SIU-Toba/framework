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

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = 'seleccion_dependencia';
		$propiedades[] = 'seleccion_dependencia_anterior';
		return $propiedades;
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
		$lista = $this->get_lista_identificadores();
		if (isset($datos['identificador']) && in_array($datos['identificador'], $lista)) {
			throw new toba_error_usuario('Este identificador ya se usa en otra dependencia!!!');
		}		
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
		$lista = $this->get_lista_identificadores();
		if (($id_nuevo != $id_anterior) && in_array($id_nuevo, $lista)) {
			throw new toba_error_usuario('Este identificador ya se usa en otra dependencia!!!');
		}
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
	function get_lista_identificadores()
	{
		$resultado = array();
		$filas = $this->get_tabla()->get_filas();
		foreach($filas as $fila) {
			if (isset($fila['identificador']))  {
				$resultado[] = $fila['identificador'];
			}
		}
		return $resultado;		
	}	
}
?>