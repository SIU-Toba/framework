<?php
require_once('toba_molde_metodo_php.php');
require_once('toba_molde_metodo_js.php');
require_once('toba_molde_separador_php.php');
require_once('toba_molde_separador_js.php');

class toba_molde_clase
{
	protected $nombre;
	protected $nombre_ancestro;
	protected $orden = 0;
	protected $elementos_php = array();
	protected $indices_php = array();
	protected $elementos_js = array();
	protected $indices_js = array();
	protected $codigo_php = '';

	function __construct($nombre, $nombre_ancestro)
	{
		$this->nombre = $nombre;
		$this->nombre_ancestro = $nombre_ancestro;
	}

	/*
		Devuelve una referencia a un metodo PHP
	*/
	function metodo_php($nombre)
	{
		if (isset($this->indices_php[$nombre])) {
			return $this->indices_php[$nombre];
		} else {
			throw new error_toba("molde clase: el metodo PHP '$nombre' no existe");	
		}
	}

	/*
		Devuelve una referencia a un metodo JS
	*/
	function metodo_js($nombre)
	{
		if (isset($this->indices_js[$nombre])) {
			return $this->indices_js[$nombre];
		} else {
			throw new error_toba("molde clase: el metodo JS '$nombre' no existe");	
		}
	}

	//-- Contruccion del molde ------------------------------------

	function agregar(elemento_molde $elemento)
	{
		if ($elemento instanceof toba_molde_metodo_js || $elemento instanceof toba_molde_separador_js ) {
			$this->elementos_js[$this->orden] = $elemento;
			if ($elemento instanceof toba_molde_metodo_js ) {
				$this->indices_js[$elemento->get_nombre()] = $this->elementos_js[$this->orden];
			}
		} else {
			$this->elementos_php[$this->orden] = $elemento;
			if ($elemento instanceof toba_molde_metodo_php ) {
				$this->indices_php[$elemento->get_nombre()] = $this->elementos_php[$this->orden];
			}
		}
		$this->orden++;
	}

	//-- Preguntas sobre la composicion del molde ------------------

	function get_plan_generacion()
	{
		$plan = array();
		
		
		return $plan;
	}

	//-- Generacion de codigo --------------------------------------

	function generar($elementos=null)
	{
		if (isset($elementos) && !is_array($elementos)) {
			throw new error_toba('molde clase: La listaa de elementos debe ser un array.');
		}
		$this->filtrar_contenido($elementos);
		$this->colapsar_separadores();
		$this->codigo_php .= "class {$this->nombre} extends {$this->padre_nombre}\n{\n";
		$this->generar_codigo_php();
		$this->generar_codigo_js();
		$this->codigo_php .= "}\n";
	}

	/*
		Borra los elementos JS y PHP que no estan en la lista de elementos a utilizar
		La lista de elementos a utilizar esta relacionada con la salida de get_plan_generacion
	*/
	function filtrar_contenido($elementos_a_utilizar)
	{
		$tipos = array('php','js');
		foreach($tipos as $tipo) {
			$var = 'elementos_' . $tipo;
			foreach( array_keys($this->$var) as $id) {
				if (!in_array($id, $elementos_a_utilizar)) {
					unset($this->$var[$id]);
				}
			}
		}
	}

	/*
		Elimina los separadores que no tengan metodos subsiguientes
	*/
	function colapasar_separadores()
	{
		$sep_previo = null;
		foreach ($this->elementos_php as $id => $elemento) {
			if(	$elemento instanceof toba_molde_separador ) {
				$sep_previo
			}
		}	
		
	}

	function generar_codigo_php()
	{
		foreach ($this->elementos_php as $elemento) {
			$this->codigo_php .= $elemento->generar_codigo();
			$this->codigo_php .= "\n";
		}	
	}

	function generar_codigo_js()
	{
		foreach ($this->elementos_js as $elemento) {
			$this->codigo_php .= $elemento->generar_codigo();
			$this->codigo_php .= "\n";
		}	
	}
}
?>