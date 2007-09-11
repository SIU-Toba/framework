<?php
php_referencia::instancia()->agregar(__FILE__);

	if (isset($_POST['notificado'])) {
		$personas = toba::tabla('ref_persona');
		$personas->cargar();
		$salida = '<strong>Personas</strong><ul>';
		foreach ($personas->get_filas() as $persona) {
			$salida .= '<li>'.$persona['nombre'].'</li>';
		}
		$salida .= '</ul>';
		toba::notificacion()->agregar($salida, 'info');
	}

	echo toba_form::abrir('mi_form', toba::vinculador()->crear_autovinculo());
	echo "<input type='submit' name='notificado' value='Ver Personas' />";
	echo toba_form::cerrar();
	
	
?>