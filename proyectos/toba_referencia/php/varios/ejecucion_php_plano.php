<?php
php_referencia::instancia()->agregar(__FILE__);

if (isset($_POST['notificado'])) {
	$personas = toba::tabla('ref_persona');
	$personas->cargar();
	$salida = '<strong>Personas</strong><ul>';
	foreach ($personas->get_filas() as $persona) {
		$salida .= '<li>'.toba::escaper()->escapeHtml($persona['nombre']).'</li>';
	}
	$salida .= '</ul>';
	toba::notificacion()->agregar($salida, 'info');
}

echo 'Esta operación no usa el esquema de componentes, sino un archivo .php procedural.<br>';
echo 'A pesar de no usar los componentes, igual se tiene acceso a la API transversal de toba.<br><br>';
echo toba_form::abrir('mi_form', toba::vinculador()->get_url());
echo "<input type='submit' name='notificado' value='Probar interacción con la BD' />";
echo toba_form::cerrar();

	
?>