<?php

class odt_formulario_efs extends toba_datos_tabla
{
	function configuracion()
	{
		$this->set_no_duplicado(array('identificador'));
	}
}
?>