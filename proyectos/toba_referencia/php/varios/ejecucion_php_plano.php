<?php
php_referencia::instancia()->agregar(__FILE__);

	if (isset($_POST['notificado'])) {
		toba::notificacion()->agregar('Notificado', 'info');
	}

	echo toba_form::abrir('mi_form', toba::vinculador()->crear_autovinculo());
	echo "<input type='submit' name='notificado' value='Notificar' />";
	echo toba_form::cerrar();
	
?>