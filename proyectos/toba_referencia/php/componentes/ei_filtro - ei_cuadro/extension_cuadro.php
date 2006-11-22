<?php
php_referencia::instancia()->agregar(__FILE__);

class extension_cuadro extends toba_ei_cuadro
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