<?php

class toba_registro_conflicto_tabla_inexistente extends toba_registro_conflicto
{
	function  __construct($registro)
	{
		parent::__construct($registro);
		$this->tipo = toba_registro_conflicto::fatal;
		$this->numero = 1;
	}

	function get_descripcion()
	{
		return "[F:$this->numero] La tabla {$this->registro->get_tabla()} no existe.";
	}
}
?>
