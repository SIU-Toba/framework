<?php
php_referencia::instancia()->agregar(__FILE__);

class extension_cuadro extends toba_ei_cuadro
{
	function ini()
	{
		$this->_pdf_tabla_ancho = '30%';
	}
	
	function extender_objeto_js()
	{
		echo toba::escaper()->escapeJs($this->objeto_js)
			.".evt__ordenar = function(parametros) {
				alert('Evento escuchado en javascript: Se quiere ordenar ' + parametros.orden_columna );
				return true;
			}
		";
	}

}

?>