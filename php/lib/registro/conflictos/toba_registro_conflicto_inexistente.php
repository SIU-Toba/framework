<?php

class toba_registro_conflicto_inexistente extends toba_registro_conflicto
{
	function  __construct($registro)
	{
		parent::__construct($registro);
		$this->tipo = toba_registro_conflicto::fatal;
		$this->numero = 3;
	}

	function get_descripcion()
	{
		return "
			[F:$this->numero] El registro con clave {$this->registro->get_clave()} de la tabla
			{$this->registro->get_tabla()} no existe.
		";
	}
}
?>
