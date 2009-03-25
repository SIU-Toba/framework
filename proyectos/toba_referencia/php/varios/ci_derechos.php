<?php 
php_referencia::instancia()->agregar(__FILE__);

class ci_derechos extends toba_ci
{
	function ini()
	{
		$texto = '';
		if (toba::derechos()->validar('derecho_a', false) ) {
			$texto .= "El grupo de acceso actual posee el derecho 'derecho_a'<br>";
		} else {
			$texto .= "El grupo de acceso actual NO posee el derecho 'derecho_a'<br>";
		}
		if (toba::derechos()->validar('derecho_b', false) ) {
			$texto .= "El grupo de acceso actual posee el derecho 'derecho_b'<br>";
		} else {
			$texto .= "El grupo de acceso actual NO posee el derecho 'derecho_b'<br>";
		}
		$this->pantalla()->set_descripcion($texto);
	}
}

?>