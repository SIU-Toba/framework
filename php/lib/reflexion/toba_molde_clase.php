<?php

class toba_molde_clase
{
	protected $nombre;
	protected $nombre_ancestro;
	protected $orden = 0;
	protected $elementos_php;
	protected $elementos_js;
	protected $codigo_php = '';

	function __construct($nombre, $nombre_ancestro)
	{
		$this->nombre = $nombre;
		$this->nombre_ancestro = $nombre_ancestro;
	}

	//-- Contruccion del molde ------------------------------------

	function agregar(elemento_molde $elemento)
	{
		if ($elemento instanceof toba_molde_metodo_js ||
				$elemento instanceof toba_model_separador_js ) {
			$this->elementos_js[] = $elemento;					
		} else {
			$this->elementos_php[] = $elemento;
		}
	}

	//-- Preguntas sobre la composicion del molde ------------------

	function get_plan_generacion()
	{
		
	}

	//-- Generacion de codigo --------------------------------------

	function generar($opciones)
	{
		$this->filtrar_contenido_molde($opciones);
		$this->colapsar_separadores();
		$this->codigo_php .= "class {$this->nombre} extends {$this->padre_nombre}\n{\n";
		$this->generar_codigo_php();
		$this->generar_codigo_js();
		$this->codigo_php .= "}\n";
	}

	function filtrar_contenido($opciones)
	{
		
	}

	function colapasar_separadores()
	{
		foreach ($this->elementos_php as $elemento) {
			if(	$elemento instanceof 
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