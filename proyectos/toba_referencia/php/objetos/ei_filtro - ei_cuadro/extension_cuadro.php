<?php
require_once('nucleo/componentes/interface/objeto_ei_cuadro.php');

class extension_cuadro extends objeto_ei_cuadro
{
	function extender_objeto_js()
	{
		echo "
			{$this->objeto_js}.evt__ordenar = function(parametros) {
				alert('Evento escuchado en javascript: Se quiere ordenar ' + parametros.orden_columna );
				return true;
			}
		";
	}

}

?>