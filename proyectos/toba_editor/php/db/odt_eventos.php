<?php

class odt_eventos extends toba_datos_tabla
{
	function configuracion()
	{
		$this->set_no_duplicado(array('identificador'));
	}
}
?>
